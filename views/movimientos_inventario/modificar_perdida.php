<?php
// Validaciones de seguridad
if (!isset($perdida) || !$perdida) {
    echo '<div class="alert alert-danger">Error: No se encontraron datos de la pérdida.</div>';
    return;
}

if (!isset($productos) || !$productos) {
    $productos = [];
}
?>
<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-edit"></i> Modificar Pérdida #<?= $perdida['numero_transaccion'] ?></h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-exchange"></i><a href="<?= BASE_URL ?>index.php?action=movimientos-inventario">Movimientos de Inventario</a></li>
                    <li><i class="fa fa-edit"></i> Modificar Pérdida</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading" style="font-size: 1.3em; background: #f5f5f5; border-bottom: 2px solid #e0e0e0;">
                        <i class="fa fa-exclamation-triangle"></i> Modificar Pérdida de Inventario
                    </header>
                    <div class="panel-body">
                        <!-- Información del movimiento original -->
                        <div class="alert alert-info">
                            <h4><i class="fa fa-info-circle"></i> Información del Movimiento Original</h4>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Número de Transacción:</strong><br>
                                    <?= htmlspecialchars($perdida['numero_transaccion']) ?>
                                </div>
                                <div class="col-md-3">
                                    <strong>Producto:</strong><br>
                                    <?= htmlspecialchars($perdida['producto']) ?>
                                </div>
                                <div class="col-md-3">
                                    <strong>Cantidad Original:</strong><br>
                                    <?= number_format($perdida['cantidad'], 2) ?>
                                </div>
                                <div class="col-md-3">
                                    <strong>Precio Original:</strong><br>
                                    <?= number_format($perdida['precio_unitario'], 2) ?> Bs
                                </div>
                            </div>
                            <div class="row" style="margin-top: 10px;">
                                <div class="col-md-6">
                                    <strong>Fecha Original:</strong><br>
                                    <?= date('d/m/Y H:i', strtotime($perdida['fecha_movimiento'])) ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Total Perdido Original:</strong><br>
                                    <?= number_format($perdida['cantidad'] * $perdida['precio_unitario'], 2) ?> Bs
                                </div>
                            </div>
                        </div>

                        <form id="formModificarPerdida" method="POST" action="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=actualizarPerdida&id=<?= $perdida['id'] ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_producto">Producto *</label>
                                        <select class="form-control" id="id_producto" name="id_producto" required>
                                            <option value="">Seleccione un producto</option>
                                            <?php foreach ($productos as $producto): ?>
                                                <option value="<?= $producto['id'] ?>" 
                                                        data-stock="<?= $producto['cantidad'] ?>" 
                                                        data-precio="<?= $producto['precio'] ?>"
                                                        data-precio-compra="<?= $producto['precio_compra'] ?>"
                                                        <?= $producto['id'] == $perdida['id_producto'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($producto['descripcion']) ?> (Stock: <?= $producto['cantidad'] ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha">Fecha de la Pérdida *</label>
                                        <input type="datetime-local" class="form-control" id="fecha" name="fecha" 
                                               value="<?= date('Y-m-d\TH:i', strtotime($perdida['fecha_movimiento'])) ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cantidad">Cantidad Perdida *</label>
                                        <input type="number" class="form-control" id="cantidad" name="cantidad" 
                                               value="<?= $perdida['cantidad'] ?>" step="0.01" min="0.01" required>
                                        <small class="text-muted">Stock disponible: <span id="stock-disponible"><?= $stock_actual ?></span></small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="precio_unitario">Precio Unitario *</label>
                                        <input type="number" class="form-control" id="precio_unitario" name="precio_unitario" 
                                               value="<?= $perdida['precio_unitario'] ?>" step="0.01" min="0.01" required>
                                        <small class="text-muted">Precio sugerido: <span id="precio-sugerido"><?= number_format($perdida['precio_unitario'], 2) ?></span> Bs</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Total Perdido</label>
                                        <div class="form-control" id="total-perdido" style="background-color: #f9f9f9; font-weight: bold;">
                                            <?= number_format($perdida['cantidad'] * $perdida['precio_unitario'], 2) ?> Bs
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="observaciones">Observaciones</label>
                                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3" 
                                                  placeholder="Describa el motivo de la pérdida o cualquier observación relevante..."><?= htmlspecialchars($perdida['observaciones'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary" style="margin-right: 10px;">
                                            <i class="fa fa-save"></i> Guardar Modificación
                                        </button>
                                        <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario" class="btn btn-default">
                                            <i class="fa fa-arrow-left"></i> Cancelar
                                        </a>
                                    </div>
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
    // Función para actualizar datos del producto
    function actualizarDatosProducto() {
        var selectedOption = $('#id_producto option:selected');
        var stock = selectedOption.data('stock') || 0;
        var precioVenta = selectedOption.data('precio') || 0;
        var precioCompra = selectedOption.data('precio-compra') || 0;
        
        // Preferir precio de compra si está disponible, sino usar precio de venta
        var precioSugerido = precioCompra > 0 ? precioCompra : precioVenta;
        
        $('#stock-disponible').text(stock);
        $('#precio-sugerido').text(precioSugerido.toFixed(2));
        
        // Actualizar el precio unitario con el precio sugerido
        if (precioSugerido > 0) {
            $('#precio_unitario').val(precioSugerido.toFixed(2));
        }
        
        // Actualizar validación de cantidad máxima
        $('#cantidad').attr('max', stock);
        
        // Recalcular total
        calcularTotal();
        
        console.log('Datos actualizados - Stock:', stock, 'Precio sugerido:', precioSugerido);
    }

    // Función para calcular el total perdido
    function calcularTotal() {
        var cantidad = parseFloat($('#cantidad').val()) || 0;
        var precio = parseFloat($('#precio_unitario').val()) || 0;
        var total = cantidad * precio;
        
        $('#total-perdido').text(total.toFixed(2) + ' Bs');
    }

    // Event listeners
    $('#id_producto').change(function() {
        actualizarDatosProducto();
    });

    $('#cantidad, #precio_unitario').on('input', function() {
        calcularTotal();
    });

    // Validación del formulario
    $('#formModificarPerdida').submit(function(e) {
        var cantidad = parseFloat($('#cantidad').val());
        var stock = parseFloat($('#stock-disponible').text());
        
        if (cantidad > stock) {
            e.preventDefault();
            Swal.fire({
                title: 'Error',
                text: 'La cantidad perdida (' + cantidad + ') no puede ser mayor al stock disponible (' + stock + ')',
                icon: 'error'
            });
            return false;
        }

        // Confirmación antes de guardar
        e.preventDefault();
        Swal.fire({
            title: '¿Confirmar modificación?',
            text: 'Se creará un nuevo registro de ajuste y se marcará el original como anulado.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, modificar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });

    // Inicializar datos del producto al cargar la página
    actualizarDatosProducto();
});
</script>