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
                            </tbody>
                        </table>

                        <div class="text-center">
                            <ul class="pagination">
                            </ul>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Distribución del Estado de Stock</h3>
                    </div>
                    <div class="panel-body">
                        <canvas id="stockChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Valor Total por Categoría</h3>
                    </div>
                    <div class="panel-body">
                        <canvas id="valorChart" height="250"></canvas>
                    </div>
                </div>
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
                            <option value="CRITICO">Crítico</option>
                            <option value="BAJO">Bajo</option>
                            <option value="NORMAL">Normal</option>
                            <option value="EXCESO">Exceso</option>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Variables globales necesarias
    const BASE_URL = '<?= BASE_URL ?>';
    let stockChart = null;
    let valorChart = null;

    // Message when no results found
    const sinResultados = $('<div id="sin-resultados" class="alert alert-warning text-center" style="display: none;">' +
        '<i class="fa fa-exclamation-circle"></i> No se encontraron productos que coincidan con la búsqueda</div>');
    $('.panel-body').append(sinResultados);

    function actualizarPaginacion(totalPaginas, paginaActual) {
        const paginacion = $('.pagination');
        paginacion.empty();

        // Botón anterior
        if (paginaActual > 1) {
            paginacion.append(`
                <li>
                    <a href="#" data-pagina="${paginaActual - 1}">
                        <i class="fa fa-angle-left"></i>
                    </a>
                </li>
            `);
        }

        // Números de página
        for (let i = 1; i <= totalPaginas; i++) {
            paginacion.append(`
                <li class="${i === paginaActual ? 'active' : ''}">
                    <a href="#" data-pagina="${i}">${i}</a>
                </li>
            `);
        }

        // Botón siguiente
        if (paginaActual < totalPaginas) {
            paginacion.append(`
                <li>
                    <a href="#" data-pagina="${paginaActual + 1}">
                        <i class="fa fa-angle-right"></i>
                    </a>
                </li>
            `);
        }
    }

    function cargarInventario(pagina = 1, forzarPagina = false) {
        const busqueda = $('#busqueda').val().trim() || null;
        const id_tipo_categoria = $('#id_tipo_categoria').val() || null;
        const id_categoria = $('#id_categoria').val() || null;
        const estado_stock = $('#estado_stock').val() || null;

        // Función para normalizar texto (eliminar acentos y convertir a minúsculas)
        const normalizarTexto = (texto) => {
            if (!texto) return null;
            return texto.toLowerCase()
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "");
        };

        // Reiniciar a la primera página cuando se realiza una búsqueda o se aplican filtros
        // a menos que se fuerce una página específica
        if (!forzarPagina && (busqueda || id_tipo_categoria || id_categoria || estado_stock)) {
            pagina = 1;
        }

        $.ajax({
            url: BASE_URL + 'index.php?action=reportes&method=inventario',
            method: 'GET',
            data: {
                ajax: true,
                pagina: pagina,
                busqueda: normalizarTexto(busqueda),
                id_tipo_categoria: id_tipo_categoria,
                id_categoria: id_categoria,
                estado_stock: estado_stock ? estado_stock.toUpperCase() : null
            },
            success: function(response) {
                const tbody = $('#tablaInventario tbody');
                tbody.empty();

                if (response.reporte && response.reporte.length > 0) {
                    response.reporte.forEach(function(item) {
                        let badgeClass = 'default';
                        if (item.estado_stock === 'CRÍTICO') badgeClass = 'danger';
                        else if (item.estado_stock === 'BAJO') badgeClass = 'warning';
                        else if (item.estado_stock === 'EXCESO') badgeClass = 'info';
                        else if (item.estado_stock === 'NORMAL') badgeClass = 'success';

                        const row = `
                            <tr>
                                <td>${item.producto}</td>
                                <td>${item.categoria}</td>
                                <td>${item.tipo_categoria}</td>
                                <td>${item.stock_actual}</td>
                                <td>${item.stock_minimo || 'N/D'}</td>
                                <td>${item.stock_maximo || 'N/D'}</td>
                                <td>
                                    <span class="label label-${badgeClass}">${item.estado_stock}</span>
                                </td>
                                <td>${new Intl.NumberFormat('es-BO', { minimumFractionDigits: 2 }).format(item.precio_venta)} Bs</td>
                                <td>${item.precio_compra_minimo ? new Intl.NumberFormat('es-BO', { minimumFractionDigits: 2 }).format(item.precio_compra_minimo) + ' Bs' : 'N/D'}</td>
                                <td>${new Intl.NumberFormat('es-BO', { minimumFractionDigits: 2 }).format(item.valor_total)} Bs</td>
                            </tr>
                        `;
                        tbody.append(row);
                    });

                    actualizarPaginacion(response.total_paginas, response.pagina_actual);
                    $('#sin-resultados').hide();

                    // Actualizar gráficos
                    actualizarGraficos(response.reporte);
                } else {
                    $('#sin-resultados').show();
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Error',
                    text: 'Error al cargar el inventario',
                    icon: 'error'
                });
            }
        });
    }

    function actualizarGraficos(datos) {
        // Preparar datos para el gráfico de estado de stock
        const stockData = {
            labels: ['Crítico', 'Bajo', 'Normal', 'Exceso'],
            datasets: [{
                data: [0, 0, 0, 0],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(255, 205, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(54, 162, 235, 0.8)'
                ]
            }]
        };

        // Contar productos por estado
        datos.forEach(item => {
            switch (item.estado_stock) {
                case 'CRÍTICO': stockData.datasets[0].data[0]++; break;
                case 'BAJO': stockData.datasets[0].data[1]++; break;
                case 'NORMAL': stockData.datasets[0].data[2]++; break;
                case 'EXCESO': stockData.datasets[0].data[3]++; break;
            }
        });

        // Preparar datos para el gráfico de valor por categoría
        const categoriaValores = {};
        datos.forEach(item => {
            if (!categoriaValores[item.categoria]) {
                categoriaValores[item.categoria] = 0;
            }
            categoriaValores[item.categoria] += parseFloat(item.valor_total);
        });

        // Ordenar categorías por valor y tomar las top 5
        const categoriasOrdenadas = Object.entries(categoriaValores)
            .sort(([,a], [,b]) => b - a)
            .slice(0, 5);

        const valorData = {
            labels: categoriasOrdenadas.map(([cat,]) => cat),
            datasets: [{
                label: 'Valor Total',
                data: categoriasOrdenadas.map(([,val]) => val),
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        };

        // Actualizar o crear gráfico de estado de stock
        if (stockChart) {
            stockChart.data = stockData;
            stockChart.update();
        } else {
            stockChart = new Chart(document.getElementById('stockChart').getContext('2d'), {
                type: 'pie',
                data: stockData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((context.raw / total) * 100);
                                    return `${context.label}: ${context.raw} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Actualizar o crear gráfico de valor por categoría
        if (valorChart) {
            valorChart.data = valorData;
            valorChart.update();
        } else {
            valorChart = new Chart(document.getElementById('valorChart').getContext('2d'), {
                type: 'bar',
                data: valorData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Valor: ${new Intl.NumberFormat('es-BO', {
                                        style: 'currency',
                                        currency: 'BOB'
                                    }).format(context.raw)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Valor Total (Bs)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('es-BO', {
                                        style: 'currency',
                                        currency: 'BOB'
                                    }).format(value);
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // Evento de búsqueda con debounce
    let timeoutId;
    $('#busqueda').on('input', function() {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => cargarInventario(1, false), 300);
    });

    // Eventos de filtros
    $('#aplicarFiltros').click(function() {
        cargarInventario(1, false);
        $('#filtrosModal').modal('hide');
    });

    $('#limpiarFiltros').click(function() {
        $('#id_tipo_categoria').val('');
        $('#id_categoria').val('');
        $('#estado_stock').val('');
        $('#busqueda').val('');
        cargarInventario(1, false);
        $('#filtrosModal').modal('hide');
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

    // Evento de paginación
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const pagina = $(this).data('pagina');
        cargarInventario(pagina, true);
    });

    // Cargar datos iniciales
    cargarInventario(1, true);

    // Mostrar mensajes de sesión con SweetAlert
    <?php if (isset($_SESSION['mensaje'])): ?>
        Swal.fire({
            title: '<?= $_SESSION["mensaje"]["title"] ?>',
            text: '<?= $_SESSION["mensaje"]["text"] ?>',
            icon: '<?= $_SESSION["mensaje"]["icon"] ?>',
            timer: 3000
        });
    <?php unset($_SESSION['mensaje']);
    endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        Swal.fire({
            title: '<?= $_SESSION["error"]["title"] ?>',
            text: '<?= $_SESSION["error"]["text"] ?>',
            icon: '<?= $_SESSION["error"]["icon"] ?>'
        });
    <?php unset($_SESSION['error']);
    endif; ?>
});
</script>
<!--main content end-->
