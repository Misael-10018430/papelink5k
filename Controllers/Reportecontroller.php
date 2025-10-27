<?php
/**
 * Controlador de Reportes
 * Maneja todas las acciones relacionadas con reportes
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Reporte.php';

class ReporteController {
    private $reporteModel;
    
    public function __construct() {
        // La sesión ahora se gestiona desde config/config.php
        $this->reporteModel = new Reporte();
    }
    
    /**
     * Generar Reporte de Ventas por Período (AJAX)
     */
    public function generarVentasPorPeriodo() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
            exit;
        }
        
        $fechaInicio = $_POST['fecha_inicio'] ?? '';
        $fechaFin = $_POST['fecha_fin'] ?? '';
        
        if (empty($fechaInicio) || empty($fechaFin)) {
            echo json_encode(['success' => false, 'mensaje' => 'Las fechas son obligatorias']);
            exit;
        }
        
        $resultado = $this->reporteModel->ventasPorPeriodo($fechaInicio, $fechaFin);
        
        echo json_encode([
            'success' => true,
            'datos' => $resultado
        ]);
        exit;
    }
    
    /**
     * Generar Reporte de Ventas por Método de Pago (AJAX)
     */
    public function generarVentasPorMetodoPago() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
            exit;
        }
        
        $fechaInicio = $_POST['fecha_inicio'] ?? '';
        $fechaFin = $_POST['fecha_fin'] ?? '';
        
        if (empty($fechaInicio) || empty($fechaFin)) {
            echo json_encode(['success' => false, 'mensaje' => 'Las fechas son obligatorias']);
            exit;
        }
        
        $resultado = $this->reporteModel->ventasPorMetodoPago($fechaInicio, $fechaFin);
        
        echo json_encode([
            'success' => true,
            'datos' => $resultado
        ]);
        exit;
    }
    
    /**
     * Generar Reporte de Ventas por Cliente (AJAX)
     */
    public function generarVentasPorCliente() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
            exit;
        }
        
        $fechaInicio = $_POST['fecha_inicio'] ?? '';
        $fechaFin = $_POST['fecha_fin'] ?? '';
        $top = isset($_POST['top']) ? (int)$_POST['top'] : 20;
        
        if (empty($fechaInicio) || empty($fechaFin)) {
            echo json_encode(['success' => false, 'mensaje' => 'Las fechas son obligatorias']);
            exit;
        }
        
        $resultado = $this->reporteModel->ventasPorCliente($fechaInicio, $fechaFin, $top);
        
        echo json_encode([
            'success' => true,
            'datos' => $resultado
        ]);
        exit;
    }
    
    /**
     * Generar Reporte Financiero (AJAX)
     */
    public function generarReporteFinanciero() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
            exit;
        }
        
        $fechaInicio = $_POST['fecha_inicio'] ?? '';
        $fechaFin = $_POST['fecha_fin'] ?? '';
        
        if (empty($fechaInicio) || empty($fechaFin)) {
            echo json_encode(['success' => false, 'mensaje' => 'Las fechas son obligatorias']);
            exit;
        }
        
        $resultado = $this->reporteModel->reporteFinanciero($fechaInicio, $fechaFin);
        
        echo json_encode([
            'success' => true,
            'datos' => $resultado
        ]);
        exit;
    }
    
    /**
     * Generar Reporte de Productos Más Vendidos (AJAX)
     */
    public function generarProductosMasVendidos() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
            exit;
        }
        
        $fechaInicio = $_POST['fecha_inicio'] ?? '';
        $fechaFin = $_POST['fecha_fin'] ?? '';
        $top = isset($_POST['top']) ? (int)$_POST['top'] : 20;
        
        if (empty($fechaInicio) || empty($fechaFin)) {
            echo json_encode(['success' => false, 'mensaje' => 'Las fechas son obligatorias']);
            exit;
        }
        
        $resultado = $this->reporteModel->productosMasVendidos($fechaInicio, $fechaFin, $top);
        
        echo json_encode([
            'success' => true,
            'datos' => $resultado
        ]);
        exit;
    }
    
    /**
     * Generar Reporte de Inventario Actual (AJAX)
     */
    public function generarInventarioActual() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
            exit;
        }
        
        $resultado = $this->reporteModel->inventarioActual();
        
        echo json_encode([
            'success' => true,
            'datos' => $resultado
        ]);
        exit;
    }
}

// ========================================
// MANEJO DE ACCIONES DIRECTAS (AJAX)
// ========================================
if (isset($_GET['action']) && basename($_SERVER['PHP_SELF']) === 'ReporteController.php') {
    $controller = new ReporteController();
    $action = $_GET['action'];
    
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'mensaje' => 'Acción no encontrada']);
        exit;
    }
}
?>