<?php
session_start();

// Simular datos de sesión
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'test';

// Incluir archivos necesarios
define('ROOT_PATH', __DIR__ . '/');
require_once ROOT_PATH . 'config/database.php';
require_once ROOT_PATH . 'models/MovimientoInventarioModel.php';

// Crear instancia del modelo
$model = new MovimientoInventarioModel();

// Buscar una transacción específica (puedes cambiar este número)
$numero_transaccion = 'TXN-20250803-001'; // Cambia por un número real de tu base de datos

echo "<h1>Prueba de Búsqueda de Transacción</h1>";
echo "<p>Buscando transacción: $numero_transaccion</p>";

try {
    $movimiento = $model->buscarMovimientoPorTransaccion($numero_transaccion);
    
    if ($movimiento) {
        echo "<h2>Movimiento encontrado:</h2>";
        echo "<pre>" . print_r($movimiento, true) . "</pre>";
        
        $tipo_subtipo = $movimiento['tipo_movimiento'] . '_' . $movimiento['subtipo_movimiento'];
        echo "<p><strong>Tipo_Subtipo:</strong> $tipo_subtipo</p>";
        
        switch ($tipo_subtipo) {
            case 'ENTRADA_OTRO':
                echo "<p style='color: green;'>✓ Debería redirigir a modificarOtroEntrada</p>";
                break;
            case 'SALIDA_OTRO':
                echo "<p style='color: green;'>✓ Debería redirigir a modificarOtroSalida</p>";
                break;
            case 'SALIDA_PERDIDA':
                echo "<p style='color: green;'>✓ Debería redirigir a modificarPerdida</p>";
                break;
            case 'SALIDA_VENTA':
                echo "<p style='color: green;'>✓ Debería redirigir a modificarVenta</p>";
                break;
            default:
                echo "<p style='color: blue;'>ℹ Debería mostrar formulario de ajuste</p>";
                break;
        }
    } else {
        echo "<p style='color: red;'>❌ No se encontró la transacción</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>Transacciones disponibles en la base de datos:</h2>";

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $query = "SELECT numero_transaccion, tipo_movimiento, subtipo_movimiento, fecha_movimiento 
              FROM movimientos_inventario 
              WHERE estatus = 'ACTIVO' 
              ORDER BY fecha_movimiento DESC 
              LIMIT 10";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $transacciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($transacciones) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Número Transacción</th><th>Tipo</th><th>Subtipo</th><th>Fecha</th></tr>";
        foreach ($transacciones as $txn) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($txn['numero_transaccion']) . "</td>";
            echo "<td>" . htmlspecialchars($txn['tipo_movimiento']) . "</td>";
            echo "<td>" . htmlspecialchars($txn['subtipo_movimiento']) . "</td>";
            echo "<td>" . htmlspecialchars($txn['fecha_movimiento']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay transacciones en la base de datos</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error al obtener transacciones: " . $e->getMessage() . "</p>";
}
?>