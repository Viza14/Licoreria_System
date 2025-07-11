<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-chart-bar"></i> Resumen de Movimientos</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-exchange"></i><a href="<?= BASE_URL ?>index.php?action=movimientos-inventario">Movimientos</a></li>
                    <li><i class="fa fa-chart-bar"></i> Resumen</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <!-- Filtros -->
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Filtros de Búsqueda
                    </header>
                    <div class="panel-body">
                        <form method="GET" class="form-inline" role="form">
                            <input type="hidden" name="action" value="movimientos-inventario">
                            <input type="hidden" name="method" value="resumen">
                            <div class="form-group">
                                <label class="sr-only" for="fecha_inicio">Fecha Inicio</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                       value="<?= isset($filtros['fecha_inicio']) ? $filtros['fecha_inicio'] : '' ?>">
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="fecha_fin">Fecha Fin</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin"
                                       value="<?= isset($filtros['fecha_fin']) ? $filtros['fecha_fin'] : '' ?>">
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="categoria">Categoría</label>
                                <select class="form-control" id="categoria" name="categoria">
                                    <option value="">Todas las categorías</option>
                                    <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (isset($filtros['categoria']) && $filtros['categoria'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= $cat['nombre'] ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=resumen" class="btn btn-default">Limpiar</a>
                        </form>
                    </div>
                </section>
            </div>
        </div>

        <!-- Resumen General -->
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <div class="info-box blue-bg">
                    <i class="fa fa-cube"></i>
                    <div class="count"><?= $resumen_general['total_productos'] ?></div>
                    <div class="title">Total Productos</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <div class="info-box green-bg">
                    <i class="fa fa-truck-loading"></i>
                    <div class="count"><?= $resumen_general['total_entradas'] ?></div>
                    <div class="title">Total Entradas</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <div class="info-box red-bg">
                    <i class="fa fa-shopping-cart"></i>
                    <div class="count"><?= $resumen_general['total_salidas'] ?></div>
                    <div class="title">Total Salidas</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <div class="info-box blue-bg">
                    <i class="fa fa-money-bill"></i>
                    <div class="count">$<?= number_format($resumen_general['valor_inventario'], 2) ?></div>
                    <div class="title">Valor del Inventario</div>
                </div>
            </div>
        </div>

        <!-- Tabla de Productos -->
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Detalle por Producto
                    </header>
                    <div class="table-responsive">
                        <table class="table table-striped table-advance table-hover">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Stock Actual</th>
                                    <th>Entradas</th>
                                    <th>Salidas</th>
                                    <th>Valor Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos as $producto): ?>
                                <tr>
                                    <td><?= $producto['descripcion'] ?></td>
                                    <td><?= $producto['categoria'] ?></td>
                                    <td><?= $producto['stock_actual'] ?></td>
                                    <td class="text-success"><?= $producto['entradas'] ?></td>
                                    <td class="text-danger"><?= $producto['salidas'] ?></td>
                                    <td>$<?= number_format($producto['valor_total'], 2) ?></td>
                                    <td>
                                        <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=porProducto&id=<?= $producto['id'] ?>" 
                                           class="btn btn-primary btn-xs" title="Ver Movimientos">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <!-- Paginación -->
                        <?php if ($total_paginas > 1): ?>
                            <div class="text-center">
                                <ul class="pagination pagination-sm">
                                    <?php 
                                    // Construir parámetros de filtro para la URL
                                    $params = [];
                                    if (!empty($filtros['fecha_inicio'])) $params[] = 'fecha_inicio=' . $filtros['fecha_inicio'];
                                    if (!empty($filtros['fecha_fin'])) $params[] = 'fecha_fin=' . $filtros['fecha_fin'];
                                    if (!empty($filtros['categoria'])) $params[] = 'categoria=' . $filtros['categoria'];
                                    $filter_url = !empty($params) ? '&' . implode('&', $params) : '';
                                    ?>
                                    <?php if ($pagina_actual > 1): ?>
                                        <li>
                                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=resumen&pagina=<?= $pagina_actual - 1 ?><?= $filter_url ?>">
                                                <i class="fa fa-angle-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                        <li class="<?= $i == $pagina_actual ? 'active' : '' ?>">
                                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=resumen&pagina=<?= $i ?><?= $filter_url ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($pagina_actual < $total_paginas): ?>
                                        <li>
                                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=resumen&pagina=<?= $pagina_actual + 1 ?><?= $filter_url ?>">
                                                <i class="fa fa-angle-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->