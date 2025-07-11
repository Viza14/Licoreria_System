<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-list-alt"></i> Detalle de Ventas</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-file-text"></i><a href="<?= BASE_URL ?>index.php?action=reportes">Reportes</a></li>
                    <li><i class="fa fa-list-alt"></i> Detalle Ventas</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Filtros del Reporte
                    </header>
                    <div class="panel-body">
                        <form class="form-inline" method="POST" action="<?= BASE_URL ?>index.php?action=reportes&method=detalleVentas">
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
                            <a href="<?= BASE_URL ?>index.php?action=reportes&method=detalleVentas" class="btn btn-default">Limpiar</a>
                        </form>

                        <hr>

                        <table class="table table-striped table-advance table-hover">
                            <thead>
                                <tr>
                                    <th>ID Venta</th>
                                    <th>Fecha</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                    <th>Cliente</th>
                                    <th>Vendedor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reporte as $detalle): ?>
                                    <tr>
                                        <td><?= $detalle['id_venta']; ?></td>
                                        <td><?= date('d/m/Y', strtotime($detalle['fecha'])); ?></td>
                                        <td><?= $detalle['producto']; ?></td>
                                        <td><?= $detalle['cantidad']; ?></td>
                                        <td><?= number_format($detalle['precio_unitario'], 2, ',', '.'); ?> Bs</td>
                                        <td><?= number_format($detalle['subtotal'], 2, ',', '.'); ?> Bs</td>
                                        <td><?= $detalle['cliente']; ?></td>
                                        <td><?= $detalle['vendedor']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <?php if ($total_paginas > 1): ?>
                            <div class="text-center">
                                <ul class="pagination">
                                    <?php
                                    // Construir la URL base con los filtros actuales
                                    $urlBase = BASE_URL . "index.php?action=reportes&method=detalleVentas";
                                    if (!empty($filtros)) {
                                        $urlBase .= "&" . http_build_query(['filtros' => $filtros]);
                                    }
                                    ?>

                                    <!-- Botón Anterior -->
                                    <li class="<?= $pagina_actual <= 1 ? 'disabled' : '' ?>">
                                        <a href="<?= $pagina_actual > 1 ? $urlBase . '&pagina=' . ($pagina_actual - 1) : '#' ?>">&laquo;</a>
                                    </li>

                                    <!-- Números de página -->
                                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                        <li class="<?= $i == $pagina_actual ? 'active' : '' ?>">
                                            <a href="<?= $urlBase . '&pagina=' . $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <!-- Botón Siguiente -->
                                    <li class="<?= $pagina_actual >= $total_paginas ? 'disabled' : '' ?>">
                                        <a href="<?= $pagina_actual < $total_paginas ? $urlBase . '&pagina=' . ($pagina_actual + 1) : '#' ?>">&raquo;</a>
                                    </li>
                                </ul>
                                <p class="text-muted">Mostrando página <?= $pagina_actual ?> de <?= $total_paginas ?> (<?= $total_registros ?> registros en total)</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->
