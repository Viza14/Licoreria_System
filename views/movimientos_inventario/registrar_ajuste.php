<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-edit"></i> <?= isset($es_edicion) && $es_edicion ? 'Editar' : 'Registrar' ?> Ajuste</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?= BASE_URL ?>">Inicio</a></li>
                    <li><i class="fa fa-exchange"></i><a href="<?= BASE_URL ?>index.php?action=movimientos-inventario">Movimientos</a></li>
                    <li><i class="fa fa-edit"></i><?= isset($es_edicion) && $es_edicion ? 'Editar' : 'Registrar' ?> Ajuste</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <?php if (isset($es_edicion) && $es_edicion && isset($movimiento_editar) && $movimiento_editar): ?>
        <!-- Mostrar información del movimiento a editar -->
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <i class="fa fa-info-circle"></i> Información del Ajuste Original
                    </header>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Número de Transacción:</strong></td>
                                    <td><?= htmlspecialchars($movimiento_editar['numero_transaccion']) ?></td>
                                    <td><strong>Fecha:</strong></td>
                                    <td><?= htmlspecialchars($movimiento_editar['fecha_movimiento']) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Producto:</strong></td>
                                    <td><?= htmlspecialchars($movimiento_editar['descripcion_producto'] ?? 'N/A') ?></td>
                                    <td><strong>Tipo:</strong></td>
                                    <td>
                                        <span class="label label-<?= $movimiento_editar['tipo_movimiento'] === 'ENTRADA' ? 'success' : 'danger' ?>">
                                            <?= htmlspecialchars($movimiento_editar['tipo_movimiento']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Cantidad:</strong></td>
                                    <td><?= htmlspecialchars($movimiento_editar['cantidad']) ?></td>
                                    <td><strong>Precio Unitario:</strong></td>
                                    <td>Bs <?= htmlspecialchars($movimiento_editar['precio_unitario']) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Observaciones:</strong></td>
                                    <td colspan="3"><?= htmlspecialchars($movimiento_editar['observaciones'] ?: 'N/A') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <i class="fa fa-edit text-warning"></i> <?= isset($es_edicion) && $es_edicion ? 'Editar' : 'Registrar' ?> Ajuste de Inventario
                    </header>
                    <div class="panel-body">
                        <form id="formAjuste" method="POST" action="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=guardarAjuste">
                            <?php if (isset($es_edicion) && $es_edicion && isset($movimiento_editar)): ?>
                                <input type="hidden" name="id_movimiento_original" value="<?= $movimiento_editar['id'] ?>">
                                <input type="hidden" name="id_producto" value="<?= $movimiento_editar['id_producto'] ?>">
                                <input type="hidden" name="tipo_movimiento" value="<?= $movimiento_editar['tipo_movimiento'] ?>">
                            <?php else: ?>
                                <!-- Campos para búsqueda de movimiento original -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i> 
                                            <strong>Información:</strong> Para registrar un ajuste, primero debe buscar el movimiento original que desea ajustar.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="numero_transaccion">Número de Transacción a Ajustar <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="numero_transaccion" 
                                                   name="numero_transaccion" required 
                                                   placeholder="Ej: TXN00000030">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>&nbsp;</label><br>
                                            <button type="button" id="btnBuscarMovimiento" class="btn btn-primary btn-lg">
                                                <i class="fa fa-search"></i> Buscar Movimiento
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Campos ocultos que se llenarán después de la búsqueda -->
                                <input type="hidden" name="id_movimiento_original" id="id_movimiento_original">
                                <input type="hidden" name="id_producto" id="id_producto">
                                <input type="hidden" name="tipo_movimiento" id="tipo_movimiento">
                            <?php endif; ?>

                            <!-- Formulario de ajuste (inicialmente oculto si no es edición) -->
                            <div id="formularioAjuste" <?= !(isset($es_edicion) && $es_edicion) ? 'style="display: none;"' : '' ?>>
                                <hr>
                                <h4><i class="fa fa-edit"></i> Datos del Ajuste</h4>
                                
                                <!-- Información de la transacción -->
                                <div id="infoTransaccion" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="alert alert-info">
                                                <h5><i class="fa fa-info-circle"></i> Información de la Transacción</h5>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <strong>Número:</strong> <span id="info_numero_transaccion"></span>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <strong>Tipo:</strong> <span id="info_tipo_movimiento"></span>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <strong>Subtipo:</strong> <span id="info_subtipo_movimiento"></span>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <strong>Fecha:</strong> <span id="info_fecha_movimiento"></span>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-top: 10px;">
                                                    <div class="col-md-6">
                                                        <strong>Proveedor:</strong> <span id="info_proveedor"></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Observaciones:</strong> <span id="info_observaciones"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabla de productos (para compras) -->
                                <div id="tablaProductosCompra" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h5><i class="fa fa-list"></i> Productos de la Compra</h5>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Producto</th>
                                                            <th>Proveedor</th>
                                                            <th>Cantidad Original</th>
                                                            <th>Precio Original</th>
                                                            <th>Nueva Cantidad</th>
                                                            <th>Nuevo Precio</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="productosCompraBody">
                                                        <!-- Los productos se cargarán aquí dinámicamente -->
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button type="button" id="btnGuardarAjustesCompra" class="btn btn-warning btn-lg">
                                                        <i class="fa fa-save"></i> Guardar Ajustes de Compra
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Formulario individual (para otros tipos de movimiento) -->
                                <div id="formularioIndividual">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Producto</label>
                                                <input type="text" class="form-control" id="producto_nombre" readonly 
                                                       value="<?= isset($movimiento_editar) ? htmlspecialchars($movimiento_editar['descripcion_producto'] ?? '') : '' ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Transacción Original</label>
                                                <input type="text" class="form-control" id="transaccion_original" readonly 
                                                       value="<?= isset($movimiento_editar) ? htmlspecialchars($movimiento_editar['numero_transaccion']) : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="cantidad">Nueva Cantidad <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="cantidad" 
                                                       name="cantidad" step="0.01" min="0" required 
                                                       value="<?= isset($movimiento_editar) ? htmlspecialchars($movimiento_editar['cantidad']) : '' ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="precio_unitario">Nuevo Precio Unitario <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Bs</span>
                                                    </div>
                                                    <input type="number" class="form-control" id="precio_unitario" 
                                                           name="precio_unitario" step="0.01" min="0" required 
                                                           value="<?= isset($movimiento_editar) ? htmlspecialchars($movimiento_editar['precio_unitario']) : '' ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="fecha_movimiento">Fecha del Ajuste</label>
                                                <input type="datetime-local" class="form-control" id="fecha_movimiento" 
                                                       name="fecha_movimiento" 
                                                       value="<?= isset($movimiento_editar) && !empty($movimiento_editar['fecha_movimiento']) ? 
                                                               (($timestamp = strtotime($movimiento_editar['fecha_movimiento'])) !== false ? 
                                                                date('Y-m-d\TH:i', $timestamp) : date('Y-m-d\TH:i')) : date('Y-m-d\TH:i') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="observaciones">Motivo del Ajuste <span class="text-danger">*</span></label>
                                                <textarea class="form-control" id="observaciones" 
                                                          name="observaciones" rows="3" required 
                                                          placeholder="Describa el motivo del ajuste (error en cantidad, precio incorrecto, etc.)"><?= isset($movimiento_editar) ? htmlspecialchars($movimiento_editar['observaciones']) : '' ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="alert alert-warning">
                                                <i class="fa fa-exclamation-triangle"></i> 
                                                <strong>Importante:</strong> 
                                                Este ajuste creará un nuevo registro y marcará el original como anulado. 
                                                Los cambios en el inventario se aplicarán automáticamente.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-warning btn-lg">
                                                    <i class="fa fa-save"></i> Guardar Ajuste
                                                </button>
                                                <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=generarMovimiento" 
                                                   class="btn btn-default btn-lg">
                                                    <i class="fa fa-arrow-left"></i> Volver
                                                </a>
                                            </div>
                                        </div>
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
    let productosCompra = []; // Array para almacenar los productos de la compra
    
    // Manejar búsqueda de movimiento
    $('#btnBuscarMovimiento').click(function() {
        const numeroTransaccion = $('#numero_transaccion').val().trim();
        
        if (!numeroTransaccion) {
            Swal.fire({
                title: 'Error',
                text: 'Por favor ingrese un número de transacción',
                icon: 'error'
            });
            return;
        }
        
        // Mostrar loading
        Swal.fire({
            title: 'Buscando...',
            text: 'Buscando el movimiento',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Realizar búsqueda AJAX
        $.ajax({
            url: '<?= BASE_URL ?>index.php?action=movimientos-inventario&method=buscarTransaccion',
            method: 'POST',
            data: {
                numero_transaccion: numeroTransaccion
            },
            dataType: 'json',
            success: function(response) {
                Swal.close();
                
                if (response.success) {
                    // Verificar si es una pérdida y redirigir
                    if (response.es_perdida) {
                        Swal.fire({
                            title: '¡Pérdida Encontrada!',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.redirect_url;
                        });
                        return;
                    }
                    
                    // Verificar si es una venta y redirigir
                    if (response.es_venta) {
                        Swal.fire({
                            title: '¡Venta Encontrada!',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.redirect_url;
                        });
                        return;
                    }
                    
                    // Mostrar información de la transacción
                    $('#info_numero_transaccion').text(response.movimiento.numero_transaccion);
                    $('#info_tipo_movimiento').text(response.movimiento.tipo_movimiento);
                    $('#info_subtipo_movimiento').text(response.movimiento.subtipo_movimiento);
                    $('#info_fecha_movimiento').text(response.movimiento.fecha_movimiento);
                    $('#info_proveedor').text(response.movimiento.proveedor || 'N/A');
                    $('#info_observaciones').text(response.movimiento.observaciones || 'N/A');
                    $('#infoTransaccion').show();
                    
                    if (response.es_compra && response.productos && response.productos.length > 0) {
                        // Es una compra con múltiples productos
                        productosCompra = response.productos;
                        cargarProductosCompra();
                        $('#tablaProductosCompra').show();
                        $('#formularioIndividual').hide();
                    } else {
                        // Es un movimiento individual
                        $('#id_movimiento_original').val(response.movimiento.id);
                        $('#id_producto').val(response.movimiento.id_producto);
                        $('#tipo_movimiento').val(response.movimiento.tipo_movimiento);
                        $('#producto_nombre').val(response.movimiento.producto);
                        $('#transaccion_original').val(response.movimiento.numero_transaccion);
                        $('#cantidad').val(response.movimiento.cantidad);
                        $('#precio_unitario').val(response.movimiento.precio_unitario);
                        $('#fecha_movimiento').val(response.movimiento.fecha_movimiento_iso);
                        
                        $('#tablaProductosCompra').hide();
                        $('#formularioIndividual').show();
                    }
                    
                    // Mostrar el formulario de ajuste
                    $('#formularioAjuste').show();
                    
                    Swal.fire({
                        title: '¡Movimiento Encontrado!',
                        text: 'Se encontró el registro a ajustar. Puede proceder con la modificación.',
                        icon: 'success'
                    });
                } else {
                    Swal.fire({
                        title: 'No Encontrado',
                        text: response.message,
                        icon: 'warning'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire({
                    title: 'Error',
                    text: 'Error al buscar el movimiento: ' + error,
                    icon: 'error'
                });
            }
        });
    });
    
    // Función para cargar productos de compra en la tabla
    function cargarProductosCompra() {
        const tbody = $('#productosCompraBody');
        tbody.empty();
        
        productosCompra.forEach(function(producto, index) {
            const row = `
                <tr data-index="${index}">
                    <td>
                        <strong>${producto.producto}</strong>
                        <input type="hidden" name="productos[${index}][id_movimiento]" value="${producto.id}">
                        <input type="hidden" name="productos[${index}][id_producto]" value="${producto.id_producto}">
                    </td>
                    <td>
                        <span class="badge badge-secondary">${producto.proveedor || 'No especificado'}</span>
                    </td>
                    <td>
                        <span class="badge badge-info">${producto.cantidad}</span>
                    </td>
                    <td>
                        <span class="badge badge-success">Bs ${producto.precio_unitario}</span>
                    </td>
                    <td>
                        <input type="number" class="form-control nueva-cantidad" 
                               name="productos[${index}][nueva_cantidad]" 
                               value="${producto.cantidad}" 
                               step="0.01" min="0" required>
                    </td>
                    <td>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Bs</span>
                            </div>
                            <input type="number" class="form-control nuevo-precio" 
                                   name="productos[${index}][nuevo_precio]" 
                                   value="${producto.precio_unitario}" 
                                   step="0.01" min="0" required>
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger eliminar-producto" data-index="${index}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }
    
    // Manejar eliminación de productos de la tabla
    $(document).on('click', '.eliminar-producto', function() {
        const index = $(this).data('index');
        $(this).closest('tr').remove();
        // Remover del array
        productosCompra.splice(index, 1);
        // Recargar la tabla para actualizar los índices
        cargarProductosCompra();
    });
    
    // Manejar guardado de ajustes de compra
    $('#btnGuardarAjustesCompra').click(function() {
        // Validar que haya al menos un producto
        if ($('#productosCompraBody tr').length === 0) {
            Swal.fire({
                title: 'Error',
                text: 'Debe tener al menos un producto para ajustar',
                icon: 'error'
            });
            return;
        }
        
        // Validar que todos los campos estén llenos
        let valido = true;
        $('#productosCompraBody input[required]').each(function() {
            if (!$(this).val() || $(this).val() <= 0) {
                valido = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!valido) {
            Swal.fire({
                title: 'Error',
                text: 'Todos los campos de cantidad y precio deben ser válidos y mayores a 0',
                icon: 'error'
            });
            return;
        }
        
        Swal.fire({
            title: '¿Confirmar Ajustes?',
            text: 'Esta acción creará nuevos registros y marcará los originales como anulados',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f0ad4e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, guardar ajustes',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                guardarAjustesCompra();
            }
        });
    });
    
    // Función para guardar ajustes de compra
    function guardarAjustesCompra() {
        // Mostrar loading
        Swal.fire({
            title: 'Procesando...',
            text: 'Guardando los ajustes de la compra',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Recopilar datos de los productos
        const ajustes = [];
        $('#productosCompraBody tr').each(function() {
            const row = $(this);
            const index = row.data('index');
            const producto = productosCompra[index];
            
            ajustes.push({
                id_movimiento_original: producto.id,
                id_producto: producto.id_producto,
                nueva_cantidad: row.find('.nueva-cantidad').val(),
                nuevo_precio: row.find('.nuevo-precio').val(),
                numero_transaccion_original: producto.numero_transaccion
            });
        });
        
        // Enviar datos por AJAX
        $.ajax({
            url: '<?= BASE_URL ?>index.php?action=movimientos-inventario&method=guardarAjustesCompra',
            method: 'POST',
            data: {
                ajustes: ajustes,
                observaciones: 'Ajuste masivo de compra ' + $('#info_numero_transaccion').text()
            },
            dataType: 'json',
            success: function(response) {
                Swal.close();
                
                if (response.success) {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Los ajustes se guardaron correctamente',
                        icon: 'success'
                    }).then(() => {
                        window.location.href = '<?= BASE_URL ?>index.php?action=movimientos-inventario';
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message || 'Error al guardar los ajustes',
                        icon: 'error'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire({
                    title: 'Error',
                    text: 'Error al guardar los ajustes: ' + error,
                    icon: 'error'
                });
            }
        });
    }
    
    // Manejar envío del formulario individual
    $('#formAjuste').submit(function(e) {
        e.preventDefault();
        
        // Validar que se haya seleccionado un movimiento (si no es edición)
        <?php if (!(isset($es_edicion) && $es_edicion)): ?>
        if (!$('#id_movimiento_original').val()) {
            Swal.fire({
                title: 'Error',
                text: 'Debe buscar y seleccionar un movimiento antes de guardar el ajuste',
                icon: 'error'
            });
            return;
        }
        <?php endif; ?>
        
        Swal.fire({
            title: '¿Confirmar Ajuste?',
            text: 'Esta acción creará un nuevo registro y marcará el original como anulado',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f0ad4e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, guardar ajuste',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar loading
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Guardando el ajuste',
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