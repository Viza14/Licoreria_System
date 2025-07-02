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
                  WHERE v.id_estatus = 1
                  ORDER BY v.fecha DESC, v.id DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerVentaPorId($id) {
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
    $venta = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($venta) {
        // Obtener los pagos de la venta
        $query = "SELECT * FROM pagos_venta WHERE id_venta = :id_venta ORDER BY fecha_pago ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id_venta", $id);
        $stmt->execute();
        $venta['pagos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return $venta;
}

    public function obtenerDetallesVenta($id_venta)
    {
        $query = "SELECT dv.*, p.descripcion as producto, p.precio as precio_actual
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

            // Validate total payments match total amount
            $total_pagos = 0;
            foreach ($data['pagos'] as $pago) {
                $total_pagos += $pago['monto'];
            }
            
            $total_productos = 0;
            foreach ($data['productos'] as $producto) {
                $total_productos += $producto['precio'] * $producto['cantidad'];
            }
            
            if (abs($total_pagos - $total_productos) > 0.01) {
                throw new Exception("El total de los pagos no coincide con el monto total de la venta");
            }

            $fecha = isset($data['fecha']) ? $data['fecha'] : date('Y-m-d H:i:s');

            // Register main sale with status
            $query = "INSERT INTO ventas (cedula_cliente, id_usuario, fecha, monto_total, id_estatus) 
                      VALUES (:cedula_cliente, :id_usuario, :fecha, :monto_total, 1)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":cedula_cliente", $data['cedula_cliente']);
            $stmt->bindParam(":id_usuario", $data['id_usuario']);
            $stmt->bindParam(":fecha", $fecha);
            $stmt->bindParam(":monto_total", $total_productos);
            $stmt->execute();
            
            $id_venta = $this->db->lastInsertId();
            $monto_total = $total_productos;

            foreach ($data['productos'] as $producto) {
                $subtotal = $producto['precio'] * $producto['cantidad'];
                $monto_total += $subtotal;

                // Register sale detail
                $query = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, monto, precio_unitario) 
                          VALUES (:id_venta, :id_producto, :cantidad, :monto, :precio_unitario)";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id_venta", $id_venta);
                $stmt->bindParam(":id_producto", $producto['id']);
                $stmt->bindParam(":cantidad", $producto['cantidad']);
                $stmt->bindParam(":monto", $subtotal);
                $stmt->bindParam(":precio_unitario", $producto['precio']);
                $stmt->execute();

                // Register inventory movement
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

                // Update product stock
                $query = "UPDATE producto SET cantidad = cantidad - :cantidad WHERE id = :id_producto";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":cantidad", $producto['cantidad']);
                $stmt->bindParam(":id_producto", $producto['id']);
                $stmt->execute();
            }

            // Update total amount
            $query = "UPDATE ventas SET monto_total = :monto_total WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":monto_total", $monto_total);
            $stmt->bindParam(":id", $id_venta);
            $stmt->execute();

            // Register payments
            if (isset($data['pagos']) && is_array($data['pagos'])) {
                foreach ($data['pagos'] as $pago) {
                    $query = "INSERT INTO pagos_venta (id_venta, forma_pago, monto, referencia_pago) 
                              VALUES (:id_venta, :forma_pago, :monto, :referencia_pago)";
                    
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(":id_venta", $id_venta);
                    $stmt->bindParam(":forma_pago", $pago['forma_pago']);
                    $stmt->bindParam(":monto", $pago['monto']);
                    $stmt->bindParam(":referencia_pago", $pago['referencia_pago']);
                    $stmt->execute();
                }
            }

            $this->db->commit();
            return $id_venta;
        } catch (PDOException $e) {
            $this->db->rollBack();
            $this->errors[] = "Error al registrar venta: " . $e->getMessage();
            return false;
        }
    }

    public function actualizarVenta($id, $data)
    {
        try {
            $this->db->beginTransaction();

            // Get current sale and validate
            $venta_actual = $this->obtenerVentaPorId($id);
            if (!$venta_actual || $venta_actual['id_estatus'] != 1) {
                throw new Exception("Venta no encontrada o no editable");
            }

            // Mark current sale as inactive
            $query = "UPDATE ventas SET id_estatus = 2 WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            // Create new sale as adjustment
            $query = "INSERT INTO ventas (
                cedula_cliente, id_usuario, fecha, monto_total,
                id_venta_original, id_estatus
            ) VALUES (
                :cedula_cliente, :id_usuario, :fecha, :monto_total,
                :id_venta_original, 1
            )";

            $params = [
                ':cedula_cliente' => $data['cedula_cliente'],
                ':id_usuario' => $data['id_usuario'],
                ':fecha' => $data['fecha'],
                ':monto_total' => 0, // Se actualizará después
                ':id_venta_original' => $id
            ];

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $nuevo_id_venta = $this->db->lastInsertId();

            // Register payments
            foreach ($data['pagos'] as $pago) {
                $query = "INSERT INTO pagos_venta (
                    id_venta, forma_pago, monto, referencia_pago
                ) VALUES (
                    :id_venta, :forma_pago, :monto, :referencia_pago
                )";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id_venta", $nuevo_id_venta);
                $stmt->bindParam(":forma_pago", $pago['forma_pago']);
                $stmt->bindParam(":monto", $pago['monto']);
                $stmt->bindParam(":referencia_pago", $pago['referencia_pago']);
                $stmt->execute();
            }

            $monto_total = 0;

            // Process products
            foreach ($data['productos'] as $producto) {
                $subtotal = $producto['precio'] * $producto['cantidad'];
                $monto_total += $subtotal;

                // Register sale detail
                $query = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, monto, precio_unitario) 
                          VALUES (:id_venta, :id_producto, :cantidad, :monto, :precio_unitario)";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id_venta", $nuevo_id_venta);
                $stmt->bindParam(":id_producto", $producto['id']);
                $stmt->bindParam(":cantidad", $producto['cantidad']);
                $stmt->bindParam(":monto", $subtotal);
                $stmt->bindParam(":precio_unitario", $producto['precio']);
                $stmt->execute();

                // Find and update original inventory movement
                $query = "SELECT id FROM movimientos_inventario 
                          WHERE tipo_movimiento = 'SALIDA' 
                          AND tipo_referencia = 'VENTA' 
                          AND id_referencia = :id_venta 
                          AND id_producto = :id_producto 
                          AND id_estatus = 1";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id_venta", $id);
                $stmt->bindParam(":id_producto", $producto['id']);
                $stmt->execute();
                $movimiento_original = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($movimiento_original) {
                    // Mark original movement as inactive
                    $query = "UPDATE movimientos_inventario SET id_estatus = 2 WHERE id = :id";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(":id", $movimiento_original['id']);
                    $stmt->execute();

                    // Create new inventory movement as adjustment
                    $query = "INSERT INTO movimientos_inventario (
                        id_producto, tipo_movimiento, cantidad, precio_unitario,
                        id_referencia, tipo_referencia, id_usuario, id_movimiento_original,
                        observaciones, id_estatus
                    ) VALUES (
                        :id_producto, 'SALIDA', :cantidad, :precio_unitario,
                        :id_referencia, 'VENTA', :id_usuario, :id_movimiento_original,
                        :observaciones, 1
                    )";

                    $observaciones = "Ajuste de venta #{$id} - Nueva venta #{$nuevo_id_venta}";

                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(":id_producto", $producto['id']);
                    $stmt->bindParam(":cantidad", $producto['cantidad']);
                    $stmt->bindParam(":precio_unitario", $producto['precio']);
                    $stmt->bindParam(":id_referencia", $nuevo_id_venta);
                    $stmt->bindParam(":id_usuario", $data['id_usuario']);
                    $stmt->bindParam(":id_movimiento_original", $movimiento_original['id']);
                    $stmt->bindParam(":observaciones", $observaciones);
                    $stmt->execute();

                    // Update product stock
                    $diferencia = $this->calcularDiferenciaStock($id, $producto['id'], $producto['cantidad']);
                    $query = "UPDATE producto SET cantidad = cantidad + :diferencia WHERE id = :id_producto";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(":diferencia", $diferencia);
                    $stmt->bindParam(":id_producto", $producto['id']);
                    $stmt->execute();
                }
            }

            // Update total amount
            $query = "UPDATE ventas SET monto_total = :monto_total WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":monto_total", $monto_total);
            $stmt->bindParam(":id", $nuevo_id_venta);
            $stmt->execute();

            $this->db->commit();
            return $nuevo_id_venta;

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    public function obtenerTotalVentasHoy()
    {
        $query = "SELECT SUM(monto_total) as total FROM ventas WHERE DATE(fecha) = CURDATE() AND id_estatus = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    private function calcularDiferenciaStock($id_venta_original, $id_producto, $nueva_cantidad)
    {
        // Obtener cantidad original de este producto en la venta
        $query = "SELECT cantidad FROM detalle_venta 
                  WHERE id_venta = :id_venta AND id_producto = :id_producto";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id_venta", $id_venta_original);
        $stmt->bindParam(":id_producto", $id_producto);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $cantidad_original = $result ? $result['cantidad'] : 0;
        
        // Para salidas de inventario:
        // Si la nueva cantidad es mayor que la original, debemos RESTAR más al stock (diferencia negativa)
        // Si la nueva cantidad es menor que la original, debemos RESTAR menos al stock (diferencia positiva)
        return $cantidad_original - $nueva_cantidad; // Mantiene la lógica correcta para SALIDAS
    }

    public function obtenerVentasRecientes($limit = 5)
    {
        $query = "SELECT v.id, v.fecha, v.monto_total, 
                  CONCAT(c.nombres, ' ', c.apellidos) as cliente
                  FROM ventas v
                  JOIN clientes c ON v.cedula_cliente = c.cedula
                  WHERE v.id_estatus = 1
                  ORDER BY v.fecha DESC
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarVentasHoy() {
        try {
            $query = "SELECT COUNT(*) as total FROM ventas WHERE DATE(fecha) = CURDATE() AND id_estatus = 1";
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
                      WHERE DATE(fecha) = CURDATE() AND id_estatus = 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
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
                       GROUP_CONCAT(CONCAT(p.descripcion, ' (', dv.cantidad, ')')) AS productos
                FROM ventas v
                JOIN clientes c ON v.cedula_cliente = c.cedula
                JOIN usuarios u ON v.id_usuario = u.id
                JOIN detalle_venta dv ON v.id = dv.id_venta
                JOIN producto p ON dv.id_producto = p.id
                WHERE v.id_estatus = 1";
        
        $params = [];
        
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