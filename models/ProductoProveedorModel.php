<?php
class ProductoProveedorModel
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

    public function obtenerTodasRelaciones()
    {
        $query = "SELECT pp.*, p.descripcion as producto, pr.nombre as proveedor, 
                  sc.nombre as simbolo_proveedor, e.nombre as estatus
                  FROM proveedor_producto pp
                  JOIN producto p ON pp.id_producto = p.id
                  JOIN proveedores pr ON pp.cedula_proveedor = pr.cedula
                  JOIN simbolos_cedula sc ON pr.id_simbolo_cedula = sc.id
                  JOIN estatus e ON pp.id_estatus = e.id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerRelacionPorId($id)
    {
        $query = "SELECT pp.*, p.descripcion as producto, pr.nombre as proveedor, 
                  sc.nombre as simbolo_proveedor, e.nombre as estatus
                  FROM proveedor_producto pp
                  JOIN producto p ON pp.id_producto = p.id
                  JOIN proveedores pr ON pp.cedula_proveedor = pr.cedula
                  JOIN simbolos_cedula sc ON pr.id_simbolo_cedula = sc.id
                  JOIN estatus e ON pp.id_estatus = e.id
                  WHERE pp.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crearRelacion($data)
    {
        try {
            // Verificar si ya existe la relación
            $query = "SELECT COUNT(*) as count FROM proveedor_producto 
                      WHERE cedula_proveedor = :cedula_proveedor AND id_producto = :id_producto";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":cedula_proveedor", $data['cedula_proveedor']);
            $stmt->bindParam(":id_producto", $data['id_producto']);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                $this->errors[] = "Ya existe una relación entre este producto y proveedor";
                return false;
            }

            // Agregar estatus por defecto
            $data['id_estatus'] = 1;

            $query = "INSERT INTO proveedor_producto (cedula_proveedor, id_producto, precio_compra, id_estatus)
                      VALUES (:cedula_proveedor, :id_producto, :precio_compra, :id_estatus)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            $this->errors[] = "Error de base de datos: " . $e->getMessage();
            return false;
        }
    }

    public function actualizarRelacion($id, $data)
    {
        try {
            $query = "UPDATE proveedor_producto SET 
                      precio_compra = :precio_compra
                      WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $data['id'] = $id;
            return $stmt->execute($data);
        } catch (PDOException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    public function cambiarEstado($id, $nuevoEstado)
    {
        try {
            $query = "UPDATE proveedor_producto SET id_estatus = :estatus WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":estatus", $nuevoEstado);
            $stmt->bindParam(":id", $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    public function obtenerProductos()
    {
        $query = "SELECT id, descripcion FROM producto WHERE id_estatus = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProveedores()
    {
        $query = "SELECT p.cedula, CONCAT(sc.nombre, '-', p.cedula) as cedula_completa, p.nombre 
                  FROM proveedores p
                  JOIN simbolos_cedula sc ON p.id_simbolo_cedula = sc.id
                  WHERE p.id_estatus = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerEstatus()
    {
        $query = "SELECT * FROM estatus";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPrecioPorProveedorProducto($id_producto, $cedula_proveedor) {
        $query = "SELECT precio_compra FROM proveedor_producto 
                  WHERE id_producto = :id_producto 
                  AND cedula_proveedor = :cedula_proveedor
                  AND id_estatus = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id_producto", $id_producto);
        $stmt->bindParam(":cedula_proveedor", $cedula_proveedor);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }
}
