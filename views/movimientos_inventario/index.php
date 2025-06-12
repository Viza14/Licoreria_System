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
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($movimiento['fecha_movimiento'])); ?></td>
                                        <td><?= htmlspecialchars($movimiento['producto']); ?></td>
                                        <td>
                                            <span class="label label-<?= $movimiento['tipo_movimiento'] == 'ENTRADA' ? 'success' : ($movimiento['tipo_movimiento'] == 'SALIDA' ? 'danger' : 'warning'); ?>">
                                                <?= $movimiento['tipo_movimiento']; ?>
                                            </span>
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