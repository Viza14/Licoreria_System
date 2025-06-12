<?php
class ProveedorController
{
    private $model;

    public function __construct()
    {
        $this->model = new ProveedorModel();
    }

    public function index()
    {
        $this->checkSession();
        $proveedores = $this->model->obtenerTodosProveedores();
        $this->loadView('proveedores/index', ['proveedores' => $proveedores]);
    }

    public function cambiarEstado($cedula)
    {
        $this->checkSession();

        $proveedor = $this->model->obtenerProveedorPorCedula($cedula);
        if (!$proveedor) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Proveedor no encontrado',
                'icon' => 'error'
            ];
            $this->redirect('proveedores');
        }

        $nuevoEstado = $proveedor['id_estatus'] == 1 ? 2 : 1;
        $estadoTexto = $nuevoEstado == 1 ? 'activado' : 'desactivado';

        if ($this->model->cambiarEstado($cedula, $nuevoEstado)) {
            $_SESSION['mensaje'] = [
                'title' => 'Éxito',
                'text' => "Proveedor $estadoTexto correctamente",
                'icon' => 'success'
            ];
        } else {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Error al cambiar el estado: ' . implode(', ', $this->model->getErrors()),
                'icon' => 'error'
            ];
        }

        $this->redirect('proveedores');
    }

    public function crear()
    {
        $this->checkSession();
        $simbolos = $this->model->obtenerSimbolosCedula();
        $this->loadView('proveedores/crear', ['simbolos' => $simbolos]);
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
                $this->redirect('proveedores&method=crear');
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
                $this->redirect('proveedores&method=crear');
                return;
            }

            $data = [
                'cedula' => $_POST['cedula'],
                'id_simbolo_cedula' => $simboloId,
                'nombre' => $_POST['nombre'],
                'telefono' => $_POST['telefono'],
                'direccion' => $_POST['direccion'] ?? 'Sin especificar',
                'id_estatus' => $_POST['id_estatus']
            ];

            // Verificar si el proveedor ya existe
            if ($this->model->existeProveedor($data['cedula'], $data['telefono'])) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Ya existe un proveedor con esta cédula o teléfono',
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('proveedores&method=crear');
                return;
            }

            if ($this->model->crearProveedor($data)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Proveedor creado exitosamente',
                    'icon' => 'success'
                ];
                $this->redirect('proveedores');
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al crear el proveedor: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('proveedores&method=crear');
            }
        }
    }


    public function editar($cedula)
    {
        $this->checkSession();
        $proveedor = $this->model->obtenerProveedorPorCedula($cedula);
        if (!$proveedor) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Proveedor no encontrado',
                'icon' => 'error'
            ];
            $this->redirect('proveedores');
        }

        $simbolos = $this->model->obtenerSimbolosCedula();
        $this->loadView('proveedores/editar', [
            'proveedor' => $proveedor,
            'simbolos' => $simbolos
        ]);
    }

    public function actualizar($cedula)
    {
        $this->checkSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener datos del formulario
            $simboloId = $_POST['id_simbolo_cedula'];
            $nuevaCedula = $_POST['cedula'];

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
                if (!preg_match('/^\d{7,8}$/', $nuevaCedula)) {
                    $_SESSION['error'] = [
                        'title' => 'Error',
                        'text' => 'Para ' . $simboloActual . ' la cédula debe tener entre 7 y 8 dígitos',
                        'icon' => 'error'
                    ];
                    $_SESSION['form_data'] = $_POST;
                    $this->redirect('proveedores&method=editar&id=' . $cedula);
                    return;
                }
            } elseif (strpos($simboloActual, 'J-') !== false) {
                // J-: 8 o 9 dígitos
                if (!preg_match('/^\d{8,9}$/', $nuevaCedula)) {
                    $_SESSION['error'] = [
                        'title' => 'Error',
                        'text' => 'Para ' . $simboloActual . ' la cédula debe tener entre 8 y 9 dígitos',
                        'icon' => 'error'
                    ];
                    $_SESSION['form_data'] = $_POST;
                    $this->redirect('proveedores&method=editar&id=' . $cedula);
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
                $this->redirect('proveedores&method=editar&id=' . $cedula);
                return;
            }

            // Preparar datos para actualización
            $datosActualizacion = [
                'cedula_original' => $cedula,
                'cedula' => $_POST['cedula'],
                'id_simbolo_cedula' => $_POST['id_simbolo_cedula'],
                'nombre' => $_POST['nombre'],
                'telefono' => $_POST['telefono'],
                'direccion' => $_POST['direccion'] ?? 'Sin especificar',
                'id_estatus' => $_POST['id_estatus']
            ];

            if ($this->model->actualizarProveedor($cedula, $datosActualizacion)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Proveedor y todos sus datos asociados fueron actualizados',
                    'icon' => 'success'
                ];
            } else {
                $errorMsg = 'Error al actualizar: ' . implode(', ', $this->model->getErrors());

                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => $errorMsg,
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
            }

            $this->redirect('proveedores');
        }
    }

    public function mostrar($cedula)
    {
        $this->checkSession();
        $proveedor = $this->model->obtenerProveedorPorCedula($cedula);
        if (!$proveedor) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Proveedor no encontrado',
                'icon' => 'error'
            ];
            $this->redirect('proveedores');
        }
        $this->loadView('proveedores/mostrar', ['proveedor' => $proveedor]);
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

    // Nuevo método de validación en el controlador
    private function validarCedulaSegunTipo($simboloNombre, $cedula)
    {
        // Eliminar cualquier caracter no numérico
        $cedula = preg_replace('/[^0-9]/', '', $cedula);

        if (stripos($simboloNombre, 'V-') !== false || stripos($simboloNombre, 'E-') !== false) {
            // Validación para cédulas V- y E- (7-8 dígitos)
            if (strlen($cedula) < 7 || strlen($cedula) > 8) {
                return [
                    'valido' => false,
                    'mensaje' => 'Para ' . $simboloNombre . ' la cédula debe tener entre 7 y 8 dígitos numéricos'
                ];
            }
        } elseif (stripos($simboloNombre, 'J-') !== false) {
            // Validación para RIF J- (8-9 dígitos)
            if (strlen($cedula) < 8 || strlen($cedula) > 9) {
                return [
                    'valido' => false,
                    'mensaje' => 'Para ' . $simboloNombre . ' el RIF debe tener entre 8 y 9 dígitos numéricos'
                ];
            }
        } else {
            return [
                'valido' => false,
                'mensaje' => 'Tipo de identificación no válido'
            ];
        }

        return ['valido' => true, 'mensaje' => ''];
    }
}
