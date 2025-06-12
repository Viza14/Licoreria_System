<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-plus-circle"></i> Entrada de Inventario</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-beer"></i><a href="<?php echo BASE_URL; ?>index.php?action=productos">Productos</a></li>
                    <li><i class="fa fa-eye"></i><a href="<?php echo BASE_URL; ?>index.php?action=productos&method=mostrar&id=<?= $producto['id'] ?>">Detalles</a></li>
                    <li><i class="fa fa-plus-circle"></i> Entrada</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->
        
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Registrar Entrada de Inventario
                    </header>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Producto:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= htmlspecialchars($producto['descripcion']) ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Stock Actual:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= $producto['cantidad'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <form class="form-horizontal" action="<?= BASE_URL ?>index.php?action=productos&method=registrarEntrada&id=<?= $producto['id'] ?>" method="POST">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Cantidad</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control" name="cantidad" min="1" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Precio de Compra</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>
                                        <input type="number" class="form-control" name="precio_compra" min="0.01" step="0.01" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Proveedor</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="cedula_proveedor" required>
                                        <option value="">Seleccione un proveedor</option>
                                        <!-- Aquí deberías cargar los proveedores disponibles -->
                                        <option value="V-12345678">Proveedor 1</option>
                                        <option value="V-87654321">Proveedor 2</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Observaciones</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="observaciones" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Registrar Entrada</button>
                                    <a href="<?= BASE_URL ?>index.php?action=productos&method=mostrar&id=<?= $producto['id'] ?>" class="btn btn-default"><i class="fa fa-times"></i> Cancelar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>

<!-- Incluir SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Mostrar mensajes de SweetAlert
<?php if (isset($_SESSION['mensaje'])): ?>
    Swal.fire({
        title: '<?= $_SESSION['mensaje']['title'] ?>',
        text: '<?= $_SESSION['mensaje']['text'] ?>',
        icon: '<?= $_SESSION['mensaje']['icon'] ?>',
        timer: 3000,
        timerProgressBar: true
    });
    <?php unset($_SESSION['mensaje']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    Swal.fire({
        title: '<?= $_SESSION['error']['title'] ?>',
        text: '<?= $_SESSION['error']['text'] ?>',
        icon: '<?= $_SESSION['error']['icon'] ?>'
    });
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>
</script>
<!--main content end-->