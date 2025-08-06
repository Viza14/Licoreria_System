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

        <!-- Filtros -->
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Filtros de Búsqueda
                    </header>
                    <div class="panel-body">
                        <form method="GET" class="form-inline" role="form">
                            <input type="hidden" name="action" value="reportes">
                            <input type="hidden" name="method" value="detalleVentas">
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
                                <label class="sr-only" for="id_producto">Producto</label>
                                <select class="form-control" id="id_producto" name="id_producto">
                                    <option value="">Todos los productos</option>
                                    <?php if (isset($productos)): ?>
                                        <?php foreach ($productos as $producto): ?>
                                        <option value="<?= $producto['id'] ?>" <?= (isset($filtros['id_producto']) && $filtros['id_producto'] == $producto['id']) ? 'selected' : '' ?>>
                                            <?= $producto['descripcion'] ?>
                                        </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            <a href="<?= BASE_URL ?>index.php?action=reportes&method=detalleVentas" class="btn btn-default">Limpiar</a>
                        </form>
                    </div>
                </section>
            </div>
        </div>

        <!-- Tabla de Detalle de Ventas -->
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Detalle de Ventas
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <input type="text" id="busquedaVentas" class="form-control" 
                                       placeholder="Buscar por producto, cliente, vendedor, ID venta...">
                                <span class="input-group-btn">
                                    <button type="button" id="limpiarBusqueda" class="btn btn-default" style="display: none;">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="tablaDetalleVentas" class="table table-striped table-advance table-hover">
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
                                <?php if (isset($reporte) && !empty($reporte)): ?>
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
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No hay datos disponibles</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- Mensaje cuando no hay resultados -->
                        <div id="sin-resultados-ventas" class="alert alert-warning text-center" style="display: none;">
                            <i class="fa fa-exclamation-circle"></i> No se encontraron ventas que coincidan con la búsqueda
                        </div>

                        <!-- Paginación -->
                        <?php if (isset($total_paginas) && $total_paginas > 1): ?>
                            <div class="text-center" id="paginacion-ventas">
                                <ul class="pagination pagination-sm">
                                    <?php 
                                    // Construir parámetros de filtro para la URL
                                    $params = [];
                                    if (!empty($filtros['fecha_inicio'])) $params[] = 'fecha_inicio=' . $filtros['fecha_inicio'];
                                    if (!empty($filtros['fecha_fin'])) $params[] = 'fecha_fin=' . $filtros['fecha_fin'];
                                    if (!empty($filtros['id_producto'])) $params[] = 'id_producto=' . $filtros['id_producto'];
                                    $filter_url = !empty($params) ? '&' . implode('&', $params) : '';
                                    ?>
                                    
                                    <!-- Primera página -->
                                    <?php if ($pagina_actual > 1): ?>
                                        <li>
                                            <a href="<?= BASE_URL ?>index.php?action=reportes&method=detalleVentas&pagina=1<?= $filter_url ?>">
                                                <i class="fa fa-angle-double-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <!-- Página anterior -->
                                    <?php if ($pagina_actual > 1): ?>
                                        <li>
                                            <a href="<?= BASE_URL ?>index.php?action=reportes&method=detalleVentas&pagina=<?= $pagina_actual - 1 ?><?= $filter_url ?>">
                                                <i class="fa fa-angle-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <!-- Páginas numeradas -->
                                    <?php 
                                    $inicio = max(1, $pagina_actual - 2);
                                    $fin = min($total_paginas, $pagina_actual + 2);
                                    ?>
                                    
                                    <?php for ($i = $inicio; $i <= $fin; $i++): ?>
                                        <li class="<?= $i == $pagina_actual ? 'active' : '' ?>">
                                            <a href="<?= BASE_URL ?>index.php?action=reportes&method=detalleVentas&pagina=<?= $i ?><?= $filter_url ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <!-- Página siguiente -->
                                    <?php if ($pagina_actual < $total_paginas): ?>
                                        <li>
                                            <a href="<?= BASE_URL ?>index.php?action=reportes&method=detalleVentas&pagina=<?= $pagina_actual + 1 ?><?= $filter_url ?>">
                                                <i class="fa fa-angle-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <!-- Última página -->
                                    <?php if ($pagina_actual < $total_paginas): ?>
                                        <li>
                                            <a href="<?= BASE_URL ?>index.php?action=reportes&method=detalleVentas&pagina=<?= $total_paginas ?><?= $filter_url ?>">
                                                <i class="fa fa-angle-double-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                                
                                <!-- Información de paginación -->
                                <div class="pagination-info">
                                    <small class="text-muted">
                                        <?php if (isset($total_registros)): ?>
                                            Mostrando <?= (($pagina_actual - 1) * 20) + 1 ?> - <?= min($pagina_actual * 20, $total_registros) ?> 
                                            de <?= $total_registros ?> registros
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>


