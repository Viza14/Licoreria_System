<?php
$total_registros = isset($total_registros) ? $total_registros : count($movimientos);
?>
<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--breadcrumbs start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-exchange"></i> Movimientos de Inventario</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><i class="fa fa-exchange"></i> Movimientos</li>
                </ol>
            </div>
        </div>
        <!--breadcrumbs end-->

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <i class="fa fa-history"></i> Historial de Movimientos
                        <div class="pull-right">
                            <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#filtrosModal">
                                <i class="fa fa-filter"></i> Filtros
                            </button>
                            <?php if ($_SESSION['user_rol'] != 2): ?>
                                <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=resumen" class="btn btn-info btn-xs">
                                    <i class="fa fa-pie-chart"></i> Resumen
                                </a>
                            <?php endif; ?>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <input type="text" id="busqueda" class="form-control" placeholder="Buscar por producto, usuario..." value="<?= isset($filtros_activos['busqueda']) ? htmlspecialchars($filtros_activos['busqueda']) : '' ?>">
                            </div>
                        </div>

                        <div id="sin-resultados" class="alert alert-warning text-center" style="display: none;">
                            <i class="fa fa-exclamation-circle"></i> No se encontraron movimientos que coincidan con los criterios de búsqueda
                        </div>

                        <div class="table-responsive">
                            <table id="tablaMovimientos" class="table table-striped table-advance table-hover">
                                <thead>
                                    <tr>
                                        <th><i class="fa fa-calendar"></i> Fecha</th>
                                        <th><i class="fa fa-box"></i> Producto</th>
                                        <th><i class="fa fa-exchange"></i> Tipo</th>
                                        <th><i class="fa fa-sort-numeric-up"></i> Cantidad</th>
                                        <th><i class="fa fa-money-bill"></i> Precio</th>
                                        <th><i class="fa fa-user"></i> Usuario</th>
                                        <th><i class="fa fa-hashtag"></i> Referencia</th>
                                        <th><i class="fa fa-cogs"></i> Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($movimientos as $movimiento): ?>
                                        <?php
                                        $tiene_ajuste = isset($movimiento['tiene_ajuste']) ? $movimiento['tiene_ajuste'] : 0;
                                        $es_ajuste = $movimiento['tipo_movimiento'] == 'AJUSTE';
                                        $es_inactivo = $movimiento['id_estatus'] == 2;
                                        ?>
                                        <tr class="<?php
                                            if ($es_inactivo || ($tiene_ajuste > 0 && ($movimiento['tipo_movimiento'] == 'SALIDA' || $movimiento['tipo_movimiento'] == 'ENTRADA'))) {
                                                echo 'inactive-movement';
                                            } elseif ($es_ajuste) {
                                                echo 'adjustment-movement';
                                            } else {
                                                echo '';
                                            }
                                            ?>">
                                            <td><?= date('d/m/Y h:i A', strtotime($movimiento['fecha_movimiento'])); ?></td>
                                            <td><?= htmlspecialchars($movimiento['producto']); ?></td>
                                            <td>
                                                <span class="label label-<?= $movimiento['tipo_movimiento'] == 'ENTRADA' ? 'success' : ($movimiento['tipo_movimiento'] == 'SALIDA' ? 'danger' : 'warning'); ?>">
                                                    <?= $movimiento['tipo_movimiento']; ?>
                                                </span>
                                                <?php if ($es_inactivo): ?>
                                                    <span class="label label-default">Inactivo</span>
                                                <?php elseif ($tiene_ajuste > 0): ?>
                                                    <span class="label label-info">Ajustado</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $movimiento['cantidad']; ?></td>
                                            <td class="<?= $movimiento['tipo_movimiento'] == 'ENTRADA' ? 'text-success' : ($movimiento['tipo_movimiento'] == 'SALIDA' ? 'text-danger' : ''); ?>">
                                                <?= number_format($movimiento['precio_unitario'], 2, ',', '.'); ?> Bs
                                            </td>
                                            <td><?= $movimiento['usuario']; ?></td>
                                            <td><?= $movimiento['referencia'] ?? 'N/A'; ?></td>
                                            <td>
                                                <?php if (!$es_inactivo && $tiene_ajuste == 0): ?>
                                                    <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=mostrar&id=<?= $movimiento['id']; ?>"
                                                        class="btn btn-success btn-xs" title="Ver Detalles">
                                                        <i class="fa fa-eye"></i>
                                                    </a>

                                                    <?php if (($movimiento['tipo_movimiento'] == 'SALIDA' && $movimiento['tipo_referencia'] == 'VENTA') ||
                                                        ($movimiento['tipo_movimiento'] == 'AJUSTE' && $movimiento['tipo_referencia'] == 'VENTA')
                                                    ): ?>
                                                        <?php if ($_SESSION['user_rol'] == 2): ?>
                                                            <button class="btn btn-primary btn-xs" title="Modificar Venta" 
                                                                onclick="solicitarAutorizacion('<?= $movimiento['id_referencia'] ?>', 'venta')">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                        <?php else: ?>
                                                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=modificarVenta&id=<?= $movimiento['id_referencia'] ?>"
                                                                class="btn btn-primary btn-xs" title="Modificar Venta">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    <?php elseif (
                                                        $movimiento['tipo_movimiento'] == 'ENTRADA' ||
                                                        ($movimiento['tipo_movimiento'] == 'AJUSTE' && $movimiento['tipo_referencia'] != 'VENTA')
                                                    ): ?>
                                                        <?php if ($_SESSION['user_rol'] != 2): ?>
                                                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=editar&id=<?= $movimiento['id'] ?>"
                                                                class="btn btn-primary btn-xs" title="Editar Entrada">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                <?php elseif ($tiene_ajuste > 0 || $es_inactivo): ?>
                                                    <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=mostrar&id=<?= $movimiento['id'] ?>"
                                                        class="btn btn-info btn-xs" title="Ver Detalle">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($total_paginas > 1): ?>
                            <div class="text-center">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        <?php if ($pagina_actual > 1): ?>
                                            <li>
                                                <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&pagina=<?= $pagina_actual - 1 ?>" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                            <li class="<?= $i == $pagina_actual ? 'active' : '' ?>">
                                                <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&pagina=<?= $i ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($pagina_actual < $total_paginas): ?>
                                            <li>
                                                <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&pagina=<?= $pagina_actual + 1 ?>" aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                                <div class="text-muted">
                                    Mostrando <?= count($movimientos) ?> de <?= $total_registros ?> registros
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>

