<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-users"></i> Gestión de Usuarios</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-users"></i> Usuarios</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Listado de Usuarios
                        <div class="pull-right">
                            <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#filtrosModal">
                                <i class="fa fa-filter"></i> Filtros
                            </button>
                            <a href="<?php echo BASE_URL; ?>index.php?action=usuarios&method=crear" class="btn btn-primary btn-xs">
                                <i class="fa fa-plus"></i> Nuevo Usuario
                            </a>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <input type="text" id="busqueda" class="form-control" placeholder="Buscar por cédula, nombre, apellido o usuario...">
                            </div>
                        </div>

                        <table id="tablaUsuarios" class="table table-striped table-advance table-hover">
                            <thead>
                                <tr>
                                    <th>Cédula</th>
                                    <th>Nombres</th>
                                    <th>Apellidos</th>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td><?= $usuario['nombre_simbolo'] ?>-<?= $usuario['cedula']; ?></td>
                                        <td><?= $usuario['nombres']; ?></td>
                                        <td><?= $usuario['apellidos']; ?></td>
                                        <td><?= $usuario['user']; ?></td>
                                        <td><?= $usuario['rol']; ?></td>
                                        <td>
                                            <span class="label label-<?= $usuario['estatus'] == 'Activo' ? 'success' : 'danger'; ?>">
                                                <?= $usuario['estatus']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?= BASE_URL ?>index.php?action=usuarios&method=mostrar&id=<?= $usuario['id']; ?>"
                                                    class="btn btn-success btn-xs" title="Ver">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="<?= BASE_URL ?>index.php?action=usuarios&method=editar&id=<?= $usuario['id']; ?>"
                                                    class="btn btn-primary btn-xs" title="Editar">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <button onclick="cambiarEstado(<?= $usuario['id']; ?>, '<?= $usuario['estatus']; ?>')"
                                                    class="btn btn-<?= $usuario['id_estatus'] == 1 ? 'danger' : 'success'; ?> btn-xs"
                                                    title="<?= $usuario['id_estatus'] == 1 ? 'Desactivar' : 'Activar'; ?>">
                                                    <i class="fa fa-power-off"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <?php if ($total_paginas > 1): ?>
                        <div class="text-center">
                            <nav aria-label="Page navigation">
                                <ul class="pagination">
                                    <?php if ($pagina_actual > 1): ?>
                                    <li>
                                        <a href="<?= BASE_URL ?>index.php?action=usuarios&pagina=<?= $pagina_actual - 1 ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                    <li class="<?= $i == $pagina_actual ? 'active' : '' ?>">
                                        <a href="<?= BASE_URL ?>index.php?action=usuarios&pagina=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                    <?php endfor; ?>

                                    <?php if ($pagina_actual < $total_paginas): ?>
                                    <li>
                                        <a href="<?= BASE_URL ?>index.php?action=usuarios&pagina=<?= $pagina_actual + 1 ?>" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                            <div class="text-muted">
                                Mostrando <?= count($usuarios) ?> de <?= $total_registros ?> registros
                            </div>
                        </div>
                        <?php endif; ?>
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
                        <label>Rol:</label>
                        <select class="form-control" id="filtroRol">
                            <option value="">Todos los roles</option>
                            <?php foreach ($roles as $rol): ?>
                                <option value="<?= $rol['nombre'] ?>"><?= $rol['nombre'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Estatus:</label>
                        <select class="form-control" id="filtroEstatus">
                            <option value="">Todos los estatus</option>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
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
            '<i class="fa fa-exclamation-circle"></i> No se encontraron usuarios que coincidan con la búsqueda</div>');
        $('.panel-body').append(sinResultados);

        // Búsqueda en tiempo real
        $('#busqueda').on('input', function() {
            const searchValue = $(this).val().trim().toLowerCase();
            let resultados = 0;

            $('#tablaUsuarios tbody tr').each(function() {
                const cedula = $(this).find('td:eq(0)').text().toLowerCase();
                const nombres = $(this).find('td:eq(1)').text().toLowerCase();
                const apellidos = $(this).find('td:eq(2)').text().toLowerCase();
                const usuario = $(this).find('td:eq(3)').text().toLowerCase();

                const match = cedula.includes(searchValue) ||
                    nombres.includes(searchValue) ||
                    apellidos.includes(searchValue) ||
                    usuario.includes(searchValue);

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
            const rol = $('#filtroRol').val().toLowerCase();
            const estatus = $('#filtroEstatus').val().toLowerCase();
            let resultados = 0;

            $('#tablaUsuarios tbody tr').each(function() {
                const rolUsuario = $(this).find('td:eq(4)').text().toLowerCase();
                const estatusUsuario = $(this).find('td:eq(5)').text().toLowerCase();

                const matchRol = rol === '' || rolUsuario.includes(rol);
                const matchEstatus = estatus === '' || estatusUsuario.includes(estatus);

                if (matchRol && matchEstatus) {
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
            $('#tablaUsuarios tbody tr').show();
            sinResultados.hide();
        });
    });

    function cambiarEstado(id, estatusActual) {
        Swal.fire({
            title: '¿Cambiar estado?',
            text: `El usuario pasará a estar ${estatusActual === 'Activo' ? 'Inactivo' : 'Activo'}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= BASE_URL ?>index.php?action=usuarios&method=cambiarEstado&id=' + id;
            }
        });
    }

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
</script>
<!--main content end-->