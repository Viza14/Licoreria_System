<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-eye"></i> Detalle de Movimiento de Pérdida</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-exchange"></i><a href="<?= BASE_URL ?>index.php?action=movimientos-inventario">Movimientos</a></li>
                    <li><i class="fa fa-eye"></i> Detalle de Pérdida</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <?php if ($movimiento['id_estatus'] == 2 && !empty($movimiento['id_movimiento_ajustado'])): ?>
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
                        Información del Movimiento de Pérdida
                        <div class="pull-right">
                            <?php if ($movimiento['id_estatus'] == 1): ?>
                                <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=modificarPerdida&id=<?= $movimiento['id'] ?>" 
                                   class="btn btn-warning btn-xs">
                                    <i class="fa fa-edit"></i> Modificar Pérdida
                                </a>
                            <?php endif; ?>
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
                                        <label class="col-sm-4 control-label">Número de Transacción:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-static">
                                                <strong><?= $movimiento['numero_transaccion'] ?></strong>
                                            </p>
                                        </div>
                                    </div>
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
                                                <span class="label label-danger">SALIDA</span>
                                                <span class="label label-warning">PÉRDIDA</span>
                                                <?php if ($movimiento['id_estatus'] == 2): ?>
                                                    <span class="label label-default">ANULADO</span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Cantidad:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-static">
                                                <strong class="text-danger"><?= $movimiento['cantidad'] ?></strong>
                                                <small class="text-muted">(unidades perdidas)</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Fecha:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-static"><?= date('d/m/Y h:i A', strtotime($movimiento['fecha_movimiento'])); ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Precio Unitario:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-static"><?= number_format($movimiento['precio_unitario'], 2, ',', '.'); ?> Bs</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Valor Total Perdido:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-static">
                                                <strong class="text-danger">
                                                    <?= number_format($movimiento['precio_unitario'] * $movimiento['cantidad'], 2, ',', '.'); ?> Bs
                                                </strong>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Usuario:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-static"><?= $movimiento['usuario'] ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                                <h4><i class="fa fa-exclamation-triangle text-warning"></i> Detalles de la Pérdida</h4>
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Motivo/Observaciones:</label>
                                        <div class="col-sm-9">
                                            <div class="well well-sm">
                                                <?= !empty($movimiento['observaciones']) ? nl2br(htmlspecialchars($movimiento['observaciones'])) : '<em class="text-muted">No se especificó motivo</em>' ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php if ($movimiento['tipo_movimiento'] === 'AJUSTE'): ?>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Referencia Original:</label>
                                        <div class="col-sm-9">
                                            <p class="form-control-static">
                                                <?php if (!empty($movimiento['numero_transaccion_original'])): ?>
                                                    <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=mostrar&id=<?= $movimiento['id_movimiento_original'] ?>" 
                                                       class="btn btn-link btn-sm">
                                                        <i class="fa fa-link"></i> <?= $movimiento['numero_transaccion_original'] ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <i class="fa fa-info-circle"></i> 
                                    <strong>Información:</strong> 
                                    Este movimiento representa una pérdida de inventario que reduce el stock disponible del producto.
                                    <?php if ($movimiento['id_estatus'] == 1): ?>
                                        Puede ajustar este registro si es necesario corregir la cantidad o el motivo.
                                    <?php endif; ?>
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