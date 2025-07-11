<?php
class StockLimiteModel
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

    public function obtenerLimitesPorProducto($idProducto)
    {
        $query = "SELECT * FROM stock_limites WHERE id_producto = :id_producto";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id_producto", $idProducto);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerTodosLimites($pagina = 1, $por_pagina = 10)
    {
        // Calcular el offset
        $offset = ($pagina - 1) * $por_pagina;

        // Consulta para obtener el total de registros
        $queryTotal = "SELECT COUNT(*) as total FROM stock_limites sl
                       JOIN producto p ON sl.id_producto = p.id
                       JOIN usuarios u ON sl.id_usuario = u.id";
        $stmtTotal = $this->db->prepare($queryTotal);
        $stmtTotal->execute();
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

        // Consulta principal con paginación
        $query = "SELECT sl.*, p.descripcion as producto, p.cantidad,
                  CONCAT(u.nombres, ' ', u.apellidos) as usuario
                  FROM stock_limites sl
                  JOIN producto p ON sl.id_producto = p.id
                  JOIN usuarios u ON sl.id_usuario = u.id
                  ORDER BY p.descripcion
                  LIMIT :por_pagina OFFSET :offset";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':por_pagina', $por_pagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'limites' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total_registros' => $total,
            'total_paginas' => ceil($total / $por_pagina),
            'pagina_actual' => $pagina
        ];
    }

    public function guardarLimites($data)
    {
        try {
            // Verificar si ya existe un registro para este producto
            $existente = $this->obtenerLimitesPorProducto($data['id_producto']);
            
            if ($existente) {
                // Actualizar registro existente
                $query = "UPDATE stock_limites SET 
                          stock_minimo = :stock_minimo,
                          stock_maximo = :stock_maximo,
                          fecha_actualizacion = NOW(),
                          id_usuario = :id_usuario
                          WHERE id_producto = :id_producto";
            } else {
                // Crear nuevo registro
                $query = "INSERT INTO stock_limites (
                          id_producto, 
                          stock_minimo, 
                          stock_maximo,
                          id_usuario
                          ) VALUES (
                          :id_producto, 
                          :stock_minimo, 
                          :stock_maximo,
                          :id_usuario)";
            }
            
            $stmt = $this->db->prepare($query);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            $this->errors[] = "Error de base de datos: " . $e->getMessage();
            return false;
        }
    }

    public function obtenerProductosSinLimite()
    {
        $query = "SELECT p.id, p.descripcion, p.cantidad FROM producto p
                  LEFT JOIN stock_limites sl ON p.id = sl.id_producto
                  WHERE sl.id_producto IS NULL AND p.id_estatus = 1
                  ORDER BY p.descripcion";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProductosConStockBajo()
    {
        $query = "SELECT p.id, p.descripcion, p.cantidad, sl.stock_minimo
                  FROM producto p
                  JOIN stock_limites sl ON p.id = sl.id_producto
                  WHERE p.cantidad <= sl.stock_minimo AND p.id_estatus = 1
                  ORDER BY p.descripcion";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProductosConStockAlto()
    {
        $query = "SELECT p.id, p.descripcion, p.cantidad, sl.stock_maximo
                  FROM producto p
                  JOIN stock_limites sl ON p.id = sl.id_producto
                  WHERE p.cantidad >= sl.stock_maximo AND p.id_estatus = 1
                  ORDER BY p.descripcion";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}