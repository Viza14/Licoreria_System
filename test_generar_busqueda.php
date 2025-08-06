<?php
// Simular el entorno de la aplicación
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_nombre'] = 'Admin Test';

define('ROOT_PATH', __DIR__ . '/');

// Cargar configuración de URL
require_once __DIR__ . '/config/url_config.php';
define('BASE_URL', getBaseUrl());

// Incluir archivos necesarios
require_once 'config/database.php';
require_once 'models/MovimientoInventarioModel.php';

$model = new MovimientoInventarioModel();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Búsqueda - Generar Movimiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Prueba de Búsqueda de Transacciones</h2>
        <p class="text-muted">Esta página simula la funcionalidad de búsqueda desde "Generar Movimiento"</p>
        
        <div class="card">
            <div class="card-header">
                <h5>Buscar Transacción para Modificar</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <input type="text" id="numero_transaccion" class="form-control" 
                               placeholder="Ingrese el número de transacción">
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-primary" onclick="buscarMovimiento()">
                            Buscar Movimiento
                        </button>
                    </div>
                </div>
                
                <div class="mt-3">
                    <h6>Transacciones de prueba disponibles:</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <button class="btn btn-outline-info btn-sm mb-2" 
                                    onclick="$('#numero_transaccion').val('TXN-ENTRADA-OTRO-001'); buscarMovimiento();">
                                TXN-ENTRADA-OTRO-001 (Entrada OTRO)
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-warning btn-sm mb-2" 
                                    onclick="$('#numero_transaccion').val('TXN-SALIDA-OTRO-001'); buscarMovimiento();">
                                TXN-SALIDA-OTRO-001 (Salida OTRO)
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-danger btn-sm mb-2" 
                                    onclick="$('#numero_transaccion').val('TXN-PERDIDA-001'); buscarMovimiento();">
                                TXN-PERDIDA-001 (Pérdida)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5>Resultado de la Búsqueda</h5>
            </div>
            <div class="card-body">
                <div id="resultado-busqueda">
                    <p class="text-muted">Los resultados aparecerán aquí...</p>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5>Transacciones Disponibles</h5>
            </div>
            <div class="card-body">
                <?php
                try {
                    $movimientos = $model->obtenerUltimosMovimientos(10);
                    if (!empty($movimientos)) {
                        echo '<div class="table-responsive">';
                        echo '<table class="table table-sm">';
                        echo '<thead><tr><th>Transacción</th><th>Tipo</th><th>Subtipo</th><th>Producto</th><th>Fecha</th></tr></thead>';
                        echo '<tbody>';
                        foreach ($movimientos as $mov) {
                            echo '<tr>';
                            echo '<td><code>' . htmlspecialchars($mov['numero_transaccion']) . '</code></td>';
                            echo '<td><span class="badge bg-' . ($mov['tipo_movimiento'] == 'ENTRADA' ? 'success' : 'danger') . '">' . $mov['tipo_movimiento'] . '</span></td>';
                            echo '<td>' . $mov['subtipo_movimiento'] . '</td>';
                            echo '<td>' . htmlspecialchars($mov['producto_descripcion'] ?? 'N/A') . '</td>';
                            echo '<td>' . date('d/m/Y H:i', strtotime($mov['fecha_movimiento'])) . '</td>';
                            echo '</tr>';
                        }
                        echo '</tbody></table>';
                        echo '</div>';
                    } else {
                        echo '<p class="text-muted">No hay movimientos disponibles.</p>';
                    }
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger">Error al obtener movimientos: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <script>
    function buscarMovimiento() {
        const numeroTransaccion = $('#numero_transaccion').val().trim();
        
        if (!numeroTransaccion) {
            Swal.fire({
                title: 'Error',
                text: 'Debe ingresar un número de transacción',
                icon: 'error'
            });
            return;
        }
        
        // Mostrar loading
        Swal.fire({
            title: 'Buscando...',
            text: 'Buscando el movimiento especificado',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Realizar búsqueda usando el mismo método que generar.php
        fetch('<?= BASE_URL ?>index.php?action=movimientos-inventario&method=buscarPorTransaccion', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'numero_transaccion=' + encodeURIComponent(numeroTransaccion)
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            
            // Mostrar resultado en la página
            const resultadoDiv = document.getElementById('resultado-busqueda');
            
            if (data.success) {
                const movimiento = data.movimiento;
                
                // Mostrar información del movimiento encontrado
                resultadoDiv.innerHTML = `
                    <div class="alert alert-success">
                        <h6>✅ Movimiento Encontrado</h6>
                        <p><strong>Transacción:</strong> ${movimiento.numero_transaccion}</p>
                        <p><strong>Tipo:</strong> ${movimiento.tipo_movimiento}</p>
                        <p><strong>Subtipo:</strong> ${movimiento.subtipo_movimiento}</p>
                        <p><strong>Producto:</strong> ${movimiento.producto_descripcion || 'N/A'}</p>
                        <p><strong>Fecha:</strong> ${new Date(movimiento.fecha_movimiento).toLocaleString()}</p>
                    </div>
                `;
                
                // Determinar redirección según la lógica corregida
                let redirectUrl = '';
                let redirectText = '';
                
                if (movimiento.tipo_referencia === 'VENTA') {
                    redirectUrl = '<?= BASE_URL ?>index.php?action=movimientos-inventario&method=modificarVenta&id=' + movimiento.id_referencia;
                    redirectText = 'Modificar Venta';
                } else if (movimiento.tipo_movimiento === 'ENTRADA') {
                    if (movimiento.subtipo_movimiento === 'COMPRA') {
                        redirectUrl = '<?= BASE_URL ?>index.php?action=movimientos-inventario&method=modificarCompra&id=' + movimiento.id;
                        redirectText = 'Modificar Compra';
                    } else if (movimiento.subtipo_movimiento === 'OTRO') {
                        redirectUrl = '<?= BASE_URL ?>index.php?action=movimientos-inventario&method=modificarOtroEntrada&id=' + movimiento.id;
                        redirectText = 'Modificar Entrada OTRO';
                    } else {
                        redirectUrl = '<?= BASE_URL ?>index.php?action=movimientos-inventario&method=editar&id=' + movimiento.id;
                        redirectText = 'Editar Movimiento';
                    }
                } else if (movimiento.tipo_movimiento === 'SALIDA') {
                    if (movimiento.subtipo_movimiento === 'PERDIDA') {
                        redirectUrl = '<?= BASE_URL ?>index.php?action=movimientos-inventario&method=modificarPerdida&id=' + movimiento.id;
                        redirectText = 'Modificar Pérdida';
                    } else if (movimiento.subtipo_movimiento === 'OTRO') {
                        redirectUrl = '<?= BASE_URL ?>index.php?action=movimientos-inventario&method=modificarOtroSalida&id=' + movimiento.id;
                        redirectText = 'Modificar Salida OTRO';
                    } else {
                        redirectUrl = '<?= BASE_URL ?>index.php?action=movimientos-inventario&method=editar&id=' + movimiento.id;
                        redirectText = 'Editar Movimiento';
                    }
                } else {
                    redirectUrl = '<?= BASE_URL ?>index.php?action=movimientos-inventario&method=registrarAjuste&numero_transaccion=' + encodeURIComponent(numeroTransaccion);
                    redirectText = 'Registrar Ajuste';
                }
                
                // Agregar botón de redirección
                resultadoDiv.innerHTML += `
                    <div class="mt-3">
                        <p><strong>Redirección:</strong> ${redirectText}</p>
                        <a href="${redirectUrl}" class="btn btn-primary" target="_blank">
                            🔗 ${redirectText}
                        </a>
                        <p class="text-muted mt-2"><small>URL: ${redirectUrl}</small></p>
                    </div>
                `;
                
                // Mostrar confirmación
                Swal.fire({
                    title: 'Movimiento Encontrado',
                    text: `Se redirigirá a: ${redirectText}`,
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'Ir Ahora',
                    cancelButtonText: 'Ver Detalles'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.open(redirectUrl, '_blank');
                    }
                });
                
            } else {
                resultadoDiv.innerHTML = `
                    <div class="alert alert-warning">
                        <h6>❌ No Encontrado</h6>
                        <p>${data.message || 'No se encontró un movimiento con ese número de transacción'}</p>
                    </div>
                `;
                
                Swal.fire({
                    title: 'No encontrado',
                    text: data.message || 'No se encontró un movimiento con ese número de transacción',
                    icon: 'warning'
                });
            }
        })
        .catch(error => {
            Swal.close();
            console.error('Error:', error);
            
            document.getElementById('resultado-busqueda').innerHTML = `
                <div class="alert alert-danger">
                    <h6>💥 Error</h6>
                    <p>Ocurrió un error al buscar el movimiento: ${error.message}</p>
                </div>
            `;
            
            Swal.fire({
                title: 'Error',
                text: 'Ocurrió un error al buscar el movimiento',
                icon: 'error'
            });
        });
    }
    </script>
</body>
</html>