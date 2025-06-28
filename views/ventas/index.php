<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-shopping-cart"></i> Gestión de Ventas</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-shopping-cart"></i> Ventas</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Listado de Ventas
                        <div class="pull-right">
                            <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#filtrosModal">
                                <i class="fa fa-filter"></i> Filtros
                            </button>
                            <a href="<?= BASE_URL ?>index.php?action=ventas&method=crear" class="btn btn-primary btn-xs">
                                <i class="fa fa-plus"></i> Nueva Venta
                            </a>
                        </div>
                    </header>
                    
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <input type="text" id="busqueda" class="form-control" placeholder="Buscar por cliente, fecha...">
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="tablaVentas" class="table table-striped table-advance table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fa fa-hashtag"></i> ID</th>
                                    <th><i class="fa fa-calendar"></i> Fecha</th>
                                    <th><i class="fa fa-user"></i> Cliente</th>
                                    <th><i class="fa fa-money-bill"></i> Total</th>
                                    <th><i class="fa fa-user-tie"></i> Vendedor</th>
                                    <th><i class="fa fa-cogs"></i> Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ventas as $venta): ?>
                                    <tr>
                                        <td><?= $venta['id']; ?></td>
                                        <td><?= date('d/m/Y h:i A', strtotime($venta['fecha'])); ?></td>
                                        <td><?= htmlspecialchars($venta['cliente']); ?></td>
                                        <td><?= number_format($venta['monto_total'], 2, ',', '.'); ?> Bs</td>
                                        <td><?= htmlspecialchars($venta['usuario']); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?= BASE_URL ?>index.php?action=ventas&method=mostrar&id=<?= $venta['id']; ?>"
                                                    class="btn btn-success btn-xs" title="Ver Detalles">
                                                    <i class="fa fa-eye"></i>
                                                </a>
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
                        <label>Fecha Inicio:</label>
                        <input type="date" class="form-control" id="filtroFechaInicio">
                    </div>
                    <div class="form-group">
                        <label>Fecha Fin:</label>
                        <input type="date" class="form-control" id="filtroFechaFin">
                    </div>
                    <div class="form-group">
                        <label>Vendedor:</label>
                        <select class="form-control" id="filtroVendedor">
                            <option value="">Todos los vendedores</option>
                            <?php foreach ($usuarios as $usuario): ?>
                                <option value="<?= $usuario['nombres'] . ' ' . $usuario['apellidos'] ?>">
                                    <?= $usuario['nombres'] . ' ' . $usuario['apellidos'] ?>
                                </option>
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
        const sinResultados = $('<div id="sin-resultados" class="alert alert-warning text-center" style="display: none;">' +
            '<i class="fa fa-exclamation-circle"></i> No se encontraron ventas que coincidan con la búsqueda</div>');
        $('.panel-body').append(sinResultados);

        // Búsqueda en tiempo real
        $('#busqueda').on('input', function() {
            const searchValue = $(this).val().trim().toLowerCase();
            let resultados = 0;

            $('#tablaVentas tbody tr').each(function() {
                const cliente = $(this).find('td:eq(2)').text().toLowerCase();
                const fecha = $(this).find('td:eq(1)').text().toLowerCase();
                const id = $(this).find('td:eq(0)').text().toLowerCase();

                const match = cliente.includes(searchValue) || fecha.includes(searchValue) || id.includes(searchValue);

                if (match) {
                    $(this).show();
                    resultados++;
                } else {
                    $(this).hide();
                }
            });

            if (resultados === 0 && searchValue.length > 0) {
                sinResultados.show();
            } else {
                sinResultados.hide();
            }
        });

        // Filtros avanzados
        $('#aplicarFiltros').click(function() {
            const fechaInicio = $('#filtroFechaInicio').val();
            const fechaFin = $('#filtroFechaFin').val();
            const vendedor = $('#filtroVendedor').val().toLowerCase();
            let resultados = 0;

            $('#tablaVentas tbody tr').each(function() {
                const fechaVenta = new Date($(this).find('td:eq(1)').text().split(' ')[0].split('/').reverse().join('-'));
                const vendedorVenta = $(this).find('td:eq(4)').text().toLowerCase();

                const matchFecha = (!fechaInicio || !fechaFin || (fechaVenta >= new Date(fechaInicio) && fechaVenta <= new Date(fechaFin)));
                const matchVendedor = !vendedor || vendedorVenta.includes(vendedor);

                if (matchFecha && matchVendedor) {
                    $(this).show();
                    resultados++;
                } else {
                    $(this).hide();
                }
            });

            if (resultados === 0) {
                sinResultados.show();
            } else {
                sinResultados.hide();
            }

            $('#filtrosModal').modal('hide');
        });

        $('#limpiarFiltros').click(function() {
            $('#formFiltros')[0].reset();
            $('#tablaVentas tbody tr').show();
            sinResultados.hide();
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