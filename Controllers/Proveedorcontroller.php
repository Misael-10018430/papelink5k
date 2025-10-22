<?php
/**
 * Controlador de Proveedores
 * Maneja todas las acciones relacionadas con la gestión de proveedores
 */

require_once __DIR__ . '/../models/Proveedor.php';

class ProveedorController {
    private $proveedorModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->proveedorModel = new Proveedor();
    }

    /**
     * Listar proveedores con filtros
     */
    public function listar() {
        $estado = isset($_GET['estado']) && $_GET['estado'] !== '' ? (int)$_GET['estado'] : null;
        
        $proveedores = $this->proveedorModel->obtenerProveedores($estado);
        
        return [
            'proveedores' => $proveedores,
            'filtro_estado' => $estado
        ];
    }

    /**
     * Ver detalle de un proveedor
     */
    public function verDetalle($idProveedor) {
        if (!$idProveedor || !is_numeric($idProveedor)) {
            $_SESSION['error'] = 'ID de proveedor inválido';
            header('Location: proveedores.php');
            exit;
        }

        $proveedor = $this->proveedorModel->obtenerPorId($idProveedor);
        
        if (!$proveedor) {
            $_SESSION['error'] = 'Proveedor no encontrado';
            header('Location: proveedores.php');
            exit;
        }

        $estadisticas = $this->proveedorModel->obtenerEstadisticas($idProveedor);
        $historial = $this->proveedorModel->obtenerHistorialCompras($idProveedor);

        return [
            'proveedor' => $proveedor,
            'estadisticas' => $estadisticas,
            'historial' => $historial
        ];
    }

    /**
     * Mostrar formulario para crear/editar
     */
    public function mostrarFormulario($idProveedor = null) {
        if ($idProveedor) {
            $proveedor = $this->proveedorModel->obtenerPorId($idProveedor);
            if (!$proveedor) {
                $_SESSION['error'] = 'Proveedor no encontrado';
                header('Location: proveedores.php');
                exit;
            }
            return ['proveedor' => $proveedor, 'modo' => 'editar'];
        }
        
        return ['proveedor' => null, 'modo' => 'crear'];
    }

    /**
     * Guardar proveedor (crear o actualizar)
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: proveedores.php');
            exit;
        }

        // Validar datos
        $errores = [];
        
        $nombre = trim($_POST['nombre'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $idProveedor = isset($_POST['id_proveedor']) ? (int)$_POST['id_proveedor'] : null;

        if (empty($nombre)) {
            $errores[] = 'El nombre del proveedor es obligatorio';
        }

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no es válido';
        }

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_form'] = $_POST;
            
            if ($idProveedor) {
                header("Location: proveedor_form.php?id=$idProveedor");
            } else {
                header('Location: proveedor_form.php');
            }
            exit;
        }

        // Preparar datos
        $datos = [
            'nombre' => $nombre,
            'telefono' => $telefono,
            'email' => $email,
            'direccion' => $direccion
        ];

        // Crear o actualizar
        if ($idProveedor) {
            $resultado = $this->proveedorModel->actualizar($idProveedor, $datos);
        } else {
            $resultado = $this->proveedorModel->crear($datos);
        }

        if ($resultado['success']) {
            $_SESSION['success'] = $resultado['mensaje'];
            header('Location: proveedores.php');
        } else {
            $_SESSION['error'] = $resultado['mensaje'];
            if ($idProveedor) {
                header("Location: proveedor_form.php?id=$idProveedor");
            } else {
                header('Location: proveedor_form.php');
            }
        }
        exit;
    }

    /**
     * Cambiar estado del proveedor (AJAX)
     */
    public function cambiarEstado() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
            exit;
        }

        $idProveedor = isset($_POST['id_proveedor']) ? (int)$_POST['id_proveedor'] : 0;
        $estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 0;

        if (!$idProveedor) {
            echo json_encode(['success' => false, 'mensaje' => 'ID de proveedor inválido']);
            exit;
        }

        $resultado = $this->proveedorModel->cambiarEstado($idProveedor, $estado);
        echo json_encode($resultado);
        exit;
    }
}

// ========================================
// MANEJO DE ACCIONES DIRECTAS (AJAX)
// ========================================
if (isset($_GET['action']) && basename($_SERVER['PHP_SELF']) === 'ProveedorController.php') {
    $controller = new ProveedorController();
    $action = $_GET['action'];
    
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'mensaje' => 'Acción no encontrada']);
        exit;
    }
}