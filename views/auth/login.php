<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/licoreria/'); // Ajusta esta ruta según tu configuración
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Login - Licorería</title>

    <!-- Bootstrap, Font Awesome, SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;300;400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #1d2a58;
            --secondary-color: #f27c1f;
            --dark-bg: #0c1833;
            --text-light: #ffffff;
            --input-bg: rgba(255, 255, 255, 0.9);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: var(--dark-bg);
            font-family: 'Nunito', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-container {
            display: flex;
            flex-direction: column;
            background-color: var(--primary-color);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 800px;
            min-height: 500px;
        }

        .login-image {
            background-color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .bottle-container {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
        }

        .bottle-img {
            height: 180px;
            filter: drop-shadow(0 5px 10px rgba(0, 0, 0, 0.3));
            transition: transform 0.3s ease;
        }

        .bottle-shadow {
            width: 120px;
            height: 20px;
            background: radial-gradient(ellipse at center, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.1) 50%, transparent 70%);
            border-radius: 50%;
            filter: blur(5px);
            margin-top: -10px;
            opacity: 0.8;
            transform: perspective(300px) rotateX(20deg) scaleY(0.6);
        }

        .bottle-img:hover {
            transform: translateY(-5px);
            filter: drop-shadow(0 8px 15px rgba(0, 0, 0, 0.4));
        }

        .login-form {
            padding: 40px;
            color: var(--text-light);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .form-wrapper {
            width: 100%;
            max-width: 300px;
        }

        .login-form h2 {
            font-size: 28px;
            margin-bottom: 30px;
            text-align: center;
        }

        .login-form h2 i {
            color: var(--secondary-color);
            margin-right: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 10px;
            border: none;
            font-size: 16px;
            background-color: var(--input-bg);
        }

        .form-group input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(242, 124, 31, 0.3);
        }

        .password-group {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.8);
            padding: 5px;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to right, #f27c1f, #f97b2f);
            border: none;
            border-radius: 10px;
            font-size: 16px;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        @media (min-width: 768px) {
            .login-container {
                flex-direction: row;
            }

            .login-image,
            .login-form {
                flex: 1;
            }

            .bottle-img {
                height: 220px;
            }

            .bottle-shadow {
                width: 150px;
                height: 25px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-image">
            <div class="bottle-container">
                <!-- Ruta de la imagen: colócala en /licoreria/assets/img/botella.png -->
                <img src="<?= BASE_URL ?>assets/img/botella.png" alt="Botella de licor" class="bottle-img">
                <div class="bottle-shadow"></div>
            </div>
        </div>

        <div class="login-form">
            <div class="form-wrapper">
                <h2><i class="fas fa-key"></i>Licorería</h2>

                <?php if (isset($_SESSION['error'])): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: '<?= addslashes($_SESSION['error']) ?>',
                                background: '#1d2a58',
                                color: '#ffffff',
                                confirmButtonColor: '#f27c1f',
                                iconColor: '#f27c1f',
                            });
                        });
                    </script>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form id="loginForm" action="<?php echo BASE_URL; ?>index.php?action=login" method="POST">
                    <div class="form-group">
                        <input type="text" name="user" id="user" placeholder="Usuario" required autofocus>
                    </div>
                    <div class="form-group password-group">
                        <input type="password" name="password" id="password" placeholder="Contraseña" required>
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    </div>
                    <button type="submit" class="btn-login">Ingresar</button>
                </form>
            </div>
        </div>
    </div>

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const password = document.getElementById('password');

            if (togglePassword && password) {
                togglePassword.addEventListener('click', function() {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            }
        });
    </script>
</body>

</html>