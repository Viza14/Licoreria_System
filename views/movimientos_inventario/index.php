<?php
$total_registros = isset($total_movimientos) ? $total_movimientos : count($movimientos);
?>
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
                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=generarMovimiento" class="btn btn-success btn-xs">
                                <i class="fa fa-plus"></i> Generar Movimiento
                            </a>
                            <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#filtrosModal">
                                <i class="fa fa-filter"></i> Filtros
                            </button>
                            <?php if ($_SESSION['user_rol'] != 2): ?>
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
                                <input type="text" id="busqueda" class="form-control" placeholder="Buscar por producto, usuario, tipo, observaciones, transacción..." value="<?= isset($filtros_activos['busqueda']) ? htmlspecialchars($filtros_activos['busqueda']) : '' ?>">
                            </div>
                        </div>

                        <div id="sin-resultados" class="alert alert-warning text-center" style="display: none;">
                            <i class="fa fa-exclamation-circle"></i> No se encontraron movimientos que coincidan con los criterios de búsqueda
                        </div>

                        <div class="table-responsive">
                            <table id="tablaMovimientos" class="table table-striped table-advance table-hover">
                                <thead>
                                    <tr>
                                        <th><i class="fa fa-calendar"></i> Fecha</th>
                                        <th><i class="fa fa-box"></i> Producto</th>
                                        <th><i class="fa fa-exchange"></i> Tipo</th>
                                        <th><i class="fa fa-sort-numeric-up"></i> Cantidad</th>
                                        <th><i class="fa fa-money-bill"></i> Precio</th>
                                        <th><i class="fa fa-user"></i> Usuario</th>
                                        <th><i class="fa fa-barcode"></i> N° Transacción</th>
                                        <th><i class="fa fa-comment"></i> Observaciones</th>
                                        <th><i class="fa fa-hashtag"></i> Referencia</th>
                                        <th><i class="fa fa-cogs"></i> Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($movimientos as $movimiento): ?>
                                        <?php
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
                                            <td><?= date('d/m/Y h:i A', strtotime($movimiento['fecha_movimiento'])); ?></td>
                                            <td><?= htmlspecialchars($movimiento['producto']); ?></td>
                                            <td>
                                                <span class="label label-<?= $movimiento['tipo_movimiento'] == 'ENTRADA' ? 'success' : ($movimiento['tipo_movimiento'] == 'SALIDA' ? 'danger' : 'warning'); ?>">
                                                    <?= $movimiento['tipo_movimiento']; ?>
                                                </span>
                                                <?php if (!empty($movimiento['subtipo_movimiento'])): ?>
                                                    <br><small class="text-muted"><?= $movimiento['subtipo_movimiento']; ?></small>
                                                <?php endif; ?>
                                                <?php if ($es_inactivo): ?>
                                                    <span class="label label-default">ANULADO</span>
                                                <?php elseif ($tiene_ajuste > 0): ?>
                                                    <span class="label label-info">Ajustado</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $movimiento['cantidad']; ?></td>
                                            <td class="<?= $movimiento['tipo_movimiento'] == 'ENTRADA' ? 'text-success' : ($movimiento['tipo_movimiento'] == 'SALIDA' ? 'text-danger' : ''); ?>">
                                                <?= number_format($movimiento['precio_unitario'], 2, ',', '.'); ?> Bs
                                            </td>
                                            <td><?= $movimiento['usuario']; ?></td>
                                            <td>
                                                <?php if (!empty($movimiento['numero_transaccion'])): ?>
                                                    <code><?= $movimiento['numero_transaccion']; ?></code>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($movimiento['observaciones_completas'])): ?>
                                                    <small><?= htmlspecialchars($movimiento['observaciones_completas']); ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $movimiento['referencia'] ?? 'N/A'; ?></td>
                                            <td>
                                <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=mostrar&id=<?= $movimiento['id']; ?>"
                                    class="btn btn-<?= (!$es_inactivo && $tiene_ajuste == 0) ? 'success' : 'info' ?> btn-xs" 
                                    title="Ver Detalles">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación del lado del cliente -->
                        <div id="paginacion-movimientos" class="text-center" style="display: none;">
                            <nav aria-label="Page navigation">
                                <ul class="pagination" id="pagination-links">
                                    <!-- Los enlaces de paginación se generarán dinámicamente -->
                                </ul>
                            </nav>
                            <div class="text-muted" id="pagination-info">
                                <!-- La información de paginación se generará dinámicamente -->
                            </div>
                        </div>
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
                        <label>Tipo de Movimiento</label>
                        <select class="form-control" id="filtroTipo">
                            <option value="">Todos los tipos</option>
                            <option value="ENTRADA" <?= isset($filtros_activos['tipos']) && in_array('ENTRADA', $filtros_activos['tipos']) ? 'selected' : '' ?>>Entrada</option>
                            <option value="SALIDA" <?= isset($filtros_activos['tipos']) && in_array('SALIDA', $filtros_activos['tipos']) ? 'selected' : '' ?>>Salida</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select class="form-control" id="filtroEstatus">
                            <option value="">Todos los estados</option>
                            <option value="1" <?= isset($filtros_activos['estados']) && in_array('1', $filtros_activos['estados']) ? 'selected' : '' ?>>Activo</option>
                            <option value="2" <?= isset($filtros_activos['estados']) && in_array('2', $filtros_activos['estados']) ? 'selected' : '' ?>>ANULADO</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Rango de Fechas</label>
                        <div class="input-group">
                            <input type="date" class="form-control" id="filtroFechaInicio" placeholder="Fecha Inicio" value="<?= isset($filtros_activos['fecha_inicio']) ? htmlspecialchars($filtros_activos['fecha_inicio']) : '' ?>">
                            <span class="input-group-addon">hasta</span>
                            <input type="date" class="form-control" id="filtroFechaFin" placeholder="Fecha Fin" value="<?= isset($filtros_activos['fecha_fin']) ? htmlspecialchars($filtros_activos['fecha_fin']) : '' ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Rango de Precios</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="filtroPrecioMin" placeholder="Precio Mínimo" min="0" step="0.01">
                            <span class="input-group-addon">a</span>
                            <input type="number" class="form-control" id="filtroPrecioMax" placeholder="Precio Máximo" min="0" step="0.01">
                        </div>
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

