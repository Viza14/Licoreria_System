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
                        Historial de Movimientos
                        <div class="pull-right">
                            <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#filtrosModal">
                                <i class="fa fa-filter"></i> Filtros
                            </button>
                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=resumen" class="btn btn-info btn-xs">
                                <i class="fa fa-pie-chart"></i> Resumen
                            </a>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <input type="text" id="busqueda" class="form-control" placeholder="Buscar por producto, usuario...">
                            </div>
                        </div>
                        
                        <div id="filtros-activos" class="alert alert-info" style="display: none; margin-bottom: 10px;">
                            <i class="fa fa-filter"></i> Filtros activos: <span id="texto-filtros-activos"></span>
                            <div class="pull-right">
                                <strong>Resultados encontrados: <span id="contador-resultados">0</span></strong>
                            </div>
                        </div>

                        <div id="sin-resultados" class="alert alert-warning text-center" style="display: none;">
                            <i class="fa fa-exclamation-circle"></i> No se encontraron movimientos que coincidan con los criterios de búsqueda
                        </div>
                        
                        <div class="alert alert-info" style="padding: 8px;">
                            <strong>Leyenda:</strong>
                            <span class="legend-item" style="margin-left: 10px; padding: 2px 8px; background-color: #f5f5f5; color: #777; font-style: italic;">Movimiento inactivo</span>
                            <span class="legend-item" style="margin-left: 10px; padding: 2px 8px; background-color: #fff8e1; color: #856404;">Movimiento de ajuste</span>
                            <span class="legend-item" style="margin-left: 10px; padding: 2px 8px; background-color: #f8f9fa; border-left: 3px solid #ffc107;">Movimiento ajustado</span>
                        </div>

                        <table id="tablaMovimientos" class="table table-striped table-advance table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Producto</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Usuario</th>
                                    <th>Referencia</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movimientos as $movimiento): ?>
                                    <?php 
                                    // Determine if has adjustment
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
                                        <td><?= date('d/m/Y H:i', strtotime($movimiento['fecha_movimiento'])); ?></td>
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
                                        <td><?= number_format($movimiento['precio_unitario'], 2, ',', '.'); ?> Bs</td>
                                        <td><?= $movimiento['usuario']; ?></td>
                                        <td><?= $movimiento['referencia'] ?? 'N/A'; ?></td>
                                        <td>
                                            <?php if (!$es_inactivo && $tiene_ajuste == 0): ?>
                                                <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=mostrar&id=<?= $movimiento['id']; ?>"
                                                    class="btn btn-success btn-xs" title="Ver Detalles">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                
                                                <?php if (($movimiento['tipo_movimiento'] == 'SALIDA' && $movimiento['tipo_referencia'] == 'VENTA') || 
                                                          ($movimiento['tipo_movimiento'] == 'AJUSTE' && $movimiento['tipo_referencia'] == 'VENTA')): ?>
                                                    <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=modificarVenta&id=<?= $movimiento['id_referencia'] ?>" 
                                                       class="btn btn-primary btn-xs" title="Modificar Venta">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                <?php elseif ($movimiento['tipo_movimiento'] == 'ENTRADA' || 
                                                            ($movimiento['tipo_movimiento'] == 'AJUSTE' && $movimiento['tipo_referencia'] != 'VENTA')): ?>
                                                    <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=editar&id=<?= $movimiento['id'] ?>" 
                                                       class="btn btn-primary btn-xs" title="Editar Entrada">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php elseif ($tiene_ajuste > 0): ?>
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
                </section>
            </div>
        </div>
    </section>
</section>

<style>
    /* Estilo para movimientos inactivos */
    .inactive-movement {
        background-color: #f5f5f5;
        color: #777;
        font-style: italic;
    }
    
    .inactive-movement td {
        opacity: 0.8;
    }
    
    .inactive-movement .label {
        opacity: 0.7;
    }
    
    /* Estilo para movimientos de ajuste */
    .adjustment-movement {
        background-color: #fff8e1; /* Fondo amarillo claro */
        color: #856404;
    }
    
    .adjustment-movement td {
        border-left: 2px solid #ffeeba;
    }
    
    .adjustment-movement .label-warning {
        background-color: #f0ad4e;
    }
    
    /* Estilo para movimientos relacionados con ajustes */
    .related-to-adjustment {
        background-color: #f8f9fa; /* Fondo gris muy claro */
        border-left: 3px solid #ffc107; /* Borde izquierdo amarillo */
    }
    
    .related-to-adjustment td:first-child {
        position: relative;
    }
    
    .related-to-adjustment td:first-child::before {
        content: "";
        position: absolute;
        left: -3px;
        top: 0;
        height: 100%;
        width: 3px;
        background-color: #ffc107;
    }
</style>

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
                        <label>Fecha Desde:</label>
                        <input type="date" class="form-control" id="filtroFechaDesde">
                    </div>
                    <div class="form-group">
                        <label>Fecha Hasta:</label>
                        <input type="date" class="form-control" id="filtroFechaHasta">
                    </div>
                    <div class="form-group">
                        <label>Tipo de Movimiento:</label>
                        <select class="form-control" id="filtroTipo">
                            <option value="">Todos los tipos</option>
                            <option value="ENTRADA">Entrada</option>
                            <option value="SALIDA">Salida</option>
                            <option value="AJUSTE">Ajuste</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Estatus:</label>
                        <select class="form-control" id="filtroEstatus">
                            <option value="">Todos los estatus</option>
                            <option value="1">Activo</option>
                            <option value="2">Inactivo</option>
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

