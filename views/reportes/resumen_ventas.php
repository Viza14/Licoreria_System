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
                        Listado de Ventas
                        <div class="pull-right">
                            <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#filtrosModal">
                                <i class="fa fa-filter"></i> Filtros
                            </button>
                        </div>
                    </header>
                    <div class="panel-body">
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

<!-- Modal de Filtros -->
<div class="modal fade" id="filtrosModal" tabindex="-1" role="dialog" aria-labelledby="filtrosModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="filtrosModalLabel"><i class="fa fa-filter"></i> Filtros Avanzados</h4>
            </div>
            <div class="modal-body">
                <form id="formFiltros" method="POST" action="<?= BASE_URL ?>index.php?action=reportes&method=resumenVentas">
                    <div class="form-group">
                        <label>Fecha Inicio:</label>
                        <input type="date" class="form-control" name="fecha_inicio" value="<?= $filtros['fecha_inicio'] ?? '' ?>">
                    </div>
                    <div class="form-group">
                        <label>Fecha Fin:</label>
                        <input type="date" class="form-control" name="fecha_fin" value="<?= $filtros['fecha_fin'] ?? '' ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                        <a href="<?= BASE_URL ?>index.php?action=reportes&method=resumenVentas" class="btn btn-link">Limpiar Filtros</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Data for charts - Reverse arrays to show most recent data on the right
    const meses = <?= json_encode(array_map(function($item) { 
        return date('M Y', strtotime($item['mes'] . '-01')); 
    }, array_reverse($reporte))) ?>;
    
    const montos = <?= json_encode(array_map(function($item) { 
        return $item['monto_total']; 
    }, array_reverse($reporte))) ?>;
    
    const ventas = <?= json_encode(array_map(function($item) { 
        return $item['total_ventas']; 
    }, array_reverse($reporte))) ?>;
    
    // Sales evolution chart - Trading style
    const ventasCtx = document.getElementById('ventasChart').getContext('2d');
    const ventasChart = new Chart(ventasCtx, {
        type: 'bar',
        data: {
            labels: meses,
            datasets: [
                {
                    label: 'Monto Total (Bs)',
                    data: montos,
                    backgroundColor: montos.map((value, index) => {
                        if (index === 0) return 'rgba(75, 192, 192, 0.6)';
                        return montos[index] >= montos[index - 1] ? 
                            'rgba(75, 192, 192, 0.6)' : 'rgba(255, 99, 132, 0.6)';
                    }),
                    borderColor: montos.map((value, index) => {
                        if (index === 0) return 'rgba(75, 192, 192, 1)';
                        return montos[index] >= montos[index - 1] ? 
                            'rgba(75, 192, 192, 1)' : 'rgba(255, 99, 132, 1)';
                    }),
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Evolución Mensual de Ventas',
                    font: {
                        size: 16
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('es-BO', {
                                    style: 'currency',
                                    currency: 'BOB'
                                }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Monto en Bolivianos (Bs)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Bs ' + value.toLocaleString('es-BO');
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
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
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            const value = context.parsed;
                            label += new Intl.NumberFormat('es-BO', {
                                style: 'currency',
                                currency: 'BOB'
                            }).format(value);
                            return label;
                        }
                    }
                }
            }
        }
    });
});
</script>
<!--main content end-->
