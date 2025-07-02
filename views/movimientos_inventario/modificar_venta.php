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
                                    <div class="form-check" style="margin-bottom: 15px;">
                                        <input type="checkbox" class="form-check-input" id="habilitarPagoPartido" <?php echo (isset($venta['pagos']) && count($venta['pagos']) > 1 ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="habilitarPagoPartido">Habilitar pago partido</label>
                                    </div>
                                    <div id="pago_simple" class="row" style="<?php echo (isset($venta['pagos']) && count($venta['pagos']) > 1 ? 'display: none;' : ''); ?>">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Forma de Pago</label>
                                                <select class="form-control" name="formas_pago[]" id="forma_pago" required>
                                                    <option value="EFECTIVO" <?php echo (isset($venta['pagos'][0]['forma_pago']) && $venta['pagos'][0]['forma_pago'] == 'EFECTIVO' ? 'selected' : ''); ?>>Efectivo</option>
                                                    <option value="TARJETA" <?php echo (isset($venta['pagos'][0]['forma_pago']) && $venta['pagos'][0]['forma_pago'] == 'TARJETA' ? 'selected' : ''); ?>>Tarjeta</option>
                                                    <option value="PAGO_MOVIL" <?php echo (isset($venta['pagos'][0]['forma_pago']) && $venta['pagos'][0]['forma_pago'] == 'PAGO_MOVIL' ? 'selected' : ''); ?>>Pago Móvil</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Monto</label>
                                                <input type="number" class="form-control" id="monto_pago" name="montos_pago[]" step="0.01"
                                                    value="<?php echo isset($venta['pagos'][0]['monto']) ? $venta['pagos'][0]['monto'] : $venta['monto_total']; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4" id="referencia_container" style="<?php echo (isset($venta['pagos'][0]['forma_pago']) && $venta['pagos'][0]['forma_pago'] != 'EFECTIVO' ? '' : 'display: none;'); ?>">
                                            <div class="form-group">
                                                <label>Referencia</label>
                                                <input type="text" class="form-control" id="referencia_pago" name="referencias_pago[]"
                                                    value="<?php echo isset($venta['pagos'][0]['referencia_pago']) ? $venta['pagos'][0]['referencia_pago'] : ''; ?>" maxlength="6">
                                            </div>
                                        </div>
                                    </div>
                                    <div id="pago_partido" style="<?= (isset($venta['pagos']) && count($venta['pagos']) > 1 ? '' : 'display: none;') ?>">
                                        <div id="formas_pago_container">
                                            <?php if (isset($venta['pagos']) && count($venta['pagos']) > 1): ?>
                                                <?php foreach ($venta['pagos'] as $index => $pago): ?>
                                                    <div class="forma-pago-item" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 10px; border-radius: 5px;">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>Forma de Pago</label>
                                                                    <select class="form-control" name="formas_pago[]" required>
                                                                        <option value="EFECTIVO" <?= $pago['forma_pago'] == 'EFECTIVO' ? 'selected' : '' ?>>Efectivo</option>
                                                                        <option value="TARJETA" <?= $pago['forma_pago'] == 'TARJETA' ? 'selected' : '' ?>>Tarjeta</option>
                                                                        <option value="PAGO_MOVIL" <?= $pago['forma_pago'] == 'PAGO_MOVIL' ? 'selected' : '' ?>>Pago Móvil</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>Monto</label>
                                                                    <input type="number" class="form-control monto-pago" name="montos_pago[]"
                                                                        value="<?= $pago['monto'] ?>" step="0.01" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 referencia-container" style="<?= $pago['forma_pago'] == 'EFECTIVO' ? 'display: none;' : '' ?>">
                                                                <div class="form-group">
                                                                    <label>Referencia</label>
                                                                    <input type="text" class="form-control" name="referencias_pago[]"
                                                                        value="<?= isset($pago['referencia_pago']) ? $pago['referencia_pago'] : '' ?>" maxlength="6">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <label>&nbsp;</label>
                                                                <button type="button" class="btn btn-danger btn-block eliminar-forma-pago" <?= $index == 0 ? 'disabled' : '' ?>>
                                                                    <i class="fa fa-trash-o"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
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

        // Verificar estado inicial de pagos
        const tieneMultiplesPagos = <?php echo (isset($venta['pagos']) && count($venta['pagos']) > 1) ? 'true' : 'false'; ?>;
        console.log('Estado inicial:', {
            tieneMultiplesPagos: tieneMultiplesPagos,
            pagos: <?php echo json_encode($venta['pagos'] ?? []); ?>
        });

        // Inicialización correcta del total con manejo preciso de decimales
        let totalVentaActual = Number(Number("<?= str_replace(',', '.', $venta['monto_total']) ?>").toFixed(2)) || 0;

        // Función para recalcular el total de la venta con manejo preciso de decimales
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

        // Función mejorada para el monto restante con limpieza previa de totales
        function actualizarMontoRestante() {
            let totalPagado = 0;
            const totalVentaFixed = Number(totalVentaActual.toFixed(2));
        
            // Limpiar los campos de pago si están vacíos o no son números válidos
            $('input[name="montos_pago[]"]').each(function() {
                const valor = $(this).val().trim();
                if (valor === '' || isNaN(Number(valor.replace(',', '.')))) {
                    $(this).val('0.00');
                }
            });
        
            if ($('#habilitarPagoPartido').is(':checked')) {
                $('input[name="montos_pago[]"]').each(function() {
                    const valor = Number($(this).val().replace(',', '.'));
                    if (!isNaN(valor)) {
                        totalPagado = Number((totalPagado + valor).toFixed(2));
                    }
                });
            } else {
                const valorPago = Number($('#monto_pago').val().replace(',', '.'));
                if (!isNaN(valorPago)) {
                    totalPagado = Number(valorPago.toFixed(2));
                }
            }
        
            const montoRestante = Number((totalVentaFixed - totalPagado).toFixed(2));
            const montoRestanteElement = $('#montoRestante');
            
            // Formatear el monto restante para mostrar
            montoRestanteElement.text(montoRestante.toLocaleString('es-VE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + ' Bs');
        
            // Aplicar estilo según el monto restante
            if (Math.abs(montoRestante) <= 0.01) { // Tolerancia para diferencias mínimas
                montoRestanteElement.parent().removeClass('alert-danger').addClass('alert-success');
            } else {
                montoRestanteElement.parent().removeClass('alert-success').addClass('alert-danger');
            }
        
            return montoRestante;
        }

        // Validación de campos numéricos
        $(document).on('input', '.cantidad, .precio, .monto-pago, #monto_pago', function() {
            // Reemplazar comas por puntos para decimales
            $(this).val($(this).val().replace(',', '.'));

            // Eliminar caracteres no numéricos excepto punto
            $(this).val($(this).val().replace(/[^0-9.]/g, ''));

            // Si hay más de un punto, quedarse solo con el primero
            if ($(this).val().split('.').length > 2) {
                $(this).val($(this).val().substring(0, $(this).val().lastIndexOf('.')));
            }
        });

        // Eventos para actualizar cuando cambian cantidades o precios
        $(document).on('input', '.cantidad, .precio', function() {
            // Validar stock máximo
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
        actualizarMontoRestante();

        function crearFormaPago() {
            const index = $('#formas_pago_container').children().length;
            console.log('Agregando nueva forma de pago, índice:', index);

            const formaPagoDiv = $(`
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
            `);
            formaPagoDiv.find('select[name="formas_pago[]"]').change(function() {
                const referenciaContainer = $(this).closest('.forma-pago-item').find('.referencia-container');
                const referenciaInput = referenciaContainer.find('input[name="referencias_pago[]"]');

                if ($(this).val() === 'EFECTIVO') {
                    referenciaContainer.hide();
                    referenciaInput.val('').prop('required', false);
                } else {
                    referenciaContainer.show();
                    referenciaInput.prop('required', true);
                }
            });

            formaPagoDiv.find('.monto-pago').on('input', function() {
                actualizarMontoRestante();
            });

            $('#formas_pago_container').append(formaPagoDiv);
            return formaPagoDiv;
        }

        // Evento para agregar nueva forma de pago
        $('#agregarFormaPago').click(function() {
            crearFormaPago();
        });

        // Eventos delegados para manejar cambios en formas de pago
        $(document).on('change', 'input[name="montos_pago[]"]', function() {
            const valor = $(this).val().trim();
            if (valor === '' || isNaN(Number(valor.replace(',', '.')))) {
                $(this).val('0.00');
            }
            actualizarMontoRestante();
        });

        $(document).on('change', '#monto_pago', function() {
            const valor = $(this).val().trim();
            if (valor === '' || isNaN(Number(valor.replace(',', '.')))) {
                $(this).val('0.00');
            }
            actualizarMontoRestante();
        });

        // Evento delegado para eliminar forma de pago
        $(document).on('click', '.eliminar-forma-pago', function() {
            if ($('#formas_pago_container .forma-pago-item').length > 1) {
                $(this).closest('.forma-pago-item').remove();
                actualizarMontoRestante();
            } else {
                alert('Debe mantener al menos una forma de pago');
            }
        });

        $(document).on('click', '.eliminar-forma-pago', function() {
            if (!$(this).is(':disabled')) {
                $(this).closest('.forma-pago-item').remove();
                actualizarMontoRestante();
            }
        });

        $(document).on('input', '.monto-pago', function() {
            actualizarMontoRestante();
        });

        // Evento para manejar el cambio entre pago simple y partido
        $('#habilitarPagoPartido').change(function() {
        // Limpiar todos los campos de pago
        $('input[name="montos_pago[]"]').val('0.00');
        $('#monto_pago').val('0.00');
        
        // Mostrar/ocultar las secciones correspondientes
        if ($(this).is(':checked')) {
        $('#pago_simple').hide();
        $('#pago_partido').show();
        // Si no hay formas de pago, crear una inicial
        if ($('#formas_pago_container').children().length === 0) {
        crearFormaPago();
        }
        } else {
        $('#pago_simple').show();
        $('#pago_partido').hide();
        }
        
        // Recalcular el monto restante
        actualizarMontoRestante();
        });

        // Función para crear una nueva forma de pago
        function crearFormaPago() {
        const formaPagoHtml = `
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
        <input type="number" class="form-control monto-pago" name="montos_pago[]" value="0.00" step="0.01" required>
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
        $('#formas_pago_container').append(formaPagoHtml);
        actualizarMontoRestante();
        }

        // Evento delegado para manejar cambio de forma de pago
        $(document).on('change', 'select[name="formas_pago[]"]', function() {
            const formaPagoItem = $(this).closest('.forma-pago-item');
            const referenciaContainer = formaPagoItem.find('.referencia-container');
            const referenciaInput = referenciaContainer.find('input[name="referencias_pago[]"]');
            const formaPago = $(this).val();

            if (formaPago === 'EFECTIVO') {
                referenciaContainer.hide();
                referenciaInput.val(''); // Limpiar referencia cuando se cambia a efectivo
                referenciaInput.prop('required', false);
            } else {
                referenciaContainer.show();
                referenciaInput.prop('required', true);
            }
        });

        // Evento para manejar cambio de forma de pago simple
        $('#forma_pago').change(function() {
            const referencia_container = $('#referencia_container');
            const referencia_input = $('#referencia_pago');

            if ($(this).val() === 'EFECTIVO') {
                referencia_container.hide();
                referencia_input.val(''); // Limpiar referencia
                referencia_input.prop('required', false);
            } else {
                referencia_container.show();
                referencia_input.prop('required', true);
            }
        });

        // Manejar cambio de forma de pago simple
        $('#monto_pago').on('input', function() {
            actualizarMontoRestante();
        });

        // Date edit switch handler
        $('#editarFecha').change(function() {
            const isChecked = $(this).prop('checked');

            if (isChecked) {
                Swal.fire({
                    title: '¿Desea editar la fecha y hora?',
                    text: "Podrá modificar la fecha y hora de la venta",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, editar',
                    cancelButtonText: 'No, mantener actual'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#fecha').prop('readonly', false);
                    } else {
                        $(this).prop('checked', false);
                        $('#fecha').prop('readonly', true);
                    }
                });
            } else {
                Swal.fire({
                    title: '¿Volver a fecha y hora original?',
                    text: "Se restaurará la fecha y hora original de la venta",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, restaurar',
                    cancelButtonText: 'No, mantener editado'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#fecha').prop('readonly', true);
                        $('#fecha').val('<?= date('Y-m-d\TH:i', strtotime($venta['fecha'])) ?>');
                    } else {
                        $(this).prop('checked', true);
                    }
                });
            }
        });

        // Client search functionality
        const clientes = <?php echo json_encode($clientes); ?>;

        $('#buscar_cliente').on('input', function() {
            const busqueda = $(this).val().toLowerCase();
            const resultados = $('#resultados_busqueda');
            resultados.empty();

            if (busqueda.length < 2) {
                resultados.hide();
                return;
            }

            const clientesFiltrados = clientes.filter(cliente =>
                cliente.nombres.toLowerCase().includes(busqueda) ||
                cliente.apellidos.toLowerCase().includes(busqueda) ||
                cliente.cedula.includes(busqueda)
            );

            if (clientesFiltrados.length > 0) {
                clientesFiltrados.forEach(cliente => {
                    resultados.append(`
                    <a href="#" class="list-group-item cliente-item" 
                       data-cedula="${cliente.cedula}"
                       data-nombre="${cliente.nombres} ${cliente.apellidos}">
                        ${cliente.nombres} ${cliente.apellidos} - ${cliente.cedula}
                    </a>
                `);
                });
                resultados.show();
            } else {
                resultados.append(`
                <div class="list-group-item">
                    No se encontraron clientes
                    <a href="<?= BASE_URL ?>index.php?action=clientes&method=crear" class="btn btn-sm btn-primary float-right">
                        Crear nuevo cliente
                    </a>
                </div>
            `);
                resultados.show();
            }
        });

        $(document).on('click', '.cliente-item', function(e) {
            e.preventDefault();
            const cedula = $(this).data('cedula');
            const nombre = $(this).data('nombre');

            $('#buscar_cliente').val(nombre);
            $('#cedula_cliente').val(cedula);
            $('#resultados_busqueda').hide();
        });

        // Hide results when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#buscar_cliente, #resultados_busqueda').length) {
                $('#resultados_busqueda').hide();
            }
        });

        // Product search functionality
        const productos = <?php echo json_encode($productos); ?>;

        $(document).on('input', '.buscar-producto', function() {
            const busqueda = $(this).val().toLowerCase();
            const resultados = $(this).closest('td').find('.resultados-busqueda-producto');
            resultados.empty();

            if (busqueda.length < 2) {
                resultados.hide();
                return;
            }

            const productosFiltrados = productos.filter(producto =>
                producto.descripcion.toLowerCase().includes(busqueda) ||
                (producto.codigo && producto.codigo.toLowerCase().includes(busqueda))
            );

            if (productosFiltrados.length > 0) {
                productosFiltrados.forEach(producto => {
                    resultados.append(`
                        <a href="#" class="list-group-item producto-item" 
                           data-id="${producto.id}"
                           data-descripcion="${producto.descripcion.replace(/"/g, '&quot;')}"
                           data-stock="${producto.cantidad}"
                           data-precio="${producto.precio}">
                            ${producto.descripcion} (Stock: ${producto.cantidad})
                        </a>
                    `);
                });
                resultados.show();
            } else {
                resultados.append(`
                    <div class="list-group-item">
                        No se encontraron productos
                    </div>
                `);
                resultados.show();
            }
        });

        $(document).on('click', '.producto-item', function(e) {
            e.preventDefault();
            const td = $(this).closest('td');
            const descripcion = $(this).data('descripcion');
            const stock = parseInt($(this).data('stock'));
            const id = $(this).data('id');
            const precio = $(this).data('precio');

            td.find('.buscar-producto').val(`${descripcion} (Stock: ${stock})`);
            td.find('input[name$="[id_producto]"]').val(id);
            td.find('input[name$="[descripcion]"]').val(descripcion);
            td.closest('tr').find('.precio').val(precio);
            td.find('.resultados-busqueda-producto').hide();

            // Actualizar el stock máximo permitido
            const cantidadInput = td.closest('tr').find('.cantidad');
            const cantidadActual = parseInt(cantidadInput.val()) || 0;
            cantidadInput.attr('max', stock + cantidadActual);
        });

        $(document).click(function(e) {
            if (!$(e.target).closest('.buscar-producto, .resultados-busqueda-producto').length) {
                $('.resultados-busqueda-producto').hide();
            }
        });

        // Calculate subtotal when quantity or price changes
        $(document).on('input', '.cantidad, .precio', function() {
            const row = $(this).closest('tr');
            const cantidad = parseFloat($(this).find('.cantidad').val().replace(',', '.')) || 0;
            const precio = parseFloat($(this).find('.precio').val().replace(',', '.')) || 0;
            const subtotal = cantidad * precio;

            row.find('.subtotal').text(subtotal.toLocaleString('es-VE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

            // Update total
            let total = 0;
            $('#tablaProductos tbody tr').each(function() {
                const subtotalText = $(this).find('.subtotal').text().replace('.', '').replace(',', '.');
                total += parseFloat(subtotalText) || 0;
            });

            $('#totalVenta').text(total.toLocaleString('es-VE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + ' Bs');
            totalVentaActual = total;
            actualizarMontoRestante();
        });

        // Delete product row
        $(document).on('click', '.eliminar-producto', function() {
            const row = $(this).closest('tr');
            Swal.fire({
                title: '¿Está seguro?',
                text: "Se eliminará el producto de la venta",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    row.remove();

                    // Update total
                    let total = 0;
                    $('#tablaProductos tbody tr').each(function() {
                        const subtotalText = $(this).find('.subtotal').text().replace('.', '').replace(',', '.');
                        total += parseFloat(subtotalText) || 0;
                    });

                    $('#totalVenta').text(total.toLocaleString('es-VE', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + ' Bs');
                    totalVentaActual = total;
                    actualizarMontoRestante();
                }
            });
        });

        // Validación del formulario antes del envío
        $('#formVenta').submit(function(e) {
            // Validar que haya un cliente seleccionado
            if (!$('#cedula_cliente').val()) {
                e.preventDefault();
                alert('Debe seleccionar un cliente');
                return false;
            }

            // Validar que haya productos
            if ($('#productosSeleccionados tr').length === 0) {
                e.preventDefault();
                alert('Debe agregar al menos un producto');
                return false;
            }

            // Validar que los productos tengan cantidades y precios válidos
            let productosInvalidos = false;
            $('#productosSeleccionados tr').each(function() {
                const cantidad = Number($(this).find('.cantidad').val());
                const precio = Number($(this).find('.precio').val());
                
                if (isNaN(cantidad) || cantidad <= 0 || isNaN(precio) || precio <= 0) {
                    productosInvalidos = true;
                    return false; // Romper el ciclo
                }
            });

            if (productosInvalidos) {
                e.preventDefault();
                alert('Todos los productos deben tener cantidad y precio válidos mayores a 0');
                return false;
            }

            const montoRestante = actualizarMontoRestante();
            
            if (Math.abs(montoRestante) > 0.01) {
                e.preventDefault();
                alert('El monto total de los pagos debe coincidir con el total de la venta');
                return false;
            }
            
            // Validar que los campos de referencia estén completos cuando son requeridos
            let referenciasFaltantes = false;
            
            if ($('#habilitarPagoPartido').is(':checked')) {
                $('select[name="formas_pago[]"]').each(function() {
                    const formaPago = $(this).val();
                    const referenciaInput = $(this).closest('.forma-pago-item').find('input[name="referencias_pago[]"]');
                    
                    if (formaPago !== 'EFECTIVO' && !referenciaInput.val().trim()) {
                        referenciasFaltantes = true;
                        return false; // Romper el ciclo
                    }
                });
            } else {
                const formaPago = $('#forma_pago').val();
                if (formaPago !== 'EFECTIVO' && !$('#referencia_pago').val().trim()) {
                    referenciasFaltantes = true;
                }
            }
            
            if (referenciasFaltantes) {
                e.preventDefault();
                alert('Debe ingresar el número de referencia para los pagos que no son en efectivo');
                return false;
            }
            
            return true;
        });

        // Initialize payment methods and total
        const ventaPagos = <?php echo json_encode($venta['pagos'] ?? []); ?>;
        console.log('Inicializando pagos de la venta:', ventaPagos);

        if (ventaPagos.length > 1) {
            $('#habilitarPagoPartido').prop('checked', true).trigger('change');
            // Limpiar contenedor de pagos
            $('#formas_pago_container').empty();

            // Agregar cada pago existente
            ventaPagos.forEach(pago => {
                const nuevaFormaPago = crearFormaPago();
                nuevaFormaPago.find('select[name="formas_pago[]"]').val(pago.forma_pago);
                nuevaFormaPago.find('input[name="montos_pago[]"]').val(pago.monto);
                if (pago.forma_pago !== 'EFECTIVO') {
                    nuevaFormaPago.find('input[name="referencias_pago[]"]').val(pago.referencia_pago);
                    nuevaFormaPago.find('.referencia-container').show();
                }
                $('#formas_pago_container').append(nuevaFormaPago);
            });
        } else if (ventaPagos.length === 1) {
            const pago = ventaPagos[0];
            $('#forma_pago').val(pago.forma_pago);
            $('#monto_pago').val(pago.monto);
            if (pago.forma_pago !== 'EFECTIVO') {
                $('#referencia_pago').val(pago.referencia_pago);
                $('#referencia_container').show();
            }
        }

        actualizarMontoRestante();

        // Verificar campos de pago después de la inicialización
        console.log('Estado de los campos de pago:', {
            pagoPartidoHabilitado: $('#habilitarPagoPartido').is(':checked'),
            formasPagoVisibles: $('select[name="formas_pago[]"]').length,
            montosPagoVisibles: $('input[name="montos_pago[]"]').length,
            referenciasPagoVisibles: $('input[name="referencias_pago[]"]').length
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