<!--main content end-->

<script>
$(document).ready(function() {
    let todasLasFilas = [];
    let paginacionOriginal = '';
    let datosOriginales = [];
    
    // Función para normalizar texto (quitar acentos, convertir a minúsculas, quitar espacios extra)
    function normalizeText(text) {
        return text.toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '') // Quitar acentos
            .replace(/\s+/g, ' ') // Normalizar espacios
            .trim();
    }
    
    // Cargar todos los datos para búsqueda dinámica
    function cargarTodosLosDatos() {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('busqueda_dinamica', '1');
        
        $.get('?' + urlParams.toString())
            .done(function(data) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(data, 'text/html');
                const filas = doc.querySelectorAll('#tablaDetalleVentas tbody tr');
                
                datosOriginales = [];
                filas.forEach(function(fila) {
                    if (fila.cells && fila.cells.length >= 8) {
                        datosOriginales.push({
                            html: fila.outerHTML,
                            texto: normalizeText(fila.textContent),
                            id: normalizeText(fila.cells[0].textContent),
                            fecha: normalizeText(fila.cells[1].textContent),
                            producto: normalizeText(fila.cells[2].textContent),
                            cliente: normalizeText(fila.cells[6].textContent),
                            vendedor: normalizeText(fila.cells[7].textContent)
                        });
                    }
                });
            });
    }
    
    // Función de búsqueda mejorada
    function buscarVentas() {
        const termino = normalizeText($('#busquedaVentas').val());
        const tabla = $('#tablaDetalleVentas tbody');
        const sinResultados = $('#sin-resultados-ventas');
        const paginacion = $('#paginacion-ventas');
        const limpiarBtn = $('#limpiarBusqueda');
        
        if (termino === '') {
            // Restaurar vista original
            location.reload();
            return;
        }
        
        // Mostrar botón de limpiar
        limpiarBtn.show();
        
        // Filtrar datos
        const resultados = datosOriginales.filter(function(item) {
            return item.texto.includes(termino) ||
                   item.id.includes(termino) ||
                   item.producto.includes(termino) ||
                   item.cliente.includes(termino) ||
                   item.vendedor.includes(termino);
        });
        
        // Actualizar tabla
        tabla.empty();
        
        if (resultados.length === 0) {
            tabla.append('<tr><td colspan="8" class="text-center text-muted">No se encontraron resultados para la búsqueda</td></tr>');
            sinResultados.show();
        } else {
            resultados.forEach(function(item) {
                tabla.append(item.html);
            });
            sinResultados.hide();
        }
        
        // Ocultar paginación durante búsqueda
        if (paginacion.length) {
            paginacion.hide();
        }
        
        // Actualizar contador de resultados
        const contador = $('.panel-heading .badge');
        if (contador.length) {
            contador.text(resultados.length);
        }
    }
    
    // Event listeners
    let timeoutId;
    $('#busquedaVentas').on('input', function() {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(function() {
            if (datosOriginales.length === 0) {
                cargarTodosLosDatos();
                setTimeout(buscarVentas, 500);
            } else {
                buscarVentas();
            }
        }, 300);
    });
    
    $('#limpiarBusqueda').on('click', function() {
        $('#busquedaVentas').val('');
        $(this).hide();
        location.reload();
    });
    
    // Cargar datos al inicio si la página no tiene muchos registros
    const filasActuales = $('#tablaDetalleVentas tbody tr').length;
    if (filasActuales > 0) {
        cargarTodosLosDatos();
    }
});
</script>
