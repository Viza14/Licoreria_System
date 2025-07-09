<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "index.php?action=login");
    exit();
}
?>
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
             background-color: transparent !important;
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
        /* Hover al botón de notificaciones y backup */
        .dropdown-toggle .fa-bell-o,
        .dropdown-toggle .fa-download {
            cursor: pointer;
            transition: color 0.3s ease, transform 0.2s ease;
        }
        .dropdown-toggle .fa-bell-o:hover,
        .dropdown-toggle .fa-download:hover {
            color: #1d2a58;
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
                        <?php 
                        $notificaciones = obtenerNotificacionesStock();
                        $totalNotificaciones = $notificaciones['total'];
                        ?>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <i class="fa fa-bell-o"></i>
                            <?php if ($totalNotificaciones > 0): ?>
                                <span class="badge bg-theme"><?= $totalNotificaciones ?></span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu extended notification">
                            <div class="notify-arrow notify-arrow-green"></div>
                            <li>
                                <p class="green">Tienes <?= $totalNotificaciones ?> notificaciones</p>
                            </li>
                            
                            <?php if ($totalNotificaciones === 0): ?>
                                <li>
                                    <a href="#">
                                        <span class="label label-success"><i class="fa fa-check"></i></span>
                                        Todo está en orden
                                    </a>
                                </li>
                            <?php else: ?>
                                <?php foreach ($notificaciones['notificaciones'] as $notif): ?>
                                    <li>
                                        <a href="<?= $notif['url'] ?>">
                                            <span class="label label-<?= $notif['tipo'] ?>">
                                                <i class="fa fa-<?= $notif['icono'] ?>"></i>
                                            </span>
                                            <?= $notif['mensaje'] ?>
                                            <span class="small italic"><?= $notif['tiempo'] ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <li>
                                <a href="<?= BASE_URL ?>index.php?action=gestion-stock&method=alertas">
                                    Ver todas las notificaciones
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- alerts dropdown end -->
                    <?php if ($_SESSION['user_rol'] != 2): ?>
                    <li class="dropdown">
                        <a href="#" onclick="generarBackup(); return false;" class="dropdown-toggle">
                            <i class="fa fa-download"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                <!--  notification end -->
            </div>
            <div class="top-menu">
                <ul class="nav pull-right top-menu">
    <script>
    function generarBackup() {
        Swal.fire({
            title: '¿Deseas realizar una copia de seguridad?',
            text: 'Se generará un respaldo completo de la base de datos',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f27c1f',
            cancelButtonColor: '#1d2a58',
            confirmButtonText: 'Continuar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: '¿Estás completamente seguro?',
                    html: '<p>Esta acción realizará lo siguiente:</p>' +
                          '<ul style="text-align: left; display: inline-block;">' +
                          '<li>Generará una copia completa de la base de datos</li>' +
                          '<li>Incluirá todas las tablas y registros</li>' +
                          '<li>Se descargará como archivo SQL</li>' +
                          '<li>No afectará al sistema actual</li>' +
                          '</ul>',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#f27c1f',
                    cancelButtonColor: '#1d2a58',
                    confirmButtonText: 'Sí, generar backup',
                    cancelButtonText: 'Cancelar'
                }).then((result2) => {
                    if (result2.isConfirmed) {
                        Swal.fire({
                            title: 'Generando backup...',
                            text: 'Por favor espere...',
                            allowOutsideClick: false,
                            confirmButtonColor: '#f27c1f',
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });

                        fetch('<?php echo BASE_URL; ?>index.php?action=backup&method=generarBackup')
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    // Crear el archivo blob desde el contenido base64
                                    const binaryString = window.atob(data.content);
                                    const bytes = new Uint8Array(binaryString.length);
                                    for (let i = 0; i < binaryString.length; i++) {
                                        bytes[i] = binaryString.charCodeAt(i);
                                    }
                                    const blob = new Blob([bytes], { type: 'application/sql' });
                                    
                                    // Crear y hacer clic en el enlace de descarga
                                    const link = document.createElement('a');
                                    link.href = window.URL.createObjectURL(blob);
                                    link.download = data.filename;
                                    document.body.appendChild(link);
                                    link.click();
                                    document.body.removeChild(link);

                                    Swal.fire({
                                        icon: 'success',
                                        title: '¡Éxito!',
                                        text: data.message,
                                        confirmButtonColor: '#f27c1f',
                                        timer: 3000
                                    });
                                } else {
                                    throw new Error(data.error);
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: error.message || 'Hubo un error al generar el backup',
                                    confirmButtonColor: '#f27c1f',
                                    confirmButtonText: 'Aceptar'
                                });
                            });
                    }
                });
            }
        });
    }
    </script>
                    <li><a class="logout" href="<?php echo BASE_URL; ?>index.php?action=logout">Cerrar Sesión</a></li>
                </ul>
            </div>
        </header>
        <!--header end-->