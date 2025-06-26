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
                  e.nombre as estado,
                  (SELECT COUNT(*) FROM movimientos_inventario WHERE id_movimiento_original = mi.id) as tiene_ajuste,
                  CASE 
                    WHEN mi.tipo_movimiento = 'ENTRADA-AJUSTADA' THEN CONCAT('Entrada ajustada #', mi.id, ' (Original #', mi.id_movimiento_original, ')')
                    WHEN mi.tipo_movimiento = 'SALIDA-AJUSTADA' THEN CONCAT('Salida ajustada #', mi.id, ' (Original #', mi.id_movimiento_original, ')')
                    WHEN mi.tipo_referencia = 'VENTA' THEN CONCAT('Venta #', mi.id_referencia)
                    WHEN mi.tipo_referencia = 'COMPRA' THEN CONCAT('Compra #', mi.id_referencia)
                    ELSE mi.observaciones
                  END as referencia
                  FROM movimientos_inventario mi
                  JOIN producto p ON mi.id_producto = p.id
                  JOIN usuarios u ON mi.id_usuario = u.id
                  JOIN estatus e ON mi.id_estatus = e.id
                  ORDER BY mi.fecha_movimiento DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerMovimientoPorId($id)
    {
        $query = "SELECT mi.*, p.descripcion as producto, e.nombre as estado,
                  CONCAT(u.nombres, ' ', u.apellidos) as usuario,
                  -- Obtener información del movimiento original si es un ajuste
                  mo.tipo_movimiento as tipo_movimiento_original,
                  mo.cantidad as cantidad_original,
                  mo.precio_unitario as precio_unitario_original,
                  mo.fecha_movimiento as fecha_movimiento_original,
                  -- Obtener información de la venta relacionada si existe
                  v.monto_total as monto_venta,
                  CONCAT(c.nombres, ' ', c.apellidos) as cliente_venta
                  FROM movimientos_inventario mi
                  JOIN producto p ON mi.id_producto = p.id
                  JOIN estatus e ON mi.id_estatus = e.id
                  JOIN usuarios u ON mi.id_usuario = u.id
                  LEFT JOIN movimientos_inventario mo ON mi.id_movimiento_original = mo.id
                  LEFT JOIN ventas v ON (mi.tipo_referencia = 'VENTA' AND mi.id_referencia = v.id)
                  LEFT JOIN clientes c ON v.cedula_cliente = c.cedula
                  WHERE mi.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registrarMovimiento($data)
    {
        try {
            // Validate maximum stock for entries
            if ($data['tipo_movimiento'] === 'ENTRADA') {
                $query = "SELECT stock_maximo, cantidad FROM producto WHERE id = :id_producto";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id_producto", $data['id_producto']);
                $stmt->execute();
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($producto && ($producto['cantidad'] + $data['cantidad']) > $producto['stock_maximo']) {
                    $this->errors[] = "La cantidad excede el stock máximo permitido para este producto";
                    return false;
                }
            }

            $query = "INSERT INTO movimientos_inventario (
                id_producto, 
                tipo_movimiento, 
                cantidad, 
                precio_unitario, 
                id_referencia, 
                tipo_referencia, 
                id_usuario, 
                observaciones,
                id_estatus
            ) VALUES (
                :id_producto, 
                :tipo_movimiento, 
                :cantidad, 
                :precio_unitario, 
                :id_referencia, 
                :tipo_referencia, 
                :id_usuario, 
                :observaciones,
                1
            )";
            
            $stmt = $this->db->prepare($query);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            $this->errors[] = "Error de base de datos: " . $e->getMessage();
            return false;
        }
    }

    public function actualizarMovimiento($id, $data) 
    {
        try {
            $this->db->beginTransaction();

            // Get current movement to compare
            $movimientoActual = $this->obtenerMovimientoPorId($id);
            
            if (!$movimientoActual) {
                throw new PDOException("Movimiento no encontrado");
            }

            // Mover el registro actual a la tabla histórica
            $query = "INSERT INTO movimientos_inventario_historico (
                id_movimiento_original, id_producto, tipo_movimiento, cantidad,
                precio_unitario, id_referencia, tipo_referencia, fecha_movimiento,
                id_usuario, observaciones
            ) SELECT 
                id, id_producto, tipo_movimiento, cantidad,
                precio_unitario, id_referencia, tipo_referencia, fecha_movimiento,
                id_usuario, observaciones
            FROM movimientos_inventario WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            // Eliminar el registro original
            $query = "DELETE FROM movimientos_inventario WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            
            // Crear nuevo movimiento ajustado
            $tipo_ajustado = $movimientoActual['tipo_movimiento'] == 'ENTRADA' ? 'ENTRADA-AJUSTADA' : 'SALIDA-AJUSTADA';
            
            $query = "INSERT INTO movimientos_inventario (
                id_producto, 
                tipo_movimiento, 
                cantidad, 
                precio_unitario, 
                id_referencia, 
                tipo_referencia, 
                id_usuario, 
                observaciones,
                id_movimiento_original
            ) VALUES (
                :id_producto, 
                :tipo_movimiento, 
                :cantidad, 
                :precio_unitario, 
                :id_referencia, 
                :tipo_referencia, 
                :id_usuario, 
                :observaciones,
                :id_movimiento_original
            )";
            
            $stmt = $this->db->prepare($query);
            $params = [
                ':id_producto' => $movimientoActual['id_producto'],
                ':tipo_movimiento' => $tipo_ajustado,
                ':cantidad' => $data['cantidad'],
                ':precio_unitario' => $data['precio_unitario'],
                ':id_referencia' => $movimientoActual['id_referencia'],
                ':tipo_referencia' => $movimientoActual['tipo_referencia'],
                ':id_usuario' => $_SESSION['user_id'],
                ':observaciones' => "Ajuste del movimiento #{$id} - " . ($data['observaciones'] ?? ''),
                ':id_movimiento_original' => $id
            ];
            
            $stmt->execute($params);
            
            // Actualizar stock
            if ($movimientoActual['tipo_movimiento'] === 'ENTRADA') {
                $diferencia = $data['cantidad'] - $movimientoActual['cantidad'];
            } else {
                $diferencia = $movimientoActual['cantidad'] - $data['cantidad'];
            }
            
            $query = "UPDATE producto SET cantidad = cantidad + :diferencia WHERE id = :id_producto";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":diferencia", $diferencia);
            $stmt->bindParam(":id_producto", $movimientoActual['id_producto']);
            $stmt->execute();
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            $this->errors[] = "Error al actualizar movimiento: " . $e->getMessage();
            return false;
        }
    }

    public function obtenerMovimientosPorProducto($idProducto)
    {
        $query = "SELECT mi.*, 
                  CONCAT(u.nombres, ' ', u.apellidos) as usuario,
                  DATE_FORMAT(mi.fecha_movimiento, '%d/%m/%Y %H:%i') as fecha_formateada,
                  e.nombre as estado
                  FROM movimientos_inventario mi
                  JOIN usuarios u ON mi.id_usuario = u.id
                  JOIN estatus e ON mi.id_estatus = e.id
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
                    SUM(CASE WHEN mi.tipo_movimiento = 'ENTRADA' AND mi.id_estatus = 1 THEN mi.cantidad ELSE 0 END) as entradas,
                    SUM(CASE WHEN mi.tipo_movimiento = 'SALIDA' AND mi.id_estatus = 1 THEN mi.cantidad ELSE 0 END) as salidas,
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

    public function obtenerVentaParaModificacion($id_venta)
    {
        $query = "SELECT v.*, 
                  CONCAT(c.nombres, ' ', c.apellidos) as cliente,
                  CONCAT(u.nombres, ' ', u.apellidos) as usuario
                  FROM ventas v
                  JOIN clientes c ON v.cedula_cliente = c.cedula
                  JOIN usuarios u ON v.id_usuario = u.id
                  WHERE v.id = :id AND v.id_estatus = 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id_venta);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerDetallesVentaParaModificacion($id_venta)
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

    public function modificarVentaConAjuste($id_venta, $data)
    {
        try {
            $this->db->beginTransaction();

            // 1. Obtener venta actual y sus detalles
            $venta_actual = $this->obtenerVentaParaModificacion($id_venta);
            if (!$venta_actual) {
                throw new PDOException("Venta no encontrada o ya ha sido modificada");
            }
            $detalles_actuales = $this->obtenerDetallesVentaParaModificacion($id_venta);

            // 2. Marcar venta original como histórica
            $query = "UPDATE ventas SET id_estatus = 2 WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id_venta);
            $stmt->execute();

            // 3. Crear nueva venta (como ajuste)
            $query = "INSERT INTO ventas (
                cedula_cliente, id_usuario, fecha, monto_total, 
                forma_pago, referencia_pago, id_venta_original, id_estatus
            ) VALUES (
                :cedula_cliente, :id_usuario, :fecha, :monto_total, 
                :forma_pago, :referencia_pago, :id_venta_original, 1
            )";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":cedula_cliente", $data['cedula_cliente']);
            $stmt->bindParam(":id_usuario", $data['id_usuario']);
            $stmt->bindParam(":fecha", $data['fecha']);
            $stmt->bindParam(":monto_total", $data['monto_total']);
            $stmt->bindParam(":forma_pago", $data['forma_pago']);
            $stmt->bindParam(":referencia_pago", $data['referencia_pago']);
            $stmt->bindParam(":id_venta_original", $id_venta);
            $stmt->execute();

            $nuevo_id_venta = $this->db->lastInsertId();

            // 4. Procesar productos y movimientos de inventario
            foreach ($data['productos'] as $id_detalle => $producto) {
                // Registrar detalle de venta
                $query = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, monto) 
                          VALUES (:id_venta, :id_producto, :cantidad, :monto)";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id_venta", $nuevo_id_venta);
                $stmt->bindParam(":id_producto", $producto['id_producto']);
                $stmt->bindParam(":cantidad", $producto['cantidad']);
                $stmt->bindParam(":monto", $producto['subtotal']);
                $stmt->execute();

                // Buscar el movimiento original de salida para este producto
                $query = "SELECT id FROM movimientos_inventario 
                          WHERE tipo_movimiento = 'SALIDA' 
                          AND tipo_referencia = 'VENTA' 
                          AND id_referencia = :id_venta 
                          AND id_producto = :id_producto 
                          AND id_estatus = 1";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id_venta", $id_venta);
                $stmt->bindParam(":id_producto", $producto['id_producto']);
                $stmt->execute();
                $movimiento_original = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($movimiento_original) {
                    // Marcar el movimiento original como histórico
                    $query = "UPDATE movimientos_inventario SET id_estatus = 2 
                              WHERE id = :id";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(":id", $movimiento_original['id']);
                    $stmt->execute();

                    // Registrar nuevo movimiento de salida (ajuste de venta)
                    $query = "INSERT INTO movimientos_inventario (
                        id_producto, tipo_movimiento, cantidad, precio_unitario, 
                        id_referencia, tipo_referencia, id_usuario, id_movimiento_original,
                        observaciones, id_estatus
                    ) VALUES (
                        :id_producto, 'SALIDA', :cantidad, :precio_unitario, 
                        :id_referencia, 'VENTA', :id_usuario, :id_movimiento_original,
                        :observaciones, 1
                    )";
                    
                    $observaciones = "Modificación de venta #{$id_venta} - Nueva venta #{$nuevo_id_venta}";
                    
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(":id_producto", $producto['id_producto']);
                    $stmt->bindParam(":cantidad", $producto['cantidad']);
                    $stmt->bindParam(":precio_unitario", $producto['precio_unitario']);
                    $stmt->bindParam(":id_referencia", $nuevo_id_venta);
                    $stmt->bindParam(":id_usuario", $data['id_usuario']);
                    $stmt->bindParam(":id_movimiento_original", $movimiento_original['id']);
                    $stmt->bindParam(":observaciones", $observaciones);
                    $stmt->execute();

                    // Ajustar stock
                    $diferencia = $this->calcularDiferenciaStock($id_venta, $producto['id_producto'], $producto['cantidad']);
                    
                    $query = "UPDATE producto SET cantidad = cantidad + :diferencia 
                              WHERE id = :id_producto";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(":diferencia", $diferencia);
                    $stmt->bindParam(":id_producto", $producto['id_producto']);
                    $stmt->execute();
                }
            }

            $this->db->commit();
            return $nuevo_id_venta;
        } catch (PDOException $e) {
            $this->db->rollBack();
            $this->errors[] = "Error al modificar venta: " . $e->getMessage();
            return false;
        }
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
}