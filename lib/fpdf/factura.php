<?php
require_once ROOT_PATH . 'vendor/setasign/fpdf/fpdf.php';

class Factura extends FPDF
{
    function __construct()
    {
        parent::__construct();
        $this->SetFont('Helvetica', '', 10);
    }

    function Header()
    {
        // Logo y título
        $this->Image('c:/xampp/htdocs/licoreria/assets/img/botella2.png', 90, 8, 30);
        $this->Ln(32);
        $this->SetFont('Helvetica', '', 12);
        $this->Cell(0, 6, utf8_decode('Licoreria El Manguito, C.A'), 0, 1, 'C');
        $this->SetFont('Helvetica', '', 10);
        $this->Cell(0, 5, 'RIF: J-6113479-4', 0, 1, 'C');
        $this->Cell(0, 5, utf8_decode('Av. Noviembre Barrio La Manguita'), 0, 1, 'C');
        $this->Cell(0, 5, 'Valencia, Edo. Carabobo', 0, 1, 'C');
        $this->Cell(0, 5, utf8_decode('Teléfono: 0412-4212523'), 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-20);
        $this->SetFont('Helvetica', '', 10);
        $this->Cell(0, 5, utf8_decode('¡Gracias por su compra!'), 0, 1, 'C');
        $this->Cell(0, 5, 'Vuelva pronto', 0, 1, 'C');
    }

    function datosFactura($cliente, $fecha, $numero)
    {
        date_default_timezone_set('America/Caracas'); // Establecer zona horaria de Venezuela
        $this->SetFont('Helvetica', 'B', 10);
        $this->Cell(0, 5, 'Nota #: ' . str_pad($numero, 6, '0', STR_PAD_LEFT), 0, 1);

        // Fecha y hora
        $this->Cell(0, 5, $fecha, 0, 1, 'R');

        $this->SetFont('Helvetica', '', 10);
        // Datos del cliente
        $this->Cell(25, 5, 'Cliente:', 0, 0);
        $this->Cell(100, 5, utf8_decode($cliente), 0, 1);

        $this->Cell(25, 5, 'C.I.:', 0, 0);
        $this->Cell(100, 5, isset($_SESSION['cliente']['cedula']) ? $_SESSION['cliente']['cedula'] : '', 0, 1);

        $this->Cell(25, 5, utf8_decode('Dirección:'), 0, 0);
        $this->Cell(100, 5, isset($_SESSION['cliente']['direccion']) ? utf8_decode($_SESSION['cliente']['direccion']) : '', 0, 1);

        $this->Ln(5);

        // Encabezado de detalles
        $this->SetFont('Helvetica', 'B', 10);
        $this->Cell(0, 5, 'Nota de Entrega:', 0, 1, 'C');
        $this->Ln(5);
    }

    function tablaProductos($productos)
    {
        $this->SetFont('Helvetica', '', 10);

        $this->Cell(120, 5, utf8_decode('Descripción'), 0, 0);
        $this->Cell(30, 5, '', 0, 1, 'R');
        $this->Cell(0, 2, '', 'B', 1);

        foreach ($productos as $producto) {
            // Cantidad y precio unitario
            $this->Cell(15, 5, number_format($producto['cantidad'], 0), 0, 0);
            $this->Cell(15, 5, 'x', 0, 0, 'C');
            $this->Cell(30, 5, 'Bs ' . number_format($producto['precio'], 2), 0, 1);

            // Descripción del producto
            $this->Cell(120, 5, utf8_decode($producto['descripcion']), 0, 0);
            $this->Cell(30, 5, 'Bs ' . number_format($producto['cantidad'] * $producto['precio'], 2), 0, 1, 'R');

            $this->Cell(0, 2, '', 'B', 1);
        }

        $this->Ln(5);

        // Totales
        $total = 0;
        foreach ($productos as $producto) {
            $total += $producto['cantidad'] * $producto['precio'];
        }


        // Formas de pago
        if (isset($_SESSION['pagos']) && is_array($_SESSION['pagos'])) {
            foreach ($_SESSION['pagos'] as $pago) {
                $this->Cell(120, 5, utf8_decode($pago['forma_pago']), 0, 0);
                $this->Cell(30, 5, 'Bs ' . number_format($pago['monto'], 2), 0, 1, 'R');
            }
        }

        $this->Cell(120, 5, 'Subtotal:', 0, 0);
        $this->Cell(30, 5, 'Bs ' . number_format($total, 2), 0, 1, 'R');

        // Total final
        $this->SetFont('Helvetica', 'B', 10);
        $this->Cell(120, 5, 'TOTAL:', 0, 0);
        $this->Cell(30, 5, 'Bs ' . number_format($total, 2), 0, 1, 'R');
    }
}
