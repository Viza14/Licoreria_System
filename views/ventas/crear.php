<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-shopping-cart"></i> Nueva Venta</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-shopping-cart"></i><a href="<?= BASE_URL ?>index.php?action=ventas">Ventas</a></li>
                    <li><i class="fa fa-plus"></i> Nueva Venta</li>
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
                        <form id="formVenta" method="POST" action="<?= BASE_URL ?>index.php?action=ventas&method=guardar">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h4 style="margin-top: 0px; color: #337ab7;"><i class="fa fa-user"></i> Cliente *</h4>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="buscar_cliente" placeholder="Buscar cliente..." autocomplete="off">
                                            <input type="hidden" id="cedula_cliente" name="cedula_cliente" required>
                                        </div>
                                        <div id="resultados_busqueda" class="list-group" style="position: absolute; z-index: 1000; width: 95%;"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha">Fecha</label>
                                        <div class="input-group">
                                            <input type="datetime-local" class="form-control" id="fecha" name="fecha" readonly>
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
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="buscar_producto">Agregar Producto</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="buscar_producto" placeholder="Buscar producto..." autocomplete="off">
                                            <input type="hidden" id="id_producto" name="id_producto">
                                        </div>
                                        <div id="resultados_productos" class="list-group" style="position: absolute; z-index: 1000; width: 95%;"></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="cantidad">Cantidad</label>
                                        <input type="number" class="form-control" id="cantidad" min="1" value="1">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-primary form-control" id="agregarProducto" style="font-weight: bold;">
                                            <i class="fa fa-plus"></i> Agregar
                                        </button>
                                    </div>
                                </div>
                            </div>

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
                                                <!-- Aquí se agregarán los productos dinámicamente -->
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                                    <td colspan="2" id="totalVenta">0,00 Bs</td>
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
                                        <input type="checkbox" class="form-check-input" id="habilitarPagoPartido">
                                        <label class="form-check-label" for="habilitarPagoPartido">Habilitar pago partido</label>
                                    </div>
                                    <div id="pago_simple" class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Forma de Pago</label>
                                                <select class="form-control" name="forma_pago" id="forma_pago" required>
                                                    <option value="EFECTIVO">Efectivo</option>
                                                    <option value="TARJETA">Tarjeta</option>
                                                    <option value="PAGO_MOVIL">Pago Móvil</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Monto</label>
                                                <input type="number" class="form-control" id="monto_pago" name="monto_pago" step="0.01" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4" id="referencia_container" style="display: none;">
                                            <div class="form-group">
                                                <label>Referencia</label>
                                                <input type="text" class="form-control" id="referencia_pago" name="referencia_pago" maxlength="6">
                                            </div>
                                        </div>
                                    </div>
                                    <div id="pago_partido" style="display: none;">
                                        <div id="formas_pago_container">
                                            <!-- Aquí se agregarán dinámicamente las formas de pago -->
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
                                        <i class="fa fa-save"></i> Registrar Venta
                                    </button>
                                    <a href="<?= BASE_URL ?>index.php?action=ventas" class="btn btn-default" style="margin-left: 10px;">
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

