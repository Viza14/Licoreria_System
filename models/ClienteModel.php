<?php
class ClienteModel
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

    public function existeCliente($cedula, $telefono, $excluirCedula = null)
    {
        $query = "SELECT COUNT(*) as count FROM clientes WHERE (cedula = :cedula OR telefono = :telefono)";
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

    public function obtenerTodosClientes($pagina = 1, $porPagina = 10, $filtros = [])
    {
        try {
            if (!$this->db) {
                throw new Exception('Error de conexión a la base de datos');
            }

            // Base query for both count and data
            $baseQuery = "FROM clientes c
                         JOIN estatus e ON c.id_estatus = e.id
                         JOIN simbolos_cedula sc ON c.id_simbolo_cedula = sc.id";

            // Build WHERE clause based on filters
            $whereConditions = [];
            $params = [];

            // Búsqueda general
            if (isset($filtros['busqueda']) && $filtros['busqueda'] !== null) {
                $whereConditions[] = "(LOWER(c.nombres) COLLATE utf8_general_ci LIKE LOWER(?) OR 
                                      LOWER(c.apellidos) COLLATE utf8_general_ci LIKE LOWER(?) OR 
                                      LOWER(c.cedula) COLLATE utf8_general_ci LIKE LOWER(?) OR
                                      LOWER(c.telefono) COLLATE utf8_general_ci LIKE LOWER(?) OR
                                      LOWER(c.direccion) COLLATE utf8_general_ci LIKE LOWER(?))";
                $searchTerm = '%' . $filtros['busqueda'] . '%';
                $params = array_merge($params, array_fill(0, 5, $searchTerm));
            }

            // Filtro por estatus
            if (isset($filtros['estatus']) && $filtros['estatus'] !== null) {
                $whereConditions[] = "LOWER(e.nombre) COLLATE utf8_general_ci = LOWER(?)";
                $params[] = $filtros['estatus'];
            }

            // Filtro por rango de compras
            if (isset($filtros['compras']) && $filtros['compras'] !== null) {
                $ventasQuery = "(SELECT COALESCE(SUM(v.monto_total), 0) FROM ventas v WHERE v.cedula_cliente = c.cedula AND v.id_estatus = 1)";
                switch($filtros['compras']) {
                    case 'mayor':
                        $whereConditions[] = "$ventasQuery > 1000";
                        break;
                    case 'medio':
                        $whereConditions[] = "$ventasQuery BETWEEN 500 AND 1000";
                        break;
                    case 'menor':
                        $whereConditions[] = "$ventasQuery < 500 AND $ventasQuery > 0";
                        break;
                    case 'cero':
                        $whereConditions[] = "$ventasQuery = 0";
                        break;
                }
            }

            $whereClause = !empty($whereConditions) ? " WHERE " . implode(' AND ', $whereConditions) : "";

            // Get total records for pagination
            $queryTotal = "SELECT COUNT(*) as total " . $baseQuery . $whereClause;
            $stmtTotal = $this->db->prepare($queryTotal);
            foreach ($params as $i => $param) {
                $stmtTotal->bindValue($i + 1, $param);
            }
            $stmtTotal->execute();
            $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

            // Calculate offset
            $offset = ($pagina - 1) * $porPagina;

            // Get paginated records
            $query = "SELECT c.*, e.nombre as estatus, sc.nombre as nombre_simbolo,
                      COALESCE((SELECT SUM(v.monto_total) FROM ventas v WHERE v.cedula_cliente = c.cedula AND v.id_estatus = 1), 0) as total_ventas
                      " . $baseQuery . $whereClause . "
                      ORDER BY c.nombres, c.apellidos
                      LIMIT ? OFFSET ?";

            $stmt = $this->db->prepare($query);
            foreach ($params as $i => $param) {
                $stmt->bindValue($i + 1, $param);
            }
            $paramCount = count($params);
            $stmt->bindValue($paramCount + 1, $porPagina, PDO::PARAM_INT);
            $stmt->bindValue($paramCount + 2, $offset, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result === false) {
                throw new Exception('Error al obtener los datos de clientes');
            }

            return [
                'data' => $result,
                'total' => $total,
                'pagina_actual' => $pagina,
                'por_pagina' => $porPagina,
                'total_paginas' => ceil($total / $porPagina)
            ];
        } catch (PDOException $e) {
            $this->errors[] = "Error de base de datos: " . $e->getMessage();
            error_log("Error en obtenerTodosClientes: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            error_log("Error en obtenerTodosClientes: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerClientePorCedula($cedula)
    {
        $query = "SELECT c.*, e.nombre as estatus, sc.nombre as nombre_simbolo 
                  FROM clientes c
                  JOIN estatus e ON c.id_estatus = e.id
                  JOIN simbolos_cedula sc ON c.id_simbolo_cedula = sc.id
                  WHERE c.cedula = :cedula";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":cedula", $cedula);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crearCliente($data)
    {
        try {
            $query = "INSERT INTO clientes (cedula, id_simbolo_cedula, nombres, apellidos, telefono, direccion, id_estatus) 
                      VALUES (:cedula, :id_simbolo_cedula, :nombres, :apellidos, :telefono, :direccion, :id_estatus)";
            $stmt = $this->db->prepare($query);

            return $stmt->execute($data);
        } catch (PDOException $e) {
            $this->errors[] = "Error de base de datos: " . $e->getMessage();
            return false;
        }
    }
    public function actualizarCliente($cedulaOriginal, $data)
    {
        try {
            $query = "UPDATE clientes SET 
                 cedula = :cedula,
                 id_simbolo_cedula = :id_simbolo_cedula,
                 nombres = :nombres, 
                 apellidos = :apellidos, 
                 telefono = :telefono, 
                 direccion = :direccion, 
                 id_estatus = :id_estatus
                 WHERE cedula = :cedula_original";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":cedula", $data['cedula']);
            $stmt->bindParam(":id_simbolo_cedula", $data['id_simbolo_cedula']);
            $stmt->bindParam(":nombres", $data['nombres']);
            $stmt->bindParam(":apellidos", $data['apellidos']);
            $stmt->bindParam(":telefono", $data['telefono']);
            $stmt->bindParam(":direccion", $data['direccion']);
            $stmt->bindParam(":id_estatus", $data['id_estatus']);
            $stmt->bindParam(":cedula_original", $cedulaOriginal);

            return $stmt->execute();
        } catch (PDOException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    public function cambiarEstado($cedula, $nuevoEstado)
    {
        try {
            $query = "UPDATE clientes SET id_estatus = :estatus WHERE cedula = :cedula";
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

        // Validación mejorada
        if ($simboloNombre === 'V' || $simboloNombre === 'E') {
            return (strlen($cedula) >= 7 && strlen($cedula) <= 8 && ctype_digit($cedula));
        } elseif ($simboloNombre === 'J') {
            return (strlen($cedula) >= 8 && strlen($cedula) <= 9 && ctype_digit($cedula));
        }

        return false;
    }
    public function obtenerTotalVentasCliente($cedula)
    {
        try {
            if (!$this->db) {
                throw new Exception('Error de conexión a la base de datos');
            }

            $query = "SELECT COALESCE(SUM(v.total), 0) as total 
                      FROM ventas v 
                      WHERE v.cedula_cliente = :cedula";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":cedula", $cedula);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return floatval($result['total']);
        } catch (PDOException $e) {
            $this->errors[] = "Error al calcular total de ventas: " . $e->getMessage();
            error_log("Error en obtenerTotalVentasCliente: " . $e->getMessage());
            return 0;
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            error_log("Error en obtenerTotalVentasCliente: " . $e->getMessage());
            return 0;
        }
    }
    public function eliminarCliente($cedula)
    {
        try {
            $query = "DELETE FROM clientes WHERE cedula = :cedula";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":cedula", $cedula);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->errors[] = "Error al eliminar cliente: " . $e->getMessage();
            return false;
        }
    }

    public function obtenerClientesActivos()
    {
        $query = "SELECT c.*, e.nombre as estatus, sc.nombre as nombre_simbolo 
              FROM clientes c
              JOIN estatus e ON c.id_estatus = e.id
              JOIN simbolos_cedula sc ON c.id_simbolo_cedula = sc.id
              WHERE c.id_estatus = 1
              ORDER BY c.nombres, c.apellidos";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarClientes() {
        $query = "SELECT COUNT(*) as total FROM clientes WHERE id_estatus = 1"; // Cambiado a id_estatus
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0; // Asegurar que devuelva 0 si es null
    }
}
