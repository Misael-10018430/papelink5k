<?php
/**
 * Configuración básica del sistema Papelink - Optimizada para Azure
 */

// =====================================================
// DETECCIÓN DE ENTORNO (CAMBIO CLAVE)
// =====================================================
// En lugar de buscar variables de Vercel, detectamos si estamos en producción
// comprobando si las variables de la base de datos están configuradas (como en Azure).
 $isProduction = getenv('DB_HOST') !== false;
 $isLocalhost = (php_sapi_name() === 'cli' || isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));

// =====================================================
// RUTAS DEL PROYECTO
// =====================================================
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__);
}

// Base URL dinámica según el entorno
if (!defined('BASE_URL')) {
    if ($isProduction) {
        // En producción (Azure), usar la URL del dominio automáticamente
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'papelink5k-app.azurewebsites.net';
        define('BASE_URL', $protocol . $host . '/');
    } else {
        // En desarrollo local
        define('BASE_URL', 'https://papelink5k-app-g9hhh8fegxe8gvgy.westus3-01.azurewebsites.net/');
    }
}

// =====================================================
// ZONA HORARIA
// =====================================================
date_default_timezone_set('America/Mexico_City');

// =====================================================
// MANEJO DE ERRORES
// =====================================================
if ($isProduction) {
    // En producción: ocultar errores del usuario, pero logearlos
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
} else {
    // En desarrollo: mostrar todos los errores
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// =====================================================
// CONFIGURACIÓN DE SESIÓN
// =====================================================
if (session_status() === PHP_SESSION_NONE) {
    // Configuración segura de sesiones
    ini_set('session.cookie_httponly', 1);
    if ($isProduction) {
        ini_set('session.cookie_secure', 1);
    }
    session_start();
}

// =====================================================
// INCLUIR ARCHIVO DE BASE DE DATOS
// =====================================================
require_once __DIR__ . '/database.php';

// =====================================================
// FUNCIONES ÚTILES
// =====================================================

// Redireccionar con URL absoluta o relativa
if (!function_exists('redirect')) {
    function redirect($url) {
        // Si ya es una URL completa, usarla tal como está
        if (strpos($url, 'http') === 0) {
            header("Location: " . $url);
        } else {
            header("Location: " . BASE_URL . $url);
        }
        exit();
    }
}

// Limpiar datos de entrada
if (!function_exists('limpiar')) {
    function limpiar($dato) {
        return htmlspecialchars(strip_tags(trim($dato)), ENT_QUOTES, 'UTF-8');
    }
}

// Formatear precio
if (!function_exists('formatearPrecio')) {
    function formatearPrecio($precio) {
        return '$' . number_format($precio, 2);
    }
}

// Verificar si está logueado
if (!function_exists('estaLogueado')) {
    function estaLogueado() {
        return isset($_SESSION['cliente_id']) && !empty($_SESSION['cliente_id']);
    }
}

// Verificar si es empleado
if (!function_exists('esEmpleado')) {
    function esEmpleado() {
        return isset($_SESSION['empleado_id']) && !empty($_SESSION['empleado_id']);
    }
}

// Generar número de pedido único
if (!function_exists('generarNumeroPedido')) {
    function generarNumeroPedido() {
        return 'PED-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}

// Función para debug (solo en desarrollo)
if (!function_exists('debug')) {
    function debug($data, $die = false) {
        global $isProduction;
        if (!$isProduction) {
            echo '<pre>';
            print_r($data);
            echo '</pre>';
            if ($die) die();
        }
    }
}

// Función para obtener la URL base de assets
if (!function_exists('asset')) {
    function asset($path) {
        return BASE_URL . ltrim($path, '/');
    }
}

// =====================================================
// CONSTANTES ADICIONALES
// =====================================================

// IVA (16%)
if (!defined('IVA_PORCENTAJE')) {
    define('IVA_PORCENTAJE', 0.16);
}

// Costos de envío
if (!defined('COSTO_ENVIO_DOMICILIO')) {
    define('COSTO_ENVIO_DOMICILIO', 50.00);
}
if (!defined('COSTO_ENVIO_SUCURSAL')) {
    define('COSTO_ENVIO_SUCURSAL', 0.00);
}

// Tamaño máximo de archivos (5 MB)
if (!defined('MAX_FILE_SIZE')) {
    define('MAX_FILE_SIZE', 5 * 1024 * 1024);
}

// Extensiones permitidas para imágenes
if (!defined('ALLOWED_EXTENSIONS')) {
    define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
}

// Rutas de directorios importantes
if (!defined('UPLOAD_PATH')) {
    define('UPLOAD_PATH', ROOT_PATH . '/assets/img/productos/');
}

if (!defined('ASSETS_PATH')) {
    define('ASSETS_PATH', ROOT_PATH . '/assets/');
}

// =====================================================
// CONFIGURACIÓN ESPECÍFICA PARA AZURE (ANTES VERCEL)
// =====================================================
if ($isProduction) {
    // Configuraciones específicas para el entorno de producción (Azure)
    // Headers para mejorar el performance y seguridad
    if (!headers_sent()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

// =====================================================
// AUTOLOAD SIMPLE PARA CONTROLADORES Y MODELOS
// =====================================================
spl_autoload_register(function ($class_name) {
    $directories = [
        ROOT_PATH . '/controllers/',
        ROOT_PATH . '/models/',
        ROOT_PATH . '/config/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

?>
