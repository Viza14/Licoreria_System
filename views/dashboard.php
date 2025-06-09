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
                        <h1 class="count"><?php echo $data['totalProductos']; ?></h1>
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
                        <h1 class="count2"><?php echo $data['totalClientes']; ?></h1>
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
                        <h1 class="count3"><?php echo $data['ventasHoy']; ?></h1>
                        <p>Ventas Hoy</p>
                        <a href="<?php echo BASE_URL; ?>index.php?action=ventas" class="btn btn-warning btn-sm">Registrar</a>
                    </div>
                </section>
            </div>
            
            <!-- Widget Ingresos -->
            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol blue">
                        <i class="fa fa-money"></i>
                    </div>
                    <div class="value">
                        <h1>$<span class="count4"><?php echo $data['ingresosHoy']; ?></span></h1>
                        <p>Ingresos Hoy</p>
                        <a href="<?php echo BASE_URL; ?>index.php?action=reportes" class="btn btn-info btn-sm">Ver reporte</a>
                    </div>
                </section>
            </div>
        </div>
        
        <!-- Gráficos y estadísticas -->
        <div class="row">
            <!-- Gráfico de ventas -->
            <div class="col-lg-8">
                <section class="panel">
                    <header class="panel-heading">
                        Ventas Mensuales
                    </header>
                    <div class="panel-body">
                        <canvas id="sales-chart" height="300" width="600"></canvas>
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
                            <?php foreach($data['topProductos'] as $producto): ?>
                            <li class="list-group-item">
                                <span class="badge bg-theme"><?php echo $producto['ventas']; ?></span>
                                <?php echo $producto['nombre']; ?>
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
                                <?php foreach($data['ultimasVentas'] as $venta): ?>
                                <tr>
                                    <td><?php echo $venta['id']; ?></td>
                                    <td><?php echo $venta['cliente']; ?></td>
                                    <td><?php echo $venta['fecha']; ?></td>
                                    <td>$<?php echo number_format($venta['total'], 2); ?></td>
                                    <td>
                                        <a href="#" class="btn btn-success btn-xs"><i class="fa fa-eye"></i></a>
                                        <a href="#" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></a>
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