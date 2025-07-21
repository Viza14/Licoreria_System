<?php
class CategoriaController
{
    private $model;

    public function __construct()
    {
        require_once ROOT_PATH . 'models/CategoriaModel.php';
        $this->model = new CategoriaModel();
    }

    private function normalizarTexto($texto)
    {
        if (!$texto) return null;
        return strtolower(trim(str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ'],
            ['a', 'e', 'i', 'o', 'u', 'u', 'n', 'a', 'e', 'i', 'o', 'u', 'u', 'n'],
            $texto
        )));
    }

    public function index()
    {
        $this->checkSession();
        
        // Obtener parámetros de paginación y búsqueda
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $por_pagina = 10;

        // Preparar filtros normalizados
        $filtros = [
            'busqueda' => isset($_GET['busqueda']) ? $this->normalizarTexto($_GET['busqueda']) : null,
            'tipo' => isset($_GET['tipo']) ? $this->normalizarTexto($_GET['tipo']) : null,
            'estatus' => isset($_GET['estatus']) ? $this->normalizarTexto($_GET['estatus']) : null
        ];

        // Obtener datos paginados
        $resultado = $this->model->obtenerTodasCategorias($pagina, $por_pagina, $filtros);
        $tipos = $this->model->obtenerTiposCategoria();
        
        // Si es una solicitud AJAX, devolver JSON
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
            header('Content-Type: application/json');
            echo json_encode([
                'categorias' => $resultado['categorias'],
                'pagina_actual' => $resultado['pagina_actual'],
                'total_paginas' => $resultado['total_paginas']
            ]);
            exit;
        }

        // Si no es AJAX, cargar la vista normal
        $this->loadView('categorias/index', [
            'categorias' => $resultado['categorias'],
            'tipos' => $tipos,
            'pagina_actual' => $resultado['pagina_actual'],
            'total_paginas' => $resultado['total_paginas'],
            'total_registros' => $resultado['total_registros']
        ]);
    }

    public function crear()
    {
        $this->checkSession();
        $tipos = $this->model->obtenerTiposCategoria();
        $this->loadView('categorias/crear', ['tipos' => $tipos]);
    }

    public function guardar()
    {
        $this->checkSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre']);
            $id_tipo_categoria = $_POST['id_tipo_categoria'];
            $id_estatus = $_POST['id_estatus'];

            if (empty($nombre)) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'El nombre no puede estar vacío',
                    'icon' => 'error'
                ];
                $this->redirect('categorias&method=crear');
                return;
            }

            if ($this->model->existeCategoria($nombre)) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Ya existe una categoría con ese nombre',
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('categorias&method=crear');
                return;
            }

            if ($this->model->crearCategoria($nombre, $id_tipo_categoria, $id_estatus)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Categoría creada exitosamente',
                    'icon' => 'success'
                ];
                $this->redirect('categorias');
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al crear la categoría: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('categorias&method=crear');
            }
        }
    }

    public function editar($id)
    {
        $this->checkSession();
        $categoria = $this->model->obtenerCategoriaPorId($id);
        if (!$categoria) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Categoría no encontrada',
                'icon' => 'error'
            ];
            $this->redirect('categorias');
        }
        $tipos = $this->model->obtenerTiposCategoria();
        $this->loadView('categorias/editar', [
            'categoria' => $categoria,
            'tipos' => $tipos
        ]);
    }

    public function actualizar($id)
    {
        $this->checkSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre']);
            $id_tipo_categoria = $_POST['id_tipo_categoria'];
            $id_estatus = $_POST['id_estatus'];

            if (empty($nombre)) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'El nombre no puede estar vacío',
                    'icon' => 'error'
                ];
                $this->redirect('categorias&method=editar&id=' . $id);
                return;
            }

            if ($this->model->existeCategoria($nombre, $id)) {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Ya existe otra categoría con ese nombre',
                    'icon' => 'error'
                ];
                $this->redirect('categorias&method=editar&id=' . $id);
                return;
            }

            if ($this->model->actualizarCategoria($id, $nombre, $id_tipo_categoria, $id_estatus)) {
                $_SESSION['mensaje'] = [
                    'title' => 'Éxito',
                    'text' => 'Categoría actualizada exitosamente',
                    'icon' => 'success'
                ];
            } else {
                $_SESSION['error'] = [
                    'title' => 'Error',
                    'text' => 'Error al actualizar la categoría: ' . implode(', ', $this->model->getErrors()),
                    'icon' => 'error'
                ];
            }
            $this->redirect('categorias');
        }
    }

    public function mostrar($id)
    {
        $this->checkSession();
        $categoria = $this->model->obtenerCategoriaPorId($id);
        if (!$categoria) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Categoría no encontrada',
                'icon' => 'error'
            ];
            $this->redirect('categorias');
        }
        $this->loadView('categorias/mostrar', ['categoria' => $categoria]);
    }

    public function cambiarEstado($id)
    {
        $this->checkSession();

        $categoria = $this->model->obtenerCategoriaPorId($id);
        if (!$categoria) {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Categoría no encontrada',
                'icon' => 'error'
            ];
            $this->redirect('categorias');
        }

        $nuevoEstado = $categoria['id_estatus'] == 1 ? 2 : 1;
        $estadoTexto = $nuevoEstado == 1 ? 'activada' : 'desactivada';

        if ($this->model->cambiarEstado($id, $nuevoEstado)) {
            $_SESSION['mensaje'] = [
                'title' => 'Éxito',
                'text' => "Categoría $estadoTexto correctamente",
                'icon' => 'success'
            ];
        } else {
            $_SESSION['error'] = [
                'title' => 'Error',
                'text' => 'Error al cambiar el estado: ' . implode(', ', $this->model->getErrors()),
                'icon' => 'error'
            ];
        }

        $this->redirect('categorias');
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
