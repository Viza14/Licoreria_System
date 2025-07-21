<?php
class ReporteController
{
    private $model;
    private $productoModel;
    private $usuarioModel;
    private $clienteModel;

    public function __construct()
    {
        require_once ROOT_PATH . 'models/ReporteModel.php';
        require_once ROOT_PATH . 'models/ProductoModel.php';
        require_once ROOT_PATH . 'models/UsuarioModel.php';
        require_once ROOT_PATH . 'models/ClienteModel.php';
        
        $this->model = new ReporteModel();
        $this->productoModel = new ProductoModel();
        $this->usuarioModel = new UsuarioModel();
        $this->clienteModel = new ClienteModel();
    }

    public function index()
    {
        $this->checkSession();
        $this->loadView('reportes/index');
    }

    public function inventario()
    {
        $this->checkSession();
        
        // Obtener parámetros de paginación
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $por_pagina = 10;
        
        // Función para normalizar texto (eliminar acentos y convertir a minúsculas)
        function normalizarTexto($texto) {
            if (!$texto) return null;
            return strtolower(trim(
                str_replace(
                    ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ'],
                    ['a', 'e', 'i', 'o', 'u', 'u', 'n', 'A', 'E', 'I', 'O', 'U', 'U', 'N'],
                    $texto
                )
            ));
        }
        
        $filtros = [];
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
            // Procesar búsqueda y filtros para solicitudes AJAX
            $filtros = [
                'busqueda' => isset($_GET['busqueda']) ? normalizarTexto($_GET['busqueda']) : null,
                'id_categoria' => $_GET['id_categoria'] ?? null,
                'id_tipo_categoria' => $_GET['id_tipo_categoria'] ?? null,
                'estado_stock' => $_GET['estado_stock'] ?? null
            ];
            
            $resultado = $this->model->generarReporteInventario($filtros, $pagina, $por_pagina);
            
            // Devolver respuesta JSON
            header('Content-Type: application/json');
            echo json_encode([
                'reporte' => $resultado['datos'],
                'pagina_actual' => $resultado['pagina_actual'],
                'total_paginas' => $resultado['total_paginas'],
                'total_registros' => $resultado['total_registros']
            ]);
            exit();
        } else {
            // Procesar filtros para solicitudes normales
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $filtros = [
                    'id_categoria' => $_POST['id_categoria'] ?? null,
                    'id_tipo_categoria' => $_POST['id_tipo_categoria'] ?? null,
                    'estado_stock' => $_POST['estado_stock'] ?? null
                ];
                // Redirigir a GET con los filtros
                $queryString = http_build_query(['filtros' => $filtros]);
                header("Location: " . BASE_URL . "index.php?action=reportes&method=inventario&" . $queryString);
                exit();
            } elseif (isset($_GET['filtros'])) {
                $filtros = $_GET['filtros'];
            }
            
            $resultado = $this->model->generarReporteInventario($filtros, $pagina, $por_pagina);
            $categorias = $this->productoModel->obtenerCategorias();
            $tiposCategoria = $this->productoModel->obtenerTiposCategoria();
            
