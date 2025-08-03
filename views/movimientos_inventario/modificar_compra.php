<?php
// Validaciones de seguridad
if (!isset($compra) || !$compra) {
    echo '<div class="alert alert-danger">Error: No se encontraron datos de la compra.</div>';
    return;
}

if (!isset($productos_compra) || !$productos_compra) {
    echo '<div class="alert alert-danger">Error: No se encontraron productos de la compra.</div>';
    return;
}

if (!isset($productos) || !$productos) {
    $productos = [];
}

if (!isset($proveedores) || !$proveedores) {
    $proveedores = [];
}

// Configurar variables para modo edición
$es_edicion = true;
$movimiento_editar = $compra;
$productos_editar = $productos_compra;
?>
<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-shopping-cart"></i> Modificar Compra</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-exchange"></i><a href="<?php echo BASE_URL; ?>index.php?action=movimientos-inventario">Movimientos</a></li>
                    <li><i class="fa fa-shopping-cart"></i> Modificar Compra</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <i class="fa fa-shopping-cart"></i> <?= isset($es_edicion) && $es_edicion ? 'Editar Compra' : 'Nueva Compra' ?>
                        <div class="pull-right">
                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario" class="btn btn-default btn-xs">
                                <i class="fa fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </header>
                    <div class="panel-body">
                        <form id="formCompra" method="POST" action="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=<?= isset($es_edicion) && $es_edicion ? 'actualizarCompra&id=' . $movimiento_editar['id'] : 'guardarCompra' ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_proveedor"><i class="fa fa-truck"></i> Proveedor *</label>
                                        <select class="form-control" id="id_proveedor" name="id_proveedor" required onchange="filtrarProductosPorProveedor()">
                                            <option value="">Seleccione un proveedor</option>
                                            <?php foreach ($proveedores as $proveedor): ?>
                                                <option value="<?= $proveedor['cedula'] ?>" 
                                                        data-nombre="<?= htmlspecialchars($proveedor['nombre']) ?>"
                                                        <?= (isset($movimiento_editar) && $movimiento_editar && isset($movimiento_editar['cedula_proveedor']) && $movimiento_editar['cedula_proveedor'] == $proveedor['cedula']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($proveedor['nombre']) ?> - <?= $proveedor['nombre_simbolo'] ?>-<?= $proveedor['cedula'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha_compra"><i class="fa fa-calendar"></i> Fecha de Compra *</label>
                                        <input type="date" class="form-control" id="fecha_compra" name="fecha_compra" 
                                               value="<?php 
                                                   if (isset($movimiento_editar) && $movimiento_editar && !empty($movimiento_editar['fecha'])) {
                                                       $timestamp = strtotime($movimiento_editar['fecha']);
                                                       echo $timestamp !== false ? date('Y-m-d', $timestamp) : date('Y-m-d');
                                                   } else {
                                                       echo date('Y-m-d');
                                                   }
                                               ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="numero_factura"><i class="fa fa-file-text"></i> Número de Factura</label>
                                        <input type="text" class="form-control" id="numero_factura" name="numero_factura" 
                                               value="<?= isset($movimiento_editar) && $movimiento_editar ? htmlspecialchars($movimiento_editar['numero_factura'] ?? '') : '' ?>" 
                                               placeholder="Opcional">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="observaciones"><i class="fa fa-comment"></i> Observaciones</label>
                                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3" 
                                                  placeholder="Observaciones adicionales"><?= isset($movimiento_editar) && $movimiento_editar ? htmlspecialchars($movimiento_editar['observaciones'] ?? '') : '' ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-lg-12">
                                    <h4><i class="fa fa-list"></i> Productos de la Compra</h4>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered" id="tablaProductos">
                                            <thead>
                                                <tr>
                                                    <th><i class="fa fa-box"></i> Producto</th>
                                                    <th><i class="fa fa-sort-numeric-up"></i> Cantidad</th>
                                                    <th><i class="fa fa-money"></i> Precio Compra</th>
                                                    <th><i class="fa fa-money"></i> Precio Venta</th>
                                                    <th><i class="fa fa-calculator"></i> Subtotal</th>
                                                    <th><i class="fa fa-cogs"></i> Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="productosBody">
                                                <!-- Las filas se agregarán dinámicamente -->
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="4" class="text-right"><strong>Total:</strong></td>
                                                    <td><strong id="totalCompra">0.00 Bs</strong></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-success" id="btnAgregarFila" onclick="agregarFila()" disabled>
                                            <i class="fa fa-plus"></i> Agregar Producto
                                        </button>
                                        <button type="button" class="btn btn-warning" id="btnLimpiar" onclick="limpiarFormulario()" style="margin-left: 10px;" disabled>
                                            <i class="fa fa-refresh"></i> Limpiar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save"></i> <?= isset($es_edicion) && $es_edicion ? 'Actualizar Compra' : 'Registrar Compra' ?>
                                        </button>
                                        <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario" class="btn btn-default">
                                            <i class="fa fa-times"></i> Cancelar
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
// Variables globales
let contadorFilas = 0;
let productosDisponibles = [];
const BASE_URL = '<?= BASE_URL ?>';

$(document).ready(function() {
    // Inicializar Select2 para el proveedor
    $('#id_proveedor').select2({
        placeholder: "Seleccione un proveedor",
        allowClear: true
    });
    
    // Si estamos en modo edición, cargar los productos automáticamente
    <?php if (isset($es_edicion) && $es_edicion && isset($productos_editar)): ?>
    const proveedorSeleccionado = $('#id_proveedor').val();
    if (proveedorSeleccionado) {
        // Cargar productos del proveedor y luego cargar los productos de la compra
        realizarCambioProveedor(proveedorSeleccionado, document.getElementById('btnAgregarFila'), function() {
            // Callback para cargar los productos de la compra original
            cargarProductosEdicion();
        });
    }
    <?php endif; ?>
});

<?php if (isset($es_edicion) && $es_edicion && isset($productos_editar)): ?>
function cargarProductosEdicion() {
    const productosEditar = <?= json_encode($productos_editar) ?>;
    
    productosEditar.forEach(function(producto, index) {
        // Agregar una nueva fila
        contadorFilas++;
        const tbody = document.getElementById('productosBody');
        
        let optionsHtml = '<option value="">Seleccione un producto</option>';
        productosDisponibles.forEach(prod => {
            const selected = prod.id == producto.id_producto ? 'selected' : '';
            optionsHtml += `<option value="${prod.id}" data-precio-compra="${prod.precio_compra}" data-precio-venta="${prod.precio_venta}" ${selected}>
                            ${prod.descripcion}
                            </option>`;
        });
        
        const nuevaFila = `
            <tr id="fila_${contadorFilas}">
                <td>
                    <select class="form-control producto-select" name="productos[${contadorFilas}][id_producto]" 
                            onchange="cargarDatosProducto(this, ${contadorFilas})" required>
                        ${optionsHtml}
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control" name="productos[${contadorFilas}][cantidad]" 
                           min="1" step="1" value="${producto.cantidad}" onchange="calcularSubtotal(${contadorFilas})" required>
                </td>
                <td>
                    <input type="number" class="form-control" name="productos[${contadorFilas}][precio_compra]" 
                           min="0" step="0.01" value="${producto.precio_unitario}" onchange="calcularSubtotal(${contadorFilas})" required>
                </td>
                <td>
                    <input type="number" class="form-control" name="productos[${contadorFilas}][precio_venta]" 
                           min="0" step="0.01" value="${producto.precio_actual || ''}" readonly style="background-color: #f5f5f5;">
                </td>
                <td>
                    <span class="subtotal" id="subtotal_${contadorFilas}">0.00 Bs</span>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-xs" onclick="eliminarFila(${contadorFilas})">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        tbody.insertAdjacentHTML('beforeend', nuevaFila);
        
        // Inicializar Select2 para el nuevo select de producto
        $(`#fila_${contadorFilas} .producto-select`).select2({
            placeholder: "Seleccione un producto",
            allowClear: true
        });
        
        // Calcular subtotal
        calcularSubtotal(contadorFilas);
    });
}
<?php endif; ?>

