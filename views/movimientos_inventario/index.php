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
                                <input type="text" id="busqueda" class="form-control" placeholder="Buscar por producto, usuario..." value="<?= isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : '' ?>">
                            </div>
                        </div>

                        <div id="filtros-activos" class="alert alert-info" style="<?= (isset($_GET['busqueda']) || isset($_GET['tipos']) || isset($_GET['estados']) || isset($_GET['fecha_inicio']) || isset($_GET['fecha_fin'])) ? 'display: block;' : 'display: none;' ?> margin-bottom: 10px;">
                            <i class="fa fa-filter"></i> Filtros activos: <span id="texto-filtros-activos"><?php
                                $filtros_texto = [];
                                if (isset($_GET['busqueda'])) {
                                    $filtros_texto[] = 'Término: "' . htmlspecialchars($_GET['busqueda']) . '"';
                                }
                                if (isset($_GET['tipos'])) {
                                    $filtros_texto[] = 'Tipos: ' . htmlspecialchars($_GET['tipos']);
                                }
                                if (isset($_GET['estados'])) {
                                    $filtros_texto[] = 'Estados: ' . htmlspecialchars($_GET['estados']);
                                }
                                if (isset($_GET['fecha_inicio']) || isset($_GET['fecha_fin'])) {
                                    $filtros_texto[] = 'Fechas: ' . 
                                        (isset($_GET['fecha_inicio']) ? htmlspecialchars($_GET['fecha_inicio']) : 'Inicio') . ' - ' . 
                                        (isset($_GET['fecha_fin']) ? htmlspecialchars($_GET['fecha_fin']) : 'Fin');
                                }
                                echo implode(' | ', $filtros_texto);
                            ?></span>
                            <div class="pull-right">
                                <strong><i class="fa fa-list"></i> Resultados encontrados: <span id="contador-resultados"><?= count($movimientos) ?></span></strong>
                            </div>
                        </div>

                        <div id="sin-resultados" class="alert alert-warning text-center" style="<?= empty($movimientos) ? 'display: block;' : 'display: none;' ?>">
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
                        <!-- Paginación -->
                        <?php if ($total_paginas > 1): ?>
                            <div class="text-center">
                                <ul class="pagination pagination-sm">
                                    <?php if ($pagina_actual > 1): ?>
                                        <li>
                                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=index&pagina=<?= $pagina_actual - 1 ?>">
                                                <i class="fa fa-angle-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                        <li class="<?= $i == $pagina_actual ? 'active' : '' ?>">
                                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=index&pagina=<?= $i ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($pagina_actual < $total_paginas): ?>
                                        <li>
                                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=index&pagina=<?= $pagina_actual + 1 ?>">
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
                <h4 class="modal-title" id="filtrosModalLabel"><i class="fa fa-filter"></i> Filtros de Búsqueda</h4>
            </div>
            <div class="modal-body">
                <form id="formFiltros">
                    <div class="form-group">
                        <label>Tipo de Movimiento</label>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="tipo_movimiento[]" value="ENTRADA"> Entradas
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="tipo_movimiento[]" value="SALIDA"> Salidas
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="estado[]" value="activo"> Activos
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="estado[]" value="inactivo"> Inactivos
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Rango de Fechas</label>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="date" class="form-control" name="fecha_inicio" placeholder="Fecha Inicio">
                            </div>
                            <div class="col-md-6">
                                <input type="date" class="form-control" name="fecha_fin" placeholder="Fecha Fin">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="limpiarFiltros()">Limpiar Filtros</button>
                <button type="button" class="btn btn-primary" onclick="aplicarFiltros()">Aplicar Filtros</button>
            </div>
        </div>
    </div>
</div>

<script>
// Función para búsqueda en tiempo real con paginación
let searchTimeout;
document.getElementById('busqueda').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    
    // Clear previous timeout
    clearTimeout(searchTimeout);
    
    // Set new timeout to prevent multiple rapid requests
    searchTimeout = setTimeout(function() {
        // En lugar de ocultar filas, redirigir al servidor con el término de búsqueda
        const currentUrl = new URL(window.location.href);
        if (searchTerm) {
            currentUrl.searchParams.set('busqueda', searchTerm);
            currentUrl.searchParams.set('pagina', '1');
        } else {
            currentUrl.searchParams.delete('busqueda');
        }
        window.location.href = currentUrl.toString();
    }, 300); // Wait 300ms after user stops typing before filtering
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
            alert('Credenciales incorrectas. Por favor, intente nuevamente.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al verificar las credenciales.');
    });
}

