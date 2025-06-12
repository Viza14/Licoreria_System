<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-pie-chart"></i> Resumen de Movimientos</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-exchange"></i><a href="<?= BASE_URL ?>index.php?action=movimientos-inventario">Movimientos</a></li>
                    <li><i class="fa fa-pie-chart"></i> Resumen</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Resumen de Entradas y Salidas
                    </header>
                    <div class="panel-body">
                        <form class="form-inline" method="POST" action="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=resumen">
                            <div class="form-group">
                                <label for="fecha_inicio">Desde:</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?= $filtros['fecha_inicio'] ?? '' ?>">
                            </div>
                            <div class="form-group">
                                <label for="fecha_fin">Hasta:</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?= $filtros['fecha_fin'] ?? '' ?>">
                            </div>
                            <div class="form-group">
                                <label for="id_producto">Producto:</label>
                                <select class="form-control" id="id_producto" name="id_producto">
                                    <option value="">Todos</option>
                                    <?php foreach ($productos as $producto): ?>
                                        <option value="<?= $producto['id'] ?>" <?= isset($filtros['id_producto']) && $filtros['id_producto'] == $producto['id'] ? 'selected' : '' ?>>
                                            <?= $producto['descripcion'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=resumen" class="btn btn-default">Limpiar</a>
                        </form>

                        <hr>

                        <table class="table table-striped table-advance table-hover">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Entradas</th>
                                    <th>Salidas</th>
                                    <th>Stock Actual</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resumen as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['producto']); ?></td>
                                        <td><?= $item['categoria']; ?></td>
                                        <td class="text-success">+<?= $item['entradas']; ?></td>
                                        <td class="text-danger">-<?= $item['salidas']; ?></td>
                                        <td><?= $item['stock_actual']; ?></td>
                                        <td>
                                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=porProducto&id=<?= $item['id']; ?>"
                                                class="btn btn-info btn-xs" title="Ver Detalles">
                                                <i class="fa fa-list"></i> Detalles
                                            </a>
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
<!--main content end-->