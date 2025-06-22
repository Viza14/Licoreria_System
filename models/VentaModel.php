<?php
class VentaModel
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

    public function obtenerTodasVentas()
    {
        $query = "SELECT v.*, 
                  CONCAT(c.nombres, ' ', c.apellidos) as cliente,
                  CONCAT(u.nombres, ' ', u.apellidos) as usuario
                  FROM ventas v
                  JOIN clientes c ON v.cedula_cliente = c.cedula
                  JOIN usuarios u ON v.id_usuario = u.id
                  ORDER BY v.fecha DESC, v.id DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerVentaPorId($id)
    {
        $query = "SELECT v.*, 
                  CONCAT(c.nombres, ' ', c.apellidos) as cliente,
                  CONCAT(u.nombres, ' ', u.apellidos) as usuario
                  FROM ventas v
                  JOIN clientes c ON v.cedula_cliente = c.cedula
                  JOIN usuarios u ON v.id_usuario = u.id
                  WHERE v.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerDetallesVenta($id_venta)
    {
        $query = "SELECT dv.*, p.descripcion as producto
                  FROM detalle_venta dv
                  JOIN producto p ON dv.id_producto = p.id
                  WHERE dv.id_venta = :id_venta";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id_venta", $id_venta);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarVenta($data)
    {
        try {
            $this->db->beginTransaction();

            // Get date from form data or use current date/time
            $fecha = isset($data['fecha']) ? $data['fecha'] : date('Y-m-d H:i:s');

            // 1. Registrar la venta principal
            $query = "INSERT INTO ventas (cedula_cliente, id_usuario, fecha, monto_total, forma_pago, referencia_pago) 
                      VALUES (:cedula_cliente, :id_usuario, :fecha, 0, :forma_pago, :referencia_pago)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":cedula_cliente", $data['cedula_cliente']);
            $stmt->bindParam(":id_usuario", $data['id_usuario']);
            $stmt->bindParam(":fecha", $fecha);
            $stmt->bindParam(":forma_pago", $data['forma_pago']);
            $stmt->bindParam(":referencia_pago", $data['referencia_pago']);
            $stmt->execute();
            
            $id_venta = $this->db->lastInsertId();
            $monto_total = 0;

            // 2. Registrar detalles de venta y actualizar stock
            foreach ($data['productos'] as $producto) {
                // Calcular subtotal
                $subtotal = $producto['precio'] * $producto['cantidad'];
                $monto_total += $subtotal;

                // Registrar detalle
                $query = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, monto) 
                          VALUES (:id_venta, :id_producto, :cantidad, :monto)";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id_venta", $id_venta);
                $stmt->bindParam(":id_producto", $producto['id']);
                $stmt->bindParam(":cantidad", $producto['cantidad']);
                $stmt->bindParam(":monto", $subtotal);
                $stmt->execute();

                // Registrar movimiento de inventario
                $query = "INSERT INTO movimientos_inventario 
                          (id_producto, tipo_movimiento, cantidad, precio_unitario, id_referencia, tipo_referencia, id_usuario) 
                          VALUES (:id_producto, 'SALIDA', :cantidad, :precio_unitario, :id_referencia, 'VENTA', :id_usuario)";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id_producto", $producto['id']);
                $stmt->bindParam(":cantidad", $producto['cantidad']);
                $stmt->bindParam(":precio_unitario", $producto['precio']);
                $stmt->bindParam(":id_referencia", $id_venta);
                $stmt->bindParam(":id_usuario", $data['id_usuario']);
                $stmt->execute();

                // Actualizar stock del producto
                $query = "UPDATE producto SET cantidad = cantidad - :cantidad WHERE id = :id_producto";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":cantidad", $producto['cantidad']);
                $stmt->bindParam(":id_producto", $producto['id']);
                $stmt->execute();
            }

            // 3. Actualizar monto total de la venta
            $query = "UPDATE ventas SET monto_total = :monto_total WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":monto_total", $monto_total);
            $stmt->bindParam(":id", $id_venta);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            $this->errors[] = "Error al registrar venta: " . $e->getMessage();
            return false;
        }
    }

    public function obtenerTotalVentasHoy()
    {
        $query = "SELECT SUM(monto_total) as total FROM ventas WHERE DATE(fecha) = CURDATE()";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function obtenerVentasRecientes($limit = 5)
    {
        $query = "SELECT v.id, v.fecha, v.monto_total, 
                  CONCAT(c.nombres, ' ', c.apellidos) as cliente
                  FROM ventas v
                  JOIN clientes c ON v.cedula_cliente = c.cedula
                  ORDER BY v.fecha DESC
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarVentasHoy() {
        try {
            $query = "SELECT COUNT(*) as total FROM ventas WHERE DATE(fecha) = CURDATE()";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['total'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error contando ventas hoy: " . $e->getMessage());
            return 0;
        }
    }
    
    public function calcularIngresosHoy() {
        try {
            $query = "SELECT IFNULL(SUM(monto_total), 0) as ingresos 
                      FROM ventas 
                      WHERE DATE(fecha) = CURDATE()";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Asegurarse de devolver un float válido
            $ingresos = (float)$result['ingresos'];
            return is_finite($ingresos) ? $ingresos : 0;
        } catch (PDOException $e) {
            error_log("Error al calcular ingresos hoy: " . $e->getMessage());
            return 0;
        }
    }

    public function generarReporteVentas($filtros = [])
{
    $sql = "SELECT v.id, v.fecha, 
                   CONCAT(c.nombres, ' ', c.apellidos) AS cliente,
                   c.cedula,
                   CONCAT(u.nombres, ' ', u.apellidos) AS usuario,
                   v.monto_total,
                   COUNT(dv.id) AS cantidad_productos,
                   GROUP_CONCAT(CONCAT(p.descripcion, ' (', dv.cantidad, ')') AS productos
            FROM ventas v
            JOIN clientes c ON v.cedula_cliente = c.cedula
            JOIN usuarios u ON v.id_usuario = u.id
            JOIN detalles_venta dv ON v.id = dv.id_venta
            JOIN productos p ON dv.id_producto = p.id
            WHERE 1=1";
    
    $params = [];
    
    // Aplicar filtros
    if (!empty($filtros['fecha_inicio'])) {
        $sql .= " AND v.fecha >= ?";
        $params[] = $filtros['fecha_inicio'];
    }
    
    if (!empty($filtros['fecha_fin'])) {
        $sql .= " AND v.fecha <= ?";
        $params[] = $filtros['fecha_fin'] . ' 23:59:59';
    }
    
    if (!empty($filtros['id_usuario'])) {
        $sql .= " AND v.id_usuario = ?";
        $params[] = $filtros['id_usuario'];
    }
    
    if (!empty($filtros['cedula_cliente'])) {
        $sql .= " AND v.cedula_cliente = ?";
        $params[] = $filtros['cedula_cliente'];
    }
    
    $sql .= " GROUP BY v.id ORDER BY v.fecha DESC";
    
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}