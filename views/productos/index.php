<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-beer"></i> Gestión de Productos</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-beer"></i> Productos</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Listado de Productos
                        <div class="pull-right">
                            <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#filtrosModal">
                                <i class="fa fa-filter"></i> Filtros
                            </button>
                            <?php if ($_SESSION['user_rol'] != 2): ?>
                            <a href="<?php echo BASE_URL; ?>index.php?action=productos&method=crear" class="btn btn-primary btn-xs">
                                <i class="fa fa-plus"></i> Nuevo Producto
                            </a>
                            <a href="<?= BASE_URL ?>index.php?action=productos&method=registrarEntrada" class="btn btn-info btn-xs">
                                <i class="fa fa-sign-in"></i> Entrada de Productos
                            </a>
                            <?php endif; ?>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <input type="text" id="busqueda" class="form-control" placeholder="Buscar por descripción, categoría...">
                            </div>
                        </div>

                        <?php if (isset($filtros_activos) && !empty($filtros_activos)): ?>
                            <div class="alert alert-info">
                                <strong>Filtros activos:</strong> <?= implode(', ', $filtros_activos) ?>
                            </div>
                        <?php endif; ?>

                        <table id="tablaProductos" class="table table-striped table-advance table-hover">
                            <thead>
                                <tr>
                                    <th>Descripción</th>
                                    <th>Categoría</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos as $producto): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($producto['descripcion']); ?></td>
                                        <td><?= $producto['categoria']; ?></td>
                                        <td><?= $producto['tipo_categoria']; ?></td>
                                        <td><?= $producto['cantidad']; ?></td>
                                        <td><?= number_format($producto['precio'], 2, ',', '.'); ?> Bs</td>
                                        <td>
                                            <span class="label label-<?= $producto['estatus'] == 'Activo' ? 'success' : 'danger'; ?>">
                                                <?= $producto['estatus']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?= BASE_URL ?>index.php?action=productos&method=mostrar&id=<?= $producto['id']; ?>"
                                                    class="btn btn-success btn-xs" title="Ver">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <?php if ($_SESSION['user_rol'] != 2): ?>
                                                <a href="<?= BASE_URL ?>index.php?action=productos&method=editar&id=<?= $producto['id']; ?>"
                                                    class="btn btn-primary btn-xs" title="Editar">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <button onclick="cambiarEstado(<?= $producto['id']; ?>, '<?= $producto['estatus']; ?>')"
                                                    class="btn btn-<?= $producto['id_estatus'] == 1 ? 'danger' : 'success'; ?> btn-xs"
                                                    title="<?= $producto['id_estatus'] == 1 ? 'Desactivar' : 'Activar'; ?>">
                                                    <i class="fa fa-power-off"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <?php if (isset($total_paginas) && $total_paginas > 1): ?>
                            <div class="text-center">
                                <ul class="pagination">
                                    <?php if ($pagina_actual > 1): ?>
                                        <li>
                                            <a href="<?= BASE_URL ?>index.php?action=productos&method=index&pagina=<?= ($pagina_actual-1) ?>&por_pagina=<?= $por_pagina ?><?= isset($termino_busqueda) ? '&busqueda=' . urlencode($termino_busqueda) : '' ?>">
                                                <i class="fa fa-angle-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                        <li class="<?= $i === $pagina_actual ? 'active' : '' ?>">
                                            <a href="<?= BASE_URL ?>index.php?action=productos&method=index&pagina=<?= $i ?>&por_pagina=<?= $por_pagina ?><?= isset($termino_busqueda) ? '&busqueda=' . urlencode($termino_busqueda) : '' ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($pagina_actual < $total_paginas): ?>
                                        <li>
                                            <a href="<?= BASE_URL ?>index.php?action=productos&method=index&pagina=<?= ($pagina_actual+1) ?>&por_pagina=<?= $por_pagina ?><?= isset($termino_busqueda) ? '&busqueda=' . urlencode($termino_busqueda) : '' ?>">
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
                        <label>Categoría:</label>
                        <select class="form-control" id="filtroCategoria">
                            <option value="">Todas las categorías</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['nombre'] ?>"><?= $categoria['nombre'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipo:</label>
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
    // Variables globales necesarias
    const BASE_URL = '<?= BASE_URL ?>';
    const SESSION_USER_ROL = <?= $_SESSION['user_rol'] ?? 'null' ?>;

    $(document).ready(function() {
        // Message when no results found
        const sinResultados = $('<div id="sin-resultados" class="alert alert-warning text-center" style="display: none;">' +
            '<i class="fa fa-exclamation-circle"></i> No se encontraron productos que coincidan con la búsqueda</div>');
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

        function cargarProductos(pagina = 1, forzarPagina = false) {
            const busqueda = $('#busqueda').val().trim() || null;
            const categoria = $('#filtroCategoria').val() || null;
            const tipo = $('#filtroTipo').val() || null;
            const estatus = $('#filtroEstatus').val() || null;

            // Función para normalizar texto (eliminar acentos y convertir a minúsculas)
            const normalizarTexto = (texto) => {
                if (!texto) return null;
                return texto.toLowerCase()
                    .normalize("NFD")
                    .replace(/[\u0300-\u036f]/g, "");
            };

            // Reiniciar a la primera página cuando se realiza una búsqueda o se aplican filtros
            // a menos que se fuerce una página específica
            if (!forzarPagina && (busqueda || categoria || tipo || estatus)) {
                pagina = 1;
            }

            $.ajax({
                url: BASE_URL + 'index.php?action=productos&method=index',
                method: 'GET',
                data: {
                    ajax: true,
                    pagina: pagina,
                    busqueda: normalizarTexto(busqueda),
                    categoria: normalizarTexto(categoria),
                    tipo: normalizarTexto(tipo),
                    estatus: normalizarTexto(estatus)
                },
                success: function(response) {
                    const tbody = $('#tablaProductos tbody');
                    tbody.empty();

                    if (response.productos && response.productos.length > 0) {
                        response.productos.forEach(function(producto) {
                            const row = `
                                <tr>
                                    <td>${producto.descripcion}</td>
                                    <td>${producto.categoria}</td>
                                    <td>${producto.tipo_categoria}</td>
                                    <td>${producto.cantidad}</td>
                                    <td>${parseFloat(producto.precio).toFixed(2)} Bs</td>
                                    <td>
                                        <span class="label label-${producto.id_estatus == 1 ? 'success' : 'danger'}">
                                            ${producto.estatus}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="${BASE_URL}index.php?action=productos&method=mostrar&id=${producto.id}" 
                                               class="btn btn-success btn-xs" title="Ver">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            ${SESSION_USER_ROL != 2 ? `
                                                <a href="${BASE_URL}index.php?action=productos&method=editar&id=${producto.id}" 
                                                   class="btn btn-primary btn-xs" title="Editar">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <button onclick="cambiarEstado(${producto.id}, '${producto.estatus}')" 
                                                        class="btn btn-${producto.id_estatus == 1 ? 'danger' : 'success'} btn-xs"
                                                        title="${producto.id_estatus == 1 ? 'Desactivar' : 'Activar'}">
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
                        text: 'Error al cargar los productos',
                        icon: 'error'
                    });
                }
            });
        }

        // Evento de búsqueda con debounce
        let timeoutId;
        $('#busqueda').on('input', function() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => cargarProductos(1, false), 300);
        });

        // Eventos de filtros
        $('#aplicarFiltros').click(function() {
            cargarProductos(1, false);
            $('#filtrosModal').modal('hide');
        });

        $('#limpiarFiltros').click(function() {
            $('#filtroCategoria').val('');
            $('#filtroTipo').val('');
            $('#filtroEstatus').val('');
            $('#busqueda').val('');
            cargarProductos(1, false);
            $('#filtrosModal').modal('hide');
        });

        // Evento de paginación
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            const pagina = $(this).data('pagina');
            cargarProductos(pagina, true);
        });

        // Cargar datos iniciales con los filtros que puedan estar en la URL
        const urlParams = new URLSearchParams(window.location.search);
        const paginaInicial = urlParams.get('pagina') || 1;
        cargarProductos(parseInt(paginaInicial), true);

        // Mostrar mensajes de sesión con SweetAlert
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

    function cambiarEstado(id, estatusActual) {
        Swal.fire({
            title: '¿Cambiar estado?',
            text: `El producto pasará a estar ${estatusActual === 'Activo' ? 'Inactivo' : 'Activo'}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = BASE_URL + 'index.php?action=productos&method=cambiarEstado&id=' + id;
            }
        });
    }
</script>
<!--main content end-->