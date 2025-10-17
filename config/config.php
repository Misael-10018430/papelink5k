<?php
/**
 * Configuración básica del sistema Papelink
 */

// =====================================================
// RUTAS DEL PROYECTO
// =====================================================
define('BASE_PATH', __DIR__);
define('BASE_URL', 'http://localhost/Papelink5k/');
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
?>