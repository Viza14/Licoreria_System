<?php
class TipoCategoriaController
{
    private $model;

    public function __construct()
    {
        require_once ROOT_PATH . 'models/TipoCategoriaModel.php';
        $this->model = new TipoCategoriaModel();
    }

    public function index()
    {
        $this->checkSession();
        $tipos = $this->model->obtenerTodosTiposCategoria();
        $this->loadView('tipos_categoria/index', ['tipos' => $tipos]);
    }

    public function crear()
    {
        $this->checkSession();
        $this->loadView('tipos_categoria/crear');
    }

    // En TipoCategoriaController.php - método guardar()
    public function guardar()
    {
        $this->checkSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre']);
            $id_estatus = $_POST['id_estatus'];

            if (empty($nombre)) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'El nombre no puede estar vacío',
                    'icon' => 'error'
                ];
                $this->redirect('tipos-categoria&method=crear');
                return;
            }

            // Verificar si ya existe un tipo con el mismo nombre
            if ($this->model->existeTipoCategoria($nombre)) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Ya existe un tipo de categoría con ese nombre',
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST; // Para mantener los datos del formulario
                $this->redirect('tipos-categoria&method=crear');
                return;
            }

            if ($this->model->crearTipoCategoria($nombre, $id_estatus)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Tipo de categoría creado exitosamente',
                    'icon' => 'success'
                ];
                $this->redirect('tipos-categoria');
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al crear el tipo de categoría: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST; // Para mantener los datos del formulario
                $this->redirect('tipos-categoria&method=crear');
            }
        }
    }

    public function editar($id)
    {
        $this->checkSession();
        $tipo = $this->model->obtenerTipoCategoriaPorId($id);
        if (!$tipo) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Tipo de categoría no encontrado',
                'icon' => 'error'
            ];
            $this->redirect('tipos-categoria');
        }
        $this->loadView('tipos_categoria/editar', ['tipo' => $tipo]);
    }

    // En TipoCategoriaController.php - método actualizar()
    public function actualizar($id)
    {
        $this->checkSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre']);
            $id_estatus = $_POST['id_estatus'];

            if (empty($nombre)) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'El nombre no puede estar vacío',
                    'icon' => 'error'
                ];
                $this->redirect('tipos-categoria&method=editar&id=' . $id);
                return;
            }

            // Verificar si ya existe otro tipo con el mismo nombre
            if ($this->model->existeTipoCategoria($nombre, $id)) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Ya existe otro tipo de categoría con ese nombre',
                    'icon' => 'error'
                ];
                $this->redirect('tipos-categoria&method=editar&id=' . $id);
                return;
            }

            if ($this->model->actualizarTipoCategoria($id, $nombre, $id_estatus)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Tipo de categoría actualizado exitosamente',
                    'icon' => 'success'
                ];
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al actualizar el tipo de categoría: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
            }
            $this->redirect('tipos-categoria');
        }
    }

    public function mostrar($id)
    {
        $this->checkSession();
        $tipo = $this->model->obtenerTipoCategoriaPorId($id);
        if (!$tipo) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Tipo de categoría no encontrado',
                'icon' => 'error'
            ];
            $this->redirect('tipos-categoria');
        }
        $this->loadView('tipos_categoria/mostrar', ['tipo' => $tipo]);
    }

    public function cambiarEstado($id)
    {
        $this->checkSession();

        $tipo = $this->model->obtenerTipoCategoriaPorId($id);
        if (!$tipo) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Tipo de categoría no encontrado',
                'icon' => 'error'
            ];
            $this->redirect('tipos-categoria');
        }

        $nuevoEstado = $tipo['id_estatus'] == 1 ? 2 : 1;
        $estadoTexto = $nuevoEstado == 1 ? 'activado' : 'desactivado';

        if ($this->model->cambiarEstado($id, $nuevoEstado)) {
            $_SESSION['mensaje'] = [
                'title' => 'Éxito',
                'text' => "Tipo de categoría $estadoTexto correctamente",
                'icon' => 'success'
            ];
        } else {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Error al cambiar el estado: ' . implode(', ', $this->model->getErrors()),
                'icon' => 'error'
            ];
        }

        $this->redirect('tipos-categoria');
    }

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
