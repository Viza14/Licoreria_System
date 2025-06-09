<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-user"></i> Detalles de Usuario</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-users"></i><a href="<?php echo BASE_URL; ?>index.php?action=usuarios">Usuarios</a></li>
                    <li><i class="fa fa-user"></i> Detalles</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->
        
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Información del Usuario
                    </header>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Cédula:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= $usuario['cedula']; ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Nombres:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= $usuario['nombres']; ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Apellidos:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= $usuario['apellidos']; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Teléfono:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= $usuario['telefono']; ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Dirección:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= $usuario['direccion']; ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Usuario:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= $usuario['user']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Último acceso:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">
                                            <?= $usuario['ultimo_inicio_sesion'] ? date('d/m/Y H:i', strtotime($usuario['ultimo_inicio_sesion'])) : 'Nunca'; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <a href="<?= BASE_URL ?>index.php?action=usuarios" class="btn btn-default"><i class="fa fa-arrow-left"></i> Volver</a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->