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
                    <header class="panel-heading">
                        Datos de la Venta
                    </header>
                    <div class="panel-body">
                        <form action="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=actualizarVenta&id=<?= $venta['id'] ?>" method="post">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha">Fecha y Hora</label>
                                        <input type="datetime-local" class="form-control" id="fecha" name="fecha" 
                                               value="<?= date('Y-m-d\TH:i', strtotime($venta['fecha'])) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cedula_cliente">Cliente</label>
                                        <select class="form-control" id="cedula_cliente" name="cedula_cliente" required>
                                            <?php foreach ($clientes as $cliente): ?>
                                                <option value="<?= $cliente['cedula'] ?>" 
                                                    <?= $cliente['cedula'] == $venta['cedula_cliente'] ? 'selected' : '' ?>>
                                                    <?= $cliente['nombres'] . ' ' . $cliente['apellidos'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Forma de Pago</label>
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-default <?= $venta['forma_pago'] == 'EFECTIVO' ? 'active' : '' ?>">
                                                <input type="radio" name="forma_pago" value="EFECTIVO" <?= $venta['forma_pago'] == 'EFECTIVO' ? 'checked' : '' ?>> Efectivo
                                            </label>
                                            <label class="btn btn-default <?= $venta['forma_pago'] == 'TARJETA' ? 'active' : '' ?>">
                                                <input type="radio" name="forma_pago" value="TARJETA" <?= $venta['forma_pago'] == 'TARJETA' ? 'checked' : '' ?>> Tarjeta
                                            </label>
                                            <label class="btn btn-default <?= $venta['forma_pago'] == 'PAGO_MOVIL' ? 'active' : '' ?>">
                                                <input type="radio" name="forma_pago" value="PAGO_MOVIL" <?= $venta['forma_pago'] == 'PAGO_MOVIL' ? 'checked' : '' ?>> Pago Móvil
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="referencia-container" style="<?= $venta['forma_pago'] == 'EFECTIVO' ? 'display: none;' : '' ?>">
                                    <div class="form-group">
                                        <label for="referencia_pago">Referencia</label>
                                        <input type="text" class="form-control" id="referencia_pago" name="referencia_pago" 
                                               value="<?= $venta['referencia_pago'] ?>" <?= $venta['forma_pago'] == 'EFECTIVO' ? '' : 'required' ?>>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="panel">
                                        <div class="panel-heading">
                                            <h4>Productos</h4>
                                            <button type="button" class="btn btn-primary btn-xs" id="add-product">
                                                <i class="fa fa-plus"></i> Agregar Producto
                                            </button>
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover" id="productos-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Producto</th>
                                                            <th>Cantidad</th>
                                                            <th>Precio Unitario</th>
                                                            <th>Subtotal</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($detalles as $detalle): ?>
                                                            <tr>
                                                                <td>
                                                                    <select class="form-control producto-select" name="productos[<?= $detalle['id'] ?>][id]" required>
                                                                        <?php foreach ($productos as $producto): ?>
                                                                            <option value="<?= $producto['id'] ?>" 
                                                                                data-precio="<?= $producto['precio'] ?>"
                                                                                <?= $producto['id'] == $detalle['id_producto'] ? 'selected' : '' ?>>
                                                                                <?= $producto['descripcion'] ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control cantidad" 
                                                                           name="productos[<?= $detalle['id'] ?>][cantidad]" 
                                                                           value="<?= $detalle['cantidad'] ?>" min="1" required>
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control precio" 
                                                                           name="productos[<?= $detalle['id'] ?>][precio]" 
                                                                           value="<?= $detalle['monto'] / $detalle['cantidad'] ?>" step="0.01" required>
                                                                </td>
                                                                <td class="subtotal"><?= number_format($detalle['monto'], 2) ?></td>
                                                                <td>
                                                                    <button type="button" class="btn btn-danger btn-xs remove-product">
                                                                        <i class="fa fa-trash-o"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                                            <td id="total-venta"><?= number_format($venta['monto_total'], 2) ?></td>
                                                            <td></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-save"></i> Guardar Modificación
                                    </button>
                                    <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario" class="btn btn-default">
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

<script>
$(document).ready(function() {
    // Payment method handler
    $('input[name="forma_pago"]').change(function() {
        if ($(this).val() === 'EFECTIVO') {
            $('#referencia-container').hide();
            $('#referencia_pago').removeAttr('required');
        } else {
            $('#referencia-container').show();
            $('#referencia_pago').attr('required', 'required');
        }
    });

    // Product price update handler
    $(document).on('change', '.producto-select', function() {
        var precio = $(this).find(':selected').data('precio');
        $(this).closest('tr').find('.precio').val(precio);
        calcularSubtotal($(this).closest('tr'));
    });

    // Subtotal calculation handler
    $(document).on('input', '.cantidad, .precio', function() {
        calcularSubtotal($(this).closest('tr'));
    });

    // Calculate subtotal for a row
    function calcularSubtotal(row) {
        var cantidad = parseFloat(row.find('.cantidad').val()) || 0;
        var precio = parseFloat(row.find('.precio').val()) || 0;
        var subtotal = cantidad * precio;
        row.find('.subtotal').text(subtotal.toFixed(2));
        calcularTotal();
    }

    // Calculate total amount
    function calcularTotal() {
        var total = 0;
        $('#productos-table tbody tr').each(function() {
            var subtotal = parseFloat($(this).find('.subtotal').text()) || 0;
            total += subtotal;
        });
        $('#total-venta').text(total.toFixed(2));
    }

    // Add new product row
    $('#add-product').click(function() {
        var newRow = `
            <tr>
                <td>
                    <select class="form-control producto-select" name="productos[new_${Date.now()}][id]" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach ($productos as $producto): ?>
                            <option value="<?= $producto['id'] ?>" data-precio="<?= $producto['precio'] ?>">
                                <?= $producto['descripcion'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control cantidad" name="productos[new_${Date.now()}][cantidad]" value="1" min="1" required>
                </td>
                <td>
                    <input type="number" class="form-control precio" name="productos[new_${Date.now()}][precio]" value="0" step="0.01" required>
                </td>
                <td class="subtotal">0.00</td>
                <td>
                    <button type="button" class="btn btn-danger btn-xs remove-product">
                        <i class="fa fa-trash-o"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#productos-table tbody').append(newRow);
    });

    // Remove product row
    $(document).on('click', '.remove-product', function() {
        $(this).closest('tr').remove();
        calcularTotal();
    });
});
</script>