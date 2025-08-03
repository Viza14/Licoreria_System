<?php
// Verificar que se hayan pasado los datos necesarios
if (!isset($movimiento) || !isset($productos)) {
    echo '<div class="alert alert-danger">Error: Datos no disponibles</div>';
    exit;
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i>
                        Modificar Movimiento OTRO - Salida
                    </h3>
                    <div class="card-tools">
                        <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Información del movimiento original -->
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-info-circle"></i> Información del Movimiento Original</h5>
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
                    <form action="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=actualizarOtroSalida" method="POST" id="formModificarOtroSalida">
                        <input type="hidden" name="id" value="<?= $movimiento['id'] ?>">
                        
                        <div class="row">
                            <!-- Selector de producto -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_producto">Producto <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="id_producto" name="id_producto" required onchange="actualizarDatosProducto()">
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

                            <!-- Stock actual -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Stock Actual</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="stock_actual" 
                                               value="<?= isset($stock_actual) ? number_format($stock_actual, 2) : '0.00' ?>" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">unidades</span>
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-exclamation-triangle text-warning"></i>
                                        Para salidas, la cantidad no puede exceder el stock disponible
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Cantidad -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cantidad">Cantidad <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="cantidad" name="cantidad" 
                                               value="<?= $movimiento['cantidad'] ?>" 
                                               step="0.01" min="0.01" required onchange="validarStock()">
                                        <div class="input-group-append">
                                            <span class="input-group-text">unidades</span>
                                        </div>
                                    </div>
                                    <div id="stock_warning" class="text-danger" style="display: none;">
                                        <small><i class="fas fa-exclamation-triangle"></i> Cantidad excede el stock disponible</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Precio unitario -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="precio_unitario">Precio Unitario <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control" id="precio_unitario" name="precio_unitario" 
                                               value="<?= $movimiento['precio_unitario'] ?>" 
                                               step="0.01" min="0.01" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Fecha -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecha">Fecha y Hora <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="fecha" name="fecha" 
                                           value="<?= date('Y-m-d\TH:i', strtotime($movimiento['fecha_movimiento'])) ?>" required>
                                </div>
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="observaciones">Observaciones</label>
                                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3" 
                                              placeholder="Ingrese observaciones adicionales sobre la modificación..."><?= htmlspecialchars($movimiento['observaciones'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5>Total del Movimiento</h5>
                                        <div class="d-flex justify-content-between">
                                            <span>Cantidad:</span>
                                            <span id="total_cantidad">0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Precio Unitario:</span>
                                            <span id="total_precio">$0.00</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <strong>Total:</strong>
                                            <strong id="total_general">$0.00</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="float-right">
                                    <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-warning" id="btnGuardar">
                                        <i class="fas fa-save"></i> Guardar Modificación
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: 'Seleccione un producto',
        allowClear: true
    });

    // Calcular total inicial
    calcularTotal();
    validarStock();

    // Eventos para recalcular total y validar stock
    $('#cantidad, #precio_unitario').on('input', function() {
        calcularTotal();
        validarStock();
    });
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
        
        // Recalcular total y validar stock
        calcularTotal();
        validarStock();
    } else {
        document.getElementById('stock_actual').value = '0.00';
        document.getElementById('precio_unitario').value = '';
        calcularTotal();
        validarStock();
    }
}

function calcularTotal() {
    const cantidad = parseFloat(document.getElementById('cantidad').value) || 0;
    const precio = parseFloat(document.getElementById('precio_unitario').value) || 0;
    const total = cantidad * precio;
    
    document.getElementById('total_cantidad').textContent = cantidad.toFixed(2);
    document.getElementById('total_precio').textContent = '$' + precio.toFixed(2);
    document.getElementById('total_general').textContent = '$' + total.toFixed(2);
}

function validarStock() {
    const cantidad = parseFloat(document.getElementById('cantidad').value) || 0;
    const stockActual = parseFloat(document.getElementById('stock_actual').value) || 0;
    const warning = document.getElementById('stock_warning');
    const btnGuardar = document.getElementById('btnGuardar');
    
    if (cantidad > stockActual) {
        warning.style.display = 'block';
        btnGuardar.disabled = true;
        btnGuardar.classList.add('disabled');
    } else {
        warning.style.display = 'none';
        btnGuardar.disabled = false;
        btnGuardar.classList.remove('disabled');
    }
}

// Validación del formulario
document.getElementById('formModificarOtroSalida').addEventListener('submit', function(e) {
    const cantidad = parseFloat(document.getElementById('cantidad').value);
    const precio = parseFloat(document.getElementById('precio_unitario').value);
    const producto = document.getElementById('id_producto').value;
    const stockActual = parseFloat(document.getElementById('stock_actual').value) || 0;
    
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
    
    if (cantidad > stockActual) {
        e.preventDefault();
        Swal.fire({
            title: 'Error',
            text: 'La cantidad no puede exceder el stock disponible (' + stockActual.toFixed(2) + ' unidades)',
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