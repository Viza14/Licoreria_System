<?php
class StockLimiteController
{
    private $model;
    private $productoModel;

    public function __construct()
    {
        require_once ROOT_PATH . 'models/StockLimiteModel.php';
        require_once ROOT_PATH . 'models/ProductoModel.php';
        $this->model = new StockLimiteModel();
        $this->productoModel = new ProductoModel();
    }

    public function index()
    {
        $this->checkSession();
        
        // Obtener parámetros de paginación
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $por_pagina = 10;

        // Obtener datos paginados
        $datos = $this->model->obtenerTodosLimites($pagina, $por_pagina);
        $productosSinLimite = $this->model->obtenerProductosSinLimite();
        
        $this->loadView('stock_limite/index', [
            'limites' => $datos['limites'],
            'productosSinLimite' => $productosSinLimite,
            'pagina_actual' => $datos['pagina_actual'],
            'total_paginas' => $datos['total_paginas'],
            'total_registros' => $datos['total_registros']
        ]);
    }

    public function editar($idProducto)
    {
        $this->checkSession();
        $limite = $this->model->obtenerLimitesPorProducto($idProducto);
        $producto = $this->productoModel->obtenerProductoPorId($idProducto);
        
        if (!$producto) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Producto no encontrado',
                'icon' => 'error'
            ];
            $this->redirect('gestion-stock');
        }
        
        $this->loadView('stock_limite/editar', [
            'limite' => $limite,
            'producto' => $producto
        ]);
    }

    public function guardar()
    {
        $this->checkSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id_producto' => (int)$_POST['id_producto'],
                'stock_minimo' => (int)$_POST['stock_minimo'],
                'stock_maximo' => (int)$_POST['stock_maximo'],
                'id_usuario' => $_SESSION['user_id']
            ];

            // Validaciones
            if ($data['stock_minimo'] < 0 || $data['stock_maximo'] < 0) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Los valores de stock no pueden ser negativos',
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('gestion-stock&method=editar&id=' . $data['id_producto']);
                return;
            }

            if ($data['stock_maximo'] <= $data['stock_minimo']) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'El stock máximo debe ser mayor al stock mínimo',
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('gestion-stock&method=editar&id=' . $data['id_producto']);
                return;
            }

            if ($this->model->guardarLimites($data)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Límites de stock guardados correctamente',
                    'icon' => 'success'
                ];
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al guardar los límites: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
            }
            $this->redirect('gestion-stock');
        }
    }

    public function alertas()
    {
        $this->checkSession();
        $stockBajo = $this->model->obtenerProductosConStockBajo();
        $stockAlto = $this->model->obtenerProductosConStockAlto();
        
        $this->loadView('stock_limite/alertas', [
            'stockBajo' => $stockBajo,
            'stockAlto' => $stockAlto
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