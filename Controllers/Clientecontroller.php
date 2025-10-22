<?php
/**
 * Controlador de Clientes
 * Maneja todas las acciones relacionadas con la gestión de clientes
 */

require_once __DIR__ . '/../models/Cliente.php';

class ClienteController {
    private $clienteModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->clienteModel = new Cliente();
        
        // NO validar sesión aquí - la validación se hace en las vistas
        // El controlador solo proporciona los métodos para trabajar con clientes
    }

    /**
     * Listar clientes con filtros
     */
    public function listar() {
        $filtros = [
            'tipo' => isset($_GET['tipo']) && $_GET['tipo'] !== '' ? (int)$_GET['tipo'] : null,
            'segmento' => isset($_GET['segmento']) && $_GET['segmento'] !== '' ? (int)$_GET['segmento'] : null,
            'estado' => isset($_GET['estado']) && $_GET['estado'] !== '' ? (int)$_GET['estado'] : null,
            'busqueda' => isset($_GET['busqueda']) && $_GET['busqueda'] !== '' ? $_GET['busqueda'] : null,
            'pagina' => isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1,
            'registros' => 20
        ];

        $resultado = $this->clienteModel->obtenerClientesAdmin($filtros);
        $tipos = $this->clienteModel->obtenerTiposCliente();
        $segmentos = $this->clienteModel->obtenerSegmentosCliente();

        // Calcular paginación
        $totalPaginas = ceil($resultado['total'] / $resultado['registros_por_pagina']);

        return [
            'clientes' => $resultado['clientes'],
            'tipos' => $tipos,
            'segmentos' => $segmentos,
            'filtros_actuales' => $filtros,
            'paginacion' => [
                'total' => $resultado['total'],
                'pagina_actual' => $resultado['pagina_actual'],
                'total_paginas' => $totalPaginas,
                'registros_por_pagina' => $resultado['registros_por_pagina']
            ]
        ];
    }

    /**
     * Ver detalle de un cliente
     */
    public function verDetalle($idCliente) {
        if (!$idCliente || !is_numeric($idCliente)) {
            $_SESSION['error'] = 'ID de cliente inválido';
            header('Location: clientes.php');
            exit;
        }

        $perfil = $this->clienteModel->obtenerPerfilCliente($idCliente);
        
        if (!$perfil) {
            $_SESSION['error'] = 'Cliente no encontrado';
            header('Location: clientes.php');
            exit;
        }

        $estadisticas = $this->clienteModel->obtenerEstadisticas($idCliente);
        $historialPedidos = $this->clienteModel->obtenerHistorialPedidos($idCliente);
        $tipos = $this->clienteModel->obtenerTiposCliente();
        $segmentos = $this->clienteModel->obtenerSegmentosCliente();

        return [
            'perfil' => $perfil,
            'estadisticas' => $estadisticas['estadisticas'],
            'productos_top' => $estadisticas['productos_top'],
            'historial' => $historialPedidos,
            'tipos' => $tipos,
            'segmentos' => $segmentos
        ];
    }

    /**
     * Cambiar estado del cliente (AJAX)
     */
    public function cambiarEstado() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
            exit;
        }

        $idCliente = isset($_POST['id_cliente']) ? (int)$_POST['id_cliente'] : 0;
        $estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 0;

        if (!$idCliente) {
            echo json_encode(['success' => false, 'mensaje' => 'ID de cliente inválido']);
            exit;
        }

        $resultado = $this->clienteModel->cambiarEstado($idCliente, $estado);
        echo json_encode($resultado);
        exit;
    }

    /**
     * Cambiar tipo de cliente (AJAX)
     */
    public function cambiarTipo() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
            exit;
        }

        $idCliente = isset($_POST['id_cliente']) ? (int)$_POST['id_cliente'] : 0;
        $idTipoCliente = isset($_POST['id_tipo']) ? (int)$_POST['id_tipo'] : 0;

        if (!$idCliente || !$idTipoCliente) {
            echo json_encode(['success' => false, 'mensaje' => 'Datos inválidos']);
            exit;
        }

        $resultado = $this->clienteModel->cambiarTipoCliente($idCliente, $idTipoCliente);
        echo json_encode($resultado);
        exit;
    }

    /**
     * Cambiar segmento del cliente (AJAX)
     */
    public function cambiarSegmento() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
            exit;
        }

        $idCliente = isset($_POST['id_cliente']) ? (int)$_POST['id_cliente'] : 0;
        $idSegmento = isset($_POST['id_segmento']) ? (int)$_POST['id_segmento'] : 0;

        if (!$idCliente || !$idSegmento) {
            echo json_encode(['success' => false, 'mensaje' => 'Datos inválidos']);
            exit;
        }

        $resultado = $this->clienteModel->cambiarSegmentoCliente($idCliente, $idSegmento);
        echo json_encode($resultado);
        exit;
    }
}

// ========================================
// MANEJO DE ACCIONES DIRECTAS (AJAX)
// ========================================
// Si se llama directamente al controlador con un parámetro 'action'
if (isset($_GET['action']) && basename($_SERVER['PHP_SELF']) === 'ClienteController.php') {
    $controller = new ClienteController();
    $action = $_GET['action'];
    
    // Ejecutar el método solicitado
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'mensaje' => 'Acción no encontrada']);
        exit;
    }
}