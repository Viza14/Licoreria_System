<?php
class MovimientoInventarioModel
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

    public function obtenerTodosMovimientos()
    {
        $query = "SELECT mi.*, 
                  p.descripcion as producto,
                  CONCAT(u.nombres, ' ', u.apellidos) as usuario,
                  CASE 
                    WHEN mi.tipo_referencia = 'VENTA' THEN CONCAT('Venta #', mi.id_referencia)
                    WHEN mi.tipo_referencia = 'COMPRA' THEN CONCAT('Compra #', mi.id_referencia)
                    ELSE mi.observaciones
                  END as referencia
                  FROM movimientos_inventario mi
                  JOIN producto p ON mi.id_producto = p.id
                  JOIN usuarios u ON mi.id_usuario = u.id
                  ORDER BY mi.fecha_movimiento DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerMovimientoPorId($id)
    {
        $query = "SELECT mi.*, p.descripcion as producto 
                  FROM movimientos_inventario mi
                  JOIN producto p ON mi.id_producto = p.id
                  WHERE mi.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registrarMovimiento($data)
    {
        try {
            $query = "INSERT INTO movimientos_inventario (
                id_producto, 
                tipo_movimiento, 
                cantidad, 
                precio_unitario, 
                id_referencia, 
                tipo_referencia, 
                id_usuario, 
                observaciones
            ) VALUES (
                :id_producto, 
                :tipo_movimiento, 
                :cantidad, 
                :precio_unitario, 
                :id_referencia, 
                :tipo_referencia, 
                :id_usuario, 
                :observaciones
            )";
            
            $stmt = $this->db->prepare($query);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            $this->errors[] = "Error de base de datos: " . $e->getMessage();
            return false;
        }
    }

    public function obtenerMovimientosPorProducto($idProducto)
    {
        $query = "SELECT mi.*, 
                  CONCAT(u.nombres, ' ', u.apellidos) as usuario,
                  DATE_FORMAT(mi.fecha_movimiento, '%d/%m/%Y %H:%i') as fecha_formateada
                  FROM movimientos_inventario mi
                  JOIN usuarios u ON mi.id_usuario = u.id
                  WHERE mi.id_producto = :id_producto
                  ORDER BY mi.fecha_movimiento DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id_producto", $idProducto);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerResumenMovimientos($filtros = [])
    {
        $query = "SELECT 
                    p.id,
                    p.descripcion as producto,
                    c.nombre as categoria,
                    SUM(CASE WHEN mi.tipo_movimiento = 'ENTRADA' THEN mi.cantidad ELSE 0 END) as entradas,
                    SUM(CASE WHEN mi.tipo_movimiento = 'SALIDA' THEN mi.cantidad ELSE 0 END) as salidas,
                    p.cantidad as stock_actual
                  FROM producto p
                  JOIN categorias c ON p.id_categoria = c.id
                  LEFT JOIN movimientos_inventario mi ON p.id = mi.id_producto";
        
        $where = [];
        $params = [];
        
        // Filtros opcionales
        if (!empty($filtros['fecha_inicio'])) {
            $where[] = "mi.fecha_movimiento >= :fecha_inicio";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $where[] = "mi.fecha_movimiento <= :fecha_fin";
            $params[':fecha_fin'] = $filtros['fecha_fin'];
        }
        
        if (!empty($filtros['id_producto'])) {
            $where[] = "p.id = :id_producto";
            $params[':id_producto'] = $filtros['id_producto'];
        }
        
        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }
        
        $query .= " GROUP BY p.id, p.descripcion, c.nombre, p.cantidad
                    ORDER BY p.descripcion";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}