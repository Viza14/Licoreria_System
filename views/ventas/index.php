<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-shopping-cart"></i> Gestión de Ventas</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-shopping-cart"></i> Ventas</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Listado de Ventas
                        <div class="pull-right">
                            <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#filtrosModal">
                                <i class="fa fa-filter"></i> Filtros
                            </button>
                            <a href="<?= BASE_URL ?>index.php?action=ventas&method=crear" class="btn btn-primary btn-xs">
                                <i class="fa fa-plus"></i> Nueva Venta
                            </a>
                        </div>
                    </header>
                    
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <input type="text" id="busqueda" class="form-control" placeholder="Buscar por cliente, fecha..." value="<?= isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : '' ?>">
                            </div>
                        </div>

                        <div id="filtros-activos" class="alert alert-info" style="<?= (!empty($filtros_activos) || isset($_GET['busqueda']) || isset($_GET['fecha_inicio']) || isset($_GET['fecha_fin']) || isset($_GET['vendedor'])) ? 'display: block;' : 'display: none;' ?> margin-bottom: 10px;">
                            <i class="fa fa-filter"></i> Filtros activos: <span id="texto-filtros-activos"><?php
                                $filtros_texto = [];
                                if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
                                    $filtros_texto[] = 'Búsqueda: "' . htmlspecialchars($_GET['busqueda']) . '"';
                                }
                                if (isset($_GET['fecha_inicio']) && !empty($_GET['fecha_inicio'])) {
                                    $filtros_texto[] = 'Desde: ' . date('d/m/Y', strtotime($_GET['fecha_inicio']));
                                }
                                if (isset($_GET['fecha_fin']) && !empty($_GET['fecha_fin'])) {
                                    $filtros_texto[] = 'Hasta: ' . date('d/m/Y', strtotime($_GET['fecha_fin']));
                                }
                                if (isset($_GET['vendedor']) && !empty($_GET['vendedor'])) {
                                    $filtros_texto[] = 'Vendedor: ' . htmlspecialchars($_GET['vendedor']);
                                }
                                echo implode(' | ', $filtros_texto);
                            ?></span>
                            <div class="pull-right">
                                <strong><i class="fa fa-list"></i> Resultados encontrados: <span id="contador-resultados"><?= count($ventas) ?></span></strong>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="tablaVentas" class="table table-striped table-advance table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fa fa-hashtag"></i> ID</th>
                                    <th><i class="fa fa-calendar"></i> Fecha</th>
                                    <th><i class="fa fa-user"></i> Cliente</th>
                                    <th><i class="fa fa-money-bill"></i> Total</th>
                                    <th><i class="fa fa-user-tie"></i> Vendedor</th>
                                    <th><i class="fa fa-cogs"></i> Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ventas as $venta): ?>
                                    <tr>
                                        <td><?= $venta['id']; ?></td>
                                        <td><?= date('d/m/Y h:i A', strtotime($venta['fecha'])); ?></td>
                                        <td><?= htmlspecialchars($venta['cliente']); ?></td>
                                        <td><?= number_format($venta['monto_total'], 2, ',', '.'); ?> Bs</td>
                                        <td><?= htmlspecialchars($venta['usuario']); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?= BASE_URL ?>index.php?action=ventas&method=mostrar&id=<?= $venta['id']; ?>"
                                                    class="btn btn-success btn-xs" title="Ver Detalles">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                        <?php if (isset($total_paginas) && $total_paginas > 1): ?>
                            <div class="text-center">
                                <ul class="pagination">
                                    <?php if ($pagina_actual > 1): ?>
                                        <li>
                                            <a href="<?= BASE_URL ?>index.php?action=ventas&method=index&pagina=<?= ($pagina_actual-1) ?>&por_pagina=<?= $por_pagina ?><?= isset($termino_busqueda) ? '&busqueda=' . urlencode($termino_busqueda) : '' ?>">
                                                <i class="fa fa-angle-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                        <li class="<?= $i === $pagina_actual ? 'active' : '' ?>">
                                            <a href="<?= BASE_URL ?>index.php?action=ventas&method=index&pagina=<?= $i ?>&por_pagina=<?= $por_pagina ?><?= isset($termino_busqueda) ? '&busqueda=' . urlencode($termino_busqueda) : '' ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($pagina_actual < $total_paginas): ?>
                                        <li>
                                            <a href="<?= BASE_URL ?>index.php?action=ventas&method=index&pagina=<?= ($pagina_actual+1) ?>&por_pagina=<?= $por_pagina ?><?= isset($termino_busqueda) ? '&busqueda=' . urlencode($termino_busqueda) : '' ?>">
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

