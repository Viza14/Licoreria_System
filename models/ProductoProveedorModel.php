<?php
class ProductoProveedorModel
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

    public function obtenerTodasRelaciones($pagina = 1, $por_pagina = 10, $filtros = [])
    {
        try {
            $condiciones = [];
            $params = [];

            // Aplicar filtros si existen
            if (!empty($filtros['busqueda'])) {
                $condiciones[] = "(LOWER(CONVERT(p.descripcion USING utf8)) LIKE :busqueda OR 
                                  LOWER(CONVERT(pr.nombre USING utf8)) LIKE :busqueda OR
                                  LOWER(CONVERT(CONCAT(sc.nombre, '-', pr.cedula) USING utf8)) LIKE :busqueda)";
                $params[':busqueda'] = "%{$filtros['busqueda']}%";
            }

            if (!empty($filtros['estatus'])) {
                $condiciones[] = "LOWER(CONVERT(e.nombre USING utf8)) = :estatus";
                $params[':estatus'] = $filtros['estatus'];
            }

            // Construir la cláusula WHERE
            $whereClause = !empty($condiciones) ? "WHERE " . implode(" AND ", $condiciones) : "";

            // Obtener el total de registros con los filtros aplicados
            $queryCount = "SELECT COUNT(*) 
                          FROM proveedor_producto pp
                          JOIN producto p ON pp.id_producto = p.id
                          JOIN proveedores pr ON pp.cedula_proveedor = pr.cedula
                          JOIN simbolos_cedula sc ON pr.id_simbolo_cedula = sc.id
                          JOIN estatus e ON pp.id_estatus = e.id
                          $whereClause";

            $stmtCount = $this->db->prepare($queryCount);
            foreach ($params as $param => $value) {
                $stmtCount->bindValue($param, $value);
            }
            $stmtCount->execute();
            $total_registros = $stmtCount->fetchColumn();
            
            // Calcular el total de páginas
            $total_paginas = ceil($total_registros / $por_pagina);
            
            // Asegurar que la página actual es válida
            $pagina = max(1, min($pagina, $total_paginas));
            
            // Calcular el offset
            $offset = ($pagina - 1) * $por_pagina;
            
            // Consulta principal con paginación y filtros
            $query = "SELECT pp.*, p.descripcion as producto, pr.nombre as proveedor, 
                      sc.nombre as simbolo_proveedor, pr.cedula as cedula_proveedor, e.nombre as estatus
                      FROM proveedor_producto pp
                      JOIN producto p ON pp.id_producto = p.id
                      JOIN proveedores pr ON pp.cedula_proveedor = pr.cedula
                      JOIN simbolos_cedula sc ON pr.id_simbolo_cedula = sc.id
                      JOIN estatus e ON pp.id_estatus = e.id
                      $whereClause
                      ORDER BY pp.id DESC
                      LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($query);
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }
            $stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return [
                'datos' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'total_registros' => $total_registros,
                'total_paginas' => $total_paginas,
                'pagina_actual' => $pagina
            ];
        } catch (PDOException $e) {
            $this->errors[] = "Error al obtener las relaciones: " . $e->getMessage();
            return [
                'datos' => [],
                'total_registros' => 0,
                'total_paginas' => 1,
                'pagina_actual' => 1
            ];
        }
    }

    public function obtenerRelacionPorId($id)
    {
        $query = "SELECT pp.*, p.descripcion as producto, pr.nombre as proveedor, 
                  sc.nombre as simbolo_proveedor, e.nombre as estatus
                  FROM proveedor_producto pp
                  JOIN producto p ON pp.id_producto = p.id
                  JOIN proveedores pr ON pp.cedula_proveedor = pr.cedula
                  JOIN simbolos_cedula sc ON pr.id_simbolo_cedula = sc.id
                  JOIN estatus e ON pp.id_estatus = e.id
                  WHERE pp.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crearRelacion($data)
    {
        try {
            // Verificar si ya existe la relación
            $query = "SELECT COUNT(*) as count FROM proveedor_producto 
                      WHERE cedula_proveedor = :cedula_proveedor AND id_producto = :id_producto";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":cedula_proveedor", $data['cedula_proveedor']);
            $stmt->bindParam(":id_producto", $data['id_producto']);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                $this->errors[] = "Ya existe una relación entre este producto y proveedor";
                return false;
            }

            // Agregar estatus por defecto
            $data['id_estatus'] = 1;

            $query = "INSERT INTO proveedor_producto (cedula_proveedor, id_producto, precio_compra, id_estatus)
                      VALUES (:cedula_proveedor, :id_producto, :precio_compra, :id_estatus)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            $this->errors[] = "Error de base de datos: " . $e->getMessage();
            return false;
        }
    }

    public function actualizarRelacion($id, $data)
    {
        try {
            // Verificar si ya existe la relación con el nuevo producto
            if (isset($data['id_producto'])) {
                $query = "SELECT COUNT(*) as count FROM proveedor_producto 
                          WHERE cedula_proveedor = (SELECT cedula_proveedor FROM proveedor_producto WHERE id = :id)
                          AND id_producto = :id_producto AND id != :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id", $id);
                $stmt->bindParam(":id_producto", $data['id_producto']);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result['count'] > 0) {
                    $this->errors[] = "Ya existe una relación con este producto y proveedor";
                    return false;
                }
            }

            $query = "UPDATE proveedor_producto SET 
                      precio_compra = :precio_compra,
                      id_producto = :id_producto
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
            $query = "UPDATE proveedor_producto SET id_estatus = :estatus WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":estatus", $nuevoEstado);
            $stmt->bindParam(":id", $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    public function obtenerProductos()
    {
        $query = "SELECT id, descripcion FROM producto WHERE id_estatus = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProveedores()
    {
        $query = "SELECT p.cedula, CONCAT(sc.nombre, '-', p.cedula) as cedula_completa, p.nombre 
                  FROM proveedores p
                  JOIN simbolos_cedula sc ON p.id_simbolo_cedula = sc.id
                  WHERE p.id_estatus = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerEstatus()
    {
        $query = "SELECT * FROM estatus";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPrecioPorProveedorProducto($id_producto, $cedula_proveedor) {
        $query = "SELECT precio_compra FROM proveedor_producto 
                  WHERE id_producto = :id_producto 
                  AND cedula_proveedor = :cedula_proveedor
                  AND id_estatus = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id_producto", $id_producto);
        $stmt->bindParam(":cedula_proveedor", $cedula_proveedor);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public function actualizarOCrearRelacion($id_producto, $cedula_proveedor, $precio_compra)
    {
        try {
            // Verificar si ya existe la relación
            $query = "SELECT id FROM proveedor_producto 
                      WHERE id_producto = :id_producto AND cedula_proveedor = :cedula_proveedor";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id_producto", $id_producto);
            $stmt->bindParam(":cedula_proveedor", $cedula_proveedor);
            $stmt->execute();
            
            $relacion_existente = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($relacion_existente) {
                // Actualizar relación existente
                $query = "UPDATE proveedor_producto SET 
                          precio_compra = :precio_compra, 
                          id_estatus = 1 
                          WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":precio_compra", $precio_compra);
                $stmt->bindParam(":id", $relacion_existente['id']);
                return $stmt->execute();
            } else {
                // Crear nueva relación
                $query = "INSERT INTO proveedor_producto (id_producto, cedula_proveedor, precio_compra, id_estatus)
                          VALUES (:id_producto, :cedula_proveedor, :precio_compra, 1)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id_producto", $id_producto);
                $stmt->bindParam(":cedula_proveedor", $cedula_proveedor);
                $stmt->bindParam(":precio_compra", $precio_compra);
                return $stmt->execute();
            }
        } catch (PDOException $e) {
            $this->errors[] = "Error al actualizar o crear relación: " . $e->getMessage();
            return false;
        }
    }

    public function obtenerProductosPorProveedor($cedula_proveedor)
    {
        try {
            $query = "SELECT p.id, p.descripcion, pp.precio_compra, p.precio as precio_venta
                      FROM producto p
                      JOIN proveedor_producto pp ON p.id = pp.id_producto
                      WHERE pp.cedula_proveedor = :cedula_proveedor 
                      AND p.id_estatus = 1 
                      AND pp.id_estatus = 1
                      ORDER BY p.descripcion ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":cedula_proveedor", $cedula_proveedor);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->errors[] = "Error al obtener productos por proveedor: " . $e->getMessage();
            return [];
        }
    }
}
