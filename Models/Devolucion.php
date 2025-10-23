<?php
/**
 * Modelo Devolucion
 * Gestión de devoluciones de pedidos del cliente
 */

require_once __DIR__ . '/../config/Database.php';

class Devolucion {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Obtener todas las devoluciones del cliente
     */
    public function obtenerDevolucionesCliente($idCliente) {
        try {
            $sql = "EXEC sp_ObtenerDevolucionesCliente @IdCliente = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idCliente]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en obtenerDevolucionesCliente: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener detalle de una devolución específica
     */
    public function obtenerDetalleDevolucion($idDevolucion, $idCliente) {
        try {
            $sql = "EXEC sp_ObtenerDetalleDevolucion @IdDevolucion = ?, @IdCliente = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idDevolucion, $idCliente]);
            
            // Resultado 1: Información de la devolución
            $informacion = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Resultado 2: Productos de la devolución
            $stmt->nextRowset();
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'informacion' => $informacion,
                'productos' => $productos
            ];

        } catch (PDOException $e) {
            error_log("Error en obtenerDetalleDevolucion: " . $e->getMessage());
            return [
                'informacion' => null,
                'productos' => []
            ];
        }
    }

    /**
     * Solicitar una nueva devolución con productos específicos
     */
    public function solicitarDevolucion($idPedido, $idCliente, $motivo, $productosJSON) {
        try {
            $sql = "EXEC sp_SolicitarDevolucion 
                    @IdPedido = ?, 
                    @IdCliente = ?, 
                    @Motivo = ?, 
                    @ProductosJSON = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idPedido, $idCliente, $motivo, $productosJSON]);
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'idDevolucion' => $resultado['IdDevolucion'],
                'mensaje' => $resultado['Mensaje'],
                'productosDevueltos' => $resultado['ProductosDevueltos']
            ];

        } catch (PDOException $e) {
            error_log("Error en solicitarDevolucion: " . $e->getMessage());
            
            // Capturar el mensaje de error específico del procedimiento
            $errorMessage = $e->getMessage();
            
            return [
                'success' => false,
                'mensaje' => $errorMessage
            ];
        }
    }

    /**
     * Obtener pedidos completados del cliente (elegibles para devolución)
     */
    public function obtenerPedidosDevolvibles($idCliente) {
        try {
            // Obtener pedidos completados
            $sql = "EXEC sp_ObtenerPedidosCliente @IdCliente = ?, @IdEstadoPedido = NULL";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idCliente]);
            
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Filtrar solo los completados que no tengan devolución
            $pedidosDevolvibles = [];
            foreach ($pedidos as $pedido) {
                if ($pedido['EstadoPedido'] === 'Completado') {
                    // Verificar que no tenga devolución
                    $sqlCheck = "SELECT COUNT(*) as Total FROM Devoluciones WHERE IdPedido = ?";
                    $stmtCheck = $this->conn->prepare($sqlCheck);
                    $stmtCheck->execute([$pedido['IdPedido']]);
                    $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
                    
                    if ($result['Total'] == 0) {
                        $pedidosDevolvibles[] = $pedido;
                    }
                }
            }
            
            return $pedidosDevolvibles;

        } catch (PDOException $e) {
            error_log("Error en obtenerPedidosDevolvibles: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener detalle de un pedido específico
     */
    public function obtenerDetallePedido($idPedido, $idCliente) {
        try {
            $sql = "EXEC sp_ObtenerDetallePedido @IdPedido = ?, @IdCliente = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idPedido, $idCliente]);
            
            // Resultado 1: Información general del pedido
            $informacion = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Resultado 2: Productos del pedido
            $stmt->nextRowset();
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Resultado 3: Información de envío (opcional)
            $stmt->nextRowset();
            $envio = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'informacion' => $informacion,
                'productos' => $productos,
                'envio' => $envio
            ];

        } catch (PDOException $e) {
            error_log("Error en obtenerDetallePedido: " . $e->getMessage());
            return [
                'informacion' => null,
                'productos' => [],
                'envio' => null
            ];
        }
    }

    /**
     * Obtener configuración de días permitidos para devolución
     */
    public function obtenerDiasDevolucion() {
        try {
            $sql = "SELECT Valor FROM Configuracion WHERE Clave = 'DIAS_DEVOLUCION'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? (int)$resultado['Valor'] : 30; // Por defecto 30 días

        } catch (PDOException $e) {
            error_log("Error en obtenerDiasDevolucion: " . $e->getMessage());
            return 30;
        }
    }

    /**
     * Obtener estados de devolución disponibles
     */
    public function obtenerEstadosDevolucion() {
        return [
            'SOLICITADA' => 'Solicitada',
            'APROBADA' => 'Aprobada',
            'RECHAZADA' => 'Rechazada',
            'PROCESADA' => 'Procesada'
        ];
    }

    /**
     * Formatear moneda
     */
    public function formatoMoneda($valor) {
        return '$' . number_format($valor, 2, '.', ',');
    }

    /**
     * Formatear fecha
     */
    public function formatoFecha($fecha) {
        return date('d/m/Y', strtotime($fecha));
    }

    /**
     * Obtener badge de estado
     */
    public function getBadgeEstado($estado) {
        $badges = [
            'SOLICITADA' => 'badge-amarillo',
            'APROBADA' => 'badge-verde',
            'RECHAZADA' => 'badge-rojo',
            'PROCESADA' => 'badge-azul'
        ];
        
        return $badges[$estado] ?? 'badge-azul';
    }
}