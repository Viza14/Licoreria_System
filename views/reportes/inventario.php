<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-cubes"></i> Reporte de Inventario</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-file-text"></i><a href="<?= BASE_URL ?>index.php?action=reportes">Reportes</a></li>
                    <li><i class="fa fa-cubes"></i> Inventario</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Listado de Inventario
                        <div class="pull-right">
                            <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#filtrosModal">
                                <i class="fa fa-filter"></i> Filtros
                            </button>
                            <a href="<?= BASE_URL ?>index.php?action=reportes&method=exportarInventario<?= !empty($filtros) ? '&' . http_build_query($filtros) : '' ?>" 
                               class="btn btn-success btn-xs">
                                <i class="fa fa-file-excel-o"></i> Exportar a Excel
                            </a>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <input type="text" id="busqueda" class="form-control" placeholder="Buscar por producto, categoría...">
                            </div>
                        </div>

                        <table id="tablaInventario" class="table table-striped table-advance table-hover">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Tipo</th>
                                    <th>Stock Actual</th>
                                    <th>Mínimo</th>
                                    <th>Máximo</th>
                                    <th>Estado</th>
                                    <th>Precio Venta</th>
                                    <th>Precio Compra</th>
                                    <th>Valor Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reporte as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['producto']); ?></td>
                                        <td><?= $item['categoria']; ?></td>
                                        <td><?= $item['tipo_categoria']; ?></td>
                                        <td><?= $item['stock_actual']; ?></td>
                                        <td><?= $item['stock_minimo'] ?? 'N/D'; ?></td>
                                        <td><?= $item['stock_maximo'] ?? 'N/D'; ?></td>
                                        <td>
                                            <?php 
                                            $badge_class = 'default';
                                            if ($item['estado_stock'] == 'CRÍTICO') $badge_class = 'danger';
                                            elseif ($item['estado_stock'] == 'BAJO') $badge_class = 'warning';
                                            elseif ($item['estado_stock'] == 'EXCESO') $badge_class = 'info';
                                            elseif ($item['estado_stock'] == 'NORMAL') $badge_class = 'success';
                                            ?>
                                            <span class="label label-<?= $badge_class ?>"><?= $item['estado_stock']; ?></span>
                                        </td>
                                        <td><?= number_format($item['precio_venta'], 2, ',', '.'); ?> Bs</td>
                                        <td><?= $item['precio_compra_minimo'] ? number_format($item['precio_compra_minimo'], 2, ',', '.') : 'N/D'; ?> Bs</td>
                                        <td><?= number_format($item['valor_total'], 2, ',', '.'); ?> Bs</td>
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

<!-- Modal de Filtros -->
<div class="modal fade" id="filtrosModal" tabindex="-1" role="dialog" aria-labelledby="filtrosModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="filtrosModalLabel"><i class="fa fa-filter"></i> Filtros Avanzados</h4>
            </div>
            <div class="modal-body">
                <form id="formFiltros" method="POST" action="<?= BASE_URL ?>index.php?action=reportes&method=inventario">
                    <div class="form-group">
                        <label>Tipo Categoría:</label>
                        <select class="form-control" id="id_tipo_categoria" name="id_tipo_categoria">
                            <option value="">Todos</option>
                            <?php foreach ($tiposCategoria as $tipo): ?>
                                <option value="<?= $tipo['id'] ?>" <?= isset($filtros['id_tipo_categoria']) && $filtros['id_tipo_categoria'] == $tipo['id'] ? 'selected' : '' ?>>
                                    <?= $tipo['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Categoría:</label>
                        <select class="form-control" id="id_categoria" name="id_categoria">
                            <option value="">Todas</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['id'] ?>" data-tipo="<?= $categoria['id_tipo_categoria'] ?>" <?= isset($filtros['id_categoria']) && $filtros['id_categoria'] == $categoria['id'] ? 'selected' : '' ?>>
                                    <?= $categoria['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Estado Stock:</label>
                        <select class="form-control" id="estado_stock" name="estado_stock">
                            <option value="">Todos</option>
                            <option value="CRITICO" <?= isset($filtros['estado_stock']) && $filtros['estado_stock'] == 'CRITICO' ? 'selected' : '' ?>>Crítico</option>
                            <option value="BAJO" <?= isset($filtros['estado_stock']) && $filtros['estado_stock'] == 'BAJO' ? 'selected' : '' ?>>Bajo</option>
                            <option value="NORMAL" <?= isset($filtros['estado_stock']) && $filtros['estado_stock'] == 'NORMAL' ? 'selected' : '' ?>>Normal</option>
                            <option value="EXCESO" <?= isset($filtros['estado_stock']) && $filtros['estado_stock'] == 'EXCESO' ? 'selected' : '' ?>>Exceso</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="submit" form="formFiltros" class="btn btn-primary">Aplicar Filtros</button>
                <a href="<?= BASE_URL ?>index.php?action=reportes&method=inventario" class="btn btn-link">Limpiar Filtros</a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Real-time search functionality
    $('#busqueda').on('input', function() {
        const searchValue = $(this).val().toLowerCase();
        $('#tablaInventario tbody tr').each(function() {
            const producto = $(this).find('td:eq(0)').text().toLowerCase();
            const categoria = $(this).find('td:eq(1)').text().toLowerCase();
            const tipo = $(this).find('td:eq(2)').text().toLowerCase();
            
            if(producto.includes(searchValue) || categoria.includes(searchValue) || tipo.includes(searchValue)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Category filtering based on type
    $('#id_tipo_categoria').change(function() {
        const tipoId = $(this).val();
        $('#id_categoria option').each(function() {
            const $option = $(this);
            if ($option.val() === '' || $option.data('tipo') == tipoId) {
                $option.show();
            } else {
                $option.hide();
            }
        });
        if($('#id_categoria option:selected').is(':hidden')) {
            $('#id_categoria').val('');
        }
    });
    
    // Initialize category filter
    $('#id_tipo_categoria').trigger('change');
});
</script>
<!--main content end-->
