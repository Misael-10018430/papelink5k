<?php
/**
 * Modelo Configuración
 * Gestión de configuraciones del sistema
 */
require_once __DIR__ . '/../config/Database.php';
class Configuracion {
    private $conn;
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    /**
     * Obtener todas las configuraciones
     */
    public function obtenerConfiguraciones() {
        try {
            $sql = "EXEC sp_ObtenerConfiguracion";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();       
            $configuraciones = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $configuraciones[$row['Clave']] = $row;
            }
            return $configuraciones;
        } catch (PDOException $e) {
            error_log("Error en obtenerConfiguraciones: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Obtener una configuración específica
     */
    public function obtenerValor($clave) {
        try {
            $sql = "SELECT Valor FROM Configuracion WHERE Clave = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$clave]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? $resultado['Valor'] : null;
        } catch (PDOException $e) {
            error_log("Error en obtenerValor: " . $e->getMessage());
            return null;
        }
    }
    /**
     * Actualizar configuración
     */
    public function actualizar($clave, $valor) {
        try {
            $sql = "EXEC sp_ActualizarConfiguracion @Clave = ?, @Valor = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$clave, $valor]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'success' => true,
                'mensaje' => $resultado['Mensaje'] ?? 'Configuración actualizada correctamente'
            ];
        } catch (PDOException $e) {
            error_log("Error en actualizar: " . $e->getMessage());
            return [
                'success' => false,
                'mensaje' => 'Error al actualizar la configuración'
            ];
        }
    }
    /**
     * Actualizar múltiples configuraciones
     */
    public function actualizarMultiple($configuraciones) {
        try {
            $errores = [];
            $exitosas = 0;
            foreach ($configuraciones as $clave => $valor) {
                $resultado = $this->actualizar($clave, $valor);
                if ($resultado['success']) {
                    $exitosas++;
                } else {
                    $errores[] = "Error al actualizar {$clave}";
                }
            }
            if (empty($errores)) {
                return [
                    'success' => true,
                    'mensaje' => "{$exitosas} configuraciones actualizadas correctamente"
                ];
            } else {
                return [
                    'success' => false,
                    'mensaje' => implode(', ', $errores)
                ];
            }
        } catch (Exception $e) {
            error_log("Error en actualizarMultiple: " . $e->getMessage());
            return [
                'success' => false,
                'mensaje' => 'Error al actualizar las configuraciones'
            ];
        }
    }
    /**
     * Obtener estados de pedido
     */
    public function obtenerEstadosPedido() {
        try {
            $sql = "EXEC sp_ObtenerEstadosPedido";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerEstadosPedido: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Obtener estados de envío
     */
    public function obtenerEstadosEnvio() {
        try {
            $sql = "EXEC sp_ObtenerEstadosEnvio";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerEstadosEnvio: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Obtener funcionalidades de un rol
     */
    public function obtenerFuncionalidadesRol($idRol) {
        try {
            $sql = "EXEC sp_ObtenerFuncionalidadesRol @IdRol = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idRol]);   
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerFuncionalidadesRol: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Obtener todos los roles con sus funcionalidades
     */
    public function obtenerRolesConFuncionalidades() {
        try {
            // Obtener roles
            $sql = "SELECT IdRol, NombreRol, Descripcion FROM Roles WHERE Estado = 1 ORDER BY NombreRol";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Obtener funcionalidades por cada rol
            foreach ($roles as &$rol) {
                $rol['funcionalidades'] = $this->obtenerFuncionalidadesRol($rol['IdRol']);
            }  
            return $roles;
        } catch (PDOException $e) {
            error_log("Error en obtenerRolesConFuncionalidades: " . $e->getMessage());
            return [];
        }
    }
}