<script>
    $(document).ready(function() {
        // Función para actualizar el contador y mensajes
        function actualizarContador(resultados, mostrarFiltros = true) {
            $('#contador-resultados').text(resultados);
            
            if (resultados === 0) {
                $('#sin-resultados').show();
            } else {
                $('#sin-resultados').hide();
            }

            if (mostrarFiltros || resultados !== $('#tablaMovimientos tbody tr').length) {
                $('#filtros-activos').show();
            } else {
                $('#filtros-activos').hide();
            }
        }

        // Búsqueda en tiempo real
        $('#busqueda').on('input', function() {
            const searchValue = $(this).val().trim().toLowerCase();
            let resultados = 0;

            $('#tablaMovimientos tbody tr').each(function() {
                const producto = $(this).find('td:eq(1)').text().toLowerCase();
                const usuario = $(this).find('td:eq(5)').text().toLowerCase();
                const referencia = $(this).find('td:eq(6)').text().toLowerCase();

                const match = producto.includes(searchValue) || 
                             usuario.includes(searchValue) || 
                             referencia.includes(searchValue);

                if (match) {
                    $(this).show();
                    resultados++;
                } else {
                    $(this).hide();
                }
            });

            // Actualizar texto de filtros para búsqueda
            if (searchValue) {
                $('#texto-filtros-activos').text(`Búsqueda: ${searchValue}`);
            }
            
            actualizarContador(resultados, searchValue.length > 0);
        });
        
        // Inicializar tooltips para movimientos relacionados
        $('.related-to-adjustment').tooltip({
            title: 'Este movimiento ha sido ajustado posteriormente',
            placement: 'top',
            container: 'body'
        });
        
        $('.adjustment-movement').tooltip({
            title: 'Este es un movimiento de ajuste',
            placement: 'top',
            container: 'body'
        });
        
        $('.inactive-movement').tooltip({
            title: 'Este movimiento está inactivo debido a un ajuste',
            placement: 'top',
            container: 'body'
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

        // Filtros avanzados
        $('#aplicarFiltros').click(function() {
            const fechaDesde = $('#filtroFechaDesde').val();
            const fechaHasta = $('#filtroFechaHasta').val();
            const tipo = $('#filtroTipo').val();
            const estatus = $('#filtroEstatus').val();
            let resultados = 0;

            $('#tablaMovimientos tbody tr').each(function() {
                const fecha = $(this).find('td:eq(0)').text();
                // Obtener solo el texto del primer label (ENTRADA, SALIDA o AJUSTE)
                const tipoMovimiento = $(this).find('td:eq(2) .label').first().clone()
                    .children().remove().end()
                    .text().trim();
                const estatusMovimiento = $(this).hasClass('inactive-movement') ? '2' : '1';

                // Convertir fecha del formato dd/mm/yyyy HH:mm a yyyy-mm-dd para comparación
                const fechaPartes = fecha.split(' ')[0].split('/');
                const fechaComparar = `${fechaPartes[2]}-${fechaPartes[1].padStart(2, '0')}-${fechaPartes[0].padStart(2, '0')}`;

                console.log('Comparando:', {
                    fecha: fechaComparar,
                    fechaDesde: fechaDesde,
                    fechaHasta: fechaHasta,
                    tipoMovimiento: tipoMovimiento,
                    tipo: tipo,
                    estatusMovimiento: estatusMovimiento,
                    estatus: estatus
                });

                const matchFechaDesde = !fechaDesde || fechaComparar >= fechaDesde;
                const matchFechaHasta = !fechaHasta || fechaComparar <= fechaHasta;
                const matchTipo = !tipo || tipoMovimiento.toUpperCase().trim() === tipo.toUpperCase().trim();
                const matchEstatus = !estatus || estatusMovimiento === estatus;

                console.log('Resultados:', {
                    matchFechaDesde,
                    matchFechaHasta,
                    matchTipo,
                    matchEstatus
                });

                if (matchFechaDesde && matchFechaHasta && matchTipo && matchEstatus) {
                    $(this).show();
                    resultados++;
                } else {
                    $(this).hide();
                }
            });

            // Actualizar indicador de filtros activos y contador
            const filtrosTexto = [];
            if (fechaDesde) filtrosTexto.push(`Desde: ${fechaDesde}`);
            if (fechaHasta) filtrosTexto.push(`Hasta: ${fechaHasta}`);
            if (tipo) filtrosTexto.push(`Tipo: ${tipo}`);
            if (estatus) filtrosTexto.push(`Estatus: ${estatus === '1' ? 'Activo' : 'Inactivo'}`);

            if (filtrosTexto.length > 0) {
                $('#texto-filtros-activos').text(filtrosTexto.join(' | '));
            }
            
            actualizarContador(resultados, filtrosTexto.length > 0);

            $('#filtrosModal').modal('hide');
        });

        // Limpiar filtros
        $('#limpiarFiltros').click(function() {
            $('#formFiltros')[0].reset();
            $('#busqueda').val('');
            $('#tablaMovimientos tbody tr').show();
            $('#texto-filtros-activos').text('');
            actualizarContador($('#tablaMovimientos tbody tr').length, false);
        });
    });
</script>
<!--main content end-->