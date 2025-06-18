<?php
class ProductoController
{
    private $model;
    private $proveedorModel;
    public function __construct()
    {
        require_once ROOT_PATH . 'models/ProductoModel.php';
        require_once ROOT_PATH . 'models/ProveedorModel.php';
        $this->model = new ProductoModel();
        $this->proveedorModel = new ProveedorModel();
    }
    public function index()
    {
        $this->checkSession();
        // Obtener TODOS los productos (activos e inactivos) para la vista principal
        $productos = $this->model->obtenerTodosProductos(false); // false = mostrar todos
        $categorias = $this->model->obtenerCategorias();
        $this->loadView('productos/index', [
            'productos' => $productos,
            'categorias' => $categorias
        ]);
    }
    public function crear()
    {
        $this->checkSession();
        $categorias = $this->model->obtenerCategorias();
        $tiposCategoria = $this->model->obtenerTiposCategoria();
        $estatus = $this->model->obtenerEstatus();
        $this->loadView('productos/crear', [
            'categorias' => $categorias,
            'tiposCategoria' => $tiposCategoria,
            'estatus' => $estatus
        ]);
    }
    public function guardar()
    {
        $this->checkSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'descripcion' => trim($_POST['descripcion']),
                'cantidad' => (int)$_POST['cantidad'],
                'precio' => (float)$_POST['precio'],
                'id_categoria' => (int)$_POST['id_categoria'],
                'id_estatus' => (int)$_POST['id_estatus']
            ];
            // Validaciones
            if (empty($data['descripcion'])) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'La descripción del producto es requerida',
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('productos&method=crear');
                return;
            }
            if ($this->model->existeProducto($data['descripcion'], $data['id_categoria'])) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Ya existe un producto con esta descripción en la categoría seleccionada',
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('productos&method=crear');
                return;
            }
            if ($this->model->crearProducto($data)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Producto creado exitosamente',
                    'icon' => 'success'
                ];
                $this->redirect('productos');
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al crear el producto: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('productos&method=crear');
            }
        }
    }
    public function editar($id)
    {
        $this->checkSession();
        $producto = $this->model->obtenerProductoPorId($id);
        if (!$producto) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Producto no encontrado',
                'icon' => 'error'
            ];
            $this->redirect('productos');
        }
        $categorias = $this->model->obtenerCategorias();
        $tiposCategoria = $this->model->obtenerTiposCategoria();
        $estatus = $this->model->obtenerEstatus();
        $this->loadView('productos/editar', [
            'producto' => $producto,
            'categorias' => $categorias,
            'tiposCategoria' => $tiposCategoria,
            'estatus' => $estatus
        ]);
    }
    public function actualizar($id)
    {
        $this->checkSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'descripcion' => trim($_POST['descripcion']),
                'cantidad' => (int)$_POST['cantidad'],
                'precio' => (float)$_POST['precio'],
                'id_categoria' => (int)$_POST['id_categoria'],
                'id_estatus' => (int)$_POST['id_estatus']
            ];
            // Validaciones
            if (empty($data['descripcion'])) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'La descripción del producto es requerida',
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('productos&method=editar&id=' . $id);
                return;
            }
            if ($this->model->existeProducto($data['descripcion'], $data['id_categoria'], $id)) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Ya existe un producto con esta descripción en la categoría seleccionada',
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('productos&method=editar&id=' . $id);
                return;
            }
            if ($this->model->actualizarProducto($id, $data)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Producto actualizado exitosamente',
                    'icon' => 'success'
                ];
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al actualizar el producto: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
            }
            $this->redirect('productos');
        }
    }
    public function mostrar($id)
    {
        $this->checkSession();
        $producto = $this->model->obtenerProductoPorId($id);
        if (!$producto) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Producto no encontrado',
                'icon' => 'error'
            ];
            $this->redirect('productos');
        }
        $this->loadView('productos/mostrar', ['producto' => $producto]);
    }
    public function cambiarEstado($id)
    {
        $this->checkSession();
        $producto = $this->model->obtenerProductoPorId($id);
        if (!$producto) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Producto no encontrado',
                'icon' => 'error'
            ];
            $this->redirect('productos');
        }

        $nuevoEstado = $producto['id_estatus'] == 1 ? 2 : 1;
        $estadoTexto = $nuevoEstado == 1 ? 'activado' : 'desactivado';

        if ($this->model->cambiarEstado($id, $nuevoEstado)) {
            $_SESSION['mensaje'] = [
                'title' => 'Éxito',
                'text' => "Producto $estadoTexto correctamente",
                'icon' => 'success'
            ];
        } else {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Error al cambiar el estado: ' . implode(', ', $this->model->getErrors()),
                'icon' => 'error'
            ];
        }

        $this->redirect('productos');
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

    public function registrarEntrada()
    {
        $this->checkSession();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar datos básicos
            if (empty($_POST['id_producto']) || empty($_POST['cantidad']) || empty($_POST['cedula_proveedor'])) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Producto, cantidad y proveedor son obligatorios',
                    'icon' => 'error'
                ];
                $this->redirect('productos&method=registrarEntrada');
                return;
            }

            // Obtener precio de la relación
            require_once ROOT_PATH . 'models/ProductoProveedorModel.php';
            $ppModel = new ProductoProveedorModel();
            $precio_compra = $ppModel->obtenerPrecioPorProveedorProducto(
                $_POST['id_producto'],
                $_POST['cedula_proveedor']
            );

            if (empty($precio_compra)) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'No existe precio establecido para este proveedor. Establezca primero la relación.',
                    'icon' => 'error'
                ];
                $this->redirect('productos&method=registrarEntrada');
                return;
            }

            // Registrar entrada
            if ($this->model->registrarEntradaProducto(
                (int)$_POST['id_producto'],
                (int)$_POST['cantidad'],
                (float)$precio_compra,
                $_POST['cedula_proveedor'],
                $_SESSION['user_id'],
                $_POST['observaciones'] ?? null
            )) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Entrada registrada correctamente',
                    'icon' => 'success'
                ];
                $this->redirect('productos');
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al registrar: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('productos&method=registrarEntrada');
            }
        } else {
            // Mostrar formulario
            $productos = $this->model->obtenerProductosActivos();
            $proveedores = $this->proveedorModel->obtenerProveedoresActivos();

            // Obtener precios de relaciones existentes para pre-cargar en JS
            $precios = [];
            require_once ROOT_PATH . 'models/ProductoProveedorModel.php';
            $ppModel = new ProductoProveedorModel();
            foreach ($productos as $producto) {
                foreach ($proveedores as $proveedor) {
                    $precio = $ppModel->obtenerPrecioPorProveedorProducto($producto['id'], $proveedor['cedula']);
                    if ($precio) {
                        $precios[$producto['id']][$proveedor['cedula']] = $precio;
                    }
                }
            }

            $this->loadView('productos/entrada', [
                'productos' => $productos,
                'proveedores' => $proveedores,
                'precios' => json_encode($precios) // Pasar a JSON para JS
            ]);
        }
    }

    public function editarEntrada($id)
    {
        $this->checkSession();

        // Obtener la entrada existente
        $entrada = $this->model->obtenerEntradaPorId($id);
        if (!$entrada) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Entrada no encontrada',
                'icon' => 'error'
            ];
            $this->redirect('productos');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar datos básicos
            if (empty($_POST['cantidad'])) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'La cantidad es obligatoria',
                    'icon' => 'error'
                ];
                $this->redirect('productos&method=editarEntrada&id=' . $id);
                return;
            }

            // Obtener precio de la relación (no editable)
            require_once ROOT_PATH . 'models/ProductoProveedorModel.php';
            $ppModel = new ProductoProveedorModel();
            $precio_compra = $ppModel->obtenerPrecioPorProveedorProducto(
                $entrada['id_producto'],
                $entrada['cedula_proveedor']
            );

            if (empty($precio_compra)) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'No existe precio establecido para este proveedor. Actualice primero la relación.',
                    'icon' => 'error'
                ];
                $this->redirect('productos&method=editarEntrada&id=' . $id);
                return;
            }

            // Actualizar entrada
            if ($this->model->actualizarEntradaProducto(
                $id,
                (int)$_POST['cantidad'],
                (float)$precio_compra,
                $_POST['observaciones'] ?? null
            )) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Entrada actualizada correctamente',
                    'icon' => 'success'
                ];
                $this->redirect('productos');
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al actualizar: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('productos&method=editarEntrada&id=' . $id);
            }
        } else {
            // Mostrar formulario de edición
            $productos = $this->model->obtenerProductosActivos();
            $proveedores = $this->proveedorModel->obtenerProveedoresActivos();

            // Obtener precio de la relación
            require_once ROOT_PATH . 'models/ProductoProveedorModel.php';
            $ppModel = new ProductoProveedorModel();
            $precio = $ppModel->obtenerPrecioPorProveedorProducto(
                $entrada['id_producto'],
                $entrada['cedula_proveedor']
            );

            $this->loadView('productos/editar_entrada', [
                'entrada' => $entrada,
                'productos' => $productos,
                'proveedores' => $proveedores,
                'precio_relacion' => $precio
            ]);
        }
    }
}
