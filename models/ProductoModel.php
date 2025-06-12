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

    public function obtenerTodosProductos($soloActivos = true)
    {
        $query = "SELECT p.*, c.nombre as categoria, e.nombre as estatus, tc.nombre as tipo_categoria
              FROM producto p
              JOIN categorias c ON p.id_categoria = c.id
              JOIN estatus e ON p.id_estatus = e.id
              JOIN tipos_categoria tc ON c.id_tipo_categoria = tc.id";

        if ($soloActivos) {
            $query .= " WHERE p.id_estatus = 1";
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProductosActivos()
    {
        return $this->obtenerTodosProductos(true);
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

            // 2. Registrar la entrada en movimientos_inventario
            $query = "INSERT INTO movimientos_inventario 
                 (id_producto, tipo_movimiento, cantidad, precio_unitario, id_usuario, observaciones) 
                 VALUES (:id_producto, 'ENTRADA', :cantidad, :precio_compra, :id_usuario, :observaciones)";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id_producto", $id_producto);
            $stmt->bindParam(":cantidad", $cantidad);
            $stmt->bindParam(":precio_compra", $precio_compra);
            $stmt->bindParam(":id_usuario", $id_usuario);
            $stmt->bindParam(":observaciones", $observaciones);
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
}