<script>
$(document).ready(function() {
    // Variables para paginación
    let movimientosPorPagina = 20;
    let paginaActual = 1;
    let movimientosFiltrados = [];
    let todosLosMovimientos = [];
    let buscandoActualmente = false;

    // Función para normalizar texto (remover acentos y convertir a minúsculas)
    function normalizarTexto(texto) {
        return texto.toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');
    }

    // Inicializar datos
    function inicializarDatos() {
        const tabla = $('#tablaMovimientos tbody tr');
        todosLosMovimientos = tabla.toArray();
        movimientosFiltrados = [...todosLosMovimientos];
        mostrarPagina(1);
        actualizarPaginacion();
    }

    // Búsqueda en tiempo real del lado del cliente
    let searchTimeout;
    $('#busqueda').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            filtrarMovimientos();
        }, 300);
    });

    // Función para filtrar movimientos
    function filtrarMovimientos() {
        const busqueda = normalizarTexto($('#busqueda').val().trim());
        const sinResultados = $('#sin-resultados');
        const paginacion = $('#paginacion-movimientos');
        
        if (busqueda === '') {
            // Si no hay búsqueda, mostrar todos los movimientos con paginación
            buscandoActualmente = false;
            movimientosFiltrados = [...todosLosMovimientos];
            paginaActual = 1;
            mostrarPagina(1);
            actualizarPaginacion();
            sinResultados.hide();
            paginacion.show();
        } else {
            // Filtrar movimientos según la búsqueda
            buscandoActualmente = true;
            movimientosFiltrados = todosLosMovimientos.filter(function(fila) {
                const $fila = $(fila);
                const producto = normalizarTexto($fila.find('td:eq(1)').text());
                const usuario = normalizarTexto($fila.find('td:eq(5)').text());
                const tipo = normalizarTexto($fila.find('td:eq(2)').text());
                const observaciones = normalizarTexto($fila.find('td:eq(7)').text());
                const transaccion = normalizarTexto($fila.find('td:eq(6)').text());
                
                return producto.includes(busqueda) || 
                       usuario.includes(busqueda) || 
                       tipo.includes(busqueda) || 
                       observaciones.includes(busqueda) ||
                       transaccion.includes(busqueda);
            });

            // Ocultar paginación durante la búsqueda y mostrar todos los resultados
            paginacion.hide();
            
            // Ocultar todas las filas primero
            $(todosLosMovimientos).hide();
            
            // Mostrar solo las filas filtradas
            $(movimientosFiltrados).show();

            // Mostrar/ocultar mensaje de sin resultados
            if (movimientosFiltrados.length === 0) {
                sinResultados.show();
            } else {
                sinResultados.hide();
            }
        }
    }

    // Función para mostrar una página específica
    function mostrarPagina(pagina) {
        if (buscandoActualmente) return; // No paginar durante búsqueda
        
        paginaActual = pagina;
        const inicio = (pagina - 1) * movimientosPorPagina;
        const fin = inicio + movimientosPorPagina;
        
        // Ocultar todas las filas
        $(todosLosMovimientos).hide();
        
        // Mostrar solo las filas de la página actual
        const movimientosPagina = movimientosFiltrados.slice(inicio, fin);
        $(movimientosPagina).show();
    }

    // Función para actualizar la paginación
    function actualizarPaginacion() {
        if (buscandoActualmente) return; // No mostrar paginación durante búsqueda
        
        const totalPaginas = Math.ceil(movimientosFiltrados.length / movimientosPorPagina);
        const paginationLinks = $('#pagination-links');
        const paginationInfo = $('#pagination-info');
        const paginacion = $('#paginacion-movimientos');
        
        if (totalPaginas <= 1) {
            paginacion.hide();
            return;
        }
        
        paginacion.show();
        paginationLinks.empty();
        
        // Botón Primera página
        if (paginaActual > 1) {
            paginationLinks.append(`
                <li><a href="#" data-pagina="1" title="Primera página">
                    <i class="fa fa-angle-double-left"></i>
                </a></li>
            `);
        }
        
        // Botón Anterior
        if (paginaActual > 1) {
            paginationLinks.append(`
                <li><a href="#" data-pagina="${paginaActual - 1}" title="Página anterior">
                    <i class="fa fa-angle-left"></i>
                </a></li>
            `);
        }
        
        // Páginas numeradas
        let inicioRango = Math.max(1, paginaActual - 2);
        let finRango = Math.min(totalPaginas, paginaActual + 2);
        
        for (let i = inicioRango; i <= finRango; i++) {
            const claseActiva = i === paginaActual ? 'active' : '';
            paginationLinks.append(`
                <li class="${claseActiva}">
                    <a href="#" data-pagina="${i}">${i}</a>
                </li>
            `);
        }
        
        // Botón Siguiente
        if (paginaActual < totalPaginas) {
            paginationLinks.append(`
                <li><a href="#" data-pagina="${paginaActual + 1}" title="Página siguiente">
                    <i class="fa fa-angle-right"></i>
                </a></li>
            `);
        }
        
        // Botón Última página
        if (paginaActual < totalPaginas) {
            paginationLinks.append(`
                <li><a href="#" data-pagina="${totalPaginas}" title="Última página">
                    <i class="fa fa-angle-double-right"></i>
                </a></li>
            `);
        }
        
        // Información de paginación
        const inicio = (paginaActual - 1) * movimientosPorPagina + 1;
        const fin = Math.min(paginaActual * movimientosPorPagina, movimientosFiltrados.length);
        paginationInfo.html(`Mostrando ${inicio}-${fin} de ${movimientosFiltrados.length} movimientos`);
    }

    // Event listener para los enlaces de paginación
    $(document).on('click', '#pagination-links a', function(e) {
        e.preventDefault();
        const pagina = parseInt($(this).data('pagina'));
        if (pagina && !buscandoActualmente) {
            // Limpiar búsqueda al cambiar de página
            $('#busqueda').val('');
            mostrarPagina(pagina);
            actualizarPaginacion();
        }
    });

    // Funcionalidad de filtros avanzados (mantener la funcionalidad original)
    function aplicarFiltrosAvanzados() {
        const searchValue = $('#busqueda').val().trim();
        const tipo = $('#filtroTipo').val();
        const estatus = $('#filtroEstatus').val();
        const fechaInicio = $('#filtroFechaInicio').val();
        const fechaFin = $('#filtroFechaFin').val();

        // Construir la URL con los filtros
        let url = '<?= BASE_URL ?>index.php?action=movimientos-inventario';
        
        if (searchValue) url += '&busqueda=' + encodeURIComponent(searchValue);
        if (tipo) url += '&tipos=' + encodeURIComponent(tipo);
        if (estatus) url += '&estados=' + encodeURIComponent(estatus);
        if (fechaInicio) url += '&fecha_inicio=' + encodeURIComponent(fechaInicio);
        if (fechaFin) url += '&fecha_fin=' + encodeURIComponent(fechaFin);

        // Redirigir a la URL con los filtros
        window.location.href = url;
    }

    $('#aplicarFiltros').click(function() {
        aplicarFiltrosAvanzados();
        $('#filtrosModal').modal('hide');
    });

    // Clear filters
    $('#limpiarFiltros').click(function() {
        window.location.href = '<?= BASE_URL ?>index.php?action=movimientos-inventario';
    });

    // Inicializar al cargar la página
    inicializarDatos();
});
</script>

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