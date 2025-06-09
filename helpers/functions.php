<?php
function obtenerTotalProductos() {
    require_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT COUNT(*) as total FROM producto WHERE id_estatus = 1";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $row['total'];
}

// Más funciones auxiliares según necesites
?>