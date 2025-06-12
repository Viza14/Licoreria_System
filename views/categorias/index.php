<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-list"></i> Categorías</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-list"></i> Categorías</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Listado de Categorías
                        <div class="pull-right">
                            <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#filtrosModal">
                                <i class="fa fa-filter"></i> Filtros
                            </button>
                            <a href="<?php echo BASE_URL; ?>index.php?action=categorias&method=crear" class="btn btn-primary btn-xs">
                                <i class="fa fa-plus"></i> Nueva Categoría
                            </a>
                            <a href="<?php echo BASE_URL; ?>index.php?action=tipos-categoria" class="btn btn-info btn-xs">
                                <i class="fa fa-list"></i> Tipos de Categoría
                            </a>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <input type="text" id="busqueda" class="form-control" placeholder="Buscar por nombre o tipo...">
                            </div>
                        </div>

                        <table id="tablaCategorias" class="table table-striped table-advance table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Tipo de Categoría</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categorias as $categoria): ?>
                                    <tr>
                                        <td><?= $categoria['id']; ?></td>
                                        <td><?= $categoria['nombre']; ?></td>
                                        <td><?= $categoria['tipo_categoria']; ?></td>
                                        <td>
                                            <span class="label label-<?= $categoria['estatus'] == 'Activo' ? 'success' : 'danger'; ?>">
                                                <?= $categoria['estatus']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?= BASE_URL ?>index.php?action=categorias&method=mostrar&id=<?= $categoria['id']; ?>"
                                                    class="btn btn-success btn-xs" title="Ver">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="<?= BASE_URL ?>index.php?action=categorias&method=editar&id=<?= $categoria['id']; ?>"
                                                    class="btn btn-primary btn-xs" title="Editar">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <button onclick="cambiarEstado(<?= $categoria['id']; ?>, '<?= $categoria['estatus']; ?>')"
                                                    class="btn btn-<?= $categoria['id_estatus'] == 1 ? 'danger' : 'success'; ?> btn-xs"
                                                    title="<?= $categoria['id_estatus'] == 1 ? 'Desactivar' : 'Activar'; ?>">
                                                    <i class="fa fa-power-off"></i>
                                                </button>
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
                        <label>Tipo de Categoría:</label>
                        <select class="form-control" id="filtroTipo">
                            <option value="">Todos los tipos</option>
                            <?php foreach ($tipos as $tipo): ?>
                                <option value="<?= $tipo['nombre'] ?>"><?= $tipo['nombre'] ?></option>
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
            '<i class="fa fa-exclamation-circle"></i> No se encontraron categorías que coincidan con la búsqueda</div>');
        $('.panel-body').append(sinResultados);

        // Búsqueda en tiempo real
        $('#busqueda').on('input', function() {
            const searchValue = $(this).val().trim().toLowerCase();
            let resultados = 0;

            $('#tablaCategorias tbody tr').each(function() {
                const nombre = $(this).find('td:eq(1)').text().toLowerCase();
                const tipo = $(this).find('td:eq(2)').text().toLowerCase();

                const match = nombre.includes(searchValue) || tipo.includes(searchValue);

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
            const tipo = $('#filtroTipo').val().toLowerCase();
            const estatus = $('#filtroEstatus').val().toLowerCase();
            let resultados = 0;

            $('#tablaCategorias tbody tr').each(function() {
                const tipoCategoria = $(this).find('td:eq(2)').text().toLowerCase();
                const estatusCategoria = $(this).find('td:eq(3)').text().toLowerCase();

                const matchTipo = tipo === '' || tipoCategoria.includes(tipo);
                const matchEstatus = estatus === '' || estatusCategoria.includes(estatus);

                if (matchTipo && matchEstatus) {
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
            $('#tablaCategorias tbody tr').show();
            sinResultados.hide();
        });
    });

    function cambiarEstado(id, estatusActual) {
        Swal.fire({
            title: '¿Cambiar estado?',
            text: `La categoría pasará a estar ${estatusActual === 'Activo' ? 'Inactiva' : 'Activa'}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= BASE_URL ?>index.php?action=categorias&method=cambiarEstado&id=' + id;
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