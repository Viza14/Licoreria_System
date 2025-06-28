<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-exclamation-triangle"></i> Alertas de Stock</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?php echo BASE_URL; ?>"><i class="fa fa-home"></i> Inicio</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?php echo BASE_URL; ?>index.php?action=gestion-stock"><i class="fa fa-cubes"></i> Gestión de Stock</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="fa fa-exclamation-triangle"></i> Alertas
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <section class="panel">
                    <header class="panel-heading red-bg">
                        <i class="fa fa-exclamation-circle"></i> Productos con Stock Bajo
                    </header>
                    <div class="panel-body">
                        <?php if (empty($stockBajo)): ?>
                            <div class="alert alert-success">
                                <i class="fa fa-check-circle"></i> No hay productos con stock bajo.
                            </div>
                        <?php else: ?>
                            <table class="table table-striped table-advance table-hover">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Stock Actual</th>
                                        <th>Mínimo</th>
                                        <th>Diferencia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stockBajo as $producto): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($producto['descripcion']) ?></td>
                                            <td class="text-center"><?= $producto['cantidad'] ?></td>
                                            <td class="text-center"><?= $producto['stock_minimo'] ?></td>
                                            <td class="text-center">
                                                <span class="label label-danger">
                                                    <?= $producto['cantidad'] - $producto['stock_minimo'] ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
            
            <div class="col-lg-6">
                <section class="panel">
                    <header class="panel-heading yellow-bg">
                        <i class="fa fa-exclamation-triangle"></i> Productos con Stock Alto
                    </header>
                    <div class="panel-body">
                        <?php if (empty($stockAlto)): ?>
                            <div class="alert alert-success">
                                <i class="fa fa-check-circle"></i> No hay productos con stock alto.
                            </div>
                        <?php else: ?>
                            <table class="table table-striped table-advance table-hover">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Stock Actual</th>
                                        <th>Máximo</th>
                                        <th>Diferencia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stockAlto as $producto): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($producto['descripcion']) ?></td>
                                            <td class="text-center"><?= $producto['cantidad'] ?></td>
                                            <td class="text-center"><?= $producto['stock_maximo'] ?></td>
                                            <td class="text-center">
                                                <span class="label label-warning">
                                                    <?= $producto['cantidad'] - $producto['stock_maximo'] ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-lg-12">
                <a href="<?php echo BASE_URL; ?>index.php?action=gestion-stock" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Volver a Gestión de Stock
                </a>
            </div>
        </div>
    </section>
</section>

<style>
.red-bg {
    background-color: #d9534f;
    color: white;
}
.yellow-bg {
    background-color: #f0ad4e;
    color: white;
}
.breadcrumb-item + .breadcrumb-item::before {
    content: ">";
}
</style>
