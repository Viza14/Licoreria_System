<?php
class UsuarioModel
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

    public function login($user, $password)
    {
        $query = "SELECT * FROM usuarios WHERE user = :user AND id_estatus = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":user", $user);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password'])) {
            return $usuario;
        }

        return false;
    }

    // En UsuarioModel.php
    public function existeUsuario($cedula, $telefono, $user, $excluirId = null)
    {
        $query = "SELECT COUNT(*) as count FROM usuarios WHERE (cedula = :cedula OR telefono = :telefono OR user = :user)";
        if ($excluirId) {
            $query .= " AND id != :excluirId";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":cedula", $cedula);
        $stmt->bindParam(":telefono", $telefono);
        $stmt->bindParam(":user", $user);
        if ($excluirId) {
            $stmt->bindParam(":excluirId", $excluirId);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    public function obtenerSimbolosCedula()
    {
        $query = "SELECT * FROM simbolos_cedula";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarUltimoLogin($id_usuario)
    {
        $query = "UPDATE usuarios SET ultimo_inicio_sesion = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id_usuario);
        return $stmt->execute();
    }

    // En UsuarioModel.php
    public function obtenerTodosUsuarios($pagina = 1, $por_pagina = 10)
    {
        // Calcular el offset
        $offset = ($pagina - 1) * $por_pagina;

        // Obtener el total de registros
        $queryTotal = "SELECT COUNT(*) as total FROM usuarios";
        $stmtTotal = $this->db->prepare($queryTotal);
        $stmtTotal->execute();
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

        // Calcular el total de páginas
        $total_paginas = ceil($total / $por_pagina);

        // Consulta principal con paginación
        $query = "SELECT u.*, r.nombre as rol, e.nombre as estatus, sc.nombre as nombre_simbolo 
              FROM usuarios u
              JOIN roles r ON u.id_rol = r.id
              JOIN estatus e ON u.id_estatus = e.id
              JOIN simbolos_cedula sc ON u.id_simbolo_cedula = sc.id
              LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'usuarios' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total_registros' => $total,
            'total_paginas' => $total_paginas,
            'pagina_actual' => $pagina
        ];
    }

    public function obtenerUsuarioPorId($id)
    {
        $query = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crearUsuario($data)
    {
        try {
            // Agrega id_simbolo_cedula a la consulta
            $query = "INSERT INTO usuarios (cedula, id_simbolo_cedula, nombres, apellidos, telefono, direccion, user, password, id_rol, id_estatus) 
                  VALUES (:cedula, :id_simbolo_cedula, :nombres, :apellidos, :telefono, :direccion, :user, :password, :id_rol, :id_estatus)";
            $stmt = $this->db->prepare($query);

            return $stmt->execute($data);
        } catch (PDOException $e) {
            $this->errors[] = "Error de base de datos: " . $e->getMessage();
            return false;
        }
    }

    public function actualizarUsuario($id, $data)
    {
        try {
            // Construir la consulta dinámicamente
            $query = "UPDATE usuarios SET 
                  cedula = :cedula, 
                  id_simbolo_cedula = :id_simbolo_cedula,
                  nombres = :nombres, 
                  apellidos = :apellidos, 
                  telefono = :telefono, 
                  direccion = :direccion, 
                  user = :user, 
                  id_rol = :id_rol, 
                  id_estatus = :id_estatus";

            if (isset($data['password'])) {
                $query .= ", password = :password";
            }

            $query .= " WHERE id = :id";

            $stmt = $this->db->prepare($query);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    public function cambiarEstado($id, $nuevoEstado)
    {
        try {
            $query = "UPDATE usuarios SET id_estatus = :estatus WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":estatus", $nuevoEstado);
            $stmt->bindParam(":id", $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    public function obtenerRoles()
    {
        $query = "SELECT * FROM roles";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function validarCedula($simboloId, $cedula)
    {
        $simbolos = $this->obtenerSimbolosCedula();
        $simboloNombre = '';

        foreach ($simbolos as $simbolo) {
            if ($simbolo['id'] == $simboloId) {
                $simboloNombre = $simbolo['nombre'];
                break;
            }
        }

        if (strpos($simboloNombre, 'V-') !== false) {
            return (strlen($cedula) >= 8 && ctype_digit($cedula));
        } elseif (strpos($simboloNombre, 'J-') !== false) {
            return (in_array(strlen($cedula), [8, 9]) && ctype_digit($cedula));
        }

        return false;
    }
}
