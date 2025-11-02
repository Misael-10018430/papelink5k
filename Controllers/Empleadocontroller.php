<?php
/**
 * Controlador de Empleados
 * Maneja todas las acciones relacionadas con empleados, roles y permisos
 */
require_once __DIR__ . '/../models/Empleado.php';
class EmpleadoController {
    private $empleadoModel;
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->empleadoModel = new Empleado();
    }
    /**
     * Listar empleados con filtros
     */
    public function listar() {
        $estado = isset($_GET['estado']) && $_GET['estado'] !== '' ? (int)$_GET['estado'] : null;
        $empleados = $this->empleadoModel->obtenerEmpleados($estado);
        $roles = $this->empleadoModel->obtenerRoles(1); // Solo roles activos
        return [
            'empleados' => $empleados,
            'roles' => $roles,
            'filtro_estado' => $estado
        ];
    }
    /**
     * Mostrar formulario para crear/editar
     */
    public function mostrarFormulario($idEmpleado = null) {
        $roles = $this->empleadoModel->obtenerRoles(1);
        
        if ($idEmpleado) {
            $empleado = $this->empleadoModel->obtenerPorId($idEmpleado);
            if (!$empleado) {
                $_SESSION['error'] = 'Empleado no encontrado';
                header('Location: empleados.php');
                exit;
            }
            return [
                'empleado' => $empleado,
                'roles' => $roles,
                'modo' => 'editar'
            ];
        }
        return [
            'empleado' => null,
            'roles' => $roles,
            'modo' => 'crear'
        ];
    }
    /**
     * Guardar empleado (crear o actualizar)
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: empleados.php');
            exit;
        }
        // Validar datos
        $errores = [];
        $nombre = trim($_POST['nombre'] ?? '');
        $usuario = trim($_POST['usuario'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $idRol = isset($_POST['id_rol']) && $_POST['id_rol'] !== '' ? (int)$_POST['id_rol'] : null;
        $idEmpleado = isset($_POST['id_empleado']) ? (int)$_POST['id_empleado'] : null;
        if (empty($nombre)) {
            $errores[] = 'El nombre completo es obligatorio';
        }
        if (!$idEmpleado) {
            // Validaciones para crear nuevo empleado
            if (empty($usuario)) {
                $errores[] = 'El nombre de usuario es obligatorio';
            }
            if (empty($password)) {
                $errores[] = 'La contraseña es obligatoria';
            } elseif (strlen($password) < 6) {
                $errores[] = 'La contraseña debe tener al menos 6 caracteres';
            }
        }
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no es válido';
        }
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_form'] = $_POST;
            
            if ($idEmpleado) {
                header("Location: empleado_form.php?id=$idEmpleado");
            } else {
                header('Location: empleado_form.php');
            }
            exit;
        }
        // Preparar datos
        $datos = [
            'nombre' => $nombre,
            'email' => $email
        ];
        // Crear o actualizar
        if ($idEmpleado) {
            $resultado = $this->empleadoModel->actualizar($idEmpleado, $datos);
        } else {
            $datos['usuario'] = $usuario;
            $datos['password'] = $password;
            $datos['id_rol'] = $idRol;
            $resultado = $this->empleadoModel->registrar($datos);
        }
        if ($resultado['success']) {
            $_SESSION['success'] = $resultado['mensaje'];
            header('Location: empleados.php');
        } else {
            $_SESSION['error'] = $resultado['mensaje'];
            if ($idEmpleado) {
                header("Location: empleado_form.php?id=$idEmpleado");
            } else {
                header('Location: empleado_form.php');
            }
        }
        exit;
    }
    /**
     * Cambiar estado del empleado (AJAX)
     */
    public function cambiarEstado() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
            exit;
        }
        $idEmpleado = isset($_POST['id_empleado']) ? (int)$_POST['id_empleado'] : 0;
        $estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 0;
        if (!$idEmpleado) {
            echo json_encode(['success' => false, 'mensaje' => 'ID de empleado inválido']);
            exit;
        }
        $resultado = $this->empleadoModel->cambiarEstado($idEmpleado, $estado);
        echo json_encode($resultado);
        exit;
    }






















    


    public function gestionarRoles($idEmpleado) {
    if (!$idEmpleado || !is_numeric($idEmpleado)) {
        return [
            'empleado' => null,
            'roles_asignados' => [],
            'roles_disponibles' => []
        ];
    }
    
    $empleado = $this->empleadoModel->obtenerPorId($idEmpleado);
    
    if (!$empleado) {
        return [
            'empleado' => null,
            'roles_asignados' => [],
            'roles_disponibles' => []
        ];
    }
    
    $rolesAsignados = $this->empleadoModel->obtenerRolesEmpleado($idEmpleado);
    $todosRoles = $this->empleadoModel->obtenerRoles(1);
    
    // Filtrar roles que no están asignados
    $idsRolesAsignados = array_column($rolesAsignados, 'IdRol');
    $rolesDisponibles = array_filter($todosRoles, function($rol) use ($idsRolesAsignados) {
        return !in_array($rol['IdRol'], $idsRolesAsignados);
    });
    
    return [
        'empleado' => $empleado,
        'roles_asignados' => $rolesAsignados,
        'roles_disponibles' => $rolesDisponibles
    ];
}














    /**
 * Asignar rol a empleado (AJAX)
 */
public function asignarRol() {
    header('Content-Type: application/json');
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
        exit;
    }
    
    $idEmpleado = isset($_POST['id_empleado']) ? (int)$_POST['id_empleado'] : 0;
    $idRol = isset($_POST['id_rol']) ? (int)$_POST['id_rol'] : 0;
    
    if (!$idEmpleado || !$idRol) {
        echo json_encode(['success' => false, 'mensaje' => 'Datos inválidos']);
        exit;
    }
    
    try {
        $resultado = $this->empleadoModel->asignarRol($idEmpleado, $idRol);
        echo json_encode($resultado);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'mensaje' => 'Error al asignar el rol: ' . $e->getMessage()
        ]);
    }
    exit;
}
/**
 * Remover rol de empleado (AJAX)
 */
public function removerRol() {
    header('Content-Type: application/json');
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
        exit;
    }
    
    $idEmpleado = isset($_POST['id_empleado']) ? (int)$_POST['id_empleado'] : 0;
    $idRol = isset($_POST['id_rol']) ? (int)$_POST['id_rol'] : 0;
    
    if (!$idEmpleado || !$idRol) {
        echo json_encode(['success' => false, 'mensaje' => 'Datos inválidos']);
        exit;
    }
    
    try {
        $resultado = $this->empleadoModel->removerRol($idEmpleado, $idRol);
        echo json_encode($resultado);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'mensaje' => 'Error al remover el rol: ' . $e->getMessage()
        ]);
    }
    exit;
}
}























// ========================================
// MANEJO DE ACCIONES DIRECTAS (AJAX)
// ========================================
if (isset($_GET['action']) && basename($_SERVER['PHP_SELF']) === 'EmpleadoController.php') {
    $controller = new EmpleadoController();
    $action = $_GET['action'];
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'mensaje' => 'Acción no encontrada']);
        exit;
    }
}