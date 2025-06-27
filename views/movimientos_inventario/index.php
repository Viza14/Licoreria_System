<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-exchange"></i> Movimientos de Inventario</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-exchange"></i> Movimientos</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <i class="fa fa-history"></i> Historial de Movimientos
                        <div class="pull-right">
                            <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#filtrosModal">
                                <i class="fa fa-filter"></i> Filtros
                            </button>
                            <?php if ($_SESSION['user_rol'] != 2): ?> <!-- Asumiendo que 2 es el rol de empleado -->
                                <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=resumen" class="btn btn-info btn-xs">
                                    <i class="fa fa-pie-chart"></i> Resumen
                                </a>
                            <?php endif; ?>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <input type="text" id="busqueda" class="form-control" placeholder="Buscar por producto, usuario...">
                            </div>
                        </div>

                        <div id="filtros-activos" class="alert alert-info" style="display: none; margin-bottom: 10px;">
                            <i class="fa fa-filter"></i> Filtros activos: <span id="texto-filtros-activos"></span>
                            <div class="pull-right">
                                <strong><i class="fa fa-list"></i> Resultados encontrados: <span id="contador-resultados">0</span></strong>
                            </div>
                        </div>

                        <div id="sin-resultados" class="alert alert-warning text-center" style="display: none;">
                            <i class="fa fa-exclamation-circle"></i> No se encontraron movimientos que coincidan con los criterios de búsqueda
                        </div>

                        <table id="tablaMovimientos" class="table table-striped table-advance table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fa fa-calendar"></i> Fecha</th>
                                    <th><i class="fa fa-box"></i> Producto</th>
                                    <th><i class="fa fa-exchange"></i> Tipo</th>
                                    <th><i class="fa fa-sort-numeric-up"></i> Cantidad</th>
                                    <th><i class="fa fa-money-bill"></i> Precio</th>
                                    <th><i class="fa fa-user"></i> Usuario</th>
                                    <th><i class="fa fa-hashtag"></i> Referencia</th>
                                    <th><i class="fa fa-cogs"></i> Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movimientos as $movimiento): ?>
                                    <?php
                                    // Determine if has adjustment
                                    $tiene_ajuste = isset($movimiento['tiene_ajuste']) ? $movimiento['tiene_ajuste'] : 0;
                                    $es_ajuste = $movimiento['tipo_movimiento'] == 'AJUSTE';
                                    $es_inactivo = $movimiento['id_estatus'] == 2;
                                    ?>

                                    <tr class="<?php
                                                if ($es_inactivo || ($tiene_ajuste > 0 && ($movimiento['tipo_movimiento'] == 'SALIDA' || $movimiento['tipo_movimiento'] == 'ENTRADA'))) {
                                                    echo 'inactive-movement';
                                                } elseif ($es_ajuste) {
                                                    echo 'adjustment-movement';
                                                } else {
                                                    echo '';
                                                }
                                                ?>">
                                        <td><i class="fa fa-clock"></i> <?= date('d/m/Y H:i', strtotime($movimiento['fecha_movimiento'])); ?></td>
                                        <td><?= htmlspecialchars($movimiento['producto']); ?></td>
                                        <td>
                                            <span class="label label-<?= $movimiento['tipo_movimiento'] == 'ENTRADA' ? 'success' : ($movimiento['tipo_movimiento'] == 'SALIDA' ? 'danger' : 'warning'); ?>">
                                                <i class="fa fa-<?= $movimiento['tipo_movimiento'] == 'ENTRADA' ? 'arrow-down' : ($movimiento['tipo_movimiento'] == 'SALIDA' ? 'arrow-up' : 'sync'); ?>"></i>
                                                <?= $movimiento['tipo_movimiento']; ?>
                                            </span>
                                            <?php if ($es_inactivo): ?>
                                                <span class="label label-default"><i class="fa fa-ban"></i> Inactivo</span>
                                            <?php elseif ($tiene_ajuste > 0): ?>
                                                <span class="label label-info"><i class="fa fa-sync"></i> Ajustado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><i class="fa fa-hashtag"></i> <?= $movimiento['cantidad']; ?></td>
                                        <td class="<?= $movimiento['tipo_movimiento'] == 'ENTRADA' ? 'text-success' : ($movimiento['tipo_movimiento'] == 'SALIDA' ? 'text-danger' : ''); ?>">
                                            <i class="fa fa-money-bill"></i> <?= number_format($movimiento['precio_unitario'], 2, ',', '.'); ?> Bs
                                        </td>
                                        <td><i class="fa fa-user"></i> <?= $movimiento['usuario']; ?></td>
                                        <td><i class="fa fa-file-alt"></i> <?= $movimiento['referencia'] ?? 'N/A'; ?></td>
                                        <td>
                                            <?php if (!$es_inactivo && $tiene_ajuste == 0): ?>
                                                <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=mostrar&id=<?= $movimiento['id']; ?>"
                                                    class="btn btn-success btn-xs" title="Ver Detalles">
                                                    <i class="fa fa-eye"></i>
                                                </a>

                                                <?php if (($movimiento['tipo_movimiento'] == 'SALIDA' && $movimiento['tipo_referencia'] == 'VENTA') ||
                                                    ($movimiento['tipo_movimiento'] == 'AJUSTE' && $movimiento['tipo_referencia'] == 'VENTA')
                                                ): ?>
                                                    <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=modificarVenta&id=<?= $movimiento['id_referencia'] ?>"
                                                        class="btn btn-primary btn-xs" title="Modificar Venta">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                <?php elseif (
                                                    $movimiento['tipo_movimiento'] == 'ENTRADA' ||
                                                    ($movimiento['tipo_movimiento'] == 'AJUSTE' && $movimiento['tipo_referencia'] != 'VENTA')
                                                ): ?>
                                                    <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=editar&id=<?= $movimiento['id'] ?>"
                                                        class="btn btn-primary btn-xs" title="Editar Entrada">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php elseif ($tiene_ajuste > 0): ?>
                                                <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=mostrar&id=<?= $movimiento['id'] ?>"
                                                    class="btn btn-info btn-xs" title="Ver Detalle">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
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

[Rest of the code remains unchanged...]
<!--main content end-->