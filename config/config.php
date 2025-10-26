<?php
/**
 * Configuración básica del sistema Papelink
 */
// =====================================================
// RUTAS DEL PROYECTO
// =====================================================
// IMPORTANTE: Usamos ROOT_PATH para evitar conflicto con header.php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__);
}
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/papelink5k/');
}
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
// INCLUIR ARCHIVO DE BASE DE DATOS
// =====================================================
require_once __DIR__ . '/database.php';
// =====================================================
// FUNCIONES ÚTILES
// =====================================================
// Redireccionar
if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: " . BASE_URL . $url);
        exit();
    }
}
// Limpiar datos de entrada
if (!function_exists('limpiar')) {
    function limpiar($dato) {
        return htmlspecialchars(strip_tags(trim($dato)));
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
        return isset($_SESSION['cliente_id']);
    }
}
// Verificar si es empleado
if (!function_exists('esEmpleado')) {
    function esEmpleado() {
        return isset($_SESSION['empleado_id']);
    }
}
// Generar número de pedido único
if (!function_exists('generarNumeroPedido')) {
    function generarNumeroPedido() {
        return 'PED-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
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
?>