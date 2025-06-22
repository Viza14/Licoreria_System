<?php
class DashboardController {
    private $productoModel;
    private $clienteModel;
    private $ventaModel;
    private $reporteModel;

    public function __construct() {
        require_once ROOT_PATH . 'models/ProductoModel.php';
        require_once ROOT_PATH . 'models/ClienteModel.php';
        require_once ROOT_PATH . 'models/VentaModel.php';
        require_once ROOT_PATH . 'models/ReporteModel.php';
        
        $this->productoModel = new ProductoModel();
        $this->clienteModel = new ClienteModel();
        $this->ventaModel = new VentaModel();
        $this->reporteModel = new ReporteModel();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login');
            return;
        }

        try {
            // Get today's income as float
            $ingresosHoy = (float)$this->ventaModel->calcularIngresosHoy();
            
            // Get products by day of week
            $productosPorDia = $this->reporteModel->obtenerProductosPorDiaSemana();
            
            // Prepare data for daily chart
            $productosPorDiaFormateado = [];
            $nombresDias = [
                'Monday' => 'LUN', 
                'Tuesday' => 'MAR', 
                'Wednesday' => 'MIE', 
                'Thursday' => 'JUE', 
                'Friday' => 'VIE', 
                'Saturday' => 'SAB', 
                'Sunday' => 'DOM'
            ];
            
            foreach ($productosPorDia as $dia) {
                $nombreDia = $nombresDias[$dia['dia_semana']];
                $productosPorDiaFormateado[$nombreDia] = $dia['total_productos'];
            }
            
            // Prepare data for view
            $data = [
                'totalProductos' => $this->productoModel->contarProductos() ?? 0,
                'totalClientes' => $this->clienteModel->contarClientes() ?? 0,
                'ventasHoy' => $this->ventaModel->contarVentasHoy() ?? 0,
                'ingresosHoy' => is_numeric($ingresosHoy) ? $ingresosHoy : 0.00,
                'ventasMensuales' => $this->reporteModel->obtenerVentasMensuales() ?? [],
                'topProductos' => $this->reporteModel->obtenerProductosMasVendidos() ?? [],
                'ultimasVentas' => $this->ventaModel->obtenerVentasRecientes() ?? [],
                'productosPorDia' => $productosPorDiaFormateado,
                'userNombre' => $_SESSION['user_nombre'] ?? 'Usuario'
            ];
            
            $this->loadView('dashboard', $data);
            
        } catch (Exception $e) {
            error_log('Error en Dashboard: ' . $e->getMessage());
            $errorData = [
                'message' => 'Ocurrió un error al cargar el dashboard.',
                'userNombre' => $_SESSION['user_nombre'] ?? 'Usuario'
            ];
            $this->loadView('error', $errorData);
        }
    }

    private function redirect($action) {
        header("Location: " . BASE_URL . "index.php?action=$action");
        exit();
    }

    private function loadView($view, $data = []) {
        extract($data);
        require ROOT_PATH . 'views/layouts/header.php';
        require ROOT_PATH . 'views/' . $view . '.php';
        require ROOT_PATH . 'views/layouts/footer.php';
    }
}
?>