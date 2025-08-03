<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-minus-circle"></i> Registrar Pérdida</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?= BASE_URL ?>">Inicio</a></li>
                    <li><i class="fa fa-exchange"></i><a href="<?= BASE_URL ?>index.php?action=movimientos-inventario">Movimientos</a></li>
                    <li><i class="fa fa-minus-circle"></i>Registrar Pérdida</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <i class="fa fa-minus-circle text-danger"></i> <?= isset($es_edicion) && $es_edicion ? 'Editar' : 'Registrar' ?> Pérdida de Producto
                    </header>
                    <div class="panel-body">
                        <form id="formPerdida" method="POST" action="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=<?= isset($es_edicion) && $es_edicion ? 'actualizarPerdida' : 'guardarPerdida' ?>">
                            <?php if (isset($es_edicion) && $es_edicion && isset($movimiento_editar) && $movimiento_editar): ?>
                                <input type="hidden" name="id" value="<?= $movimiento_editar['id'] ?>">
                            <?php endif; ?>
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
                                        <small class="text-muted">Stock disponible: <span id="stock-disponible">0</span></small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="precio_unitario">Precio Unitario de Venta</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">Bs</span>
                                            <input type="number" class="form-control" id="precio_unitario" 
                                                   name="precio_unitario" step="0.01" 
                                                   value="<?= isset($movimiento_editar) && $movimiento_editar ? $movimiento_editar['precio_unitario'] : '' ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="descripcion">Motivo de la Pérdida <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="descripcion" name="descripcion" 
                                                  rows="4" required placeholder="Describa el motivo de la pérdida (robo, daño, vencimiento, etc.)"><?= isset($movimiento_editar) && $movimiento_editar ? htmlspecialchars($movimiento_editar['observaciones']) : '' ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-warning">
                                        <i class="fa fa-warning"></i> 
                                        <strong>Importante:</strong> Esta acción reducirá el stock del producto seleccionado y no se puede deshacer.
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-danger btn-lg">
                                            <i class="fa fa-save"></i> <?= isset($es_edicion) && $es_edicion ? 'Actualizar Pérdida' : 'Registrar Pérdida' ?>
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
    console.log('Inicializando formulario de pérdida');
    
    // Función para actualizar stock y precio
    function actualizarDatosProducto() {
        const selectedOption = $('#id_producto').find('option:selected');
        const stock = selectedOption.data('stock') || 0;
        const precio = selectedOption.data('precio') || 0;
        
        console.log('Producto seleccionado:', {
            id: selectedOption.val(),
            descripcion: selectedOption.text(),
            stock: stock,
            precio: precio
        });
        
        $('#stock-disponible').text(stock);
        
        // En modo edición, mantener el precio original, en modo nuevo usar el precio del producto
        <?php if (isset($es_edicion) && $es_edicion): ?>
        // En modo edición, no cambiar el precio que ya está cargado
        if (!$('#precio_unitario').val()) {
            $('#precio_unitario').val(precio);
        }
        <?php else: ?>
        // En modo nuevo, cargar el precio del producto
        $('#precio_unitario').val(precio);
        <?php endif; ?>
        
        $('#cantidad').attr('max', stock);
        
        if (stock === 0) {
            $('#cantidad').attr('disabled', true);
            Swal.fire({
                title: 'Sin Stock',
                text: 'Este producto no tiene stock disponible',
                icon: 'warning'
            });
        } else {
            $('#cantidad').attr('disabled', false);
        }
    }
    
    // Inicializar datos en modo edición
    <?php if (isset($es_edicion) && $es_edicion && isset($movimiento_editar) && $movimiento_editar): ?>
    console.log('Modo edición detectado');
    // Disparar el evento change para cargar los datos del producto seleccionado
    setTimeout(function() {
        $('#id_producto').trigger('change');
    }, 100);
    <?php endif; ?>
    
    // Manejar cambio de producto
    $('#id_producto').change(function() {
        console.log('Cambio de producto detectado');
        actualizarDatosProducto();
    });
    
    // Cargar datos iniciales si hay un producto seleccionado
    if ($('#id_producto').val()) {
        console.log('Producto ya seleccionado al cargar');
        actualizarDatosProducto();
    }
    
    // Validar cantidad
    $('#cantidad').on('input', function() {
        const cantidad = parseInt($(this).val());
        const stockDisponible = parseInt($('#stock-disponible').text());
        
        console.log('Validando cantidad:', cantidad, 'vs stock:', stockDisponible);
        
        if (cantidad > stockDisponible) {
            $(this).val(stockDisponible);
            Swal.fire({
                title: 'Cantidad Excedida',
                text: 'La cantidad no puede ser mayor al stock disponible',
                icon: 'warning'
            });
        }
    });
    
    // Manejar envío del formulario
    $('#formPerdida').submit(function(e) {
        e.preventDefault();
        
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
        
        Swal.fire({
            title: '¿Confirmar Pérdida?',
            text: 'Esta acción reducirá el stock del producto y no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, registrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar loading
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Registrando la pérdida',
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