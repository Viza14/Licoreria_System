<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-cubes"></i> Gestión de Stock</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-cubes"></i> Stock</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Límites de Stock Configurados
                        <div class="pull-right">
                            <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#filtrosModal">
                                <i class="fa fa-filter"></i> Filtros
                            </button>
                            <a href="index.php?action=gestion-stock&method=alertas" class="btn btn-warning btn-xs">
                                <i class="fa fa-exclamation-triangle"></i> Ver Alertas
                            </a>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <input type="text" id="busqueda" class="form-control" placeholder="Buscar por producto...">
                            </div>
                        </div>

                        <table id="tablaStock" class="table table-striped table-advance table-hover">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center" style="width: 120px;">Stock Actual</th>
                                    <th>Mínimo</th>
                                    <th>Máximo</th>
                                    <th>Última Actualización</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($limites as $limite): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($limite['producto']) ?></td>
                                        <td class="text-center"><?= isset($limite['cantidad']) ? $limite['cantidad'] : 0 ?></td>
                                        <td class="text-center <?= (isset($limite['cantidad']) && $limite['cantidad'] <= $limite['stock_minimo']) ? 'text-danger' : '' ?>">
                                            <?= $limite['stock_minimo'] ?>
                                        </td>
                                        <td class="text-center <?= (isset($limite['cantidad']) && $limite['cantidad'] >= $limite['stock_maximo']) ? 'text-danger' : '' ?>">
                                            <?= $limite['stock_maximo'] ?>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($limite['fecha_actualizacion'])) ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="index.php?action=gestion-stock&method=editar&id=<?= $limite['id_producto'] ?>" 
                                                   class="btn btn-primary btn-xs" title="Editar">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <?php if (!empty($productosSinLimite)): ?>
                <section class="panel">
                    <header class="panel-heading">
                        Productos sin Límites Configurados
                    </header>
                    <div class="panel-body">
                        <table id="tablaSinLimites" class="table table-striped table-advance table-hover">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center" style="width: 120px;">Stock Actual</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productosSinLimite as $producto): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($producto['descripcion']) ?></td>
                                        <td class="text-center"><?= $producto['cantidad'] ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="index.php?action=gestion-stock&method=editar&id=<?= $producto['id'] ?>" 
                                                   class="btn btn-success btn-xs" title="Configurar">
                                                    <i class="fa fa-plus"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
                <?php endif; ?>
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
                        <label>Estado de Stock:</label>
                        <select class="form-control" id="filtroEstadoStock">
                            <option value="">Todos</option>
                            <option value="bajo">Stock Bajo</option>
                            <option value="normal">Stock Normal</option>
                            <option value="alto">Stock Alto</option>
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

<!-- Scripts para manejar la búsqueda y filtros -->
<script>
    $(document).ready(function() {
        const sinResultados = $('<div id="sin-resultados" class="alert alert-warning text-center" style="display: none;">' +
            '<i class="fa fa-exclamation-circle"></i> No se encontraron productos que coincidan con la búsqueda</div>');
        $('.panel-body').first().append(sinResultados);

        $('#busqueda').on('input', function() {
            const searchValue = $(this).val().trim().toLowerCase();
            let resultados = 0;

            $('#tablaStock tbody tr').each(function() {
                const producto = $(this).find('td:eq(0)').text().toLowerCase();
                if (producto.includes(searchValue)) {
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

        $('#aplicarFiltros').click(function() {
            const estadoStock = $('#filtroEstadoStock').val();
            let resultados = 0;

            $('#tablaStock tbody tr').each(function() {
                const cantidad = parseInt($(this).find('td:eq(1)').text()) || 0;
                const minimo = parseInt($(this).find('td:eq(2)').text());
                const maximo = parseInt($(this).find('td:eq(3)').text());

                let mostrar = true;
                if (estadoStock === 'bajo' && cantidad >= minimo) mostrar = false;
                if (estadoStock === 'normal' && (cantidad < minimo || cantidad > maximo)) mostrar = false;
                if (estadoStock === 'alto' && cantidad <= maximo) mostrar = false;

                if (mostrar || estadoStock === '') {
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
            $('#tablaStock tbody tr').show();
            sinResultados.hide();
        });
    });

    // SweetAlert para mensajes de sesión
    <?php if (isset($_SESSION['mensaje'])): ?>
        Swal.fire({
            title: '<?= $_SESSION['mensaje']['title'] ?>',
            text: '<?= $_SESSION['mensaje']['text'] ?>',
            icon: '<?= strtolower($_SESSION['mensaje']['title']) ?>',
            timer: 3000,
            showConfirmButton: false,
            icon: 'success'
        });
    <?php unset($_SESSION['mensaje']); endif; ?>
</script>
