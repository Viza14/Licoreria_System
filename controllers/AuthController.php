<?php
class AuthController
{
    private $usuarioModel;

    public function __construct()
    {
        require_once ROOT_PATH . 'models/UsuarioModel.php';
        $this->usuarioModel = new UsuarioModel();
    }

    public function login()
    {
        // Verificar si ya está logueado y redirigir
        if (isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "index.php?action=dashboard");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $_POST['user'] ?? '';
            $password = $_POST['password'] ?? '';

            $usuario = $this->usuarioModel->login($user, $password);

            if ($usuario) {
                // Regenerar el ID de sesión para prevenir fixation
                session_regenerate_id(true);
                
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_nombre'] = $usuario['nombres'] . ' ' . $usuario['apellidos'];
                $_SESSION['user_rol'] = $usuario['id_rol'];
                $_SESSION['last_activity'] = time(); // Marcar tiempo de última actividad

                $this->usuarioModel->actualizarUltimoLogin($usuario['id']);

                header("Location: " . BASE_URL . "index.php?action=dashboard");
                exit();
            } else {
                $_SESSION['error'] = "Usuario o contraseña incorrectos";
                header("Location: " . BASE_URL . "index.php?action=login");
                exit();
            }
        }

        require ROOT_PATH . 'views/auth/login.php';
    }

    public function logout()
    {
        // Limpiar todas las variables de sesión
        $_SESSION = array();
        
        // Borrar la cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destruir la sesión
        session_destroy();
        header("Location: " . BASE_URL);
        exit();
    }
}
