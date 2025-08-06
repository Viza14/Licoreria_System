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

    public function guardarAjustesCompra() {
        try {
            // Verificar que se recibieron los datos necesarios
            if (!isset($_POST['ajustes']) || !is_array($_POST['ajustes']) || empty($_POST['ajustes'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se recibieron datos de ajustes válidos'
                ]);
                return;
            }
            
            $ajustes = $_POST['ajustes'];
            $observaciones = $_POST['observaciones'] ?? 'Ajuste masivo de compra';
            
            // Validar que todos los ajustes tengan los datos necesarios
            foreach ($ajustes as $ajuste) {
                if (!isset($ajuste['id_movimiento_original']) || 
                    !isset($ajuste['id_producto']) || 
                    !isset($ajuste['nueva_cantidad']) || 
                    !isset($ajuste['nuevo_precio'])) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Datos de ajuste incompletos'
                    ]);
                    return;
                }
                
                // Validar que los valores sean válidos
                if ($ajuste['nueva_cantidad'] <= 0 || $ajuste['nuevo_precio'] <= 0) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Las cantidades y precios deben ser mayores a 0'
                    ]);
                    return;
                }
            }
            
            // Procesar cada ajuste
            $ajustesExitosos = 0;
            $errores = [];
            
            foreach ($ajustes as $ajuste) {
                try {
                    // Obtener el movimiento original
                    $movimientoOriginal = $this->model->obtenerMovimientoPorId($ajuste['id_movimiento_original']);
                    
                    if (!$movimientoOriginal) {
                        $errores[] = "No se encontró el movimiento original con ID: " . $ajuste['id_movimiento_original'];
                        continue;
                    }
                    
                    // Verificar si hay cambios
                    $cantidadCambio = $ajuste['nueva_cantidad'] != $movimientoOriginal['cantidad'];
                    $precioCambio = $ajuste['nuevo_precio'] != $movimientoOriginal['precio_unitario'];
                    
                    if (!$cantidadCambio && !$precioCambio) {
                        // No hay cambios, saltar este ajuste
                        continue;
                    }
                    
                    // Preparar datos del ajuste
                    $datosAjuste = [
                        'id_producto' => $ajuste['id_producto'],
                        'tipo_movimiento' => $movimientoOriginal['tipo_movimiento'],
                        'subtipo_movimiento' => $movimientoOriginal['subtipo_movimiento'],
                        'cantidad' => $ajuste['nueva_cantidad'],
                        'precio_unitario' => $ajuste['nuevo_precio'],
                        'observaciones' => $observaciones,
                        'id_usuario' => $_SESSION['user_id']
                    ];
                    
                    // Registrar el ajuste
                    $resultado = $this->model->registrarAjuste($ajuste['id_movimiento_original'], $datosAjuste);
                    
                    if ($resultado) {
                        $ajustesExitosos++;
                    } else {
                        $errores[] = "Error al procesar ajuste para producto ID: " . $ajuste['id_producto'];
                    }
                    
                } catch (Exception $e) {
                    $errores[] = "Error en ajuste de producto ID " . $ajuste['id_producto'] . ": " . $e->getMessage();
                }
            }
            
            // Preparar respuesta
            if ($ajustesExitosos > 0) {
                $mensaje = "Se procesaron $ajustesExitosos ajuste(s) exitosamente";
                if (!empty($errores)) {
                    $mensaje .= ". Errores: " . implode(', ', $errores);
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => $mensaje,
                    'ajustes_procesados' => $ajustesExitosos,
                    'errores' => $errores
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo procesar ningún ajuste. Errores: ' . implode(', ', $errores)
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al procesar los ajustes: ' . $e->getMessage()
            ]);
        }
    }

    public function index()
    {
        $this->checkSession();

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

        // Obtener todos los movimientos sin paginación
        $resultado = $this->model->obtenerTodosLosMovimientos($filtros);
        
        $this->loadView('movimientos_inventario/index', [
            'movimientos' => $resultado['data'],
            'total_movimientos' => $resultado['total'],
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
        } elseif ($movimiento['subtipo_movimiento'] == 'PERDIDA') {
            $this->loadView('movimientos_inventario/mostrar_perdida', ['movimiento' => $movimiento]);
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
        $por_pagina = 20; // Aumentamos a 20 registros por página

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

    // Nuevos métodos para generar movimientos
    public function generarMovimiento()
    {
        $this->checkSession();
        $this->loadView('movimientos_inventario/generar');
    }

    public function registrarPerdida()
    {
        $this->checkSession();
        
        // Verificar si se está editando un movimiento existente
        $editar_id = $_GET['editar_id'] ?? null;
        $movimiento_editar = null;
        
        if ($editar_id) {
            // Obtener el movimiento a editar
            $movimiento_editar = $this->model->obtenerMovimientoPorId($editar_id);
            
            if (!$movimiento_editar || $movimiento_editar['subtipo_movimiento'] !== 'PERDIDA') {
                // Si no es una pérdida válida, redirigir
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'El movimiento especificado no es una pérdida válida',
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario');
                return;
            }
        }
        
        // Obtener productos activos
        $productos = $this->productoModel->obtenerProductosActivos();
        
        $this->loadView('movimientos_inventario/registrar_perdida', [
            'productos' => $productos,
            'movimiento_editar' => $movimiento_editar,
            'es_edicion' => !empty($editar_id)
        ]);
    }

    public function guardarPerdida()
    {
        $this->checkSession();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id_producto' => (int)$_POST['id_producto'],
                'cantidad' => (int)$_POST['cantidad'],
                'precio_unitario' => (float)$_POST['precio_unitario'],
                'observaciones' => 'PÉRDIDA - ' . ($_POST['descripcion'] ?? $_POST['observaciones'] ?? ''),
                'tipo_movimiento' => 'SALIDA',
                'subtipo_movimiento' => 'PERDIDA',
                'tipo_referencia' => 'PERDIDA',
                'id_referencia' => null,
                'id_usuario' => $_SESSION['user_id']
            ];

            $resultado = $this->model->registrarMovimiento($data);
            
            if ($resultado) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Pérdida registrada correctamente con número de transacción',
                    'icon' => 'success'
                ];
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al registrar la pérdida: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
            }

            $this->redirect('movimientos-inventario');
        }
    }

    public function registrarOtro()
    {
        $this->checkSession();
        
        $tipo = $_GET['tipo'] ?? 'ENTRADA';
        if (!in_array($tipo, ['ENTRADA', 'SALIDA'])) {
            $tipo = 'ENTRADA';
        }
        
        // Verificar si se está editando un movimiento existente
        $editar_id = $_GET['editar_id'] ?? null;
        $movimiento_editar = null;
        
        if ($editar_id) {
            // Obtener el movimiento a editar
            $movimiento_editar = $this->model->obtenerMovimientoPorId($editar_id);
            
            if ($movimiento_editar && $movimiento_editar['subtipo_movimiento'] === 'OTRO') {
                // Usar el tipo del movimiento original
                $tipo = $movimiento_editar['tipo_movimiento'];
            } else {
                // Si no es un movimiento tipo OTRO válido, redirigir
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'El movimiento especificado no es de tipo OTRO válido',
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario');
                return;
            }
        }
        
        // Obtener productos activos
        $productos = $this->productoModel->obtenerProductosActivos();
        
        $this->loadView('movimientos_inventario/registrar_otro', [
            'tipo' => $tipo,
            'productos' => $productos,
            'movimiento_editar' => $movimiento_editar,
            'es_edicion' => !empty($editar_id)
        ]);
    }

    public function guardarOtro()
    {
        $this->checkSession();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo_texto = $_POST['tipo_movimiento'] === 'ENTRADA' ? 'ENTRADA' : 'SALIDA';
            
            $data = [
                'id_producto' => $_POST['id_producto'],
                'tipo_movimiento' => $_POST['tipo_movimiento'],
                'subtipo_movimiento' => 'OTRO',
                'cantidad' => $_POST['cantidad'],
                'precio_unitario' => $_POST['precio_unitario'],
                'id_referencia' => null,
                'tipo_referencia' => 'OTRO',
                'id_usuario' => $_SESSION['user_id'],
                'observaciones' => strtoupper($tipo_texto) . ' - ' . ($_POST['observaciones'] ?? '')
            ];

            if ($this->model->registrarMovimiento($data)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Movimiento registrado exitosamente',
                    'icon' => 'success'
                ];
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al registrar el movimiento: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
            }

            $this->redirect('movimientos-inventario');
        }
    }

    public function registrarAjuste()
    {
        $this->checkSession();
        
        // Verificar si se está editando un movimiento existente
        $editar_id = $_GET['editar_id'] ?? null;
        $movimiento_editar = null;
        
        if ($editar_id) {
            // Obtener el movimiento a editar
            $movimiento_editar = $this->model->obtenerMovimientoPorId($editar_id);
            
            if ($movimiento_editar && $movimiento_editar['subtipo_movimiento'] === 'AJUSTE') {
                // Es un ajuste válido para editar
            } else {
                // Si no es un ajuste válido, redirigir
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'El movimiento especificado no es un ajuste válido',
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario');
                return;
            }
        }
        
        $tipo = $_GET['tipo'] ?? 'ENTRADA';
        if (!in_array($tipo, ['ENTRADA', 'SALIDA'])) {
            $tipo = 'ENTRADA';
        }
        
        $this->loadView('movimientos_inventario/registrar_ajuste', [
            'tipo' => $tipo,
            'movimiento_editar' => $movimiento_editar,
            'es_edicion' => !empty($editar_id)
        ]);
    }

    public function buscarTransaccion()
    {
        // Configurar headers para JSON desde el inicio
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            // Verificar sesión sin redirección
            if (!isset($_SESSION['user_id'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Sesión expirada. Por favor, inicie sesión nuevamente.'
                ]);
                exit();
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Método no permitido'
                ]);
                exit();
            }

            $numero_transaccion = trim($_POST['numero_transaccion'] ?? '');
            
            if (empty($numero_transaccion)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Número de transacción requerido'
                ]);
                exit();
            }

            $movimiento = $this->model->buscarMovimientoPorTransaccion($numero_transaccion);
            
            if ($movimiento) {
                // Log de depuración
                error_log("DEBUG - Movimiento encontrado: " . json_encode($movimiento));
                
                // Crear clave única para el switch basada en tipo y subtipo
                $tipo_subtipo = $movimiento['tipo_movimiento'] . '_' . $movimiento['subtipo_movimiento'];
                error_log("DEBUG - Tipo_Subtipo: " . $tipo_subtipo);
                
                // Switch específico para manejar redirecciones según tipo y subtipo
                switch ($tipo_subtipo) {
                    case 'SALIDA_PERDIDA':
                        error_log("DEBUG - Redirigiendo a modificarPerdida");
                        echo json_encode([
                            'success' => true,
                            'es_perdida' => true,
                            'redirect_url' => BASE_URL . 'index.php?action=movimientos-inventario&method=modificarPerdida&id=' . $movimiento['id'],
                            'message' => 'Pérdida encontrada. Redirigiendo a la vista de modificación...'
                        ]);
                        exit();
                        
                    case 'SALIDA_VENTA':
                        error_log("DEBUG - Redirigiendo a modificarVenta");
                        echo json_encode([
                            'success' => true,
                            'es_venta' => true,
                            'redirect_url' => BASE_URL . 'index.php?action=movimientos-inventario&method=modificarVenta&id=' . $movimiento['id'],
                            'message' => 'Venta encontrada. Redirigiendo a la vista de modificación...'
                        ]);
                        exit();
                        
                    case 'ENTRADA_OTRO':
                        error_log("DEBUG - Redirigiendo a modificarOtroEntrada");
                        echo json_encode([
                            'success' => true,
                            'es_otro_entrada' => true,
                            'redirect_url' => BASE_URL . 'index.php?action=movimientos-inventario&method=modificarOtroEntrada&id=' . $movimiento['id'],
                            'message' => 'Entrada OTRO encontrada. Redirigiendo a la vista de modificación...'
                        ]);
                        exit();
                        
                    case 'SALIDA_OTRO':
                        error_log("DEBUG - Redirigiendo a modificarOtroSalida");
                        echo json_encode([
                            'success' => true,
                            'es_otro_salida' => true,
                            'redirect_url' => BASE_URL . 'index.php?action=movimientos-inventario&method=modificarOtroSalida&id=' . $movimiento['id'],
                            'message' => 'Salida OTRO encontrada. Redirigiendo a la vista de modificación...'
                        ]);
                        exit();
                        
                    default:
                        error_log("DEBUG - Caso default, mostrando formulario de ajuste");
                        // Para otros tipos de movimientos (como COMPRA), continuar con el flujo normal
                        break;
                }
                
                // Formatear fecha para datetime-local
                $fecha_iso = date('Y-m-d\TH:i', strtotime($movimiento['fecha_movimiento']));
                $movimiento['fecha_movimiento_iso'] = $fecha_iso;
                
                // Si es una compra, obtener todos los productos
                $productos = [];
                if ($movimiento['subtipo_movimiento'] === 'COMPRA') {
                    $productos = $this->model->obtenerProductosDeCompraPorTransaccion($numero_transaccion);
                    
                    // Formatear fechas para todos los productos
                    foreach ($productos as &$producto) {
                        $producto['fecha_movimiento_iso'] = date('Y-m-d\TH:i', strtotime($producto['fecha_movimiento']));
                    }
                }
                
                echo json_encode([
                    'success' => true,
                    'movimiento' => $movimiento,
                    'productos' => $productos,
                    'es_compra' => $movimiento['subtipo_movimiento'] === 'COMPRA'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se encontró ningún movimiento con ese número de transacción'
                ]);
            }
        } catch (Exception $e) {
            // Capturar cualquier error y devolver JSON
            echo json_encode([
                'success' => false,
                'message' => 'Error del servidor: ' . $e->getMessage()
            ]);
        }
        exit();
    }

    public function guardarAjuste()
    {
        $this->checkSession();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id_producto' => $_POST['id_producto'],
                'tipo_movimiento' => $_POST['tipo_movimiento'],
                'subtipo_movimiento' => 'AJUSTE',
                'cantidad' => $_POST['cantidad'],
                'precio_unitario' => $_POST['precio_unitario'],
                'id_referencia' => $_POST['referencia'] ?? null,
                'tipo_referencia' => 'AJUSTE',
                'fecha_movimiento' => $_POST['fecha_movimiento'],
                'observaciones' => $_POST['observaciones'] ?? '',
                'id_usuario' => $_SESSION['user_id'],
                'numero_transaccion_original' => $_POST['numero_transaccion_original'] ?? ''
            ];

            $id_movimiento_original = $_POST['id_movimiento_original'];

            if ($this->model->registrarAjuste($id_movimiento_original, $data)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Ajuste registrado exitosamente',
                    'icon' => 'success'
                ];
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al registrar el ajuste: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
            }

            $this->redirect('movimientos-inventario');
        }
    }

    public function registrarCompra()
    {
        $this->checkSession();
        
        // Verificar si se está editando un movimiento existente
        $editar_id = $_GET['editar_id'] ?? null;
        $movimiento_editar = null;
        $productos_editar = [];
        
        if ($editar_id) {
            // Obtener el movimiento a editar
            $movimiento_editar = $this->model->obtenerMovimientoPorId($editar_id);
            
            if ($movimiento_editar && $movimiento_editar['subtipo_movimiento'] === 'COMPRA') {
                // Obtener los productos de la compra original
                $productos_editar = $this->model->obtenerProductosDeCompra($editar_id);
            } else {
                // Si no es una compra válida, redirigir
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'El movimiento especificado no es una compra válida',
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario');
                return;
            }
        }
        
        // Obtener proveedores activos
        $proveedores = $this->proveedorModel->obtenerProveedoresActivos();
        
        // Obtener productos activos
        $productos = $this->productoModel->obtenerProductosActivos();
        
        // Get existing price relationships for JS preloading
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
        
        $this->loadView('movimientos_inventario/registrar_compra', [
            'proveedores' => $proveedores,
            'productos' => $productos,
            'precios' => json_encode($precios),
            'movimiento_editar' => $movimiento_editar,
            'productos_editar' => $productos_editar,
            'es_edicion' => !empty($editar_id)
        ]);
    }

    public function guardarCompra()
    {
        $this->checkSession();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id_proveedor' => $_POST['id_proveedor'],
                'fecha_compra' => $_POST['fecha_compra'],
                'numero_factura' => $_POST['numero_factura'] ?? '',
                'observaciones' => $_POST['observaciones'] ?? '',
                'productos' => $_POST['productos'],
                'id_usuario' => $_SESSION['user_id']
            ];

            // Validar que hay productos
            $productos_validos = array_filter($data['productos'], function($producto) {
                return !empty($producto['id_producto']) && 
                       !empty($producto['cantidad']) && 
                       !empty($producto['precio_compra']);
            });

            if (empty($productos_validos)) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Debe agregar al menos un producto a la compra',
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario&method=registrarCompra');
                return;
            }

            $resultado = $this->model->registrarCompra($data);
            
            if ($resultado && isset($resultado['success']) && $resultado['success']) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Compra registrada correctamente',
                    'icon' => 'success'
                ];
            } else {
                $error_message = 'Error al registrar la compra';
                if (isset($resultado['message'])) {
                    $error_message = $resultado['message'];
                } else {
                    $errors = $this->model->getErrors();
                    if (!empty($errors)) {
                        $error_message .= ': ' . implode(', ', $errors);
                    }
                }
                
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => $error_message,
                    'icon' => 'error'
                ];
            }

            $this->redirect('movimientos-inventario');
        }
    }

    public function modificarCompra()
    {
        $this->checkSession();

        $id_compra = $_GET['id'] ?? null;
        
        if (!$id_compra) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'ID de compra no especificado',
                'icon' => 'error'
            ];
            $this->redirect('movimientos-inventario');
            return;
        }

        // Obtener datos de la compra
        $compra = $this->model->obtenerCompraParaModificacion($id_compra);
        if (!$compra) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Compra no encontrada o no modificable',
                'icon' => 'error'
            ];
            $this->redirect('movimientos-inventario');
            return;
        }

        // Obtener productos de la compra y datos adicionales
        $productos_compra = $this->model->obtenerProductosDeCompra($id_compra);
        $productos = $this->productoModel->obtenerProductosActivos();
        $proveedores = $this->proveedorModel->obtenerProveedoresActivos();

        $this->loadView('movimientos_inventario/modificar_compra', [
            'compra' => $compra,
            'productos_compra' => $productos_compra,
            'productos' => $productos,
            'proveedores' => $proveedores
        ]);
    }

    public function actualizarCompra()
    {
        $this->checkSession();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_compra = $_GET['id'] ?? null;
            
            if (!$id_compra) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'ID de compra no especificado',
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario');
                return;
            }

            $data = [
                'id_proveedor' => $_POST['id_proveedor'],
                'fecha_compra' => $_POST['fecha_compra'],
                'numero_factura' => $_POST['numero_factura'] ?? '',
                'observaciones' => $_POST['observaciones'] ?? '',
                'productos' => $_POST['productos'],
                'id_usuario' => $_SESSION['user_id']
            ];

            // Validar que hay productos
            $productos_validos = array_filter($data['productos'], function($producto) {
                return !empty($producto['id_producto']) && 
                       !empty($producto['cantidad']) && 
                       !empty($producto['precio_compra']);
            });

            if (empty($productos_validos)) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Debe agregar al menos un producto a la compra',
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario&method=registrarCompra&editar_id=' . $id_compra);
                return;
            }

            if ($this->model->modificarCompraConAjuste($id_compra, $data)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Compra modificada correctamente',
                    'icon' => 'success'
                ];
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al modificar la compra: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
            }

            $this->redirect('movimientos-inventario');
        }
    }

    public function modificarPerdida($id)
    {
        $this->checkSession();

        // Obtener datos de la pérdida
        $perdida = $this->model->obtenerMovimientoPorId($id);
        if (!$perdida || $perdida['subtipo_movimiento'] !== 'PERDIDA') {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Pérdida no encontrada o no modificable',
                'icon' => 'error'
            ];
            $this->redirect('movimientos-inventario');
        }

        // Verificar que la pérdida esté activa
        if ($perdida['id_estatus'] != 1) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'No se puede modificar una pérdida que ya ha sido ajustada',
                'icon' => 'error'
            ];
            $this->redirect('movimientos-inventario');
        }

        // Obtener productos activos
        $productos = $this->productoModel->obtenerProductosActivos();
        
        // Obtener el stock actual del producto de la pérdida
        $stock_actual = $this->productoModel->obtenerStockProducto($perdida['id_producto']);

        $this->loadView('movimientos_inventario/modificar_perdida', [
            'perdida' => $perdida,
            'productos' => $productos,
            'stock_actual' => $stock_actual
        ]);
    }

    public function actualizarPerdida($id)
    {
        $this->checkSession();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $perdida = $this->model->obtenerMovimientoPorId($id);

            if (!$perdida || $perdida['subtipo_movimiento'] !== 'PERDIDA') {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Pérdida no encontrada',
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario');
                return;
            }

            if ($perdida['id_estatus'] != 1) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'No se puede modificar una pérdida que ya ha sido ajustada',
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario');
                return;
            }

            $data = [
                'id_producto' => (int)$_POST['id_producto'],
                'cantidad' => (float)$_POST['cantidad'],
                'precio_unitario' => (float)$_POST['precio_unitario'],
                'fecha' => $_POST['fecha'],
                'observaciones' => $_POST['observaciones'] ?? null,
                'id_usuario' => $_SESSION['user_id']
            ];

            // Validaciones básicas
            if ($data['cantidad'] <= 0) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'La cantidad debe ser mayor a cero',
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario&method=modificarPerdida&id=' . $id);
                return;
            }

            if ($data['precio_unitario'] <= 0) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'El precio unitario debe ser mayor a cero',
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario&method=modificarPerdida&id=' . $id);
                return;
            }

            // Modificar la pérdida con ajuste
            if ($this->model->modificarPerdidaConAjuste($id, $data)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Pérdida modificada correctamente. Se ha creado un nuevo registro de ajuste.',
                    'icon' => 'success'
                ];
                $this->redirect('movimientos-inventario');
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al modificar pérdida: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario&method=modificarPerdida&id=' . $id);
            }
        }
    }

    public function modificarOtroEntrada()
    {
        $this->checkSession();
        
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'ID de movimiento no especificado',
                'icon' => 'error'
            ];
            $this->redirect('movimientos-inventario');
            return;
        }
        
        $id = (int)$_GET['id'];
        
        // Obtener el movimiento
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
        
        // Verificar que sea un movimiento de tipo ENTRADA y subtipo OTRO
        if ($movimiento['tipo_movimiento'] !== 'ENTRADA' || $movimiento['subtipo_movimiento'] !== 'OTRO') {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Este movimiento no es una entrada de tipo OTRO',
                'icon' => 'error'
            ];
            $this->redirect('movimientos-inventario');
            return;
        }
        
        // Verificar que el movimiento esté activo
        if ($movimiento['estado'] !== 'Activo') {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'No se puede modificar un movimiento inactivo',
                'icon' => 'error'
            ];
            $this->redirect('movimientos-inventario');
            return;
        }
        
        // Obtener productos activos
        $productos = $this->productoModel->obtenerProductosActivos();
        
        // Obtener stock actual del producto
        $stock_actual = $this->productoModel->obtenerStockProducto($movimiento['id_producto']);
        
        $this->loadView('movimientos_inventario/modificar_otro_entrada', [
            'movimiento' => $movimiento,
            'productos' => $productos,
            'stock_actual' => $stock_actual
        ]);
    }
    
    public function actualizarOtroEntrada()
    {
        $this->checkSession();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('movimientos-inventario');
            return;
        }
        
        if (!isset($_POST['id'])) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'ID de movimiento no especificado',
                'icon' => 'error'
            ];
            $this->redirect('movimientos-inventario');
            return;
        }
        
        $id = (int)$_POST['id'];
        
        // Verificar que el movimiento existe y es válido
        $movimiento_original = $this->model->obtenerMovimientoPorId($id);
        
        if (!$movimiento_original) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Movimiento no encontrado',
                'icon' => 'error'
            ];
            $this->redirect('movimientos-inventario');
            return;
        }
        
        if ($movimiento_original['tipo_movimiento'] !== 'ENTRADA' || $movimiento_original['subtipo_movimiento'] !== 'OTRO') {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Este movimiento no es una entrada de tipo OTRO',
                'icon' => 'error'
            ];
            $this->redirect('movimientos-inventario&method=modificarOtroEntrada&id=' . $id);
            return;
        }
        
        if ($movimiento_original['estado'] !== 'Activo') {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'No se puede modificar un movimiento inactivo',
                'icon' => 'error'
            ];
            $this->redirect('movimientos-inventario&method=modificarOtroEntrada&id=' . $id);
            return;
        }
        
        try {
            // Preparar datos para la modificación
            $data = [
                'id_producto' => (int)$_POST['id_producto'],
                'cantidad' => (float)$_POST['cantidad'],
                'precio_unitario' => (float)$_POST['precio_unitario'],
                'fecha' => $_POST['fecha'],
                'observaciones' => $_POST['observaciones'] ?? null,
                'id_usuario' => $_SESSION['user_id']
            ];

            // Validaciones básicas
            if ($data['cantidad'] <= 0) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'La cantidad debe ser mayor a cero',
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario&method=modificarOtroEntrada&id=' . $id);
                return;
            }

            if ($data['precio_unitario'] <= 0) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'El precio unitario debe ser mayor a cero',
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario&method=modificarOtroEntrada&id=' . $id);
                return;
            }

            // Modificar la entrada OTRO con ajuste
            if ($this->model->modificarOtroEntradaConAjuste($id, $data)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Entrada OTRO modificada correctamente. Se ha creado un nuevo registro de ajuste.',
                    'icon' => 'success'
                ];
                $this->redirect('movimientos-inventario');
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al modificar entrada OTRO: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario&method=modificarOtroEntrada&id=' . $id);
            }
        } catch (Exception $e) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Error al procesar la modificación: ' . $e->getMessage(),
                'icon' => 'error'
            ];
            $this->redirect('movimientos-inventario&method=modificarOtroEntrada&id=' . $id);
        }
    }

    public function modificarOtroSalida()
    {
        $this->checkSession();
        
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'ID de movimiento no especificado',
                'icon' => 'error'
            ];
            $this->redirect('movimientos-inventario');
            return;
        }
        
        $id = (int)$_GET['id'];
        
        // Obtener el movimiento
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
        
        // Verificar que sea un movimiento de tipo SALIDA y subtipo OTRO
        if ($movimiento['tipo_movimiento'] !== 'SALIDA' || $movimiento['subtipo_movimiento'] !== 'OTRO') {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Este movimiento no es una salida de tipo OTRO',
                'icon' => 'error'
            ];
            $this->redirect('movimientos-inventario');
            return;
        }
        
        // Verificar que el movimiento esté activo
        if ($movimiento['estado'] !== 'Activo') {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'No se puede modificar un movimiento inactivo',
                'icon' => 'error'
            ];
            $this->redirect('movimientos-inventario');
            return;
        }
        
        // Obtener productos activos
        $productos = $this->productoModel->obtenerProductosActivos();
        
        // Obtener stock actual del producto
        $stock_actual = $this->productoModel->obtenerStockProducto($movimiento['id_producto']);
        
        $this->loadView('movimientos_inventario/modificar_otro_salida', [
            'movimiento' => $movimiento,
            'productos' => $productos,
            'stock_actual' => $stock_actual
        ]);
    }
    
    public function actualizarOtroSalida()
    {
        $this->checkSession();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('movimientos-inventario');
            return;
        }
        
        if (!isset($_POST['id'])) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'ID de movimiento no especificado',
                'icon' => 'error'
            ];
            $this->redirect('movimientos-inventario');
            return;
        }
        
        $id = (int)$_POST['id'];
        
        // Verificar que el movimiento existe y es válido
        $movimiento_original = $this->model->obtenerMovimientoPorId($id);
        
        if (!$movimiento_original) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Movimiento no encontrado',
                'icon' => 'error'
            ];
            $this->redirect('movimientos-inventario');
            return;
        }
        
        if ($movimiento_original['tipo_movimiento'] !== 'SALIDA' || $movimiento_original['subtipo_movimiento'] !== 'OTRO') {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Este movimiento no es una salida de tipo OTRO',
                'icon' => 'error'
            ];
            $this->redirect('movimientos-inventario&method=modificarOtroSalida&id=' . $id);
            return;
        }
        
        if ($movimiento_original['estado'] !== 'Activo') {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'No se puede modificar un movimiento inactivo',
                'icon' => 'error'
            ];
            $this->redirect('movimientos-inventario&method=modificarOtroSalida&id=' . $id);
            return;
        }
        
        try {
            // Preparar datos para la modificación
            $data = [
                'id_producto' => (int)$_POST['id_producto'],
                'cantidad' => (float)$_POST['cantidad'],
                'precio_unitario' => (float)$_POST['precio_unitario'],
                'fecha' => $_POST['fecha'],
                'observaciones' => $_POST['observaciones'] ?? null,
                'id_usuario' => $_SESSION['user_id']
            ];

            // Validaciones básicas
            if ($data['cantidad'] <= 0) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'La cantidad debe ser mayor a cero',
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario&method=modificarOtroSalida&id=' . $id);
                return;
            }

            if ($data['precio_unitario'] <= 0) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'El precio unitario debe ser mayor a cero',
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario&method=modificarOtroSalida&id=' . $id);
                return;
            }

            // Modificar la salida OTRO con ajuste
            if ($this->model->modificarOtroSalidaConAjuste($id, $data)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Salida OTRO modificada correctamente. Se ha creado un nuevo registro de ajuste.',
                    'icon' => 'success'
                ];
                $this->redirect('movimientos-inventario');
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al modificar salida OTRO: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
                $this->redirect('movimientos-inventario&method=modificarOtroSalida&id=' . $id);
            }
        } catch (Exception $e) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Error al procesar la modificación: ' . $e->getMessage(),
                'icon' => 'error'
            ];
            $this->redirect('movimientos-inventario&method=modificarOtroSalida&id=' . $id);
        }
    }

    public function obtenerProductosPorProveedor()
    {
        $this->checkSession();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cedula_proveedor'])) {
            require_once ROOT_PATH . 'models/ProductoProveedorModel.php';
            $ppModel = new ProductoProveedorModel();
            
            $productos = $ppModel->obtenerProductosPorProveedor($_POST['cedula_proveedor']);
            
            header('Content-Type: application/json');
            echo json_encode($productos);
            exit();
        }
        
        header('Content-Type: application/json');
        echo json_encode([]);
        exit();
    }

    public function buscarPorTransaccion()
    {
        $this->checkSession();
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['numero_transaccion'])) {
            $numero_transaccion = trim($_POST['numero_transaccion']);
            
            if (empty($numero_transaccion)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Número de transacción requerido'
                ]);
                exit();
            }
            
            try {
                $movimiento = $this->model->buscarMovimientoPorTransaccion($numero_transaccion);
                
                if ($movimiento) {
                    echo json_encode([
                        'success' => true,
                        'movimiento' => $movimiento
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'No se encontró ningún movimiento con el número de transacción: ' . $numero_transaccion
                    ]);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al buscar el movimiento: ' . $e->getMessage()
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
        }
        
        exit();
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
