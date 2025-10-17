<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Carrito.php';

class CarritoController {
    private $carritoModel;
    
    public function __construct() {
        $this->carritoModel = new Carrito();
    }
    
    /**
     * Verificar que el cliente esté logueado
     */
    private function verificarCliente() {
        if (!isset($_SESSION['cliente_id'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para usar el carrito';
            header("Location: ../../view/cliente/login.php");
            exit();
        }
        return $_SESSION['cliente_id'];
    }
    
    /**
     * Agregar producto al carrito
     */
    public function agregar() {
        $idCliente = $this->verificarCliente();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ../../view/cliente/productos.php");
            exit();
        }
        
        $idProducto = (int)$_POST['id_producto'];
        $cantidad = (int)$_POST['cantidad'];
        
        if ($cantidad <= 0) {
            $_SESSION['error'] = 'Cantidad inválida';
            header("Location: ../../view/cliente/producto_detalle.php?id=" . $idProducto);
            exit();
        }
        
        $resultado = $this->carritoModel->agregar($idCliente, $idProducto, $cantidad);
        
        if (isset($resultado['success'])) {
            $_SESSION['exito'] = $resultado['mensaje'];
        } else {
            $_SESSION['error'] = $resultado['error'];
        }
        
        header("Location: ../../view/cliente/carrito.php");
        exit();
    }
    
    /**
     * Ver carrito
     */
    public function ver() {
        $idCliente = $this->verificarCliente();
        
        $items = $this->carritoModel->obtenerPorCliente($idCliente);
        $totales = $this->carritoModel->obtenerTotales($idCliente);
        
        return [
            'items' => $items,
            'totales' => $totales
        ];
    }
    
    /**
     * Actualizar cantidad
     */
    public function actualizarCantidad() {
        $this->verificarCliente();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ../../view/cliente/carrito.php");
            exit();
        }
        
        $idCarrito = (int)$_POST['id_carrito'];
        $cantidad = (int)$_POST['cantidad'];
        
        $resultado = $this->carritoModel->actualizarCantidad($idCarrito, $cantidad);
        
        if (isset($resultado['success'])) {
            $_SESSION['exito'] = $resultado['mensaje'];
        } else {
            $_SESSION['error'] = $resultado['error'];
        }
        
        header("Location: ../../view/cliente/carrito.php");
        exit();
    }
    
    /**
     * Eliminar producto
     */
    public function eliminar() {
        $this->verificarCliente();
        
        if (!isset($_GET['id'])) {
            header("Location: ../../view/cliente/carrito.php");
            exit();
        }
        
        $idCarrito = (int)$_GET['id'];
        
        $resultado = $this->carritoModel->eliminar($idCarrito);
        
        if (isset($resultado['success'])) {
            $_SESSION['exito'] = $resultado['mensaje'];
        } else {
            $_SESSION['error'] = $resultado['error'];
        }
        
        header("Location: ../../view/cliente/carrito.php");
        exit();
    }
    
    /**
     * Vaciar carrito
     */
    public function vaciar() {
        $idCliente = $this->verificarCliente();
        
        $resultado = $this->carritoModel->vaciar($idCliente);
        
        if (isset($resultado['success'])) {
            $_SESSION['exito'] = $resultado['mensaje'];
        } else {
            $_SESSION['error'] = $resultado['error'];
        }
        
        header("Location: ../../view/cliente/carrito.php");
        exit();
    }
    
    /**
     * Contar items (para header)
     */
    public function contarItems($idCliente) {
        return $this->carritoModel->contarItems($idCliente);
    }
    
    /**
     * Contar productos en el carrito (para AJAX)
     */
    public function contar() {
        // No verificar cliente aquí porque esta función debe poder ejecutarse sin redirección
        if (!isset($_SESSION['cliente_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['cantidad' => 0]);
            exit;
        }
        
        try {
            $carritoData = $this->ver();
            $cantidad = count($carritoData['items']);
            
            header('Content-Type: application/json');
            echo json_encode(['cantidad' => $cantidad, 'success' => true]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['cantidad' => 0, 'success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
}

// =====================================================
// ENRUTADOR: Procesar las acciones del carrito
// =====================================================
if (isset($_GET['action'])) {
    $controller = new CarritoController();
    $action = $_GET['action'];
    
    switch ($action) {
        case 'agregar':
            $controller->agregar();
            break;
        
        case 'actualizar':
            $controller->actualizarCantidad();
            break;
        
        case 'eliminar':
            $controller->eliminar();
            break;
        
        case 'vaciar':
            $controller->vaciar();
            break;
        
        case 'contar':
            $controller->contar();
            break;
        
        default:
            header("Location: ../../view/cliente/carrito.php");
            break;
    }
} 
else {
    // Si no hay acción, mostrar el carrito
    header("Location: ../../view/cliente/carrito.php");
}
?>