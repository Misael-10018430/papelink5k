<?php
require_once __DIR__ . '/../config/Database.php';

class Devolucion {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Obtener todas las devoluciones (Admin)
     */
    public function obtenerTodas($estado = null, $fechaInicio = null, $fechaFin = null, $pagina = 1) {
        try {
            $query = "EXEC sp_ObtenerDevolucionesAdmin 
                      @EstadoDevolucion = ?,
                      @FechaInicio = ?,
                      @FechaFin = ?,
                      @Pagina = ?,
                      @RegistrosPorPagina = 20";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$estado, $fechaInicio, $fechaFin, $pagina]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Obtener detalle de una devolución
     */
    public function obtenerDetalle($idDevolucion, $idCliente = null) {
        try {
            $query = "EXEC sp_ObtenerDetalleDevolucion 
                      @IdDevolucion = ?,
                      @IdCliente = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$idDevolucion, $idCliente]);
            
            $devolucion = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$devolucion) {
                return null;
            }
            
            // Obtener productos
            $stmt->nextRowset();
            $devolucion['productos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $devolucion;
            
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Cambiar estado de devolución
     */
    public function cambiarEstado($idDevolucion, $nuevoEstado) {
        try {
            $query = "EXEC sp_CambiarEstadoDevolucion 
                      @IdDevolucion = ?,
                      @NuevoEstado = ?";
            
            $stmt = $this->conn->prepare($query);
            
            if ($stmt->execute([$idDevolucion, $nuevoEstado])) {
                return ['success' => true, 'mensaje' => 'Estado actualizado'];
            }
            
            return ['error' => 'Error al cambiar estado'];
            
        } catch (PDOException $e) {
            return ['error' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Reintegrar productos al inventario
     */
    public function reintegrarProductos($idDevolucion) {
        try {
            $query = "EXEC sp_ReintegrarProductosDevolucion @IdDevolucion = ?";
            
            $stmt = $this->conn->prepare($query);
            
            if ($stmt->execute([$idDevolucion])) {
                return ['success' => true, 'mensaje' => 'Productos reintegrados'];
            }
            
            return ['error' => 'Error al reintegrar productos'];
            
        } catch (PDOException $e) {
            return ['error' => 'Error: ' . $e->getMessage()];
        }
    }
}
?>