<!-- Modal de Autorización -->
<div class="modal fade" id="autorizacionModal" tabindex="-1" role="dialog" aria-labelledby="autorizacionModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="autorizacionModalLabel"><i class="fa fa-lock"></i> Autorización Requerida</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> Se requiere autorización de un administrador para continuar.
                </div>
                <form id="formAutorizacion">
                    <input type="hidden" id="idReferencia" name="idReferencia">
                    <input type="hidden" id="tipoOperacion" name="tipoOperacion">
                    <div class="form-group">
                        <label for="usuario">Usuario Administrador</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="verificarAutorizacion()">Verificar</button>
            </div>
        </div>
    </div>
</div>

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
                        <label>Tipo de Movimiento</label>
                        <select class="form-control" id="filtroTipo">
                            <option value="">Todos los tipos</option>
                            <option value="ENTRADA" <?= isset($filtros_activos['tipos']) && in_array('ENTRADA', $filtros_activos['tipos']) ? 'selected' : '' ?>>Entrada</option>
                            <option value="SALIDA" <?= isset($filtros_activos['tipos']) && in_array('SALIDA', $filtros_activos['tipos']) ? 'selected' : '' ?>>Salida</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select class="form-control" id="filtroEstatus">
                            <option value="">Todos los estados</option>
                            <option value="1" <?= isset($filtros_activos['estados']) && in_array('1', $filtros_activos['estados']) ? 'selected' : '' ?>>Activo</option>
                            <option value="2" <?= isset($filtros_activos['estados']) && in_array('2', $filtros_activos['estados']) ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Rango de Fechas</label>
                        <div class="input-group">
                            <input type="date" class="form-control" id="filtroFechaInicio" placeholder="Fecha Inicio" value="<?= isset($filtros_activos['fecha_inicio']) ? htmlspecialchars($filtros_activos['fecha_inicio']) : '' ?>">
                            <span class="input-group-addon">hasta</span>
                            <input type="date" class="form-control" id="filtroFechaFin" placeholder="Fecha Fin" value="<?= isset($filtros_activos['fecha_fin']) ? htmlspecialchars($filtros_activos['fecha_fin']) : '' ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Rango de Precios</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="filtroPrecioMin" placeholder="Precio Mínimo" min="0" step="0.01">
                            <span class="input-group-addon">a</span>
                            <input type="number" class="form-control" id="filtroPrecioMax" placeholder="Precio Máximo" min="0" step="0.01">
                        </div>
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

