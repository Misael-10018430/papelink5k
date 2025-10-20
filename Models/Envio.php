<?php
require_once __DIR__ . '/../config/Database.php';

class Envio {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Obtener envíos pendientes
     */
    public function obtenerPendientes() {
        try {
            $query = "EXEC sp_ObtenerEnviosPendientes";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Obtener detalle de envío
     */
    public function obtenerDetalle($idEnvio) {
        try {
            $query = "SELECT 
                        e.*,
                        p.NumeroPedido,
                        p.NombreClienteSnapshot,
                        p.TelefonoSnapshot,
                        ee.NombreEstado as EstadoEnvio
                      FROM Envios e
                      INNER JOIN Pedidos p ON e.IdPedido = p.IdPedido
                      INNER JOIN EstadosEnvio ee ON e.IdEstadoEnvio = ee.IdEstadoEnvio
                      WHERE e.IdEnvio = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$idEnvio]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Actualizar envío
     */
    public function actualizar($idEnvio, $datos) {
        try {
            $query = "EXEC sp_ActualizarEnvio 
                      @IdEnvio = ?,
                      @IdEstadoEnvio = ?,
                      @FechaEnvio = ?,
                      @FechaEntregaEstimada = ?,
                      @Observaciones = ?";
            
            $stmt = $this->conn->prepare($query);
            
            if ($stmt->execute([
                $idEnvio,
                $datos['id_estado_envio'] ?? null,
                $datos['fecha_envio'] ?? null,
                $datos['fecha_entrega_estimada'] ?? null,
                $datos['observaciones'] ?? null
            ])) {
                return ['success' => true, 'mensaje' => 'Envío actualizado'];
            }
            
            return ['error' => 'Error al actualizar envío'];
            
        } catch (PDOException $e) {
            return ['error' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener estados de envío
     */
    public function obtenerEstados() {
        try {
            $query = "SELECT * FROM EstadosEnvio WHERE Estado = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>