<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-star"></i> Productos Más Vendidos</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-file-text"></i><a href="<?= BASE_URL ?>index.php?action=reportes">Reportes</a></li>
                    <li><i class="fa fa-star"></i> Productos Más Vendidos</li>
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
                        <form class="form-inline" method="POST" action="<?= BASE_URL ?>index.php?action=reportes&method=productosMasVendidos">
                            <div class="form-group">
                                <label for="fecha_inicio">Desde:</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?= $filtros['fecha_inicio'] ?? '' ?>">
                            </div>
                            <div class="form-group">
                                <label for="fecha_fin">Hasta:</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?= $filtros['fecha_fin'] ?? '' ?>">
                            </div>
                            <div class="form-group">
                                <label for="id_categoria">Categoría:</label>
                                <select class="form-control" id="id_categoria" name="id_categoria">
                                    <option value="">Todas</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= $categoria['id'] ?>" <?= isset($filtros['id_categoria']) && $filtros['id_categoria'] == $categoria['id'] ? 'selected' : '' ?>>
                                            <?= $categoria['nombre'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
                            <a href="<?= BASE_URL ?>index.php?action=reportes&method=productosMasVendidos" class="btn btn-default">Limpiar</a>
                        </form>

                        <hr>

                        <div class="row">
                            <div class="col-md-8">
                                <table class="table table-striped table-advance table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Producto</th>
                                            <th>Categoría</th>
                                            <th>Cantidad Vendida</th>
                                            <th>Monto Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($reporte as $index => $producto): ?>
                                            <tr>
                                                <td><?= $index + 1; ?></td>
                                                <td><?= $producto['producto']; ?></td>
                                                <td><?= $producto['categoria']; ?></td>
                                                <td><?= $producto['total_vendido']; ?></td>
                                                <td><?= number_format($producto['monto_total'], 2, ',', '.'); ?> Bs</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Distribución de Ventas</h3>
                                    </div>
                                    <div class="panel-body">
                                        <canvas id="productosChart" height="300"></canvas>
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
    // Chart data
    const productos = <?= json_encode(array_map(function($item) { 
        return $item['producto']; 
    }, $reporte)) ?>;
    
    const cantidades = <?= json_encode(array_map(function($item) { 
        return $item['total_vendido']; 
    }, $reporte)) ?>;
    
    const colores = [
        'rgba(255, 99, 132, 0.7)',
        'rgba(54, 162, 235, 0.7)',
        'rgba(255, 206, 86, 0.7)',
        'rgba(75, 192, 192, 0.7)',
        'rgba(153, 102, 255, 0.7)',
        'rgba(255, 159, 64, 0.7)',
        'rgba(199, 199, 199, 0.7)',
        'rgba(83, 102, 255, 0.7)',
        'rgba(255, 99, 255, 0.7)',
        'rgba(99, 255, 132, 0.7)'
    ];
    
    // Best selling products chart
    const productosCtx = document.getElementById('productosChart').getContext('2d');
    const productosChart = new Chart(productosCtx, {
        type: 'pie',
        data: {
            labels: productos,
            datasets: [{
                data: cantidades,
                backgroundColor: colores
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
                    text: 'Porcentaje de Ventas'
                }
            }
        }
    });
});
</script>
<!--main content end-->
