<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-tags"></i> Tipos de Categoría</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-tags"></i> Tipos de Categoría</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Listado de Tipos de Categoría
                        <div class="pull-right">
                            <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#filtrosModal">
                                <i class="fa fa-filter"></i> Filtros
                            </button>
                            <a href="<?php echo BASE_URL; ?>index.php?action=tipos-categoria&method=crear" class="btn btn-primary btn-xs">
                                <i class="fa fa-plus"></i> Nuevo Tipo
                            </a>
                            <a href="<?= BASE_URL ?>index.php?action=categorias" class="btn btn-default btn-xs">
                                <i class="fa fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </header>
                    <div class="panel-body">
                        <?php if (empty($tipos)): ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> No hay tipos de categoría registrados
                            </div>
                        <?php else: ?>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                    <input type="text" id="busqueda" class="form-control" placeholder="Buscar por nombre o estatus...">
                                </div>
                            </div>

                            <table id="tablaTipos" class="table table-striped table-advance table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Estatus</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tipos as $tipo): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($tipo['id']); ?></td>
                                            <td><?= htmlspecialchars($tipo['nombre']); ?></td>
                                            <td>
                                                <span class="label label-<?= $tipo['id_estatus'] == 1 ? 'success' : 'danger'; ?>">
                                                    <?= htmlspecialchars($tipo['estatus']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?= BASE_URL ?>index.php?action=tipos-categoria&method=mostrar&id=<?= $tipo['id']; ?>"
                                                        class="btn btn-success btn-xs" title="Ver">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="<?= BASE_URL ?>index.php?action=tipos-categoria&method=editar&id=<?= $tipo['id']; ?>"
                                                        class="btn btn-primary btn-xs" title="Editar">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <button onclick="cambiarEstado(<?= $tipo['id']; ?>, <?= $tipo['id_estatus']; ?>)"
                                                        class="btn btn-<?= $tipo['id_estatus'] == 1 ? 'danger' : 'success'; ?> btn-xs"
                                                        title="<?= $tipo['id_estatus'] == 1 ? 'Desactivar' : 'Activar'; ?>">
                                                        <i class="fa fa-power-off"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                        </table>

                        <!-- Controles de paginación -->
                        <?php if ($total_paginas > 1): ?>
                        <div class="text-center">
                            <ul class="pagination">
                                <?php if ($pagina_actual > 1): ?>
                                    <li>
                                        <a href="<?= BASE_URL ?>index.php?action=tipos-categoria&pagina=<?= $pagina_actual - 1 ?>">&laquo; Anterior</a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                    <li class="<?= $i == $pagina_actual ? 'active' : '' ?>">
                                        <a href="<?= BASE_URL ?>index.php?action=tipos-categoria&pagina=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($pagina_actual < $total_paginas): ?>
                                    <li>
                                        <a href="<?= BASE_URL ?>index.php?action=tipos-categoria&pagina=<?= $pagina_actual + 1 ?>">Siguiente &raquo;</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                            <div class="text-muted">
                                Mostrando <?= count($tipos) ?> de <?= $total_registros ?> registros
                            </div>
                        </div>
                        <?php endif; ?>
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
            '<i class="fa fa-exclamation-circle"></i> No se encontraron tipos de categoría que coincidan con los filtros</div>');
        $('.panel-body').append(sinResultados);

        // Búsqueda en tiempo real
        $('#busqueda').on('input', function() {
            const searchValue = $(this).val().trim().toLowerCase();
            let resultados = 0;

            $('#tablaTipos tbody tr').each(function() {
                const nombre = $(this).find('td:eq(1)').text().toLowerCase();
                const estatus = $(this).find('td:eq(2)').text().toLowerCase();

                const match = nombre.includes(searchValue) || estatus.includes(searchValue);

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
            let resultados = 0;

            $('#tablaTipos tbody tr').each(function() {
                const estatusTipo = $(this).find('td:eq(2)').text().toLowerCase();

                const matchEstatus = estatus === '' || estatusTipo.includes(estatus);

                if (matchEstatus) {
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
            $('#tablaTipos tbody tr').show();
            $('#busqueda').val(''); // Limpiar también la búsqueda
            sinResultados.hide();
        });
    });

    function cambiarEstado(id, estatusActual) {
        Swal.fire({
            title: '¿Cambiar estado?',
            text: `El tipo de categoría pasará a estar ${estatusActual === 1 ? 'Inactivo' : 'Activo'}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= BASE_URL ?>index.php?action=tipos-categoria&method=cambiarEstado&id=' + id;
            }
        });
    }

    // Mostrar mensajes de sesión con SweetAlert
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
</script>
<!--main content end-->