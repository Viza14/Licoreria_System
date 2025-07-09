<?php
class ClienteController
{
    private $model;

    public function __construct()
    {
        require_once ROOT_PATH . 'models/ClienteModel.php';
        $this->model = new ClienteModel();
    }

    private function checkSession()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "index.php?action=login");
            exit();
        }
    }

    private function redirect($action)
    {
        header("Location: " . BASE_URL . "index.php?action=" . $action);
        exit();
    }

    private function loadView($view, $data = [])
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login');
            return;
        }

        if (headers_sent($file, $line)) {
            error_log("Headers already sent in $file on line $line");
            return;
        }

        ob_start();
        try {
            extract($data);
            require_once ROOT_PATH . 'views/layouts/header.php';
            require_once ROOT_PATH . 'views/layouts/sidebar.php';
            require_once ROOT_PATH . 'views/' . $view . '.php';
            require_once ROOT_PATH . 'views/layouts/footer.php';
            ob_end_flush();
        } catch (Exception $e) {
            ob_end_clean();
            error_log("Error al cargar la vista: " . $e->getMessage());
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Error al cargar la página. Por favor, intente nuevamente.',
                'icon' => 'error'
            ];
            $this->redirect('dashboard');
        }
    }

    public function index()
    {
        $this->checkSession();
        try {
            $clientes = $this->model->obtenerTodosClientes();
            if ($clientes === false) {
                throw new Exception('Error al obtener la lista de clientes');
            }
            $this->loadView('clientes/index', [
                'clientes' => $clientes,
                'pageTitle' => 'Gestión de Clientes',
                'user_rol' => $_SESSION['user_rol'] ?? null
            ]);
        } catch (Exception $e) {
            error_log("Error en index: " . $e->getMessage());
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Error al cargar la lista de clientes. Por favor, intente nuevamente.',
                'icon' => 'error'
            ];
            $this->redirect('dashboard');
        }
    }

    public function crear()
    {
        $this->checkSession();
        $simbolos = $this->model->obtenerSimbolosCedula();
        $this->loadView('clientes/crear', [
            'simbolos' => $simbolos,
            'pageTitle' => 'Crear Cliente'
        ]);
    }
    
   public function guardar()
    {
        
        $this->checkSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validaciones
            $simboloId = $_POST['id_simbolo_cedula'];
            $cedula = $_POST['cedula'];
            $telefono = $_POST['telefono'];

            // Validar cédula según símbolo
            $simbolos = $this->model->obtenerSimbolosCedula();
            $simbolo = '';
            foreach ($simbolos as $s) {
                if ($s['id'] == $simboloId) {
                    $simbolo = $s['nombre'];
                    break;
                }
            }

            // Validación mejorada
            $errorValidacion = false;
            $mensajeError = '';
            
            // Validar según tipo de documento
            if ($simbolo === 'V' || $simbolo === 'E') {
                if (strlen($cedula) < 7 || strlen($cedula) > 8 || !ctype_digit($cedula)) {
                    $mensajeError = "La cédula debe tener entre 7 y 8 dígitos numéricos para $simbolo-";
                    $errorValidacion = true;
                }
            } elseif ($simbolo === 'J') {
                if (strlen($cedula) < 8 || strlen($cedula) > 9 || !ctype_digit($cedula)) {
                    $mensajeError = "El RIF debe tener entre 8 y 9 dígitos numéricos para $simbolo-";
                    $errorValidacion = true;
                }
            } else {
                $mensajeError = "Tipo de documento no válido";
                $errorValidacion = true;
            }

            if ($errorValidacion) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => $mensajeError,
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('clientes&method=crear');
                return;
            }

            // Validar caracteres especiales en nombres y apellidos
            if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $_POST['nombres'])) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'El campo nombres solo puede contener letras y espacios',
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('clientes&method=crear');
                return;
            }

            if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $_POST['apellidos'])) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'El campo apellidos solo puede contener letras y espacios',
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('clientes&method=crear');
                return;
            }

            // Validar teléfono (11 dígitos)
            if (strlen($telefono) != 11 || !ctype_digit($telefono)) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'El teléfono debe tener exactamente 11 dígitos numéricos',
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('clientes&method=crear');
                return;
            }

            $data = [
                'cedula' => $cedula,
                'id_simbolo_cedula' => $simboloId,
                'nombres' => $_POST['nombres'],
                'apellidos' => $_POST['apellidos'],
                'telefono' => $telefono,
                'direccion' => $_POST['direccion'] ?? 'Sin especificar',
                'id_estatus' => $_POST['id_estatus']
            ];

            // Verificar si el cliente ya existe
            if ($this->model->existeCliente($cedula, $telefono)) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Ya existe un cliente con esta cédula o teléfono',
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('clientes&method=crear');
                return;
            }

            if ($this->model->crearCliente($data)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Cliente creado exitosamente',
                    'icon' => 'success'
                ];
                $this->redirect('clientes');
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al crear el cliente: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('clientes&method=crear');
            }
        }
    }

    public function editar($cedula)
    {
        $this->checkSession();
        $cliente = $this->model->obtenerClientePorCedula($cedula);
        if (!$cliente) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Cliente no encontrado',
                'icon' => 'error'
            ];
            $this->redirect('clientes');
        }

        $simbolos = $this->model->obtenerSimbolosCedula();
        $this->loadView('clientes/editar', [
            'cliente' => $cliente,
            'simbolos' => $simbolos,
            'pageTitle' => 'Editar Cliente'
        ]);
    }
    public function actualizar($cedula)
{
    $this->checkSession();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // ... validaciones previas ...

        $data = [
            'cedula' => $_POST['cedula'], // Incluimos la nueva cédula aquí
            'id_simbolo_cedula' => $_POST['id_simbolo_cedula'],
            'nombres' => $_POST['nombres'],
            'apellidos' => $_POST['apellidos'],
            'telefono' => $_POST['telefono'],
            'direccion' => $_POST['direccion'] ?? 'Sin especificar',
            'id_estatus' => $_POST['id_estatus']
        ];

        // Verificar si la cédula cambió
        if ($_POST['cedula'] !== $cedula) {
            // Verificar si la nueva cédula ya existe
            if ($this->model->existeCliente($_POST['cedula'], $_POST['telefono'], $cedula)) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Ya existe otro cliente con esta cédula o teléfono',
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('clientes&method=editar&cedula=' . $cedula);
                return;
            }
        }

        // Actualizar directamente
        if ($this->model->actualizarCliente($cedula, $data)) {
            $_SESSION['mensaje'] = [
                'title' => 'Éxito',
                'text' => 'Cliente actualizado exitosamente',
                'icon' => 'success'
            ];
            $this->redirect('clientes');
        } else {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Error al actualizar el cliente: ' . implode(', ', $this->model->getErrors()),
                'icon' => 'error'
            ];
            $_SESSION['form_data'] = $_POST;
            $this->redirect('clientes&method=editar&cedula=' . $cedula);
        }
    }
}
    public function mostrar($cedula)
    {
        $this->checkSession();
        $cliente = $this->model->obtenerClientePorCedula($cedula);
        if (!$cliente) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Cliente no encontrado',
                'icon' => 'error'
            ];
            $this->redirect('clientes');
        }

        // Obtener historial de compras
        $totalVentas = $this->model->obtenerTotalVentasCliente($cedula);
        
        $this->loadView('clientes/mostrar', [
            'cliente' => $cliente,
            'totalVentas' => $totalVentas
        ]);
    }
