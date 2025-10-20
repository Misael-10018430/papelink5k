<?php
/**
 * Configuración básica del sistema Papelink
 */

// =====================================================
// RUTAS DEL PROYECTO
// =====================================================
// IMPORTANTE: Cambiamos BASE_PATH por ROOT_PATH para evitar conflictos
// El header.php se encarga de definir BASE_PATH para las vistas
define('ROOT_PATH', __DIR__);
define('BASE_URL', 'http://localhost/Papelink5k/');

// =====================================================
// CONFIGURACIÓN DE BASE DE DATOS
// =====================================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'papelink5k');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// =====================================================
// ZONA HORARIA
// =====================================================
date_default_timezone_set('America/Mexico_City');

// =====================================================
// MANEJO DE ERRORES
// =====================================================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// =====================================================
// CONFIGURACIÓN DE SESIÓN
// =====================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =====================================================
// CLASE DE CONEXIÓN A BASE DE DATOS
// =====================================================
class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;
    private $conn = null;

    public function getConnection() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            die();
        }
        
        return $this->conn;
    }
}

// =====================================================
// FUNCIONES ÚTILES
// =====================================================

// Redireccionar
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

// Limpiar datos de entrada
function limpiar($dato) {
    return htmlspecialchars(strip_tags(trim($dato)));
}

// Formatear precio
function formatearPrecio($precio) {
    return '$' . number_format($precio, 2);
}

// Verificar si está logueado
function estaLogueado() {
    return isset($_SESSION['cliente_id']);
}

// Verificar si es empleado
function esEmpleado() {
    return isset($_SESSION['empleado_id']);
}

// =====================================================
// CONSTANTES ADICIONALES
// =====================================================

// IVA (16%)
define('IVA_PORCENTAJE', 0.16);

// Costos de envío
define('COSTO_ENVIO_DOMICILIO', 50.00);
define('COSTO_ENVIO_SUCURSAL', 0.00);

// Tamaño máximo de archivos (5 MB)
define('MAX_FILE_SIZE', 5 * 1024 * 1024);

// Extensiones permitidas para imágenes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

?>