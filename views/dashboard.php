<?php
include 'views/layouts/header.php';
include 'views/layouts/sidebar.php';
?>

<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-dashboard"></i> Dashboard</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-dashboard"></i> Dashboard</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row state-overview">
            <!-- Widget Productos -->
            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol terques">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                    <div class="value">
                        <h1><?php echo $data['totalProductos']; ?></h1>
                        <p>Productos</p>
                        <a href="<?php echo BASE_URL; ?>index.php?action=productos" class="btn btn-primary btn-sm">Ver todos</a>
                    </div>
                </section>
            </div>

            <!-- Widget Clientes -->
            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol red">
                        <i class="fa fa-users"></i>
                    </div>
                    <div class="value">
                        <h1><?php echo $data['totalClientes']; ?></h1>
                        <p>Clientes</p>
                        <a href="<?php echo BASE_URL; ?>index.php?action=clientes" class="btn btn-danger btn-sm">Administrar</a>
                    </div>
                </section>
            </div>

            <!-- Widget Ventas -->
            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol yellow">
                        <i class="fa fa-bar-chart-o"></i>
                    </div>
                    <div class="value">
                        <h1><?php echo $data['ventasHoy']; ?></h1>
                        <p>Ventas Hoy</p>
                        <a href="<?= BASE_URL ?>index.php?action=ventas&method=crear" class="btn btn-warning btn-sm">Registrar</a>
                    </div>
                </section>
            </div>

            <?php if ($_SESSION['user_rol'] != 2): ?> <!-- Asumiendo que 2 es el rol de empleado -->

                <!-- Widget Ingresos -->
                <div class="col-lg-3 col-sm-6">
                    <section class="panel">
                        <div class="symbol blue">
                            <i class="fa fa-money"></i>
                        </div>
                        <div class="value">
                            <h1>Bs. <?php echo number_format($data['ingresosHoy'], 2, ',', '.'); ?></h1>
                            <p>Ingresos Hoy</p>
                            <a href="<?php echo BASE_URL; ?>index.php?action=reportes" class="btn btn-info btn-sm">Ver reporte</a>
                        </div>
                    </section>
                </div>
            <?php endif; ?>

        </div>

        <!-- Gráficos y estadísticas -->
        <div class="row">
            <!-- Gráfico de Productos Vendidos por Día -->
            <div class="col-lg-8">
                <section class="panel">
                    <header class="panel-heading">
                        Productos Vendidos por Día de la Semana
                        <span class="pull-right"><?php echo $mesActual; ?></span>
                    </header>
                    <div class="panel-body">
                        <div class="custom-bar-chart">
                            <ul class="y-axis">
                                <?php
                                // Calcular el máximo para la escala del eje Y
                                $maxProductos = !empty($data['productosPorDia']) ? max($data['productosPorDia']) : 10;
                                $steps = 5;
                                $stepSize = $maxProductos / $steps;

                                for ($i = $steps; $i >= 0; $i--):
                                    $value = $i * $stepSize;
                                ?>
                                    <li><span><?php echo number_format($value, 0); ?></span></li>
                                <?php endfor; ?>
                            </ul>
                            <?php
                            // Días de la semana en orden con números
                            foreach ($data['productosPorDia'] as $dia => $cantidad):
                                // Calcular altura en porcentaje (mínimo 1% para que sea visible)
                                $height = ($maxProductos > 0) ? max(($cantidad / $maxProductos) * 100, 1) : 1;

                                // Determinar color según la cantidad
                                if ($cantidad >= ($maxProductos * 0.8)) {
                                    $color = 'bg-success';
                                } elseif ($cantidad >= ($maxProductos * 0.5)) {
                                    $color = 'bg-warning';
                                } else {
                                    $color = 'bg-danger';
                                }
                            ?>
                                <div class="bar">
                                    <div class="title"><?php echo $dia; ?></div>
                                    <div class="value tooltips <?php echo $color; ?>"
                                        data-original-title="<?php echo $cantidad; ?> productos"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        style="height: 0%;"
                                        data-height="<?php echo $height; ?>%">
                                        <?php echo $cantidad; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Productos más vendidos -->
            <div class="col-lg-4">
                <section class="panel">
                    <header class="panel-heading">
                        Productos Más Vendidos
                    </header>
                    <div class="panel-body">
                        <ul class="list-group">
                            <?php foreach ($data['topProductos'] as $producto): ?>
                                <li class="list-group-item">
                                    <span class="badge bg-theme"><?php echo $producto['total_vendido']; ?></span>
                                    <?php echo $producto['producto']; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </section>
            </div>
        </div>

        <!-- Últimas ventas -->
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Últimas Ventas
                    </header>
                    <div class="panel-body">
                        <table class="table table-striped table-advance table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['ultimasVentas'] as $venta): ?>
                                    <tr>
                                        <td><?php echo $venta['id'] ?? 'N/A'; ?></td>
                                        <td><?php echo $venta['cliente'] ?? 'N/A'; ?></td>
                                        <td><?php 
                                            if(isset($venta['fecha'])) {
                                                $fecha = new DateTime($venta['fecha']);
                                                echo $fecha->format('d/m/y h:i ') . $fecha->format('A');
                                            } else {
                                                echo 'N/A';
                                            }
                                        ?></td>
                                        <td>Bs. <?php echo isset($venta['monto_total']) ? number_format($venta['monto_total'], 2, ',', '.') : '0,00'; ?></td>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>index.php?action=ventas&method=mostrar&id=<?php echo $venta['id']; ?>" class="btn btn-success btn-xs"><i class="fa fa-eye"></i></a>
                                            <a href="<?php echo BASE_URL; ?>index.php?action=ventas&method=imprimir&id=<?php echo $venta['id']; ?>" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></a>
                                        </td>
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
<!--main content end-->