            $this->loadView('reportes/inventario', [
                'reporte' => $resultado['datos'],
                'categorias' => $categorias,
                'tiposCategoria' => $tiposCategoria,
                'filtros' => $filtros,
                'pagina_actual' => $resultado['pagina_actual'],
                'total_paginas' => $resultado['total_paginas'],
                'total_registros' => $resultado['total_registros']
            ]);
        }
    }

    public function detalleVentas()
    {
        $this->checkSession();
        
        $filtros = [];
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $por_pagina = 10;

        // Obtener filtros de POST o GET
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $filtros = [
                'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
                'fecha_fin' => $_POST['fecha_fin'] ?? null,
                'id_producto' => $_POST['id_producto'] ?? null
            ];
            // Redirigir a GET con los filtros
            $queryString = http_build_query(['filtros' => $filtros]);
            header("Location: " . BASE_URL . "index.php?action=reportes&method=detalleVentas&" . $queryString);
            exit();
        } elseif (isset($_GET['filtros'])) {
            $filtros = $_GET['filtros'];
        }
        
        $resultado = $this->model->generarDetalleVentas($filtros, $pagina, $por_pagina);
        $productos = $this->productoModel->obtenerProductosActivos();
        
        $this->loadView('reportes/detalle_ventas', [
            'reporte' => $resultado['datos'],
            'productos' => $productos,
            'filtros' => $filtros,
            'pagina_actual' => $resultado['pagina_actual'],
            'total_paginas' => $resultado['total_paginas'],
            'total_registros' => $resultado['total_registros']
        ]);
    }

    public function resumenVentas()
    {
        $this->checkSession();
        
        $filtros = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate and sanitize dates
            $fechaInicio = filter_input(INPUT_POST, 'fecha_inicio', FILTER_SANITIZE_STRING);
            $fechaFin = filter_input(INPUT_POST, 'fecha_fin', FILTER_SANITIZE_STRING);
            
            // Validate date format
            if ($this->validateDate($fechaInicio) && $this->validateDate($fechaFin)) {
                $filtros = [
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin
                ];
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error de validación',
                    'text' => 'Las fechas ingresadas no son válidas',
                    'icon' => 'error'
                ];
            }
        }
        
        $reporte = $this->model->obtenerResumenVentas($filtros);
        
        $this->loadView('reportes/resumen_ventas', [
            'reporte' => $reporte,
            'filtros' => $filtros
        ]);
    }

    public function productosMasVendidos()
    {
        $this->checkSession();
        
        $filtros = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $filtros = [
                'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
                'fecha_fin' => $_POST['fecha_fin'] ?? null,
                'id_categoria' => $_POST['id_categoria'] ?? null
            ];
        }
        
        $reporte = $this->model->obtenerProductosMasVendidos($filtros);
        $categorias = $this->productoModel->obtenerCategorias();
        
        $this->loadView('reportes/productos_mas_vendidos', [
            'reporte' => $reporte,
            'categorias' => $categorias,
            'filtros' => $filtros
        ]);
    }

    public function exportarInventario()
    {
        $this->checkSession();
        
        $filtros = [
            'id_categoria' => $_GET['id_categoria'] ?? null,
            'id_tipo_categoria' => $_GET['id_tipo_categoria'] ?? null,
            'estado_stock' => $_GET['estado_stock'] ?? null
        ];
        
        $reporte = $this->model->generarReporteInventario($filtros);
        
        // Generate CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=reporte_inventario_' . date('Ymd') . '.csv');
        
        $output = fopen('php://output', 'w');
        
        // Headers
        fputcsv($output, [
            'ID', 
            'Producto', 
            'Categoría', 
            'Tipo Categoría', 
            'Stock Actual', 
            'Stock Mínimo', 
            'Stock Máximo', 
            'Estado Stock', 
            'Precio Venta', 
            'Precio Compra Mínimo', 
            'Valor Total'
        ]);
        
        // Data
        foreach ($reporte as $row) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit();
    }

    public function exportarVentas()
    {
        $this->checkSession();
        
        $filtros = [
            'fecha_inicio' => $_GET['fecha_inicio'] ?? null,
            'fecha_fin' => $_GET['fecha_fin'] ?? null,
            'id_usuario' => $_GET['id_usuario'] ?? null,
            'cedula_cliente' => $_GET['cedula_cliente'] ?? null
        ];
        
        $reporte = $this->model->generarReporteVentas($filtros);
        
        // Generate CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=reporte_ventas_' . date('Ymd') . '.csv');
        
        $output = fopen('php://output', 'w');
        
        // Headers
        fputcsv($output, [
            'ID Venta', 
            'Fecha', 
            'Cliente', 
            'Cédula', 
            'Vendedor', 
            'Monto Total', 
            'Cant. Productos', 
            'Productos'
        ]);
        
        // Data
        foreach ($reporte as $row) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit();
    }

    // Helper methods
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

    // Validate date format YYYY-MM-DD
    private function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}
