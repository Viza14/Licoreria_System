<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-user-plus"></i> Crear Usuario</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-users"></i><a href="<?php echo BASE_URL; ?>index.php?action=usuarios">Usuarios</a></li>
                    <li><i class="fa fa-user-plus"></i> Crear</li>
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
                        <form class="form-horizontal" action="<?= BASE_URL ?>index.php?action=usuarios&method=guardar" method="POST" id="formUsuario">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Cédula</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <select name="id_simbolo_cedula" id="simboloCedula" style="border: none; background: transparent;">
                                                <?php
                                                $simbolos = $this->model->obtenerSimbolosCedula();
                                                foreach ($simbolos as $simbolo): ?>
                                                    <option value="<?= $simbolo['id'] ?>"><?= $simbolo['nombre'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </span>
                                        <input type="text" class="form-control" name="cedula" id="cedula" required
                                            pattern="\d{8,9}" title="La cédula debe tener 8 dígitos para V- o 9 dígitos para J-">
                                    </div>
                                    <small class="text-muted" id="cedulaHelp">Ingrese 8 dígitos para V- o 9 dígitos para J-</small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Nombres</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="nombres" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Apellidos</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="apellidos" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Teléfono</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="telefono" required maxlength="11" pattern="\d{11}" title="El teléfono debe tener exactamente 11 dígitos">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Dirección</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="direccion">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Usuario</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="user" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Contraseña</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Rol</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="id_rol" required>
                                        <?php foreach ($roles as $rol): ?>
                                            <option value="<?= $rol['id']; ?>"><?= $rol['nombre']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
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
                                    <a href="<?= BASE_URL ?>index.php?action=usuarios" class="btn btn-default"><i class="fa fa-times"></i> Cancelar</a>
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
    // Validación dinámica de cédula según símbolo
    document.addEventListener('DOMContentLoaded', function() {
        const simboloCedula = document.getElementById('simboloCedula');
        const cedulaInput = document.getElementById('cedula');
        const cedulaHelp = document.getElementById('cedulaHelp');

        function actualizarValidacion() {
            const simbolo = simboloCedula.options[simboloCedula.selectedIndex].text;

            if (simbolo.includes('V-') || simbolo.includes('E-')) {
                cedulaInput.pattern = '\\d{7,8}';
                cedulaInput.title = 'La cédula debe tener 7 u 8 dígitos para ' + simbolo;
                cedulaHelp.textContent = 'Para ' + simbolo + ' ingrese 7 u 8 dígitos';
                cedulaInput.maxLength = 8;
            } else if (simbolo.includes('J-')) {
                cedulaInput.pattern = '\\d{8,9}';
                cedulaInput.title = 'La cédula debe tener 8 o 9 dígitos para ' + simbolo;
                cedulaHelp.textContent = 'Para ' + simbolo + ' ingrese 8 o 9 dígitos';
                cedulaInput.maxLength = 9;
            }
        }

        function validarCedula() {
            const simbolo = simboloCedula.options[simboloCedula.selectedIndex].text;
            const valor = cedulaInput.value;

            if (simbolo.includes('V-') || simbolo.includes('E-')) {
                if (valor.length < 7 || valor.length > 8) {
                    cedulaInput.setCustomValidity('Debe tener entre 7 y 8 dígitos para ' + simbolo);
                    return false;
                }
            } else if (simbolo.includes('J-')) {
                if (valor.length < 8 || valor.length > 9) {
                    cedulaInput.setCustomValidity('Debe tener entre 8 y 9 dígitos para ' + simbolo);
                    return false;
                }
            }
            cedulaInput.setCustomValidity('');
            return true;
        }

        // Eventos
        simboloCedula.addEventListener('change', actualizarValidacion);

        cedulaInput.addEventListener('input', function() {
            // Solo permitir números
            this.value = this.value.replace(/[^0-9]/g, '');

            // Limitar longitud según tipo
            const simbolo = simboloCedula.options[simboloCedula.selectedIndex].text;
            if ((simbolo.includes('V-') || simbolo.includes('E-')) && this.value.length > 8) {
                this.value = this.value.slice(0, 8);
            } else if (simbolo.includes('J-') && this.value.length > 9) {
                this.value = this.value.slice(0, 9);
            }

            validarCedula();
        });

        // Validar antes de enviar el formulario
        document.getElementById('formUsuario').addEventListener('submit', function(e) {
            if (!validarCedula()) {
                e.preventDefault();
                Swal.fire({
                    title: 'Error',
                    text: cedulaInput.validationMessage,
                    icon: 'error'
                });
            }
        });

        // Inicializar
        actualizarValidacion();
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