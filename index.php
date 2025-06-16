<?php
// 1. Definir constantes básicas
define('BASE_URL', 'http://localhost/licoreria/');
define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);

// 2. Cargar archivos esenciales
require_once ROOT_PATH . 'config/database.php';
require_once ROOT_PATH . 'models/UsuarioModel.php';
require_once ROOT_PATH . 'models/ProductoModel.php';
require_once ROOT_PATH . 'models/ProveedorModel.php';
require_once ROOT_PATH . 'models/StockLimiteModel.php';
require_once ROOT_PATH . 'controllers/AuthController.php';
require_once ROOT_PATH . 'controllers/DashboardController.php';
require_once ROOT_PATH . 'controllers/UsuarioController.php';
require_once ROOT_PATH . 'controllers/ProveedorController.php';
require_once ROOT_PATH . 'controllers/ProductoProveedorController.php';
require_once ROOT_PATH . 'controllers/ClienteController.php';
require_once ROOT_PATH . 'controllers/ProductoController.php';
require_once ROOT_PATH . 'controllers/MovimientoInventarioController.php';
require_once ROOT_PATH . 'controllers/CategoriaController.php';
require_once ROOT_PATH . 'controllers/TipoCategoriaController.php';
require_once ROOT_PATH . 'controllers/StockLimiteController.php';
// Cargar helpers
require_once ROOT_PATH . 'helpers/notifications.php';

// 3. Iniciar sesión con configuración segura
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400,
        'cookie_secure' => false,
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
$_SESSION['last_activity'] = time();

// 5. Cabeceras para evitar caché en páginas sensibles
if (!isset($_GET['action']) || $_GET['action'] != 'login') {
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
}

// 6. Manejo de rutas
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

        case 'proveedores':
            if (!isset($_SESSION['user_id'])) {
                header("Location: " . BASE_URL . "index.php?action=login");
                exit();
            }
            $controller = new ProveedorController();
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

        case 'producto-proveedor':
            if (!isset($_SESSION['user_id'])) {
                header("Location: " . BASE_URL . "index.php?action=login");
                exit();
            }

            error_log("Enrutador: Acción producto-proveedor detectada");

            $controller = new ProductoProveedorController();
            $method = $_GET['method'] ?? 'index';

            error_log("Método a ejecutar: " . $method);

            if (method_exists($controller, $method)) {
                if ($method === 'crear' || $method === 'guardar') {
                    $controller->$method();
                } elseif (in_array($method, ['editar', 'actualizar', 'mostrar', 'cambiarEstado'])) {
                    $id = $_GET['id'] ?? 0;
                    $controller->$method($id);
                } else {
                    $controller->index();
                }
            } else {
                $controller->index();
            }
            break;

        case 'clientes':
            $controller = new ClienteController();
            $method = $_GET['method'] ?? 'index';
            $param = $_GET['cedula'] ?? null;
            if ($method === 'crear' || $method === 'guardar') {
                $controller->$method();
            } elseif ($method === 'editar' || $method === 'actualizar' || $method === 'mostrar' || $method === 'cambiarEstado') {
                $controller->$method($param);
            } else {
                $controller->index();
            }
            break;

        case 'productos':
            $controller = new ProductoController();

            $method = $_GET['method'] ?? 'index';
            $id = $_GET['id'] ?? null;

            if ($method === 'crear') {
                $controller->crear();
            } elseif ($method === 'guardar') {
                $controller->guardar();
            } elseif ($method === 'editar' && $id) {
                $controller->editar($id);
            } elseif ($method === 'actualizar' && $id) {
                $controller->actualizar($id);
            } elseif ($method === 'mostrar' && $id) {
                $controller->mostrar($id);
            } elseif ($method === 'cambiarEstado' && $id) {
                $controller->cambiarEstado($id);
            } elseif ($method === 'registrarEntrada') {
                $controller->registrarEntrada();
            } else {
                $controller->index();
            }
            break;

        case 'movimientos-inventario':
            if (!isset($_SESSION['user_id'])) {
                header("Location: " . BASE_URL . "index.php?action=login");
                exit();
            }
            $controller = new MovimientoInventarioController();
            $method = $_GET['method'] ?? 'index';
            $id = $_GET['id'] ?? null;

            if ($method === 'mostrar' && $id) {
                $controller->mostrar($id);
            } elseif ($method === 'porProducto' && $id) {
                $controller->porProducto($id);
            } elseif ($method === 'resumen') {
                $controller->resumen();
            } else {
                $controller->index();
            }
            break;

        case 'categorias':
            try {
                $controller = new CategoriaController();
                $method = $_GET['method'] ?? 'index';
                $id = $_GET['id'] ?? null;

                // Métodos sin parámetro
                if (in_array($method, ['index', 'crear', 'guardar'])) {
                    $controller->{$method}();
                }
                // Métodos con parámetro ID
                elseif (in_array($method, ['editar', 'actualizar', 'mostrar', 'cambiarEstado']) && $id) {
                    $controller->{$method}($id);
                }
                // Método no reconocido
                else {
                    throw new Exception("Método no válido");
                }
            } catch (Exception $e) {
                error_log("Error en categorías: " . $e->getMessage());
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Ocurrió un error al procesar la solicitud',
                    'icon' => 'error'
                ];
                header("Location: " . BASE_URL . "index.php?action=categorias");
                exit();
            }
            break;

        case 'tipos-categoria':
            if (!isset($_SESSION['user_id'])) {
                header("Location: " . BASE_URL . "index.php?action=login");
                exit();
            }
            $controller = new TipoCategoriaController();
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

        case 'gestion-stock':
            if (!isset($_SESSION['user_id'])) {
                header("Location: " . BASE_URL . "index.php?action=login");
                exit();
            }
            $controller = new StockLimiteController();
            $method = $_GET['method'] ?? 'index';
            $id = $_GET['id'] ?? null;

            if ($method === 'editar' && $id) {
                $controller->editar($id);
            } elseif ($method === 'guardar') {
                $controller->guardar();
            } elseif ($method === 'alertas') {
                $controller->alertas();
            } else {
                $controller->index();
            }
            break;

        default:
            header("HTTP/1.0 404 Not Found");
            include ROOT_PATH . 'views/errors/404.php';
            exit();
    }
} catch (Exception $e) {
    error_log('Error en la aplicación: ' . $e->getMessage());
    header("Location: " . BASE_URL . "index.php?action=login");
    exit();
}
