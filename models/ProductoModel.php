<?php
class ProductoModel {
    private $db;

    public function __construct() {
        require_once ROOT_PATH . 'config/database.php';
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function contarProductos() {
        $query = "SELECT COUNT(*) as total FROM producto WHERE id_estatus = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>