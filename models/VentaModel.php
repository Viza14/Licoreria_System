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

            // 1. Registrar la venta principal
            $query = "INSERT INTO ventas (cedula_cliente, id_usuario, fecha, monto_total) 
                      VALUES (:cedula_cliente, :id_usuario, NOW(), 0)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":cedula_cliente", $data['cedula_cliente']);
            $stmt->bindParam(":id_usuario", $data['id_usuario']);
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
}