<!-- Modal de Filtros -->
<div class="modal fade" id="filtrosModal" tabindex="-1" role="dialog" aria-labelledby="filtrosModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="filtrosModalLabel"><i class="fa fa-filter"></i> Filtros Avanzados</h4>
            </div>
            <div class="modal-body">
                <form id="formFiltros">
                    <div class="form-group">
                        <label>Fecha Inicio:</label>
                        <input type="date" class="form-control" id="filtroFechaInicio">
                    </div>
                    <div class="form-group">
                        <label>Fecha Fin:</label>
                        <input type="date" class="form-control" id="filtroFechaFin">
                    </div>
                    <div class="form-group">
                        <label>Vendedor:</label>
                        <select class="form-control" id="filtroVendedor">
                            <option value="">Todos los vendedores</option>
                            <?php foreach ($usuarios as $usuario): ?>
                                <option value="<?= htmlspecialchars($usuario['user']) ?>" <?= isset($_GET['vendedor']) && $_GET['vendedor'] === $usuario['user'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="aplicarFiltros">Aplicar Filtros</button>
                <button type="button" class="btn btn-link" id="limpiarFiltros">Limpiar Filtros</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts para manejar la búsqueda, filtros y SweetAlert -->
<script>
    $(document).ready(function() {
        // Búsqueda en tiempo real sin recargar la página
        $('#busqueda').on('input', function() {
            const searchValue = $(this).val().trim().toLowerCase();
            let encontrados = 0;
            
            $('#tablaVentas tbody tr').each(function() {
                const cliente = $(this).find('td:eq(2)').text().toLowerCase();
                const fecha = $(this).find('td:eq(1)').text().toLowerCase();
                const vendedor = $(this).find('td:eq(4)').text().toLowerCase();
                const id = $(this).find('td:eq(0)').text().toLowerCase();
                
                if (cliente.includes(searchValue) || 
                    fecha.includes(searchValue) || 
                    vendedor.includes(searchValue) ||
                    id.includes(searchValue)) {
                    $(this).show();
                    encontrados++;
                } else {
                    $(this).hide();
                }
            });
            
            // Actualizar contador de resultados
            $('#contador-resultados').text(encontrados);
            
            // Mostrar u ocultar mensaje de sin resultados
            if (encontrados === 0) {
                if (!$('#sin-resultados').length) {
                    const sinResultados = $('<div id="sin-resultados" class="alert alert-warning text-center">' +
                        '<i class="fa fa-exclamation-circle"></i> No se encontraron ventas que coincidan con la búsqueda</div>');
                    $('#tablaVentas').before(sinResultados);
                } else {
                    $('#sin-resultados').show();
                }
            } else {
                $('#sin-resultados').hide();
            }
            
            // Actualizar filtros activos
            if (searchValue) {
                $('#filtros-activos').show();
                const filtrosTexto = [];
                filtrosTexto.push('Búsqueda: "' + searchValue + '"');
                $('#texto-filtros-activos').html(filtrosTexto.join(' | '));
            } else if (!$('#filtroFechaInicio').val() && !$('#filtroFechaFin').val() && !$('#filtroVendedor').val()) {
                $('#filtros-activos').hide();
            }
        });

        // Filtros avanzados
        $('#aplicarFiltros').click(function() {
            const fechaInicio = $('#filtroFechaInicio').val();
            const fechaFin = $('#filtroFechaFin').val();
            const vendedor = $('#filtroVendedor').val();
            
            const currentUrl = new URL(window.location.href);
            
            if (fechaInicio) currentUrl.searchParams.set('fecha_inicio', fechaInicio);
            else currentUrl.searchParams.delete('fecha_inicio');
            
            if (fechaFin) currentUrl.searchParams.set('fecha_fin', fechaFin);
            else currentUrl.searchParams.delete('fecha_fin');
            
            if (vendedor) currentUrl.searchParams.set('vendedor', vendedor);
            else currentUrl.searchParams.delete('vendedor');
            
            currentUrl.searchParams.set('pagina', '1');
            window.location.href = currentUrl.toString();
        });

        $('#limpiarFiltros').click(function() {
            const currentUrl = new URL(window.location.href);
            ['fecha_inicio', 'fecha_fin', 'vendedor', 'busqueda'].forEach(param => {
                currentUrl.searchParams.delete(param);
            });
            window.location.href = currentUrl.toString();
        });
    });
</script>
<!--main content end-->

<?php if (isset($_SESSION['mensaje'])): ?>
<script>
    Swal.fire({
        title: '<?= $_SESSION['mensaje']['title'] ?>',
        text: '<?= $_SESSION['mensaje']['text'] ?>',
        icon: '<?= $_SESSION['mensaje']['icon'] ?>',
        timer: 3000,
        timerProgressBar: true
    });
</script>
<?php unset($_SESSION['mensaje']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<script>
    Swal.fire({
        title: '<?= $_SESSION['error']['title'] ?>',
        text: '<?= $_SESSION['error']['text'] ?>',
        icon: '<?= $_SESSION['error']['icon'] ?>',
        timer: 3000,
        timerProgressBar: true
    });
</script>
<?php unset($_SESSION['error']); ?>
<?php endif; ?>

</section>