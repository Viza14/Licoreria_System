<?php
// Validaciones de seguridad
if (!isset($venta) || !$venta) {
    echo '<div class="alert alert-danger">Error: No se encontraron datos de la venta.</div>';
    return;
}

if (!isset($detalles) || !$detalles) {
    echo '<div class="alert alert-danger">Error: No se encontraron detalles de la venta.</div>';
    return;
}

if (!isset($productos) || !$productos) {
    $productos = [];
}

if (!isset($clientes) || !$clientes) {
    $clientes = [];
}
?>
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
                                                value="<?= !empty($venta['fecha']) ? 
                                                        (($timestamp = strtotime($venta['fecha'])) !== false ? 
                                                         date('Y-m-d\TH:i', $timestamp) : date('Y-m-d\TH:i')) : date('Y-m-d\TH:i') ?>" readonly>
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
                                                <?php foreach ($detalles as $index => $detalle): ?>
                                                    <?php
                                                    // Corregir el cálculo del stock actual
                                                    $stockActual = $this->productoModel->obtenerStockProducto($detalle['id_producto']);
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
                                                            <input type="number" class="form-control cantidad" name="productos[<?= $index ?>][cantidad]" value="<?= $detalle['cantidad'] ?>" min="1" max="<?= $stockActual + $detalle['cantidad'] ?>" required>
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
                                                                    <option value="EFECTIVO" <?= $pago['forma_pago'] == 'EFECTIVO' ? 'selected' : '' ?>>Efectivo</option>
                                                                    <option value="TARJETA" <?= $pago['forma_pago'] == 'TARJETA' ? 'selected' : '' ?>>Tarjeta</option>
                                                                    <option value="PAGO_MOVIL" <?= $pago['forma_pago'] == 'PAGO_MOVIL' ? 'selected' : '' ?>>Pago Móvil</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Monto</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control monto-pago" name="montos_pago[]" value="<?= $pago['monto'] ?>" step="0.01" required>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-info seleccionar-productos" title="Seleccionar productos para esta forma de pago">
                                                            <i class="fa fa-list"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-success usar-monto-restante" title="Completar monto restante">
                                                            <i class="fa fa-level-up"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                                        <div class="col-md-3 referencia-container" style="<?= $pago['forma_pago'] == 'EFECTIVO' ? 'display: none;' : '' ?>">
                                                            <div class="form-group">
                                                                <label>Referencia</label>
                                                                <input type="text" class="form-control" name="referencias_pago[]" value="<?= isset($pago['referencia_pago']) ? $pago['referencia_pago'] : '' ?>" maxlength="6" <?= $pago['forma_pago'] != 'EFECTIVO' ? 'required' : '' ?>>
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

        // Inicialización correcta del total con manejo preciso de decimales
        let totalVentaActual = Number(Number("<?= str_replace(',', '.', $venta['monto_total']) ?>").toFixed(2)) || 0;

        // Datos de clientes y productos para búsqueda
        const clientes = <?php echo json_encode($clientes); ?>;
        const productos = <?php echo json_encode($productos); ?>;

        // Array para manejar productos agregados dinámicamente
        let productosAgregados = [];

        // Inicializar productos existentes en el array
        <?php foreach ($detalles as $index => $detalle): ?>
            productosAgregados.push({
                id: '<?= $detalle['id_producto'] ?>',
                descripcion: '<?= htmlspecialchars($detalle['producto']) ?>',
                precio: <?= $detalle['precio_unitario'] ?>,
                cantidad: <?= $detalle['cantidad'] ?>,
                subtotal: <?= $detalle['monto'] ?>
            });
        <?php endforeach; ?>

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
        
            $('input[name="montos_pago[]"]').each(function() {
                const valor = Number($(this).val().replace(',', '.'));
                if (!isNaN(valor)) {
                    totalPagado = Number((totalPagado + valor).toFixed(2));
                }
            });
        
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
        $(document).on('input', '.cantidad, .precio, .monto-pago', function() {
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

        // Evento para usar el monto restante
        $(document).on('click', '.usar-monto-restante', function() {
            const montoRestante = actualizarMontoRestante();
            if (montoRestante > 0) {
                $(this).closest('.forma-pago-item').find('.monto-pago').val(montoRestante.toFixed(2));
                actualizarMontoRestante();
            }
        });

        // Variable global para rastrear productos asignados a formas de pago
        let productosAsignadosPorFormaPago = {};

        // Función para obtener cantidades disponibles de un producto
        function obtenerCantidadDisponible(productoIndex) {
            const producto = productosAgregados[productoIndex];
            let cantidadAsignada = 0;
            
            // Sumar todas las cantidades asignadas a otras formas de pago
            Object.values(productosAsignadosPorFormaPago).forEach(asignaciones => {
                if (asignaciones[productoIndex]) {
                    cantidadAsignada += asignaciones[productoIndex];
                }
            });
            
            return Math.max(0, producto.cantidad - cantidadAsignada);
        }

        // Evento para seleccionar productos específicos
        $(document).on('click', '.seleccionar-productos', function() {
            const $formaPagoItem = $(this).closest('.forma-pago-item');
            const $montoPago = $formaPagoItem.find('.monto-pago');
            const formaPagoIndex = $('.forma-pago-item').index($formaPagoItem);

            let productosHtml = '';
            let hayProductosDisponibles = false;
            
            productosAgregados.forEach((producto, index) => {
                const cantidadDisponible = obtenerCantidadDisponible(index);
                
                // Solo mostrar productos que tengan cantidad disponible
                if (cantidadDisponible > 0) {
                    hayProductosDisponibles = true;
                    const cantidadActualAsignada = productosAsignadosPorFormaPago[formaPagoIndex]?.[index] || 0;
                    const cantidadMaxima = cantidadDisponible + cantidadActualAsignada;
                    
                    productosHtml += `
                        <div class="form-group mb-3 producto-item-container">
                            <div class="d-flex align-items-center">
                                <input type="checkbox" class="form-check-input producto-seleccionado mr-2" 
                                       id="producto${index}" data-index="${index}" 
                                       data-precio="${producto.precio}" 
                                       data-cantidad-total="${producto.cantidad}"
                                       data-cantidad-disponible="${cantidadMaxima}"
                                       ${cantidadActualAsignada > 0 ? 'checked' : ''}>
                                <label class="form-check-label flex-grow-1" for="producto${index}">
                                    ${producto.descripcion} - Disponible: ${cantidadMaxima} x ${producto.precio.toFixed(2)} Bs
                                    ${cantidadActualAsignada > 0 ? `<br><small class="text-info">(Ya asignado: ${cantidadActualAsignada})</small>` : ''}
                                </label>
                            </div>
                            <div class="cantidad-container mt-2" style="display: ${cantidadActualAsignada > 0 ? 'block' : 'none'};">
                                <label>Cantidad a pagar (máx. ${cantidadMaxima}):</label>
                                <input type="number" class="form-control cantidad-a-pagar" 
                                       min="1" max="${cantidadMaxima}" value="${cantidadActualAsignada || 1}"
                                       oninput="this.value = this.value > ${cantidadMaxima} ? ${cantidadMaxima} : Math.abs(this.value)">
                            </div>
                        </div>`;
                }
            });

            if (!hayProductosDisponibles) {
                Swal.fire({
                    title: 'Sin productos disponibles',
                    text: 'Todos los productos ya han sido asignados a otras formas de pago',
                    icon: 'info',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            Swal.fire({
                title: 'Seleccionar Productos',
                html: `
                    <div style="text-align: left; max-height: 300px; overflow-y: auto;">
                        ${productosHtml}
                    </div>
                    <div class="mt-3">
                        <strong>Total seleccionado: </strong>
                        <span id="totalSeleccionado">0.00 Bs</span>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Aplicar',
                cancelButtonText: 'Cancelar',
                didOpen: () => {
                    // Mostrar/ocultar campo de cantidad cuando se selecciona un producto
                    $('.producto-seleccionado').on('change', function() {
                        const $container = $(this).closest('.producto-item-container');
                        const $cantidadContainer = $container.find('.cantidad-container');
                        
                        if ($(this).is(':checked')) {
                            $cantidadContainer.show();
                        } else {
                            $cantidadContainer.hide();
                        }
                        
                        calcularTotalSeleccionado();
                    });

                    // Recalcular total cuando cambia la cantidad
                    $('.cantidad-a-pagar').on('input', function() {
                        const $container = $(this).closest('.producto-item-container');
                        const $checkbox = $container.find('.producto-seleccionado');
                        
                        if ($checkbox.is(':checked')) {
                            calcularTotalSeleccionado();
                        }
                    });

                    function calcularTotalSeleccionado() {
                        let totalSeleccionado = 0;
                        $('.producto-seleccionado:checked').each(function() {
                            const $container = $(this).closest('.producto-item-container');
                            const cantidad = parseInt($container.find('.cantidad-a-pagar').val()) || 0;
                            const precio = parseFloat($(this).data('precio'));
                            totalSeleccionado += cantidad * precio;
                        });
                        $('#totalSeleccionado').text(totalSeleccionado.toFixed(2) + ' Bs');
                    }
                    
                    // Calcular total inicial
                    calcularTotalSeleccionado();
                },
                preConfirm: () => {
                    let total = 0;
                    let detalleProductos = [];
                    let nuevasAsignaciones = {};
                    
                    $('.producto-seleccionado:checked').each(function() {
                        const $container = $(this).closest('.producto-item-container');
                        const cantidad = parseInt($container.find('.cantidad-a-pagar').val()) || 0;
                        const precio = parseFloat($(this).data('precio'));
                        const cantidadDisponible = parseInt($(this).data('cantidad-disponible'));
                        const index = $(this).data('index');
                        
                        if (cantidad > cantidadDisponible) {
                            Swal.showValidationMessage(`La cantidad seleccionada excede la disponible para ${productosAgregados[index].descripcion}`);
                            return false;
                        }
                        
                        total += cantidad * precio;
                        nuevasAsignaciones[index] = cantidad;
                        detalleProductos.push({
                            descripcion: productosAgregados[index].descripcion,
                            cantidad: cantidad,
                            precio: precio,
                            subtotal: cantidad * precio
                        });
                    });
                    
                    return { total, detalleProductos, nuevasAsignaciones };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Actualizar las asignaciones para esta forma de pago
                    productosAsignadosPorFormaPago[formaPagoIndex] = result.value.nuevasAsignaciones;
                    
                    $montoPago.val(result.value.total.toFixed(2));
                    actualizarMontoRestante();
                }
            });
        });

        // Inicializar los cálculos al cargar la página
        recalcularTotalVenta();
        actualizarMontoRestante();

        // Funcionalidad de búsqueda de clientes
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
                    </div>
                `);
                resultados.show();
            }
        });

        // Funcionalidad de búsqueda de productos
        $('#buscar_producto').on('input', function() {
            const busqueda = $(this).val().toLowerCase();
            const resultados = $('#resultados_productos');
            resultados.empty();

            if (busqueda.length < 2) {
                resultados.hide();
                return;
            }

            const productosFiltrados = productos.filter(producto => {
                if (!producto || !producto.descripcion) {
                    return false;
                }
                const descripcionNormalizada = producto.descripcion.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                const busquedaNormalizada = busqueda.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                return descripcionNormalizada.includes(busquedaNormalizada);
            });

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

        // Eventos para seleccionar cliente y producto
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

        // Ocultar resultados al hacer clic fuera
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#buscar_cliente, #resultados_busqueda').length) {
                $('#resultados_busqueda').hide();
            }
            if (!$(e.target).closest('#buscar_producto, #resultados_productos').length) {
                $('#resultados_productos').hide();
            }
        });

        // Función para agregar producto a la tabla
        $('#agregarProducto').click(function() {
            const productoId = $('#id_producto').val();
            const productoTexto = $('#buscar_producto').val();
            const precio = parseFloat($('#id_producto').data('precio'));
            const stock = parseInt($('#id_producto').data('stock'));
            const cantidad = parseInt($('#cantidad').val()) || 1;

            // Validaciones
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

            // Verificar si el producto ya existe en la tabla
            const index = productosAgregados.findIndex(p => p.id === productoId);
            const subtotal = precio * cantidad;

            if (index >= 0) {
                // Actualizar cantidad y subtotal si ya existe
                const nuevaCantidad = productosAgregados[index].cantidad + cantidad;
                if (nuevaCantidad > stock) {
                    Swal.fire('Error', 'No hay suficiente stock para esta cantidad total', 'error');
                    return;
                }
                productosAgregados[index].cantidad = nuevaCantidad;
                productosAgregados[index].subtotal = nuevaCantidad * precio;
            } else {
                // Agregar nuevo producto
                productosAgregados.push({
                    id: productoId,
                    descripcion: productoTexto,
                    precio: precio,
                    cantidad: cantidad,
                    subtotal: subtotal
                });
            }

            // Renderizar tabla y actualizar total
            renderizarTablaProductos();
            recalcularTotalVenta();

            // Limpiar campos
            $('#buscar_producto').val('');
            $('#id_producto').val('');
            $('#cantidad').val(1);
        });

        // Función para renderizar la tabla de productos
        function renderizarTablaProductos() {
            const tbody = $('#productosSeleccionados');
            tbody.empty();

            productosAgregados.forEach((producto, index) => {
                const stockActual = productos.find(p => p.id == producto.id)?.cantidad || 0;
                const row = `
                    <tr>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control buscar-producto" placeholder="Buscar producto..." autocomplete="off"
                                    value="${producto.descripcion} (Stock: ${stockActual})" readonly>
                                <input type="hidden" name="productos[${index}][id_producto]" value="${producto.id}">
                                <input type="hidden" name="productos[${index}][descripcion]" value="${producto.descripcion}">
                            </div>
                        </td>
                        <td>
                            <input type="number" class="form-control cantidad" name="productos[${index}][cantidad]" 
                                value="${producto.cantidad}" min="1" max="${stockActual + producto.cantidad}" required>
                        </td>
                        <td>
                            <input type="number" class="form-control precio" name="productos[${index}][precio]"
                                value="${producto.precio}" step="0.01" required>
                        </td>
                        <td class="subtotal">${producto.subtotal.toFixed(2)}</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-xs eliminar-producto" data-index="${index}">
                                <i class="fa fa-trash-o"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
        }

        // Evento para eliminar producto
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
                    recalcularTotalVenta();
                }
            });
        });

        // Evento para editar cantidad con doble clic
        $(document).on('dblclick', '.cantidad', function() {
            const input = $(this);
            const index = input.closest('tr').find('.eliminar-producto').data('index');
            const producto = productosAgregados[index];
            const stockDisponible = productos.find(p => p.id == producto.id)?.cantidad || 0;
            const stockTotal = stockDisponible + producto.cantidad;

            Swal.fire({
                title: 'Editar Cantidad',
                html: `
                    <div class="form-group">
                        <label>Producto: ${producto.descripcion}</label>
                        <label>Stock disponible: ${stockTotal}</label>
                        <input type="number" id="nuevaCantidad" class="form-control" 
                               value="${producto.cantidad}" min="1" max="${stockTotal}" 
                               placeholder="Ingrese la nueva cantidad">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Actualizar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const nuevaCantidad = parseInt(document.getElementById('nuevaCantidad').value);
                    
                    if (!nuevaCantidad || nuevaCantidad < 1) {
                        Swal.showValidationMessage('La cantidad debe ser mayor a 0');
                        return false;
                    }
                    
                    if (nuevaCantidad > stockTotal) {
                        Swal.showValidationMessage(`No hay suficiente stock. Máximo: ${stockTotal}`);
                        return false;
                    }
                    
                    return nuevaCantidad;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    productosAgregados[index].cantidad = result.value;
                    productosAgregados[index].subtotal = result.value * producto.precio;
                    renderizarTablaProductos();
                    recalcularTotalVenta();
                }
            });
        });

        // Evento delegado para eliminar forma de pago
        $(document).on('click', '.eliminar-forma-pago', function() {
            if ($('#formas_pago_container .forma-pago-item').length > 1) {
                const $formaPagoItem = $(this).closest('.forma-pago-item');
                const formaPagoIndex = $('.forma-pago-item').index($formaPagoItem);
                
                // Limpiar las asignaciones de productos para esta forma de pago
                delete productosAsignadosPorFormaPago[formaPagoIndex];
                
                // Reindexar las asignaciones después de eliminar
                const nuevasAsignaciones = {};
                Object.keys(productosAsignadosPorFormaPago).forEach(index => {
                    const indexNum = parseInt(index);
                    if (indexNum > formaPagoIndex) {
                        nuevasAsignaciones[indexNum - 1] = productosAsignadosPorFormaPago[index];
                    } else if (indexNum < formaPagoIndex) {
                        nuevasAsignaciones[indexNum] = productosAsignadosPorFormaPago[index];
                    }
                });
                productosAsignadosPorFormaPago = nuevasAsignaciones;
                
                $formaPagoItem.remove();
                actualizarMontoRestante();
            } else {
                Swal.fire({
                    title: 'No se puede eliminar',
                    text: 'Debe mantener al menos una forma de pago',
                    icon: 'warning',
                    timer: 2000,
                    timerProgressBar: true
                });
            }
        });

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
                                <div class="input-group">
                                    <input type="number" class="form-control monto-pago" name="montos_pago[]" step="0.01" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-info seleccionar-productos" title="Seleccionar productos para esta forma de pago">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <button type="button" class="btn btn-success usar-monto-restante" title="Completar monto restante">
                                            <i class="fa fa-level-up"></i>
                                        </button>
                                    </div>
                                </div>
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

        // Inicializar los selectores de forma de pago existentes
        $('select[name="formas_pago[]"]').each(function() {
            $(this).trigger('change');
        });

        // Evento para agregar nueva forma de pago
        $('#agregarFormaPago').click(function() {
            crearFormaPago();
        });

        // Eventos delegados para manejar cambios en formas de pago
        $(document).on('change', 'select[name="formas_pago[]"]', function() {
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

        $(document).on('input', '.monto-pago', function() {
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
                        $('#fecha').val('<?= !empty($venta['fecha']) ? (($timestamp = strtotime($venta['fecha'])) !== false ? date('Y-m-d\TH:i', $timestamp) : date('Y-m-d\TH:i')) : date('Y-m-d\TH:i') ?>');
                    } else {
                        $(this).prop('checked', true);
                    }
                });
            }
        });

        // Client search functionality
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
            
            $('select[name="formas_pago[]"]').each(function() {
                const formaPago = $(this).val();
                const referenciaInput = $(this).closest('.forma-pago-item').find('input[name="referencias_pago[]"]');
                
                if (formaPago !== 'EFECTIVO' && !referenciaInput.val().trim()) {
                    referenciasFaltantes = true;
                    return false; // Romper el ciclo
                }
            });
            
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

        if (ventaPagos.length === 0) {
            crearFormaPago();
        }

        actualizarMontoRestante();
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