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

    public function obtenerTodosClientes()
    {
        $query = "SELECT c.*, e.nombre as estatus, sc.nombre as nombre_simbolo 
                  FROM clientes c
                  JOIN estatus e ON c.id_estatus = e.id
                  JOIN simbolos_cedula sc ON c.id_simbolo_cedula = sc.id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $query = "SELECT CalcularTotalVentasCliente(:cedula) as total";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":cedula", $cedula);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    public function eliminarCliente($cedula){
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
}
