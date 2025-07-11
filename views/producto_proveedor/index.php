<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-link"></i> Relaciones Producto-Proveedor</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-link"></i> Producto-Proveedor</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Listado de Relaciones
                        <div class="pull-right">
                            <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#filtrosModal">
                                <i class="fa fa-filter"></i> Filtros
                            </button>
                            <a href="<?php echo BASE_URL; ?>index.php?action=producto-proveedor&method=crear" class="btn btn-primary btn-xs">
                                <i class="fa fa-plus"></i> Nueva Relación
                            </a>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <input type="text" id="busqueda" class="form-control" placeholder="Buscar por producto o proveedor...">
                            </div>
                        </div>

                        <table id="tablaRelaciones" class="table table-striped table-advance table-hover">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Proveedor</th>
                                    <th>Precio Compra</th>
                                    <th>Estatus</th>
                                    <th>Última Actualización</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($relaciones as $relacion): ?>
                                    <tr>
                                        <td><?= $relacion['producto']; ?></td>
                                        <td><?= $relacion['simbolo_proveedor'] . '-' . $relacion['cedula_proveedor'] . ' - ' . $relacion['proveedor']; ?></td>
                                        <td><?= number_format($relacion['precio_compra'], 2); ?></td>
                                        <td>
                                            <span class="label label-<?= $relacion['id_estatus'] == 1 ? 'success' : 'danger'; ?>">
                                                <?= $relacion['estatus']; ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($relacion['fecha_actualizacion'])); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?= BASE_URL ?>index.php?action=producto-proveedor&method=mostrar&id=<?= $relacion['id']; ?>"
                                                    class="btn btn-success btn-xs" title="Ver">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="<?= BASE_URL ?>index.php?action=producto-proveedor&method=editar&id=<?= $relacion['id']; ?>"
                                                    class="btn btn-primary btn-xs" title="Editar">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <button onclick="cambiarEstado(<?= $relacion['id']; ?>, '<?= $relacion['estatus']; ?>')"
                                                    class="btn btn-<?= $relacion['id_estatus'] == 1 ? 'danger' : 'success'; ?> btn-xs"
                                                    title="<?= $relacion['id_estatus'] == 1 ? 'Desactivar' : 'Activar'; ?>">
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
                            <div class="mb-2">
                                Mostrando <?= count($relaciones) ?> de <?= $total_registros ?> registros
                            </div>
                            <ul class="pagination justify-content-center">
                                <?php if ($pagina_actual > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?action=producto-proveedor&pagina=<?= $pagina_actual - 1 ?>">
                                            <i class="fa fa-angle-left"></i> Anterior
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                    <li class="page-item <?= $i === $pagina_actual ? 'active' : '' ?>">
                                        <a class="page-link" href="?action=producto-proveedor&pagina=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($pagina_actual < $total_paginas): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?action=producto-proveedor&pagina=<?= $pagina_actual + 1 ?>">
                                            Siguiente <i class="fa fa-angle-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
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
                        <label>Estatus:</label>
                        <select class="form-control" id="filtroEstatus">
                            <option value="">Todos los estatus</option>
                            <?php foreach ($estatus as $e): ?>
                                <option value="<?= $e['nombre'] ?>"><?= $e['nombre'] ?></option>
                            <?php endforeach; ?>
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
            '<i class="fa fa-exclamation-circle"></i> No se encontraron relaciones que coincidan con la búsqueda</div>');
        $('.panel-body').append(sinResultados);

        // Búsqueda en tiempo real
        $('#busqueda').on('input', function() {
            const searchValue = $(this).val().trim().toLowerCase();
            let resultados = 0;

            $('#tablaRelaciones tbody tr').each(function() {
                const producto = $(this).find('td:eq(0)').text().toLowerCase();
                const proveedor = $(this).find('td:eq(1)').text().toLowerCase();

                const match = producto.includes(searchValue) || proveedor.includes(searchValue);

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

            $('#tablaRelaciones tbody tr').each(function() {
                const estatusRelacion = $(this).find('td:eq(3)').text().toLowerCase();

                const matchEstatus = estatus === '' || estatusRelacion.includes(estatus);

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
            $('#tablaRelaciones tbody tr').show();
            sinResultados.hide();
        });
    });

    function cambiarEstado(id, estatusActual) {
        Swal.fire({
            title: '¿Cambiar estado?',
            text: `La relación pasará a estar ${estatusActual === 'Activo' ? 'Inactiva' : 'Activa'}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= BASE_URL ?>index.php?action=producto-proveedor&method=cambiarEstado&id=' + id;
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