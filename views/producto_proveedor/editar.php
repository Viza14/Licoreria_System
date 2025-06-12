<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-edit"></i> Editar Relación Producto-Proveedor</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-link"></i><a href="<?php echo BASE_URL; ?>index.php?action=producto-proveedor">Producto-Proveedor</a></li>
                    <li><i class="fa fa-edit"></i> Editar</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Editar Relación Producto-Proveedor
                    </header>
                    <div class="panel-body">
                        <form class="form-horizontal" action="<?= BASE_URL ?>index.php?action=producto-proveedor&method=actualizar&id=<?= $relacion['id']; ?>" method="POST" id="formRelacion">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Producto</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static"><?= $relacion['producto']; ?></p>
                                    <input type="hidden" name="id_producto" value="<?= $relacion['id_producto']; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Proveedor</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static"><?= $relacion['simbolo_proveedor'] . '-' . $relacion['cedula_proveedor'] . ' - ' . $relacion['proveedor']; ?></p>
                                    <input type="hidden" name="cedula_proveedor" value="<?= $relacion['cedula_proveedor']; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Precio Compra</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control" name="precio_compra" step="0.01" min="0" value="<?= $relacion['precio_compra']; ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Actualizar</button>
                                    <a href="<?= BASE_URL ?>index.php?action=producto-proveedor" class="btn btn-default"><i class="fa fa-times"></i> Cancelar</a>
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