function filtrarProductosPorProveedor() {
    const proveedorSelect = document.getElementById('id_proveedor');
    const cedulaProveedor = proveedorSelect.value;
    const btnAgregar = document.getElementById('btnAgregarFila');
    
    if (!cedulaProveedor) {
        productosDisponibles = [];
        btnAgregar.disabled = true;
        limpiarTablaProductos();
        return;
    }
    
    // Verificar si hay productos en la tabla antes de cambiar proveedor
    const filas = document.querySelectorAll('#productosBody tr');
    if (filas.length > 0) {
        Swal.fire({
            title: '¿Cambiar proveedor?',
            text: 'Al cambiar el proveedor se eliminarán todos los productos agregados. ¿Desea continuar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Usuario confirmó, limpiar tabla y proceder con el cambio
                limpiarTablaProductos();
                realizarCambioProveedor(cedulaProveedor, btnAgregar);
            } else {
                // Usuario canceló, revertir la selección
                $('#id_proveedor').val('').trigger('change');
                return;
            }
        });
    } else {
        // No hay productos, proceder normalmente
        realizarCambioProveedor(cedulaProveedor, btnAgregar);
    }
}

function realizarCambioProveedor(cedulaProveedor, btnAgregar, callback) {
    // Realizar petición AJAX para obtener productos del proveedor
     $.ajax({
         url: 'index.php?action=movimientos-inventario&method=obtenerProductosPorProveedor',
         type: 'POST',
         data: { cedula_proveedor: cedulaProveedor },
         dataType: 'json',
        success: function(response) {
            productosDisponibles = response;
            btnAgregar.disabled = false;
            
            // Hacer readonly el selector de proveedor después de seleccionarlo para evitar cambios
            const proveedorSelect = document.getElementById('id_proveedor');
            const btnLimpiar = document.getElementById('btnLimpiar');
            // Usar readonly en lugar de disabled para que el valor se envíe en el formulario
            proveedorSelect.style.pointerEvents = 'none';
            proveedorSelect.style.backgroundColor = '#f5f5f5';
            $('#id_proveedor').addClass('readonly-select');
            btnLimpiar.disabled = false;
            
            if (productosDisponibles.length === 0) {
                Swal.fire({
                    title: 'Sin productos',
                    text: 'Este proveedor no tiene productos asociados.',
                    icon: 'warning'
                });
                btnAgregar.disabled = true;
            }
            
            // Ejecutar callback si se proporciona
            if (typeof callback === 'function') {
                callback();
            }
        },
        error: function() {
            Swal.fire({
                title: 'Error',
                text: 'Error al cargar los productos del proveedor.',
                icon: 'error'
            });
            productosDisponibles = [];
            btnAgregar.disabled = true;
        }
    });
}

