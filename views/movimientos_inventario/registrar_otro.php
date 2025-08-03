<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-cog"></i> Registrar Otro Movimiento</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?= BASE_URL ?>">Inicio</a></li>
                    <li><i class="fa fa-exchange"></i><a href="<?= BASE_URL ?>index.php?action=movimientos-inventario">Movimientos</a></li>
                    <li><i class="fa fa-cog"></i>Otro Movimiento</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <i class="fa fa-<?= $tipo === 'ENTRADA' ? 'plus' : 'minus' ?>-circle text-<?= $tipo === 'ENTRADA' ? 'success' : 'danger' ?>"></i> 
                        <?= isset($es_edicion) && $es_edicion ? 'Editar' : 'Registrar' ?> <?= $tipo === 'ENTRADA' ? 'Entrada' : 'Salida' ?> por Otro Motivo
                    </header>
                    <div class="panel-body">
                        <form id="formOtroMovimiento" method="POST" action="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=guardarOtro">
                            <input type="hidden" name="tipo_movimiento" value="<?= $tipo ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_producto">Producto <span class="text-danger">*</span></label>
                                        <select class="form-control" id="id_producto" name="id_producto" required>
                                            <option value="">Seleccione un producto</option>
                                            <?php foreach ($productos as $producto): ?>
                                                <option value="<?= $producto['id'] ?>" 
                                                        data-stock="<?= $producto['cantidad'] ?>"
                                                        data-precio="<?= $producto['precio'] ?>"
                                                        data-precio-compra="<?= $producto['precio_compra'] ?? 0 ?>"
                                                        <?= (isset($movimiento_editar) && $movimiento_editar && $movimiento_editar['id_producto'] == $producto['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($producto['descripcion']) ?> 
                                                    (Stock: <?= $producto['cantidad'] ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cantidad">Cantidad <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="cantidad" name="cantidad" 
                                               min="1" value="<?= isset($movimiento_editar) && $movimiento_editar ? $movimiento_editar['cantidad'] : '' ?>" required>
                                        <small class="text-muted">
                                            <?php if ($tipo === 'SALIDA'): ?>
                                                Stock disponible: <span id="stock-disponible">0</span>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="precio_unitario">Precio Unitario</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Bs</span>
                                            </div>
                                            <input type="number" class="form-control" id="precio_unitario"
                                                   name="precio_unitario" step="0.01" 
                                                   value="<?= isset($movimiento_editar) && $movimiento_editar ? $movimiento_editar['precio_unitario'] : '' ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="observaciones">Observaciones <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="observaciones" name="observaciones" 
                                                  rows="4" required placeholder="Describa el motivo de esta <?= strtolower($tipo) ?> (devolución, donación, ajuste manual, etc.)"><?= isset($movimiento_editar) && $movimiento_editar ? htmlspecialchars($movimiento_editar['observaciones']) : '' ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-<?= $tipo === 'ENTRADA' ? 'success' : 'warning' ?>">
                                        <i class="fa fa-info-circle"></i> 
                                        <strong>Información:</strong> 
                                        <?php if ($tipo === 'ENTRADA'): ?>
                                            Esta acción aumentará el stock del producto seleccionado.
                                        <?php else: ?>
                                            Esta acción reducirá el stock del producto seleccionado y no se puede deshacer.
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-<?= $tipo === 'ENTRADA' ? 'success' : 'danger' ?> btn-lg">
                                            <i class="fa fa-save"></i> Registrar <?= $tipo === 'ENTRADA' ? 'Entrada' : 'Salida' ?>
                                        </button>
                                        <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=generarMovimiento" 
                                           class="btn btn-default btn-lg">
                                            <i class="fa fa-arrow-left"></i> Volver
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

<script>
$(document).ready(function() {
    const tipoMovimiento = '<?= $tipo ?>';
    
    // Inicializar datos en modo edición
    <?php if (isset($es_edicion) && $es_edicion && isset($movimiento_editar) && $movimiento_editar): ?>
    // Disparar el evento change para cargar los datos del producto seleccionado
    actualizarDatosProducto();
    <?php endif; ?>
    
    // Función para actualizar datos del producto
    function actualizarDatosProducto() {
        const selectedOption = $('#id_producto')[0].options[$('#id_producto')[0].selectedIndex];
        const precioVenta = selectedOption.getAttribute('data-precio');
        const precioCompra = selectedOption.getAttribute('data-precio-compra');
        const stock = selectedOption.getAttribute('data-stock');
        
        console.log('Datos del producto:', {
            precioVenta: precioVenta,
            precioCompra: precioCompra,
            stock: stock
        });
        
        if ($('#id_producto').val() && (precioVenta || precioCompra)) {
            // Usar precio de compra si está disponible, sino precio de venta
            const precio = precioCompra && precioCompra !== '0' ? precioCompra : precioVenta;
            
            // En modo edición, mantener el precio original, en modo nuevo usar el precio del producto
            <?php if (isset($es_edicion) && $es_edicion): ?>
            // En modo edición, no cambiar el precio que ya está cargado
            if (!$('#precio_unitario').val()) {
                $('#precio_unitario').val(parseFloat(precio).toFixed(2));
            }
            <?php else: ?>
            // En modo nuevo, cargar el precio del producto
            $('#precio_unitario').val(parseFloat(precio).toFixed(2));
            <?php endif; ?>
            $('#precio_unitario').prop('disabled', true);
        } else {
            // Si no hay producto seleccionado, limpiar y habilitar el campo
            $('#precio_unitario').val('');
            $('#precio_unitario').prop('disabled', false);
        }
        
        if (stock) {
            $('#stock-disponible').text(stock);
            $('#cantidad').attr('max', stock);
        } else {
            $('#stock-disponible').text('0');
            $('#cantidad').attr('max', 0);
        }
    }
    
    // Manejar cambio de producto
    $('#id_producto').change(actualizarDatosProducto);
    
    // Validar cantidad para salidas
    if (tipoMovimiento === 'SALIDA') {
        $('#cantidad').on('input', function() {
            const cantidad = parseInt($(this).val());
            const stockDisponible = parseInt($('#stock-disponible').text());
            
            if (cantidad > stockDisponible) {
                $(this).val(stockDisponible);
                Swal.fire({
                    title: 'Cantidad Excedida',
                    text: 'La cantidad no puede ser mayor al stock disponible',
                    icon: 'warning'
                });
            }
        });
    }
    
    // Manejar envío del formulario
    $('#formOtroMovimiento').submit(function(e) {
        e.preventDefault();
        
        if (tipoMovimiento === 'SALIDA') {
            const cantidad = parseInt($('#cantidad').val());
            const stockDisponible = parseInt($('#stock-disponible').text());
            
            if (cantidad > stockDisponible) {
                Swal.fire({
                    title: 'Error',
                    text: 'La cantidad no puede ser mayor al stock disponible',
                    icon: 'error'
                });
                return;
            }
        }
        
        const accion = tipoMovimiento === 'ENTRADA' ? 'aumentará' : 'reducirá';
        const confirmText = tipoMovimiento === 'ENTRADA' ? 'Sí, registrar entrada' : 'Sí, registrar salida';
        
        Swal.fire({
            title: '¿Confirmar ' + (tipoMovimiento === 'ENTRADA' ? 'Entrada' : 'Salida') + '?',
            text: 'Esta acción ' + accion + ' el stock del producto',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: tipoMovimiento === 'ENTRADA' ? '#28a745' : '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: confirmText,
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Habilitar temporalmente el campo de precio para que se envíe
                $('#precio_unitario').prop('disabled', false);
                
                // Mostrar loading
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Registrando el movimiento',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Enviar formulario
                this.submit();
            }
        });
    });
});
</script>
    </section>
</section>