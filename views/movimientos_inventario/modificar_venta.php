<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-edit"></i> Modificar Venta #<?= $venta['id'] ?></h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-shopping-cart"></i><a href="<?= BASE_URL ?>index.php?action=movimientos-inventario">Ventas</a></li>
                    <li><i class="fa fa-edit"></i> Modificar Venta</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading" style="font-size: 1.3em; background: #f5f5f5; border-bottom: 2px solid #e0e0e0;">
                        <i class="fa fa-shopping-cart"></i> Formulario de Venta
                    </header>
                    <div class="panel-body">
                        <form id="formVenta" method="POST" action="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=actualizarVenta&id=<?= $venta['id'] ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h4 style="margin-top: 0px; color: #337ab7;"><i class="fa fa-user"></i> Cliente *</h4>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="buscar_cliente" placeholder="Buscar cliente..." autocomplete="off"
                                                value="<?= htmlspecialchars($venta['cliente'] ?? '') ?>">
                                            <input type="hidden" id="cedula_cliente" name="cedula_cliente" value="<?= $venta['cedula_cliente'] ?>" required>
                                        </div>
                                        <div id="resultados_busqueda" class="list-group" style="position: absolute; z-index: 1000; width: 95%; display: none;"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha">Fecha</label>
                                        <div class="input-group">
                                            <input type="datetime-local" class="form-control" id="fecha" name="fecha"
                                                value="<?= date('Y-m-d\TH:i', strtotime($venta['fecha'])) ?>" readonly>
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="editarFecha">
                                                        <label class="custom-control-label" for="editarFecha">Editar fecha</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-12">
                                    <h4 style="margin-top: 30px; color: #337ab7;"><i class="fa fa-list"></i> Productos</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="tablaProductos">
                                            <thead>
                                                <tr>
                                                    <th>Producto</th>
                                                    <th>Cantidad</th>
                                                    <th>Precio Unitario</th>
                                                    <th>Subtotal</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="productosSeleccionados">
                                                <?php foreach ($detalles as $index => $detalle): ?>
                                                    <?php
                                                    $stockActual = $this->productoModel->obtenerStockProducto($detalle['id_producto']) + $detalle['cantidad'];
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control buscar-producto" placeholder="Buscar producto..." autocomplete="off"
                                                                    value="<?= htmlspecialchars($detalle['producto'] . ' (Stock: ' . $stockActual . ')') ?>">
                                                                <input type="hidden" name="productos[<?= $index ?>][id_producto]" value="<?= $detalle['id_producto'] ?>">
                                                                <input type="hidden" name="productos[<?= $index ?>][descripcion]" value="<?= htmlspecialchars($detalle['producto']) ?>">
                                                            </div>
                                                            <div class="resultados-busqueda-producto list-group" style="position: absolute; z-index: 1000; width: 95%; display: none;"></div>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control cantidad" name="productos[<?= $index ?>][cantidad]"
                                                                value="<?= $detalle['cantidad'] ?>" min="1" max="<?= $stockActual ?>" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control precio" name="productos[<?= $index ?>][precio]"
                                                                value="<?= $detalle['precio_unitario'] ?>" step="0.01" required>
                                                        </td>
                                                        <td class="subtotal"><?= number_format($detalle['monto'], 2) ?></td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger btn-xs eliminar-producto">
                                                                <i class="fa fa-trash-o"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                                    <td colspan="2" id="totalVenta"><?= number_format($venta['monto_total'], 2) ?> Bs</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <h4 style="margin-top: 30px; color: #337ab7;"><i class="fa fa-credit-card"></i> Forma de Pago</h4>
                                    <div id="pago_partido">
                                        <div id="formas_pago_container">
                                            <?php foreach ($venta['pagos'] as $index => $pago): ?>
                                                <div class="forma-pago-item" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 10px; border-radius: 5px;">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Forma de Pago</label>
                                                                <select class="form-control" name="formas_pago[]" required>
                                                                    <option value="EFECTIVO" <?php echo ($pago['forma_pago'] == 'EFECTIVO' ? 'selected' : ''); ?>>Efectivo</option>
                                                                    <option value="TARJETA" <?php echo ($pago['forma_pago'] == 'TARJETA' ? 'selected' : ''); ?>>Tarjeta</option>
                                                                    <option value="PAGO_MOVIL" <?php echo ($pago['forma_pago'] == 'PAGO_MOVIL' ? 'selected' : ''); ?>>Pago Móvil</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Monto</label>
                                                                <input type="number" class="form-control monto-pago" name="montos_pago[]" step="0.01" value="<?php echo $pago['monto']; ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 referencia-container" style="<?php echo ($pago['forma_pago'] != 'EFECTIVO' ? '' : 'display: none;'); ?>">
                                                            <div class="form-group">
                                                                <label>Referencia</label>
                                                                <input type="text" class="form-control" name="referencias_pago[]" maxlength="6" value="<?php echo $pago['referencia_pago']; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <label>&nbsp;</label>
                                                            <button type="button" class="btn btn-danger btn-block eliminar-forma-pago">
                                                                <i class="fa fa-trash-o"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <button type="button" class="btn btn-info" id="agregarFormaPago" style="margin-top: 10px;">
                                                <i class="fa fa-plus"></i> Agregar Forma de Pago
                                            </button>
                                            <div class="alert alert-info" style="margin-top: 10px;">
                                                <strong>Monto restante por pagar: </strong>
                                                <span id="montoRestante">0,00 Bs</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success" style="font-size: 1.1em; padding: 8px 24px;">
                                        <i class="fa fa-save"></i> Guardar Modificación
                                    </button>
                                    <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario" class="btn btn-default" style="margin-left: 10px;">
                                        <i class="fa fa-times"></i> Cancelar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->

