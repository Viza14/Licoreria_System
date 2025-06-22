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
                    <header class="panel-heading">
                        Formulario de Venta
                    </header>
                    <div class="panel-body">
                        <form id="formVenta" method="POST" action="<?= BASE_URL ?>index.php?action=ventas&method=guardar">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="buscar_cliente">Cliente *</label>
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
                                        <button type="button" class="btn btn-primary form-control" id="agregarProducto">
                                            <i class="fa fa-plus"></i> Agregar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Productos</h4>
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
                                    <h4>Forma de Pago</h4>
                                    <div class="form-group">
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-outline-primary active">
                                                <input type="radio" name="forma_pago" value="EFECTIVO" checked> Efectivo
                                            </label>
                                            <label class="btn btn-outline-primary">
                                                <input type="radio" name="forma_pago" value="TARJETA"> Tarjeta
                                            </label>
                                            <label class="btn btn-outline-primary">
                                                <input type="radio" name="forma_pago" value="PAGO_MOVIL"> Pago Móvil
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div id="referencia_pago_container" class="form-group" style="display: none;">
                                        <label for="referencia_pago">Número de Referencia</label>
                                        <input type="text" class="form-control" id="referencia_pago" name="referencia_pago" placeholder="Ingrese número de referencia">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-save"></i> Registrar Venta
                                    </button>
                                    <a href="<?= BASE_URL ?>index.php?action=ventas" class="btn btn-default">
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
            
            // 12-hour format
            let hours = now.getHours();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // '0' should be '12'
            hours = String(hours).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        };

        $('#fecha').val(setDefaultDateTime());

        // Payment method handler
        $('input[name="forma_pago"]').change(function() {
            const paymentMethod = $(this).val();
            if(paymentMethod === 'TARJETA' || paymentMethod === 'PAGO_MOVIL') {
                $('#referencia_pago_container').show();
                $('#referencia_pago').prop('required', true);
            } else {
                $('#referencia_pago_container').hide();
                $('#referencia_pago').prop('required', false);
            }
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
                        // Convert to 12-hour format when editing
                        const currentValue = $('#fecha').val();
                        if (currentValue) {
                            const [datePart, timePart] = currentValue.split('T');
                            if (timePart) {
                                const [hours, minutes] = timePart.split(':');
                                let hourInt = parseInt(hours);
                                const ampm = hourInt >= 12 ? 'PM' : 'AM';
                                hourInt = hourInt % 12;
                                hourInt = hourInt ? hourInt : 12;
                                const formattedHours = String(hourInt).padStart(2, '0');
                                $('#fecha').val(`${datePart}T${formattedHours}:${minutes}`);
                            }
                        }
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
                        <button type="button" class="btn btn-danger btn-xs eliminar-producto" data-index="${index}">
                            <i class="fa fa-trash"></i>
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

            // Add events to delete buttons
            $('.eliminar-producto').click(function() {
                const index = $(this).data('index');
                productosAgregados.splice(index, 1);
                renderizarTablaProductos();
                actualizarTotal();
            });
        }

        // Validate form before submit
        $('#formVenta').submit(function(e) {
            if (productosAgregados.length === 0) {
                e.preventDefault();
                Swal.fire('Error', 'Debe agregar al menos un producto', 'error');
            }

            if (!$('#cedula_cliente').val()) {
                e.preventDefault();
                Swal.fire('Error', 'Debe seleccionar un cliente', 'error');
            }

            const paymentMethod = $('input[name="forma_pago"]:checked').val();
            if ((paymentMethod === 'TARJETA' || paymentMethod === 'PAGO_MOVIL') && !$('#referencia_pago').val()) {
                e.preventDefault();
                Swal.fire('Error', 'Debe ingresar el número de referencia', 'error');
            }
        });
    });
</script>
<!--main content end-->