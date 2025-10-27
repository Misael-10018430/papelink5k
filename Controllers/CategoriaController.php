<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Categoria.php';

class CategoriaController {
    private $categoriaModel;
    
    public function __construct() {
        $this->categoriaModel = new Categoria();
    }
    /**
     * Listar categorías (ADMIN)
     */
    public function listarAdmin() {
        $estado = isset($_GET['estado']) ? (int)$_GET['estado'] : null;
        $categorias = $this->categoriaModel->obtenerTodas($estado);
        return $categorias;
    }
    /**
     * Listar categorías activas (CLIENTE)
     */
    public function listarActivas() {
        return $this->categoriaModel->obtenerActivas();
    }
    /**
     * Crear categoría
     */
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('view/admin/categorias.php');
            exit();
        }
        // Validar
        if (empty($_POST['nombre_categoria'])) {
            $_SESSION['error'] = 'El nombre de la categoría es obligatorio';
            redirect('view/admin/categorias.php?accion=nuevo');
            exit();
        }
        $nombreCategoria = trim($_POST['nombre_categoria']);
        // Crear
        $resultado = $this->categoriaModel->crear($nombreCategoria);
        if (isset($resultado['error'])) {
            $_SESSION['error'] = 'Error al crear la categoría: ' . $resultado['error'];
            $_SESSION['datos_form'] = $_POST;
            redirect('view/admin/categorias.php?accion=nuevo');
        } else {
            $_SESSION['exito'] = 'Categoría creada exitosamente';
            redirect('view/admin/categorias.php');
        }
        exit();
    }
    /**
     * Actualizar categoría
     */
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('view/admin/categorias.php');
            exit();
        }
        // Validar
        if (empty($_POST['id_categoria']) || empty($_POST['nombre_categoria'])) {
            $_SESSION['error'] = 'Datos incompletos';
            redirect('view/admin/categorias.php');
            exit();
        }
        $idCategoria = (int)$_POST['id_categoria'];
        $nombreCategoria = trim($_POST['nombre_categoria']);
        // Actualizar
        $resultado = $this->categoriaModel->actualizar($idCategoria, $nombreCategoria);
        if (isset($resultado['error'])) {
            $_SESSION['error'] = 'Error al actualizar la categoría: ' . $resultado['error'];
            $_SESSION['datos_form'] = $_POST;
            redirect('view/admin/categorias.php?accion=editar&id=' . $idCategoria);
        } else {
            $_SESSION['exito'] = 'Categoría actualizada exitosamente';
            redirect('view/admin/categorias.php');
        }
        exit();
    }
    
    /**
     * Cambiar estado
     */
    public function cambiarEstado() {
        if (!isset($_GET['id']) || !isset($_GET['estado'])) {
            $_SESSION['error'] = 'Parámetros incompletos';
            redirect('view/admin/categorias.php');
            exit();
        }
        $idCategoria = (int)$_GET['id'];
        $estado = (int)$_GET['estado'];
        $resultado = $this->categoriaModel->cambiarEstado($idCategoria, $estado);
        if (isset($resultado['error'])) {
            $_SESSION['error'] = 'Error al cambiar estado: ' . $resultado['error'];
        } else {
            $mensaje = $estado == 1 ? 'activada' : 'desactivada';
            $_SESSION['exito'] = "Categoría $mensaje exitosamente";
        }
        redirect('view/admin/categorias.php');
        exit();
    }
}
?>