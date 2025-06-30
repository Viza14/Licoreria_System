<?php
class ReporteModel
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

    public function generarReporteInventario($filtros = [])
    {
        try {
            $query = "SELECT 
                        p.id,
                        p.descripcion as producto,
                        c.nombre as categoria,
                        tc.nombre as tipo_categoria,
                        p.cantidad as stock_actual,
                        sl.stock_minimo,
                        sl.stock_maximo,
                        CASE 
                            WHEN p.cantidad <= sl.stock_minimo THEN 'CRÍTICO'
                            WHEN p.cantidad <= (sl.stock_minimo * 1.5) THEN 'BAJO'
                            WHEN p.cantidad >= sl.stock_maximo THEN 'EXCESO'
                            ELSE 'NORMAL'
                        END as estado_stock,
                        p.precio as precio_venta,
                        (SELECT precio_compra FROM proveedor_producto pp WHERE pp.id_producto = p.id ORDER BY precio_compra ASC LIMIT 1) as precio_compra_minimo,
                        (p.precio * p.cantidad) as valor_total
                      FROM producto p
                      JOIN categorias c ON p.id_categoria = c.id
                      JOIN tipos_categoria tc ON c.id_tipo_categoria = tc.id
                      LEFT JOIN stock_limites sl ON p.id = sl.id_producto
                      WHERE p.id_estatus = 1";

            $params = [];

            // Filtros
            if (!empty($filtros['id_categoria'])) {
                $query .= " AND p.id_categoria = :id_categoria";
                $params[':id_categoria'] = $filtros['id_categoria'];
            }

            if (!empty($filtros['id_tipo_categoria'])) {
                $query .= " AND c.id_tipo_categoria = :id_tipo_categoria";
                $params[':id_tipo_categoria'] = $filtros['id_tipo_categoria'];
            }

            if (!empty($filtros['estado_stock'])) {
                switch ($filtros['estado_stock']) {
                    case 'CRITICO':
                        $query .= " AND p.cantidad <= sl.stock_minimo";
                        break;
                    case 'BAJO':
                        $query .= " AND p.cantidad > sl.stock_minimo AND p.cantidad <= (sl.stock_minimo * 1.5)";
                        break;
                    case 'EXCESO':
                        $query .= " AND p.cantidad >= sl.stock_maximo";
                        break;
                    case 'NORMAL':
                        $query .= " AND p.cantidad > (sl.stock_minimo * 1.5) AND p.cantidad < sl.stock_maximo";
                        break;
                }
            }

            $query .= " ORDER BY p.descripcion";

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->errors[] = "Error al generar reporte de inventario: " . $e->getMessage();
            return false;
        }
    }

    public function generarReporteVentas($filtros = [])
    {
        try {
            $query = "SELECT 
                        v.id as id_venta,
                        v.fecha,
                        CONCAT(c.nombres, ' ', c.apellidos) as cliente,
                        c.cedula as cedula_cliente,
                        CONCAT(u.nombres, ' ', u.apellidos) as vendedor,
                        v.monto_total,
                        COUNT(dv.id) as cantidad_productos,
                        GROUP_CONCAT(p.descripcion SEPARATOR ', ') as productos
                      FROM ventas v
                      JOIN clientes c ON v.cedula_cliente = c.cedula
                      JOIN usuarios u ON v.id_usuario = u.id
                      JOIN detalle_venta dv ON v.id = dv.id_venta
                      JOIN producto p ON dv.id_producto = p.id
                      WHERE v.id_estatus = 1";

            $params = [];

            if (!empty($filtros['fecha_inicio'])) {
                $query .= " AND v.fecha >= :fecha_inicio";
                $params[':fecha_inicio'] = $filtros['fecha_inicio'];
            }

            if (!empty($filtros['fecha_fin'])) {
                $query .= " AND v.fecha <= :fecha_fin";
                $params[':fecha_fin'] = $filtros['fecha_fin'];
            }

            if (!empty($filtros['id_usuario'])) {
                $query .= " AND v.id_usuario = :id_usuario";
                $params[':id_usuario'] = $filtros['id_usuario'];
            }

            if (!empty($filtros['cedula_cliente'])) {
                $query .= " AND v.cedula_cliente = :cedula_cliente";
                $params[':cedula_cliente'] = $filtros['cedula_cliente'];
            }

            $query .= " GROUP BY v.id
                      ORDER BY v.fecha DESC";

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->errors[] = "Error al generar reporte de ventas: " . $e->getMessage();
            return false;
        }
    }

    public function generarDetalleVentas($filtros = [])
    {
        try {
            $query = "SELECT 
                        v.id as id_venta,
                        v.fecha,
                        p.descripcion as producto,
                        dv.cantidad,
                        p.precio as precio_unitario,
                        dv.monto as subtotal,
                        c.nombres as cliente,
                        u.nombres as vendedor
                      FROM ventas v
                      JOIN detalle_venta dv ON v.id = dv.id_venta
                      JOIN producto p ON dv.id_producto = p.id
                      JOIN clientes c ON v.cedula_cliente = c.cedula
                      JOIN usuarios u ON v.id_usuario = u.id
                      WHERE v.id_estatus = 1";

            $params = [];

            // Filtros
            if (!empty($filtros['fecha_inicio'])) {
                $query .= " AND v.fecha >= :fecha_inicio";
                $params[':fecha_inicio'] = $filtros['fecha_inicio'];
            }

            if (!empty($filtros['fecha_fin'])) {
                $query .= " AND v.fecha <= :fecha_fin";
                $params[':fecha_fin'] = $filtros['fecha_fin'];
            }

            if (!empty($filtros['id_producto'])) {
                $query .= " AND dv.id_producto = :id_producto";
                $params[':id_producto'] = $filtros['id_producto'];
            }

            $query .= " ORDER BY v.fecha DESC, p.descripcion";

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->errors[] = "Error al generar detalle de ventas: " . $e->getMessage();
            return false;
        }
    }

    public function obtenerResumenVentas($filtros = [])
    {
        try {
            $query = "SELECT 
                        DATE_FORMAT(v.fecha, '%Y-%m') as mes,
                        COUNT(v.id) as total_ventas,
                        SUM(v.monto_total) as monto_total,
                        ROUND(AVG(v.monto_total), 2) as promedio_venta,
                        MAX(v.monto_total) as venta_maxima,
                        MIN(v.monto_total) as venta_minima
                    FROM ventas v
                    WHERE v.id_estatus = 1";

            $params = [];
            
            if (!empty($filtros['fecha_inicio'])) {
                $query .= " AND v.fecha >= :fecha_inicio";
                $params[':fecha_inicio'] = $filtros['fecha_inicio'];
            }

            if (!empty($filtros['fecha_fin'])) {
                $query .= " AND v.fecha <= :fecha_fin";
                $params[':fecha_fin'] = $filtros['fecha_fin'];
            }

            $query .= " GROUP BY DATE_FORMAT(v.fecha, '%Y-%m')
                      ORDER BY mes DESC";

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->errors[] = "Error al generar resumen de ventas: " . $e->getMessage();
            return false;
        }
    }

    public function obtenerProductosMasVendidos($filtros = [])
    {
        try {
            $query = "SELECT 
                        p.id,
                        p.descripcion as producto,
                        c.nombre as categoria,
                        SUM(dv.cantidad) as total_vendido,
                        SUM(dv.monto) as monto_total
                      FROM detalle_venta dv
                      JOIN producto p ON dv.id_producto = p.id
                      JOIN categorias c ON p.id_categoria = c.id
                      JOIN ventas v ON dv.id_venta = v.id
                      WHERE v.id_estatus = 1";

            $params = [];

            // Filtros
            if (!empty($filtros['fecha_inicio'])) {
                $query .= " AND v.fecha >= :fecha_inicio";
                $params[':fecha_inicio'] = $filtros['fecha_inicio'];
            }

            if (!empty($filtros['fecha_fin'])) {
                $query .= " AND v.fecha <= :fecha_fin";
                $params[':fecha_fin'] = $filtros['fecha_fin'];
            }

            if (!empty($filtros['id_categoria'])) {
                $query .= " AND p.id_categoria = :id_categoria";
                $params[':id_categoria'] = $filtros['id_categoria'];
            }

            $query .= " GROUP BY p.id, p.descripcion, c.nombre
                      ORDER BY total_vendido DESC
                      LIMIT 10";

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->errors[] = "Error al obtener productos más vendidos: " . $e->getMessage();
            return false;
        }
    }

    public function obtenerVentasMensuales()
    {
        $query = "SELECT 
                    YEAR(fecha) as anio,
                    MONTH(fecha) as mes,
                    SUM(monto_total) as total,
                    MIN(monto_total) as minimo,
                    MAX(monto_total) as maximo,
                    (SELECT monto_total FROM ventas 
                     WHERE MONTH(fecha) = mes 
                     AND YEAR(fecha) = anio 
                     ORDER BY fecha ASC LIMIT 1) as apertura,
                    (SELECT monto_total FROM ventas 
                     WHERE MONTH(fecha) = mes 
                     AND YEAR(fecha) = anio 
                     ORDER BY fecha DESC LIMIT 1) as cierre
                  FROM ventas
                  WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                  AND id_estatus = 1
                  GROUP BY YEAR(fecha), MONTH(fecha)
                  ORDER BY anio, mes";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerVentasSemanales()
    {
        try {
            $query = "SELECT 
                    YEARWEEK(fecha, 1) as semana,
                    SUM(monto_total) as total_ventas,
                    SUM((SELECT SUM(cantidad) FROM detalle_venta WHERE id_venta = v.id)) as total_productos
                  FROM ventas v
                  WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 8 WEEK)
                  AND id_estatus = 1
                  GROUP BY YEARWEEK(fecha, 1)
                  ORDER BY semana DESC
                  LIMIT 7";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->errors[] = "Error al obtener ventas semanales: " . $e->getMessage();
            return false;
        }
    }

    public function obtenerProductosPorDiaSemana() {
        try {
            // Start from current day
            $hoy = new DateTime();
            $inicioSemana = clone $hoy;
            $inicioSemana->modify('-6 days'); // Get data from 6 days ago
            
            // Spanish day abbreviations array
            $diasSemana = [
                'Monday' => 'LUN',
                'Tuesday' => 'MAR', 
                'Wednesday' => 'MIE', 
                'Thursday' => 'JUE', 
                'Friday' => 'VIE', 
                'Saturday' => 'SAB', 
                'Sunday' => 'DOM'
            ];
            
            // Query to get products sold for the last 7 days
            $query = "SELECT 
                        DATE(fecha) as fecha,
                        DAYNAME(fecha) as dia_semana,
                        SUM((SELECT SUM(cantidad) FROM detalle_venta WHERE id_venta = v.id)) as total_productos
                      FROM ventas v
                      WHERE fecha >= :inicio_semana
                      AND fecha <= NOW()
                      AND id_estatus = 1
                      GROUP BY DATE(fecha), DAYNAME(fecha)
                      ORDER BY fecha";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([':inicio_semana' => $inicioSemana->format('Y-m-d')]);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Initialize array with last 7 days
            $diasCompletos = [];
            $fechaActual = clone $inicioSemana;
            
            // Fill array with the last 7 days
            for ($i = 0; $i < 7; $i++) {
                $nombreDiaIngles = $fechaActual->format('l');
                $nombreDiaCorto = $diasSemana[$nombreDiaIngles];
                $diasCompletos[$nombreDiaCorto . '-' . $fechaActual->format('d')] = 0;
                $fechaActual->modify('+1 day');
            }
            
            // Merge query results with initialized array
            foreach ($resultados as $resultado) {
                $fecha = new DateTime($resultado['fecha']);
                $nombreDiaIngles = $resultado['dia_semana'];
                $nombreDiaCorto = $diasSemana[$nombreDiaIngles];
                $clave = $nombreDiaCorto . '-' . $fecha->format('d');
                
                if (array_key_exists($clave, $diasCompletos)) {
                    $diasCompletos[$clave] = (int)$resultado['total_productos'];
                }
            }
            
            return $diasCompletos;
        } catch (PDOException $e) {
            $this->errors[] = "Error al obtener productos por día de semana: " . $e->getMessage();
            return false;
        }
    }
}
