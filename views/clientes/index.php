<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-users"></i> <?= $pageTitle ?></h3>
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
                            <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#filtrosModal">
                                <i class="fa fa-filter"></i> Filtros
                            </button>
                            <a href="<?= BASE_URL ?>index.php?action=clientes&method=crear" class="btn btn-success btn-xs">
                                <i class="fa fa-plus"></i> Nuevo Cliente
                            </a>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <input type="text" id="busqueda" class="form-control" placeholder="Buscar por cédula, nombre, apellido o teléfono...">
                            </div>
                        </div>

                        <table id="tablaClientes" class="table table-striped table-advance table-hover">
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
                                                   class="btn btn-info btn-xs" title="Ver detalles">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <?php if ($_SESSION['user_rol'] != 2): ?>
                                                <a href="<?= BASE_URL ?>index.php?action=clientes&method=editar&cedula=<?= $cliente['cedula'] ?>" 
                                                   class="btn btn-primary btn-xs" title="Editar">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                
                                                    <button class="btn btn-<?= $cliente['id_estatus'] == 1 ? 'danger' : 'success' ?> btn-xs cambiar-estado" 
                                                            title="<?= $cliente['id_estatus'] == 1 ? 'Desactivar' : 'Activar' ?>"
                                                            data-cedula="<?= $cliente['cedula'] ?>">
                                                        <i class="fa fa-power-off"></i>
                                                    </button>
                                                 <?php endif; ?>
                                            </div>
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

<!-- Modal de Filtros -->
<div class="modal fade" id="filtrosModal" tabindex="-1" role="dialog" aria-labelledby="filtrosModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="filtrosModalLabel"><i class="fa fa-filter"></i> Filtros Avanzados</h4>
            </div>
            <div class="modal-body">
                <form id="formFiltros">
                    <div class="form-group">
                        <label>Estatus:</label>
                        <select class="form-control" id="filtroEstatus">
                            <option value="">Todos los estatus</option>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Total de Compras:</label>
                        <select class="form-control" id="filtroCompras">
                            <option value="">Todos los montos</option>
                            <option value="mayor">Mayores a 1000 Bs</option>
                            <option value="medio">Entre 500 y 1000 Bs</option>
                            <option value="menor">Menores a 500 Bs</option>
                            <option value="cero">Sin compras</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="aplicarFiltros">Aplicar Filtros</button>
                <button type="button" class="btn btn-link" id="limpiarFiltros">Limpiar Filtros</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts para manejar la búsqueda, filtros y SweetAlert -->
<script>
$(document).ready(function() {
    // Mensaje cuando no hay resultados
    const sinResultados = $('<div id="sin-resultados" class="alert alert-warning text-center" style="display: none;">' +
        '<i class="fa fa-exclamation-circle"></i> No se encontraron clientes que coincidan con la búsqueda</div>');
    $('.panel-body').append(sinResultados);

    // Búsqueda en tiempo real
    $('#busqueda').on('input', function() {
        const searchValue = $(this).val().trim().toLowerCase();
        let resultados = 0;

        $('#tablaClientes tbody tr').each(function() {
            const cedula = $(this).find('td:eq(0)').text().toLowerCase();
            const nombreCompleto = $(this).find('td:eq(1)').text().toLowerCase();
            const telefono = $(this).find('td:eq(2)').text().toLowerCase();

            const match = cedula.includes(searchValue) ||
                nombreCompleto.includes(searchValue) ||
                telefono.includes(searchValue);

            if (match) {
                $(this).show();
                resultados++;
            } else {
                $(this).hide();
            }
        });

        // Mostrar mensaje si no hay resultados
        if (resultados === 0 && searchValue.length > 0) {
            sinResultados.show();
        } else {
            sinResultados.hide();
        }
    });

    // Filtros avanzados
    $('#aplicarFiltros').click(function() {
        const estatus = $('#filtroEstatus').val().toLowerCase();
        const compras = $('#filtroCompras').val();
        let resultados = 0;

        $('#tablaClientes tbody tr').each(function() {
            const estatusCliente = $(this).find('td:eq(5)').text().toLowerCase();
            const comprasCliente = parseFloat($(this).find('td:eq(4)').text().replace(/[^0-9.]/g, ''));

            // Validar estatus
            const matchEstatus = estatus === '' || estatusCliente.includes(estatus);
            
            // Validar monto de compras
            let matchCompras = true;
            if (compras !== '') {
                switch(compras) {
                    case 'mayor':
                        matchCompras = comprasCliente > 1000;
                        break;
                    case 'medio':
                        matchCompras = comprasCliente >= 500 && comprasCliente <= 1000;
                        break;
                    case 'menor':
                        matchCompras = comprasCliente < 500 && comprasCliente > 0;
                        break;
                    case 'cero':
                        matchCompras = comprasCliente === 0;
                        break;
                }
            }

            if (matchEstatus && matchCompras) {
                $(this).show();
                resultados++;
            } else {
                $(this).hide();
            }
        });

        // Mostrar mensaje si no hay resultados
        if (resultados === 0) {
            sinResultados.show();
        } else {
            sinResultados.hide();
        }

        $('#filtrosModal').modal('hide');
    });

    $('#limpiarFiltros').click(function() {
        $('#formFiltros')[0].reset();
        $('#tablaClientes tbody tr').show();
        sinResultados.hide();
        $('#busqueda').val('').trigger('input');
    });

    // Manejar cambio de estado
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

    // Mostrar mensajes de sesión con SweetAlert
    <?php if (isset($_SESSION['mensaje'])): ?>
        Swal.fire({
            title: '<?= $_SESSION['mensaje']['title'] ?>',
            text: '<?= $_SESSION['mensaje']['text'] ?>',
            icon: '<?= $_SESSION['mensaje']['icon'] ?>',
            timer: 3000
        });
    <?php unset($_SESSION['mensaje']);
    endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        Swal.fire({
            title: '<?= $_SESSION['error']['title'] ?>',
            text: '<?= $_SESSION['error']['text'] ?>',
            icon: '<?= $_SESSION['error']['icon'] ?>'
        });
    <?php unset($_SESSION['error']);
    endif; ?>
});
</script>
<!--main content end-->