<?php
class UsuarioController
{
    private $model;

    public function __construct()
    {
        require_once ROOT_PATH . 'models/UsuarioModel.php';
        $this->model = new UsuarioModel();
    }

    public function index()
    {
        $this->checkSession();
        
        // Obtener parámetros de paginación
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $por_pagina = 10;

        // Obtener usuarios paginados
        $resultado = $this->model->obtenerTodosUsuarios($pagina, $por_pagina);
        $roles = $this->model->obtenerRoles(); // Para los filtros
        
        $this->loadView('usuarios/index', [
            'usuarios' => $resultado['usuarios'],
            'pagina_actual' => $resultado['pagina_actual'],
            'total_paginas' => $resultado['total_paginas'],
            'total_registros' => $resultado['total_registros'],
            'roles' => $roles // Para los filtros
        ]);
    }

    public function cambiarEstado($id)
    {
        $this->checkSession();

        $usuario = $this->model->obtenerUsuarioPorId($id);
        if (!$usuario) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Usuario no encontrado',
                'icon' => 'error'
            ];
            $this->redirect('usuarios');
        }

        $nuevoEstado = $usuario['id_estatus'] == 1 ? 2 : 1;
        $estadoTexto = $nuevoEstado == 1 ? 'activado' : 'desactivado';

        if ($this->model->cambiarEstado($id, $nuevoEstado)) {
            $_SESSION['mensaje'] = [
                'title' => 'Éxito',
                'text' => "Usuario $estadoTexto correctamente",
                'icon' => 'success'
            ];
        } else {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Error al cambiar el estado: ' . implode(', ', $this->model->getErrors()),
                'icon' => 'error'
            ];
        }

        $this->redirect('usuarios');
    }

    public function crear()
    {
        $this->checkSession();
        $roles = $this->model->obtenerRoles();
        $this->loadView('usuarios/crear', ['roles' => $roles]);
    }

    public function guardar()
    {
        $this->checkSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener el símbolo seleccionado
            $simboloId = $_POST['id_simbolo_cedula'];
            $simbolos = $this->model->obtenerSimbolosCedula();
            $simbolo = null;

            foreach ($simbolos as $s) {
                if ($s['id'] == $simboloId) {
                    $simbolo = $s['nombre'];
                    break;
                }
            }

            // Validar longitud de cédula según símbolo
            $longitudCedula = strlen($_POST['cedula']);
            $cedulaValida = false;

            if (($simbolo === 'V' || $simbolo === 'E') && $longitudCedula >= 7 && $longitudCedula <= 8 && ctype_digit($_POST['cedula'])) {
                $cedulaValida = true;
            } elseif ($simbolo === 'J' && $longitudCedula >= 8 && $longitudCedula <= 9 && ctype_digit($_POST['cedula'])) {
                $cedulaValida = true;
            }

            if (!$cedulaValida) {
                $mensajeError = ($simbolo === 'V' || $simbolo === 'E') ?
                    "La cédula debe tener entre 7 y 8 dígitos numéricos para $simbolo-" :
                    "La cédula debe tener entre 8 y 9 dígitos numéricos para $simbolo-";

                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => $mensajeError,
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('usuarios&method=crear');
                return;
            }

            // Validar teléfono (11 dígitos)
            if (strlen($_POST['telefono']) != 11 || !ctype_digit($_POST['telefono'])) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'El teléfono debe tener exactamente 11 dígitos numéricos',
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('usuarios&method=crear');
                return;
            }

            $data = [
                'cedula' => $_POST['cedula'],
                'id_simbolo_cedula' => $_POST['id_simbolo_cedula'],
                'nombres' => $_POST['nombres'],
                'apellidos' => $_POST['apellidos'],
                'telefono' => $_POST['telefono'],
                'direccion' => $_POST['direccion'] ?? 'Sin especificar',
                'user' => $_POST['user'],
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                'id_rol' => $_POST['id_rol'],
                'id_estatus' => $_POST['id_estatus']
            ];

            // Verificar si el usuario ya existe
            if ($this->model->existeUsuario($data['cedula'], $data['telefono'], $data['user'])) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Ya existe un usuario con esta cédula, teléfono o nombre de usuario',
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('usuarios&method=crear');
                return;
            }

            if ($this->model->crearUsuario($data)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Usuario creado exitosamente',
                    'icon' => 'success'
                ];
                $this->redirect('usuarios');
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al crear el usuario: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('usuarios&method=crear');
            }
        }
    }

    public function editar($id)
    {
        $this->checkSession();
        $usuario = $this->model->obtenerUsuarioPorId($id);
        if (!$usuario) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Usuario no encontrado',
                'icon' => 'error'
            ];
            $this->redirect('usuarios');
        }

        $roles = $this->model->obtenerRoles();
        $this->loadView('usuarios/editar', [
            'usuario' => $usuario,
            'roles' => $roles
        ]);
    }

    public function actualizar($id)
    {
        $this->checkSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener datos del formulario
            $simboloId = $_POST['id_simbolo_cedula'];
            $cedula = $_POST['cedula'];

            // Obtener el símbolo actual
            $simbolos = $this->model->obtenerSimbolosCedula();
            $simboloActual = '';
            foreach ($simbolos as $s) {
                if ($s['id'] == $simboloId) {
                    $simboloActual = $s['nombre'] . '-';
                    break;
                }
            }

            // Validación de cédula según el tipo
            if (strpos($simboloActual, 'V-') !== false || strpos($simboloActual, 'E-') !== false) {
                // V- o E-: 7 u 8 dígitos
                if (!preg_match('/^\d{7,8}$/', $cedula)) {
                    $_SESSION['error'] = [
                        'title' => 'Error',
                        'text' => 'Para ' . $simboloActual . ' la cédula debe tener entre 7 y 8 dígitos',
                        'icon' => 'error'
                    ];
                    $_SESSION['form_data'] = $_POST;
                    $this->redirect('usuarios&method=editar&id=' . $id);
                    return;
                }
            } elseif (strpos($simboloActual, 'J-') !== false) {
                // J-: 8 o 9 dígitos
                if (!preg_match('/^\d{8,9}$/', $cedula)) {
                    $_SESSION['error'] = [
                        'title' => 'Error',
                        'text' => 'Para ' . $simboloActual . ' la cédula debe tener entre 8 y 9 dígitos',
                        'icon' => 'error'
                    ];
                    $_SESSION['form_data'] = $_POST;
                    $this->redirect('usuarios&method=editar&id=' . $id);
                    return;
                }
            }

            // Validar teléfono (11 dígitos)
            if (strlen($_POST['telefono']) != 11 || !ctype_digit($_POST['telefono'])) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'El teléfono debe tener exactamente 11 dígitos numéricos',
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('usuarios&method=editar&id=' . $id);
                return;
            }

            // Preparar datos para actualización
            $data = [
                'id' => $id,
                'cedula' => $cedula,
                'id_simbolo_cedula' => $simboloId,
                'nombres' => $_POST['nombres'],
                'apellidos' => $_POST['apellidos'],
                'telefono' => $_POST['telefono'],
                'direccion' => $_POST['direccion'] ?? 'Sin especificar',
                'user' => $_POST['user'],
                'id_rol' => $_POST['id_rol'],
                'id_estatus' => $_POST['id_estatus']
            ];

            if (!empty($_POST['password'])) {
                $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            if ($this->model->actualizarUsuario($id, $data)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Usuario actualizado exitosamente',
                    'icon' => 'success'
                ];
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al actualizar el usuario: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
            }
            $this->redirect('usuarios');
        }
    }

    public function mostrar($id)
    {
        $this->checkSession();
        $usuario = $this->model->obtenerUsuarioPorId($id);
        if (!$usuario) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Usuario no encontrado',
                'icon' => 'error'
            ];
            $this->redirect('usuarios');
        }
        $this->loadView('usuarios/mostrar', ['usuario' => $usuario]);
    }

    // Métodos auxiliares
    private function checkSession()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = [
                'title' => 'Acceso denegado',
                'text' => 'Debe iniciar sesión para acceder a esta página',
                'icon' => 'error'
            ];
            $this->redirect('login');
        }
    }

    private function redirect($action)
    {
        header("Location: " . BASE_URL . "index.php?action=$action");
        exit();
    }

    private function loadView($view, $data = [])
    {
        extract($data);
        require ROOT_PATH . 'views/layouts/header.php';
        require ROOT_PATH . 'views/layouts/sidebar.php';
        require ROOT_PATH . 'views/' . $view . '.php';
        require ROOT_PATH . 'views/layouts/footer.php';
    }
}
