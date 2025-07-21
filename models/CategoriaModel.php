<?php
class CategoriaModel
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

    public function obtenerTodasCategorias($pagina = 1, $por_pagina = 10, $filtros = [])
    {
        // Preparar la consulta base
        $whereConditions = [];
        $params = [];

        // Aplicar filtros
        if (!empty($filtros['busqueda'])) {
            $whereConditions[] = "(LOWER(CONVERT(c.nombre USING utf8)) LIKE :busqueda OR 
                                  LOWER(CONVERT(t.nombre USING utf8)) LIKE :busqueda)";
            $params[':busqueda'] = "%{$filtros['busqueda']}%";
        }

        if (!empty($filtros['tipo'])) {
            $whereConditions[] = "LOWER(CONVERT(t.nombre USING utf8)) = :tipo";
            $params[':tipo'] = $filtros['tipo'];
        }

        if (!empty($filtros['estatus'])) {
            $whereConditions[] = "LOWER(CONVERT(e.nombre USING utf8)) = :estatus";
            $params[':estatus'] = $filtros['estatus'];
        }

        // Construir la cláusula WHERE
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }

        // Calcular el offset
        $offset = ($pagina - 1) * $por_pagina;

        // Consulta para obtener el total de registros con filtros
        $queryTotal = "SELECT COUNT(*) as total 
                       FROM categorias c
                       JOIN tipos_categoria t ON c.id_tipo_categoria = t.id
                       JOIN estatus e ON c.id_estatus = e.id
                       $whereClause";
        $stmtTotal = $this->db->prepare($queryTotal);
        foreach ($params as $key => $value) {
            $stmtTotal->bindValue($key, $value);
        }
        $stmtTotal->execute();
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

        // Consulta principal con paginación y filtros
        $query = "SELECT c.*, t.nombre as tipo_categoria, e.nombre as estatus 
                  FROM categorias c
                  JOIN tipos_categoria t ON c.id_tipo_categoria = t.id
                  JOIN estatus e ON c.id_estatus = e.id
                  $whereClause
                  ORDER BY c.id DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'categorias' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total_registros' => $total,
            'total_paginas' => ceil($total / $por_pagina),
            'pagina_actual' => $pagina
        ];
    }

    public function obtenerCategoriaPorId($id)
    {
        $query = "SELECT * FROM categorias WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crearCategoria($nombre, $id_tipo_categoria, $id_estatus)
    {
        try {
            $query = "INSERT INTO categorias (nombre, id_tipo_categoria, id_estatus) 
                      VALUES (:nombre, :id_tipo_categoria, :id_estatus)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":id_tipo_categoria", $id_tipo_categoria);
            $stmt->bindParam(":id_estatus", $id_estatus);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->errors[] = "Error de base de datos: " . $e->getMessage();
            return false;
        }
    }

    public function actualizarCategoria($id, $nombre, $id_tipo_categoria, $id_estatus)
    {
        try {
            $query = "UPDATE categorias SET 
                      nombre = :nombre, 
                      id_tipo_categoria = :id_tipo_categoria,
                      id_estatus = :id_estatus
                      WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":id_tipo_categoria", $id_tipo_categoria);
            $stmt->bindParam(":id_estatus", $id_estatus);
            $stmt->bindParam(":id", $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    public function cambiarEstado($id, $nuevoEstado)
    {
        try {
            $query = "UPDATE categorias SET id_estatus = :estatus WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":estatus", $nuevoEstado);
            $stmt->bindParam(":id", $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    public function obtenerTiposCategoria()
    {
        $query = "SELECT * FROM tipos_categoria WHERE id_estatus = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeCategoria($nombre, $id = null)
    {
        try {
            $query = "SELECT COUNT(*) as count FROM categorias WHERE nombre = :nombre";
            if ($id !== null) {
                $query .= " AND id != :id";
            }

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":nombre", $nombre);
            if ($id !== null) {
                $stmt->bindParam(":id", $id);
            }
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['count'] > 0;
        } catch (PDOException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    public function obtenerCategoriasActivas()
    {
        $query = "SELECT c.*, t.nombre as tipo_categoria 
                  FROM categorias c
                  JOIN tipos_categoria t ON c.id_tipo_categoria = t.id
                  WHERE c.id_estatus = 1
                  ORDER BY c.nombre";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