<!-- Scripts para el formulario de venta -->
<script>
    $(document).ready(function() {
        console.log('Inicializando página de modificación de venta');

        // Inicialización del total con manejo preciso de decimales
        let totalVentaActual = Number(Number("<?= str_replace(',', '.', $venta['monto_total']) ?>").toFixed(2)) || 0;

        // Función para recalcular el total de la venta
        function recalcularTotalVenta() {
            let nuevoTotal = 0;
            
            $('#productosSeleccionados tr').each(function() {
                const cantidad = Number($(this).find('.cantidad').val().replace(',', '.'));
                const precio = Number($(this).find('.precio').val().replace(',', '.'));
                
                if (!isNaN(cantidad) && !isNaN(precio)) {
                    const subtotal = Number((cantidad * precio).toFixed(2));
                    nuevoTotal = Number((nuevoTotal + subtotal).toFixed(2));
                    
                    $(this).find('.subtotal').text(subtotal.toLocaleString('es-VE', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                }
            });
            
            $('#totalVenta').text(nuevoTotal.toLocaleString('es-VE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + ' Bs');
            
            totalVentaActual = nuevoTotal;
            actualizarMontoRestante();
            return nuevoTotal;
        }

        function actualizarMontoRestante() {
            let totalPagado = 0;
            $('input[name="montos_pago[]"]').each(function() {
                totalPagado += parseFloat($(this).val() || 0);
            });
            const montoRestante = totalVentaActual - totalPagado;
            const $montoRestanteElement = $('#montoRestante');
            const $alertContainer = $montoRestanteElement.closest('.alert');
            
            if (montoRestante > 0) {
                $alertContainer.removeClass('alert-success alert-danger').addClass('alert-warning');
                $montoRestanteElement.text(montoRestante.toFixed(2) + ' Bs (Falta por pagar)');
            } else if (montoRestante < 0) {
                $alertContainer.removeClass('alert-success alert-warning').addClass('alert-danger');
                $montoRestanteElement.text(Math.abs(montoRestante).toFixed(2) + ' Bs (Excede el total)');
            } else {
                $alertContainer.removeClass('alert-warning alert-danger').addClass('alert-success');
                $montoRestanteElement.text('0.00 Bs (Monto exacto)');
            }
            
            return montoRestante;
        }

        function crearFormaPago() {
            const template = `
                <div class="forma-pago-item" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 10px; border-radius: 5px;">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Forma de Pago</label>
                                <select class="form-control" name="formas_pago[]" required>
                                    <option value="EFECTIVO">Efectivo</option>
                                    <option value="TARJETA">Tarjeta</option>
                                    <option value="PAGO_MOVIL">Pago Móvil</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Monto</label>
                                <input type="number" class="form-control monto-pago" name="montos_pago[]" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-3 referencia-container" style="display: none;">
                            <div class="form-group">
                                <label>Referencia</label>
                                <input type="text" class="form-control" name="referencias_pago[]" maxlength="6">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-block eliminar-forma-pago">
                                <i class="fa fa-trash-o"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            $('#formas_pago_container').append(template);
        }

        $('#agregarFormaPago').click(function() {
            crearFormaPago();
            actualizarMontoRestante();
        });

        $(document).on('change', 'select[name="formas_pago[]"]', function() {
            const referenciaContainer = $(this).closest('.forma-pago-item').find('.referencia-container');
            const referenciaInput = referenciaContainer.find('input');
            
            if ($(this).val() === 'EFECTIVO') {
                referenciaContainer.hide();
                referenciaInput.prop('required', false);
            } else {
                referenciaContainer.show();
                referenciaInput.prop('required', true);
            }
        });

        $(document).on('click', '.eliminar-forma-pago', function() {
            if ($('.forma-pago-item').length > 1) {
                $(this).closest('.forma-pago-item').remove();
                actualizarMontoRestante();
            } else {
                Swal.fire({
                    title: 'Error',
                    text: 'Debe mantener al menos una forma de pago',
                    icon: 'error'
                });
            }
        });

        $(document).on('input', '.monto-pago', function() {
            actualizarMontoRestante();
        });

        // Validación de campos numéricos
        $(document).on('input', '.cantidad, .precio, .monto-pago', function() {
            $(this).val($(this).val().replace(',', '.'));
            $(this).val($(this).val().replace(/[^0-9.]/g, ''));
            if ($(this).val().split('.').length > 2) {
                $(this).val($(this).val().substring(0, $(this).val().lastIndexOf('.')));
            }
        });

        // Eventos para actualizar cuando cambian cantidades o precios
        $(document).on('input', '.cantidad, .precio', function() {
            const inputCantidad = $(this).hasClass('cantidad') ? $(this) : $(this).closest('tr').find('.cantidad');
            const stockMaximo = parseInt(inputCantidad.attr('max')) || 0;
            const cantidadActual = parseInt(inputCantidad.val()) || 0;

            if (cantidadActual > stockMaximo) {
                inputCantidad.val(stockMaximo);
                Swal.fire('Advertencia', `No puede superar el stock disponible de ${stockMaximo} unidades`, 'warning');
            }

            recalcularTotalVenta();
        });

        // Inicializar los cálculos al cargar la página
        recalcularTotalVenta();

        // Validar formulario antes de enviar
        // Validar formulario antes de enviar
        $('#formVenta').submit(function(e) {
            e.preventDefault();
            let errores = [];
        
            // Validar productos
            if ($('#productosSeleccionados tr').length === 0) {
                errores.push('Debe agregar al menos un producto');
            }
        
            // Validar cliente
            if (!$('#cedula_cliente').val()) {
                errores.push('Debe seleccionar un cliente');
            }
        
            // Validar formas de pago
            if ($('.forma-pago-item').length === 0) {
                errores.push('Debe agregar al menos una forma de pago');
            }
        
            // Validar montos y referencias
            let totalPagado = 0;
            $('.forma-pago-item').each(function() {
                const formaPago = $(this).find('select[name="formas_pago[]"]').val();
                const monto = parseFloat($(this).find('input[name="montos_pago[]"]').val() || 0);
                const referencia = $(this).find('input[name="referencias_pago[]"]').val();
        
                if (monto <= 0) {
                    errores.push('Los montos deben ser mayores a 0');
                }
        
                if (formaPago !== 'EFECTIVO' && !referencia) {
                    errores.push('Debe ingresar el número de referencia para pagos con tarjeta o pago móvil');
                }
        
                totalPagado += monto;
            });
        
            // Validar que el total pagado coincida con el total de la venta
            const montoRestante = actualizarMontoRestante();
            if (montoRestante !== 0) {
                Swal.fire({
                    title: 'Error',
                    text: 'El monto total de los pagos debe ser igual al total de la venta',
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonText: 'Ajustar último monto',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const ultimoMontoPago = $('.monto-pago').last();
                        const montoActual = parseFloat(ultimoMontoPago.val() || 0);
                        const nuevoMonto = montoActual + montoRestante;
                        ultimoMontoPago.val(nuevoMonto.toFixed(2));
                        actualizarMontoRestante();
                    }
                });
                return;
            }
        
            if (errores.length > 0) {
                Swal.fire({
                    title: 'Error',
                    html: errores.join('<br>'),
                    icon: 'error'
                });
            } else {
                this.submit();
            }
        });
    });
</script>

<?php if (isset($_SESSION['mensaje'])): ?>
<script>
    Swal.fire({
        title: '<?= $_SESSION['mensaje']['title'] ?>',
        text: '<?= $_SESSION['mensaje']['text'] ?>',
        icon: '<?= $_SESSION['mensaje']['icon'] ?>',
        timer: 3000,
        timerProgressBar: true
    });
</script>
<?php unset($_SESSION['mensaje']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<script>
    Swal.fire({
        title: '<?= $_SESSION['error']['title'] ?>',
        text: '<?= $_SESSION['error']['text'] ?>',
        icon: '<?= $_SESSION['error']['icon'] ?>',
        timer: 3000,
        timerProgressBar: true
    });
</script>
<?php unset($_SESSION['error']); ?>
<?php endif; ?>