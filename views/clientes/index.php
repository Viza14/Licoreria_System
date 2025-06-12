<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-users"></i> Gestión de Clientes</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-users"></i>Clientes</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Listado de Clientes
                        <div class="pull-right">
                            <a href="<?= BASE_URL ?>index.php?action=clientes&method=crear" class="btn btn-success btn-sm">
                                <i class="fa fa-plus"></i> Nuevo Cliente
                            </a>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-advance table-hover" id="clientesTable">
                                <thead>
                                    <tr>
                                        <th><i class="fa fa-id-card"></i> Cédula</th>
                                        <th><i class="fa fa-user"></i> Nombre Completo</th>
                                        <th><i class="fa fa-phone"></i> Teléfono</th>
                                        <th><i class="fa fa-map-marker"></i> Dirección</th>
                                        <th><i class="fa fa-shopping-cart"></i> Total Compras</th>
                                        <th><i class="fa fa-info-circle"></i> Estatus</th>
                                        <th><i class="fa fa-cogs"></i> Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <tr>
                                            <td><?= $cliente['nombre_simbolo'] . '-' . $cliente['cedula'] ?></td>
                                            <td><?= $cliente['nombres'] . ' ' . $cliente['apellidos'] ?></td>
                                            <td><?= $cliente['telefono'] ?></td>
                                            <td><?= $cliente['direccion'] ?></td>
                                            <td><?= number_format($this->model->obtenerTotalVentasCliente($cliente['cedula']), 2) ?> Bs</td>
                                            <td>
                                                <span class="label label-<?= $cliente['id_estatus'] == 1 ? 'success' : 'danger' ?>">
                                                    <?= $cliente['estatus'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?= BASE_URL ?>index.php?action=clientes&method=mostrar&cedula=<?= $cliente['cedula'] ?>" 
                                                       class="btn btn-info btn-sm" title="Ver detalles">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <?php if ($_SESSION['user_rol'] != 2): ?>
                                                    <a href="<?= BASE_URL ?>index.php?action=clientes&method=editar&cedula=<?= $cliente['cedula'] ?>" 
                                                       class="btn btn-primary btn-sm" title="Editar">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    
                                                        <button class="btn btn-<?= $cliente['id_estatus'] == 1 ? 'danger' : 'success' ?> btn-sm cambiar-estado" 
                                                                title="<?= $cliente['id_estatus'] == 1 ? 'Desactivar' : 'Activar' ?>"
                                                                data-cedula="<?= $cliente['cedula'] ?>">
                                                            <i class="fa fa-power-off"></i>
                                                            <?= $cliente['id_estatus'] == 1 ? ' ' : '' ?>
                                                        </button>
                                                     <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>

<!-- Incluir SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<script>
$(document).ready(function() {
    // Inicializar DataTable
    $('#clientesTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
        }
    });

    // Manejar cambio de estado
    $('.cambiar-estado').click(function() {
        const cedula = $(this).data('cedula');
        const accion = $(this).attr('title').toLowerCase();
        
        Swal.fire({
            title: 'Confirmar',
            text: `¿Está seguro que desea ${accion} este cliente?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: `Sí, ${accion}`,
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `<?= BASE_URL ?>index.php?action=clientes&method=cambiarEstado&cedula=${cedula}`;
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
});
$(document).ready(function() {
    // Manejar clic en botón de cambiar estado
    $(document).on('click', '.cambiar-estado', function() {
        const cedula = $(this).data('cedula');
        const accion = $(this).attr('title').toLowerCase();
        
        Swal.fire({
            title: 'Confirmar',
            text: `¿Está seguro que desea ${accion} este cliente?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: `Sí, ${accion}`,
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `<?= BASE_URL ?>index.php?action=clientes&method=cambiarEstado&cedula=${cedula}`;
            }
        });
    });
});
</script>
</<script>

</script>//
<!--main content end-->