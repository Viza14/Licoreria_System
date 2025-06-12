<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-plus-circle"></i> Crear Tipo de Categoría</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-tags"></i><a href="<?php echo BASE_URL; ?>index.php?action=tipos-categoria">Tipos de Categoría</a></li>
                    <li><i class="fa fa-plus-circle"></i> Crear</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Formulario de Tipo de Categoría
                    </header>
                    <div class="panel-body">
                        <form class="form-horizontal" action="<?= BASE_URL ?>index.php?action=tipos-categoria&method=guardar" method="POST">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Nombre</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="nombre" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Estatus</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="id_estatus" required>
                                        <option value="1">Activo</option>
                                        <option value="2">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar</button>
                                    <a href="<?= BASE_URL ?>index.php?action=tipos-categoria" class="btn btn-default"><i class="fa fa-times"></i> Cancelar</a>
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