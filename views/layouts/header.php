<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Gestión para Licorería">
    <meta name="author" content="TuNombre">
    <title>Licorería DashGum | <?php echo $pageTitle ?? 'Dashboard'; ?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo BASE_URL; ?>assets/css/bootstrap.css" rel="stylesheet">
    <!--external css-->
    <link href="<?php echo BASE_URL; ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>assets/css/zabuto_calendar.css">
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>assets/js/gritter/css/jquery.gritter.css">
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>assets/lineicons/style.css">

    <!-- Custom styles for this template -->
    <link href="<?php echo BASE_URL; ?>assets/css/style.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/css/style-responsive.css" rel="stylesheet">

    <script src="<?php echo BASE_URL; ?>assets/js/chart-master/Chart.js"></script>



    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        .black-bg {
            background: #1d2a58 !important;
            border-bottom: 3px solid #f27c1f;
        }

        .logo b {
            color: #f27c1f;
        }

        /* Sombra en el header */
        .header {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        /* Botón de cerrar sesión */
        .logout {
            background: #f27c1f !important;
            color: white !important;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .logout:hover {
            background-color: #d86600 !important;
            transform: scale(1.05);
        }

        /* Hover al botón de toggle (fa-bars) */
        .sidebar-toggle-box .fa-bars {
            cursor: pointer;
            transition: color 0.3s ease, transform 0.2s ease;
        }

        .sidebar-toggle-box .fa-bars:hover {
            color: #f27c1f;
            transform: scale(1.2);
        }

        /* Hover al botón de notificaciones (fa-bell-o) */
        .dropdown-toggle .fa-bell-o {
            cursor: pointer;
            transition: color 0.3s ease, transform 0.2s ease;
        }

        .dropdown-toggle .fa-bell-o:hover {
            color: #f27c1f;
            transform: scale(1.2);
        }

        /* Opcional: efecto para el badge de notificaciones */
        .dropdown-toggle .badge {
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .dropdown-toggle:hover .badge {
            background-color: #f27c1f !important;
            transform: scale(1.1);
        }

        /* Otros botones generales (opcional) */
        .btn,
        button,
        input[type="submit"] {
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn:hover,
        button:hover,
        input[type="submit"]:hover {
            transform: scale(1.05);
            opacity: 0.95;
        }
    </style>

</head>

<body>
    <section id="container">
        <!--header start-->
        <header class="header black-bg">
            <div class="sidebar-toggle-box">
                <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
            </div>
            <!--logo start-->
            <a class="logo"><b>LICORERÍA <span>La Manguita C.A</span></b></a>
            <!--logo end-->
            <div class="nav notify-row" id="top_menu">
                <!--  notification start -->
                <ul class="nav top-menu">
                    <!-- alerts dropdown start-->
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <i class="fa fa-bell-o"></i>
                            <span class="badge bg-theme">4</span>
                        </a>
                        <ul class="dropdown-menu extended notification">
                            <div class="notify-arrow notify-arrow-green"></div>
                            <li>
                                <p class="green">Tienes 4 notificaciones</p>
                            </li>
                            <li>
                                <a href="#">
                                    <span class="label label-danger"><i class="fa fa-bolt"></i></span>
                                    Stock bajo en productos.
                                    <span class="small italic">Hace 5 min</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span class="label label-warning"><i class="fa fa-bell"></i></span>
                                    Ventas pendientes.
                                    <span class="small italic">Hace 30 min</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span class="label label-success"><i class="fa fa-plus"></i></span>
                                    Nuevo cliente registrado.
                                    <span class="small italic">Hace 1 hora</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span class="label label-info"><i class="fa fa-bullhorn"></i></span>
                                    Inventario actualizado.
                                    <span class="small italic">Hoy</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">Ver todas las notificaciones</a>
                            </li>
                        </ul>
                    </li>
                    <!-- alerts dropdown end -->
                </ul>
                <!--  notification end -->
            </div>
            <div class="top-menu">
                <ul class="nav pull-right top-menu">
                    <li><a class="logout" href="<?php echo BASE_URL; ?>index.php?action=logout">Cerrar Sesión</a></li>
                </ul>
            </div>
        </header>
        <!--header end-->