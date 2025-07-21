<?php
class VentaController
{
    private $model;
    private $productoModel;
    private $clienteModel;
    private $usuarioModel;

    private function procesarPagos($post_data)
    {
        $pagos = [];
        
        // Verificar si es pago partido o simple
        if (isset($post_data['formas_pago'])) {
            // Pago partido
            $formas_pago = $post_data['formas_pago'];
            $montos = $post_data['montos_pago'];
            $referencias = $post_data['referencias_pago'] ?? [];

            for ($i = 0; $i < count($formas_pago); $i++) {
                if (!empty($formas_pago[$i]) && !empty($montos[$i])) {
                    $pagos[] = [
                        'forma_pago' => $formas_pago[$i],
                        'monto' => $montos[$i],
                        'referencia_pago' => ($formas_pago[$i] != 'EFECTIVO') ? $referencias[$i] : null
                    ];
                }
            }
        } else {
            // Pago simple
            $forma_pago = $post_data['forma_pago'];
            $monto = $post_data['monto_pago'];
            $referencia = $post_data['referencia_pago'] ?? null;

            $pagos[] = [
                'forma_pago' => $forma_pago,
                'monto' => $monto,
                'referencia_pago' => ($forma_pago != 'EFECTIVO') ? $referencia : null
            ];
        }

        return $pagos;
    }
    
    public function __construct()
    {
        require_once ROOT_PATH . 'models/VentaModel.php';
        require_once ROOT_PATH . 'models/ProductoModel.php';
        require_once ROOT_PATH . 'models/ClienteModel.php';
        require_once ROOT_PATH . 'models/UsuarioModel.php';
        require_once ROOT_PATH . 'lib/fpdf/factura.php';
        $this->model = new VentaModel();
        $this->productoModel = new ProductoModel();
        $this->clienteModel = new ClienteModel();
        $this->usuarioModel = new UsuarioModel();
    }

    public function index()
    {
        $this->checkSession();
        
        // Parámetros de paginación
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $por_pagina = isset($_GET['por_pagina']) ? (int)$_GET['por_pagina'] : 10;
        $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
        
        // Filtros
        $filtros = [];
        $filtros_activos = [];
        
        // Procesar filtros del modal
        if (isset($_GET['fecha_inicio']) && !empty($_GET['fecha_inicio'])) {
            $filtros['fecha_inicio'] = $_GET['fecha_inicio'];
            $filtros_activos[] = 'Desde: ' . date('d/m/Y', strtotime($_GET['fecha_inicio']));
        }
        
        if (isset($_GET['fecha_fin']) && !empty($_GET['fecha_fin'])) {
            $filtros['fecha_fin'] = $_GET['fecha_fin'];
            $filtros_activos[] = 'Hasta: ' . date('d/m/Y', strtotime($_GET['fecha_fin']));
        }
        
        if (isset($_GET['vendedor']) && !empty($_GET['vendedor'])) {
            $filtros['vendedor'] = $_GET['vendedor'];
            $filtros_activos[] = 'Vendedor: ' . $_GET['vendedor'];
        }
        
        // Si hay término de búsqueda, agregarlo a los filtros activos
        if (!empty($busqueda)) {
            $filtros_activos[] = 'Búsqueda: ' . $busqueda;
        }
        
        // Obtener datos con paginación
        $resultado = $this->model->obtenerTodasVentas($pagina, $por_pagina, $busqueda, $filtros);
        
        // Obtener usuarios para el filtro de vendedores
        $usuarios = $this->usuarioModel->obtenerTodosUsuarios(1, 9999, true)['usuarios'];
        
        $this->loadView('ventas/index', [
            'ventas' => $resultado['ventas'],
            'total_registros' => $resultado['total_registros'],
            'pagina_actual' => $resultado['pagina_actual'],
            'por_pagina' => $resultado['por_pagina'],
            'total_paginas' => $resultado['total_paginas'],
            'usuarios' => $usuarios,
            'termino_busqueda' => $busqueda,
            'filtros_activos' => $filtros_activos
        ]);
    }

    public function crear()
    {
        $this->checkSession();
        error_log('Iniciando método crear() en VentaController');
        
        // Obtener y verificar productos activos
        error_log('Obteniendo productos activos...');
        $productos = $this->productoModel->obtenerProductosActivos();
        error_log('Total de productos activos obtenidos: ' . count($productos));
        
        // Verificar estructura de cada producto
        foreach ($productos as $producto) {
            error_log("Verificando producto - ID: {$producto['id']}, Descripción: {$producto['descripcion']}, "
                   . "Categoría: {$producto['categoria']}, Estatus: {$producto['estatus']}");
        }
        
        // Obtener y verificar clientes activos
        error_log('Obteniendo clientes activos...');
        $clientes = $this->clienteModel->obtenerClientesActivos();
        error_log('Total de clientes activos obtenidos: ' . count($clientes));
        
        error_log('Cargando vista crear.php con ' . count($productos) . ' productos y ' . count($clientes) . ' clientes');
        
        $this->loadView('ventas/crear', [
            'productos' => $productos,
            'clientes' => $clientes
        ]);
    }

