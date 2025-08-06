<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-exchange"></i> Generar Movimiento de Inventario</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?= BASE_URL ?>">Inicio</a></li>
                    <li><i class="fa fa-exchange"></i><a href="<?= BASE_URL ?>index.php?action=movimientos-inventario">Movimientos</a></li>
                    <li><i class="fa fa-plus"></i>Generar Movimiento</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <i class="fa fa-cogs"></i> Seleccionar Tipo de Movimiento
                    </header>
                    <div class="panel-body">
                        <!-- Selección de Tipo de Movimiento -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">
                                        <i class="fa fa-arrow-down text-success"></i> Tipo de Movimiento
                                    </label>
                                    <div class="radio-list">
                                        <label class="radio-inline">
                                            <input type="radio" name="tipo_movimiento" value="ENTRADA" id="entrada">
                                            <span class="text-success"><strong>ENTRADA</strong></span>
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="tipo_movimiento" value="SALIDA" id="salida">
                                            <span class="text-danger"><strong>SALIDA</strong></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Subtipos para ENTRADA -->
                        <div id="subtipos-entrada" class="subtipo-container" style="display: none;">
                            <div class="alert alert-success">
                                <i class="fa fa-arrow-down"></i> <strong>Movimientos de Entrada</strong>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Subtipo de Entrada</label>
                                        <div class="radio-list">
                                            <label class="radio-block">
                                                <input type="radio" name="subtipo_entrada" value="COMPRA">
                                                <i class="fa fa-shopping-cart text-primary"></i> <strong>Compra a Proveedor</strong>
                                                <small class="text-muted d-block">Registrar entrada de productos por compra a proveedores</small>
                                            </label>
                                            <label class="radio-block">
                                                <input type="radio" name="subtipo_entrada" value="AJUSTE">
                                                <i class="fa fa-edit text-warning"></i> <strong>Ajuste de Movimiento</strong>
                                                <small class="text-muted d-block">Modificar un movimiento existente por corrección</small>
                                            </label>
                                            <label class="radio-block">
                                                <input type="radio" name="subtipo_entrada" value="OTRO">
                                                <i class="fa fa-plus text-info"></i> <strong>Otro Motivo</strong>
                                                <small class="text-muted d-block">Entrada por otros motivos (devolución, donación, etc.)</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Subtipos para SALIDA -->
                        <div id="subtipos-salida" class="subtipo-container" style="display: none;">
                            <div class="alert alert-danger">
                                <i class="fa fa-arrow-up"></i> <strong>Movimientos de Salida</strong>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Subtipo de Salida</label>
                                        <div class="radio-list">
                                            <label class="radio-block">
                                                <input type="radio" name="subtipo_salida" value="PERDIDA">
                                                <i class="fa fa-exclamation-triangle text-danger"></i> <strong>Pérdida</strong>
                                                <small class="text-muted d-block">Registrar pérdida de productos (robo, daño, vencimiento, etc.)</small>
                                            </label>
                                            <label class="radio-block">
                                                <input type="radio" name="subtipo_salida" value="VENTA">
                                                <i class="fa fa-shopping-bag text-success"></i> <strong>Venta</strong>
                                                <small class="text-muted d-block">Salida por venta a cliente</small>
                                            </label>
                                            <label class="radio-block">
                                                <input type="radio" name="subtipo_salida" value="AJUSTE">
                                                <i class="fa fa-edit text-warning"></i> <strong>Ajuste de Movimiento</strong>
                                                <small class="text-muted d-block">Modificar un movimiento existente por corrección</small>
                                            </label>
                                            <label class="radio-block">
                                                <input type="radio" name="subtipo_salida" value="OTRO">
                                                <i class="fa fa-minus text-info"></i> <strong>Otro Motivo</strong>
                                                <small class="text-muted d-block">Salida por otros motivos (devolución, donación, etc.)</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Formulario para Ajuste -->
                        <div id="formulario-ajuste" style="display: none;">
                            <div class="alert alert-warning">
                                <i class="fa fa-edit"></i> <strong>Ajuste de Movimiento</strong>
                                <p>Ingrese el número de transacción del movimiento que desea modificar.</p>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="numero_transaccion">Número de Transacción</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
                                            <input type="text" class="form-control" id="numero_transaccion" 
                                                   placeholder="Ej: TXN00000001" maxlength="20">
                                        </div>
                                        <small class="text-muted">Formato: TXN seguido de números</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="button" class="btn btn-primary" onclick="buscarMovimiento()">
                                                <i class="fa fa-search"></i> Buscar Movimiento
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row" id="botones-accion" style="display: none;">
                            <div class="col-md-12">
                                <hr>
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary btn-lg" id="btn-continuar">
                                        <i class="fa fa-arrow-right"></i> Continuar
                                    </button>
                                    <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario" class="btn btn-default btn-lg">
                                        <i class="fa fa-times"></i> Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>

<style>
.radio-block {
    display: block !important;
    margin-bottom: 15px;
    padding: 10px;
    border: 1px solid #e0e0e0;
    border-radius: 5px;
    background-color: #f9f9f9;
    cursor: pointer;
    transition: all 0.3s ease;
}

.radio-block:hover {
    background-color: #f0f0f0;
    border-color: #ccc;
}

.radio-block input[type="radio"]:checked + i {
    color: #337ab7 !important;
}

.radio-block input[type="radio"]:checked {
    margin-right: 8px;
}

