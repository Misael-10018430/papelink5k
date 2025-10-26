<?php
/**
 * Controlador de Configuración
 * Maneja todas las acciones relacionadas con la configuración del sistema
 */
require_once __DIR__ . '/../models/Configuracion.php';

class ConfiguracionController {
    private $configuracionModel;
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }       
        $this->configuracionModel = new Configuracion();
    }
    /**
     * Obtener todas las configuraciones y datos del sistema
     */
    public function index() {
        $configuraciones = $this->configuracionModel->obtenerConfiguraciones();
        $rolesConFuncionalidades = $this->configuracionModel->obtenerRolesConFuncionalidades();
        $estadosPedido = $this->configuracionModel->obtenerEstadosPedido();
        $estadosEnvio = $this->configuracionModel->obtenerEstadosEnvio();
        return [
            'configuraciones' => $configuraciones,
            'roles' => $rolesConFuncionalidades,
            'estados_pedido' => $estadosPedido,
            'estados_envio' => $estadosEnvio
        ];
    }
    /**
     * Guardar configuraciones (AJAX)
     */
    public function guardar() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
            exit;
        }
        // Validar datos
        $configuraciones = [];
        
        // Configuraciones de empresa
        if (isset($_POST['NOMBRE_EMPRESA'])) {
            $configuraciones['NOMBRE_EMPRESA'] = trim($_POST['NOMBRE_EMPRESA']);
        }
        if (isset($_POST['DIRECCION_EMPRESA'])) {
            $configuraciones['DIRECCION_EMPRESA'] = trim($_POST['DIRECCION_EMPRESA']);
        }
        if (isset($_POST['TELEFONO_EMPRESA'])) {
            $configuraciones['TELEFONO_EMPRESA'] = trim($_POST['TELEFONO_EMPRESA']);
        }
        if (isset($_POST['EMAIL_EMPRESA'])) {
            $configuraciones['EMAIL_EMPRESA'] = trim($_POST['EMAIL_EMPRESA']);
        }
        if (isset($_POST['MONEDA'])) {
            $configuraciones['MONEDA'] = trim($_POST['MONEDA']);
        }
        if (isset($_POST['TASA_IVA'])) {
            $configuraciones['TASA_IVA'] = trim($_POST['TASA_IVA']);
        }
        // Configuraciones de inventario
        if (isset($_POST['STOCK_MINIMO_ALERTA'])) {
            $configuraciones['STOCK_MINIMO_ALERTA'] = trim($_POST['STOCK_MINIMO_ALERTA']);
        }
        // Configuraciones de ventas
        if (isset($_POST['DIAS_DEVOLUCION'])) {
            $configuraciones['DIAS_DEVOLUCION'] = trim($_POST['DIAS_DEVOLUCION']);
        }
        if (isset($_POST['DIAS_ENTREGA_ESTIMADA'])) {
            $configuraciones['DIAS_ENTREGA_ESTIMADA'] = trim($_POST['DIAS_ENTREGA_ESTIMADA']);
        }
        if (isset($_POST['COSTO_ENVIO_ESTANDAR'])) {
            $configuraciones['COSTO_ENVIO_ESTANDAR'] = trim($_POST['COSTO_ENVIO_ESTANDAR']);
        }
        if (empty($configuraciones)) {
            echo json_encode(['success' => false, 'mensaje' => 'No se recibieron configuraciones']);
            exit;
        }
        // Actualizar configuraciones
        $resultado = $this->configuracionModel->actualizarMultiple($configuraciones);
        if ($resultado['success']) {
            $_SESSION['success'] = $resultado['mensaje'];
        }
        echo json_encode($resultado);
        exit;
    }
}
// ========================================
// MANEJO DE ACCIONES DIRECTAS (AJAX)
// ========================================
if (isset($_GET['action']) && basename($_SERVER['PHP_SELF']) === 'ConfiguracionController.php') {
    $controller = new ConfiguracionController();
    $action = $_GET['action'];
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'mensaje' => 'Acción no encontrada']);
        exit;
    }
}