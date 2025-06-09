<?php
class DashboardController {
    private $productoModel;

    public function __construct() {
        // Carga del modelo (versión original)
        require_once ROOT_PATH . 'models/ProductoModel.php';
        $this->productoModel = new ProductoModel();
    }

    public function index() {
        // Verificar sesión (versión original mejorada)
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login');
            return; // Para asegurar que no continúe la ejecución
        }

        try {
            // Obtener datos para el dashboard (versión original)
            $totalProductos = $this->productoModel->contarProductos();
            
            // Incluir la vista con datos organizados
            $data = [
                'totalProductos' => $totalProductos,
                'userNombre' => $_SESSION['user_nombre'] ?? 'Usuario'
            ];
            
            $this->loadView('dashboard', $data);
            
        } catch (Exception $e) {
            error_log('Error en Dashboard: ' . $e->getMessage());
            $this->redirect('dashboard');
        }
    }

    // Métodos auxiliares (nuevos pero simples)
    private function redirect($action) {
        header("Location: " . BASE_URL . "index.php?action=$action");
        exit();
    }

    private function loadView($view, $data = []) {
        extract($data);
        require ROOT_PATH . 'views/layouts/header.php';
        require ROOT_PATH . 'views/' . $view . '.php';
        require ROOT_PATH . 'views/layouts/footer.php';
    }
}
?>