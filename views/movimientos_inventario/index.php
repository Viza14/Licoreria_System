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
                                    <tr class="<?php
                                        if ($movimiento['id_estatus'] == 2) {
                                            echo 'inactive-movement';
                                        } elseif ($movimiento['tipo_movimiento'] == 'AJUSTE') {
                                            echo 'adjustment-movement';
                                        } elseif ($movimiento['tiene_ajuste'] > 0) {
                                            echo 'related-to-adjustment';
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
                                            <?php if ($movimiento['id_estatus'] == 2): ?>
                                                <span class="label label-default">Inactivo</span>
                                            <?php elseif ($movimiento['tiene_ajuste'] > 0): ?>
                                                <span class="label label-info">Ajustado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $movimiento['cantidad']; ?></td>
                                        <td><?= number_format($movimiento['precio_unitario'], 2, ',', '.'); ?> Bs</td>
                                        <td><?= $movimiento['usuario']; ?></td>
                                        <td><?= $movimiento['referencia'] ?? 'N/A'; ?></td>
                                        <td>
                                            <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=mostrar&id=<?= $movimiento['id']; ?>"
                                                class="btn btn-success btn-xs" title="Ver Detalles">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <?php if ($movimiento['id_estatus'] == 1 && $movimiento['tiene_ajuste'] == 0): ?>
                                                <?php if ($movimiento['tipo_movimiento'] == 'SALIDA' && $movimiento['tipo_referencia'] == 'VENTA'): ?>
                                                    <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=modificarVenta&id=<?= $movimiento['id_referencia'] ?>" 
                                                       class="btn btn-primary btn-xs" title="Modificar Venta">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                <?php elseif ($movimiento['tipo_movimiento'] == 'ENTRADA'): ?>
                                                    <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=editar&id=<?= $movimiento['id'] ?>" 
                                                       class="btn btn-primary btn-xs" title="Editar Entrada">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=editar&id=<?= $movimiento['id'] ?>" 
                                                       class="btn btn-primary btn-xs" title="Editar">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php elseif ($movimiento['tiene_ajuste'] > 0): ?>
                                                <a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=mostrar&id=<?= $movimiento['id_ajuste_relacionado'] ?>" 
                                                   class="btn btn-info btn-xs" title="Ver Ajuste">
                                                    <i class="fa fa-exchange"></i>
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

<script>
    $(document).ready(function() {
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
    });
</script>
<!--main content end-->