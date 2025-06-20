<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-line-chart"></i> Resumen de Ventas</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-file-text"></i><a href="<?= BASE_URL ?>index.php?action=reportes">Reportes</a></li>
                    <li><i class="fa fa-line-chart"></i> Resumen Ventas</li>
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
                        <form class="form-inline" method="POST" action="<?= BASE_URL ?>index.php?action=reportes&method=resumenVentas">
                            <div class="form-group">
                                <label for="fecha_inicio">Desde:</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?= $filtros['fecha_inicio'] ?? '' ?>">
                            </div>
                            <div class="form-group">
                                <label for="fecha_fin">Hasta:</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?= $filtros['fecha_fin'] ?? '' ?>">
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
                            <a href="<?= BASE_URL ?>index.php?action=reportes&method=resumenVentas" class="btn btn-default">Limpiar</a>
                        </form>

                        <hr>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Resumen Mensual de Ventas</h3>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-striped table-advance table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Mes</th>
                                                    <th>Total Ventas</th>
                                                    <th>Monto Total</th>
                                                    <th>Promedio por Venta</th>
                                                    <th>Venta Máxima</th>
                                                    <th>Venta Mínima</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($reporte as $resumen): ?>
                                                    <tr>
                                                        <td><?= date('F Y', strtotime($resumen['mes'] . '-01')); ?></td>
                                                        <td><?= $resumen['total_ventas']; ?></td>
                                                        <td><?= number_format($resumen['monto_total'], 2, ',', '.'); ?> Bs</td>
                                                        <td><?= number_format($resumen['promedio_venta'], 2, ',', '.'); ?> Bs</td>
                                                        <td><?= number_format($resumen['venta_maxima'], 2, ',', '.'); ?> Bs</td>
                                                        <td><?= number_format($resumen['venta_minima'], 2, ',', '.'); ?> Bs</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Evolución de Ventas</h3>
                                    </div>
                                    <div class="panel-body">
                                        <canvas id="ventasChart" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Distribución de Ventas</h3>
                                    </div>
                                    <div class="panel-body">
                                        <canvas id="distribucionChart" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Data for charts
    const meses = <?= json_encode(array_map(function($item) { 
        return date('M Y', strtotime($item['mes'] . '-01')); 
    }, $reporte)) ?>;
    
    const montos = <?= json_encode(array_map(function($item) { 
        return $item['monto_total']; 
    }, $reporte)) ?>;
    
    const ventas = <?= json_encode(array_map(function($item) { 
        return $item['total_ventas']; 
    }, $reporte)) ?>;
    
    // Sales evolution chart
    const ventasCtx = document.getElementById('ventasChart').getContext('2d');
    const ventasChart = new Chart(ventasCtx, {
        type: 'line',
        data: {
            labels: meses,
            datasets: [
                {
                    label: 'Monto Total (Bs)',
                    data: montos,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    yAxisID: 'y'
                },
                {
                    label: 'Número de Ventas',
                    data: ventas,
                    borderColor: 'rgba(153, 102, 255, 1)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    yAxisID: 'y1',
                    type: 'bar'
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Monto (Bs)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Número de Ventas'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
    
    // Distribution chart
    const distribucionCtx = document.getElementById('distribucionChart').getContext('2d');
    const distribucionChart = new Chart(distribucionCtx, {
        type: 'doughnut',
        data: {
            labels: meses,
            datasets: [{
                data: montos,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                },
                title: {
                    display: true,
                    text: 'Distribución por Mes'
                }
            }
        }
    });
});
</script>
<!--main content end-->
