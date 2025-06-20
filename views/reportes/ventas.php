<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-shopping-cart"></i> Reporte de Ventas</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-file-text"></i><a href="<?= BASE_URL ?>index.php?action=reportes">Reportes</a></li>
                    <li><i class="fa fa-shopping-cart"></i> Ventas</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Filtros del Reporte
                        <div class="pull-right">
                            <a href="<?= BASE_URL ?>index.php?action=reportes&method=exportarVentas<?= !empty($filtros) ? '&' . http_build_query($filtros) : '' ?>" 
                               class="btn btn-success btn-xs">
                                <i class="fa fa-file-excel-o"></i> Exportar a Excel
                            </a>
                        </div>
                    </header>
                    <div class="panel-body">
                        <form class="form-inline" method="POST" action="<?= BASE_URL ?>index.php?action=reportes&method=ventas">
                            <div class="form-group">
                                <label for="fecha_inicio">Desde:</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?= $filtros['fecha_inicio'] ?? '' ?>">
                            </div>
                            <div class="form-group">
                                <label for="fecha_fin">Hasta:</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?= $filtros['fecha_fin'] ?? '' ?>">
                            </div>
                            <div class="form-group">
                                <label for="id_usuario">Vendedor:</label>
                                <select class="form-control" id="id_usuario" name="id_usuario">
                                    <option value="">Todos</option>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <option value="<?= $usuario['id'] ?>" <?= isset($filtros['id_usuario']) && $filtros['id_usuario'] == $usuario['id'] ? 'selected' : '' ?>>
                                            <?= $usuario['nombres'] . ' ' . $usuario['apellidos'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="cedula_cliente">Cliente:</label>
                                <select class="form-control" id="cedula_cliente" name="cedula_cliente">
                                    <option value="">Todos</option>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <option value="<?= $cliente['cedula'] ?>" <?= isset($filtros['cedula_cliente']) && $filtros['cedula_cliente'] == $cliente['cedula'] ? 'selected' : '' ?>>
                                            <?= $cliente['nombres'] . ' ' . $cliente['apellidos'] ?> (<?= $cliente['cedula'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
                            <a href="<?= BASE_URL ?>index.php?action=reportes&method=ventas" class="btn btn-default">Limpiar</a>
                        </form>

                        <hr>

                        <table class="table table-striped table-advance table-hover">
                            <thead>
                                <tr>
                                    <th>ID Venta</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Vendedor</th>
                                    <th>Monto Total</th>
                                    <th>Productos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reporte as $venta): ?>
                                    <tr>
                                        <td><?= $venta['id_venta']; ?></td>
                                        <td><?= date('d/m/Y', strtotime($venta['fecha'])); ?></td>
                                        <td><?= $venta['cliente']; ?></td>
                                        <td><?= $venta['vendedor']; ?></td>
                                        <td><?= number_format($venta['monto_total'], 2, ',', '.'); ?> Bs</td>
                                        <td><?= $venta['cantidad_productos']; ?></td>
                                        <td>
                                            <a href="<?= BASE_URL ?>index.php?action=ventas&method=mostrar&id=<?= $venta['id_venta'] ?>" 
                                               class="btn btn-info btn-xs" title="Ver Detalle">
                                                <i class="fa fa-eye"></i>
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
