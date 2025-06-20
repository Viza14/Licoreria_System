<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-file-text"></i> Reportes</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-file-text"></i> Reportes</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Seleccione el tipo de reporte
                    </header>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="panel panel-info">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"><i class="fa fa-cubes"></i> Reporte de Inventario</h3>
                                    </div>
                                    <div class="panel-body">
                                        <p>Reporte detallado del inventario actual con niveles de stock y valores.</p>
                                        <a href="<?= BASE_URL ?>index.php?action=reportes&method=inventario" class="btn btn-info">
                                            <i class="fa fa-eye"></i> Ver Reporte
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="panel panel-success">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"><i class="fa fa-shopping-cart"></i> Reporte de Ventas</h3>
                                    </div>
                                    <div class="panel-body">
                                        <p>Reporte de ventas realizadas con filtros por fecha, vendedor y cliente.</p>
                                        <a href="<?= BASE_URL ?>index.php?action=reportes&method=ventas" class="btn btn-success">
                                            <i class="fa fa-eye"></i> Ver Reporte
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="panel panel-warning">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"><i class="fa fa-list-alt"></i> Detalle de Ventas</h3>
                                    </div>
                                    <div class="panel-body">
                                        <p>Reporte detallado de productos vendidos con filtros por producto y fecha.</p>
                                        <a href="<?= BASE_URL ?>index.php?action=reportes&method=detalleVentas" class="btn btn-warning">
                                            <i class="fa fa-eye"></i> Ver Reporte
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"><i class="fa fa-line-chart"></i> Resumen de Ventas</h3>
                                    </div>
                                    <div class="panel-body">
                                        <p>Resumen mensual de ventas con totales, promedios y extremos.</p>
                                        <a href="<?= BASE_URL ?>index.php?action=reportes&method=resumenVentas" class="btn btn-primary">
                                            <i class="fa fa-eye"></i> Ver Reporte
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="panel panel-danger">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"><i class="fa fa-star"></i> Productos Más Vendidos</h3>
                                    </div>
                                    <div class="panel-body">
                                        <p>Top 10 de productos más vendidos con filtros por categoría y fecha.</p>
                                        <a href="<?= BASE_URL ?>index.php?action=reportes&method=productosMasVendidos" class="btn btn-danger">
                                            <i class="fa fa-eye"></i> Ver Reporte
                                        </a>
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
<!--main content end-->