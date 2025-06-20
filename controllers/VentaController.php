<?php
class VentaController
{
    private $model;
    private $productoModel;
    private $clienteModel;
    
    public function __construct()
    {
        require_once ROOT_PATH . 'models/VentaModel.php';
        require_once ROOT_PATH . 'models/ProductoModel.php';
        require_once ROOT_PATH . 'models/ClienteModel.php';
        $this->model = new VentaModel();
        $this->productoModel = new ProductoModel();
        $this->clienteModel = new ClienteModel();
    }

    public function index()
    {
        $this->checkSession();
        $ventas = $this->model->obtenerTodasVentas();
        $this->loadView('ventas/index', ['ventas' => $ventas]);
    }

    public function crear()
    {
        $this->checkSession();
        $productos = $this->productoModel->obtenerProductosActivos();
        $clientes = $this->clienteModel->obtenerClientesActivos();
        
        $this->loadView('ventas/crear', [
            'productos' => $productos,
            'clientes' => $clientes
        ]);
    }

    public function guardar()
    {
        $this->checkSession();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'cedula_cliente' => $_POST['cedula_cliente'],
                'id_usuario' => $_SESSION['user_id'],
                'productos' => $_POST['productos']
            ];

            // Validación básica
            if (empty($data['cedula_cliente'])) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Debe seleccionar un cliente',
                    'icon' => 'error'
                ];
                $this->redirect('ventas&method=crear');
                return;
            }

            if (empty($data['productos']) || !is_array($data['productos'])) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Debe agregar al menos un producto',
                    'icon' => 'error'
                ];
                $this->redirect('ventas&method=crear');
                return;
            }

            // Validar stock de productos
            foreach ($data['productos'] as $producto) {
                $stock = $this->productoModel->obtenerStockProducto($producto['id']);
                if ($stock < $producto['cantidad']) {
                    $_SESSION['error'] = [
                        'title' => 'Error',
                        'text' => 'No hay suficiente stock para el producto: ' . $producto['descripcion'],
                        'icon' => 'error'
                    ];
                    $this->redirect('ventas&method=crear');
                    return;
                }
            }

            // Registrar la venta
            if ($this->model->registrarVenta($data)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Venta registrada correctamente',
                    'icon' => 'success'
                ];
                $this->redirect('ventas');
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al registrar la venta: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
                $this->redirect('ventas&method=crear');
            }
        }
    }

    public function mostrar($id)
    {
        $this->checkSession();
        $venta = $this->model->obtenerVentaPorId($id);
        
        if (!$venta) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Venta no encontrada',
                'icon' => 'error'
            ];
            $this->redirect('ventas');
            return;
        }

        $detalles = $this->model->obtenerDetallesVenta($id);
        $this->loadView('ventas/mostrar', [
            'venta' => $venta,
            'detalles' => $detalles
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
        require ROOT_PATH . "views/$view.php";
        require ROOT_PATH . 'views/layouts/footer.php';
    }
}