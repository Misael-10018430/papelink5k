<?php
/**
 * Modelo Empleado
 * Gestión de empleados, roles y permisos del sistema
 */
require_once __DIR__ . '/../config/Database.php';
class Empleado {
    private $conn;
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    /**
     * Obtener todos los empleados
     */





















    public function obtenerEmpleados($estado = null) {
    try {
        $sql = "EXEC sp_ObtenerEmpleados @Estado = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$estado]);
        
        // ✅ IMPORTANTE: Usar fetchAll para obtener TODOS los resultados
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // ✅ Limpiar el buffer de resultados
        $stmt->closeCursor();
        
        return $resultados;
        
    } catch (PDOException $e) {
        error_log("Error en obtenerEmpleados: " . $e->getMessage());
        return [];
    }
    }












    /**
     * Obtener un empleado por ID
     */
    public function obtenerPorId($idEmpleado) {
        try {
            $sql = "SELECT 
                        IdEmpleado,
                        NombreCompleto,
                        Usuario,
                        Email,
                        Estado
                    FROM Empleados 
                    WHERE IdEmpleado = ?";           
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idEmpleado]);           
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerPorId: " . $e->getMessage());
            return null;
        }
    }
    /**
     * Registrar nuevo empleado
     */
    public function registrar($datos) {
        try {
            // Hash de la contraseña
            $passwordHash = password_hash($datos['password'], PASSWORD_BCRYPT);            
            $sql = "EXEC sp_RegistrarEmpleado 
                    @NombreCompleto = ?,
                    @Usuario = ?,
                    @Email = ?,
                    @ContraseñaHash = ?,
                    @IdRol = ?";           
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $datos['nombre'],
                $datos['usuario'],
                $datos['email'],
                $passwordHash,
                $datos['id_rol'] ?? null
            ]);           
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);            
            return [
                'success' => true,
                'mensaje' => 'Empleado registrado correctamente',
                'id' => $resultado['IdEmpleado'] ?? null
            ];
        } catch (PDOException $e) {
            error_log("Error en registrar: " . $e->getMessage());            
            // Detectar errores específicos
            $mensaje = 'Error al registrar el empleado';
            if (strpos($e->getMessage(), '50001') !== false) {
                $mensaje = 'El nombre de usuario ya está registrado';
            } elseif (strpos($e->getMessage(), '50002') !== false) {
                $mensaje = 'El email ya está registrado';
            }            
            return [
                'success' => false,
                'mensaje' => $mensaje
            ];
        }
    }
    /**
     * Actualizar empleado
     */
    public function actualizar($idEmpleado, $datos) {
        try {
            $sql = "EXEC sp_ActualizarEmpleado 
                    @IdEmpleado = ?,
                    @NombreCompleto = ?,
                    @Email = ?";          
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $idEmpleado,
                $datos['nombre'],
                $datos['email'] ?? null
            ]);           
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);            
            return [
                'success' => true,
                'mensaje' => $resultado['Mensaje'] ?? 'Empleado actualizado correctamente'
            ];
        } catch (PDOException $e) {
            error_log("Error en actualizar: " . $e->getMessage());            
            $mensaje = 'Error al actualizar el empleado';
            if (strpos($e->getMessage(), '50035') !== false) {
                $mensaje = 'El email ya está registrado por otro empleado';
            }            
            return [
                'success' => false,
                'mensaje' => $mensaje
            ];
        }
    }
    /**
     * Cambiar estado del empleado (Activo/Inactivo)
     */
    public function cambiarEstado($idEmpleado, $estado) {
        try {
            $sql = "EXEC sp_CambiarEstadoEmpleado @IdEmpleado = ?, @Estado = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idEmpleado, $estado]);           
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);            
            return [
                'success' => true,
                'mensaje' => $resultado['Mensaje'] ?? 'Estado actualizado correctamente'
            ];
        } catch (PDOException $e) {
            error_log("Error en cambiarEstado: " . $e->getMessage());
            return [
                'success' => false,
                'mensaje' => 'Error al cambiar el estado del empleado'
            ];
        }
    }
    /**
     * Cambiar contraseña del empleado
     */
    public function cambiarContrasena($idEmpleado, $passwordActual, $passwordNueva) {
        try {
            $passwordActualHash = password_hash($passwordActual, PASSWORD_BCRYPT);
            $passwordNuevaHash = password_hash($passwordNueva, PASSWORD_BCRYPT);            
            $sql = "EXEC sp_CambiarContraseñaEmpleado 
                    @IdEmpleado = ?,
                    @ContraseñaActualHash = ?,
                    @ContraseñaNuevaHash = ?";            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $idEmpleado,
                $passwordActualHash,
                $passwordNuevaHash
            ]);            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);           
            return [
                'success' => true,
                'mensaje' => $resultado['Mensaje'] ?? 'Contraseña actualizada correctamente'
            ];

        } catch (PDOException $e) {
            error_log("Error en cambiarContrasena: " . $e->getMessage());           
            $mensaje = 'Error al cambiar la contraseña';
            if (strpos($e->getMessage(), '50036') !== false) {
                $mensaje = 'La contraseña actual es incorrecta';
            }           
            return [
                'success' => false,
                'mensaje' => $mensaje
            ];
        }
    }
    // ========================================
    // GESTIÓN DE ROLES
    // ========================================

    /**
     * Obtener todos los roles
     */
    public function obtenerRoles($estado = null) {
        try {
            $sql = "EXEC sp_ObtenerRoles @Estado = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$estado]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en obtenerRoles: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Obtener roles asignados a un empleado
     */
    public function obtenerRolesEmpleado($idEmpleado) {
        try {
            $sql = "SELECT 
                        r.IdRol,
                        r.NombreRol,
                        r.Descripcion
                    FROM Empleado_Roles er
                    INNER JOIN Roles r ON er.IdRol = r.IdRol
                    WHERE er.IdEmpleado = ?
                    ORDER BY r.NombreRol";           
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idEmpleado]);            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerRolesEmpleado: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Asignar rol a empleado
     */
    public function asignarRol($idEmpleado, $idRol) {
        try {
            $sql = "EXEC sp_AsignarRolEmpleado @IdEmpleado = ?, @IdRol = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idEmpleado, $idRol]);            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);            
            return [
                'success' => true,
                'mensaje' => $resultado['Mensaje'] ?? 'Rol asignado correctamente'
            ];
        } catch (PDOException $e) {
            error_log("Error en asignarRol: " . $e->getMessage());            
            $mensaje = 'Error al asignar el rol';
            if (strpos($e->getMessage(), '50037') !== false) {
                $mensaje = 'El empleado ya tiene asignado este rol';
            }            
            return [
                'success' => false,
                'mensaje' => $mensaje
            ];
        }
    }
    /**
     * Remover rol de empleado
     */
    public function removerRol($idEmpleado, $idRol) {
        try {
            $sql = "EXEC sp_RemoverRolEmpleado @IdEmpleado = ?, @IdRol = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idEmpleado, $idRol]);           
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);           
            return [
                'success' => true,
                'mensaje' => $resultado['Mensaje'] ?? 'Rol removido correctamente'
            ];
        } catch (PDOException $e) {
            error_log("Error en removerRol: " . $e->getMessage());
            return [
                'success' => false,
                'mensaje' => 'Error al remover el rol'
            ];
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
     * Verificar si un empleado tiene permiso para una funcionalidad
     */
    public function verificarPermiso($idEmpleado, $nombreFuncionalidad) {
        try {
            $sql = "EXEC sp_VerificarPermisoEmpleado 
                    @IdEmpleado = ?,
                    @NombreFuncionalidad = ?";            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idEmpleado, $nombreFuncionalidad]);          
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);         
            return (bool)($resultado['TienePermiso'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error en verificarPermiso: " . $e->getMessage());
            return false;
        }
    }
}