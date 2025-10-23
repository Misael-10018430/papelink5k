<?php
/**
 * Modelo DevolucionAdmin
 * Gestión administrativa de devoluciones
 */

require_once __DIR__ . '/../config/Database.php';

class DevolucionAdmin {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Obtener todas las devoluciones (Admin)
     */
    public function obtenerDevolucionesAdmin($estado = null, $fechaInicio = null, $fechaFin = null, $pagina = 1, $registrosPorPagina = 20) {
        try {
            $sql = "EXEC sp_ObtenerDevolucionesAdmin 
                    @EstadoDevolucion = ?, 
                    @FechaInicio = ?, 
                    @FechaFin = ?,
                    @Pagina = ?,
                    @RegistrosPorPagina = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$estado, $fechaInicio, $fechaFin, $pagina, $registrosPorPagina]);
            
            // Resultado 1: Lista de devoluciones
            $devoluciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Resultado 2: Total de registros
            $stmt->nextRowset();
            $total = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'devoluciones' => $devoluciones,
                'total' => $total['TotalRegistros'] ?? 0
            ];

        } catch (PDOException $e) {
            error_log("Error en obtenerDevolucionesAdmin: " . $e->getMessage());
            return [
                'devoluciones' => [],
                'total' => 0
            ];
        }
    }

    /**
     * Obtener detalle de una devolución específica
     */
    public function obtenerDetalleDevolucion($idDevolucion) {
        try {
            $sql = "EXEC sp_ObtenerDetalleDevolucion @IdDevolucion = ?, @IdCliente = NULL";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idDevolucion]);
            
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
     * Cambiar estado de una devolución
     * Estados: SOLICITADA → APROBADA → COMPLETADA | RECHAZADA
     */
    public function cambiarEstadoDevolucion($idDevolucion, $nuevoEstado) {
        try {
            $sql = "EXEC sp_CambiarEstadoDevolucion 
                    @IdDevolucion = ?, 
                    @NuevoEstado = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idDevolucion, $nuevoEstado]);
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'mensaje' => $resultado['Mensaje'] ?? 'Estado actualizado correctamente'
            ];

        } catch (PDOException $e) {
            error_log("Error en cambiarEstadoDevolucion: " . $e->getMessage());
            
            return [
                'success' => false,
                'mensaje' => $e->getMessage()
            ];
        }
    }

    /**
     * Reintegrar productos al inventario disponible
     * (Mover de "En Revisión" a "Disponible")
     */
    public function reintegrarProductos($idDevolucion) {
        try {
            $sql = "EXEC sp_ReintegrarProductosDevolucion @IdDevolucion = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idDevolucion]);
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'mensaje' => $resultado['Mensaje'] ?? 'Productos reintegrados correctamente'
            ];

        } catch (PDOException $e) {
            error_log("Error en reintegrarProductos: " . $e->getMessage());
            
            return [
                'success' => false,
                'mensaje' => $e->getMessage()
            ];
        }
    }

    /**
     * Aprobar devolución
     * (Cambia estado a APROBADA y mueve productos a "En Revisión")
     */
    public function aprobarDevolucion($idDevolucion) {
        try {
            // Cambiar estado a APROBADA
            // El procedimiento sp_CambiarEstadoDevolucion NO reintegra en APROBADA
            // Solo cuando se marca como COMPLETADA
            $resultado = $this->cambiarEstadoDevolucion($idDevolucion, 'APROBADA');
            
            return $resultado;

        } catch (Exception $e) {
            error_log("Error en aprobarDevolucion: " . $e->getMessage());
            
            return [
                'success' => false,
                'mensaje' => $e->getMessage()
            ];
        }
    }

    /**
     * Completar devolución
     * (Cambia estado a COMPLETADA y reintegra productos automáticamente)
     */
    public function completarDevolucion($idDevolucion) {
        try {
            // Al cambiar a COMPLETADA, el procedimiento reintegra automáticamente
            // los productos a "En Revisión"
            $resultado = $this->cambiarEstadoDevolucion($idDevolucion, 'COMPLETADA');
            
            return $resultado;

        } catch (Exception $e) {
            error_log("Error en completarDevolucion: " . $e->getMessage());
            
            return [
                'success' => false,
                'mensaje' => $e->getMessage()
            ];
        }
    }

    /**
     * Rechazar devolución
     */
    public function rechazarDevolucion($idDevolucion) {
        try {
            $resultado = $this->cambiarEstadoDevolucion($idDevolucion, 'RECHAZADA');
            
            return $resultado;

        } catch (Exception $e) {
            error_log("Error en rechazarDevolucion: " . $e->getMessage());
            
            return [
                'success' => false,
                'mensaje' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener información del cliente de una devolución
     */
    public function obtenerInfoCliente($idDevolucion) {
        try {
            $sql = "SELECT 
                        c.IdCliente,
                        c.NombreCliente,
                        c.Email,
                        c.Telefono,
                        c.Direccion
                    FROM Devoluciones d
                    INNER JOIN Clientes c ON d.IdCliente = c.IdCliente
                    WHERE d.IdDevolucion = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idDevolucion]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en obtenerInfoCliente: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener estadísticas de devoluciones
     */
    public function obtenerEstadisticas() {
        try {
            $sql = "SELECT 
                        COUNT(*) AS TotalDevoluciones,
                        SUM(CASE WHEN EstadoDevolucion = 'SOLICITADA' THEN 1 ELSE 0 END) AS Solicitadas,
                        SUM(CASE WHEN EstadoDevolucion = 'APROBADA' THEN 1 ELSE 0 END) AS Aprobadas,
                        SUM(CASE WHEN EstadoDevolucion = 'COMPLETADA' THEN 1 ELSE 0 END) AS Completadas,
                        SUM(CASE WHEN EstadoDevolucion = 'RECHAZADA' THEN 1 ELSE 0 END) AS Rechazadas
                    FROM Devoluciones
                    WHERE FechaSolicitud >= DATEADD(MONTH, -1, GETDATE())";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en obtenerEstadisticas: " . $e->getMessage());
            return [
                'TotalDevoluciones' => 0,
                'Solicitadas' => 0,
                'Aprobadas' => 0,
                'Completadas' => 0,
                'Rechazadas' => 0
            ];
        }
    }

    /**
     * Buscar devoluciones por cliente o pedido
     */
    public function buscarDevoluciones($termino) {
        try {
            $sql = "SELECT 
                        d.IdDevolucion,
                        d.IdPedido,
                        p.NumeroPedido,
                        d.IdCliente,
                        c.NombreCliente,
                        c.Email,
                        d.FechaSolicitud,
                        d.EstadoDevolucion,
                        p.Total AS MontoTotal
                    FROM Devoluciones d
                    INNER JOIN Pedidos p ON d.IdPedido = p.IdPedido
                    INNER JOIN Clientes c ON d.IdCliente = c.IdCliente
                    WHERE c.NombreCliente LIKE ? 
                        OR c.Email LIKE ?
                        OR p.NumeroPedido LIKE ?
                        OR CAST(d.IdDevolucion AS NVARCHAR) LIKE ?
                    ORDER BY d.FechaSolicitud DESC";
            
            $terminoBusqueda = '%' . $termino . '%';
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$terminoBusqueda, $terminoBusqueda, $terminoBusqueda, $terminoBusqueda]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en buscarDevoluciones: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener estados disponibles
     */
    public function obtenerEstados() {
        return [
            'SOLICITADA' => 'Solicitada',
            'APROBADA' => 'Aprobada',
            'COMPLETADA' => 'Completada',
            'RECHAZADA' => 'Rechazada'
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
     * Formatear fecha y hora
     */
    public function formatoFechaHora($fecha) {
        return date('d/m/Y H:i', strtotime($fecha));
    }

    /**
     * Obtener badge de estado
     */
    public function getBadgeEstado($estado) {
        $badges = [
            'SOLICITADA' => 'badge-amarillo',
            'APROBADA' => 'badge-azul',
            'COMPLETADA' => 'badge-verde',
            'RECHAZADA' => 'badge-rojo'
        ];
        
        return $badges[$estado] ?? 'badge-azul';
    }

    /**
     * Validar transición de estado
     */
    public function validarTransicion($estadoActual, $estadoNuevo) {
        $transicionesValidas = [
            'SOLICITADA' => ['APROBADA', 'RECHAZADA'],
            'APROBADA' => ['COMPLETADA', 'RECHAZADA'],
            'COMPLETADA' => [],
            'RECHAZADA' => []
        ];
        
        if (!isset($transicionesValidas[$estadoActual])) {
            return false;
        }
        
        return in_array($estadoNuevo, $transicionesValidas[$estadoActual]);
    }
}