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
        $this->model = new VentaModel();
        $this->productoModel = new ProductoModel();
        $this->clienteModel = new ClienteModel();
        $this->usuarioModel = new UsuarioModel();
    }

    public function index()
    {
        $this->checkSession();
        
        // Handle report filters
        $filtros = [];
        $esReporte = isset($_GET['reporte']) && $_GET['reporte'] == '1';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $filtros = [
                'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
                'fecha_fin' => $_POST['fecha_fin'] ?? null,
                'id_usuario' => $_POST['id_usuario'] ?? null,
                'cedula_cliente' => $_POST['cedula_cliente'] ?? null
            ];
            $_SESSION['ventas_filtros'] = $filtros;
        } elseif ($esReporte) {
            $filtros = $_SESSION['ventas_filtros'] ?? [];
        }
        
        // Get sales data based on filters
        $ventas = empty($filtros) ? 
            $this->model->obtenerTodasVentas() : 
            $this->model->generarReporteVentas($filtros);
        
        // Additional data for reports
        $usuarios = $this->usuarioModel->obtenerTodosUsuarios();
        $clientes = $this->clienteModel->obtenerClientesActivos();
        
        $this->loadView('ventas/index', [
            'ventas' => $ventas,
            'usuarios' => $usuarios,
            'clientes' => $clientes,
            'filtros' => $filtros,
            'esReporte' => $esReporte
        ]);
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
                'productos' => $_POST['productos'],
                'fecha' => $_POST['fecha'],
                'pagos' => $this->procesarPagos($_POST)
            ];

            // Basic validation
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

            // Validar pagos
            if (empty($data['pagos'])) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Debe ingresar al menos un método de pago',
                    'icon' => 'error'
                ];
                $this->redirect('ventas&method=crear');
                return;
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
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'El total de los pagos debe ser igual al total de la venta',
                    'icon' => 'error'
                ];
                $this->redirect('ventas&method=crear');
                return;
            }

            // Validate product stock
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

            // Register sale
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

    public function editar($id)
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
        }

        $detalles = $this->model->obtenerDetallesVenta($id);
        $productos = $this->productoModel->obtenerProductosActivos();
        $clientes = $this->clienteModel->obtenerClientesActivos();

        $this->loadView('ventas/editar', [
            'venta' => $venta,
            'detalles' => $detalles,
            'productos' => $productos,
            'clientes' => $clientes
        ]);
    }

    public function actualizar($id)
    {
        $this->checkSession();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'cedula_cliente' => $_POST['cedula_cliente'],
                'id_usuario' => $_SESSION['user_id'],
                'fecha' => $_POST['fecha'],
                'forma_pago' => $_POST['forma_pago'],
                'referencia_pago' => $_POST['forma_pago'] != 'EFECTIVO' ? $_POST['referencia_pago'] : null,
                'productos' => $_POST['productos']
            ];

            // Basic validation
            if (empty($data['cedula_cliente']) || empty($data['productos'])) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Datos incompletos',
                    'icon' => 'error'
                ];
                $this->redirect('ventas&method=editar&id=' . $id);
                return;
            }

            if ($this->model->actualizarVenta($id, $data)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Venta actualizada correctamente. Se ha creado un registro de ajuste.',
                    'icon' => 'success'
                ];
                $this->redirect('ventas');
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al actualizar: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
                $this->redirect('ventas&method=editar&id=' . $id);
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
        }

        $detalles = $this->model->obtenerDetallesVenta($id);
        $pagos = $venta['pagos'] ?? [];
        unset($venta['pagos']); // Removemos los pagos del array principal de venta

        $this->loadView('ventas/mostrar', [
            'venta' => $venta,
            'detalles' => $detalles,
            'pagos' => $pagos
        ]);
    }

    public function exportar()
    {
        $this->checkSession();
        
        $filtros = $_SESSION['ventas_filtros'] ?? [];
        $reporte = $this->model->generarReporteVentas($filtros);
        
        // Generate CSV file
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=reporte_ventas_' . date('Ymd') . '.csv');
        
        $output = fopen('php://output', 'w');
        
        // Write CSV headers
        fputcsv($output, [
            'ID Venta', 
            'Fecha', 
            'Cliente', 
            'Cédula', 
            'Vendedor', 
            'Monto Total', 
            'Cant. Productos', 
            'Productos',
            'Forma de Pago',
            'Referencia'
        ]);
        
        // Write data rows
        foreach ($reporte as $row) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit();
    }

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