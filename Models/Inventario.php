<?php
require_once __DIR__ . '/../config/Database.php';
class Inventario {
    private $conn;
    private $db;
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    /**
     * Obtener inventario completo
     */
    public function obtenerCompleto($idCategoria = null, $soloStockBajo = 0) {
        try {
            $sql = "EXEC sp_ObtenerInventarioCompleto @IdCategoria = ?, @SoloStockBajo = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idCategoria, $soloStockBajo]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerCompleto: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Obtener productos con stock bajo
     */
    public function obtenerStockBajo() {
        try {
            $sql = "EXEC sp_ObtenerProductosStockBajo";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerStockBajo: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Ajustar inventario manualmente
     */
    public function ajustar($idProducto, $tipoAjuste, $cantidad, $motivo, $nuevoCostoUnitario = null) {
        try {
            $sql = "EXEC sp_AjustarInventario 
                    @IdProducto = ?, 
                    @TipoAjuste = ?, 
                    @Cantidad = ?, 
                    @Motivo = ?, 
                    @NuevoCostoUnitario = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $idProducto,
                $tipoAjuste,
                $cantidad,
                $motivo,
                $nuevoCostoUnitario
            ]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en ajustar: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    /**
     * Actualizar costo unitario
     */
    public function actualizarCosto($idProducto, $nuevoCosto) {
        try {
            $sql = "EXEC sp_ActualizarCostoUnitario @IdProducto = ?, @NuevoCostoUnitario = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idProducto, $nuevoCosto]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en actualizarCosto: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
?>