<script>
$(document).ready(function() {
    // Real-time search functionality with debounce
    let searchTimeout;
    $('#busqueda').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            aplicarFiltros();
        }, 500);
    });

    // Filter functionality
    function aplicarFiltros() {
        const searchValue = $('#busqueda').val().trim();
        const tipo = $('#filtroTipo').val();
        const estatus = $('#filtroEstatus').val();
        const fechaInicio = $('#filtroFechaInicio').val();
        const fechaFin = $('#filtroFechaFin').val();

        // Construir la URL con los filtros
        let url = '<?= BASE_URL ?>index.php?action=movimientos-inventario';
        
        if (searchValue) url += '&busqueda=' + encodeURIComponent(searchValue);
        if (tipo) url += '&tipos=' + encodeURIComponent(tipo);
        if (estatus) url += '&estados=' + encodeURIComponent(estatus);
        if (fechaInicio) url += '&fecha_inicio=' + encodeURIComponent(fechaInicio);
        if (fechaFin) url += '&fecha_fin=' + encodeURIComponent(fechaFin);

        // Redirigir a la URL con los filtros
        window.location.href = url;
    }

    $('#aplicarFiltros').click(function() {
        aplicarFiltros();
        $('#filtrosModal').modal('hide');
    });

    // Clear filters
    $('#limpiarFiltros').click(function() {
        window.location.href = '<?= BASE_URL ?>index.php?action=movimientos-inventario';
    });
});

function solicitarAutorizacion(id, tipo) {
    document.getElementById('idReferencia').value = id;
    document.getElementById('tipoOperacion').value = tipo;
    $('#autorizacionModal').modal('show');
}

function verificarAutorizacion() {
    const formData = new FormData(document.getElementById('formAutorizacion'));
    
    fetch('<?= BASE_URL ?>index.php?action=auth&method=verificarAdmin', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const id = document.getElementById('idReferencia').value;
            const tipo = document.getElementById('tipoOperacion').value;
            
            if (tipo === 'venta') {
                window.location.href = `<?= BASE_URL ?>index.php?action=movimientos-inventario&method=modificarVenta&id=${id}`;
            }
            $('#autorizacionModal').modal('hide');
        } else {
            Swal.fire({
                title: 'Error',
                text: 'Credenciales incorrectas. Por favor, intente nuevamente.',
                icon: 'error'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error',
            text: 'Ocurrió un error al verificar las credenciales.',
            icon: 'error'
        });
    });
}
</script>

<?php if (isset($_SESSION['mensaje'])): ?>
<script>
    Swal.fire({
        title: '<?= $_SESSION['mensaje']['title'] ?>',
        text: '<?= $_SESSION['mensaje']['text'] ?>',
        icon: '<?= $_SESSION['mensaje']['icon'] ?>',
        timer: 3000,
        timerProgressBar: true
    });
</script>
<?php unset($_SESSION['mensaje']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<script>
    Swal.fire({
        title: '<?= $_SESSION['error']['title'] ?>',
        text: '<?= $_SESSION['error']['text'] ?>',
        icon: '<?= $_SESSION['error']['icon'] ?>',
        timer: 3000,
        timerProgressBar: true
    });
</script>
<?php unset($_SESSION['error']); ?>
<?php endif; ?>