    private function generarFactura($id_venta, $cliente, $fecha, $productos, $pagos)
    {
        date_default_timezone_set('America/Caracas'); // Establecer zona horaria de Venezuela
        $pdf = new Factura();
        $pdf->AddPage();
        
        // Set all payments and user info in session for the invoice
        $_SESSION['pagos'] = $pagos;
        $_SESSION['usuario'] = ['nombre' => $_SESSION['user_nombre']];
        
        // Usar la fecha y hora actual del servidor
        $fecha_actual = date('d/m/Y h:i A');
        $pdf->datosFactura($cliente, $fecha_actual, $id_venta);
        $pdf->tablaProductos($productos);
        
        $filename = ROOT_PATH . 'facturas/factura_' . $id_venta . '.pdf';
        $pdf->Output('F', $filename);
        
        // Clear payment, user and client info from session
        unset($_SESSION['pagos']);
        unset($_SESSION['usuario']);
        unset($_SESSION['cliente']);
        
        return $filename;
    }

    public function guardar()
    {
        $this->checkSession();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                date_default_timezone_set('America/Caracas'); // Establecer zona horaria de Venezuela
                $data = [
                    'cedula_cliente' => $_POST['cedula_cliente'],
                    'id_usuario' => $_SESSION['user_id'],
                    'productos' => $_POST['productos'],
                    'fecha' => date('Y-m-d H:i:s'),  // Fecha y hora actual del servidor con zona horaria correcta
                    'pagos' => $this->procesarPagos($_POST)
                ];

                // Basic validation
                if (empty($data['cedula_cliente'])) {
                    throw new Exception('Debe seleccionar un cliente');
                }

                if (empty($data['productos']) || !is_array($data['productos'])) {
                    throw new Exception('Debe agregar al menos un producto');
                }

                // Validar pagos
                if (empty($data['pagos'])) {
                    throw new Exception('Debe ingresar al menos un método de pago');
                }

                // Validar que el total de los pagos coincida con el total de la venta
                $total_venta = 0;
                foreach ($data['productos'] as $producto) {
                    $total_venta += $producto['precio'] * $producto['cantidad'];
                }

                $total_pagos = 0;
                foreach ($data['pagos'] as $pago) {
                    $total_pagos += $pago['monto'];
                }

                if ($total_pagos != $total_venta) {
                    throw new Exception('El total de los pagos debe ser igual al total de la venta');
                }

                // Validate product stock
                foreach ($data['productos'] as $producto) {
                    $stock = $this->productoModel->obtenerStockProducto($producto['id']);
                    if ($stock < $producto['cantidad']) {
                        throw new Exception('No hay suficiente stock para el producto: ' . $producto['descripcion']);
                    }
                }

                // Register sale
                $id_venta = $this->model->registrarVenta($data);
                if ($id_venta) {
                    // Get client info
                    $cliente = $this->clienteModel->obtenerClientePorCedula($data['cedula_cliente']);
                    $nombre_cliente = $cliente['nombres'] . ' ' . $cliente['apellidos'];

                    // Set client info in session for invoice
                    $_SESSION['cliente'] = [
                        'cedula' => $cliente['cedula'],
                        'direccion' => $cliente['direccion']
                    ];

                    // Generate invoice
                    $productos_factura = [];
                    foreach ($data['productos'] as $producto) {
                        $productos_factura[] = [
                            'descripcion' => $producto['descripcion'],
                            'cantidad' => $producto['cantidad'],
                            'precio' => $producto['precio']
                        ];
                    }

                    $factura_path = $this->generarFactura(
                        $id_venta,
                        $nombre_cliente,
                        $data['fecha'],
                        $productos_factura,
                        $data['pagos']
                    );

                    // Set success message and download path
                    $_SESSION['mensaje'] = [
                        'title' => 'Éxito',
                        'text' => 'Venta registrada correctamente',
                        'icon' => 'success',
                        'factura_path' => 'facturas/factura_' . $id_venta . '.pdf'
                    ];
                    $this->redirect('ventas&method=crear');
                } else {
                    $errores = $this->model->getErrors();
                    throw new Exception(!empty($errores) ? $errores[0] : 'Error al registrar la venta');
                }
            } catch (Exception $e) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => $e->getMessage(),
                    'icon' => 'error'
                ];
                $this->redirect('ventas&method=crear');
            }
        } else {
            $this->redirect('ventas&method=crear');
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

        // Obtener detalles y pagos de la venta
        $detalles = $this->model->obtenerDetallesVenta($id);
        $pagos = $this->model->obtenerPagosVenta($id);

        $this->loadView('ventas/mostrar', [
            'venta' => $venta,
            'detalles' => $detalles,
            'pagos' => $pagos
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