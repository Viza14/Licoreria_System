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
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <input type="text" id="busquedaProductos" class="form-control" placeholder="Buscar por producto, categoría...">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="tablaProductosResumen" class="table table-striped table-advance table-hover">
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

                        <!-- Mensaje cuando no hay resultados -->
                        <div id="sin-resultados-resumen" class="alert alert-warning text-center" style="display: none;">
                            <i class="fa fa-exclamation-circle"></i> No se encontraron productos que coincidan con la búsqueda
                        </div>

                        <!-- Paginación -->
                        <?php if ($total_paginas > 1): ?>
                            <div class="text-center" id="paginacion-resumen">
                                <ul class="pagination pagination-sm">
                                    <?php 
                                    // Construir parámetros de filtro para la URL
                                    $params = [];
                                    if (!empty($filtros['fecha_inicio'])) $params[] = 'fecha_inicio=' . $filtros['fecha_inicio'];
                                    if (!empty($filtros['fecha_fin'])) $params[] = 'fecha_fin=' . $filtros['fecha_fin'];
                                    if (!empty($filtros['categoria'])) $params[] = 'categoria=' . $filtros['categoria'];
                                    $filter_url = !empty($params) ? '&' . implode('&', $params) : '';
                                    ?>
                                    
                                    <!-- Primera página -->
                                    <?php if ($pagina_actual > 1): ?>
                                        <li>
                                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=resumen&pagina=1<?= $filter_url ?>">
                                                <i class="fa fa-angle-double-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <!-- Página anterior -->
                                    <?php if ($pagina_actual > 1): ?>
                                        <li>
                                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=resumen&pagina=<?= $pagina_actual - 1 ?><?= $filter_url ?>">
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
                                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=resumen&pagina=<?= $i ?><?= $filter_url ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <!-- Página siguiente -->
                                    <?php if ($pagina_actual < $total_paginas): ?>
                                        <li>
                                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=resumen&pagina=<?= $pagina_actual + 1 ?><?= $filter_url ?>">
                                                <i class="fa fa-angle-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <!-- Última página -->
                                    <?php if ($pagina_actual < $total_paginas): ?>
                                        <li>
                                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=resumen&pagina=<?= $total_paginas ?><?= $filter_url ?>">
                                                <i class="fa fa-angle-double-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                                
                                <!-- Información de paginación -->
                                <div class="pagination-info">
                                    <small class="text-muted">
                                        Mostrando <?= (($pagina_actual - 1) * 20) + 1 ?> - <?= min($pagina_actual * 20, $total_productos) ?> 
                                        de <?= $total_productos ?> productos
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

<script>
        // Variables para manejar la búsqueda y paginación
        let todasLasFilas = [];
        let paginacionOriginal = '';
        
        // Función para normalizar texto (remover acentos y convertir a minúsculas)
        function normalizarTexto(texto) {
            return texto.toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '');
        }

        // Función para filtrar productos
        function filtrarProductos() {
            const busqueda = normalizarTexto(document.getElementById('busquedaProductos').value);
            const tabla = document.getElementById('tablaProductosResumen');
            const filas = tabla.querySelectorAll('tbody tr');
            const sinResultados = document.getElementById('sin-resultados-resumen');
            const paginacion = document.getElementById('paginacion-resumen');
            
            let productosVisibles = 0;

            // Si no hay búsqueda, mostrar todo normal
            if (busqueda === '') {
                filas.forEach(function(fila) {
                    fila.style.display = '';
                });
                sinResultados.style.display = 'none';
                tabla.style.display = 'table';
                if (paginacion) {
                    paginacion.style.display = 'block';
                }
                return;
            }

            // Filtrar filas según la búsqueda
            filas.forEach(function(fila) {
                const producto = normalizarTexto(fila.cells[0].textContent);
                const categoria = normalizarTexto(fila.cells[1].textContent);
                
                if (producto.includes(busqueda) || categoria.includes(busqueda)) {
                    fila.style.display = '';
                    productosVisibles++;
                } else {
                    fila.style.display = 'none';
                }
            });

            // Mostrar/ocultar mensaje de sin resultados
            if (productosVisibles === 0) {
                sinResultados.style.display = 'block';
                tabla.style.display = 'none';
            } else {
                sinResultados.style.display = 'none';
                tabla.style.display = 'table';
            }

            // Ocultar paginación durante la búsqueda
            if (paginacion) {
                paginacion.style.display = 'none';
            }
        }

        // Debounce para optimizar la búsqueda
        let timeoutId;
        document.getElementById('busquedaProductos').addEventListener('input', function() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(filtrarProductos, 300);
        });

        // Limpiar búsqueda al cambiar de página
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('pagina')) {
                document.getElementById('busquedaProductos').value = '';
            }
        });
    </script>
<!--main content end-->