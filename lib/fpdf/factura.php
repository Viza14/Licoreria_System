<?php
require_once ROOT_PATH . 'vendor/setasign/fpdf/fpdf.php';

class Factura extends FPDF {
    function __construct() {
        parent::__construct();
        $this->SetFont('Helvetica','',10);
    }

    function Header() {
        // Logo y título
        $this->SetFont('Helvetica', '', 12);
        $this->Cell(0, 6, utf8_decode('Licoreria El Manguito, C.A'), 0, 1, 'C');
        $this->SetFont('Helvetica', '', 10);
        $this->Cell(0, 5, 'J-6113479-4', 0, 1, 'C');
        $this->Cell(0, 5, utf8_decode('Av. Noviembre Barrio La Manguita Calle la Esperanza, Valencia Estado Carabobo'), 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer() {
        // No footer needed
    }

    function datosFactura($cliente, $fecha, $numero) {
        $this->SetFont('Helvetica', '', 10);
        
        // Datos del vendedor y cliente
        $this->Cell(75, 5, 'Vendedor: ' . utf8_decode($_SESSION['usuario']['nombre']), 0, 1);
        
        $this->Cell(25, 5, 'Cliente:', 0, 0);
        $this->Cell(100, 5, utf8_decode($cliente), 0, 1);
        
        $this->Cell(25, 5, 'R.I.F.:', 0, 0);
        $this->Cell(100, 5, 'J501995303', 0, 1);
        
        $this->Cell(25, 5, utf8_decode('Dirección:'), 0, 0);
        $this->Cell(100, 5, 'LA MANGUITA', 0, 1);
        
        $this->Ln(5);
        
        // Nota de entrega y fecha
        $this->Cell(60, 5, 'Nota de Entrega:', 0, 0);
        $this->Cell(60, 5, '', 0, 1, 'R');
        $this->Cell(60, 5, 'NE' . str_pad($numero, 7, '0', STR_PAD_LEFT), 0, 1);
        
        $this->Cell(60, 5, 'Fecha: ' . date('d/m/Y', strtotime($fecha)), 0, 0);
        $this->Cell(60, 5, 'Hora: ' . date('h:i:s A', strtotime($fecha)), 0, 1);
        
        $this->Ln(5);
    }

    function tablaProductos($productos) {
        $this->SetFont('Helvetica', '', 10);
        
        foreach($productos as $producto) {
            // Cantidad y precio unitario
            $this->Cell(15, 5, number_format($producto['cantidad'], 2), 0, 0);
            $this->Cell(15, 5, 'x', 0, 0, 'C');
            $this->Cell(30, 5, 'Bs ' . number_format($producto['precio'], 2), 0, 1);
            
            // Descripción del producto
            $this->Cell(120, 5, utf8_decode($producto['descripcion']), 0, 0);
            $this->Cell(30, 5, 'Bs ' . number_format($producto['cantidad'] * $producto['precio'], 2), 0, 1, 'R');
            
            // Línea separadora
            $this->Cell(0, 2, '', 'B', 1);
        }
        
        $this->Ln(5);
        
        // Totales
        $total = 0;
        foreach($productos as $producto) {
            $total += $producto['cantidad'] * $producto['precio'];
        }
        
        $this->Cell(30, 5, 'SubTotal:', 0, 0);
        $this->Cell(120, 5, 'Bs ' . number_format($total, 2), 0, 1, 'R');
        
        // Formas de pago
        if (isset($_SESSION['pagos']) && is_array($_SESSION['pagos'])) {
            foreach ($_SESSION['pagos'] as $pago) {
                $this->Cell(30, 5, utf8_decode($pago['forma_pago']), 0, 0);
                $this->Cell(120, 5, 'Bs ' . number_format($pago['monto'], 2), 0, 1, 'R');
            }
        }
        
        // Total final
        $this->Cell(30, 5, 'TOTAL', 0, 0);
        $this->Cell(120, 5, 'Bs ' . number_format($total, 2), 0, 1, 'R');
    }
}