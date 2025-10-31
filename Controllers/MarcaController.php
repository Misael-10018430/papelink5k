
<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Marca.php';
class MarcaController {
    private $marcaModel;
    public function __construct() {
        $this->marcaModel = new Marca();
    }
    /**
     * Listar marcas (ADMIN)
     */
    public function listarAdmin() {
        $estado = isset($_GET['estado']) ? (int)$_GET['estado'] : null;
        $marcas = $this->marcaModel->obtenerTodas($estado);
        return $marcas;
    }
    /**
     * Listar marcas activas (CLIENTE)
     */
    public function listarActivas() {
        return $this->marcaModel->obtenerActivas();
    }
    /**
     * Crear marca
     */
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: marcas.php");
            exit();
        }
        // Validar
        if (empty($_POST['nombre_marca'])) {
            $_SESSION['error'] = 'El nombre de la marca es obligatorio';
            header("Location: marcas.php?accion=nuevo");
            exit();
        }
        // Preparar datos
        $datos = [
            'nombreMarca' => trim($_POST['nombre_marca']),
            'logoMarca' => trim($_POST['logo_marca']) ?: null,
            'descripcionMarca' => trim($_POST['descripcion_marca']) ?: null,
            'sitioWeb' => trim($_POST['sitio_web']) ?: null
        ];
        // Crear
        $resultado = $this->marcaModel->crear($datos);
        if (isset($resultado['error'])) {
            $_SESSION['error'] = 'Error al crear la marca: ' . $resultado['error'];
            $_SESSION['datos_form'] = $_POST;
            header("Location: marcas.php?accion=nuevo");
        } else {
            $_SESSION['exito'] = 'Marca creada exitosamente';
            header("Location: marcas.php");
        }
        exit();
    }
    /**
     * Actualizar marca
     */
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: marcas.php");
            exit();
        }
        // Validar
        if (empty($_POST['id_marca']) || empty($_POST['nombre_marca'])) {
            $_SESSION['error'] = 'Datos incompletos';
            header("Location: marcas.php");
            exit();
        }
        // Preparar datos
        $datos = [
            'idMarca' => (int)$_POST['id_marca'],
            'nombreMarca' => trim($_POST['nombre_marca']),
            'logoMarca' => trim($_POST['logo_marca']) ?: null,
            'descripcionMarca' => trim($_POST['descripcion_marca']) ?: null,
            'sitioWeb' => trim($_POST['sitio_web']) ?: null
        ];
        // Actualizar
        $resultado = $this->marcaModel->actualizar($datos);
        if (isset($resultado['error'])) {
            $_SESSION['error'] = 'Error al actualizar la marca: ' . $resultado['error'];
            $_SESSION['datos_form'] = $_POST;
            header("Location: marcas.php?accion=editar&id=" . $datos['idMarca']);
        } else {
            $_SESSION['exito'] = 'Marca actualizada exitosamente';
            header("Location: marcas.php");
        }
        exit();
    }
    /**
     * Cambiar estado
     */
    public function cambiarEstado() {
        if (!isset($_GET['id']) || !isset($_GET['estado'])) {
            $_SESSION['error'] = 'Parámetros incompletos';
            header("Location: marcas.php");
            exit();
        }
        $idMarca = (int)$_GET['id'];
        $estado = (int)$_GET['estado'];
        $resultado = $this->marcaModel->cambiarEstado($idMarca, $estado);
        if (isset($resultado['error'])) {
            $_SESSION['error'] = 'Error al cambiar estado: ' . $resultado['error'];
        } else {
            $mensaje = $estado == 1 ? 'activada' : 'desactivada';
            $_SESSION['exito'] = "Marca $mensaje exitosamente";
        }
        header("Location: marcas.php");
        exit();
    }
}
?>