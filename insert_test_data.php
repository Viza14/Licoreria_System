<?php
session_start();

// Incluir archivos necesarios
define('ROOT_PATH', __DIR__ . '/');
require_once ROOT_PATH . 'config/database.php';

echo "<h1>Insertar Datos de Prueba</h1>";

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Verificar si ya existen productos
    $query = "SELECT COUNT(*) as total FROM productos WHERE estatus = 'ACTIVO'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['total'] == 0) {
        echo "<p>No hay productos activos. Insertando productos de prueba...</p>";
        
        // Insertar productos de prueba
        $productos = [
            ['descripcion' => 'Producto Prueba 1', 'precio' => 10.50, 'cantidad' => 100],
            ['descripcion' => 'Producto Prueba 2', 'precio' => 25.00, 'cantidad' => 50],
            ['descripcion' => 'Producto Prueba 3', 'precio' => 15.75, 'cantidad' => 75]
        ];
        
        foreach ($productos as $producto) {
            $query = "INSERT INTO productos (descripcion, precio, cantidad, estatus, fecha_creacion) 
                      VALUES (:descripcion, :precio, :cantidad, 'ACTIVO', NOW())";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':descripcion', $producto['descripcion']);
            $stmt->bindParam(':precio', $producto['precio']);
            $stmt->bindParam(':cantidad', $producto['cantidad']);
            $stmt->execute();
            echo "<p>✓ Producto insertado: " . $producto['descripcion'] . "</p>";
        }
    }
    
    // Verificar si ya existen usuarios
    $query = "SELECT COUNT(*) as total FROM usuarios WHERE estatus = 'ACTIVO'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['total'] == 0) {
        echo "<p>No hay usuarios activos. Insertando usuario de prueba...</p>";
        
        $query = "INSERT INTO usuarios (username, password, nombre, apellido, email, rol, estatus, fecha_creacion) 
                  VALUES ('admin', :password, 'Admin', 'Test', 'admin@test.com', 'ADMINISTRADOR', 'ACTIVO', NOW())";
        $stmt = $conn->prepare($query);
        $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $password_hash);
        $stmt->execute();
        echo "<p>✓ Usuario admin insertado (password: admin123)</p>";
    }
    
    // Obtener el primer producto y usuario para crear movimientos de prueba
    $query = "SELECT id FROM productos WHERE estatus = 'ACTIVO' LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $query = "SELECT id FROM usuarios WHERE estatus = 'ACTIVO' LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($producto && $usuario) {
        // Crear movimientos de prueba
        $movimientos = [
            [
                'tipo' => 'ENTRADA',
                'subtipo' => 'OTRO',
                'numero' => 'TXN-ENTRADA-OTRO-001',
                'cantidad' => 10,
                'precio' => 12.50
            ],
            [
                'tipo' => 'SALIDA',
                'subtipo' => 'OTRO',
                'numero' => 'TXN-SALIDA-OTRO-001',
                'cantidad' => 5,
                'precio' => 15.00
            ],
            [
                'tipo' => 'SALIDA',
                'subtipo' => 'PERDIDA',
                'numero' => 'TXN-PERDIDA-001',
                'cantidad' => 2,
                'precio' => 10.50
            ]
        ];
        
        foreach ($movimientos as $mov) {
            // Verificar si ya existe
            $query = "SELECT COUNT(*) as total FROM movimientos_inventario WHERE numero_transaccion = :numero";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':numero', $mov['numero']);
            $stmt->execute();
            $exists = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($exists['total'] == 0) {
                $query = "INSERT INTO movimientos_inventario 
                          (numero_transaccion, tipo_movimiento, subtipo_movimiento, id_producto, cantidad, 
                           precio_unitario, id_usuario, fecha_movimiento, observaciones, estatus, fecha_creacion) 
                          VALUES (:numero, :tipo, :subtipo, :id_producto, :cantidad, :precio, :id_usuario, 
                                  NOW(), 'Movimiento de prueba', 'ACTIVO', NOW())";
                
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':numero', $mov['numero']);
                $stmt->bindParam(':tipo', $mov['tipo']);
                $stmt->bindParam(':subtipo', $mov['subtipo']);
                $stmt->bindParam(':id_producto', $producto['id']);
                $stmt->bindParam(':cantidad', $mov['cantidad']);
                $stmt->bindParam(':precio', $mov['precio']);
                $stmt->bindParam(':id_usuario', $usuario['id']);
                $stmt->execute();
                
                echo "<p>✓ Movimiento insertado: " . $mov['tipo'] . " - " . $mov['subtipo'] . " (" . $mov['numero'] . ")</p>";
            } else {
                echo "<p>⚠ Movimiento ya existe: " . $mov['numero'] . "</p>";
            }
        }
    }
    
    echo "<hr>";
    echo "<h2>Movimientos disponibles para prueba:</h2>";
    
    $query = "SELECT numero_transaccion, tipo_movimiento, subtipo_movimiento, fecha_movimiento 
              FROM movimientos_inventario 
              WHERE estatus = 'ACTIVO' 
              ORDER BY fecha_movimiento DESC";
    
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
        
        echo "<hr>";
        echo "<h3>URLs de prueba:</h3>";
        foreach ($transacciones as $txn) {
            $tipo_subtipo = $txn['tipo_movimiento'] . '_' . $txn['subtipo_movimiento'];
            echo "<p><strong>" . $txn['numero_transaccion'] . "</strong> (" . $tipo_subtipo . ")</p>";
            echo "<p>Prueba en: <a href='index.php?action=movimientos-inventario&method=registrarAjuste' target='_blank'>Registrar Ajuste</a></p>";
        }
    } else {
        echo "<p>No hay transacciones en la base de datos</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>