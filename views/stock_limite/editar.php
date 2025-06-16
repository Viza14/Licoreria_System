<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-edit"></i> <?= isset($limite) ? 'Editar' : 'Configurar' ?> Límites de Stock</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="index.php">Inicio</a></li>
                    <li><i class="fa fa-cubes"></i><a href="index.php?action=gestion-stock">Gestión de Stock</a></li>
                    <li><i class="fa fa-edit"></i> <?= isset($limite) ? 'Editar' : 'Configurar' ?> Límites</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Configuración de Límites para: <?= htmlspecialchars($producto['descripcion']) ?>
                    </header>
                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="index.php?action=gestion-stock&method=guardar">
                            <input type="hidden" name="id_producto" value="<?= $producto['id'] ?>">
                            
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Stock Actual</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="<?= $producto['cantidad'] ?>" readonly>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="stock_minimo">Stock Mínimo</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" 
                                           value="<?= $limite['stock_minimo'] ?? 0 ?>" min="0" required>
                                    <span class="help-block">El sistema alertará cuando el stock esté por debajo de este valor.</span>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="stock_maximo">Stock Máximo</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control" id="stock_maximo" name="stock_maximo" 
                                           value="<?= $limite['stock_maximo'] ?? 0 ?>" min="0" required>
                                    <span class="help-block">El sistema alertará cuando el stock supere este valor.</span>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Guardar
                                    </button>
                                    <a href="index.php?action=gestion-stock" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Volver
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->
