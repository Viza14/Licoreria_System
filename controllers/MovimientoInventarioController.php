<?php
class MovimientoInventarioController
{
    private $model;
    private $productoModel;
    private $proveedorModel;
    private $clienteModel;

    public function __construct()
    {
        require_once ROOT_PATH . 'models/MovimientoInventarioModel.php';
        require_once ROOT_PATH . 'models/ProductoModel.php';
        require_once ROOT_PATH . 'models/ProveedorModel.php';
        require_once ROOT_PATH . 'models/ClienteModel.php';
        $this->model = new MovimientoInventarioModel();
        $this->productoModel = new ProductoModel();
        $this->proveedorModel = new ProveedorModel();
        $this->clienteModel = new ClienteModel();
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
        
        // Determinar qué vista cargar basado en el tipo de movimiento
        if ($movimiento['tipo_movimiento'] == 'SALIDA' && $movimiento['tipo_referencia'] == 'VENTA') {
            $this->loadView('movimientos_inventario/mostrar_venta', ['movimiento' => $movimiento]);
        } else {
            $this->loadView('movimientos_inventario/mostrar', ['movimiento' => $movimiento]);
        }
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

    public function editar($id)
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

        $producto = $this->productoModel->obtenerProductoPorId($movimiento['id_producto']);
        $proveedores = $this->proveedorModel->obtenerProveedoresActivos();
        
        // Get existing price relationships for JS preloading
        $precios = [];
        require_once ROOT_PATH . 'models/ProductoProveedorModel.php';
        $ppModel = new ProductoProveedorModel();
        
        foreach ($proveedores as $proveedor) {
            $precio = $ppModel->obtenerPrecioPorProveedorProducto($movimiento['id_producto'], $proveedor['cedula']);
            if ($precio) {
                $precios[$movimiento['id_producto']][$proveedor['cedula']] = $precio;
            }
        }
        
        $this->loadView('movimientos_inventario/editar', [
            'movimiento' => $movimiento,
            'producto' => $producto,
            'proveedores' => $proveedores,
            'precios' => json_encode($precios),
            'precio_actual' => $movimiento['precio_unitario']
        ]);
    }

    public function actualizar($id)
    {
        $this->checkSession();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $movimiento = $this->model->obtenerMovimientoPorId($id);
            
            $data = [
                'cantidad' => (int)$_POST['cantidad'],
                'precio_unitario' => (float)$_POST['precio_unitario'],
                'observaciones' => $_POST['observaciones'] ?? null
            ];
            
            if ($this->model->actualizarMovimiento($id, $data)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Movimiento actualizado correctamente',
                    'icon' => 'success'
                ];
            } else {
                $errors = $this->model->getErrors();
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al actualizar el movimiento: ' . implode(', ', $errors),
                    'icon' => 'error'
                ];
            }
            
            $this->redirect('movimientos-inventario');
        }
    }

    public function modificarVenta($id_venta)
    {
        $this->checkSession();
        
        // Obtener datos de la venta
        $venta = $this->model->obtenerVentaParaModificacion($id_venta);
        if (!$venta) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Venta no encontrada o no modificable',
                'icon' => 'error'
            ];
            $this->redirect('movimientos-inventario');
        }

        // Obtener detalles y datos adicionales
        $detalles = $this->model->obtenerDetallesVentaParaModificacion($id_venta);
        $productos = $this->productoModel->obtenerProductosActivos();
        $clientes = $this->clienteModel->obtenerClientesActivos();

        $this->loadView('movimientos_inventario/modificar_venta', [
            'venta' => $venta,
            'detalles' => $detalles,
            'productos' => $productos,
            'clientes' => $clientes
        ]);
    }

    public function actualizarVenta($id_venta)
    {
        $this->checkSession();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Procesar datos del formulario
            $productos = [];
            $monto_total = 0;
            
            foreach ($_POST['productos'] as $id_detalle => $producto) {
                $subtotal = $producto['cantidad'] * $producto['precio'];
                
                $productos[] = [
                    'id_detalle' => $id_detalle,
                    'id_producto' => $producto['id'],
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto['precio'],
                    'subtotal' => $subtotal
                ];
                
                $monto_total += $subtotal;
            }

            $data = [
                'cedula_cliente' => $_POST['cedula_cliente'],
                'id_usuario' => $_SESSION['user_id'],
                'fecha' => $_POST['fecha'],
                'forma_pago' => $_POST['forma_pago'],
                'referencia_pago' => $_POST['forma_pago'] != 'EFECTIVO' ? $_POST['referencia_pago'] : null,
                'monto_total' => $monto_total,
                'productos' => $productos
            ];

            // Validaciones
            if (empty($data['productos'])) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Debe incluir al menos un producto',
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario&method=modificarVenta&id=' . $id_venta);
                return;
            }

            // Modificar la venta
            $resultado = $this->model->modificarVentaConAjuste($id_venta, $data);
            
            if ($resultado) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Venta modificada correctamente. Se ha registrado como ajuste de inventario.',
                    'icon' => 'success'
                ];
                $this->redirect('movimientos-inventario');
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al modificar venta: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario&method=modificarVenta&id=' . $id_venta);
            }
        }
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