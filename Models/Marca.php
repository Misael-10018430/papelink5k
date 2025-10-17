<?php
require_once __DIR__ . '/../config/Database.php';
class Marca {
    private $conn;
    private $db;
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    /**
     * Obtener todas las marcas (para cliente - solo activas)
     */
    public function obtenerActivas() {
        try {
            $sql = "EXEC sp_ObtenerMarcas";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerActivas: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Obtener todas las marcas (para admin con filtros)
     */
    public function obtenerTodas($estado = null) {
        try {
            $sql = "EXEC sp_ObtenerMarcasAdmin @Estado = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$estado]); 
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerTodas: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Crear nueva marca
     */
    public function crear($datos) {
        try {
            $sql = "EXEC sp_CrearMarca 
                    @NombreMarca = ?, 
                    @LogoMarca = ?, 
                    @DescripcionMarca = ?, 
                    @SitioWeb = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $datos['nombreMarca'],
                $datos['logoMarca'] ?? null,
                $datos['descripcionMarca'] ?? null,
                $datos['sitioWeb'] ?? null
            ]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en crear: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    /**
     * Actualizar marca
     */
    public function actualizar($datos) {
        try {
            $sql = "EXEC sp_ActualizarMarca 
                    @IdMarca = ?, 
                    @NombreMarca = ?, 
                    @LogoMarca = ?, 
                    @DescripcionMarca = ?, 
                    @SitioWeb = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $datos['idMarca'],
                $datos['nombreMarca'],
                $datos['logoMarca'] ?? null,
                $datos['descripcionMarca'] ?? null,
                $datos['sitioWeb'] ?? null
            ]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en actualizar: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    /**
     * Cambiar estado de marca
     */
    public function cambiarEstado($idMarca, $estado) {
        try {
            $sql = "EXEC sp_CambiarEstadoMarca @IdMarca = ?, @Estado = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idMarca, $estado]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en cambiarEstado: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
?>