<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-list"></i> Movimientos de Producto</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-exchange"></i><a href="<?= BASE_URL ?>index.php?action=movimientos-inventario">Movimientos</a></li>
                    <li><i class="fa fa-list"></i> <?= $producto['descripcion'] ?></li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Historial de Movimientos: <?= $producto['descripcion'] ?>
                        <div class="pull-right">
                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario" class="btn btn-default btn-xs">
                                <i class="fa fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="small-box bg-aqua">
                                    <div class="inner">
                                        <h3><?= $producto['cantidad'] ?></h3>
                                        <p>Stock Actual</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-cubes"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="table table-striped table-advance table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Usuario</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movimientos as $movimiento): ?>
                                    <tr>
                                        <td><?= $movimiento['fecha_formateada'] ?></td>
                                        <td>
                                            <span class="label label-<?= $movimiento['tipo_movimiento'] == 'ENTRADA' ? 'success' : ($movimiento['tipo_movimiento'] == 'SALIDA' ? 'danger' : 'warning'); ?>">
                                                <?= $movimiento['tipo_movimiento']; ?>
                                            </span>
                                        </td>
                                        <td><?= $movimiento['cantidad']; ?></td>
                                        <td><?= number_format($movimiento['precio_unitario'], 2, ',', '.'); ?> Bs</td>
                                        <td><?= $movimiento['usuario']; ?></td>
                                        <td><?= $movimiento['observaciones'] ?? 'N/A'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->