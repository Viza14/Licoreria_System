<?php
class CategoriaModel
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

    public function obtenerTodasCategorias()
    {
        $query = "SELECT c.*, t.nombre as tipo_categoria, e.nombre as estatus 
              FROM categorias c
              JOIN tipos_categoria t ON c.id_tipo_categoria = t.id
              JOIN estatus e ON c.id_estatus = e.id";  // ← Correcto: usa c.id_estatus
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerCategoriaPorId($id)
    {
        $query = "SELECT * FROM categorias WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crearCategoria($nombre, $id_tipo_categoria, $id_estatus)
    {
        try {
            $query = "INSERT INTO categorias (nombre, id_tipo_categoria, id_estatus) 
                      VALUES (:nombre, :id_tipo_categoria, :id_estatus)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":id_tipo_categoria", $id_tipo_categoria);
            $stmt->bindParam(":id_estatus", $id_estatus);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->errors[] = "Error de base de datos: " . $e->getMessage();
            return false;
        }
    }

    public function actualizarCategoria($id, $nombre, $id_tipo_categoria, $id_estatus)
    {
        try {
            $query = "UPDATE categorias SET 
                      nombre = :nombre, 
                      id_tipo_categoria = :id_tipo_categoria,
                      id_estatus = :id_estatus
                      WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":id_tipo_categoria", $id_tipo_categoria);
            $stmt->bindParam(":id_estatus", $id_estatus);
            $stmt->bindParam(":id", $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    public function cambiarEstado($id, $nuevoEstado)
    {
        try {
            $query = "UPDATE categorias SET id_estatus = :estatus WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":estatus", $nuevoEstado);
            $stmt->bindParam(":id", $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    public function obtenerTiposCategoria()
    {
        $query = "SELECT * FROM tipos_categoria WHERE id_estatus = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // En CategoriaModel.php
    public function existeCategoria($nombre, $id = null)
    {
        try {
            $query = "SELECT COUNT(*) as count FROM categorias WHERE nombre = :nombre";
            if ($id !== null) {
                $query .= " AND id != :id";
            }

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":nombre", $nombre);
            if ($id !== null) {
                $stmt->bindParam(":id", $id);
            }
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['count'] > 0;
        } catch (PDOException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }
}
