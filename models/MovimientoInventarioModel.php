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
                if ($estado === 'inactivo' || $estado === '2') {
                    $estadosConditions[] = "mi.id_estatus = 2";
                } elseif ($estado === 'ajustado') {
                    $estadosConditions[] = "EXISTS (SELECT 1 FROM movimientos_inventario WHERE id_movimiento_original = mi.id)";
                } elseif ($estado === 'activo' || $estado === '1') {
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
                  mi.numero_transaccion,
                  (SELECT COUNT(*) FROM movimientos_inventario WHERE id_movimiento_original = mi.id) as tiene_ajuste,
                  CASE 
                    WHEN mi.tipo_referencia = 'VENTA' THEN CONCAT('Venta #', mi.id_referencia)
                    WHEN mi.subtipo_movimiento = 'COMPRA' THEN CONCAT('Compra #', mi.numero_transaccion)
                    ELSE mi.observaciones
                  END as referencia,
                  -- Formatear observaciones según el tipo de movimiento
                  CASE 
                    WHEN mi.subtipo_movimiento = 'AJUSTE' THEN
                        CASE 
                            WHEN mi.observaciones LIKE '%Modificación de compra%' THEN
                                CONCAT('AJUSTE - Modificación de compra #', 
                                       SUBSTRING_INDEX(SUBSTRING_INDEX(mi.observaciones, 'compra #', -1), ' ', 1))
                            WHEN mi.observaciones LIKE '%Modificación de venta%' THEN
                                CONCAT('AJUSTE - Modificación de venta #', 
                                       SUBSTRING_INDEX(SUBSTRING_INDEX(mi.observaciones, 'venta #', -1), ' ', 1))
                            ELSE 
                                CONCAT('AJUSTE - ', mi.observaciones)
                        END
                    WHEN mi.subtipo_movimiento = 'PERDIDA' THEN
                        CONCAT('PERDIDA - ', COALESCE(mi.observaciones, 'Sin observaciones'))
                    WHEN mi.subtipo_movimiento = 'DEVOLUCION' THEN
                        CONCAT('DEVOLUCION - ', COALESCE(mi.observaciones, 'Sin observaciones'))
                    WHEN mi.subtipo_movimiento = 'COMPRA' THEN
                        CASE 
                            WHEN LENGTH(mi.observaciones) > 80 THEN
                                CONCAT(SUBSTRING(mi.observaciones, 1, 77), '...')
                            ELSE
                                mi.observaciones
                        END
                    ELSE 
                        COALESCE(mi.observaciones, 'Sin observaciones')
                  END as observaciones_completas
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

    // Método para obtener todos los movimientos sin paginación
    public function obtenerTodosLosMovimientos($filtros = [])
    {
        // Base query for data
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
                if ($estado === 'inactivo' || $estado === '2') {
                    $estadosConditions[] = "mi.id_estatus = 2";
                } elseif ($estado === 'ajustado') {
                    $estadosConditions[] = "EXISTS (SELECT 1 FROM movimientos_inventario WHERE id_movimiento_original = mi.id)";
                } elseif ($estado === 'activo' || $estado === '1') {
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

        // Get total records
        $queryTotal = "SELECT COUNT(*) as total " . $baseQuery . $whereClause;
        $stmtTotal = $this->db->prepare($queryTotal);
        foreach ($params as $i => $param) {
            $stmtTotal->bindValue($i + 1, $param);
        }
        $stmtTotal->execute();
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

        // Get all records without pagination
        $query = "SELECT mi.*, 
                  p.descripcion as producto,
                  CONCAT(u.nombres, ' ', u.apellidos) as usuario,
                  e.nombre as estado,
                  mi.numero_transaccion,
                  (SELECT COUNT(*) FROM movimientos_inventario WHERE id_movimiento_original = mi.id) as tiene_ajuste,
                  CASE 
                    WHEN mi.tipo_referencia = 'VENTA' THEN CONCAT('Venta #', mi.id_referencia)
                    WHEN mi.subtipo_movimiento = 'COMPRA' THEN CONCAT('Compra #', mi.numero_transaccion)
                    ELSE mi.observaciones
                  END as referencia,
                  -- Formatear observaciones según el tipo de movimiento
                  CASE 
                    WHEN mi.subtipo_movimiento = 'AJUSTE' THEN
                        CASE 
                            WHEN mi.observaciones LIKE '%Modificación de compra%' THEN
                                CONCAT('AJUSTE - Modificación de compra #', 
                                       SUBSTRING_INDEX(SUBSTRING_INDEX(mi.observaciones, 'compra #', -1), ' ', 1))
                            WHEN mi.observaciones LIKE '%Modificación de venta%' THEN
                                CONCAT('AJUSTE - Modificación de venta #', 
                                       SUBSTRING_INDEX(SUBSTRING_INDEX(mi.observaciones, 'venta #', -1), ' ', 1))
                            ELSE 
                                CONCAT('AJUSTE - ', mi.observaciones)
                        END
                    WHEN mi.subtipo_movimiento = 'PERDIDA' THEN
                        CONCAT('PERDIDA - ', COALESCE(mi.observaciones, 'Sin observaciones'))
                    WHEN mi.subtipo_movimiento = 'DEVOLUCION' THEN
                        CONCAT('DEVOLUCION - ', COALESCE(mi.observaciones, 'Sin observaciones'))
                    WHEN mi.subtipo_movimiento = 'COMPRA' THEN
                        CASE 
                            WHEN LENGTH(mi.observaciones) > 80 THEN
                                CONCAT(SUBSTRING(mi.observaciones, 1, 77), '...')
                            ELSE
                                mi.observaciones
                        END
                    ELSE 
                        COALESCE(mi.observaciones, 'Sin observaciones')
                  END as observaciones_completas
                  " . $baseQuery . $whereClause . "
                  ORDER BY mi.fecha_movimiento DESC";
        
        $stmt = $this->db->prepare($query);
        foreach ($params as $i => $param) {
            $stmt->bindValue($i + 1, $param);
        }
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total
        ];
    }

    // Método de compatibilidad para obtenerMovimientos
    public function obtenerMovimientos($filtros = [], $pagina = 1, $porPagina = 10)
    {
        // Convertir filtros al formato esperado por obtenerTodosMovimientos
        $filtrosConvertidos = [];
        
        if (isset($filtros['tipos'])) {
            $filtrosConvertidos['tipos'] = $filtros['tipos'];
        }
        
        if (isset($filtros['subtipos'])) {
            // Los subtipos se manejan filtrando después
            $filtrosConvertidos['subtipos'] = $filtros['subtipos'];
        }
        
        if (isset($filtros['estados'])) {
            // Convertir estados numéricos a nombres
            $estadosConvertidos = [];
            foreach ($filtros['estados'] as $estado) {
                if ($estado == 1 || $estado === 'activo') {
                    $estadosConvertidos[] = 'activo';
                } elseif ($estado == 2 || $estado === 'inactivo') {
                    $estadosConvertidos[] = 'inactivo';
                }
            }
            $filtrosConvertidos['estados'] = $estadosConvertidos;
        }
        
        if (isset($filtros['busqueda'])) {
            $filtrosConvertidos['busqueda'] = $filtros['busqueda'];
        }
        
        if (isset($filtros['fecha_inicio'])) {
            $filtrosConvertidos['fecha_inicio'] = $filtros['fecha_inicio'];
        }
        
        if (isset($filtros['fecha_fin'])) {
            $filtrosConvertidos['fecha_fin'] = $filtros['fecha_fin'];
        }
        
        $resultado = $this->obtenerTodosMovimientos($pagina, $porPagina, $filtrosConvertidos);
        
        // Si se especificaron subtipos, filtrar los resultados
        if (isset($filtros['subtipos'])) {
            $resultado['data'] = array_filter($resultado['data'], function($movimiento) use ($filtros) {
                return in_array($movimiento['subtipo_movimiento'], $filtros['subtipos']);
            });
            // Reindexar el array
            $resultado['data'] = array_values($resultado['data']);
        }
        
        return $resultado['data']; // Devolver solo los datos para compatibilidad
    }

    public function obtenerMovimientoPorId($id)
    {
        $query = "SELECT mi.*, p.descripcion as producto, e.nombre as estado,
                  CONCAT(u.nombres, ' ', u.apellidos) as usuario,
                  mi.numero_transaccion,
                  -- Obtener información del movimiento original si es un ajuste
                  CASE
                    WHEN mi.tipo_movimiento = 'AJUSTE' THEN mo.tipo_movimiento
                    ELSE mi.tipo_movimiento
                  END as tipo_movimiento_original,
                  CASE
                    WHEN mi.tipo_movimiento = 'AJUSTE' THEN mo.cantidad
                    ELSE mi.cantidad
                  END as cantidad_original,
                  CASE
                    WHEN mi.tipo_movimiento = 'AJUSTE' THEN mo.precio_unitario
                    ELSE mi.precio_unitario
                  END as precio_unitario_original,
                  CASE
                    WHEN mi.tipo_movimiento = 'AJUSTE' THEN mo.fecha_movimiento
                    ELSE mi.fecha_movimiento
                  END as fecha_movimiento_original,
                  CASE
                    WHEN mi.tipo_movimiento = 'AJUSTE' THEN mo.observaciones
                    ELSE mi.observaciones
                  END as observaciones_original,
                  -- Obtener el ID del movimiento ajustado más reciente si existe
                  (SELECT id FROM movimientos_inventario WHERE id_movimiento_original = mi.id ORDER BY fecha_movimiento DESC, id DESC LIMIT 1) as id_movimiento_ajustado,
                  -- Obtener información de pagos del movimiento original
                  GROUP_CONCAT(DISTINCT
                    CASE 
                        WHEN (mo.tipo_referencia = 'VENTA' AND mi.tipo_movimiento = 'AJUSTE') OR (mi.tipo_movimiento != 'AJUSTE' AND mi.tipo_referencia = 'VENTA') THEN
                            CONCAT(pvo.forma_pago, ':', pvo.monto, ':', IFNULL(pvo.referencia_pago, 'NULL'))
                    END
                  ) as pagos_original_info,
                  -- Obtener información de la venta relacionada si existe
                  v.monto_total as monto_venta,
                  CONCAT(c.nombres, ' ', c.apellidos) as cliente_venta,
                  -- Obtener información de pagos del movimiento actual
                  GROUP_CONCAT(DISTINCT
                    CASE 
                        WHEN mi.tipo_referencia = 'VENTA' THEN
                            CONCAT(pv.forma_pago, ':', pv.monto, ':', IFNULL(pv.referencia_pago, 'NULL'))
                    END
                  ) as pagos_info
                  FROM movimientos_inventario mi
                  JOIN producto p ON mi.id_producto = p.id
                  JOIN estatus e ON mi.id_estatus = e.id
                  JOIN usuarios u ON mi.id_usuario = u.id
                  LEFT JOIN movimientos_inventario mo ON mi.id_movimiento_original = mo.id
                  LEFT JOIN ventas v ON (mi.tipo_referencia = 'VENTA' AND mi.id_referencia = v.id)
                  LEFT JOIN clientes c ON v.cedula_cliente = c.cedula
                  LEFT JOIN pagos_venta pv ON (mi.tipo_referencia = 'VENTA' AND mi.id_referencia = pv.id_venta)
                  LEFT JOIN pagos_venta pvo ON (mo.id IS NOT NULL AND mo.tipo_referencia = 'VENTA' AND mo.id_referencia = pvo.id_venta)
                  WHERE mi.id = :id
                  GROUP BY mi.id, mo.id, p.descripcion, e.nombre, u.nombres, u.apellidos, 
                           mo.tipo_movimiento, mo.cantidad, mo.precio_unitario, 
                           mo.fecha_movimiento, mo.observaciones, v.monto_total, 
                           c.nombres, c.apellidos";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $movimiento = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($movimiento) {
            // Extraer información del proveedor si es una compra
            if ($movimiento['subtipo_movimiento'] === 'COMPRA') {
                $proveedor = 'No especificado';
                $cedula_proveedor = '';
                
                // Las observaciones tienen formato: "COMPRA - Proveedor: nombre_proveedor - descripción"
                if (!empty($movimiento['observaciones'])) {
                    $pattern = '/COMPRA - Proveedor: ([^-]+)/';
                    if (preg_match($pattern, $movimiento['observaciones'], $matches)) {
                        $proveedor = trim($matches[1]);
                        
                        // Buscar la cédula del proveedor por nombre
                        $query_prov = "SELECT cedula FROM proveedores WHERE nombre = :nombre AND id_estatus = 1";
                        $stmt_prov = $this->db->prepare($query_prov);
                        $stmt_prov->bindParam(":nombre", $proveedor);
                        $stmt_prov->execute();
                        $result_prov = $stmt_prov->fetch(PDO::FETCH_ASSOC);
                        
                        if ($result_prov) {
                            $cedula_proveedor = $result_prov['cedula'];
                        }
                    }
                }
                
                $movimiento['proveedor'] = $proveedor;
                $movimiento['cedula_proveedor'] = $cedula_proveedor;
            }
            
            // Procesar pagos del movimiento actual y original, marcando los cambios
            $pagos = [];
            $pagos_original = [];
            
            // Primero procesamos los pagos originales
            if ($movimiento['pagos_original_info']) {
                foreach (explode(',', $movimiento['pagos_original_info']) as $pago_info) {
                    list($forma_pago, $monto, $referencia) = explode(':', $pago_info);
                    $pagos_original[] = [
                        'forma_pago' => $forma_pago,
                        'monto' => $monto,
                        'referencia_pago' => $referencia === 'NULL' ? null : $referencia,
                        'cambio' => false
                    ];
                }
            }
            
            // Luego procesamos los pagos actuales y comparamos
            if ($movimiento['pagos_info']) {
                foreach (explode(',', $movimiento['pagos_info']) as $index => $pago_info) {
                    list($forma_pago, $monto, $referencia) = explode(':', $pago_info);
                    $pago_actual = [
                        'forma_pago' => $forma_pago,
                        'monto' => $monto,
                        'referencia_pago' => $referencia === 'NULL' ? null : $referencia,
                        'cambio' => false
                    ];
                    
                    // Verificar si hay un pago original correspondiente
                    if (isset($pagos_original[$index])) {
                        $pago_original = $pagos_original[$index];
                        if ($pago_actual['forma_pago'] !== $pago_original['forma_pago'] ||
                            $pago_actual['monto'] !== $pago_original['monto'] ||
                            $pago_actual['referencia_pago'] !== $pago_original['referencia_pago']) {
                            $pago_actual['cambio'] = true;
                            $pagos_original[$index]['cambio'] = true;
                        }
                    } else {
                        // Es un nuevo pago
                        $pago_actual['cambio'] = true;
                    }
                    
                    $pagos[] = $pago_actual;
                }
                
                // Marcar pagos originales eliminados
                if (count($pagos_original) > count($pagos)) {
                    for ($i = count($pagos); $i < count($pagos_original); $i++) {
                        $pagos_original[$i]['cambio'] = true;
                    }
                }
            }
            
            $movimiento['pagos'] = $pagos;
            $movimiento['pagos_original'] = $pagos_original;
            unset($movimiento['pagos_info']);
            unset($movimiento['pagos_original_info']);
        }

        return $movimiento;
    }

    public function registrarMovimiento($data)
    {
        $query = "INSERT INTO movimientos_inventario (
            id_producto, 
            tipo_movimiento, 
            subtipo_movimiento,
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
            :subtipo_movimiento,
            :cantidad, 
            :precio_unitario, 
            :id_referencia, 
            :tipo_referencia, 
            :id_usuario, 
            :observaciones,
            1
        )";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id_producto", $data['id_producto']);
        $stmt->bindParam(":tipo_movimiento", $data['tipo_movimiento']);
        $stmt->bindParam(":subtipo_movimiento", $data['subtipo_movimiento']);
        $stmt->bindParam(":cantidad", $data['cantidad']);
        $stmt->bindParam(":precio_unitario", $data['precio_unitario']);
        $stmt->bindParam(":id_referencia", $data['id_referencia']);
        $stmt->bindParam(":tipo_referencia", $data['tipo_referencia']);
        $stmt->bindParam(":id_usuario", $data['id_usuario']);
        $stmt->bindParam(":observaciones", $data['observaciones']);
        
        $result = $stmt->execute();
        
        if ($result) {
            // Actualizar stock del producto
            if ($data['tipo_movimiento'] === 'ENTRADA') {
                $query = "UPDATE producto SET cantidad = cantidad + :cantidad WHERE id = :id_producto";
            } else {
                $query = "UPDATE producto SET cantidad = cantidad - :cantidad WHERE id = :id_producto";
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":cantidad", $data['cantidad']);
            $stmt->bindParam(":id_producto", $data['id_producto']);
            $stmt->execute();
            
            return true;
        }
        
        return false;
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

    public function obtenerProductosDeCompra($id_movimiento)
    {
        $query = "SELECT mi.*, p.descripcion as producto, p.precio as precio_actual
                  FROM movimientos_inventario mi
                  JOIN producto p ON mi.id_producto = p.id
                  WHERE mi.numero_transaccion = (
                      SELECT numero_transaccion 
                      FROM movimientos_inventario 
                      WHERE id = :id_movimiento
                  )
                  AND mi.subtipo_movimiento = 'COMPRA'
                  AND mi.id_estatus = 1
                  ORDER BY mi.id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id_movimiento", $id_movimiento);
        $stmt->execute();
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Extraer información del proveedor de las observaciones
        foreach ($productos as &$producto) {
            $proveedor = 'No especificado';
            $cedula_proveedor = '';
            
            // Las observaciones tienen formato: "COMPRA - Proveedor: nombre_proveedor - descripción"
            if (!empty($producto['observaciones'])) {
                $pattern = '/COMPRA - Proveedor: ([^-]+)/';
                if (preg_match($pattern, $producto['observaciones'], $matches)) {
                    $proveedor = trim($matches[1]);
                    
                    // Buscar la cédula del proveedor por nombre
                    $query_prov = "SELECT cedula FROM proveedores WHERE nombre = :nombre AND id_estatus = 1";
                    $stmt_prov = $this->db->prepare($query_prov);
                    $stmt_prov->bindParam(":nombre", $proveedor);
                    $stmt_prov->execute();
                    $result_prov = $stmt_prov->fetch(PDO::FETCH_ASSOC);
                    
                    if ($result_prov) {
                        $cedula_proveedor = $result_prov['cedula'];
                    }
                }
            }
            
            $producto['proveedor'] = $proveedor;
            $producto['cedula_proveedor'] = $cedula_proveedor;
        }
        
        return $productos;
    }

    public function obtenerCompraParaModificacion($id_movimiento)
    {
        $query = "SELECT mi.*, p.nombre as proveedor, p.cedula as cedula_proveedor,
                         CONCAT(u.nombres, ' ', u.apellidos) as usuario
                  FROM movimientos_inventario mi
                  LEFT JOIN proveedores p ON mi.observaciones LIKE CONCAT('%Proveedor: ', p.nombre, '%')
                  JOIN usuarios u ON mi.id_usuario = u.id
                  WHERE mi.id = :id AND mi.id_estatus = 1 AND mi.subtipo_movimiento = 'COMPRA'
                  LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id_movimiento);
        $stmt->execute();
        
        $compra = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($compra) {
            // Extraer información adicional de las observaciones
            if (!empty($compra['observaciones'])) {
                // Extraer número de factura si existe
                $pattern = '/Factura: ([^-]+)/';
                if (preg_match($pattern, $compra['observaciones'], $matches)) {
                    $compra['numero_factura'] = trim($matches[1]);
                } else {
                    $compra['numero_factura'] = '';
                }
                
                // Si no se encontró el proveedor por JOIN, extraerlo de observaciones
                if (empty($compra['proveedor'])) {
                    $pattern = '/COMPRA - Proveedor: ([^-]+)/';
                    if (preg_match($pattern, $compra['observaciones'], $matches)) {
                        $compra['proveedor'] = trim($matches[1]);
                        
                        // Buscar la cédula del proveedor
                        $query_prov = "SELECT cedula FROM proveedores WHERE nombre = :nombre AND id_estatus = 1";
                        $stmt_prov = $this->db->prepare($query_prov);
                        $stmt_prov->bindParam(":nombre", $compra['proveedor']);
                        $stmt_prov->execute();
                        $result_prov = $stmt_prov->fetch(PDO::FETCH_ASSOC);
                        
                        if ($result_prov) {
                            $compra['cedula_proveedor'] = $result_prov['cedula'];
                        }
                    }
                }
            }
            
            $compra['fecha_movimiento'] = $compra['fecha_movimiento'];
        }
        
        return $compra;
    }

    public function modificarCompraConAjuste($id_movimiento, $data)
    {
        try {
            error_log("Iniciando modificación de compra #" . $id_movimiento);
            error_log("Datos recibidos: " . print_r($data, true));
            $this->db->beginTransaction();

            // 1. Obtener la compra actual
            $compra_actual = $this->obtenerCompraParaModificacion($id_movimiento);
            if (!$compra_actual) {
                throw new PDOException("Compra no encontrada o no modificable");
            }

            // 2. Obtener el número de transacción de la compra original
            $numero_transaccion_original = $compra_actual['numero_transaccion'];

            // 3. Marcar todos los movimientos de la compra original como inactivos
            $query = "UPDATE movimientos_inventario SET id_estatus = 2 
                      WHERE numero_transaccion = :numero_transaccion AND id_estatus = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":numero_transaccion", $numero_transaccion_original);
            $stmt->execute();

            // 4. Generar nuevo número de transacción
            $query_txn = "SELECT GenerarNumeroTransaccion() as numero_transaccion";
            $stmt_txn = $this->db->prepare($query_txn);
            $stmt_txn->execute();
            $result_txn = $stmt_txn->fetch(PDO::FETCH_ASSOC);
            $nuevo_numero_transaccion = $result_txn['numero_transaccion'];

            // 5. Generar número de transacción para el ajuste
            $query_txn_ajuste = "SELECT GenerarNumeroTransaccion() as numero_transaccion";
            $stmt_txn_ajuste = $this->db->prepare($query_txn_ajuste);
            $stmt_txn_ajuste->execute();
            $result_txn_ajuste = $stmt_txn_ajuste->fetch(PDO::FETCH_ASSOC);
            $numero_transaccion_ajuste = $result_txn_ajuste['numero_transaccion'];

            // 6. Obtener información del proveedor
            $query_proveedor = "SELECT nombre FROM proveedores WHERE cedula = :cedula";
            $stmt_proveedor = $this->db->prepare($query_proveedor);
            $stmt_proveedor->bindParam(":cedula", $data['id_proveedor']);
            $stmt_proveedor->execute();
            $proveedor = $stmt_proveedor->fetch(PDO::FETCH_ASSOC);
            $nombre_proveedor = $proveedor ? $proveedor['nombre'] : 'Proveedor desconocido';

            // 7. Procesar productos y crear nuevos movimientos
            foreach ($data['productos'] as $producto) {
                if (empty($producto['id_producto']) || empty($producto['cantidad']) || empty($producto['precio_compra'])) {
                    continue;
                }

                // Crear observaciones para el nuevo movimiento
                $observaciones_base = "COMPRA - Proveedor: {$nombre_proveedor}";
                if (!empty($data['numero_factura'])) {
                    $observaciones_base .= " - Factura: {$data['numero_factura']}";
                }
                if (!empty($data['observaciones'])) {
                    $observaciones_base .= " - {$data['observaciones']}";
                }
                $observaciones_base .= " - Modificación de compra #{$numero_transaccion_original}";

                // Registrar nuevo movimiento de entrada (compra ajustada)
                $query = "INSERT INTO movimientos_inventario (
                    id_producto, tipo_movimiento, subtipo_movimiento, cantidad, precio_unitario, 
                    numero_transaccion, id_usuario, id_movimiento_original, observaciones, 
                    fecha_movimiento, id_estatus
                ) VALUES (
                    :id_producto, 'ENTRADA', 'COMPRA', :cantidad, :precio_unitario, 
                    :numero_transaccion, :id_usuario, :id_movimiento_original, :observaciones,
                    :fecha_movimiento, 1
                )";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id_producto", $producto['id_producto']);
                $stmt->bindParam(":cantidad", $producto['cantidad']);
                $stmt->bindParam(":precio_unitario", $producto['precio_compra']);
                $stmt->bindParam(":numero_transaccion", $nuevo_numero_transaccion);
                $stmt->bindParam(":id_usuario", $data['id_usuario']);
                $stmt->bindParam(":id_movimiento_original", $id_movimiento);
                $stmt->bindParam(":observaciones", $observaciones_base);
                $stmt->bindParam(":fecha_movimiento", $data['fecha_compra']);
                $stmt->execute();

                // Buscar el movimiento original específico de este producto
                $query_original = "SELECT id, cantidad FROM movimientos_inventario 
                                   WHERE numero_transaccion = :numero_transaccion_original 
                                   AND id_producto = :id_producto 
                                   AND subtipo_movimiento = 'COMPRA'
                                   AND id_estatus = 2";
                $stmt_original = $this->db->prepare($query_original);
                $stmt_original->bindParam(":numero_transaccion_original", $numero_transaccion_original);
                $stmt_original->bindParam(":id_producto", $producto['id_producto']);
                $stmt_original->execute();
                $movimiento_original = $stmt_original->fetch(PDO::FETCH_ASSOC);

                if ($movimiento_original) {
                    // Calcular diferencia de stock
                    $cantidad_original = $movimiento_original['cantidad'];
                    $cantidad_nueva = $producto['cantidad'];
                    $diferencia = $cantidad_nueva - $cantidad_original;

                    // Solo registrar ajuste si hay diferencia
                    if ($diferencia != 0) {
                        $tipo_ajuste = $diferencia > 0 ? 'ENTRADA' : 'SALIDA';
                        $cantidad_ajuste = abs($diferencia);
                        
                        // Buscar la transacción original (no ajuste) para evitar acumulación
                        // Rastrear hacia atrás hasta encontrar la compra base
                        $transaccion_base = $numero_transaccion_original;
                        
                        // Buscar si existe una compra original con este número de transacción
                        $query_original = "SELECT numero_transaccion FROM movimientos_inventario 
                                          WHERE numero_transaccion = :numero_transaccion 
                                          AND subtipo_movimiento = 'COMPRA' 
                                          AND id_estatus IN (1, 2) 
                                          LIMIT 1";
                        $stmt_original = $this->db->prepare($query_original);
                        $stmt_original->bindParam(":numero_transaccion", $numero_transaccion_original);
                        $stmt_original->execute();
                        $compra_original = $stmt_original->fetch(PDO::FETCH_ASSOC);
                        
                        if ($compra_original) {
                            $transaccion_base = $compra_original['numero_transaccion'];
                        } else {
                            // Si no encontramos la compra original, buscar en las observaciones de ajustes previos
                            $query_ajuste = "SELECT observaciones FROM movimientos_inventario 
                                           WHERE numero_transaccion = :numero_transaccion 
                                           AND subtipo_movimiento = 'AJUSTE' 
                                           AND observaciones LIKE 'AJUSTE - Modificación de compra #%' 
                                           ORDER BY id DESC LIMIT 1";
                            $stmt_ajuste = $this->db->prepare($query_ajuste);
                            $stmt_ajuste->bindParam(":numero_transaccion", $numero_transaccion_original);
                            $stmt_ajuste->execute();
                            $ajuste_previo = $stmt_ajuste->fetch(PDO::FETCH_ASSOC);
                            
                            if ($ajuste_previo && preg_match('/AJUSTE - Modificación de compra #(.+)/', $ajuste_previo['observaciones'], $matches)) {
                                $transaccion_base = $matches[1];
                            }
                        }
                        
                        $observaciones_ajuste = "AJUSTE - Modificación de compra #{$transaccion_base}";

                        // Registrar movimiento de ajuste
                        $query_ajuste = "INSERT INTO movimientos_inventario (
                            id_producto, tipo_movimiento, subtipo_movimiento, cantidad, precio_unitario, 
                            numero_transaccion, id_usuario, id_movimiento_original, observaciones, 
                            fecha_movimiento, id_estatus
                        ) VALUES (
                            :id_producto, :tipo_movimiento, 'AJUSTE', :cantidad, :precio_unitario, 
                            :numero_transaccion, :id_usuario, :id_movimiento_original, :observaciones,
                            NOW(), 1
                        )";
                        
                        $stmt_ajuste = $this->db->prepare($query_ajuste);
                        $stmt_ajuste->bindParam(":id_producto", $producto['id_producto']);
                        $stmt_ajuste->bindParam(":tipo_movimiento", $tipo_ajuste);
                        $stmt_ajuste->bindParam(":cantidad", $cantidad_ajuste);
                        $stmt_ajuste->bindParam(":precio_unitario", $producto['precio_compra']);
                        $stmt_ajuste->bindParam(":numero_transaccion", $numero_transaccion_ajuste);
                        $stmt_ajuste->bindParam(":id_usuario", $data['id_usuario']);
                        $stmt_ajuste->bindParam(":id_movimiento_original", $movimiento_original['id']);
                        $stmt_ajuste->bindParam(":observaciones", $observaciones_ajuste);
                        $stmt_ajuste->execute();
                    }

                    // Actualizar stock del producto
                    $query_stock = "UPDATE producto SET cantidad = cantidad + :diferencia WHERE id = :id_producto";
                    $stmt_stock = $this->db->prepare($query_stock);
                    $stmt_stock->bindParam(":diferencia", $diferencia);
                    $stmt_stock->bindParam(":id_producto", $producto['id_producto']);
                    $stmt_stock->execute();

                    // Actualizar precio de venta si se proporcionó
                    if (!empty($producto['precio_venta'])) {
                        $query_precio = "UPDATE producto SET precio = :precio WHERE id = :id_producto";
                        $stmt_precio = $this->db->prepare($query_precio);
                        $stmt_precio->bindParam(":precio", $producto['precio_venta']);
                        $stmt_precio->bindParam(":id_producto", $producto['id_producto']);
                        $stmt_precio->execute();
                    }
                }
            }

            $this->db->commit();
            error_log("Modificación de compra completada exitosamente");
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error en modificarCompraConAjuste: " . $e->getMessage());
            $this->errors[] = "Error al modificar la compra: " . $e->getMessage();
            return false;
        }
    }

    public function obtenerProductosDeCompraPorTransaccion($numero_transaccion)
    {
        $query = "SELECT mi.*, p.descripcion as producto, p.precio as precio_actual
                  FROM movimientos_inventario mi
                  JOIN producto p ON mi.id_producto = p.id
                  WHERE mi.numero_transaccion = :numero_transaccion
                  AND mi.subtipo_movimiento = 'COMPRA'
                  AND mi.id_estatus = 1
                  ORDER BY mi.id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":numero_transaccion", $numero_transaccion);
        $stmt->execute();
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Extraer información del proveedor de las observaciones
        foreach ($productos as &$producto) {
            $proveedor = 'No especificado';
            $cedula_proveedor = '';
            
            // Las observaciones tienen formato: "COMPRA - Proveedor: nombre_proveedor - descripción"
            if (!empty($producto['observaciones'])) {
                $pattern = '/COMPRA - Proveedor: ([^-]+)/';
                if (preg_match($pattern, $producto['observaciones'], $matches)) {
                    $proveedor = trim($matches[1]);
                    
                    // Buscar la cédula del proveedor por nombre
                    $query_prov = "SELECT cedula FROM proveedores WHERE nombre = :nombre AND id_estatus = 1";
                    $stmt_prov = $this->db->prepare($query_prov);
                    $stmt_prov->bindParam(":nombre", $proveedor);
                    $stmt_prov->execute();
                    $result_prov = $stmt_prov->fetch(PDO::FETCH_ASSOC);
                    
                    if ($result_prov) {
                        $cedula_proveedor = $result_prov['cedula'];
                    }
                }
            }
            
            $producto['proveedor'] = $proveedor;
            $producto['cedula_proveedor'] = $cedula_proveedor;
        }
        
        return $productos;
    }

    public function obtenerResumenGeneral($filtros = [])
    {
        $where = [];
        $params = [];

        if (!empty($filtros['fecha_inicio'])) {
            $where[] = "mi.fecha_movimiento >= :fecha_inicio";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
        }

        if (!empty($filtros['fecha_fin'])) {
            $where[] = "mi.fecha_movimiento <= :fecha_fin";
            $params[':fecha_fin'] = $filtros['fecha_fin'];
        }

        if (!empty($filtros['categoria'])) {
            $where[] = "p.id_categoria = :categoria";
            $params[':categoria'] = $filtros['categoria'];
        }

        $whereClause = !empty($where) ? " WHERE " . implode(" AND ", $where) : "";

        // Obtener totales
        $query = "SELECT 
                    COUNT(DISTINCT p.id) as total_productos,
                    SUM(CASE WHEN mi.tipo_movimiento = 'ENTRADA' AND mi.id_estatus = 1 THEN mi.cantidad ELSE 0 END) as total_entradas,
                    SUM(CASE WHEN mi.tipo_movimiento = 'SALIDA' AND mi.id_estatus = 1 THEN mi.cantidad ELSE 0 END) as total_salidas,
                    SUM(p.cantidad * p.precio) as valor_inventario
                  FROM producto p
                  LEFT JOIN movimientos_inventario mi ON p.id = mi.id_producto
                  LEFT JOIN categorias c ON p.id_categoria = c.id
                  " . $whereClause;

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerTodosLosProductosResumen($filtros = [])
    {
        $where = [];
        $params = [];

        if (!empty($filtros['fecha_inicio'])) {
            $where[] = "mi.fecha_movimiento >= :fecha_inicio";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
        }

        if (!empty($filtros['fecha_fin'])) {
            $where[] = "mi.fecha_movimiento <= :fecha_fin";
            $params[':fecha_fin'] = $filtros['fecha_fin'];
        }

        if (!empty($filtros['categoria'])) {
            $where[] = "p.id_categoria = :categoria";
            $params[':categoria'] = $filtros['categoria'];
        }

        $whereClause = !empty($where) ? " WHERE " . implode(" AND ", $where) : "";

        // Obtener todos los productos sin paginación
        $query = "SELECT 
                    p.id,
                    p.descripcion,
                    c.nombre as categoria,
                    p.cantidad as stock_actual,
                    COALESCE(SUM(CASE WHEN mi.tipo_movimiento = 'ENTRADA' AND mi.id_estatus = 1 THEN mi.cantidad ELSE 0 END), 0) as entradas,
                    COALESCE(SUM(CASE WHEN mi.tipo_movimiento = 'SALIDA' AND mi.id_estatus = 1 THEN mi.cantidad ELSE 0 END), 0) as salidas,
                    (p.cantidad * p.precio) as valor_total
                  FROM producto p
                  LEFT JOIN movimientos_inventario mi ON p.id = mi.id_producto
                  LEFT JOIN categorias c ON p.id_categoria = c.id
                  " . $whereClause . "
                  GROUP BY p.id, p.descripcion, c.nombre, p.cantidad, p.precio
                  ORDER BY p.descripcion ASC";

        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerResumenProductos($filtros = [], $pagina = 1, $por_pagina = 10)
    {
        $offset = ($pagina - 1) * $por_pagina;
        $where = [];
        $params = [];

        if (!empty($filtros['fecha_inicio'])) {
            $where[] = "mi.fecha_movimiento >= :fecha_inicio";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
        }

        if (!empty($filtros['fecha_fin'])) {
            $where[] = "mi.fecha_movimiento <= :fecha_fin";
            $params[':fecha_fin'] = $filtros['fecha_fin'];
        }

        if (!empty($filtros['categoria'])) {
            $where[] = "p.id_categoria = :categoria";
            $params[':categoria'] = $filtros['categoria'];
        }

        $whereClause = !empty($where) ? " WHERE " . implode(" AND ", $where) : "";

        // Obtener total de registros
        $queryTotal = "SELECT COUNT(DISTINCT p.id) as total
                      FROM producto p
                      LEFT JOIN movimientos_inventario mi ON p.id = mi.id_producto
                      LEFT JOIN categorias c ON p.id_categoria = c.id
                      " . $whereClause;

        $stmtTotal = $this->db->prepare($queryTotal);
        $stmtTotal->execute($params);
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

        // Obtener productos paginados
        $query = "SELECT 
                    p.id,
                    p.descripcion,
                    c.nombre as categoria,
                    p.cantidad as stock_actual,
                    COALESCE(SUM(CASE WHEN mi.tipo_movimiento = 'ENTRADA' AND mi.id_estatus = 1 THEN mi.cantidad ELSE 0 END), 0) as entradas,
                    COALESCE(SUM(CASE WHEN mi.tipo_movimiento = 'SALIDA' AND mi.id_estatus = 1 THEN mi.cantidad ELSE 0 END), 0) as salidas,
                    (p.cantidad * p.precio) as valor_total
                  FROM producto p
                  LEFT JOIN movimientos_inventario mi ON p.id = mi.id_producto
                  LEFT JOIN categorias c ON p.id_categoria = c.id
                  " . $whereClause . "
                  GROUP BY p.id, p.descripcion, c.nombre, p.cantidad, p.precio
                  ORDER BY p.descripcion ASC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'productos' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'pagina_actual' => $pagina,
            'total_paginas' => ceil($total / $por_pagina)
        ];
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
        $query = "SELECT dv.*, p.descripcion as producto, p.precio as precio_actual, p.cantidad as stock_actual
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

        // 2. Generar número de transacción para el ajuste
        $query_txn_ajuste = "SELECT GenerarNumeroTransaccion() as numero_transaccion";
        $stmt_txn_ajuste = $this->db->prepare($query_txn_ajuste);
        $stmt_txn_ajuste->execute();
        $result_txn_ajuste = $stmt_txn_ajuste->fetch(PDO::FETCH_ASSOC);
        $numero_transaccion_ajuste = $result_txn_ajuste['numero_transaccion'];

        // 3. Mark current sale as inactive
        $query = "UPDATE ventas SET id_estatus = 2 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id_venta);
        $stmt->execute();

        // 4. Create new sale as adjustment
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
            $query = "SELECT id, cantidad FROM movimientos_inventario 
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

                // Calcular diferencia y registrar ajuste si es necesario
                $cantidad_original = $movimiento_original['cantidad'];
                $cantidad_nueva = $producto['cantidad'];
                $diferencia = $this->calcularDiferenciaStock($id_venta, $producto['id_producto'], $cantidad_nueva);

                // Solo registrar ajuste si hay diferencia
                if ($diferencia != 0) {
                    $tipo_ajuste = $diferencia > 0 ? 'ENTRADA' : 'SALIDA';
                    $cantidad_ajuste = abs($diferencia);
                    
                    // Buscar la venta original (no ajuste) para evitar acumulación
                    // Rastrear hacia atrás hasta encontrar la venta base
                    $venta_base = $id_venta;
                    
                    // Primero intentar encontrar la venta original activa
                    $query_original = "SELECT id FROM ventas 
                                      WHERE id = :id_venta 
                                      AND id_estatus = 1 
                                      LIMIT 1";
                    $stmt_original = $this->db->prepare($query_original);
                    $stmt_original->bindParam(":id_venta", $id_venta);
                    $stmt_original->execute();
                    $venta_original = $stmt_original->fetch(PDO::FETCH_ASSOC);
                    
                    if ($venta_original) {
                        $venta_base = $venta_original['id'];
                    } else {
                        // Si la venta actual está inactiva, buscar en ajustes previos para encontrar la venta base
                        $query_ajuste = "SELECT observaciones FROM movimientos_inventario 
                                       WHERE id_referencia = :id_venta 
                                       AND tipo_referencia = 'VENTA' 
                                       AND subtipo_movimiento = 'AJUSTE' 
                                       AND observaciones LIKE 'AJUSTE - Modificación de venta #%' 
                                       ORDER BY id DESC LIMIT 1";
                        $stmt_ajuste = $this->db->prepare($query_ajuste);
                        $stmt_ajuste->bindParam(":id_venta", $id_venta);
                        $stmt_ajuste->execute();
                        $ajuste_previo = $stmt_ajuste->fetch(PDO::FETCH_ASSOC);
                        
                        if ($ajuste_previo && preg_match('/AJUSTE - Modificación de venta #(\d+)/', $ajuste_previo['observaciones'], $matches)) {
                            $venta_base = $matches[1];
                        }
                    }
                    
                    $observaciones_ajuste = "AJUSTE - Modificación de venta #{$venta_base}";

                    // Registrar movimiento de ajuste
                    $query_ajuste = "INSERT INTO movimientos_inventario (
                        id_producto, tipo_movimiento, subtipo_movimiento, cantidad, precio_unitario, 
                        numero_transaccion, id_usuario, id_movimiento_original, observaciones, 
                        fecha_movimiento, id_estatus
                    ) VALUES (
                        :id_producto, :tipo_movimiento, 'AJUSTE', :cantidad, :precio_unitario, 
                        :numero_transaccion, :id_usuario, :id_movimiento_original, :observaciones,
                        NOW(), 1
                    )";
                    
                    $stmt_ajuste = $this->db->prepare($query_ajuste);
                    $stmt_ajuste->bindParam(":id_producto", $producto['id_producto']);
                    $stmt_ajuste->bindParam(":tipo_movimiento", $tipo_ajuste);
                    $stmt_ajuste->bindParam(":cantidad", $cantidad_ajuste);
                    $stmt_ajuste->bindParam(":precio_unitario", $producto['precio_unitario']);
                    $stmt_ajuste->bindParam(":numero_transaccion", $numero_transaccion_ajuste);
                    $stmt_ajuste->bindParam(":id_usuario", $data['id_usuario']);
                    $stmt_ajuste->bindParam(":id_movimiento_original", $movimiento_original['id']);
                    $stmt_ajuste->bindParam(":observaciones", $observaciones_ajuste);
                    $stmt_ajuste->execute();
                }

                // Adjust stock
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

    // Nuevos métodos para el sistema de movimientos mejorado
    public function buscarMovimientoPorTransaccion($numero_transaccion)
    {
        $query = "SELECT mi.*, 
                  p.descripcion as producto,
                  CONCAT(u.nombres, ' ', u.apellidos) as usuario,
                  e.nombre as estado
                  FROM movimientos_inventario mi
                  JOIN producto p ON mi.id_producto = p.id
                  JOIN usuarios u ON mi.id_usuario = u.id
                  JOIN estatus e ON mi.id_estatus = e.id
                  WHERE mi.numero_transaccion = :numero_transaccion
                  AND mi.id_estatus = 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":numero_transaccion", $numero_transaccion);
        $stmt->execute();
        
        $movimiento = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Si es una compra, extraer información del proveedor de las observaciones
        if ($movimiento && $movimiento['subtipo_movimiento'] === 'COMPRA') {
            $proveedor = 'No especificado';
            $cedula_proveedor = '';
            
            // Las observaciones tienen formato: "COMPRA - Proveedor: nombre_proveedor - descripción"
            if (!empty($movimiento['observaciones'])) {
                $pattern = '/COMPRA - Proveedor: ([^-]+)/';
                if (preg_match($pattern, $movimiento['observaciones'], $matches)) {
                    $proveedor = trim($matches[1]);
                    
                    // Buscar la cédula del proveedor por nombre
                    $query_prov = "SELECT cedula FROM proveedores WHERE nombre = :nombre AND id_estatus = 1";
                    $stmt_prov = $this->db->prepare($query_prov);
                    $stmt_prov->bindParam(":nombre", $proveedor);
                    $stmt_prov->execute();
                    $result_prov = $stmt_prov->fetch(PDO::FETCH_ASSOC);
                    
                    if ($result_prov) {
                        $cedula_proveedor = $result_prov['cedula'];
                    }
                }
            }
            
            $movimiento['proveedor'] = $proveedor;
            $movimiento['cedula_proveedor'] = $cedula_proveedor;
        }
        
        return $movimiento;
    }

    public function registrarAjuste($id_movimiento_original, $data)
    {
        try {
            $this->db->beginTransaction();

            // 1. Marcar el movimiento original como inactivo
            $query = "UPDATE movimientos_inventario SET id_estatus = 2 WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id_movimiento_original);
            $stmt->execute();

            // 2. Obtener datos del movimiento original para calcular diferencias
            $movimiento_original = $this->obtenerMovimientoPorId($id_movimiento_original);
            if (!$movimiento_original) {
                throw new PDOException("Movimiento original no encontrado");
            }

            // 3. Registrar el nuevo movimiento como ajuste
            // Limpiar las observaciones para evitar duplicación
            $observaciones_limpias = $data['observaciones'];
            
            // Si las observaciones ya contienen "AJUSTE -", usar solo la parte después
            if (strpos($observaciones_limpias, 'AJUSTE - ') === 0) {
                $observaciones_limpias = substr($observaciones_limpias, 9); // Remover "AJUSTE - "
            }
            
            // Construir observaciones del ajuste
            $observaciones_ajuste = "AJUSTE de " . $data['numero_transaccion_original'];
            if (!empty($observaciones_limpias)) {
                $observaciones_ajuste .= " - " . $observaciones_limpias;
            }
            
            $query = "INSERT INTO movimientos_inventario (
                id_producto, tipo_movimiento, subtipo_movimiento, cantidad, 
                precio_unitario, id_referencia, tipo_referencia, fecha_movimiento,
                observaciones, id_usuario, id_movimiento_original, id_estatus
            ) VALUES (
                :id_producto, :tipo_movimiento, 'AJUSTE', :cantidad,
                :precio_unitario, :id_referencia, :tipo_referencia, :fecha_movimiento,
                :observaciones, :id_usuario, :id_movimiento_original, 1
            )";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id_producto", $data['id_producto']);
            $stmt->bindParam(":tipo_movimiento", $data['tipo_movimiento']);
            $stmt->bindParam(":cantidad", $data['cantidad']);
            $stmt->bindParam(":precio_unitario", $data['precio_unitario']);
            $stmt->bindParam(":id_referencia", $data['id_referencia']);
            $stmt->bindParam(":tipo_referencia", $data['tipo_referencia']);
            $stmt->bindParam(":fecha_movimiento", $data['fecha_movimiento']);
            $stmt->bindParam(":observaciones", $observaciones_ajuste);
            $stmt->bindParam(":id_usuario", $data['id_usuario']);
            $stmt->bindParam(":id_movimiento_original", $id_movimiento_original);
            $stmt->execute();

            // 4. Calcular y aplicar diferencia en el stock
            $cantidad_original = $movimiento_original['cantidad'];
            $nueva_cantidad = $data['cantidad'];
            $tipo_movimiento = $data['tipo_movimiento'];

            if ($tipo_movimiento === 'ENTRADA') {
                // Para entradas: nueva cantidad - cantidad original
                $diferencia = $nueva_cantidad - $cantidad_original;
            } else {
                // Para salidas: cantidad original - nueva cantidad
                $diferencia = $cantidad_original - $nueva_cantidad;
            }

            if ($diferencia != 0) {
                $query = "UPDATE producto SET cantidad = cantidad + :diferencia WHERE id = :id_producto";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":diferencia", $diferencia);
                $stmt->bindParam(":id_producto", $data['id_producto']);
                $stmt->execute();
            }

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            $this->errors[] = "Error al registrar ajuste: " . $e->getMessage();
            return false;
        }
    }

    public function modificarPerdidaConAjuste($id_perdida, $data)
    {
        try {
            $this->db->beginTransaction();

            // 1. Obtener la pérdida original
            $perdida_original = $this->obtenerMovimientoPorId($id_perdida);
            if (!$perdida_original || $perdida_original['subtipo_movimiento'] !== 'PERDIDA') {
                throw new PDOException("Pérdida no encontrada o no válida");
            }

            if ($perdida_original['id_estatus'] != 1) {
                throw new PDOException("No se puede modificar una pérdida que ya ha sido ajustada");
            }

            // 2. Marcar la pérdida original como inactiva
            $query = "UPDATE movimientos_inventario SET id_estatus = 2 WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id_perdida);
            $stmt->execute();

            // 3. Generar número de transacción para el ajuste
            $query_txn = "SELECT GenerarNumeroTransaccion() as numero_transaccion";
            $stmt_txn = $this->db->prepare($query_txn);
            $stmt_txn->execute();
            $result_txn = $stmt_txn->fetch(PDO::FETCH_ASSOC);
            $numero_transaccion_ajuste = $result_txn['numero_transaccion'];

            // 4. Crear nueva pérdida como ajuste
            $observaciones_ajuste = "AJUSTE - Modificación de pérdida #{$perdida_original['numero_transaccion']}";
            if (!empty($data['observaciones'])) {
                $observaciones_ajuste .= " - " . $data['observaciones'];
            }

            $query = "INSERT INTO movimientos_inventario (
                id_producto, tipo_movimiento, subtipo_movimiento, cantidad, precio_unitario,
                numero_transaccion, fecha_movimiento, observaciones, id_usuario, 
                id_movimiento_original, id_estatus
            ) VALUES (
                :id_producto, 'SALIDA', 'PERDIDA', :cantidad, :precio_unitario,
                :numero_transaccion, :fecha_movimiento, :observaciones, :id_usuario,
                :id_movimiento_original, 1
            )";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id_producto", $data['id_producto']);
            $stmt->bindParam(":cantidad", $data['cantidad']);
            $stmt->bindParam(":precio_unitario", $data['precio_unitario']);
            $stmt->bindParam(":numero_transaccion", $numero_transaccion_ajuste);
            $stmt->bindParam(":fecha_movimiento", $data['fecha']);
            $stmt->bindParam(":observaciones", $observaciones_ajuste);
            $stmt->bindParam(":id_usuario", $data['id_usuario']);
            $stmt->bindParam(":id_movimiento_original", $id_perdida);
            $stmt->execute();

            // 5. Calcular diferencia y ajustar stock
            $cantidad_original = $perdida_original['cantidad'];
            $cantidad_nueva = $data['cantidad'];
            
            // Para pérdidas: diferencia = cantidad_original - cantidad_nueva
            // Si la nueva cantidad es menor, devolvemos stock (positivo)
            // Si la nueva cantidad es mayor, quitamos más stock (negativo)
            $diferencia = $cantidad_original - $cantidad_nueva;

            if ($diferencia != 0) {
                $query = "UPDATE producto SET cantidad = cantidad + :diferencia WHERE id = :id_producto";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":diferencia", $diferencia);
                $stmt->bindParam(":id_producto", $data['id_producto']);
                $stmt->execute();
            }

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            $this->errors[] = "Error al modificar pérdida: " . $e->getMessage();
            return false;
        }
    }

    public function modificarOtroEntradaConAjuste($id_entrada, $data)
    {
        try {
            $this->db->beginTransaction();

            // 1. Obtener la entrada OTRO original
            $entrada_original = $this->obtenerMovimientoPorId($id_entrada);
            if (!$entrada_original || $entrada_original['tipo_movimiento'] !== 'ENTRADA' || $entrada_original['subtipo_movimiento'] !== 'OTRO') {
                throw new PDOException("Entrada OTRO no encontrada o no válida");
            }

            if ($entrada_original['id_estatus'] != 1) {
                throw new PDOException("No se puede modificar una entrada OTRO que ya ha sido ajustada");
            }

            // 2. Marcar la entrada original como inactiva
            $query = "UPDATE movimientos_inventario SET id_estatus = 2 WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id_entrada);
            $stmt->execute();

            // 3. Generar número de transacción para el ajuste
            $query_txn = "SELECT GenerarNumeroTransaccion() as numero_transaccion";
            $stmt_txn = $this->db->prepare($query_txn);
            $stmt_txn->execute();
            $result_txn = $stmt_txn->fetch(PDO::FETCH_ASSOC);
            $numero_transaccion_ajuste = $result_txn['numero_transaccion'];

            // 4. Crear nueva entrada OTRO como ajuste
            $observaciones_ajuste = "AJUSTE - Modificación de entrada OTRO #{$entrada_original['numero_transaccion']}";
            if (!empty($data['observaciones'])) {
                $observaciones_ajuste .= " - " . $data['observaciones'];
            }

            $query = "INSERT INTO movimientos_inventario (
                id_producto, tipo_movimiento, subtipo_movimiento, cantidad, precio_unitario,
                numero_transaccion, fecha_movimiento, observaciones, id_usuario, 
                id_movimiento_original, id_estatus
            ) VALUES (
                :id_producto, 'ENTRADA', 'OTRO', :cantidad, :precio_unitario,
                :numero_transaccion, :fecha_movimiento, :observaciones, :id_usuario,
                :id_movimiento_original, 1
            )";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id_producto", $data['id_producto']);
            $stmt->bindParam(":cantidad", $data['cantidad']);
            $stmt->bindParam(":precio_unitario", $data['precio_unitario']);
            $stmt->bindParam(":numero_transaccion", $numero_transaccion_ajuste);
            $stmt->bindParam(":fecha_movimiento", $data['fecha']);
            $stmt->bindParam(":observaciones", $observaciones_ajuste);
            $stmt->bindParam(":id_usuario", $data['id_usuario']);
            $stmt->bindParam(":id_movimiento_original", $id_entrada);
            $stmt->execute();

            // 5. Calcular diferencia y ajustar stock
            $cantidad_original = $entrada_original['cantidad'];
            $cantidad_nueva = $data['cantidad'];
            
            // Para entradas OTRO: diferencia = cantidad_nueva - cantidad_original
            // Si la nueva cantidad es mayor, agregamos más stock (positivo)
            // Si la nueva cantidad es menor, quitamos stock (negativo)
            $diferencia = $cantidad_nueva - $cantidad_original;

            if ($diferencia != 0) {
                $query = "UPDATE producto SET cantidad = cantidad + :diferencia WHERE id = :id_producto";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":diferencia", $diferencia);
                $stmt->bindParam(":id_producto", $data['id_producto']);
                $stmt->execute();
            }

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            $this->errors[] = "Error al modificar entrada OTRO: " . $e->getMessage();
            return false;
        }
    }

    public function registrarCompra($data)
    {
        try {
            $this->db->beginTransaction();

            // Generar un número de transacción único para toda la compra
            $query_txn = "SELECT GenerarNumeroTransaccion() as numero_transaccion";
            $stmt_txn = $this->db->prepare($query_txn);
            $stmt_txn->execute();
            $result_txn = $stmt_txn->fetch(PDO::FETCH_ASSOC);
            $numero_transaccion = $result_txn['numero_transaccion'];

            $ultimo_id_movimiento = null;

            // Obtener nombre del proveedor
            $nombre_proveedor = $this->obtenerNombreProveedor($data['id_proveedor']);

            // 1. Registrar cada producto como movimiento de inventario
            foreach ($data['productos'] as $producto) {
                if (empty($producto['id_producto']) || empty($producto['cantidad']) || empty($producto['precio_compra'])) {
                    continue; // Saltar productos vacíos
                }

                // Generar referencia en formato: Compra #numero_transaccion
                $referencia = "Compra #" . $numero_transaccion;
                
                // Generar observaciones en formato: COMPRA - Proveedor: "nombre" - "descripción"
                $observaciones = "COMPRA - Proveedor: " . $nombre_proveedor;
                if (!empty($data['observaciones'])) {
                    $observaciones .= " - " . $data['observaciones'];
                }

                // Usar fecha y hora actual del servidor
                $fecha_hora_actual = date('Y-m-d H:i:s');

                // Insertar movimiento de inventario con número de transacción específico
                $query = "INSERT INTO movimientos_inventario (
                    id_producto, tipo_movimiento, subtipo_movimiento, cantidad, 
                    precio_unitario, id_referencia, tipo_referencia, fecha_movimiento,
                    observaciones, id_usuario, id_estatus, numero_transaccion
                ) VALUES (
                    :id_producto, 'ENTRADA', 'COMPRA', :cantidad,
                    :precio_unitario, :id_referencia, 'COMPRA', :fecha_movimiento,
                    :observaciones, :id_usuario, 1, :numero_transaccion
                )";

                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id_producto", $producto['id_producto']);
                $stmt->bindParam(":cantidad", $producto['cantidad']);
                $stmt->bindParam(":precio_unitario", $producto['precio_compra']);
                $stmt->bindParam(":id_referencia", $referencia); // Guardar referencia de compra
                $stmt->bindParam(":fecha_movimiento", $fecha_hora_actual);
                $stmt->bindParam(":observaciones", $observaciones);
                $stmt->bindParam(":id_usuario", $data['id_usuario']);
                $stmt->bindParam(":numero_transaccion", $numero_transaccion);
                $stmt->execute();

                // Obtener el ID del movimiento recién insertado
                $ultimo_id_movimiento = $this->db->lastInsertId();

                // 2. Actualizar stock del producto
                $query = "UPDATE producto SET cantidad = cantidad + :cantidad WHERE id = :id_producto";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":cantidad", $producto['cantidad']);
                $stmt->bindParam(":id_producto", $producto['id_producto']);
                $stmt->execute();

                // 3. Actualizar o crear relación producto-proveedor
                require_once ROOT_PATH . 'models/ProductoProveedorModel.php';
                $ppModel = new ProductoProveedorModel();
                
                // Obtener cédula del proveedor
                $cedula_proveedor = $this->obtenerCedulaProveedor($data['id_proveedor']);
                
                if ($cedula_proveedor) {
                    $ppModel->actualizarOCrearRelacion(
                        $producto['id_producto'],
                        $cedula_proveedor,
                        $producto['precio_compra']
                    );
                }
            }

            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Compra registrada exitosamente',
                'id_movimiento' => $ultimo_id_movimiento,
                'numero_transaccion' => $numero_transaccion
            ];

        } catch (PDOException $e) {
            $this->db->rollBack();
            $this->errors[] = "Error al registrar compra: " . $e->getMessage();
            return [
                'success' => false,
                'message' => "Error al registrar compra: " . $e->getMessage()
            ];
        }
    }

    private function obtenerNombreProveedor($id_proveedor)
    {
        $query = "SELECT nombre FROM proveedores WHERE cedula = :cedula";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":cedula", $id_proveedor);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['nombre'] : 'Desconocido';
    }

    private function obtenerCedulaProveedor($id_proveedor)
    {
        // Si ya es una cédula, devolverla directamente
        if (is_string($id_proveedor) && strlen($id_proveedor) <= 15) {
            return $id_proveedor;
        }
        
        $query = "SELECT cedula FROM proveedores WHERE cedula = :cedula";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":cedula", $id_proveedor);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['cedula'] : $id_proveedor;
    }
}