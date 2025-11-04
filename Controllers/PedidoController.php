<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Pedido.php';

class PedidoController {
    private $pedidoModel;
    
    public function __construct() {
        $this->pedidoModel = new Pedido();
    }

    // ==========================================
    // MÉTODOS PARA CLIENTE
    // ==========================================
    
    /**
     * Crear pedido desde carrito (CLIENTE)
     */
    public function crear() {
        // Verificar que el cliente esté logueado
        if (!isset($_SESSION['cliente_id'])) {
            $_SESSION['error'] = 'Debe iniciar sesión para realizar un pedido';
            header('Location: ' . BASE_URL . 'view/cliente/login.php');
            exit;
        }
        
        // Verificar que sea POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'view/cliente/carrito.php');
            exit;
        }
        
        $idCliente = $_SESSION['cliente_id'];
        
        // Validar campos requeridos
        $errores = [];
        if (empty($_POST['tipo_envio'])) {
            $errores[] = 'El tipo de envío es requerido';
        }
        if (empty($_POST['direccion_envio'])) {
            $errores[] = 'La dirección de envío es requerida';
        }
        if (empty($_POST['ciudad_envio'])) {
            $errores[] = 'La ciudad es requerida';
        }
        if (empty($_POST['codigo_postal_envio'])) {
            $errores[] = 'El código postal es requerido';
        }
        
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_form'] = $_POST;
            header('Location: ' . BASE_URL . 'view/cliente/checkout.php');
            exit;
        }
        
        // Sanitizar datos
        $tipoEnvio = trim($_POST['tipo_envio']);
        $direccionEnvio = trim($_POST['direccion_envio']);
        $ciudadEnvio = trim($_POST['ciudad_envio']);
        $codigoPostalEnvio = trim($_POST['codigo_postal_envio']);
        $referenciasAdicionales = isset($_POST['referencias_adicionales']) ? trim($_POST['referencias_adicionales']) : '';
        
        // Validar tipo de envío
        if (!in_array($tipoEnvio, ['Domicilio', 'Sucursal'])) {
            $_SESSION['error'] = 'Tipo de envío inválido';
            header('Location: ' . BASE_URL . 'view/cliente/checkout.php');
            exit;
        }
        
        // Crear pedido
        $resultado = $this->pedidoModel->crearDesdeCarrito(
            $idCliente,
            $tipoEnvio,
            $direccionEnvio,
            $ciudadEnvio,
            $codigoPostalEnvio,
            $referenciasAdicionales
        );
        
        if (isset($resultado['success'])) {
            $_SESSION['exito'] = 'Pedido creado exitosamente. Número de pedido: ' . $resultado['numero_pedido'];
            header('Location: ' . BASE_URL . 'view/cliente/confirmacion.php?pedido=' . $resultado['id_pedido']);
        } else {
            $_SESSION['error'] = $resultado['error'];
            header('Location: ' . BASE_URL . 'view/cliente/checkout.php');
        }
        exit;
    }

    /**
     * Obtener pedidos del cliente logueado
     */
    public function misPedidos() {
        // Verificar que el cliente esté logueado
        if (!isset($_SESSION['cliente_id'])) {
            $_SESSION['error'] = 'Debe iniciar sesión para ver sus pedidos';
            header('Location: ' . BASE_URL . 'view/cliente/login.php');
            exit;
        }
        
        $idCliente = (int)$_SESSION['cliente_id'];
        $estadoFiltro = $_GET['estado'] ?? null;
        $pedidos = $this->pedidoModel->obtenerPorCliente($idCliente, 100, $estadoFiltro);
        
        return $pedidos;
    }

    /**
     * Ver detalle de un pedido (CLIENTE)
     */
    public function verDetalle($idPedido = null) {
        if ($idPedido === null && isset($_GET['id'])) {
            $idPedido = (int)$_GET['id'];
        }
        
        if (!$idPedido) {
            return null;
        }
        
        $idCliente = isset($_SESSION['cliente_id']) ? $_SESSION['cliente_id'] : null;
        
        return $this->pedidoModel->obtenerDetalle($idPedido, $idCliente);
    }

    /**
     * Cancelar pedido (CONTROLLER)
     */
    public function cancelar() {
        // Verificar que el cliente esté logueado
        if (!isset($_SESSION['cliente_id'])) {
            $_SESSION['error'] = 'Debe iniciar sesión';
            header('Location: ' . BASE_URL . 'view/cliente/login.php');
            exit;
        }
        
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = 'ID de pedido no especificado';
            header('Location: ' . BASE_URL . 'view/cliente/mis_pedidos.php');
            exit;
        }
        
        $idPedido = (int)$_GET['id'];
        $idCliente = $_SESSION['cliente_id'];
        
        // Confirmar cancelación
        if (!isset($_GET['confirmar'])) {
            $_SESSION['confirmacion'] = [
                'mensaje' => '¿Está seguro que desea cancelar este pedido?',
                'url_confirmar' => BASE_URL . 'controllers/PedidoController.php?action=cancelar&id=' . $idPedido . '&confirmar=si',
                'url_cancelar' => BASE_URL . 'view/cliente/mis_pedidos.php'
            ];
            header('Location: ' . BASE_URL . 'view/cliente/mis_pedidos.php');
            exit;
        }
        
        $resultado = $this->pedidoModel->cancelarPedido($idPedido, $idCliente);
        
        if (isset($resultado['success'])) {
            $_SESSION['exito'] = 'Pedido cancelado exitosamente';
        } else {
            $_SESSION['error'] = $resultado['error'];
        }
        
        header('Location: ' . BASE_URL . 'view/cliente/mis_pedidos.php');
        exit;
    }

    // ==========================================
    // MÉTODOS PARA ADMIN
    // ==========================================
    
    /**
     * Listar todos los pedidos (ADMIN)
     */
    public function listar() {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'empleado') {
            $_SESSION['error'] = 'Acceso denegado';
            header('Location: ' . BASE_URL . 'view/admin/login.php');
            exit;
        }
        
        $estado = isset($_GET['estado']) ? trim($_GET['estado']) : '';
        $fechaDesde = isset($_GET['fecha_desde']) ? trim($_GET['fecha_desde']) : '';
        $fechaHasta = isset($_GET['fecha_hasta']) ? trim($_GET['fecha_hasta']) : '';
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $limite = 20;
        
        $pedidos = $this->pedidoModel->obtenerTodos(
            $estado,
            $fechaDesde,
            $fechaHasta,
            $pagina,
            $limite
        );
        
        return $pedidos;
    }

    /**
     * Ver detalle de pedido (ADMIN)
     */
    public function verDetalleAdmin() {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'empleado') {
            $_SESSION['error'] = 'Acceso denegado';
            header('Location: ' . BASE_URL . 'view/admin/login.php');
            exit;
        }
        
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = 'ID de pedido no especificado';
            header('Location: ' . BASE_URL . 'view/admin/pedidos.php');
            exit;
        }
        
        $idPedido = (int)$_GET['id'];
        $detalle = $this->pedidoModel->obtenerDetalle($idPedido);
        
        if (empty($detalle)) {
            $_SESSION['error'] = 'Pedido no encontrado';
            header('Location: ' . BASE_URL . 'view/admin/pedidos.php');
            exit;
        }
        
        return $detalle;
    }

    /**
     * Cambiar estado de pedido (ADMIN)
     */
    public function cambiarEstado() {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'empleado') {
            $_SESSION['error'] = 'Acceso denegado';
            header('Location: ' . BASE_URL . 'view/admin/login.php');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'view/admin/pedidos.php');
            exit;
        }
        
        if (!isset($_POST['id_pedido']) || !isset($_POST['estado'])) {
            $_SESSION['error'] = 'Datos incompletos';
            header('Location: ' . BASE_URL . 'view/admin/pedidos.php');
            exit;
        }
        
        $idPedido = (int)$_POST['id_pedido'];
        $estado = trim($_POST['estado']);
        
        $estadosValidos = ['Pendiente', 'En Proceso', 'Enviado', 'Completado', 'Cancelado'];
        if (!in_array($estado, $estadosValidos)) {
            $_SESSION['error'] = 'Estado inválido';
            header('Location: ' . BASE_URL . 'view/admin/pedidos.php');
            exit;
        }
        
        $resultado = $this->pedidoModel->cambiarEstado($idPedido, $estado);
        
        if (isset($resultado['success'])) {
            $_SESSION['exito'] = 'Estado del pedido actualizado a: ' . $estado;
        } else {
            $_SESSION['error'] = $resultado['error'];
        }
        
        if (isset($_POST['redirigir']) && $_POST['redirigir'] === 'detalle') {
            header('Location: ' . BASE_URL . 'view/admin/pedido_detalle.php?id=' . $idPedido);
        } else {
            header('Location: ' . BASE_URL . 'view/admin/pedidos.php');
        }
        exit;
    }
}

// =====================================================
// MANEJO DE ACCIONES
// =====================================================
if (isset($_GET['action'])) {
    $controller = new PedidoController();
    $action = $_GET['action'];
    
    switch ($action) {
        case 'crear':
            $controller->crear();
            break;
        case 'cancelar':
            $controller->cancelar();
            break;
        default:
            header('Location: ' . BASE_URL);
            break;
    }
}
?>