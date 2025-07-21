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
                            <a href="<?= BASE_URL ?>index.php?action=clientes&method=crear" class="btn btn-primary btn-xs">
                                <i class="fa fa-plus"></i> Nuevo Cliente
                            </a>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <input type="text" id="busqueda" class="form-control" placeholder="Buscar por nombre, cédula, teléfono...">
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
                                        <td><?= number_format($cliente['total_ventas'] ?? 0, 2) ?> Bs</td>
                                        <td>
                                            <span class="label label-<?= $cliente['id_estatus'] == 1 ? 'success' : 'danger' ?>">
                                                <?= $cliente['estatus'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?= BASE_URL ?>index.php?action=clientes&method=mostrar&cedula=<?= $cliente['cedula'] ?>" 
                                                   class="btn btn-success btn-xs" title="Ver detalles">
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

                        <?php if ($total_paginas > 1): ?>
                            <div class="text-center">
                                <ul class="pagination pagination-sm">
                                    <?php if ($pagina_actual > 1): ?>
                                        <li>
                                            <a href="<?= BASE_URL ?>index.php?action=clientes&method=index&pagina=<?= $pagina_actual - 1 ?>">
                                                <i class="fa fa-angle-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                        <li class="<?= $i == $pagina_actual ? 'active' : '' ?>">
                                            <a href="<?= BASE_URL ?>index.php?action=clientes&method=index&pagina=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($pagina_actual < $total_paginas): ?>
                                        <li>
                                            <a href="<?= BASE_URL ?>index.php?action=clientes&method=index&pagina=<?= $pagina_actual + 1 ?>">
                                                <i class="fa fa-angle-right"></i>
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
    // Variables globales necesarias
    const BASE_URL = '<?= BASE_URL ?>';
    const SESSION_USER_ROL = <?= $_SESSION['user_rol'] ?? 'null' ?>;

    $(document).ready(function() {
        // Message when no results found
        const sinResultados = $('<div id="sin-resultados" class="alert alert-warning text-center" style="display: none;">' +
            '<i class="fa fa-exclamation-circle"></i> No se encontraron clientes que coincidan con la búsqueda</div>');
        $('.panel-body').append(sinResultados);

        function actualizarPaginacion(totalPaginas, paginaActual) {
            const paginacion = $('.pagination');
            paginacion.empty();

            // Botón anterior
            if (paginaActual > 1) {
                paginacion.append(`
                    <li>
                        <a href="#" data-pagina="${paginaActual - 1}">
                            <i class="fa fa-angle-left"></i>
                        </a>
                    </li>
                `);
            }

            // Números de página
            for (let i = 1; i <= totalPaginas; i++) {
                paginacion.append(`
                    <li class="${i === paginaActual ? 'active' : ''}">
                        <a href="#" data-pagina="${i}">${i}</a>
                    </li>
                `);
            }

            // Botón siguiente
            if (paginaActual < totalPaginas) {
                paginacion.append(`
                    <li>
                        <a href="#" data-pagina="${paginaActual + 1}">
                            <i class="fa fa-angle-right"></i>
                        </a>
                    </li>
                `);
            }
        }

        function cargarClientes(pagina = 1, forzarPagina = false) {
            const busqueda = $('#busqueda').val().trim() || null;
            const estatus = $('#filtroEstatus').val() || null;
            const compras = $('#filtroCompras').val() || null;

            // Reiniciar a la primera página cuando se realiza una búsqueda o se aplican filtros
            // a menos que se fuerce una página específica
            if (!forzarPagina && (busqueda || estatus || compras)) {
                pagina = 1;
            }

            $.ajax({
                url: BASE_URL + 'index.php?action=clientes&method=index',
                method: 'GET',
                data: {
                    ajax: true,
                    pagina: pagina,
                    busqueda: busqueda,
                    estatus: estatus,
                    compras: compras
                },
                success: function(response) {
                    const tbody = $('#tablaClientes tbody');
                    tbody.empty();

                    if (response.clientes && response.clientes.length > 0) {
                        response.clientes.forEach(function(cliente) {
                            const row = `
                                <tr>
                                    <td>${cliente.nombre_simbolo}-${cliente.cedula}</td>
                                    <td>${cliente.nombres} ${cliente.apellidos}</td>
                                    <td>${cliente.telefono}</td>
                                    <td>${cliente.direccion}</td>
                                    <td>${parseFloat(cliente.total_ventas || 0).toFixed(2)} Bs</td>
                                    <td>
                                        <span class="label label-${cliente.id_estatus == 1 ? 'success' : 'danger'}">
                                            ${cliente.estatus}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="${BASE_URL}index.php?action=clientes&method=mostrar&cedula=${cliente.cedula}" 
                                               class="btn btn-success btn-xs" title="Ver detalles">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            ${SESSION_USER_ROL != 2 ? `
                                                <a href="${BASE_URL}index.php?action=clientes&method=editar&cedula=${cliente.cedula}" 
                                                   class="btn btn-primary btn-xs" title="Editar">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <button class="btn btn-${cliente.id_estatus == 1 ? 'danger' : 'success'} btn-xs cambiar-estado" 
                                                        title="${cliente.id_estatus == 1 ? 'Desactivar' : 'Activar'}"
                                                        data-cedula="${cliente.cedula}">
                                                    <i class="fa fa-power-off"></i>
                                                </button>
                                            ` : ''}
                                        </div>
                                    </td>
                                </tr>
                            `;
                            tbody.append(row);
                        });

                        actualizarPaginacion(response.total_paginas, response.pagina_actual);
                        $('#sin-resultados').hide();
                    } else {
                        $('#sin-resultados').show();
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error',
                        text: 'Error al cargar los clientes',
                        icon: 'error'
                    });
                }
            });
        }

        // Evento de búsqueda con debounce
        let timeoutId;
        $('#busqueda').on('input', function() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => cargarClientes(1, false), 300);
        });

        // Eventos de filtros
        $('#aplicarFiltros').click(function() {
            cargarClientes(1, false);
            $('#filtrosModal').modal('hide');
        });

        $('#limpiarFiltros').click(function() {
            $('#filtroEstatus').val('');
            $('#filtroCompras').val('');
            $('#busqueda').val('');
            cargarClientes(1, false);
            $('#filtrosModal').modal('hide');
        });

        // Evento de paginación
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            const pagina = $(this).data('pagina');
            cargarClientes(pagina, true);
        });


        // Cargar datos iniciales con los filtros que puedan estar en la URL
        const urlParams = new URLSearchParams(window.location.search);
        const paginaInicial = urlParams.get('pagina') || 1;
        cargarClientes(parseInt(paginaInicial), true);

        // Change client status
        $(document).on('click', '.cambiar-estado', function() {
            const cedula = $(this).data('cedula');
            const nuevoEstado = $(this).attr('title') === 'Activar' ? 1 : 2;
            const mensaje = nuevoEstado === 1 ? 'activar' : 'desactivar';

            Swal.fire({
                title: '¿Estás seguro?',
                text: `¿Deseas ${mensaje} este cliente?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `${BASE_URL}index.php?action=clientes&method=cambiarEstado&cedula=${cedula}&estado=${nuevoEstado}`;
                }
            });
        });

        // Show session messages with SweetAlert
        <?php if (isset($_SESSION['mensaje'])): ?>
            Swal.fire({
                title: '<?= $_SESSION["mensaje"]["title"] ?>',
                text: '<?= $_SESSION["mensaje"]["text"] ?>',
                icon: '<?= $_SESSION["mensaje"]["icon"] ?>',
                timer: 3000
            });
        <?php unset($_SESSION['mensaje']);
        endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                title: '<?= $_SESSION["error"]["title"] ?>',
                text: '<?= $_SESSION["error"]["text"] ?>',
                icon: '<?= $_SESSION["error"]["icon"] ?>'
            });
        <?php unset($_SESSION['error']);
        endif; ?>
    });
</script>
<!--main content end-->