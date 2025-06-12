<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-info-circle"></i> Detalles de Relación</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-link"></i><a href="<?php echo BASE_URL; ?>index.php?action=producto-proveedor">Producto-Proveedor</a></li>
                    <li><i class="fa fa-info-circle"></i> Detalles</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->
        
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Información de la Relación
                    </header>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Producto:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= $relacion['producto']; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Proveedor:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= $relacion['simbolo_proveedor'] . '-' . $relacion['cedula_proveedor'] . ' - ' . $relacion['proveedor']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Precio Compra:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= number_format($relacion['precio_compra'], 2); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Última Actualización:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= date('d/m/Y H:i', strtotime($relacion['fecha_actualizacion'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <a href="<?= BASE_URL ?>index.php?action=producto-proveedor" class="btn btn-default"><i class="fa fa-arrow-left"></i> Volver</a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->