public function cambiarEstado($cedula)
{
    $this->checkSession();

    if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] == 2) {
        $_SESSION['error'] = [
            'title' => 'Error',
            'text' => 'No tiene permisos para realizar esta acción',
            'icon' => 'error'
        ];
        $this->redirect('clientes');
        return;
    }

    try {
        $cliente = $this->model->obtenerClientePorCedula($cedula);
        if (!$cliente) {
            throw new Exception('Cliente no encontrado');
        }

        $nuevoEstado = $cliente['id_estatus'] == 1 ? 2 : 1;
        $estadoTexto = $nuevoEstado == 1 ? 'activado' : 'desactivado';

        if ($this->model->cambiarEstado($cedula, $nuevoEstado)) {
            $_SESSION['mensaje'] = [
                'title' => 'Éxito',
                'text' => "Cliente $estadoTexto correctamente",
                'icon' => 'success'
            ];
        } else {
            throw new Exception('Error al cambiar el estado: ' . implode(', ', $this->model->getErrors()));
        }
    } catch (Exception $e) {
        error_log("Error en cambiarEstado: " . $e->getMessage());
        $_SESSION['error'] = [
            'title' => 'Error',
            'text' => $e->getMessage(),
            'icon' => 'error'
        ];
    }

    $this->redirect('clientes');
    $estadoTexto = $nuevoEstado == 1 ? 'activado' : 'desactivado';

    if ($this->model->cambiarEstado($cedula, $nuevoEstado)) {
        $_SESSION['mensaje'] = [
            'title' => 'Éxito',
            'text' => "Cliente $estadoTexto correctamente",
            'icon' => 'success'
        ];
    } else {
        $_SESSION['error'] = [
            'title' => 'Error',
            'text' => 'Error al cambiar el estado: ' . implode(', ', $this->model->getErrors()),
            'icon' => 'error'
        ];
    }

    $this->redirect('clientes');
}

   
}