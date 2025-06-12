<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-sign-in"></i> Entrada de Productos</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-beer"></i><a href="<?= BASE_URL ?>index.php?action=productos">Productos</a></li>
                    <li><i class="fa fa-sign-in"></i> Entrada</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Registrar Entrada de Producto
                    </header>
                    <div class="panel-body">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <strong><?= $_SESSION['error']['title'] ?></strong> <?= $_SESSION['error']['text'] ?>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>

                        <form class="form-horizontal" method="POST" action="<?= BASE_URL ?>index.php?action=productos&method=registrarEntrada">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Producto *</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="id_producto" required>
                                        <option value="">Seleccione un producto</option>
                                        <?php foreach ($productos as $producto): ?>
                                            <option value="<?= $producto['id'] ?>" <?= isset($_POST['id_producto']) && $_POST['id_producto'] == $producto['id'] ? 'selected' : '' ?>>
                                                <?= $producto['descripcion'] ?> (Stock: <?= $producto['cantidad'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Cantidad *</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control" name="cantidad" min="1" 
                                           value="<?= isset($_POST['cantidad']) ? $_POST['cantidad'] : '' ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Precio de Compra *</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <span class="input-group-addon">Bs</span>
                                        <input type="number" step="0.01" class="form-control" name="precio_compra" min="0" 
                                               value="<?= isset($_POST['precio_compra']) ? $_POST['precio_compra'] : '' ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Proveedor *</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="cedula_proveedor" required>
                                        <option value="">Seleccione un proveedor</option>
                                        <?php foreach ($proveedores as $proveedor): ?>
                                            <option value="<?= $proveedor['cedula'] ?>" <?= isset($_POST['cedula_proveedor']) && $_POST['cedula_proveedor'] == $proveedor['cedula'] ? 'selected' : '' ?>>
                                                <?= $proveedor['nombre'] ?> (<?= $proveedor['cedula'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Observaciones</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="observaciones" rows="3"><?= isset($_POST['observaciones']) ? $_POST['observaciones'] : '' ?></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Registrar Entrada</button>
                                    <a href="<?= BASE_URL ?>index.php?action=productos" class="btn btn-default"><i class="fa fa-times"></i> Cancelar</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <small class="text-muted">* Campos obligatorios</small>
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