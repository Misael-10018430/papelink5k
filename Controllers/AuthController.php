<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Auth.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    private $usuarioModel;    
    public function __construct() {
        $this->usuarioModel = new Usuario();
    }   
    /**
     * Login Unificado (detecta automáticamente si es cliente o admin)
     */
    public function loginUnificado() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('view/cliente/login.php');
            exit();
        }        
        $email = trim($_POST['email']);
        $password = $_POST['password'];        
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Todos los campos son obligatorios';
            redirect('view/cliente/login.php');
            exit();
        }       
        // Primero intentar login como CLIENTE
        $resultadoCliente = $this->usuarioModel->loginCliente($email, $password);        
        if (isset($resultadoCliente['success'])) {
            // ES UN CLIENTE
            $_SESSION['cliente_id'] = $resultadoCliente['usuario']['id'];
            $_SESSION['nombre_cliente'] = $resultadoCliente['usuario']['nombre'];
            $_SESSION['email_cliente'] = $resultadoCliente['usuario']['email'];
            $_SESSION['telefono_cliente'] = $resultadoCliente['usuario']['telefono'];
            $_SESSION['tipo_usuario'] = 'cliente';
            $_SESSION['logueado'] = true;
            $_SESSION['exito'] = '¡Bienvenido de nuevo, ' . $resultadoCliente['usuario']['nombre'] . '!';
            
            redirect('view/cliente/index.php');
            exit();
        }        
        // Si no es cliente, intentar como EMPLEADO
        $resultadoEmpleado = $this->usuarioModel->loginEmpleado($email, $password);
        if (isset($resultadoEmpleado['success'])) {
            // ES UN EMPLEADO
            $_SESSION['usuario_id'] = $resultadoEmpleado['usuario']['id'];
            $_SESSION['nombre_usuario'] = $resultadoEmpleado['usuario']['nombre'];
            $_SESSION['email_usuario'] = $resultadoEmpleado['usuario']['email'];
            $_SESSION['tipo_usuario'] = 'empleado';
            $_SESSION['logueado'] = true;
            // CARGAR ROLES
            $roles = $resultadoEmpleado['usuario']['rol'] ?? '';
            $_SESSION['roles'] = $roles;
            $_SESSION['rol_usuario'] = $roles;           
            // VERIFICAR SI ES ADMINISTRADOR
            if (stripos($roles, 'Administrador') !== false) {
                $_SESSION['funcionalidades'] = ['*'];
                $_SESSION['nivel_acceso'] = 100;
            } else {
                $database = new Database();
                $conn = $database->getConnection();
                $funcionalidades = Auth::cargarFuncionalidades($resultadoEmpleado['usuario']['id'], $conn);
                $_SESSION['funcionalidades'] = $funcionalidades;
                $_SESSION['nivel_acceso'] = 50;
            }            
            $_SESSION['exito'] = '¡Bienvenido, ' . $resultadoEmpleado['usuario']['nombre'] . '!';
            redirect('view/admin/dashboard.php');
            exit();
    }
    }    
    /**
     * Login Admin (directo)
     */
   public function loginAdmin() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('view/admin/login.php');
        exit();
    }   
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';    
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Todos los campos son obligatorios';
        redirect('view/admin/login.php');
        exit();
    }   
    $resultado = $this->usuarioModel->loginEmpleado($email, $password);   
    if (isset($resultado['success'])) {
        // Login exitoso
        $_SESSION['usuario_id'] = $resultado['usuario']['id'];
        $_SESSION['nombre_usuario'] = $resultado['usuario']['nombre'];
        $_SESSION['email_usuario'] = $resultado['usuario']['email'];
        $_SESSION['tipo_usuario'] = 'empleado';
        $_SESSION['logueado'] = true;       
        //  CARGAR ROLES
        $roles = $resultado['usuario']['rol'] ?? '';
        $_SESSION['roles'] = $roles;
        $_SESSION['rol_usuario'] = $roles; // Compatibilidad       
        //  VERIFICAR SI ES ADMINISTRADOR
        if (stripos($roles, 'Administrador') !== false) {
            // Administrador: acceso a TODO
            $_SESSION['funcionalidades'] = ['*'];
            $_SESSION['nivel_acceso'] = 100;
        } else {
            // Otros roles: cargar funcionalidades específicas
            $database = new Database();
            $conn = $database->getConnection();
            $funcionalidades = Auth::cargarFuncionalidades($resultado['usuario']['id'], $conn);
            $_SESSION['funcionalidades'] = $funcionalidades;
            $_SESSION['nivel_acceso'] = 50;
        }       
        $_SESSION['exito'] = '¡Bienvenido, ' . $resultado['usuario']['nombre'] . '!';
        redirect('view/admin/dashboard.php');
        exit();
    } else {
        $_SESSION['error'] = $resultado['error'] ?? 'Error desconocido';
        $_SESSION['email_anterior'] = $email;
        redirect('view/admin/login.php');
        exit();
    }
}    
    /**
     * Login Cliente (directo)
     */
    public function loginCliente() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('view/cliente/login.php');
            exit();
        }       
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';       
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Todos los campos son obligatorios';
            redirect('view/cliente/login.php');
            exit();
        }       
        $resultado = $this->usuarioModel->loginCliente($email, $password);        
        if (isset($resultado['success'])) {
            // Login exitoso
            $_SESSION['cliente_id'] = $resultado['usuario']['id'];
            $_SESSION['nombre_cliente'] = $resultado['usuario']['nombre'];
            $_SESSION['email_cliente'] = $resultado['usuario']['email'];
            $_SESSION['telefono_cliente'] = $resultado['usuario']['telefono'];
            $_SESSION['tipo_usuario'] = 'cliente';
            $_SESSION['logueado'] = true;
            $_SESSION['exito'] = '¡Bienvenido de nuevo, ' . $resultado['usuario']['nombre'] . '!';
            
            redirect('view/cliente/index.php');
            exit();
        } else {
            $_SESSION['error'] = $resultado['error'] ?? 'Error desconocido';
            $_SESSION['email_anterior'] = $email;
            redirect('view/cliente/login.php');
            exit();
        }
    }   
    /**
     * Registro Cliente - CON LOGIN AUTOMÁTICO 
     */
    public function registrarCliente() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('view/cliente/registro.php');
            exit();
        }       
        // Validar datos
        $errores = [];        
        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmar_password = $_POST['confirmar_password'] ?? '';       
        if (empty($nombre)) {
            $errores[] = 'El nombre es obligatorio';
        }       
        if (empty($email)) {
            $errores[] = 'El email es obligatorio';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no es válido';
        }        
        if (empty($telefono)) {
            $errores[] = 'El teléfono es obligatorio';
        }        
        if (empty($password)) {
            $errores[] = 'La contraseña es obligatoria';
        } elseif (strlen($password) < 6) {
            $errores[] = 'La contraseña debe tener al menos 6 caracteres';
        }        
        if ($password !== $confirmar_password) {
            $errores[] = 'Las contraseñas no coinciden';
        }        
        // Verificar si email ya existe
        if (empty($errores) && $this->usuarioModel->existeEmail($email, 'cliente')) {
            $errores[] = 'El email ya está registrado';
        }       
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_form'] = $_POST;
            redirect('view/cliente/registro.php');
            exit();
        }        
        // Registrar cliente
        $datos = [
            'nombre' => $nombre,
            'email' => $email,
            'telefono' => $telefono,
            'direccion' => $direccion,
            'password' => $password
        ];      
        $resultado = $this->usuarioModel->registrarCliente($datos);        
        if (isset($resultado['success'])) {
            // REGISTRO EXITOSO - Redirigir al login con mensaje
            $_SESSION['registro_exitoso'] = true;
            $_SESSION['nombre_registrado'] = $nombre;
            $_SESSION['email_registrado'] = $email;
            redirect('view/cliente/login.php');
        } else {
            // Error al crear la cuenta
            $_SESSION['error'] = $resultado['error'] ?? 'Error desconocido';
            $_SESSION['datos_form'] = $_POST;
            redirect('view/cliente/registro.php');
        }
        exit();
    }    
    /**
     * Logout
     */
    public function logout() {
        $tipoUsuario = $_SESSION['tipo_usuario'] ?? 'cliente';
        $nombreUsuario = $_SESSION['nombre_cliente'] ?? $_SESSION['nombre_usuario'] ?? 'Usuario';        
        // Guardar mensaje antes de destruir sesión
        $mensajeLogout = '¡Hasta pronto, ' . $nombreUsuario . '! Sesión cerrada correctamente';        
        // Destruir todas las variables de sesión
        $_SESSION = array();        
        // Destruir la sesión
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-42000, '/');
        }
        session_destroy();        
        // Iniciar nueva sesión limpia para el mensaje
        session_start();
        $_SESSION['exito'] = $mensajeLogout;
        
        // Redirigir según tipo de usuario
        if ($tipoUsuario === 'empleado') {
            redirect('view/admin/login.php');
        } else {
            redirect('view/cliente/index.php');
        }
        exit();
    }    
    /**
     * Verificar si usuario está logueado
     */
    public static function verificarLogin($tipoRequerido = null) {
        if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
            if ($tipoRequerido === 'empleado') {
                $_SESSION['error'] = 'Debes iniciar sesión como empleado para acceder';
                redirect('view/admin/login.php');
            } else {
                $_SESSION['error'] = 'Debes iniciar sesión para continuar';
                redirect('view/cliente/login.php');
            }
            exit();
        }        
        // Verificar que el tipo de usuario coincida
        if ($tipoRequerido && $_SESSION['tipo_usuario'] !== $tipoRequerido) {
            session_destroy();
            $_SESSION['error'] = 'Acceso no autorizado para este tipo de cuenta';
            redirect('view/cliente/login.php');
            exit();
        }
    }   
    /**
     * Verificar si es admin (para proteger rutas admin)
     */
    public static function verificarAdmin() {
        if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
            $_SESSION['error'] = 'Debes iniciar sesión como administrador';
            redirect('view/admin/login.php');
            exit();
        }       
        if ($_SESSION['tipo_usuario'] !== 'empleado') {
            $_SESSION['error'] = 'Acceso no autorizado. Solo para empleados';
            redirect('view/admin/dashboard.php');
            exit();
        }
    }    
    /**
     * Verificar si es cliente (para proteger rutas cliente)
     */
    public static function verificarCliente() {
        if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
            $_SESSION['error'] = 'Debes iniciar sesión para continuar';
            redirect('view/cliente/login.php');
            exit();
        }       
        if ($_SESSION['tipo_usuario'] !== 'cliente') {
            $_SESSION['error'] = 'Acceso restringido para clientes';
            redirect('view/admin/dashboard.php');
            exit();
        }
    }
}

// =====================================================
// ENRUTADOR: Procesar las acciones de autenticación
// =====================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $controller = new AuthController();
    $accion = $_POST['accion'];    
    switch ($accion) {
        case 'login_unificado':
            $controller->loginUnificado();
            break;        
        case 'login_admin':
            $controller->loginAdmin();
            break;        
        case 'login_cliente':
            $controller->loginCliente();
            break;        
        case 'registro_cliente':
            $controller->registrarCliente();
            break;        
        default:
            redirect('view/cliente/login.php');
            break;
    }
} elseif (isset($_GET['action'])) {
    $controller = new AuthController();
    $action = $_GET['action'];   
    switch ($action) {
        case 'logout':
            $controller->logout();
            break;        
        default:
            redirect('view/cliente/login.php');
            break;
    }
} else {
    // Si no hay acción, redirigir al login
    redirect('view/cliente/login.php');
}
?>