<!-- Scripts para el formulario de venta -->
<script>
    $(document).ready(function() {
        // Set default date and time
        const setDefaultDateTime = () => {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');

            return `${year}-${month}-${day}T${hours}:${minutes}`;
        };

        $('#fecha').val(setDefaultDateTime());

        // Manejo de pagos divididos
        let totalVentaActual = 0;

        function actualizarMontoRestante() {
            let totalPagado = 0;
            if ($('#habilitarPagoPartido').is(':checked')) {
                $('input[name="montos_pago[]"]').each(function() {
                    totalPagado += parseFloat($(this).val() || 0);
                });
            } else {
                totalPagado = parseFloat($('#monto_pago').val() || 0);
            }
            const montoRestante = totalVentaActual - totalPagado;
            $('#montoRestante').text(montoRestante.toFixed(2) + ' Bs');
            
            // Actualizar el estado cuando no hay formas de pago
            if ($('#habilitarPagoPartido').is(':checked')) {
                const formasPago = $('select[name="formas_pago[]"]');
                if (formasPago.length === 0) {
                    $('#montoRestante').text(totalVentaActual.toFixed(2) + ' Bs');
                    return totalVentaActual;
                }
            }
            
            return montoRestante;
        }

        function crearFormaPago() {
            const index = $('#formas_pago_container').children().length;
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
            $(this).closest('.forma-pago-item').remove();
            const montoRestante = actualizarMontoRestante();
            
            // Mostrar mensaje si no quedan formas de pago
            if ($('.forma-pago-item').length === 0) {
                Swal.fire({
                    title: 'Atención',
                    text: 'Debe agregar al menos una forma de pago',
                    icon: 'warning',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });

        // Manejar cambio entre pago simple y partido
        $('#habilitarPagoPartido').change(function() {
            if ($(this).is(':checked')) {
                $('#pago_simple').hide();
                $('#pago_partido').show();
                $('#formas_pago_container').empty();
                crearFormaPago();
                $('#monto_pago').val('').prop('required', false);
            } else {
                $('#pago_simple').show();
                $('#pago_partido').hide();
                $('#formas_pago_container').empty();
                $('#monto_pago').val(totalVentaActual.toFixed(2)).prop('required', true);
            }
            actualizarMontoRestante();
        });

        // Manejar cambio de forma de pago simple
        $('#forma_pago').change(function() {
            if ($(this).val() === 'EFECTIVO') {
                $('#referencia_container').hide();
                $('#referencia_pago').prop('required', false);
            } else {
                $('#referencia_container').show();
                $('#referencia_pago').prop('required', true);
            }
        });

        // Manejar cambio en monto de pago simple
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
                        $('#fecha').val(setDefaultDateTime());
                    }
                });
            } else {
                Swal.fire({
                    title: '¿Volver a fecha y hora actual?',
                    text: "Se restaurará la fecha y hora actual",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, restaurar',
                    cancelButtonText: 'No, mantener editado'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#fecha').prop('readonly', true);
                        $('#fecha').val(setDefaultDateTime());
                    } else {
                        $(this).prop('checked', true);
                    }
                });
            }
        });

        // Client search functionality
        const clientes = <?php echo json_encode($clientes); ?>;
        const productos = <?php echo json_encode($productos); ?>;

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

        // Product search functionality
        $('#buscar_producto').on('input', function() {
            const busqueda = $(this).val().toLowerCase();
            const resultados = $('#resultados_productos');
            resultados.empty();

            if (busqueda.length < 2) {
                resultados.hide();
                return;
            }

            const productosFiltrados = productos.filter(producto =>
                producto.descripcion.toLowerCase().includes(busqueda)
            );

            if (productosFiltrados.length > 0) {
                productosFiltrados.forEach(producto => {
                    resultados.append(`
                    <a href="#" class="list-group-item producto-item" 
                       data-id="${producto.id}"
                       data-descripcion="${producto.descripcion}"
                       data-precio="${producto.precio}"
                       data-stock="${producto.cantidad}">
                        ${producto.descripcion} - 
                        ${parseFloat(producto.precio).toFixed(2).replace('.', ',')} Bs
                        (Stock: ${producto.cantidad})
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

        $(document).on('click', '.cliente-item', function(e) {
            e.preventDefault();
            const cedula = $(this).data('cedula');
            const nombre = $(this).data('nombre');

            $('#buscar_cliente').val(nombre);
            $('#cedula_cliente').val(cedula);
            $('#resultados_busqueda').hide();
        });

        $(document).on('click', '.producto-item', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const descripcion = $(this).data('descripcion');
            const precio = $(this).data('precio');
            const stock = $(this).data('stock');

            $('#buscar_producto').val(descripcion);
            $('#id_producto').val(id);
            $('#id_producto').data('precio', precio);
            $('#id_producto').data('stock', stock);
            $('#resultados_productos').hide();
        });

        // Hide results when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#buscar_cliente, #resultados_busqueda').length) {
                $('#resultados_busqueda').hide();
            }
            if (!$(e.target).closest('#buscar_producto, #resultados_productos').length) {
                $('#resultados_productos').hide();
            }
        });

        const productosAgregados = [];
        let totalVenta = 0;

        // Function to update total
        function actualizarTotal() {
            totalVenta = 0;
            productosAgregados.forEach(producto => {
                totalVenta += producto.subtotal;
            });
            $('#totalVenta').text(totalVenta.toFixed(2).replace('.', ',') + ' Bs');
            // Update total for split payments
            totalVentaActual = totalVenta;
            
            // Actualizar el monto de pago simple si está visible
            if (!$('#habilitarPagoPartido').is(':checked')) {
                $('#monto_pago').val(totalVenta.toFixed(2));
            }
            
            actualizarMontoRestante();
        }

        // Add product to table
        $('#agregarProducto').click(function() {
            const productoId = $('#id_producto').val();
            const productoTexto = $('#buscar_producto').val();
            const precio = parseFloat($('#id_producto').data('precio'));
            const stock = parseInt($('#id_producto').data('stock'));
            const cantidad = parseInt($('#cantidad').val()) || 1;

            // Validations
            if (!productoId) {
                Swal.fire('Error', 'Debe seleccionar un producto', 'error');
                return;
            }

            if (cantidad < 1) {
                Swal.fire('Error', 'La cantidad debe ser mayor a 0', 'error');
                return;
            }

            if (cantidad > stock) {
                Swal.fire('Error', 'No hay suficiente stock para este producto', 'error');
                return;
            }

            // Check if product already exists
            const index = productosAgregados.findIndex(p => p.id === productoId);
            const subtotal = precio * cantidad;

            if (index >= 0) {
                // Update quantity and subtotal if exists
                productosAgregados[index].cantidad += cantidad;
                productosAgregados[index].subtotal += subtotal;
            } else {
                // Add new product
                productosAgregados.push({
                    id: productoId,
                    descripcion: productoTexto,
                    precio: precio,
                    cantidad: cantidad,
                    subtotal: subtotal
                });
            }

            // Render table
            renderizarTablaProductos();
            actualizarTotal();

            // Clear fields
            $('#buscar_producto').val('');
            $('#id_producto').val('');
            $('#cantidad').val(1);
        });

        // Render products table
        function renderizarTablaProductos() {
            const tbody = $('#productosSeleccionados');
            tbody.empty();

            productosAgregados.forEach((producto, index) => {
                const row = `
                <tr>
                    <td>${producto.descripcion}</td>
                    <td>${producto.cantidad}</td>
                    <td>${producto.precio.toFixed(2).replace('.', ',')} Bs</td>
                    <td>${producto.subtotal.toFixed(2).replace('.', ',')} Bs</td>
                    <td>
                        <button type="button" class="btn btn-info btn-xs editar-cantidad" data-index="${index}" style="margin-right: 5px;">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-xs eliminar-producto" data-index="${index}">
                            <i class="fa fa-trash-o"></i>
                        </button>
                    </td>
                    <input type="hidden" name="productos[${index}][id]" value="${producto.id}">
                    <input type="hidden" name="productos[${index}][cantidad]" value="${producto.cantidad}">
                    <input type="hidden" name="productos[${index}][precio]" value="${producto.precio}">
                    <input type="hidden" name="productos[${index}][descripcion]" value="${producto.descripcion}">
                </tr>
            `;
                tbody.append(row);
            });
        }

        // Add events to edit buttons
        $(document).on('click', '.editar-cantidad', function() {
            const index = $(this).data('index');
            const producto = productosAgregados[index];
                
            Swal.fire({
                title: 'Editar Cantidad',
                html: `
                    <div class="form-group">
                        <label>Producto: ${producto.descripcion}</label>
                        <input type="number" id="nueva-cantidad" class="form-control" value="${producto.cantidad}" min="1">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const nuevaCantidad = parseInt($('#nueva-cantidad').val());
                    if (nuevaCantidad < 1) {
                        Swal.showValidationMessage('La cantidad debe ser mayor a 0');
                        return false;
                    }
                    return nuevaCantidad;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const nuevaCantidad = result.value;
                    productosAgregados[index].cantidad = nuevaCantidad;
                    productosAgregados[index].subtotal = nuevaCantidad * producto.precio;
                    renderizarTablaProductos();
                    actualizarTotal();
                }
            });
        });

        // Add events to delete buttons
        $(document).on('click', '.eliminar-producto', function() {
            const index = $(this).data('index');
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
                    productosAgregados.splice(index, 1);
                    renderizarTablaProductos();
                    actualizarTotal();
                }
            });
        });

        // Validate form before submit
        $('#formVenta').submit(function(e) {
            e.preventDefault();
            let errores = [];

            // Validar productos
            if (productosAgregados.length === 0) {
                errores.push('Debe agregar al menos un producto');
            }

            // Validar cliente
            if (!$('#cedula_cliente').val()) {
                errores.push('Debe seleccionar un cliente');
            }

            // Validar formas de pago
            if ($('#habilitarPagoPartido').is(':checked')) {
                if ($('.forma-pago-item').length === 0) {
                    errores.push('Debe agregar al menos una forma de pago');
                }
            } else {
                if (!$('#monto_pago').val() || parseFloat($('#monto_pago').val()) <= 0) {
                    errores.push('Debe ingresar un monto válido');
                }
            }

            // Validar montos y referencias
            let totalPagado = 0;
            if ($('#habilitarPagoPartido').is(':checked')) {
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
            } else {
                totalPagado = parseFloat($('#monto_pago').val() || 0);
                const formaPago = $('#forma_pago').val();
                const referencia = $('#referencia_pago').val();

                if (formaPago !== 'EFECTIVO' && !referencia) {
                    errores.push('Debe ingresar el número de referencia para pagos con tarjeta o pago móvil');
                }
            }

            // Validar que el total pagado coincida con el total de la venta
            if (Math.abs(totalPagado - totalVentaActual) > 0.01) {
                errores.push('El total de los pagos debe ser igual al total de la venta');
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

    // Mostrar mensajes de sesión con SweetAlert
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
    </script>
</section>