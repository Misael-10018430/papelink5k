<?php
/**
 * Clase de Autorización
 * Maneja permisos basados en funcionalidades
 */
class Auth {
    /**
     * Verificar que el usuario sea un empleado logueado
     * Redirige al login si no está autenticado
     */
    public static function checkEmpleadoLogin() {
        // Verificar que la sesión esté iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar que esté logueado
        if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
            $_SESSION['error'] = 'Debe iniciar sesión como empleado';
            redirect('view/admin/login.php');
        }
        
        // Verificar que sea empleado
        if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'empleado') {
            $_SESSION['error'] = 'Acceso denegado. Solo para empleados';
            redirect('view/cliente/index.php');
        }
        
        // Verificar que tenga ID de usuario
        if (!isset($_SESSION['usuario_id'])) {
            $_SESSION['error'] = 'Sesión inválida. Inicie sesión nuevamente';
            redirect('view/admin/login.php');
        }
    }
    
    /**
     * Verificar si el cliente está logueado
     */
    public static function checkClienteLogin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['cliente_id'])) {
            $_SESSION['error'] = 'Debe iniciar sesión para acceder';
            redirect('view/cliente/login.php');
        }
    }
    
    /**
     * Verificar si el empleado tiene una funcionalidad específica
     * @param string $funcionalidad Nombre de la funcionalidad requerida
     * @return bool
     */
    public static function tieneFuncionalidad($funcionalidad) {
        // Verificar que esté logueado
        if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['empleado_id'])) {
            return false;
        }
        
        // Verificar que tenga funcionalidades cargadas
        if (!isset($_SESSION['funcionalidades']) || !is_array($_SESSION['funcionalidades'])) {
            return false;
        }
        
        // Buscar la funcionalidad
        return in_array($funcionalidad, $_SESSION['funcionalidades']);
    }
    
    /**
     * Requerir una funcionalidad específica
     * Redirige al dashboard si no tiene permiso
     * @param string $funcionalidad Nombre de la funcionalidad requerida
     */
    public static function requiereFuncionalidad($funcionalidad) {
        self::checkEmpleadoLogin();
        
        if (self::esAdministrador()) {
            return;
        }
        
        if (!self::tieneFuncionalidad($funcionalidad)) {
            $_SESSION['error'] = 'No tiene permisos para acceder a esta sección';
            redirect('view/admin/dashboard.php');
        }
    }
    
    /**
     * Verificar múltiples funcionalidades (al menos una)
     * @param array $funcionalidades Array de funcionalidades
     * @return bool
     */
    public static function tieneAlgunaFuncionalidad($funcionalidades) {
        if (self::esAdministrador()) {
            return true;
        }
        
        foreach ($funcionalidades as $func) {
            if (self::tieneFuncionalidad($func)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Requerir al menos una funcionalidad de una lista
     * @param array $funcionalidades Array de funcionalidades
     */
    public static function requiereAlgunaFuncionalidad($funcionalidades) {
        self::checkEmpleadoLogin();
        
        if (self::esAdministrador()) {
            return;
        }
        
        if (!self::tieneAlgunaFuncionalidad($funcionalidades)) {
            $_SESSION['error'] = 'No tiene permisos para acceder a esta sección';
            redirect('view/admin/dashboard.php');
        }
    }
    
    /**
     * Verificar si es administrador
     * @return bool
     */
    public static function esAdministrador() {
        if (isset($_SESSION['roles'])) {
            $roles = explode(',', $_SESSION['roles']);
            foreach ($roles as $rol) {
                if (strtolower(trim($rol)) === 'administrador') {
                    return true;
                }
            }
        }
        
        // Verificar por rol_usuario único (compatibilidad)
        if (isset($_SESSION['rol_usuario']) && 
            strtolower($_SESSION['rol_usuario']) === 'administrador') {
            return true;
        }
        
        // Verificar por nivel de acceso
        if (isset($_SESSION['nivel_acceso']) && $_SESSION['nivel_acceso'] >= 90) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Requerir ser administrador
     */
    public static function requiereAdministrador() {
        self::checkEmpleadoLogin();
        
        if (!self::esAdministrador()) {
            $_SESSION['error'] = 'Solo los administradores pueden acceder a esta sección';
            redirect('view/admin/dashboard.php');
        }
    }
    
    /**
     * Obtener funcionalidades del empleado desde la BD
     * @param int $empleadoId
     * @param PDO $conn
     * @return array
     */
    public static function cargarFuncionalidades($empleadoId, $conn) {
        try {
            $query = "EXEC sp_ObtenerFuncionalidadesEmpleado @IdEmpleado = :empleado_id";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':empleado_id', $empleadoId, PDO::PARAM_INT);
            $stmt->execute();
            
            $funcionalidades = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $funcionalidades[] = $row['NombreFuncionalidad'];
            }
            
            return $funcionalidades;
            
        } catch (PDOException $e) {
            error_log("Error cargando funcionalidades: " . $e->getMessage());
            return [];
        }
    }
}
?>