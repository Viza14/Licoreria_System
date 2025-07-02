<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-eye"></i> Detalle de Movimiento de Venta</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-exchange"></i><a href="<?= BASE_URL ?>index.php?action=movimientos-inventario">Movimientos</a></li>
                    <li><i class="fa fa-eye"></i> Detalle de Venta</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Información del Movimiento de Venta
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
                                                <span class="label label-danger">SALIDA</span>
                                                <?php if ($movimiento['id_estatus'] == 2): ?>
                                                    <span class="label label-default">Inactivo</span>
                                                <?php endif; ?>
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
                                        <label class="col-sm-4 control-label">Venta:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-static">#<?= $movimiento['id_referencia'] ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
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
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Usuario:</label>
                                        <div class="col-sm-9">
                                            <p class="form-control-static"><?= $movimiento['usuario'] ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Observaciones:</label>
                                        <div class="col-sm-9">
                                            <p class="form-control-static"><?= $movimiento['observaciones'] ?? 'Ninguna' ?></p>
                                        </div>
                                    </div>
                                </div>

                                <?php if (($movimiento['tipo_movimiento'] === 'AJUSTE' && $movimiento['tipo_movimiento_original']) || $movimiento['id_estatus'] == 2): ?>
                                    <hr>
                                    <h4>Comparación de Cambios</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Campo</th>
                                                    <th>Valor Original</th>
                                                    <th>Valor Ajustado</th>
                                                    <th>¿Cambió?</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Tipo de Movimiento</td>
                                                    <td>
                                                        <span class="label label-danger">SALIDA</span>
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">AJUSTE</span>
                                                    </td>
                                                    <td class="text-center"><i class="fa fa-check text-success"></i></td>
                                                </tr>
                                                <tr>
                                                    <td>Cantidad</td>
                                                    <td><?= $movimiento['cantidad_original'] ?></td>
                                                    <td><?= $movimiento['cantidad'] ?></td>
                                                    <td class="text-center">
                                                        <?= $movimiento['cantidad'] != $movimiento['cantidad_original'] ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-minus text-muted"></i>' ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Precio Unitario</td>
                                                    <td><?= number_format($movimiento['precio_unitario_original'], 2, ',', '.') ?> Bs</td>
                                                    <td><?= number_format($movimiento['precio_unitario'], 2, ',', '.') ?> Bs</td>
                                                    <td class="text-center">
                                                        <?= $movimiento['precio_unitario'] != $movimiento['precio_unitario_original'] ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-minus text-muted"></i>' ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Fecha</td>
                                                    <td><?= date('d/m/Y h:i A', strtotime($movimiento['fecha_movimiento_original'])) ?></td>
                                                    <td><?= date('d/m/Y h:i A', strtotime($movimiento['fecha_movimiento'])) ?></td>
                                                    <td class="text-center">
                                                        <?= $movimiento['fecha_movimiento'] != $movimiento['fecha_movimiento_original'] ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-minus text-muted"></i>' ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Observaciones</td>
                                                    <td><?= $movimiento['observaciones_original'] ?? 'Ninguna' ?></td>
                                                    <td><?= $movimiento['observaciones'] ?? 'Ninguna' ?></td>
                                                    <td class="text-center">
                                                        <?= $movimiento['observaciones'] != $movimiento['observaciones_original'] ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-minus text-muted"></i>' ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Forma de Pago</td>
                                                    <td>
                                                        <?php if (isset($movimiento['pagos_original']) && is_array($movimiento['pagos_original'])): ?>
                                                            <strong>Pago Partido</strong><br>
                                                            <?php foreach ($movimiento['pagos_original'] as $pago): ?>
                                                                <div class="payment-item" style="margin-bottom: 5px; padding: 5px; background-color: #f9f9f9; border-radius: 4px;">
                                                                    <span class="label label-info"><?= $pago['forma_pago'] ?></span>
                                                                    <strong>(<?= number_format($pago['monto'], 2, ',', '.') ?> Bs)</strong>
                                                                    <?php if ($pago['referencia_pago']): ?>
                                                                        <br><small>Ref: <?= $pago['referencia_pago'] ?></small>
                                                                    <?php endif; ?>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <strong>Pago Normal</strong><br>
                                                            <div class="payment-item" style="margin-top: 5px; padding: 5px; background-color: #f9f9f9; border-radius: 4px;">
                                                                <span class="label label-info"><?= $movimiento['forma_pago_original'] ?? 'No disponible' ?></span>
                                                                <?php if (isset($movimiento['referencia_pago_original'])): ?>
                                                                    <br><small>Ref: <?= $movimiento['referencia_pago_original'] ?></small>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if (isset($movimiento['pagos']) && is_array($movimiento['pagos'])): ?>
                                                            <strong>Pago Partido</strong><br>
                                                            <?php foreach ($movimiento['pagos'] as $pago): ?>
                                                                <div class="payment-item" style="margin-bottom: 5px; padding: 5px; background-color: #f9f9f9; border-radius: 4px;">
                                                                    <span class="label label-info"><?= $pago['forma_pago'] ?></span>
                                                                    <strong>(<?= number_format($pago['monto'], 2, ',', '.') ?> Bs)</strong>
                                                                    <?php if ($pago['referencia_pago']): ?>
                                                                        <br><small>Ref: <?= $pago['referencia_pago'] ?></small>
                                                                    <?php endif; ?>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <strong>Pago Normal</strong><br>
                                                            <div class="payment-item" style="margin-top: 5px; padding: 5px; background-color: #f9f9f9; border-radius: 4px;">
                                                                <span class="label label-info"><?= $movimiento['forma_pago'] ?? 'No disponible' ?></span>
                                                                <?php if (isset($movimiento['referencia_pago'])): ?>
                                                                    <br><small>Ref: <?= $movimiento['referencia_pago'] ?></small>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php
                                                        $pagos_cambiaron = false;
                                                        if (isset($movimiento['pagos']) && isset($movimiento['pagos_original'])) {
                                                            if (count($movimiento['pagos']) !== count($movimiento['pagos_original'])) {
                                                                $pagos_cambiaron = true;
                                                            } else {
                                                                foreach ($movimiento['pagos'] as $index => $pago_nuevo) {
                                                                    $pago_original = $movimiento['pagos_original'][$index];
                                                                    if ($pago_nuevo['forma_pago'] !== $pago_original['forma_pago'] ||
                                                                        $pago_nuevo['monto'] !== $pago_original['monto'] ||
                                                                        $pago_nuevo['referencia_pago'] !== ($pago_original['referencia_pago'] ?? null)) {
                                                                        $pagos_cambiaron = true;
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                        } else {
                                                            $pagos_cambiaron = ($movimiento['forma_pago'] ?? null) !== ($movimiento['forma_pago_original'] ?? null) ||
                                                                            ($movimiento['referencia_pago'] ?? null) !== ($movimiento['referencia_pago_original'] ?? null);
                                                        }
                                                        ?>
                                                        <?= $pagos_cambiaron ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-minus text-muted"></i>' ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
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