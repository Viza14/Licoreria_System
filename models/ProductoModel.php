<?php
class ProductoModel
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

    public function obtenerTodosProductos()
    {
        $query = "SELECT p.*, c.nombre as categoria, e.nombre as estatus, tc.nombre as tipo_categoria
                  FROM producto p
                  JOIN categorias c ON p.id_categoria = c.id
                  JOIN estatus e ON p.id_estatus = e.id
                  JOIN tipos_categoria tc ON c.id_tipo_categoria = tc.id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProductoPorId($id)
    {
        $query = "SELECT p.*, 
              c.nombre as categoria, 
              e.nombre as estatus,
              tc.nombre as tipo_categoria
              FROM producto p
              JOIN categorias c ON p.id_categoria = c.id
              JOIN estatus e ON p.id_estatus = e.id
              JOIN tipos_categoria tc ON c.id_tipo_categoria = tc.id
              WHERE p.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crearProducto($data)
    {
        try {
            $query = "INSERT INTO producto (descripcion, cantidad, precio, id_categoria, id_estatus) 
                      VALUES (:descripcion, :cantidad, :precio, :id_categoria, :id_estatus)";
            $stmt = $this->db->prepare($query);

            return $stmt->execute($data);
        } catch (PDOException $e) {
            $this->errors[] = "Error de base de datos: " . $e->getMessage();
            return false;
        }
    }

    public function actualizarProducto($id, $data)
    {
        try {
            $query = "UPDATE producto SET 
                      descripcion = :descripcion,
                      cantidad = :cantidad,
                      precio = :precio,
                      id_categoria = :id_categoria,
                      id_estatus = :id_estatus
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
            $query = "UPDATE producto SET id_estatus = :estatus WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":estatus", $nuevoEstado);
            $stmt->bindParam(":id", $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    public function obtenerCategorias()
    {
        $query = "SELECT c.*, tc.nombre as tipo_categoria 
                  FROM categorias c
                  JOIN tipos_categoria tc ON c.id_tipo_categoria = tc.id
                  WHERE c.id_estatus = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTiposCategoria()
    {
        $query = "SELECT * FROM tipos_categoria";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerEstatus()
    {
        $query = "SELECT * FROM estatus WHERE id IN (1,2,3,4)";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeProducto($descripcion, $idCategoria, $excluirId = null)
    {
        $query = "SELECT COUNT(*) as count FROM producto 
                  WHERE descripcion = :descripcion AND id_categoria = :id_categoria";

        if ($excluirId) {
            $query .= " AND id != :excluirId";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":id_categoria", $idCategoria);

        if ($excluirId) {
            $stmt->bindParam(":excluirId", $excluirId);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    public function contarProductos()
    {
        try {
            $query = "SELECT COUNT(*) as total FROM producto";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            $this->errors[] = "Error al contar productos: " . $e->getMessage();
            return 0;
        }
    }
}
