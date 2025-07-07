<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>
    <link href="<?= BASE_URL ?>assets/css/bootstrap.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>assets/css/style-responsive.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row" style="margin-top: 100px;">
            <div class="col-lg-6 col-lg-offset-3 p-404 centered">
                <h1><i class="fa fa-warning text-warning"></i></h1>
                <h2>404</h2>
                <h3>¡Oops! Página no encontrada</h3>
                <p>La página que estás buscando no existe o ha sido movida.</p>
                <br>
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <a href="<?= BASE_URL ?>" class="btn btn-theme btn-block">
                            <i class="fa fa-home"></i> Volver al Inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>