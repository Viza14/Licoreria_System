<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-truck"></i> Detalles del Proveedor</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-truck"></i><a href="<?php echo BASE_URL; ?>index.php?action=proveedores">Proveedores</a></li>
                    <li><i class="fa fa-eye"></i>Detalles</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Información del Proveedor
                    </header>
                    <div class="panel-body">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Cédula/RIF:</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static">
                                        <?php
                                        // Mostrar el símbolo basado en id_simbolo_cedula
                                        $simbolo = '';
                                        switch ($proveedor['id_simbolo_cedula']) {
                                            case 1:
                                                $simbolo = 'V';
                                                break;
                                            case 2:
                                                $simbolo = 'E';
                                                break;
                                            case 3:
                                                $simbolo = 'J';
                                                break;
                                            default:
                                                $simbolo = '';
                                                break;
                                        }
                                        echo $simbolo . '-' . $proveedor['cedula'];
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Nombre:</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static"><?= $proveedor['nombre'] ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Teléfono:</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static"><?= $proveedor['telefono'] ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Dirección:</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static"><?= $proveedor['direccion'] ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Estatus:</label>
                                <div class="col-sm-10">
                                    <span class="label label-<?= $proveedor['estatus'] == 'Activo' ? 'success' : 'danger'; ?>">
                                        <?= $proveedor['estatus']; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <a href="<?= BASE_URL ?>index.php?action=proveedores&method=editar&id=<?= $proveedor['cedula']; ?>"
                                        class="btn btn-primary" title="Editar">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>index.php?action=proveedores"
                                        class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Volver
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->