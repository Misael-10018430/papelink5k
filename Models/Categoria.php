<?php
require_once __DIR__ . '/../config/Database.php';
class Categoria {
    private $conn;
    private $db;
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    /**
     * Obtener todas las categorías (para cliente - solo activas)
     */
    public function obtenerActivas() {
        try {
            $sql = "EXEC sp_ObtenerCategorias";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerActivas: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Obtener todas las categorías (para admin con filtros)
     */
    public function obtenerTodas($estado = null) {
        try {
            $sql = "EXEC sp_ObtenerCategoriasAdmin @Estado = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$estado]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerTodas: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Crear nueva categoría
     */
    public function crear($nombreCategoria) {
        try {
            $sql = "EXEC sp_CrearCategoria @NombreCategoria = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$nombreCategoria]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en crear: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    /**
     * Actualizar categoría
     */
    public function actualizar($idCategoria, $nombreCategoria) {
        try {
            $sql = "EXEC sp_ActualizarCategoria @IdCategoria = ?, @NombreCategoria = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idCategoria, $nombreCategoria]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en actualizar: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    /**
     * Cambiar estado de categoría
     */
    public function cambiarEstado($idCategoria, $estado) {
        try {
            $sql = "EXEC sp_CambiarEstadoCategoria @IdCategoria = ?, @Estado = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idCategoria, $estado]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en cambiarEstado: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
?>