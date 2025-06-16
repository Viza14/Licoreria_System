<style>
    /* ... (tus estilos actuales del header) ... */

    /* ==================== SIDEBAR ==================== */
    #sidebar {
        background: #1d2a58;
        transition: all 0.3s ease;
    }

    .sidebar-menu {
        padding-top: 20px;
    }

    .sidebar-menu a {
        color: #e0e0e0;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .sidebar-menu a:hover,
    .sidebar-menu a:focus {
        color: white;
        background: rgba(242, 124, 31, 0.2);
        text-decoration: none;
    }

    .sidebar-menu .fa {
        width: 20px;
        margin-right: 10px;
        text-align: center;
        transition: transform 0.2s ease;
    }

    .sidebar-menu a:hover .fa {
        color: #f27c1f;
        transform: scale(1.2);
    }

    .sidebar-menu .active a {
        background: rgba(242, 124, 31, 0.3);
        color: white;
        border-left: 3px solid #f27c1f;
    }

    .sidebar-menu .active a .fa {
        color: #f27c1f;
    }

    /* Submenús */
    .sidebar-menu .sub-menu {
        background: rgba(0, 0, 0, 0.1);
    }

    .sidebar-menu .sub-menu a {
        padding-left: 35px;
        color: #b0b0b0;
        border-left: 3px solid transparent;
    }

    .sidebar-menu .sub-menu a:hover {
        color: white;
        border-left: 3px solid #f27c1f;
    }

    .sidebar-menu .sub-menu .active a {
        color: white;
        background: rgba(242, 124, 31, 0.2);
    }

    /* Foto de perfil */
    .img-circle {
        border: 3px solid #f27c1f;
        transition: all 0.3s ease;
    }

    /* Estilo para el icono SVG en el sidebar */
    .img-circle[src*=".svg"] {
        filter: brightness(0) invert(1);
        /* Opcional: suavizar el borde del SVG */
        padding: 5px;
    }

    .img-circle:hover {
        transform: scale(1.05);
        box-shadow: 0 0 10px rgba(242, 124, 31, 0.5);
    }

    .centered {
        color: white;
        text-align: center;
        margin-bottom: 20px;
    }
</style>


<!--sidebar start-->
<aside>
    <div id="sidebar" class="nav-collapse">
        <!-- sidebar menu start-->
        <ul class="sidebar-menu" id="nav-accordion">
            <p class="centered"><img src="<?php echo BASE_URL; ?>assets/img/account.svg" class="img-circle" width="80"></p>
            <h5 class="centered"><?php echo $_SESSION['user_nombre'] ?? 'Administrador'; ?></h5>

            <li class="mt">
                <a class="active" href="<?php echo BASE_URL; ?>index.php?action=dashboard">
                    <i class="fa fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Menú Productos -->
            <li class="sub-menu">
                <a href="javascript:;">
                    <i class="fa fa-glass"></i>
                    <span>Productos</span>
                </a>
                <ul class="sub">
                    <li><a href="<?php echo BASE_URL; ?>index.php?action=productos">Lista de Productos</a></li>
                    <?php if ($_SESSION['user_rol'] != 2): ?> <!-- Asumiendo que 2 es el rol de empleado -->
                        <li><a href="<?php echo BASE_URL; ?>index.php?action=producto-proveedor">Producto-Proveedor</a></li>
                        <li><a href="<?php echo BASE_URL; ?>index.php?action=categorias">Categorías</a></li>
                    <?php endif; ?>
                </ul>
            </li>

            <!-- Menú Ventas -->
            <li class="sub-menu">
                <a href="javascript:;">
                    <i class="fa fa-shopping-cart"></i>
                    <span>Ventas</span>
                </a>
                <ul class="sub">
                    <li><a href="<?php echo BASE_URL; ?>index.php?action=ventas">Nueva Venta</a></li>
                    <?php if ($_SESSION['user_rol'] != 2): ?> <!-- Asumiendo que 2 es el rol de empleado -->
                        <li><a href="<?php echo BASE_URL; ?>index.php?action=ventas&sub=historial">Historial</a></li>
                    <?php endif; ?>
                </ul>
            </li>

            <!-- Menú Clientes -->
            <li>
                <a href="<?php echo BASE_URL; ?>index.php?action=clientes">
                    <i class="fa fa-users"></i>
                    <span>Clientes</span>
                </a>
            </li>

            <!-- Menú Reportes -->
            <?php if ($_SESSION['user_rol'] != 2): ?> <!-- Asumiendo que 2 es el rol de empleado -->
                <li class="sub-menu">
                    <a href="javascript:;">
                        <i class="fa fa-bar-chart-o"></i>
                        <span>Reportes</span>
                    </a>
                    <ul class="sub">
                        <li><a href="<?php echo BASE_URL; ?>index.php?action=reportes&sub=ventas">Ventas</a></li>
                        <li><a href="<?php echo BASE_URL; ?>index.php?action=reportes&sub=inventario">Inventario</a></li>
                    </ul>
                </li>
            <?php endif; ?>

            <li class="sub-menu">
                <a href="javascript:;">
                    <i class="fa fa-exchange"></i>
                    <span>Movimientos</span>
                </a>
                <ul class="sub">
                    <li><a href="<?= BASE_URL ?>index.php?action=movimientos-inventario">Historial</a></li>
                    <li><a href="<?= BASE_URL ?>index.php?action=movimientos-inventario&method=resumen">Resumen</a></li>
                    <!--                     <li><a href="<?= BASE_URL ?>index.php?action=productos&method=registrarEntrada">Entrada Productos</a></li> -->
                </ul>
            </li>

            <!-- Menú Configuración -->
            <?php if ($_SESSION['user_rol'] != 2): ?> <!-- Asumiendo que 2 es el rol de empleado -->
                <li class="sub-menu">
                    <a href="javascript:;">
                        <i class="fa fa-cogs"></i>
                        <span>Configuración</span>
                    </a>
                    <ul class="sub">
                        <li> <a href="<?php echo BASE_URL; ?>index.php?action=usuarios"><i class="fa fa-users"></i><span>Usuarios</span></a></li>
                        <li><a href="<?php echo BASE_URL; ?>index.php?action=proveedores">Proveedores</a></li>
                        <li><a href="<?php echo BASE_URL; ?>index.php?action=gestion-stock"><i class="fa fa-cubes"></i> Gestión de Stock</a></li>
                        </li>
                    </ul>

                </li>
            <?php endif; ?>
        </ul>
        <!-- sidebar menu end-->
    </div>
</aside>
<!--sidebar end-->