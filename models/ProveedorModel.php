<?php
class ProveedorModel
{
    private $db;
    private $errors = [];

    public function __construct()
    {
        require_once ROOT_PATH . 'config/database.php';
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function obtenerSimbolosCedula()
    {
        $query = "SELECT * FROM simbolos_cedula";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodosProveedores($pagina = 1, $por_pagina = 10)
    {
        // Calcular el offset
        $offset = ($pagina - 1) * $por_pagina;

        // Obtener el total de registros
        $queryTotal = "SELECT COUNT(*) as total FROM proveedores";
        $stmtTotal = $this->db->prepare($queryTotal);
        $stmtTotal->execute();
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

        // Calcular el total de páginas
        $total_paginas = ceil($total / $por_pagina);

        // Consulta principal con paginación
        $query = "SELECT p.*, e.nombre as estatus, sc.nombre as nombre_simbolo 
                  FROM proveedores p
                  JOIN estatus e ON p.id_estatus = e.id
                  JOIN simbolos_cedula sc ON p.id_simbolo_cedula = sc.id
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'proveedores' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total_registros' => $total,
            'total_paginas' => $total_paginas,
            'pagina_actual' => $pagina
        ];
    }

    public function obtenerProveedorPorCedula($cedula)
    {
        $query = "SELECT * FROM proveedores WHERE cedula = :cedula";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":cedula", $cedula);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function existeProveedor($cedula, $telefono, $excluirCedula = null)
    {
        $query = "SELECT COUNT(*) as count FROM proveedores WHERE (cedula = :cedula OR telefono = :telefono)";
        if ($excluirCedula) {
            $query .= " AND cedula != :excluirCedula";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":cedula", $cedula);
        $stmt->bindParam(":telefono", $telefono);
        if ($excluirCedula) {
            $stmt->bindParam(":excluirCedula", $excluirCedula);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }


    public function crearProveedor($data)
    {
        try {
            $query = "INSERT INTO proveedores (cedula, id_simbolo_cedula, nombre, telefono, direccion, id_estatus) 
                      VALUES (:cedula, :id_simbolo_cedula, :nombre, :telefono, :direccion, :id_estatus)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            $this->errors[] = "Error de base de datos: " . $e->getMessage();
            return false;
        }
    }

    public function actualizarProveedor($cedulaOriginal, $nuevosDatos)
    {
        try {
            // Iniciar transacción
            $this->db->beginTransaction();

            // Primero actualizar el proveedor principal
            $query = "UPDATE proveedores SET 
              cedula = :cedula, 
              id_simbolo_cedula = :id_simbolo_cedula,
              nombre = :nombre, 
              telefono = :telefono, 
              direccion = :direccion, 
              id_estatus = :id_estatus
              WHERE cedula = :cedula_original";

            $stmt = $this->db->prepare($query);
            $stmt->execute($nuevosDatos);

            // Si la cédula está cambiando, actualizar en todas las tablas referenciadoras
            if ($cedulaOriginal != $nuevosDatos['cedula']) {
                $tablasReferenciadoras = [
                    'proveedor_producto' => 'cedula_proveedor',
                    // Agrega aquí otras tablas que referencien a proveedores
                ];

                foreach ($tablasReferenciadoras as $tabla => $campo) {
                    $query = "UPDATE {$tabla} SET {$campo} = :nuevaCedula 
                      WHERE {$campo} = :cedulaOriginal";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':nuevaCedula', $nuevosDatos['cedula']);
                    $stmt->bindParam(':cedulaOriginal', $cedulaOriginal);
                    $stmt->execute();
                }
            }

            // Confirmar transacción
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            // Revertir en caso de error
            $this->db->rollBack();
            $this->errors[] = "Error en actualización en cascada: " . $e->getMessage();
            return false;
        }
    }

    public function cambiarEstado($cedula, $nuevoEstado)
    {
        try {
            $query = "UPDATE proveedores SET id_estatus = :estatus WHERE cedula = :cedula";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":estatus", $nuevoEstado);
            $stmt->bindParam(":cedula", $cedula);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    public function validarCedula($simboloId, $cedula)
    {
        $simbolos = $this->obtenerSimbolosCedula();
        $simboloNombre = '';

        foreach ($simbolos as $simbolo) {
            if ($simbolo['id'] == $simboloId) {
                $simboloNombre = $simbolo['nombre'];
                break;
            }
        }

        if (strpos($simboloNombre, 'V-') !== false || strpos($simboloNombre, 'E-') !== false) {
            return (strlen($cedula) >= 7 && strlen($cedula) <= 8 && ctype_digit($cedula));
        } elseif (strpos($simboloNombre, 'J-') !== false) {
            return (strlen($cedula) >= 8 && strlen($cedula) <= 9 && ctype_digit($cedula));
        }

        return false;
    }

    public function obtenerProveedoresActivos()
    {
        $query = "SELECT * FROM proveedores WHERE id_estatus = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
