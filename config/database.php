<?php
class Database {
    private $host = "localhost";
    private $db_name = "licoreria";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            error_log('Intentando conectar a la base de datos...');
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            error_log('Conexión exitosa a la base de datos');
        } catch(PDOException $e) {
            error_log('Error de conexión: ' . $e->getMessage());
        }
        return $this->conn;
    }
}
?>