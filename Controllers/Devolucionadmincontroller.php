<?php
/**
 * Controlador de Devoluciones (Admin)
 * Maneja todas las acciones administrativas de devoluciones
 */
require_once __DIR__ . '/../models/DevolucionAdmin.php';
class DevolucionAdminController {
    private $devolucionModel;
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Verificar que el empleado esté autenticado
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ../../view/admin/login.php');
            exit();
        }
        $this->devolucionModel = new DevolucionAdmin();
    }
    /**
     * Listar devoluciones (AJAX)
     */
    public function listarDevoluciones() {
        header('Content-Type: application/json');
        $estado = $_GET['estado'] ?? null;
        $fechaInicio = $_GET['fecha_inicio'] ?? null;
        $fechaFin = $_GET['fecha_fin'] ?? null;
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $registrosPorPagina = isset($_GET['registros']) ? (int)$_GET['registros'] : 20;
        // Si estado es "TODAS", pasar NULL
        if ($estado === 'TODAS') {
            $estado = null;
        }
        $resultado = $this->devolucionModel->obtenerDevolucionesAdmin(
            $estado,
            $fechaInicio,
            $fechaFin,
            $pagina,
            $registrosPorPagina
        );
        echo json_encode([
            'success' => true,
            'devoluciones' => $resultado['devoluciones'],
            'total' => $resultado['total']
        ]);
        exit;
    }
    /**
     * Obtener detalle de devolución (AJAX)
     */
    public function obtenerDetalle() {
        header('Content-Type: application/json');
        if (!isset($_GET['id'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID de devolución no proporcionado']);
            exit;
        }
        $idDevolucion = (int)$_GET['id'];
        $detalle = $this->devolucionModel->obtenerDetalleDevolucion($idDevolucion);
        if ($detalle['informacion']) {
            // Obtener info del cliente
            $infoCliente = $this->devolucionModel->obtenerInfoCliente($idDevolucion);
            echo json_encode([
                'success' => true,
                'detalle' => $detalle,
                'cliente' => $infoCliente
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Devolución no encontrada'
            ]);
        }
        exit;
    }
    /**
     * Aprobar devolución (AJAX)
     */
    public function aprobarDevolucion() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
            exit;
        }
        $idDevolucion = isset($_POST['id_devolucion']) ? (int)$_POST['id_devolucion'] : 0;

        if ($idDevolucion <= 0) {
            echo json_encode(['success' => false, 'mensaje' => 'ID de devolución no válido']);
            exit;
        }
        $resultado = $this->devolucionModel->aprobarDevolucion($idDevolucion);
        echo json_encode($resultado);
        exit;
    }
    /**
     * Completar devolución (AJAX)
     * Reintegra productos automáticamente a inventario "En Revisión"
     */
    public function completarDevolucion() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
            exit;
        }
        $idDevolucion = isset($_POST['id_devolucion']) ? (int)$_POST['id_devolucion'] : 0;

        if ($idDevolucion <= 0) {
            echo json_encode(['success' => false, 'mensaje' => 'ID de devolución no válido']);
            exit;
        }
        $resultado = $this->devolucionModel->completarDevolucion($idDevolucion);    
        echo json_encode($resultado);
        exit;
    }
    /**
     * Rechazar devolución (AJAX)
     */
    public function rechazarDevolucion() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
            exit;
        }
        $idDevolucion = isset($_POST['id_devolucion']) ? (int)$_POST['id_devolucion'] : 0;
        if ($idDevolucion <= 0) {
            echo json_encode(['success' => false, 'mensaje' => 'ID de devolución no válido']);
            exit;
        }
        $resultado = $this->devolucionModel->rechazarDevolucion($idDevolucion);
        echo json_encode($resultado);
        exit;
    }
    /**
     * Reintegrar productos al inventario disponible (AJAX)
     * (Mover de "En Revisión" a "Disponible")
     */
    public function reintegrarProductos() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
            exit;
        }
        $idDevolucion = isset($_POST['id_devolucion']) ? (int)$_POST['id_devolucion'] : 0;
        if ($idDevolucion <= 0) {
            echo json_encode(['success' => false, 'mensaje' => 'ID de devolución no válido']);
            exit;
        }
        $resultado = $this->devolucionModel->reintegrarProductos($idDevolucion);
        echo json_encode($resultado);
        exit;
    }
    /**
     * Obtener estadísticas (AJAX)
     */
    public function obtenerEstadisticas() {
        header('Content-Type: application/json');
        $estadisticas = $this->devolucionModel->obtenerEstadisticas();
        echo json_encode([
            'success' => true,
            'estadisticas' => $estadisticas
        ]);
        exit;
    }
    /**
     * Buscar devoluciones (AJAX)
     */
    public function buscarDevoluciones() {
        header('Content-Type: application/json');
        $termino = $_GET['termino'] ?? '';
        if (empty($termino)) {
            echo json_encode(['success' => false, 'mensaje' => 'Término de búsqueda vacío']);
            exit;
        }
        $resultados = $this->devolucionModel->buscarDevoluciones($termino);
        echo json_encode([
            'success' => true,
            'resultados' => $resultados
        ]);
        exit;
    }
    /**
     * Cambiar estado manualmente (AJAX)
     */
    public function cambiarEstado() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
            exit;
        }
        $idDevolucion = isset($_POST['id_devolucion']) ? (int)$_POST['id_devolucion'] : 0;
        $nuevoEstado = $_POST['nuevo_estado'] ?? '';
        if ($idDevolucion <= 0) {
            echo json_encode(['success' => false, 'mensaje' => 'ID de devolución no válido']);
            exit;
        }
        if (empty($nuevoEstado)) {
            echo json_encode(['success' => false, 'mensaje' => 'Estado no especificado']);
            exit;
        }
        $resultado = $this->devolucionModel->cambiarEstadoDevolucion($idDevolucion, $nuevoEstado);
        echo json_encode($resultado);
        exit;
    }
}
// ========================================
// MANEJO DE ACCIONES DIRECTAS (AJAX)
// ========================================
if (isset($_GET['action']) && basename($_SERVER['PHP_SELF']) === 'DevolucionAdminController.php') {
    $controller = new DevolucionAdminController();
    $action = $_GET['action'];
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'mensaje' => 'Acción no encontrada']);
        exit;
    }
}