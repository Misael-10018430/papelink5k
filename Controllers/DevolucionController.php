<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Devolucion.php';

class DevolucionController {
    private $devolucionModel;
    
    public function __construct() {
        $this->devolucionModel = new Devolucion();
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
     * Listar devoluciones
     */
    public function listar() {
        $this->verificarAdmin();
        
        $estado = $_GET['estado'] ?? null;
        $fechaInicio = $_GET['fecha_inicio'] ?? null;
        $fechaFin = $_GET['fecha_fin'] ?? null;
        $pagina = $_GET['pagina'] ?? 1;
        
        return $this->devolucionModel->obtenerTodas($estado, $fechaInicio, $fechaFin, $pagina);
    }
    
    /**
     * Ver detalle
     */
    public function verDetalle() {
        $this->verificarAdmin();
        
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = 'ID no especificado';
            header('Location: devoluciones.php');
            exit;
        }
        
        $idDevolucion = (int)$_GET['id'];
        return $this->devolucionModel->obtenerDetalle($idDevolucion);
    }
    
    /**
     * Cambiar estado
     */
    public function cambiarEstado() {
        $this->verificarAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: devoluciones.php');
            exit;
        }
        
        $idDevolucion = (int)$_POST['id_devolucion'];
        $nuevoEstado = $_POST['estado'];
        
        $resultado = $this->devolucionModel->cambiarEstado($idDevolucion, $nuevoEstado);
        
        if (isset($resultado['success'])) {
            $_SESSION['exito'] = 'Estado actualizado: ' . $nuevoEstado;
        } else {
            $_SESSION['error'] = $resultado['error'];
        }
        
        header('Location: devolucion_detalle.php?id=' . $idDevolucion);
        exit;
    }
    
    /**
     * Reintegrar productos
     */
    public function reintegrar() {
        $this->verificarAdmin();
        
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = 'ID no especificado';
            header('Location: devoluciones.php');
            exit;
        }
        
        $idDevolucion = (int)$_GET['id'];
        $resultado = $this->devolucionModel->reintegrarProductos($idDevolucion);
        
        if (isset($resultado['success'])) {
            $_SESSION['exito'] = 'Productos reintegrados al inventario';
        } else {
            $_SESSION['error'] = $resultado['error'];
        }
        
        header('Location: devolucion_detalle.php?id=' . $idDevolucion);
        exit;
    }
}

// Manejo de acciones
if (basename($_SERVER['PHP_SELF']) === 'DevolucionController.php') {
    $controller = new DevolucionController();
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'cambiar_estado':
            $controller->cambiarEstado();
            break;
        case 'reintegrar':
            $controller->reintegrar();
            break;
        default:
            header('Location: ../view/admin/devoluciones.php');
            exit;
    }
}
?>