<?php
class TipoCategoriaModel
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

    public function obtenerTodosTiposCategoria($pagina = 1, $por_pagina = 10, $filtros = [])
    {
        try {
            // Construir la consulta base
            $whereConditions = [];
            $params = [];

            // Aplicar filtros
            if (!empty($filtros['busqueda'])) {
                $whereConditions[] = "LOWER(CONVERT(t.nombre USING utf8)) LIKE :busqueda";
                $params[':busqueda'] = "%{$filtros['busqueda']}%";
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
                          FROM tipos_categoria t
                          JOIN estatus e ON t.id_estatus = e.id
                          $whereClause";
            $stmtTotal = $this->db->prepare($queryTotal);
            foreach ($params as $param => $value) {
                $stmtTotal->bindValue($param, $value);
            }
            $stmtTotal->execute();
            $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

            // Consulta principal con paginación y filtros
            $query = "SELECT t.*, e.nombre as estatus 
                      FROM tipos_categoria t
                      JOIN estatus e ON t.id_estatus = e.id
                      $whereClause
                      LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($query);

            // Vincular parámetros de filtros
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }

            // Vincular parámetros de paginación
            $stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return [
                'tipos' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'total_registros' => $total,
                'total_paginas' => ceil($total / $por_pagina),
                'pagina_actual' => $pagina
            ];
        } catch (PDOException $e) {
            $this->errors[] = "Error de base de datos: " . $e->getMessage();
            return [
                'tipos' => [],
                'total_registros' => 0,
                'total_paginas' => 0,
                'pagina_actual' => $pagina
            ];
        }
    }

    public function obtenerTipoCategoriaPorId($id)
    {
        $query = "SELECT t.*, e.nombre as estatus 
                 FROM tipos_categoria t
                 JOIN estatus e ON t.id_estatus = e.id
                 WHERE t.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crearTipoCategoria($nombre, $id_estatus)
    {
        try {
            $query = "INSERT INTO tipos_categoria (nombre, id_estatus) 
                      VALUES (:nombre, :id_estatus)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":id_estatus", $id_estatus);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->errors[] = "Error de base de datos: " . $e->getMessage();
            return false;
        }
    }

    public function actualizarTipoCategoria($id, $nombre, $id_estatus)
    {
        try {
            $query = "UPDATE tipos_categoria SET 
                      nombre = :nombre, 
                      id_estatus = :id_estatus
                      WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":nombre", $nombre);
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
            $query = "UPDATE tipos_categoria SET id_estatus = :estatus WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":estatus", $nuevoEstado);
            $stmt->bindParam(":id", $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    public function existeTipoCategoria($nombre, $id = null)
    {
        try {
            $query = "SELECT COUNT(*) as count FROM tipos_categoria WHERE nombre = :nombre";
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
}
