<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Pedidos.php';

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
            header('Location: ' . BASE_URL . '/views/cliente/login.php');
            exit;
        }
        // Verificar que sea POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/views/cliente/carrito.php');
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
            header('Location: ' . BASE_URL . '/views/cliente/checkout.php');
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
            header('Location: ' . BASE_URL . '/views/cliente/checkout.php');
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
     * Ver pedidos del cliente (CLIENTE)
     */
    public function misPedidos() {
        // Verificar que el cliente esté logueado
        if (!isset($_SESSION['cliente_id'])) {
            $_SESSION['error'] = 'Debe iniciar sesión';
            header('Location: ' . BASE_URL . '/views/cliente/login.php');
            exit;
        }
        $idCliente = $_SESSION['cliente_id'];
        $limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 10;
        $pedidos = $this->pedidoModel->obtenerPorCliente($idCliente, $limite);
        // La vista mostrará los pedidos
        return $pedidos;
    }
    /**
     * Ver detalle de un pedido (CLIENTE)
     */
    public function verDetalle() {
        // Verificar que el cliente esté logueado
        if (!isset($_SESSION['cliente_id'])) {
            $_SESSION['error'] = 'Debe iniciar sesión';
            header('Location: ' . BASE_URL . '/views/cliente/login.php');
            exit;
        }
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = 'ID de pedido no especificado';
            header('Location: ' . BASE_URL . '/views/cliente/mis_pedidos.php');
            exit;
        }
        $idPedido = (int)$_GET['id'];
        $idCliente = $_SESSION['cliente_id'];
        $detalle = $this->pedidoModel->obtenerDetalle($idPedido, $idCliente);
        if (empty($detalle)) {
            $_SESSION['error'] = 'Pedido no encontrado';
            header('Location: ' . BASE_URL . '/views/cliente/mis_pedidos.php');
            exit;
        }
        // La vista mostrará el detalle
        return $detalle;
    }
    /**
     * Cancelar pedido (CLIENTE)
     */
    public function cancelar() {
        // Verificar que el cliente esté logueado
        if (!isset($_SESSION['cliente_id'])) {
            $_SESSION['error'] = 'Debe iniciar sesión';
            header('Location: ' . BASE_URL . '/views/cliente/login.php');
            exit;
        }
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = 'ID de pedido no especificado';
            header('Location: ' . BASE_URL . '/views/cliente/mis_pedidos.php');
            exit;
        }
        $idPedido = (int)$_GET['id'];
        $idCliente = $_SESSION['cliente_id'];
        // Confirmar cancelación (prevenir cancelación accidental)
        if (!isset($_GET['confirmar'])) {
            $_SESSION['confirmacion'] = [
                'mensaje' => '¿Está seguro que desea cancelar este pedido?',
                'url_confirmar' => BASE_URL . '/controllers/PedidoController.php?action=cancelar&id=' . $idPedido . '&confirmar=si',
                'url_cancelar' => BASE_URL . '/views/cliente/mis_pedidos.php'
            ];
            header('Location: ' . BASE_URL . '/views/cliente/mis_pedidos.php');
            exit;
        }
        $resultado = $this->pedidoModel->cancelar($idPedido, $idCliente);
        if (isset($resultado['success'])) {
            $_SESSION['exito'] = 'Pedido cancelado exitosamente';
        } else {
            $_SESSION['error'] = $resultado['error'];
        }
        header('Location: ' . BASE_URL . '/views/cliente/mis_pedidos.php');
        exit;
    }
    // ==========================================
    // MÉTODOS PARA ADMIN
    // ==========================================
    /**
     * Listar todos los pedidos (ADMIN)
     */
    public function listar() {
        // Verificar que sea admin
        if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'empleado') {
            $_SESSION['error'] = 'Acceso denegado';
            header('Location: ' . BASE_URL . '/views/admin/login.php');
            exit;
        }
        // Obtener filtros
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
        // La vista mostrará los pedidos
        return $pedidos;
    }
    /**
     * Ver detalle de pedido (ADMIN)
     */
    public function verDetalleAdmin() {
        // Verificar que sea admin
        if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'empleado') {
            $_SESSION['error'] = 'Acceso denegado';
            header('Location: ' . BASE_URL . '/views/admin/login.php');
            exit;
        }
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = 'ID de pedido no especificado';
            header('Location: ' . BASE_URL . '/views/admin/pedidos.php');
            exit;
        }
        $idPedido = (int)$_GET['id'];
        // Admin puede ver cualquier pedido (sin filtro de cliente)
        $detalle = $this->pedidoModel->obtenerDetalle($idPedido);
        if (empty($detalle)) {
            $_SESSION['error'] = 'Pedido no encontrado';
            header('Location: ' . BASE_URL . '/views/admin/pedidos.php');
            exit;
        }
        // La vista mostrará el detalle
        return $detalle;
    }
    /**
     * Cambiar estado de pedido (ADMIN)
     */
    public function cambiarEstado() {
        // Verificar que sea admin
        if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'empleado') {
            $_SESSION['error'] = 'Acceso denegado';
            header('Location: ' . BASE_URL . '/views/admin/login.php');
            exit;
        }
        // Verificar que sea POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/views/admin/pedidos.php');
            exit;
        }
        if (!isset($_POST['id_pedido']) || !isset($_POST['estado'])) {
            $_SESSION['error'] = 'Datos incompletos';
            header('Location: ' . BASE_URL . '/views/admin/pedidos.php');
            exit;
        }
        $idPedido = (int)$_POST['id_pedido'];
        $estado = trim($_POST['estado']);
        // Validar estado
        $estadosValidos = ['Pendiente', 'En Proceso', 'Enviado', 'Completado', 'Cancelado'];
        if (!in_array($estado, $estadosValidos)) {
            $_SESSION['error'] = 'Estado inválido';
            header('Location: ' . BASE_URL . '/views/admin/pedidos.php');
            exit;
        }
        $resultado = $this->pedidoModel->cambiarEstado($idPedido, $estado);
        if (isset($resultado['success'])) {
            $_SESSION['exito'] = 'Estado del pedido actualizado a: ' . $estado;
        } else {
            $_SESSION['error'] = $resultado['error'];
        }
        // Redirigir al detalle del pedido o al listado
        if (isset($_POST['redirigir']) && $_POST['redirigir'] === 'detalle') {
            header('Location: ' . BASE_URL . '/views/admin/pedido_detalle.php?id=' . $idPedido);
        } else {
            header('Location: ' . BASE_URL . '/views/admin/pedidos.php');
        }
        exit;
    }
}
// ==========================================
// MANEJO DE ACCIONES
// ==========================================
// Solo ejecutar si se accede directamente al controller
if (basename($_SERVER['PHP_SELF']) === 'PedidoController.php') {
    $controller = new PedidoController();
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    switch ($action) {
        case 'crear':
            $controller->crear();
            break;
        case 'cancelar':
            $controller->cancelar();
            break;
        case 'cambiar_estado':
            $controller->cambiarEstado();
            break;
        default:
            // Redirigir según tipo de usuario
            if (isset($_SESSION['tipo_usuario'])) {
                if ($_SESSION['tipo_usuario'] === 'cliente') {
                    header('Location: ' . BASE_URL . '/views/cliente/mis_pedidos.php');
                } else {
                    header('Location: ' . BASE_URL . '/views/admin/pedidos.php');
                }
            } else {
                header('Location: ' . BASE_URL . '/index.php');
            }
            exit;
    }
}
?>