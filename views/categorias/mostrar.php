<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-eye"></i> Detalles de Categoría</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-list"></i><a href="<?php echo BASE_URL; ?>index.php?action=categorias">Categorías</a></li>
                    <li><i class="fa fa-eye"></i> Detalles</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->
        
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Información de la Categoría
                    </header>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">ID:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= htmlspecialchars($categoria['id'] ?? 'N/A'); ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Nombre:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= htmlspecialchars($categoria['nombre'] ?? 'N/A'); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Tipo de Categoría:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">
                                            <?php 
                                            if (isset($categoria['id_tipo_categoria'])) {
                                                // Mostrar ID si no hay nombre disponible
                                                echo htmlspecialchars($categoria['tipo_categoria'] ?? 'Tipo ID: ' . $categoria['id_tipo_categoria']);
                                            } else {
                                                echo 'N/A';
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Estatus:</label>
                                    <div class="col-sm-8">
                                        <span class="label label-<?= ($categoria['id_estatus'] ?? 0) == 1 ? 'success' : 'danger'; ?>">
                                            <?= ($categoria['id_estatus'] ?? 0) == 1 ? 'Activo' : 'Inactivo'; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <a href="<?= BASE_URL ?>index.php?action=categorias" class="btn btn-default"><i class="fa fa-arrow-left"></i> Volver</a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->