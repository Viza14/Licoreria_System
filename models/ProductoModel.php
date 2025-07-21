<?php
class ProductoModel
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

    public function obtenerTodosProductos($soloActivos = true, $pagina = 1, $porPagina = 10, $filtros = [])
    {
        try {
            error_log('Iniciando obtenerTodosProductos');
            $condiciones = [];
            $params = [];

            // Construir la consulta base
            $baseQuery = "FROM producto p
                         JOIN categorias c ON p.id_categoria = c.id
                         JOIN estatus e ON p.id_estatus = e.id
                         JOIN tipos_categoria tc ON c.id_tipo_categoria = tc.id";

            // Aplicar filtros
            if ($soloActivos) {
                $condiciones[] = "p.id_estatus = 1";
                $condiciones[] = "c.id_estatus = 1";
                error_log('Aplicando filtro de productos y categorías activas');
            }

            // Filtro de búsqueda
            if (!empty($filtros['busqueda'])) {
                $busqueda = '%' . $filtros['busqueda'] . '%';
                $condiciones[] = "(LOWER(CONVERT(p.descripcion USING utf8)) LIKE LOWER(:busqueda) OR 
                                  LOWER(CONVERT(c.nombre USING utf8)) LIKE LOWER(:busqueda) OR 
                                  LOWER(CONVERT(tc.nombre USING utf8)) LIKE LOWER(:busqueda))";
                $params[':busqueda'] = $busqueda;
            }

            // Filtro de categoría
            if (!empty($filtros['categoria'])) {
                $condiciones[] = "LOWER(CONVERT(c.nombre USING utf8)) = LOWER(:categoria)";
                $params[':categoria'] = $filtros['categoria'];
            }

            // Filtro de tipo
            if (!empty($filtros['tipo'])) {
                $condiciones[] = "LOWER(CONVERT(tc.nombre USING utf8)) = LOWER(:tipo)";
                $params[':tipo'] = $filtros['tipo'];
            }

            // Filtro de estatus
            if (!empty($filtros['estatus'])) {
                $condiciones[] = "LOWER(CONVERT(e.nombre USING utf8)) = LOWER(:estatus)";
                $params[':estatus'] = $filtros['estatus'];
            }

            // Agregar condiciones a la consulta
            $whereClause = '';
            if (!empty($condiciones)) {
                $whereClause = " WHERE " . implode(" AND ", $condiciones);
            }

            // Contar total de registros para paginación
            $queryCount = "SELECT COUNT(*) as total " . $baseQuery . $whereClause;
            $stmtCount = $this->db->prepare($queryCount);
            foreach ($params as $key => $value) {
                $stmtCount->bindValue($key, $value);
            }
            $stmtCount->execute();
            $totalRegistros = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];

            // Calcular total de páginas
            $totalPaginas = ceil($totalRegistros / $porPagina);
            $pagina = max(1, min($pagina, $totalPaginas)); // Asegurar que la página esté en rango válido
            $offset = ($pagina - 1) * $porPagina;

            // Obtener los registros paginados
            $query = "SELECT p.*, c.nombre as categoria, e.nombre as estatus, tc.nombre as tipo_categoria "
                   . $baseQuery . $whereClause . "
                   ORDER BY p.descripcion ASC
                   LIMIT :offset, :porPagina";
            
            error_log('Query a ejecutar: ' . $query);
            $stmt = $this->db->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':porPagina', $porPagina, PDO::PARAM_INT);
            $stmt->execute();
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log('Productos encontrados: ' . count($productos));
            
            return [
                'productos' => $productos,
                'total_registros' => $totalRegistros,
                'pagina_actual' => $pagina,
                'por_pagina' => $porPagina,
                'total_paginas' => $totalPaginas
            ];
        } catch (PDOException $e) {
            error_log('Error en obtenerTodosProductos: ' . $e->getMessage());
            $this->errors[] = "Error al obtener productos: " . $e->getMessage();
            return false;
        }
    }

    public function obtenerProductosActivos()
    {
        try {
            $query = "SELECT p.*, c.nombre as categoria, e.nombre as estatus, tc.nombre as tipo_categoria 
                     FROM producto p
                     JOIN categorias c ON p.id_categoria = c.id
                     JOIN estatus e ON p.id_estatus = e.id
                     JOIN tipos_categoria tc ON c.id_tipo_categoria = tc.id
                     WHERE p.id_estatus = 1 AND c.id_estatus = 1
                     ORDER BY p.descripcion ASC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log('Productos activos encontrados: ' . count($productos));
            
            // Log de productos para debugging
            foreach ($productos as $producto) {
                error_log("Producto: {$producto['descripcion']} - ID: {$producto['id']} - Estatus: {$producto['id_estatus']}");
            }
            
            return $productos;
        } catch (PDOException $e) {
            error_log('Error en obtenerProductosActivos: ' . $e->getMessage());
            $this->errors[] = "Error al obtener productos: " . $e->getMessage();
            return [];
        }
    }


    public function obtenerProductoPorId($id)
    {
        $query = "SELECT p.*, 
              c.nombre as categoria, 
              e.nombre as estatus,
              tc.nombre as tipo_categoria
              FROM producto p
              JOIN categorias c ON p.id_categoria = c.id
              JOIN estatus e ON p.id_estatus = e.id
              JOIN tipos_categoria tc ON c.id_tipo_categoria = tc.id
              WHERE p.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crearProducto($data)
    {
        try {
            $query = "INSERT INTO producto (descripcion, cantidad, precio, id_categoria, id_estatus) 
                      VALUES (:descripcion, :cantidad, :precio, :id_categoria, :id_estatus)";
            $stmt = $this->db->prepare($query);

            return $stmt->execute($data);
        } catch (PDOException $e) {
            $this->errors[] = "Error de base de datos: " . $e->getMessage();
            return false;
        }
    }

    public function actualizarProducto($id, $data)
    {
        try {
            $query = "UPDATE producto SET 
                      descripcion = :descripcion,
                      cantidad = :cantidad,
                      precio = :precio,
                      id_categoria = :id_categoria,
                      id_estatus = :id_estatus
                      WHERE id = :id";

            $stmt = $this->db->prepare($query);
            $data['id'] = $id;
            return $stmt->execute($data);
        } catch (PDOException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    public function cambiarEstado($id, $nuevoEstado)
    {
        try {
            $query = "UPDATE producto SET id_estatus = :estatus WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":estatus", $nuevoEstado);
            $stmt->bindParam(":id", $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    public function obtenerCategorias()
    {
        $query = "SELECT c.*, tc.nombre as tipo_categoria 
                  FROM categorias c
                  JOIN tipos_categoria tc ON c.id_tipo_categoria = tc.id
                  WHERE c.id_estatus = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTiposCategoria()
    {
        $query = "SELECT * FROM tipos_categoria";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerEstatus()
    {
        $query = "SELECT * FROM estatus WHERE id IN (1,2,3,4)";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeProducto($descripcion, $idCategoria, $excluirId = null)
    {
        $query = "SELECT COUNT(*) as count FROM producto 
                  WHERE descripcion = :descripcion AND id_categoria = :id_categoria";

        if ($excluirId) {
            $query .= " AND id != :excluirId";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":id_categoria", $idCategoria);

        if ($excluirId) {
            $stmt->bindParam(":excluirId", $excluirId);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    public function contarProductos()
    {
        try {
            $query = "SELECT COUNT(*) as total FROM producto";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            $this->errors[] = "Error al contar productos: " . $e->getMessage();
            return 0;
        }
    }

    private function obtenerSiguienteNumeroEntrada()
    {
        $query = "SELECT MAX(id) as ultimo_id FROM movimientos_inventario WHERE tipo_movimiento = 'ENTRADA'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['ultimo_id'] ?? 0) + 1;
    }

    public function registrarEntradaProducto($id_producto, $cantidad, $precio_compra, $cedula_proveedor, $id_usuario, $observaciones = null)
    {
        try {
            $this->db->beginTransaction();

            // 1. Verificar que el producto esté activo
            $query = "SELECT id_estatus FROM producto WHERE id = :id_producto";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id_producto", $id_producto);
            $stmt->execute();
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$producto || $producto['id_estatus'] != 1) {
                throw new PDOException("No se puede registrar entrada para un producto inactivo");
            }

            // Obtener el siguiente número de entrada
            $numeroEntrada = $this->obtenerSiguienteNumeroEntrada();
            
            // Agregar el número de entrada a las observaciones
            $observacionesCompletas = "Entrada #" . $numeroEntrada . ($observaciones ? " - " . $observaciones : "");

            // 2. Registrar la entrada en movimientos_inventario
            $query = "INSERT INTO movimientos_inventario 
                 (id_producto, tipo_movimiento, cantidad, precio_unitario, id_usuario, observaciones) 
                 VALUES (:id_producto, 'ENTRADA', :cantidad, :precio_compra, :id_usuario, :observaciones)";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id_producto", $id_producto);
            $stmt->bindParam(":cantidad", $cantidad);
            $stmt->bindParam(":precio_compra", $precio_compra);
            $stmt->bindParam(":id_usuario", $id_usuario);
            $stmt->bindParam(":observaciones", $observacionesCompletas);
            $stmt->execute();

            // 3. Actualizar el stock del producto
            $query = "UPDATE producto SET cantidad = cantidad + :cantidad WHERE id = :id_producto";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":cantidad", $cantidad);
            $stmt->bindParam(":id_producto", $id_producto);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            $this->errors[] = "Error al registrar entrada: " . $e->getMessage();
            return false;
        }
    }

    public function obtenerEntradaPorId($id)
    {
        $query = "SELECT e.*, p.descripcion as producto_descripcion, pr.nombre as proveedor_nombre
              FROM movimientos_inventario e
              JOIN producto p ON e.id_producto = p.id
              JOIN proveedores pr ON e.cedula_proveedor = pr.cedula
              WHERE e.id = ? AND e.tipo_movimiento = 'ENTRADA'";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarEntradaProducto($id, $cantidad, $precio_compra, $observaciones)
    {
        try {
            $this->db->beginTransaction();

            // 1. Obtener la entrada actual para calcular la diferencia
            $entrada_actual = $this->obtenerEntradaPorId($id);
            if (!$entrada_actual) {
                throw new PDOException("Entrada no encontrada");
            }

            $diferencia = $cantidad - $entrada_actual['cantidad'];

            // 2. Mantener el número de entrada en las observaciones
            $observacionesActuales = $entrada_actual['observaciones'];
            $matches = [];
            if (preg_match('/^Entrada #(\d+)(?:\s*-\s*(.*))?$/', $observacionesActuales, $matches)) {
                $numeroEntrada = $matches[1];
                $observacionesCompletas = "Entrada #" . $numeroEntrada . ($observaciones ? " - " . $observaciones : "");
            } else {
                $observacionesCompletas = $observaciones;
            }

            // Actualizar el movimiento de inventario
            $query = "UPDATE movimientos_inventario SET 
                  cantidad = :cantidad,
                  precio_unitario = :precio_compra,
                  observaciones = :observaciones,
                  fecha_actualizacion = NOW()
                  WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":cantidad", $cantidad);
            $stmt->bindParam(":precio_compra", $precio_compra);
            $stmt->bindParam(":observaciones", $observacionesCompletas);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            // 3. Actualizar el stock del producto
            $query = "UPDATE producto SET cantidad = cantidad + :diferencia WHERE id = :id_producto";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":diferencia", $diferencia);
            $stmt->bindParam(":id_producto", $entrada_actual['id_producto']);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    public function obtenerStockProducto($idProducto)
    {
        $query = "SELECT cantidad FROM producto WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $idProducto);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['cantidad'] : 0;
    }
}
