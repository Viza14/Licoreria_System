<?php
// 1. Definir constantes básicas (sin cambios)
define('BASE_URL', 'http://localhost/licoreria/');
define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);

// 2. Cargar archivos esenciales (versión original)
require_once ROOT_PATH . 'config/database.php';
require_once ROOT_PATH . 'models/UsuarioModel.php';
require_once ROOT_PATH . 'models/ProductoModel.php';
require_once ROOT_PATH . 'controllers/AuthController.php';
require_once ROOT_PATH . 'controllers/DashboardController.php';
require_once ROOT_PATH . 'controllers/UsuarioController.php';

// 3. Iniciar sesión con configuración segura
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400, // 1 día
        'cookie_secure' => false, // Cambiar a true en producción con HTTPS
        'cookie_httponly' => true,
        'use_strict_mode' => true
    ]);
}

// 4. Verificación de inactividad (30 minutos)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: " . BASE_URL . "index.php?action=login");
    exit();
}
$_SESSION['last_activity'] = time(); // Actualizar tiempo de actividad

// 5. Cabeceras para evitar caché en páginas sensibles
if (!isset($_GET['action']) || $_GET['action'] != 'login') {
    header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
    header("Pragma: no-cache"); // HTTP 1.0.
    header("Expires: 0"); // Proxies.
}

// 6. Manejo de rutas (versión original mejorada)
$action = $_GET['action'] ?? 'login';

try {
    switch ($action) {
        case 'login':
            $controller = new AuthController();
            $controller->login();
            break;

        case 'logout':
            $controller = new AuthController();
            $controller->logout();
            break;

        case 'dashboard':
            // Verificación de sesión más estricta
            if (!isset($_SESSION['user_id']) || !isset($_SESSION['last_activity'])) {
                header("Location: " . BASE_URL . "index.php?action=login");
                exit();
            }
            $controller = new DashboardController();
            $controller->index();
            break;

        case 'usuarios':
            if (!isset($_SESSION['user_id'])) {
                header("Location: " . BASE_URL . "index.php?action=login");
                exit();
            }
            $controller = new UsuarioController();
            $method = $_GET['method'] ?? 'index';

            if ($method === 'crear') {
                $controller->crear();
            } elseif ($method === 'guardar') {
                $controller->guardar();
            } elseif ($method === 'editar') {
                $id = $_GET['id'] ?? 0;
                $controller->editar($id);
            } elseif ($method === 'actualizar') {
                $id = $_GET['id'] ?? 0;
                $controller->actualizar($id);
            } elseif ($method === 'mostrar') {
                $id = $_GET['id'] ?? 0;
                $controller->mostrar($id);
            } elseif ($method === 'cambiarEstado') {
                $id = $_GET['id'] ?? 0;
                $controller->cambiarEstado($id);
            } else {
                $controller->index();
            }
            break;
        default:
            // Página de error 404 más amigable
            header("HTTP/1.0 404 Not Found");
            include ROOT_PATH . 'views/errors/404.php';
            exit();
    }
} catch (Exception $e) {
    // Manejo de error básico pero más limpio
    error_log('Error en la aplicación: ' . $e->getMessage());
    header("Location: " . BASE_URL . "index.php?action=login");
    exit();
}
