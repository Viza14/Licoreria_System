<?php
class ProductoProveedorController
{
    private $model;

    public function __construct()
    {
        require_once ROOT_PATH . 'models/ProductoProveedorModel.php';
        $this->model = new ProductoProveedorModel();
    }

    private function normalizarTexto($texto)
    {
        if (!$texto) return null;
        return strtolower(trim(str_replace(['á','é','í','ó','ú','ñ','ü','Á','É','Í','Ó','Ú','Ñ','Ü'], 
                                         ['a','e','i','o','u','n','u','A','E','I','O','U','N','U'], 
                                         $texto)));
    }

    public function index()
    {
        $this->checkSession();
        
        // Obtener parámetros de paginación y filtros
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $por_pagina = 10;
        
        // Preparar filtros normalizados
        $filtros = [
            'busqueda' => isset($_GET['busqueda']) ? $this->normalizarTexto($_GET['busqueda']) : null,
            'estatus' => isset($_GET['estatus']) ? $this->normalizarTexto($_GET['estatus']) : null
        ];
        
        $resultado = $this->model->obtenerTodasRelaciones($pagina, $por_pagina, $filtros);
        
        // Si es una solicitud AJAX, devolver JSON
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
            header('Content-Type: application/json');
            echo json_encode([
                'relaciones' => $resultado['datos'],
                'pagina_actual' => $resultado['pagina_actual'],
                'total_paginas' => $resultado['total_paginas'],
                'total_registros' => $resultado['total_registros']
            ]);
            exit;
        }
        
        // Si no es AJAX, cargar la vista normal
        $estatus = $this->model->obtenerEstatus();
        $this->loadView('producto_proveedor/index', [
            'relaciones' => $resultado['datos'],
            'estatus' => $estatus,
            'pagina_actual' => $resultado['pagina_actual'],
            'total_paginas' => $resultado['total_paginas'],
            'total_registros' => $resultado['total_registros']
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
