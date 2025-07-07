<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-eye"></i> Detalle de Movimiento</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-exchange"></i><a href="<?= BASE_URL ?>index.php?action=movimientos-inventario">Movimientos</a></li>
                    <li><i class="fa fa-eye"></i> Detalle</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <?php if ($movimiento['id_estatus'] == 2 && isset($movimiento['id_movimiento_ajustado'])): ?>
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> Este registro ha sido modificado. 
            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=mostrar&id=<?= $movimiento['id_movimiento_ajustado'] ?>" class="alert-link">
                Ver versión ajustada <i class="fa fa-arrow-right"></i>
            </a>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Información del Movimiento
                        <div class="pull-right">
                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario" class="btn btn-default btn-xs">
                                <i class="fa fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Producto:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-static"><?= $movimiento['producto'] ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Tipo:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-static">
                                                <span class="label label-<?= $movimiento['tipo_movimiento'] == 'ENTRADA' ? 'success' : ($movimiento['tipo_movimiento'] == 'SALIDA' ? 'danger' : 'warning'); ?>">
                                                    <?= $movimiento['tipo_movimiento']; ?>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Cantidad:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-static"><?= $movimiento['cantidad'] ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Fecha:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-static"><?= date('d/m/Y H:i', strtotime($movimiento['fecha_movimiento'])); ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Precio Unitario:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-static"><?= number_format($movimiento['precio_unitario'], 2, ',', '.'); ?> Bs</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Referencia:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-static">
                                                <?php if ($movimiento['tipo_referencia'] && $movimiento['id_referencia']): ?>
                                                    <?= $movimiento['tipo_referencia'] ?> #<?= $movimiento['id_referencia'] ?>
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Usuario:</label>
                                    <p class="form-control-static"><?= $movimiento['usuario'] ?></p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Observaciones:</label>
                                    <p class="form-control-static"><?= $movimiento['observaciones'] ?? 'Ninguna' ?></p>
                                </div>
                                
                                <?php if ($movimiento['tipo_referencia'] === 'VENTA' && $movimiento['cliente_venta']): ?>
                                    <hr>
                                    <h4>Detalles de la Venta</h4>
                                    <div class="form-horizontal">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Cliente:</label>
                                            <div class="col-sm-9">
                                                <p class="form-control-static"><?= $movimiento['cliente_venta'] ?></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Monto Total:</label>
                                            <div class="col-sm-9">
                                                <p class="form-control-static"><?= number_format($movimiento['monto_venta'], 2, ',', '.') ?> Bs</p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->