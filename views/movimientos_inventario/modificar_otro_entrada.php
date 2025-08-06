<?php
// Verificar que se hayan pasado los datos necesarios
if (!isset($movimiento) || !isset($productos)) {
    echo '<div class="alert alert-danger">Error: Datos no disponibles</div>';
    exit;
}
?>

<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-edit"></i> Modificar Movimiento OTRO - Entrada</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-exchange"></i><a href="<?php echo BASE_URL; ?>index.php?action=movimientos-inventario">Movimientos de Inventario</a></li>
                    <li><i class="fa fa-edit"></i> Modificar OTRO - Entrada</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Modificar Movimiento OTRO - Entrada
                        <span class="tools pull-right">
                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario" class="btn btn-default btn-sm">
                                <i class="fa fa-arrow-left"></i> Volver
                            </a>
                        </span>
                    </header>
                    <div class="panel-body">
                    <!-- Información del movimiento original -->
                    <div class="alert alert-info">
                        <h4><i class="fa fa-info-circle"></i> Información del Movimiento Original</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Número de Transacción:</strong><br>
                                <?= htmlspecialchars($movimiento['numero_transaccion']) ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Fecha Original:</strong><br>
                                <?= date('d/m/Y H:i', strtotime($movimiento['fecha_movimiento'])) ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Cantidad Original:</strong><br>
                                <?= number_format($movimiento['cantidad'], 2) ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Precio Original:</strong><br>
                                $<?= number_format($movimiento['precio_unitario'], 2) ?>
                            </div>
                        </div>
                        <?php if (!empty($movimiento['observaciones'])): ?>
                        <div class="row mt-2">
                            <div class="col-12">
                                <strong>Observaciones Originales:</strong><br>
                                <?= htmlspecialchars($movimiento['observaciones']) ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                        <!-- Formulario de modificación -->
                        <form class="form-horizontal" action="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=actualizarOtroEntrada" method="POST" id="formModificarOtroEntrada">
                            <input type="hidden" name="id" value="<?= $movimiento['id'] ?>">
                            
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Producto <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="id_producto" name="id_producto" required onchange="actualizarDatosProducto()">
                                        <option value="">Seleccione un producto</option>
                                        <?php foreach ($productos as $producto): ?>
                                            <option value="<?= $producto['id'] ?>" 
                                                    data-stock="<?= $producto['cantidad'] ?>" 
                                                    data-precio="<?= $producto['precio'] ?>"
                                                    <?= ($producto['id'] == $movimiento['id_producto']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($producto['descripcion']) ?> 
                                                (Stock: <?= $producto['cantidad'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Stock Actual</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="stock_actual" 
                                               value="<?= isset($stock_actual) ? number_format($stock_actual, 2) : '0.00' ?>" readonly>
                                        <span class="input-group-addon">unidades</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Cantidad <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="cantidad" name="cantidad" 
                                               value="<?= $movimiento['cantidad'] ?>" 
                                               step="0.01" min="0.01" required>
                                        <span class="input-group-addon">unidades</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Precio Unitario <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <span class="input-group-addon">Bs</span>
                                        <input type="number" class="form-control" id="precio_unitario" name="precio_unitario" 
                                               value="<?= $movimiento['precio_unitario'] ?>" 
                                               step="0.01" min="0.01" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Fecha y Hora <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="datetime-local" class="form-control" id="fecha" name="fecha" 
                                           value="<?= date('Y-m-d\TH:i', strtotime($movimiento['fecha_movimiento'])) ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Observaciones</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3" 
                                              placeholder="Ingrese observaciones adicionales sobre la modificación..."><?= htmlspecialchars($movimiento['observaciones'] ?? '') ?></textarea>
                                </div>
                            </div>

                            <!-- Total -->
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Total del Movimiento</label>
                                <div class="col-sm-10">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <strong>Cantidad:</strong> <span id="total_cantidad">0.00</span> unidades
                                                </div>
                                                <div class="col-sm-6">
                                                    <strong>Precio Unitario:</strong> Bs <span id="total_precio">0.00</span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="text-center">
                                                <h4><strong>Total: Bs <span id="total_general">0.00</span></strong></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-primary">
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
    // Calcular total inicial
    calcularTotal();

    // Eventos para recalcular total
    $('#cantidad, #precio_unitario').on('input', calcularTotal);
});

function actualizarDatosProducto() {
    const select = document.getElementById('id_producto');
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption.value) {
        const stock = selectedOption.getAttribute('data-stock');
        const precio = selectedOption.getAttribute('data-precio');
        
        // Actualizar stock mostrado
        document.getElementById('stock_actual').value = parseFloat(stock).toFixed(2);
        
        // Sugerir precio
        document.getElementById('precio_unitario').value = parseFloat(precio).toFixed(2);
        
        // Recalcular total
        calcularTotal();
    } else {
        document.getElementById('stock_actual').value = '0.00';
        document.getElementById('precio_unitario').value = '';
        calcularTotal();
    }
}

function calcularTotal() {
    const cantidad = parseFloat(document.getElementById('cantidad').value) || 0;
    const precio = parseFloat(document.getElementById('precio_unitario').value) || 0;
    const total = cantidad * precio;
    
    document.getElementById('total_cantidad').textContent = cantidad.toFixed(2);
    document.getElementById('total_precio').textContent = precio.toFixed(2);
    document.getElementById('total_general').textContent = total.toFixed(2);
}

// Validación del formulario
document.getElementById('formModificarOtroEntrada').addEventListener('submit', function(e) {
    const cantidad = parseFloat(document.getElementById('cantidad').value);
    const precio = parseFloat(document.getElementById('precio_unitario').value);
    const producto = document.getElementById('id_producto').value;
    
    if (!producto) {
        e.preventDefault();
        Swal.fire({
            title: 'Error',
            text: 'Debe seleccionar un producto',
            icon: 'error'
        });
        return;
    }
    
    if (cantidad <= 0) {
        e.preventDefault();
        Swal.fire({
            title: 'Error',
            text: 'La cantidad debe ser mayor a cero',
            icon: 'error'
        });
        return;
    }
    
    if (precio <= 0) {
        e.preventDefault();
        Swal.fire({
            title: 'Error',
            text: 'El precio unitario debe ser mayor a cero',
            icon: 'error'
        });
        return;
    }
    
    // Confirmación antes de guardar
    e.preventDefault();
    Swal.fire({
        title: '¿Confirmar modificación?',
        text: 'Se creará un nuevo registro de ajuste y se marcará el movimiento original como anulado.',
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
</script>