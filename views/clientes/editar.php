<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-edit"></i> Editar Cliente</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-users"></i><a href="<?php echo BASE_URL; ?>index.php?action=clientes">Clientes</a></li>
                    <li><i class="fa fa-edit"></i> Editar</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Editar Información de Cliente
                    </header>
                    <div class="panel-body">
                        <form class="form-horizontal" action="<?= BASE_URL ?>index.php?action=clientes&method=actualizar&cedula=<?= $cliente['cedula']; ?>" method="POST" id="formCliente">
                            <input type="hidden" id="cedulaOriginal" value="<?= $cliente['cedula'] ?>">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Cédula</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <select name="id_simbolo_cedula" id="simboloCedula" style="border: none; background: transparent;">
                                                <?php foreach ($simbolos as $simbolo): ?>
                                                    <option value="<?= $simbolo['id'] ?>" <?= $simbolo['id'] == $cliente['id_simbolo_cedula'] ? 'selected' : '' ?>>
                                                        <?= $simbolo['nombre'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </span>
                                        <input type="text" class="form-control" name="cedula" id="cedula" 
                                            value="<?= $cliente['cedula'] ?>" required>
                                    </div>
                                    <small class="text-muted" id="cedulaHelp">
                                        <?= $cliente['nombre_simbolo'] == 'V' || $cliente['nombre_simbolo'] == 'E' ? 
                                            '7 u 8 dígitos para V- o E-' : '8 o 9 dígitos para J-' ?>
                                    </small>
                                </div>
                            </div>
                            <!-- Rest of the form fields remain the same -->
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Nombres</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="nombres" value="<?= $cliente['nombres'] ?>" required
                                           pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$"
                                           title="Solo se permiten letras y espacios">
                                    <small class="text-muted">Solo letras y espacios permitidos</small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Apellidos</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="apellidos" value="<?= $cliente['apellidos'] ?>" required
                                           pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$"
                                           title="Solo se permiten letras y espacios">
                                    <small class="text-muted">Solo letras y espacios permitidos</small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Teléfono</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="telefono" value="<?= $cliente['telefono'] ?>" required maxlength="11" pattern="\d{11}" title="El teléfono debe tener exactamente 11 dígitos">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Dirección</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="direccion" value="<?= $cliente['direccion'] ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Estatus</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="id_estatus" required>
                                        <option value="1" <?= $cliente['id_estatus'] == 1 ? 'selected' : '' ?>>Activo</option>
                                        <option value="2" <?= $cliente['id_estatus'] == 2 ? 'selected' : '' ?>>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Actualizar</button>
                                    <a href="<?= BASE_URL ?>index.php?action=clientes" class="btn btn-default"><i class="fa fa-times"></i> Cancelar</a>
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
document.addEventListener('DOMContentLoaded', function() {
    const simboloCedula = document.getElementById('simboloCedula');
    const cedulaInput = document.getElementById('cedula');
    const cedulaHelp = document.getElementById('cedulaHelp');
    const cedulaOriginal = document.getElementById('cedulaOriginal').value;

    function actualizarValidacion() {
        const simbolo = simboloCedula.options[simboloCedula.selectedIndex].text;
        
        if (simbolo === 'V' || simbolo === 'E') {
            cedulaInput.pattern = '\\d{7,8}';
            cedulaInput.title = 'La cédula debe tener 7 u 8 dígitos para ' + simbolo + '-';
            cedulaHelp.textContent = 'Para ' + simbolo + '- ingrese 7 u 8 dígitos';
            cedulaInput.maxLength = 8;
        } else if (simbolo === 'J') {
            cedulaInput.pattern = '\\d{8,9}';
            cedulaInput.title = 'El RIF debe tener 8 o 9 dígitos para ' + simbolo + '-';
            cedulaHelp.textContent = 'Para ' + simbolo + '- ingrese 8 o 9 dígitos';
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
        this.value = this.value.replace(/[^0-9]/g, '');
        
        const simbolo = simboloCedula.options[simboloCedula.selectedIndex].text;
        if ((simbolo.includes('V-') || simbolo.includes('E-')) && this.value.length > 8) {
            this.value = this.value.slice(0, 8);
        } else if (simbolo.includes('J-') && this.value.length > 9) {
            this.value = this.value.slice(0, 9);
        }
        
        validarCedula();
    });

    // Validar antes de enviar el formulario
    document.getElementById('formCliente').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validarCedula()) {
            Swal.fire({
                title: 'Error',
                text: cedulaInput.validationMessage,
                icon: 'error'
            });
            return;
        }

        // Check if cedula was modified
        if (cedulaInput.value !== cedulaOriginal) {
            Swal.fire({
                title: '¿Está seguro?',
                text: 'Si modifica la cédula, este cambio se aplicará a todos los registros existentes del cliente en el sistema.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, modificar',
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        } else {
            this.submit();
        }
    });

    // Initialize
    actualizarValidacion();

    // Validación de nombres y apellidos
    const nombresInput = document.querySelector('[name="nombres"]');
    const apellidosInput = document.querySelector('[name="apellidos"]');

    function validarSoloLetras(input) {
        input.value = input.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
    }

    nombresInput.addEventListener('input', function() {
        validarSoloLetras(this);
    });

    apellidosInput.addEventListener('input', function() {
        validarSoloLetras(this);
    });
});

// Show SweetAlert messages
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

// Initialize validation based on selected symbol
document.addEventListener('DOMContentLoaded', function() {
    const simboloSelect = document.getElementById('simboloCedula');
    const event = new Event('change');
    simboloSelect.dispatchEvent(event);
});
</script>
<!--main content end-->