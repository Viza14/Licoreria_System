<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-plus-circle"></i> Crear Producto</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-beer"></i><a href="<?php echo BASE_URL; ?>index.php?action=productos">Productos</a></li>
                    <li><i class="fa fa-plus-circle"></i> Crear</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Formulario de Registro
                    </header>
                    <div class="panel-body">
                        <form class="form-horizontal" action="<?= BASE_URL ?>index.php?action=productos&method=guardar" method="POST" id="formProducto">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Descripción</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="descripcion" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Cantidad</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control" name="cantidad" min="0" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Precio</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <span class="input-group-addon">Bs</span>
                                        <input type="number" step="0.01" class="form-control" name="precio" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Tipo de Categoría</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="tipoCategoria" required>
                                        <option value="">Seleccione un tipo</option>
                                        <?php foreach ($tiposCategoria as $tipo): ?>
                                            <option value="<?= $tipo['id'] ?>"><?= $tipo['nombre'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Categoría</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="id_categoria" id="categoria" required>
                                        <option value="">Seleccione una categoría</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?= $categoria['id'] ?>" data-tipo="<?= $categoria['id_tipo_categoria'] ?>">
                                                <?= $categoria['nombre'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Estatus</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="id_estatus" required>
                                        <?php foreach ($estatus as $est): ?>
                                            <option value="<?= $est['id'] ?>"><?= $est['nombre'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar</button>
                                    <a href="<?= BASE_URL ?>index.php?action=productos" class="btn btn-default"><i class="fa fa-times"></i> Cancelar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>

<!-- Incluir SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Filtrar categorías por tipo seleccionado
        $('#tipoCategoria').change(function() {
            const tipoId = $(this).val();
            $('#categoria option').each(function() {
                const $option = $(this);
                if ($option.data('tipo') == tipoId || $option.val() == '') {
                    $option.show();
                } else {
                    $option.hide();
                }
            });
            $('#categoria').val('');
        });

        // Validar antes de enviar el formulario
        $('#formProducto').submit(function(e) {
            const descripcion = $('input[name="descripcion"]').val().trim();
            const cantidad = $('input[name="cantidad"]').val();
            const precio = $('input[name="precio"]').val();
            const categoria = $('select[name="id_categoria"]').val();

            if (descripcion === '') {
                e.preventDefault();
                Swal.fire({
                    title: 'Error',
                    text: 'La descripción del producto es requerida',
                    icon: 'error'
                });
                return false;
            }

            if (cantidad < 0) {
                e.preventDefault();
                Swal.fire({
                    title: 'Error',
                    text: 'La cantidad no puede ser negativa',
                    icon: 'error'
                });
                return false;
            }

            if (precio <= 0) {
                e.preventDefault();
                Swal.fire({
                    title: 'Error',
                    text: 'El precio debe ser mayor que cero',
                    icon: 'error'
                });
                return false;
            }

            if (categoria === '') {
                e.preventDefault();
                Swal.fire({
                    title: 'Error',
                    text: 'Debe seleccionar una categoría',
                    icon: 'error'
                });
                return false;
            }
        });
    });

    // Mostrar mensajes de SweetAlert
    <?php if (isset($_SESSION['mensaje'])): ?>
        Swal.fire({
            title: '<?= $_SESSION['mensaje']['title'] ?>',
            text: '<?= $_SESSION['mensaje']['text'] ?>',
            icon: '<?= $_SESSION['mensaje']['icon'] ?>',
            timer: 3000,
            timerProgressBar: true
        });
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        Swal.fire({
            title: '<?= $_SESSION['error']['title'] ?>',
            text: '<?= $_SESSION['error']['text'] ?>',
            icon: '<?= $_SESSION['error']['icon'] ?>'
        });
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    // Rellenar formulario si hay datos en sesión
    <?php if (isset($_SESSION['form_data'])): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const formData = <?= json_encode($_SESSION['form_data']); ?>;
            for (const key in formData) {
                const element = document.querySelector(`[name="${key}"]`);
                if (element) {
                    element.value = formData[key];
                }
            }
            <?php unset($_SESSION['form_data']); ?>
        });
    <?php endif; ?>
</script>
<!--main content end-->