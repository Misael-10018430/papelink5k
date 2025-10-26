<?php
/**
 * Modelo Cliente
 * Gestión de clientes del sistema
 */
require_once __DIR__ . '/../config/Database.php';
class Cliente {
    private $conn;
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    /**
     * Obtener listado de clientes (Admin)
     */
    public function obtenerClientesAdmin($filtros = []) {
        try {
            $idTipoCliente = isset($filtros['tipo']) ? $filtros['tipo'] : null;
            $idSegmento = isset($filtros['segmento']) ? $filtros['segmento'] : null;
            $estado = isset($filtros['estado']) ? $filtros['estado'] : null;
            $busqueda = isset($filtros['busqueda']) ? $filtros['busqueda'] : null;
            $pagina = isset($filtros['pagina']) ? $filtros['pagina'] : 1;
            $registrosPorPagina = isset($filtros['registros']) ? $filtros['registros'] : 20;
            $sql = "EXEC sp_ObtenerClientesAdmin 
                    @IdTipoCliente = ?, 
                    @IdSegmento = ?, 
                    @Estado = ?, 
                    @BusquedaNombre = ?,
                    @Pagina = ?,
                    @RegistrosPorPagina = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $idTipoCliente, 
                $idSegmento, 
                $estado, 
                $busqueda,
                $pagina,
                $registrosPorPagina
            ]);
            // Primer resultset: datos de clientes
            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Segundo resultset: total de registros
            $stmt->nextRowset();
            $totalRegistros = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'clientes' => $clientes,
                'total' => $totalRegistros['TotalRegistros'] ?? 0,
                'pagina_actual' => $pagina,
                'registros_por_pagina' => $registrosPorPagina
            ];
        } catch (PDOException $e) {
            error_log("Error en obtenerClientesAdmin: " . $e->getMessage());
            return [
                'clientes' => [],
                'total' => 0,
                'pagina_actual' => 1,
                'registros_por_pagina' => $registrosPorPagina
            ];
        }
    }
    /**
     * Obtener perfil completo de un cliente
     */
    public function obtenerPerfilCliente($idCliente) {
        try {
            $sql = "EXEC sp_ObtenerPerfilCliente @IdCliente = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idCliente]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerPerfilCliente: " . $e->getMessage());
            return null;
        }
    }
    /**
     * Obtener estadísticas del cliente
     */
    public function obtenerEstadisticas($idCliente) {
        try {
            $sql = "EXEC sp_ObtenerEstadisticasCliente @IdCliente = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idCliente]);
            // Primer resultset: estadísticas generales
            $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);
            // Segundo resultset: productos más comprados
            $stmt->nextRowset();
            $productosTop = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return [
                'estadisticas' => $estadisticas,
                'productos_top' => $productosTop
            ];
        } catch (PDOException $e) {
            error_log("Error en obtenerEstadisticas: " . $e->getMessage());
            return [
                'estadisticas' => [
                    'TotalPedidos' => 0,
                    'TotalGastado' => 0,
                    'PedidosPendientes' => 0,
                    'PedidosCompletados' => 0
                ],
                'productos_top' => []
            ];
        }
    }
    /**
     * Cambiar estado del cliente (Activo/Inactivo)
     */
    public function cambiarEstado($idCliente, $estado) {
        try {
            $sql = "EXEC sp_CambiarEstadoCliente @IdCliente = ?, @Estado = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idCliente, $estado]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'success' => true,
                'mensaje' => $resultado['Mensaje'] ?? 'Estado actualizado correctamente'
            ];
        } catch (PDOException $e) {
            error_log("Error en cambiarEstado: " . $e->getMessage());
            return [
                'success' => false,
                'mensaje' => 'Error al cambiar el estado del cliente'
            ];
        }
    }
    /**
     * Obtener tipos de cliente disponibles
     */
    public function obtenerTiposCliente() {
        try {
            $sql = "EXEC sp_ObtenerTiposCliente";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerTiposCliente: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Obtener segmentos de cliente disponibles
     */
    public function obtenerSegmentosCliente() {
        try {
            $sql = "SELECT IdSegmento, NombreSegmento 
                    FROM Segmentos_Cliente 
                    WHERE Estado = 1 
                    ORDER BY NombreSegmento";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(); 
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerSegmentosCliente: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Cambiar tipo de cliente
     */
    public function cambiarTipoCliente($idCliente, $idTipoCliente) {
        try {
            $sql = "UPDATE Clientes SET IdTipoCliente = ? WHERE IdCliente = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idTipoCliente, $idCliente]);
            return [
                'success' => true,
                'mensaje' => 'Tipo de cliente actualizado correctamente'
            ];

        } catch (PDOException $e) {
            error_log("Error en cambiarTipoCliente: " . $e->getMessage());
            return [
                'success' => false,
                'mensaje' => 'Error al cambiar el tipo de cliente'
            ];
        }
    }
    /**
     * Cambiar segmento del cliente
     */
    public function cambiarSegmentoCliente($idCliente, $idSegmento) {
        try {
            $sql = "UPDATE Clientes SET IdSegmentoCliente = ? WHERE IdCliente = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idSegmento, $idCliente]);
            return [
                'success' => true,
                'mensaje' => 'Segmento actualizado correctamente'
            ];
        } catch (PDOException $e) {
            error_log("Error en cambiarSegmentoCliente: " . $e->getMessage());
            return [
                'success' => false,
                'mensaje' => 'Error al cambiar el segmento del cliente'
            ];
        }
    }
    /**
     * Obtener historial de pedidos del cliente
     */
    public function obtenerHistorialPedidos($idCliente, $limite = 10) {
        try {
            $sql = "SELECT TOP (?) 
                        p.IdPedido,
                        p.FechaPedido,
                        p.Total,
                        ep.NombreEstado,
                        ep.Color AS ColorEstado
                    FROM Pedidos p
                    INNER JOIN EstadosPedido ep ON p.IdEstadoPedido = ep.IdEstadoPedido
                    WHERE p.IdCliente = ?
                    ORDER BY p.FechaPedido DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$limite, $idCliente]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerHistorialPedidos: " . $e->getMessage());
            return [];
        }
    }
}