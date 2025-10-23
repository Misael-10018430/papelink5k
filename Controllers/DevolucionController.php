<?php
/**
 * Controlador de Devoluciones (Cliente)
 * Maneja todas las acciones de devoluciones del cliente
 */

require_once __DIR__ . '/../models/Devolucion.php';

class DevolucionController {
    private $devolucionModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar que el cliente esté autenticado
        if (!isset($_SESSION['cliente_id'])) {
            header('Location: ../../view/cliente/login.php');
            exit();
        }
        
        $this->devolucionModel = new Devolucion();
    }

    /**
     * Listar devoluciones del cliente (AJAX)
     */
    public function listarDevoluciones() {
        header('Content-Type: application/json');

        $idCliente = $_SESSION['cliente_id'];
        $devoluciones = $this->devolucionModel->obtenerDevolucionesCliente($idCliente);
        
        echo json_encode([
            'success' => true,
            'devoluciones' => $devoluciones
        ]);
        exit;
    }

    /**
     * Obtener detalle de devolución (AJAX)
     */
    public function obtenerDetalle() {
        header('Content-Type: application/json');

        if (!isset($_GET['id'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID de devolución no proporcionado']);
            exit;
        }

        $idDevolucion = (int)$_GET['id'];
        $idCliente = $_SESSION['cliente_id'];
        
        $detalle = $this->devolucionModel->obtenerDetalleDevolucion($idDevolucion, $idCliente);
        
        if ($detalle['informacion']) {
            echo json_encode([
                'success' => true,
                'detalle' => $detalle
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Devolución no encontrada o no autorizada'
            ]);
        }
        exit;
    }

    /**
     * Obtener pedidos devolvibles (AJAX)
     */
    public function obtenerPedidosDevolvibles() {
        header('Content-Type: application/json');

        $idCliente = $_SESSION['cliente_id'];
        $pedidos = $this->devolucionModel->obtenerPedidosDevolvibles($idCliente);
        
        echo json_encode([
            'success' => true,
            'pedidos' => $pedidos
        ]);
        exit;
    }

    /**
     * Obtener detalle de pedido para devolución (AJAX)
     */
    public function obtenerDetallePedido() {
        header('Content-Type: application/json');

        if (!isset($_GET['id'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID de pedido no proporcionado']);
            exit;
        }

        $idPedido = (int)$_GET['id'];
        $idCliente = $_SESSION['cliente_id'];
        
        $detalle = $this->devolucionModel->obtenerDetallePedido($idPedido, $idCliente);
        
        if ($detalle['informacion']) {
            echo json_encode([
                'success' => true,
                'detalle' => $detalle
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Pedido no encontrado o no autorizado'
            ]);
        }
        exit;
    }

    /**
     * Solicitar nueva devolución (AJAX)
     */
    public function solicitarDevolucion() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
            exit;
        }

        $idPedido = isset($_POST['id_pedido']) ? (int)$_POST['id_pedido'] : 0;
        $motivo = $_POST['motivo'] ?? '';
        $productosJSON = $_POST['productos'] ?? '';

        // Validaciones
        if ($idPedido <= 0) {
            echo json_encode(['success' => false, 'mensaje' => 'Pedido no válido']);
            exit;
        }

        if (empty($motivo)) {
            echo json_encode(['success' => false, 'mensaje' => 'El motivo es obligatorio']);
            exit;
        }

        if (empty($productosJSON)) {
            echo json_encode(['success' => false, 'mensaje' => 'Debe seleccionar al menos un producto']);
            exit;
        }

        // Validar que el JSON sea válido
        $productos = json_decode($productosJSON, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['success' => false, 'mensaje' => 'Formato de productos inválido']);
            exit;
        }

        if (empty($productos)) {
            echo json_encode(['success' => false, 'mensaje' => 'Debe seleccionar al menos un producto']);
            exit;
        }

        $idCliente = $_SESSION['cliente_id'];
        
        // Solicitar devolución
        $resultado = $this->devolucionModel->solicitarDevolucion(
            $idPedido, 
            $idCliente, 
            $motivo, 
            $productosJSON
        );
        
        echo json_encode($resultado);
        exit;
    }

    /**
     * Obtener días permitidos para devolución (AJAX)
     */
    public function obtenerDiasDevolucion() {
        header('Content-Type: application/json');

        $dias = $this->devolucionModel->obtenerDiasDevolucion();
        
        echo json_encode([
            'success' => true,
            'dias' => $dias
        ]);
        exit;
    }
}

// ========================================
// MANEJO DE ACCIONES DIRECTAS (AJAX)
// ========================================
if (isset($_GET['action']) && basename($_SERVER['PHP_SELF']) === 'DevolucionController.php') {
    $controller = new DevolucionController();
    $action = $_GET['action'];
    
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'mensaje' => 'Acción no encontrada']);
        exit;
    }
}