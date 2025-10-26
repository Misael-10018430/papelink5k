<?php
/**
 * Modelo Proveedor
 * Gestión de proveedores del sistema
 */
require_once __DIR__ . '/../config/Database.php';
class Proveedor {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    /**
     * Obtener todos los proveedores
     */
    public function obtenerProveedores($estado = null) {
        try {
            $sql = "EXEC sp_ObtenerProveedores @Estado = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$estado]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerProveedores: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Obtener un proveedor por ID
     */
    public function obtenerPorId($idProveedor) {
        try {
            $sql = "SELECT 
                        IdProveedor,
                        NombreProveedor,
                        Telefono,
                        Email,
                        Direccion,
                        Estado
                    FROM Proveedores 
                    WHERE IdProveedor = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idProveedor]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerPorId: " . $e->getMessage());
            return null;
        }
    }
    /**
     * Crear nuevo proveedor
     */
    public function crear($datos) {
        try {
            $sql = "EXEC sp_CrearProveedor 
                    @NombreProveedor = ?,
                    @Telefono = ?,
                    @Email = ?,
                    @Direccion = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $datos['nombre'],
                $datos['telefono'] ?? null,
                $datos['email'] ?? null,
                $datos['direccion'] ?? null
            ]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'success' => true,
                'mensaje' => $resultado['Mensaje'] ?? 'Proveedor creado correctamente',
                'id' => $resultado['IdProveedor'] ?? null
            ];
        } catch (PDOException $e) {
            error_log("Error en crear: " . $e->getMessage());
            return [
                'success' => false,
                'mensaje' => 'Error al crear el proveedor'
            ];
        }
    }
    /**
     * Actualizar proveedor
     */
    public function actualizar($idProveedor, $datos) {
        try {
            $sql = "EXEC sp_ActualizarProveedor 
                    @IdProveedor = ?,
                    @NombreProveedor = ?,
                    @Telefono = ?,
                    @Email = ?,
                    @Direccion = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $idProveedor,
                $datos['nombre'],
                $datos['telefono'] ?? null,
                $datos['email'] ?? null,
                $datos['direccion'] ?? null
            ]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'success' => true,
                'mensaje' => $resultado['Mensaje'] ?? 'Proveedor actualizado correctamente'
            ];
        } catch (PDOException $e) {
            error_log("Error en actualizar: " . $e->getMessage());
            return [
                'success' => false,
                'mensaje' => 'Error al actualizar el proveedor'
            ];
        }
    }
    /**
     * Cambiar estado del proveedor (Activo/Inactivo)
     */
    public function cambiarEstado($idProveedor, $estado) {
        try {
            $sql = "EXEC sp_CambiarEstadoProveedor @IdProveedor = ?, @Estado = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idProveedor, $estado]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'success' => true,
                'mensaje' => $resultado['Mensaje'] ?? 'Estado actualizado correctamente'
            ];
        } catch (PDOException $e) {
            error_log("Error en cambiarEstado: " . $e->getMessage());
            return [
                'success' => false,
                'mensaje' => 'Error al cambiar el estado del proveedor'
            ];
        }
    }
    /**
     * Obtener historial de compras de un proveedor
     */
    public function obtenerHistorialCompras($idProveedor, $limite = 10) {
        try {
            $sql = "SELECT TOP (?) 
                        c.IdCompra,
                        c.FechaCompra,
                        c.Total,
                        ec.NombreEstado,
                        ec.Color
                    FROM ComprasProveedores c
                    INNER JOIN EstadosCompra ec ON c.IdEstadoCompra = ec.IdEstadoCompra
                    WHERE c.IdProveedor = ?
                    ORDER BY c.FechaCompra DESC";     
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$limite, $idProveedor]);   
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerHistorialCompras: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Obtener estadísticas del proveedor
     */
    public function obtenerEstadisticas($idProveedor) {
        try {
            $sql = "SELECT 
                        COUNT(*) AS TotalCompras,
                        ISNULL(SUM(Total), 0) AS TotalGastado,
                        ISNULL(AVG(Total), 0) AS PromedioCompra
                    FROM ComprasProveedores
                    WHERE IdProveedor = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idProveedor]);         
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerEstadisticas: " . $e->getMessage());
            return [
                'TotalCompras' => 0,
                'TotalGastado' => 0,
                'PromedioCompra' => 0
            ];
        }
    }
}