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

    public function obtenerTodosMovimientos($pagina = 1, $porPagina = 10, $filtros = [])
    {
        // Calculate offset
        $offset = ($pagina - 1) * $porPagina;

        // Base query for both count and data
        $baseQuery = "FROM movimientos_inventario mi
                      JOIN producto p ON mi.id_producto = p.id
                      JOIN usuarios u ON mi.id_usuario = u.id
                      JOIN estatus e ON mi.id_estatus = e.id";

        // Build WHERE clause based on filters
        $whereConditions = [];
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $whereConditions[] = "(p.descripcion LIKE ? OR 
                                  mi.tipo_movimiento LIKE ? OR 
                                  CONCAT(u.nombres, ' ', u.apellidos) LIKE ? OR 
                                  CASE 
                                    WHEN mi.tipo_referencia = 'VENTA' THEN CONCAT('Venta #', mi.id_referencia)
                                    WHEN mi.tipo_referencia = 'COMPRA' THEN CONCAT('Compra #', mi.id_referencia)
                                    ELSE mi.observaciones
                                  END LIKE ?)";
            $searchTerm = '%' . $filtros['busqueda'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filtros['tipos'])) {
            $whereConditions[] = "mi.tipo_movimiento IN (" . implode(',', array_fill(0, count($filtros['tipos']), '?')) . ")";
            foreach ($filtros['tipos'] as $tipo) {
                $params[] = $tipo;
            }
        }

        if (!empty($filtros['estados'])) {
            $estadosConditions = [];
            foreach ($filtros['estados'] as $estado) {
                if ($estado === 'inactivo') {
                    $estadosConditions[] = "mi.id_estatus = 2";
                } elseif ($estado === 'ajustado') {
                    $estadosConditions[] = "EXISTS (SELECT 1 FROM movimientos_inventario WHERE id_movimiento_original = mi.id)";
                } elseif ($estado === 'activo') {
                    $estadosConditions[] = "mi.id_estatus = 1 AND NOT EXISTS (SELECT 1 FROM movimientos_inventario WHERE id_movimiento_original = mi.id)";
                }
            }
            if (!empty($estadosConditions)) {
                $whereConditions[] = "(" . implode(' OR ', $estadosConditions) . ")";
            }
        }

        if (!empty($filtros['fecha_inicio'])) {
            $whereConditions[] = "mi.fecha_movimiento >= ?";
            $params[] = $filtros['fecha_inicio'];
        }

        if (!empty($filtros['fecha_fin'])) {
            $whereConditions[] = "mi.fecha_movimiento <= ?";
            $params[] = $filtros['fecha_fin'];
        }

        $whereClause = !empty($whereConditions) ? " WHERE " . implode(' AND ', $whereConditions) : "";

        // Get total records for pagination
        $queryTotal = "SELECT COUNT(*) as total " . $baseQuery . $whereClause;
        $stmtTotal = $this->db->prepare($queryTotal);
        foreach ($params as $i => $param) {
            $stmtTotal->bindValue($i + 1, $param);
        }
        $stmtTotal->execute();
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

        // Get paginated records
        $query = "SELECT mi.*, 
                  p.descripcion as producto,
                  CONCAT(u.nombres, ' ', u.apellidos) as usuario,
                  e.nombre as estado,
                  (SELECT COUNT(*) FROM movimientos_inventario WHERE id_movimiento_original = mi.id) as tiene_ajuste,
                  CASE 
                    WHEN mi.tipo_referencia = 'VENTA' THEN CONCAT('Venta #', mi.id_referencia)
                    WHEN mi.tipo_referencia = 'COMPRA' THEN CONCAT('Compra #', mi.id_referencia)
                    ELSE mi.observaciones
                  END as referencia
                  " . $baseQuery . $whereClause . "
                  ORDER BY mi.fecha_movimiento DESC
                  LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($query);
        foreach ($params as $i => $param) {
            $stmt->bindValue($i + 1, $param);
        }
        $stmt->bindValue(count($params) + 1, $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'pagina_actual' => $pagina,
            'por_pagina' => $porPagina,
            'total_paginas' => ceil($total / $porPagina)
        ];
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
                  mo.observaciones as observaciones_original,
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

    private function obtenerSiguienteNumeroEntrada() {
        $query = "SELECT MAX(id) as ultimo_id FROM movimientos_inventario WHERE tipo_movimiento = 'ENTRADA'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['ultimo_id'] ?? 0) + 1;
    }

    public function actualizarMovimiento($id, $data) 
    {
        try {
            $this->db->beginTransaction();
            error_log("Transacción iniciada");

            // 1. Get current movement
            $movimientoActual = $this->obtenerMovimientoPorId($id);
            
            if (!$movimientoActual) {
                throw new PDOException("Movimiento no encontrado");
            }

            // 2. Marcar el movimiento original como inactivo
            $query = "UPDATE movimientos_inventario SET id_estatus = 2 WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            // 3. Crear nuevo movimiento como ajuste
            $query = "INSERT INTO movimientos_inventario (
                id_producto, tipo_movimiento, cantidad, precio_unitario,
                id_referencia, tipo_referencia, id_usuario, observaciones,
                id_estatus, id_movimiento_original
            ) VALUES (
                :id_producto, :tipo_movimiento, :cantidad, :precio_unitario,
                :id_referencia, :tipo_referencia, :id_usuario, :observaciones,
                1, :id_movimiento_original
            )";

            // Extraer el número de entrada original si existe
            $numeroEntrada = null;
            if (preg_match('/Entrada #(\d+)/', $movimientoActual['observaciones'], $matches)) {
                $numeroEntrada = $matches[1];
            }
            
            // Si no se encuentra un número de entrada en el movimiento original, obtener el siguiente
            if (!$numeroEntrada) {
                $numeroEntrada = $this->obtenerSiguienteNumeroEntrada();
            }
            
            $observaciones = "Entrada #" . $numeroEntrada . ($data['observaciones'] ? " - " . $data['observaciones'] : "");
            
            $params = [
                ':id_producto' => $movimientoActual['id_producto'],
                ':tipo_movimiento' => 'ENTRADA',
                ':cantidad' => $data['cantidad'],
                ':precio_unitario' => $data['precio_unitario'],
                ':id_referencia' => $movimientoActual['id_referencia'],
                ':tipo_referencia' => $movimientoActual['tipo_referencia'],
                ':id_usuario' => $movimientoActual['id_usuario'],
                ':observaciones' => $observaciones,
                ':id_movimiento_original' => $id
            ];
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);

            // 4. Actualizar stock del producto
            $diferencia = $data['cantidad'] - $movimientoActual['cantidad'];
            
            $query = "UPDATE producto SET cantidad = cantidad + :diferencia WHERE id = :id_producto";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":diferencia", $diferencia);
            $stmt->bindParam(":id_producto", $movimientoActual['id_producto']);
            $stmt->execute();
            
            $this->db->commit();
            error_log("Transacción completada exitosamente");
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error en la transacción: " . $e->getMessage());
            $this->errors[] = "Error al actualizar movimiento: " . $e->getMessage();
            return false;
        }
    }

    private function registrarEnHistorico($movimiento)
    {
        $query = "INSERT INTO movimientos_inventario_historico (
            id_movimiento_original, id_producto, tipo_movimiento, cantidad,
            precio_unitario, id_referencia, tipo_referencia, fecha_movimiento,
            id_usuario, observaciones
        ) VALUES (
            :id, :id_producto, :tipo_movimiento, :cantidad,
            :precio_unitario, :id_referencia, :tipo_referencia, :fecha_movimiento,
            :id_usuario, :observaciones
        )";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($movimiento);
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
                  CONCAT(u.nombres, ' ', u.apellidos) as usuario,
                  GROUP_CONCAT(
                      CONCAT(pv.forma_pago, ':', pv.monto, ':', IFNULL(pv.referencia_pago, 'NULL'))
                      SEPARATOR '|'
                  ) as pagos_info
                  FROM ventas v
                  JOIN clientes c ON v.cedula_cliente = c.cedula
                  JOIN usuarios u ON v.id_usuario = u.id
                  LEFT JOIN pagos_venta pv ON v.id = pv.id_venta
                  WHERE v.id = :id AND v.id_estatus = 1
                  GROUP BY v.id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id_venta);
        $stmt->execute();
        
        $venta = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($venta) {
            $pagos = [];
            if ($venta['pagos_info']) {
                foreach (explode('|', $venta['pagos_info']) as $pago_info) {
                    list($forma_pago, $monto, $referencia) = explode(':', $pago_info);
                    $pagos[] = [
                        'forma_pago' => $forma_pago,
                        'monto' => $monto,
                        'referencia_pago' => $referencia === 'NULL' ? null : $referencia
                    ];
                }
            }
            $venta['pagos'] = $pagos;
            unset($venta['pagos_info']);
        }
        
        return $venta;
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
        error_log("Iniciando modificación de venta #" . $id_venta);
        error_log("Datos recibidos: " . print_r($data, true));
        $this->db->beginTransaction();

        // 1. Get current sale
        $venta_actual = $this->obtenerVentaParaModificacion($id_venta);
        if (!$venta_actual) {
            throw new PDOException("Venta no encontrada o no modificable");
        }

        // 2. Mark current sale as inactive
        $query = "UPDATE ventas SET id_estatus = 2 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id_venta);
        $stmt->execute();

        // 3. Create new sale as adjustment
        $query = "INSERT INTO ventas (
            cedula_cliente, id_usuario, fecha, monto_total,
            id_venta_original, id_estatus
        ) VALUES (
            :cedula_cliente, :id_usuario, :fecha, :monto_total,
            :id_venta_original, 1
        )";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":cedula_cliente", $data['cedula_cliente']);
        $stmt->bindParam(":id_usuario", $data['id_usuario']);
        $stmt->bindParam(":fecha", $data['fecha']);
        $stmt->bindParam(":monto_total", $data['monto_total']);
        $stmt->bindParam(":id_venta_original", $id_venta);
        $stmt->execute();

        $nuevo_id_venta = $this->db->lastInsertId();
        error_log("Nueva venta creada con ID: " . $nuevo_id_venta);

        // Register payments
        error_log("Registrando pagos: " . print_r($data['pagos'], true));
        foreach ($data['pagos'] as $pago) {
            try {
                $query = "INSERT INTO pagos_venta (
                    id_venta, forma_pago, monto, referencia_pago
                ) VALUES (
                    :id_venta, :forma_pago, :monto, :referencia_pago
                )";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id_venta", $nuevo_id_venta);
                $stmt->bindParam(":forma_pago", $pago['forma_pago']);
                $stmt->bindParam(":monto", $pago['monto']);
                $stmt->bindParam(":referencia_pago", $pago['referencia_pago'], PDO::PARAM_STR);
                
                $stmt->execute();
            } catch (PDOException $e) {
                error_log("Error al insertar pago: " . $e->getMessage());
                throw $e;
            }
        }

        // Process products
        foreach ($data['productos'] as $producto) {
            $subtotal = (float)$producto['cantidad'] * (float)$producto['precio_unitario'];
            
            // Register sale detail
            $query = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, monto, precio_unitario) 
                      VALUES (:id_venta, :id_producto, :cantidad, :monto, :precio_unitario)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id_venta", $nuevo_id_venta);
            $stmt->bindParam(":id_producto", $producto['id_producto']);
            $stmt->bindParam(":cantidad", $producto['cantidad']);
            $stmt->bindParam(":monto", $subtotal);
            $stmt->bindParam(":precio_unitario", $producto['precio_unitario']);
            $stmt->execute();

            // Find original outbound movement for this product
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
                // Mark original movement as inactive
                $query = "UPDATE movimientos_inventario SET id_estatus = 2 
                          WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id", $movimiento_original['id']);
                $stmt->execute();

                // Register new outbound movement (sale adjustment)
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

                // Adjust stock
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