function limpiarTablaProductos() {
    document.getElementById('productosBody').innerHTML = '';
    calcularTotal();
}

function limpiarFormulario() {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Se limpiarán todos los datos del formulario.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, limpiar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Limpiar tabla de productos
            limpiarTablaProductos();
            
            // Reiniciar selector de proveedor
            const proveedorSelect = document.getElementById('id_proveedor');
            const btnAgregar = document.getElementById('btnAgregarFila');
            const btnLimpiar = document.getElementById('btnLimpiar');
            
            $('#id_proveedor').val('').trigger('change');
            // Restaurar el estado normal del selector de proveedor
            proveedorSelect.style.pointerEvents = 'auto';
            proveedorSelect.style.backgroundColor = '';
            $('#id_proveedor').removeClass('readonly-select');
            btnAgregar.disabled = true;
            btnLimpiar.disabled = true;
            
            // Limpiar campos del formulario
            const numeroFacturaInput = document.getElementById('numero_factura');
            const fechaCompraInput = document.getElementById('fecha_compra');
            const observacionesInput = document.getElementById('observaciones');
            
            if (numeroFacturaInput) numeroFacturaInput.value = '';
            if (fechaCompraInput) fechaCompraInput.value = '';
            if (observacionesInput) observacionesInput.value = '';
            
            // Reiniciar variables globales
            productosDisponibles = [];
            contadorFilas = 0;
            
            Swal.fire({
                title: 'Formulario limpiado',
                text: 'El formulario ha sido reiniciado correctamente.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        }
    });
}

