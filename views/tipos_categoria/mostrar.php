<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-list-alt"></i> Detalles de Tipo de Categoría</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-list-alt"></i><a href="<?php echo BASE_URL; ?>index.php?action=tipos-categoria">Tipos de Categoría</a></li>
                    <li><i class="fa fa-list-alt"></i> Detalles</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->
        
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Información del Tipo de Categoría
                    </header>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">ID:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= htmlspecialchars($tipo['id'] ?? 'N/A'); ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Nombre:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= htmlspecialchars($tipo['nombre'] ?? 'N/A'); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Estatus:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">
                                            <span class="label label-<?= ($tipo['id_estatus'] ?? 0) == 1 ? 'success' : 'danger'; ?>">
                                                <?= ($tipo['id_estatus'] ?? 0) == 1 ? 'Activo' : 'Inactivo'; ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (isset($tipo['descripcion'])): ?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Descripción:</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><?= htmlspecialchars($tipo['descripcion']); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <a href="<?= BASE_URL ?>index.php?action=tipos-categoria" class="btn btn-default"><i class="fa fa-arrow-left"></i> Volver</a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->