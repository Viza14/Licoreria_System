<?php
class ProductoProveedorController
{
    private $model;

    public function __construct()
    {
        require_once ROOT_PATH . 'models/ProductoProveedorModel.php';
        $this->model = new ProductoProveedorModel();
    }

    public function index()
    {
        $this->checkSession();
        $relaciones = $this->model->obtenerTodasRelaciones();
        $estatus = $this->model->obtenerEstatus();
        $this->loadView('producto_proveedor/index', [
            'relaciones' => $relaciones,
            'estatus' => $estatus
        ]);
    }

    public function crear()
    {
        $this->checkSession();
        $productos = $this->model->obtenerProductos();
        $proveedores = $this->model->obtenerProveedores();
        $this->loadView('producto_proveedor/crear', [
            'productos' => $productos,
            'proveedores' => $proveedores
        ]);
    }

    public function guardar()
    {
        $this->checkSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'cedula_proveedor' => $_POST['cedula_proveedor'],
                'id_producto' => $_POST['id_producto'],
                'precio_compra' => $_POST['precio_compra']
            ];

            if ($this->model->crearRelacion($data)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Relación creada exitosamente',
                    'icon' => 'success'
                ];
                $this->redirect('producto-proveedor');
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al crear la relación: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('producto-proveedor&method=crear');
            }
        }
    }

    public function editar($id)
    {
        $this->checkSession();
        $relacion = $this->model->obtenerRelacionPorId($id);
        if (!$relacion) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Relación no encontrada',
                'icon' => 'error'
            ];
            $this->redirect('producto-proveedor');
        }

        $productos = $this->model->obtenerProductos();
        $proveedores = $this->model->obtenerProveedores();
        $this->loadView('producto_proveedor/editar', [
            'relacion' => $relacion,
            'productos' => $productos,
            'proveedores' => $proveedores
        ]);
    }

    public function actualizar($id)
    {
        $this->checkSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'precio_compra' => $_POST['precio_compra'],
                'id_producto' => $_POST['id_producto']
            ];

            if ($this->model->actualizarRelacion($id, $data)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Relación actualizada exitosamente',
                    'icon' => 'success'
                ];
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al actualizar la relación: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
            }
            $this->redirect('producto-proveedor');
        }
    }

    public function mostrar($id)
    {
        $this->checkSession();
        $relacion = $this->model->obtenerRelacionPorId($id);
        if (!$relacion) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Relación no encontrada',
                'icon' => 'error'
            ];
            $this->redirect('producto-proveedor');
        }
        $this->loadView('producto_proveedor/mostrar', ['relacion' => $relacion]);
    }
    public function cambiarEstado($id)
    {
        $this->checkSession();

        $relacion = $this->model->obtenerRelacionPorId($id);
        if (!$relacion) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Relación no encontrada',
                'icon' => 'error'
            ];
            $this->redirect('producto-proveedor');
        }

        $nuevoEstado = $relacion['id_estatus'] == 1 ? 2 : 1;
        $estadoTexto = $nuevoEstado == 1 ? 'activada' : 'desactivada';

        if ($this->model->cambiarEstado($id, $nuevoEstado)) {
            $_SESSION['mensaje'] = [
                'title' => 'Éxito',
                'text' => "Relación $estadoTexto correctamente",
                'icon' => 'success'
            ];
        } else {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Error al cambiar el estado: ' . implode(', ', $this->model->getErrors()),
                'icon' => 'error'
            ];
        }

        $this->redirect('producto-proveedor');
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
