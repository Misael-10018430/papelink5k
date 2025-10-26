<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Envio.php';
class EnvioController {
    private $envioModel;
    public function __construct() {
        $this->envioModel = new Envio();
    }
    /**
     * Verificar que sea admin
     */
    private function verificarAdmin() {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'empleado') {
            $_SESSION['error'] = 'Acceso denegado';
            header('Location: ../admin/login.php');
            exit;
        }
    }
    /**
     * Listar envíos pendientes
     */
    public function listar() {
        $this->verificarAdmin();
        return $this->envioModel->obtenerPendientes();
    }
    /**
     * Ver detalle
     */
    public function verDetalle() {
        $this->verificarAdmin();
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = 'ID no especificado';
            header('Location: envios.php');
            exit;
        }
        $idEnvio = (int)$_GET['id'];
        return $this->envioModel->obtenerDetalle($idEnvio);
    }
    /**
     * Actualizar envío
     */
    public function actualizar() {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: envios.php');
            exit;
        }
        $idEnvio = (int)$_POST['id_envio'];
        $datos = [
            'id_estado_envio' => $_POST['id_estado_envio'] ?? null,
            'fecha_envio' => $_POST['fecha_envio'] ?? null,
            'fecha_entrega_estimada' => $_POST['fecha_entrega_estimada'] ?? null,
            'observaciones' => $_POST['observaciones'] ?? null
        ];
        $resultado = $this->envioModel->actualizar($idEnvio, $datos);
        if (isset($resultado['success'])) {
            $_SESSION['exito'] = 'Envío actualizado correctamente';
        } else {
            $_SESSION['error'] = $resultado['error'];
        }
        header('Location: envio_detalle.php?id=' . $idEnvio);
        exit;
    }
    /**
     * Obtener estados
     */
    public function obtenerEstados() {
        return $this->envioModel->obtenerEstados();
    }
}
// Manejo de acciones
if (basename($_SERVER['PHP_SELF']) === 'EnvioController.php') {
    $controller = new EnvioController();
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'actualizar':
            $controller->actualizar();
            break;
        default:
            header('Location: ../view/admin/envios.php');
            exit;
    }
}
?>