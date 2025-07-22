<?php
require_once ROOT_PATH . 'lib/fpdf/factura.php';

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
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $porPagina = isset($_GET['por_pagina']) ? (int)$_GET['por_pagina'] : 10;

        // Process filter parameters from URL
        $filtros = [];

        if (isset($_GET['busqueda'])) {
            $filtros['busqueda'] = $_GET['busqueda'];
        }

        if (isset($_GET['tipos'])) {
            $filtros['tipos'] = explode(',', $_GET['tipos']);
        }

        if (isset($_GET['estados'])) {
            $filtros['estados'] = explode(',', $_GET['estados']);
        }

        if (isset($_GET['fecha_inicio'])) {
            $filtros['fecha_inicio'] = $_GET['fecha_inicio'];
        }

        if (isset($_GET['fecha_fin'])) {
            $filtros['fecha_fin'] = $_GET['fecha_fin'];
        }

        $resultado = $this->model->obtenerTodosMovimientos($pagina, $porPagina, $filtros);
        $this->loadView('movimientos_inventario/index', [
            'movimientos' => $resultado['data'],
            'total' => $resultado['total'],
            'pagina_actual' => $resultado['pagina_actual'],
            'por_pagina' => $resultado['por_pagina'],
            'total_paginas' => $resultado['total_paginas'],
            'filtros_activos' => $filtros
        ]);
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

        // Procesar paginación
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $por_pagina = 10; // Número de registros por página

        // Procesar filtros
        $filtros = [];
        
        // Obtener filtros de POST o GET
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $filtros = [
                'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
                'fecha_fin' => $_POST['fecha_fin'] ?? null,
                'categoria' => $_POST['categoria'] ?? null
            ];
        } else {
            $filtros = [
                'fecha_inicio' => $_GET['fecha_inicio'] ?? null,
                'fecha_fin' => $_GET['fecha_fin'] ?? null,
                'categoria' => $_GET['categoria'] ?? null
            ];
        }

        // Obtener datos para el resumen
        $resumen_general = $this->model->obtenerResumenGeneral($filtros);
        $resultado = $this->model->obtenerResumenProductos($filtros, $pagina, $por_pagina);

        // Obtener categorías para el filtro
        require_once ROOT_PATH . 'models/CategoriaModel.php';
        $categoriaModel = new CategoriaModel();
        $categorias = $categoriaModel->obtenerCategoriasActivas();

        $this->loadView('movimientos_inventario/resumen', [
            'resumen_general' => $resumen_general,
            'productos' => $resultado['productos'],
            'total_productos' => $resultado['total'],
            'pagina_actual' => $resultado['pagina_actual'],
            'total_paginas' => $resultado['total_paginas'],
            'categorias' => $categorias,
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

            if (!$movimiento) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Movimiento no encontrado',
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario');
                return;
            }

            if ($movimiento['tipo_movimiento'] !== 'ENTRADA') {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Solo se pueden editar movimientos de entrada',
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario');
                return;
            }

            $data = [
                'cantidad' => (int)$_POST['cantidad'],
                'precio_unitario' => (float)$_POST['precio_unitario'],
                'observaciones' => $_POST['observaciones'] ?? null
            ];

            if ($this->model->actualizarMovimiento($id, $data)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Movimiento actualizado correctamente. Se ha creado un nuevo registro de ajuste.',
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

            if (!isset($_POST['productos']) || !is_array($_POST['productos'])) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'No se recibieron productos para la venta',
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario&method=modificarVenta&id=' . $id_venta);
                return;
            }

            foreach ($_POST['productos'] as $id_detalle => $producto) {
                if (!isset($producto['id_producto']) || !isset($producto['cantidad']) || !isset($producto['precio'])) {
                    $_SESSION['error'] = [
                        'title' => 'Error',
                        'text' => 'Datos de producto incompletos',
                        'icon' => 'error'
                    ];
                    $this->redirect('movimientos-inventario&method=modificarVenta&id=' . $id_venta);
                    return;
                }

                $subtotal = (float)$producto['cantidad'] * (float)$producto['precio'];

                $productos[] = [
                    'id_detalle' => $id_detalle,
                    'id_producto' => $producto['id_producto'],
                    'cantidad' => (float)$producto['cantidad'],
                    'precio_unitario' => (float)$producto['precio'],
                    'subtotal' => $subtotal
                ];

                $monto_total += $subtotal;
            }

            if (!isset($_POST['cedula_cliente']) || !isset($_POST['fecha'])) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Faltan datos requeridos para la venta',
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario&method=modificarVenta&id=' . $id_venta);
                return;
            }

            // Procesar pagos
            $pagos = [];
            if (isset($_POST['formas_pago']) && is_array($_POST['formas_pago'])) {
                $formas_pago = $_POST['formas_pago'];
                $montos_pago = $_POST['montos_pago'];
                $referencias_pago = $_POST['referencias_pago'] ?? [];

                for ($i = 0; $i < count($formas_pago); $i++) {
                    $forma_pago = $formas_pago[$i];
                    $monto = (float)$montos_pago[$i];

                    // Solo agregar referencia si no es efectivo
                    $referencia = ($forma_pago !== 'EFECTIVO') ? ($referencias_pago[$i] ?? null) : null;

                    $pagos[] = [
                        'forma_pago' => $forma_pago,
                        'monto' => $monto,
                        'referencia_pago' => $referencia
                    ];
                }
            }

            $data = [
                'cedula_cliente' => $_POST['cedula_cliente'],
                'id_usuario' => $_SESSION['user_id'],
                'fecha' => $_POST['fecha'],
                'monto_total' => $monto_total,
                'productos' => $productos,
                'pagos' => $pagos
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

            // Validar que el total de pagos coincida con el monto total
            $total_pagado = array_reduce($pagos, function ($carry, $pago) {
                return $carry + $pago['monto'];
            }, 0);

            if (abs($total_pagado - $monto_total) > 0.01) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'El total de los pagos (' . number_format($total_pagado, 2) . ') debe ser igual al total de la venta (' . number_format($monto_total, 2) . ')',
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario&method=modificarVenta&id=' . $id_venta);
                return;
            }

            // Modificar la venta
            $nuevo_id_venta = $this->model->modificarVentaConAjuste($id_venta, $data);

            if ($nuevo_id_venta) {
                // Generar factura para la nueva venta
                $this->generarFacturaModificacion($nuevo_id_venta, $data);
                
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Venta modificada correctamente. Se ha registrado como ajuste de inventario y se ha generado la nueva factura.',
                    'icon' => 'success',
                    'factura_path' => 'facturas/factura_' . $nuevo_id_venta . '.pdf'
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

    private function generarFacturaModificacion($id_venta, $data)
    {
        try {
            date_default_timezone_set('America/Caracas'); // Establecer zona horaria de Venezuela
            
            // Obtener información del cliente
            $cliente = $this->clienteModel->obtenerClientePorCedula($data['cedula_cliente']);
            $nombre_cliente = $cliente['nombres'] . ' ' . $cliente['apellidos'];
            
            // Configurar información del cliente en sesión para la factura
            $_SESSION['cliente'] = [
                'cedula' => $cliente['cedula'],
                'direccion' => $cliente['direccion']
            ];
            
            // Configurar información de pagos y usuario en sesión para la factura
            $_SESSION['pagos'] = $data['pagos'];
            $_SESSION['usuario'] = ['nombre' => $_SESSION['user_nombre']];
            
            // Crear PDF
            $pdf = new Factura();
            $pdf->AddPage();
            
            // Usar la fecha y hora actual del servidor
            $fecha_actual = date('d/m/Y h:i A');
            $pdf->datosFactura($nombre_cliente, $fecha_actual, $id_venta);
            
            // Preparar productos para la factura
            $productos_factura = [];
            foreach ($data['productos'] as $producto) {
                // Obtener descripción del producto
                $producto_info = $this->productoModel->obtenerProductoPorId($producto['id_producto']);
                $productos_factura[] = [
                    'descripcion' => $producto_info['descripcion'],
                    'cantidad' => $producto['cantidad'],
                    'precio' => $producto['precio_unitario']
                ];
            }
            
            $pdf->tablaProductos($productos_factura);
            
            // Guardar factura
            $filename = ROOT_PATH . 'facturas/factura_' . $id_venta . '.pdf';
            $pdf->Output('F', $filename);
            
            // Limpiar información de sesión
            unset($_SESSION['pagos']);
            unset($_SESSION['usuario']);
            unset($_SESSION['cliente']);
            
            return $filename;
        } catch (Exception $e) {
            error_log("Error al generar factura: " . $e->getMessage());
            return false;
        }
    }
}
