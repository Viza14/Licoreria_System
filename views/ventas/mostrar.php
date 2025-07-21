<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-shopping-cart"></i> Detalle de Venta</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-shopping-cart"></i><a href="<?= BASE_URL ?>index.php?action=ventas">Ventas</a></li>
                    <li><i class="fa fa-eye"></i> Detalle</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Información de la Venta #<?= $venta['id'] ?>
                    </header>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Información General</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Fecha y Hora:</th>
                                        <td><?= date('d/m/Y h:i A', strtotime($venta['fecha'])) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Cliente:</th>
                                        <td><?= htmlspecialchars($venta['cliente']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Vendedor:</th>
                                        <td><?= htmlspecialchars($venta['usuario']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Formas de Pago:</th>
                                        <td>
                                            <?php if (!empty($pagos)): ?>
                                                <ul style="list-style: none; padding-left: 0;">
                                                <?php foreach ($pagos as $pago): ?>
                                                    <li>
                                                        <?= match($pago['forma_pago']) {
                                                            'EFECTIVO' => 'Efectivo',
                                                            'TARJETA' => 'Tarjeta',
                                                            'PAGO_MOVIL' => 'Pago Móvil',
                                                            default => $pago['forma_pago']
                                                        } ?>
                                                        (<?= number_format($pago['monto'], 2, ',', '.') ?> Bs)
                                                        <?php if($pago['referencia_pago']): ?>
                                                            <br>Ref: <?= htmlspecialchars($pago['referencia_pago']) ?>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <span class="text-muted">No hay información de pagos disponible</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4>Resumen de Pago</h4>
                                <table class="table table-bordered">
                                    <?php
                                    $subtotal = 0;
                                    foreach ($detalles as $detalle) {
                                        $subtotal += $detalle['monto'];
                                    }
                                    ?>
                                    <tr>
                                        <th>Subtotal:</th>
                                        <td><?= number_format($subtotal, 2, ',', '.') ?> Bs</td>
                                    </tr>
                                    <tr>
                                        <th>Total:</th>
                                        <td><strong><?= number_format($subtotal, 2, ',', '.') ?> Bs</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h4>Productos Vendidos</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Cantidad</th>
                                                <th>Precio Unitario</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($detalles as $detalle): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($detalle['producto']) ?></td>
                                                    <td><?= $detalle['cantidad'] ?></td>
                                                    <td><?= number_format($detalle['monto'] / $detalle['cantidad'], 2, ',', '.') ?> Bs</td>
                                                    <td><?= number_format($detalle['monto'], 2, ',', '.') ?> Bs</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <?php if ($_SESSION['user_rol'] != 2): ?>
                                <div class="col-md-12">
                                    <a href="<?= BASE_URL ?>index.php?action=reportes" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Volver
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->