function agregarFila() {
    if (productosDisponibles.length === 0) {
        Swal.fire({
            title: 'Sin productos',
            text: 'Seleccione un proveedor que tenga productos asociados.',
            icon: 'warning'
        });
        return;
    }
    
    contadorFilas++;
    const tbody = document.getElementById('productosBody');
    
    let optionsHtml = '<option value="">Seleccione un producto</option>';
    productosDisponibles.forEach(producto => {
        optionsHtml += `<option value="${producto.id}" data-precio-compra="${producto.precio_compra}" data-precio-venta="${producto.precio_venta}">
                        ${producto.descripcion}
                        </option>`;
    });
    
    const nuevaFila = `
        <tr id="fila_${contadorFilas}">
            <td>
                <select class="form-control producto-select" name="productos[${contadorFilas}][id_producto]" 
                        onchange="cargarDatosProducto(this, ${contadorFilas})" required>
                    ${optionsHtml}
                </select>
            </td>
            <td>
                <input type="number" class="form-control" name="productos[${contadorFilas}][cantidad]" 
                       min="1" step="1" onchange="calcularSubtotal(${contadorFilas})" required>
            </td>
            <td>
                <input type="number" class="form-control" name="productos[${contadorFilas}][precio_compra]" 
                       min="0" step="0.01" onchange="calcularSubtotal(${contadorFilas})" required>
            </td>
            <td>
                <input type="number" class="form-control" name="productos[${contadorFilas}][precio_venta]" 
                       min="0" step="0.01" readonly style="background-color: #f5f5f5;">
            </td>
            <td>
                <span class="subtotal" id="subtotal_${contadorFilas}">0.00 Bs</span>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-xs" onclick="eliminarFila(${contadorFilas})">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    tbody.insertAdjacentHTML('beforeend', nuevaFila);
    
    // Inicializar Select2 para el nuevo select de producto
    $(`#fila_${contadorFilas} .producto-select`).select2({
        placeholder: "Seleccione un producto",
        allowClear: true
    });
}

function cargarDatosProducto(select, fila) {
    const productoId = select.value;
    const precioCompraInput = document.querySelector(`input[name="productos[${fila}][precio_compra]"]`);
    const precioVentaInput = document.querySelector(`input[name="productos[${fila}][precio_venta]"]`);
    
    if (!precioCompraInput || !precioVentaInput) {
        console.error('Elementos de precio no encontrados para la fila', fila);
        return;
    }
    
    if (productoId) {
        const producto = productosDisponibles.find(p => p.id == productoId);
        if (producto) {
            // Cargar precio de compra
            if (producto.precio_compra) {
                precioCompraInput.value = parseFloat(producto.precio_compra).toFixed(2);
            }
            // Cargar precio de venta (readonly)
            if (producto.precio_venta) {
                precioVentaInput.value = parseFloat(producto.precio_venta).toFixed(2);
            }
            calcularSubtotal(fila);
        }
    } else {
        precioCompraInput.value = '';
        precioVentaInput.value = '';
        calcularSubtotal(fila);
    }
}

function eliminarFila(fila) {
    document.getElementById(`fila_${fila}`).remove();
    calcularTotal();
}

function calcularSubtotal(fila) {
    const cantidad = parseFloat(document.querySelector(`input[name="productos[${fila}][cantidad]"]`).value) || 0;
    const precioCompra = parseFloat(document.querySelector(`input[name="productos[${fila}][precio_compra]"]`).value) || 0;
    
    const subtotal = cantidad * precioCompra;
    document.getElementById(`subtotal_${fila}`).textContent = subtotal.toFixed(2) + ' Bs';
    
    calcularTotal();
}

function calcularTotal() {
    let total = 0;
    document.querySelectorAll('.subtotal').forEach(subtotalElement => {
        const valor = parseFloat(subtotalElement.textContent.replace(' Bs', '')) || 0;
        total += valor;
    });
    
    document.getElementById('totalCompra').textContent = total.toFixed(2) + ' Bs';
}

// Validación del formulario
document.getElementById('formCompra').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const filas = document.querySelectorAll('#productosBody tr');
    if (filas.length === 0) {
        Swal.fire({
            title: 'Error',
            text: 'Debe agregar al menos un producto a la compra.',
            icon: 'error'
        });
        return;
    }
    
    // Validar que todos los productos tengan datos completos
    let datosCompletos = true;
    filas.forEach(fila => {
        const inputs = fila.querySelectorAll('input[required], select[required]');
        inputs.forEach(input => {
            if (!input.value) {
                datosCompletos = false;
            }
        });
    });
    
    if (!datosCompletos) {
        Swal.fire({
            title: 'Error',
            text: 'Complete todos los campos requeridos de los productos.',
            icon: 'error'
        });
        return;
    }
    
    Swal.fire({
        title: '¿Confirmar modificación?',
        text: 'Se actualizará la compra con los productos especificados.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, actualizar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
    });
});
</script>

<style>
.readonly-select {
    background-color: #f5f5f5 !important;
    cursor: not-allowed !important;
}

.readonly-select .select2-selection {
    background-color: #f5f5f5 !important;
    cursor: not-allowed !important;
}
</style>