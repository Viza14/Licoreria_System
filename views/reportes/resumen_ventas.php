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
                        Resumen de Ventas
                        <div class="pull-right">
                            <div class="btn-group" style="margin-right: 10px;">
                                <a href="<?= BASE_URL ?>index.php?action=reportes&method=resumenVentas&periodo=mensual" 
                                   class="btn btn-xs <?= ($periodo ?? 'mensual') == 'mensual' ? 'btn-primary' : 'btn-default' ?>">
                                    <i class="fa fa-calendar"></i> Mensual
                                </a>
                                <a href="<?= BASE_URL ?>index.php?action=reportes&method=resumenVentas&periodo=semanal" 
                                   class="btn btn-xs <?= ($periodo ?? 'mensual') == 'semanal' ? 'btn-primary' : 'btn-default' ?>">
                                    <i class="fa fa-calendar-o"></i> Semanal
                                </a>
                                <a href="<?= BASE_URL ?>index.php?action=reportes&method=resumenVentas&periodo=diario" 
                                   class="btn btn-xs <?= ($periodo ?? 'mensual') == 'diario' ? 'btn-primary' : 'btn-default' ?>">
                                    <i class="fa fa-calendar-check-o"></i> Diario
                                </a>
                            </div>
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
                                        <h3 class="panel-title">
                                            <?php 
                                            switch($periodo ?? 'mensual') {
                                                case 'semanal':
                                                    echo 'Resumen Semanal de Ventas';
                                                    break;
                                                case 'diario':
                                                    echo 'Resumen Diario de Ventas';
                                                    break;
                                                default:
                                                    echo 'Resumen Mensual de Ventas';
                                                    break;
                                            }
                                            ?>
                                        </h3>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-striped table-advance table-hover">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <?php 
                                                        switch($periodo ?? 'mensual') {
                                                            case 'semanal':
                                                                echo 'Semana';
                                                                break;
                                                            case 'diario':
                                                                echo 'Fecha';
                                                                break;
                                                            default:
                                                                echo 'Mes';
                                                                break;
                                                        }
                                                        ?>
                                                    </th>
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
                                                        <td>
                                                            <?php 
                                                            switch($periodo ?? 'mensual') {
                                                                case 'semanal':
                                                                    echo date('d/m/Y', strtotime($resumen['inicio_semana'])) . ' - ' . date('d/m/Y', strtotime($resumen['fin_semana']));
                                                                    break;
                                                                case 'diario':
                                                                    $dias_semana = [
                                                                        'Monday' => 'Lunes',
                                                                        'Tuesday' => 'Martes',
                                                                        'Wednesday' => 'Miércoles',
                                                                        'Thursday' => 'Jueves',
                                                                        'Friday' => 'Viernes',
                                                                        'Saturday' => 'Sábado',
                                                                        'Sunday' => 'Domingo'
                                                                    ];
                                                                    echo $dias_semana[$resumen['dia_semana']] . ', ' . date('d/m/Y', strtotime($resumen['fecha']));
                                                                    break;
                                                                default:
                                                                    echo date('F Y', strtotime($resumen['mes'] . '-01'));
                                                                    break;
                                                            }
                                                            ?>
                                                        </td>
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
                                        <!-- Explicación visual -->
                                        <div class="alert alert-info" style="margin-bottom: 15px; padding: 10px; border-radius: 8px;">
                                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <div style="width: 20px; height: 15px; background-color: rgba(54, 162, 235, 0.8); border: 2px solid rgba(54, 162, 235, 1); border-radius: 2px;"></div>
                                                    <span style="font-weight: bold; color: #2c3e50; font-size: 12px;">💰 DINERO GANADO</span>
                                                </div>
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <div style="width: 20px; height: 4px; background-color: rgba(255, 140, 0, 1); border-radius: 2px; position: relative;">
                                                        <div style="width: 8px; height: 8px; background-color: rgba(255, 140, 0, 1); border: 2px solid #fff; border-radius: 50%; position: absolute; top: -4px; left: 6px;"></div>
                                                    </div>
                                                    <span style="font-weight: bold; color: #2c3e50; font-size: 12px;">🛒 CANTIDAD VENDIDA</span>
                                                </div>
                                            </div>
                                            <div style="margin-top: 8px; font-size: 11px; color: #5a6c7d; text-align: center;">
                                                <strong>📊 Cómo leer el gráfico:</strong> Las barras azules muestran cuánto dinero ganaste. La línea naranja muestra cuántas ventas hiciste.
                                            </div>
                                        </div>
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
                        <label>Período:</label>
                        <select class="form-control" name="periodo">
                            <option value="mensual" <?= ($periodo ?? 'mensual') == 'mensual' ? 'selected' : '' ?>>Mensual</option>
                            <option value="semanal" <?= ($periodo ?? 'mensual') == 'semanal' ? 'selected' : '' ?>>Semanal</option>
                            <option value="diario" <?= ($periodo ?? 'mensual') == 'diario' ? 'selected' : '' ?>>Diario</option>
                        </select>
                    </div>
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
    <?php 
    $periodo_actual = $periodo ?? 'mensual';
    $labels = [];
    
    foreach (array_reverse($reporte) as $item) {
        switch($periodo_actual) {
            case 'semanal':
                $labels[] = date('d/m', strtotime($item['inicio_semana'])) . ' - ' . date('d/m', strtotime($item['fin_semana']));
                break;
            case 'diario':
                $labels[] = date('d/m/Y', strtotime($item['fecha']));
                break;
            default:
                $labels[] = date('M Y', strtotime($item['mes'] . '-01'));
                break;
        }
    }
    ?>
    
    const labels = <?= json_encode($labels) ?>;
    
    const montos = <?= json_encode(array_map(function($item) { 
        return $item['monto_total']; 
    }, array_reverse($reporte))) ?>;
    
    const ventas = <?= json_encode(array_map(function($item) { 
        return $item['total_ventas']; 
    }, array_reverse($reporte))) ?>;
    
    // Sales evolution chart - Enhanced with multiple datasets
    const ventasCtx = document.getElementById('ventasChart').getContext('2d');
    const ventasChart = new Chart(ventasCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: '💰 INGRESOS TOTALES (Bolívares)',
                    data: montos,
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    yAxisID: 'y',
                    order: 2
                },
                {
                    label: '🛒 NÚMERO DE VENTAS REALIZADAS',
                    data: ventas,
                    type: 'line',
                    backgroundColor: 'rgba(255, 140, 0, 0.3)',
                    borderColor: 'rgba(255, 140, 0, 1)',
                    borderWidth: 4,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: 'rgba(255, 140, 0, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3,
                    pointRadius: 8,
                    pointHoverRadius: 12,
                    yAxisID: 'y1',
                    order: 1
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Evolución <?= ucfirst($periodo ?? 'mensual') ?> de Ventas',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: 20
                },
                legend: {
                    display: true,
                    position: 'top',
                    align: 'center',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'rect',
                        padding: 25,
                        boxWidth: 20,
                        boxHeight: 15,
                        font: {
                            size: 13,
                            weight: 'bold',
                            family: 'Arial, sans-serif'
                        },
                        color: '#2c3e50',
                        generateLabels: function(chart) {
                            const original = Chart.defaults.plugins.legend.labels.generateLabels;
                            const labels = original.call(this, chart);
                            
                            labels.forEach((label, index) => {
                                if (index === 0) {
                                    // Barras azules para ingresos
                                    label.fillStyle = 'rgba(54, 162, 235, 0.8)';
                                    label.strokeStyle = 'rgba(54, 162, 235, 1)';
                                    label.pointStyle = 'rect';
                                    label.text = '💰 DINERO GANADO (Barras Azules)';
                                } else {
                                    // Línea naranja para cantidad
                                    label.fillStyle = 'rgba(255, 140, 0, 1)';
                                    label.strokeStyle = 'rgba(255, 140, 0, 1)';
                                    label.pointStyle = 'line';
                                    label.text = '🛒 CANTIDAD VENDIDA (Línea Naranja)';
                                }
                            });
                            
                            return labels;
                        }
                    },
                    onHover: function(event, legendItem, legend) {
                        legend.chart.canvas.style.cursor = 'pointer';
                        // Mostrar tooltip explicativo
                        const tooltip = document.getElementById('legend-tooltip');
                        if (!tooltip) {
                            const div = document.createElement('div');
                            div.id = 'legend-tooltip';
                            div.style.cssText = `
                                position: absolute;
                                background: rgba(0,0,0,0.9);
                                color: white;
                                padding: 10px;
                                border-radius: 5px;
                                font-size: 12px;
                                z-index: 1000;
                                pointer-events: none;
                                max-width: 250px;
                            `;
                            document.body.appendChild(div);
                        }
                        const tooltipDiv = document.getElementById('legend-tooltip');
                        if (legendItem.datasetIndex === 0) {
                            tooltipDiv.innerHTML = '💰 <strong>DINERO GANADO:</strong><br>Muestra cuánto dinero se obtuvo en cada período (en bolívares)';
                        } else {
                            tooltipDiv.innerHTML = '🛒 <strong>CANTIDAD VENDIDA:</strong><br>Muestra cuántas ventas se realizaron en cada período';
                        }
                        tooltipDiv.style.left = event.native.pageX + 10 + 'px';
                        tooltipDiv.style.top = event.native.pageY - 10 + 'px';
                        tooltipDiv.style.display = 'block';
                    },
                    onLeave: function(event, legendItem, legend) {
                        legend.chart.canvas.style.cursor = 'default';
                        const tooltip = document.getElementById('legend-tooltip');
                        if (tooltip) {
                            tooltip.style.display = 'none';
                        }
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        title: function(context) {
                            return 'Período: ' + context[0].label;
                        },
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                if (context.datasetIndex === 0) {
                                    label += new Intl.NumberFormat('es-BO', {
                                        style: 'currency',
                                        currency: 'BOB'
                                    }).format(context.parsed.y);
                                } else {
                                    label += context.parsed.y + ' ventas';
                                }
                            }
                            return label;
                        },
                        afterBody: function(context) {
                            if (context.length > 1) {
                                const monto = context[0].parsed.y;
                                const cantidad = context[1].parsed.y;
                                const promedio = cantidad > 0 ? monto / cantidad : 0;
                                return ['', '📈 Promedio por venta: ' + new Intl.NumberFormat('es-BO', {
                                    style: 'currency',
                                    currency: 'BOB'
                                }).format(promedio)];
                            }
                            return [];
                        }
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: '💰 DINERO GANADO (Bolívares)',
                        font: {
                            size: 14,
                            weight: 'bold'
                        },
                        color: 'rgba(54, 162, 235, 1)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Bs ' + value.toLocaleString('es-BO');
                        },
                        color: 'rgba(54, 162, 235, 0.8)',
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        color: 'rgba(54, 162, 235, 0.1)',
                        drawOnChartArea: true,
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: '🛒 CANTIDAD DE VENTAS',
                        font: {
                            size: 14,
                            weight: 'bold'
                        },
                        color: 'rgba(255, 140, 0, 1)'
                    },
                    ticks: {
                        callback: function(value) {
                            return value + ' ventas';
                        },
                        color: 'rgba(255, 140, 0, 0.8)',
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        drawOnChartArea: false,
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: '📅 Período',
                        font: {
                            size: 12,
                            weight: 'bold'
                        },
                        color: '#333'
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 0,
                        font: {
                            size: 10
                        }
                    },
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
            labels: labels,
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
                    text: 'Distribución por <?= ucfirst($periodo ?? 'mensual') == 'Mensual' ? 'Mes' : ($periodo == 'semanal' ? 'Semana' : 'Día') ?>'
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

