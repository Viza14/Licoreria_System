<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-cube"></i> Detalles de Producto</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-cubes"></i><a href="<?php echo BASE_URL; ?>index.php?action=productos">Productos</a></li>
                    <li><i class="fa fa-cube"></i> Detalles</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Información del Producto
                        <div class="pull-right">
                            <span class="label label-<?= $producto['id_estatus'] == 1 ? 'success' : 'danger' ?>">
                                <?= $producto['estatus'] ?>
                            </span>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Descripción:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= $producto['descripcion'] ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Categoría:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= $producto['categoria'] ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Tipo de Categoría:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= $producto['tipo_categoria'] ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Cantidad:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= $producto['cantidad'] ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Precio:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= number_format($producto['precio'], 2) ?> Bs</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Estado:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= $producto['estatus'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <a href="<?= BASE_URL ?>index.php?action=productos" class="btn btn-default"><i class="fa fa-arrow-left"></i> Volver</a>
                                <a href="<?= BASE_URL ?>index.php?action=productos&method=editar&id=<?= $producto['id'] ?>" class="btn btn-primary">
                                    <i class="fa fa-edit"></i> Editar
                                </a>
                                <a href="<?= BASE_URL ?>index.php?action=productos&method=cambiarEstado&id=<?= $producto['id'] ?>" class="btn btn-<?= $producto['id_estatus'] == 1 ? 'danger' : 'success' ?>">
                                    <i class="fa fa-power-off"></i> <?= $producto['id_estatus'] == 1 ? 'Desactivar' : 'Activar' ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->