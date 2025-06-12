<?php
class MovimientoInventarioController
{
    private $model;
    private $productoModel;

    public function __construct()
    {
        require_once ROOT_PATH . 'models/MovimientoInventarioModel.php';
        require_once ROOT_PATH . 'models/ProductoModel.php';
        $this->model = new MovimientoInventarioModel();
        $this->productoModel = new ProductoModel();
    }

    public function index()
    {
        $this->checkSession();
        $movimientos = $this->model->obtenerTodosMovimientos();
        $this->loadView('movimientos_inventario/index', ['movimientos' => $movimientos]);
    }

    public function mostrar($id)
    {
        $this->checkSession();
        $movimiento = $this->model->obtenerMovimientoPorId($id);
        
        if (!$movimiento) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Movimiento no encontrado',
                'icon' => 'error'
            ];
            $this->redirect('movimientos-inventario');
        }
        
        $this->loadView('movimientos_inventario/mostrar', ['movimiento' => $movimiento]);
    }

    public function porProducto($idProducto)
    {
        $this->checkSession();
        $producto = $this->productoModel->obtenerProductoPorId($idProducto);
        $movimientos = $this->model->obtenerMovimientosPorProducto($idProducto);
        
        $this->loadView('movimientos_inventario/por_producto', [
            'producto' => $producto,
            'movimientos' => $movimientos
        ]);
    }

    public function resumen()
    {
        $this->checkSession();
        
        $filtros = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $filtros = [
                'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
                'fecha_fin' => $_POST['fecha_fin'] ?? null,
                'id_producto' => $_POST['id_producto'] ?? null
            ];
        }
        
        $resumen = $this->model->obtenerResumenMovimientos($filtros);
        $productos = $this->productoModel->obtenerTodosProductos();
        
        $this->loadView('movimientos_inventario/resumen', [
            'resumen' => $resumen,
            'productos' => $productos,
            'filtros' => $filtros
        ]);
    }

    // Métodos auxiliares
    private function checkSession()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = [
                'title' => 'Acceso denegado',
                'text' => 'Debe iniciar sesión para acceder a esta página',
                'icon' => 'error'
            ];
            $this->redirect('login');
        }
    }

    private function redirect($action)
    {
        header("Location: " . BASE_URL . "index.php?action=$action");
        exit();
    }

    private function loadView($view, $data = [])
    {
        extract($data);
        require ROOT_PATH . 'views/layouts/header.php';
        require ROOT_PATH . 'views/layouts/sidebar.php';
        require ROOT_PATH . 'views/' . $view . '.php';
        require ROOT_PATH . 'views/layouts/footer.php';
    }
}