function aplicarFiltros() {
    const formData = new FormData(document.getElementById('formFiltros'));
    const filtros = {};
    let filtrosActivos = [];

    // Recopilar tipos de movimiento seleccionados
    const tiposMovimiento = formData.getAll('tipo_movimiento[]');
    if (tiposMovimiento.length > 0) {
        filtros.tipos = tiposMovimiento;
        filtrosActivos.push(`Tipos: ${tiposMovimiento.join(', ')}`);
    }

    // Recopilar estados seleccionados
    const estados = formData.getAll('estado[]');
    if (estados.length > 0) {
        filtros.estados = estados;
        filtrosActivos.push(`Estados: ${estados.join(', ')}`);
    }

    // Recopilar fechas
    const fechaInicio = formData.get('fecha_inicio');
    const fechaFin = formData.get('fecha_fin');
    if (fechaInicio || fechaFin) {
        filtros.fechaInicio = fechaInicio;
        filtros.fechaFin = fechaFin;
        filtrosActivos.push(`Fechas: ${fechaInicio || 'Inicio'} - ${fechaFin || 'Fin'}`);
    }

    // Aplicar filtros a la tabla
    const tabla = document.getElementById('tablaMovimientos');
    const filas = tabla.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    let contadorResultados = 0;

    for (let fila of filas) {
        let mostrarFila = true;

        // Filtrar por tipo de movimiento
        if (filtros.tipos && filtros.tipos.length > 0) {
            const tipoMovimiento = fila.cells[2].textContent.trim();
            if (!filtros.tipos.some(tipo => tipoMovimiento.includes(tipo))) {
                mostrarFila = false;
            }
        }

        // Filtrar por estado
        if (mostrarFila && filtros.estados && filtros.estados.length > 0) {
            const esInactivo = fila.classList.contains('inactive-movement');
            const esAjustado = fila.classList.contains('adjustment-movement');
            const estado = esInactivo ? 'inactivo' : (esAjustado ? 'ajustado' : 'activo');
            if (!filtros.estados.includes(estado)) {
                mostrarFila = false;
            }
        }

        // Filtrar por fecha
        if (mostrarFila && (fechaInicio || fechaFin)) {
            const fecha = new Date(fila.cells[0].textContent.trim());
            if (fechaInicio && new Date(fechaInicio) > fecha) {
                mostrarFila = false;
            }
            if (fechaFin && new Date(fechaFin) < fecha) {
                mostrarFila = false;
            }
        }

        fila.style.display = mostrarFila ? '' : 'none';
        if (mostrarFila) contadorResultados++;
    }

    // Actualizar mensajes y contador
    document.getElementById('contador-resultados').textContent = contadorResultados;
    document.getElementById('filtros-activos').style.display = filtrosActivos.length > 0 ? 'block' : 'none';
    document.getElementById('texto-filtros-activos').textContent = filtrosActivos.join(' | ');
    document.getElementById('sin-resultados').style.display = contadorResultados === 0 ? 'block' : 'none';

    // Reset to first page when applying filters
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('pagina', '1');
    
    // Add filter parameters to URL
    if (tiposMovimiento.length > 0) currentUrl.searchParams.set('tipos', tiposMovimiento.join(','));
    if (estados.length > 0) currentUrl.searchParams.set('estados', estados.join(','));
    if (fechaInicio) currentUrl.searchParams.set('fecha_inicio', fechaInicio);
    if (fechaFin) currentUrl.searchParams.set('fecha_fin', fechaFin);
    
    // Mantener el término de búsqueda si existe
    const busquedaActual = document.getElementById('busqueda').value;
    if (busquedaActual) {
        currentUrl.searchParams.set('busqueda', busquedaActual);
    }
    
    window.history.pushState({}, '', currentUrl);

    $('#filtrosModal').modal('hide');
}

function limpiarFiltros() {
    document.getElementById('formFiltros').reset();
    document.getElementById('busqueda').value = '';
    
    // Redirigir a la página sin filtros
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.delete('tipos');
    currentUrl.searchParams.delete('estados');
    currentUrl.searchParams.delete('fecha_inicio');
    currentUrl.searchParams.delete('fecha_fin');
    currentUrl.searchParams.delete('busqueda');
    currentUrl.searchParams.set('pagina', '1');
    window.location.href = currentUrl.toString();
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