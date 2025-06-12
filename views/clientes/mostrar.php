<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-user"></i> Detalles de Cliente</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-users"></i><a href="<?php echo BASE_URL; ?>index.php?action=clientes">Clientes</a></li>
                    <li><i class="fa fa-user"></i> Detalles</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->
        
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Información del Cliente
                        <div class="pull-right">
                            <span class="label label-<?= $cliente['id_estatus'] == 1 ? 'success' : 'danger' ?>">
                                <?= $cliente['estatus'] ?>
                            </span>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Cédula:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= $cliente['nombre_simbolo'] . '-' . $cliente['cedula'] ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Nombres:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= $cliente['nombres'] ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Apellidos:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= $cliente['apellidos'] ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Teléfono:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= $cliente['telefono'] ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Dirección:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= $cliente['direccion'] ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Total Compras:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= number_format($totalVentas, 2) ?> Bs</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <a href="<?= BASE_URL ?>index.php?action=clientes" class="btn btn-default"><i class="fa fa-arrow-left"></i> Volver</a>
                                <a href="<?= BASE_URL ?>index.php?action=clientes&method=editar&cedula=<?= $cliente['cedula'] ?>" class="btn btn-primary">
                                    <i class="fa fa-edit"></i> Editar
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