.subtipo-container {
    margin-top: 20px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #fafafa;
}

.text-muted.d-block {
    display: block;
    margin-top: 5px;
    font-size: 12px;
}
</style>

<script>
$(document).ready(function() {
    // Manejar cambio de tipo de movimiento
    $('input[name="tipo_movimiento"]').change(function() {
        const tipo = $(this).val();
        
        // Ocultar todos los subtipos
        $('.subtipo-container').hide();
        $('#formulario-ajuste').hide();
        $('#botones-accion').hide();
        
        // Limpiar selecciones
        $('input[name="subtipo_entrada"]').prop('checked', false);
        $('input[name="subtipo_salida"]').prop('checked', false);
        
        // Mostrar subtipos correspondientes
        if (tipo === 'ENTRADA') {
            $('#subtipos-entrada').show();
        } else if (tipo === 'SALIDA') {
            $('#subtipos-salida').show();
        }
    });
    
    // Manejar cambio de subtipo
    $('input[name="subtipo_entrada"], input[name="subtipo_salida"]').change(function() {
        const subtipo = $(this).val();
        
        $('#formulario-ajuste').hide();
        $('#botones-accion').hide();
        
        if (subtipo === 'AJUSTE') {
            $('#formulario-ajuste').show();
        } else {
            $('#botones-accion').show();
        }
    });
    
    // Manejar botón continuar
    $('#btn-continuar').click(function() {
        const tipo = $('input[name="tipo_movimiento"]:checked').val();
        const subtipo = $('input[name="subtipo_entrada"]:checked, input[name="subtipo_salida"]:checked').val();
        
        if (!tipo || !subtipo) {
            Swal.fire({
                title: 'Error',
                text: 'Debe seleccionar un tipo y subtipo de movimiento',
                icon: 'error'
            });
            return;
        }
        
        // Redirigir según el tipo y subtipo seleccionado
        if (tipo === 'ENTRADA' && subtipo === 'COMPRA') {
            window.location.href = '<?= BASE_URL ?>index.php?action=movimientos-inventario&method=registrarCompra';
        } else if (tipo === 'SALIDA' && subtipo === 'VENTA') {
            Swal.fire({
                title: 'Información',
                text: 'Para generar una venta, debe ir al módulo de ventas.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Ir a Ventas',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= BASE_URL ?>index.php?action=ventas&method=crear';
                }
            });
        } else if (subtipo === 'PERDIDA') {
            window.location.href = '<?= BASE_URL ?>index.php?action=movimientos-inventario&method=registrarPerdida';
        } else if (subtipo === 'OTRO') {
            window.location.href = '<?= BASE_URL ?>index.php?action=movimientos-inventario&method=registrarOtro&tipo=' + tipo;
        }
    });
});

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
    
    // Realizar búsqueda
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
        
        if (data.success) {
            // Redirigir al formulario correspondiente según el tipo de movimiento
            const movimiento = data.movimiento;
            
            if (movimiento.tipo_referencia === 'VENTA') {
                // Si es una venta, ir al formulario de modificar venta
                window.location.href = '<?= BASE_URL ?>index.php?action=movimientos-inventario&method=modificarVenta&id=' + movimiento.id_referencia;
            } else if (movimiento.tipo_movimiento === 'ENTRADA') {
                // Si es entrada, verificar el subtipo
                if (movimiento.subtipo_movimiento === 'COMPRA') {
                    // Ir al formulario de modificar compra usando el ID del movimiento
                    window.location.href = '<?= BASE_URL ?>index.php?action=movimientos-inventario&method=modificarCompra&id=' + movimiento.id;
                } else if (movimiento.subtipo_movimiento === 'OTRO') {
                    // Ir al formulario específico de modificar entrada OTRO
                    window.location.href = '<?= BASE_URL ?>index.php?action=movimientos-inventario&method=modificarOtroEntrada&id=' + movimiento.id;
                } else {
                    // Para otros tipos de entrada, usar el formulario de editar genérico
                    window.location.href = '<?= BASE_URL ?>index.php?action=movimientos-inventario&method=editar&id=' + movimiento.id;
                }
            } else if (movimiento.tipo_movimiento === 'SALIDA') {
                // Si es salida, verificar el subtipo
                if (movimiento.subtipo_movimiento === 'PERDIDA') {
                    // Ir al formulario de modificar pérdida
                    window.location.href = '<?= BASE_URL ?>index.php?action=movimientos-inventario&method=modificarPerdida&id=' + movimiento.id;
                } else if (movimiento.subtipo_movimiento === 'OTRO') {
                    // Ir al formulario específico de modificar salida OTRO
                    window.location.href = '<?= BASE_URL ?>index.php?action=movimientos-inventario&method=modificarOtroSalida&id=' + movimiento.id;
                } else {
                    // Para otros tipos de salida, usar el formulario de editar genérico
                    window.location.href = '<?= BASE_URL ?>index.php?action=movimientos-inventario&method=editar&id=' + movimiento.id;
                }
            } else {
                // Para cualquier otro caso, usar el formulario de ajuste
                window.location.href = '<?= BASE_URL ?>index.php?action=movimientos-inventario&method=registrarAjuste&numero_transaccion=' + encodeURIComponent(numeroTransaccion);
            }
        } else {
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
        Swal.fire({
            title: 'Error',
            text: 'Ocurrió un error al buscar el movimiento',
            icon: 'error'
        });
    });
}
</script>