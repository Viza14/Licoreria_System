<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-edit"></i> Editar Movimiento de Inventario</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-exchange"></i><a href="<?= BASE_URL ?>index.php?action=movimientos-inventario">Movimientos</a></li>
                    <li><i class="fa fa-edit"></i>Editar</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Editar Movimiento #<?= $movimiento['id'] ?>
                    </header>
                    <div class="panel-body">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <strong><?= $_SESSION['error']['title'] ?></strong> <?= $_SESSION['error']['text'] ?>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>

                        <div class="alert alert-info">
                            <strong>Nota:</strong> Al editar este movimiento, el registro actual se marcará como histórico y se creará un nuevo registro de tipo "AJUSTE" con los cambios realizados.
                        </div>

                        <form class="form-horizontal" method="POST" action="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=actualizar&id=<?= $movimiento['id'] ?>">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Producto</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="<?= $producto['descripcion'] ?>" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Tipo de Movimiento</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="<?= $movimiento['tipo_movimiento'] ?>" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Proveedor *</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="cedula_proveedor" required>
                                        <option value="">Seleccione un proveedor</option>
                                        <?php foreach ($proveedores as $proveedor): ?>
                                            <option value="<?= $proveedor['cedula'] ?>" <?= isset($_POST['cedula_proveedor']) && $_POST['cedula_proveedor'] == $proveedor['cedula'] ? 'selected' : '' ?>>
                                                <?= $proveedor['nombre'] ?> (<?= $proveedor['cedula'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Cantidad *</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control" name="cantidad" min="1" 
                                           value="<?= $movimiento['cantidad'] ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Precio de Compra</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <span class="input-group-addon">Bs</span>
                                        <input type="number" step="0.01" class="form-control" name="precio_unitario"
                                               value="<?= $movimiento['precio_unitario'] ?>" required>
                                    </div>
                                    <small class="text-muted">Precio establecido en la relación producto-proveedor</small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Observaciones</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="observaciones" rows="3"><?= $movimiento['observaciones'] ?? '' ?></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar Cambios</button>
                                    <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario" class="btn btn-default"><i class="fa fa-times"></i> Cancelar</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <small class="text-muted">* Campos obligatorios</small>
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
// Precios pre-cargados desde PHP
const preciosRelaciones = <?= $precios ?>;
const precioActual = <?= $movimiento['precio_unitario'] ?>;

document.addEventListener('DOMContentLoaded', function() {
    const proveedorSelect = document.querySelector('[name="cedula_proveedor"]');
    const precioInput = document.getElementById('precio_compra');
    const hiddenPrecioInput = document.getElementById('hidden_precio_unitario');
    
    // Set current price on load
    precioInput.value = precioActual;
    hiddenPrecioInput.value = precioActual;

    function actualizarPrecio() {
        const proveedorId = proveedorSelect.value;
        const productoId = <?= $producto['id'] ?>;

        if (proveedorId && 
            preciosRelaciones[productoId] && 
            preciosRelaciones[productoId][proveedorId]) {
            const nuevoPrecio = preciosRelaciones[productoId][proveedorId];
            precioInput.value = nuevoPrecio;
            hiddenPrecioInput.value = nuevoPrecio;
        }
    }

    proveedorSelect.addEventListener('change', actualizarPrecio);
});
</script>
