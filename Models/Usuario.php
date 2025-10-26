<?php
require_once __DIR__ . '/../config/Database.php';
class Usuario {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    /**
     * Login de empleado (Admin) - SIN HASH
     */
    public function loginEmpleado($email, $password) {
    try {
        $query = "EXEC sp_LoginEmpleado @Email = :email, @Contraseña = :password";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();  
        $empleado = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($empleado && isset($empleado['IdEmpleado'])) {
            return [
                'success' => true,
                'usuario' => [
                    'id' => $empleado['IdEmpleado'],
                    'nombre' => $empleado['NombreCompleto'],
                    'email' => $empleado['Email'],
                    'usuario' => $empleado['Usuario'],
                    'rol' => $empleado['Roles'] ?? 'Sin Rol' // ✅ IMPORTANTE
                ]
            ];
        } else {
            return ['error' => 'Credenciales incorrectas'];
        }
    } catch (PDOException $e) {
        error_log("Error en loginEmpleado: " . $e->getMessage());
        return ['error' => 'Error en el sistema'];
    }
}
    /**
     * Login de cliente - SIN HASH
     */
    public function loginCliente($email, $password) {
        try {
            if (!$this->conn) {
                return ['error' => 'No hay conexión a la base de datos'];
            }
            //  YA NO SE USA HASH
            $sql = "EXEC sp_LoginCliente @Email = ?, @Contraseña = ?";
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                return ['error' => 'Error al preparar la consulta'];
            }    
            $stmt->bindParam(1, $email, PDO::PARAM_STR);
            $stmt->bindParam(2, $password, PDO::PARAM_STR);  // ⬅️ Directo sin hash
            if (!$stmt->execute()) {
                return ['error' => 'Email o contraseña incorrectos'];
            }
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($cliente) {
                return [
                    'success' => true,
                    'usuario' => [
                        'id' => $cliente['IdCliente'],
                        'nombre' => $cliente['NombreCliente'],
                        'email' => $cliente['Email'],
                        'telefono' => $cliente['Telefono'] ?? '',
                        'direccion' => $cliente['Direccion'] ?? '',
                        'tipo_cliente' => $cliente['IdTipoCliente'],
                        'segmento' => $cliente['IdSegmentoCliente'] ?? null,
                        'canal' => $cliente['CanalCliente'],
                        'tipo' => 'cliente'
                    ]
                ];
            } else {
                return ['error' => 'Email o contraseña incorrectos'];
            }
        } catch (PDOException $e) {
            return ['error' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }
    /**
     * Registrar nuevo cliente - SIN HASH
     */
    public function registrarCliente($datos) {
        try {
            if (!$this->conn) {
                return ['error' => 'No hay conexión a la base de datos'];
            }
            // YA NO SE USA HASH
            $sql = "EXEC sp_RegistrarCliente 
                    @NombreCliente = ?,
                    @Email = ?,
                    @Telefono = ?,
                    @Direccion = ?,
                    @ContraseñaHash = ?,
                    @IdTipoCliente = ?,
                    @CanalCliente = ?";          
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                return ['error' => 'Error al preparar la consulta'];
            }
            $direccion = $datos['direccion'] ?? null;
            $idTipoCliente = 2;
            $canalCliente = 'DIGITAL';
            $stmt->bindParam(1, $datos['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(2, $datos['email'], PDO::PARAM_STR);
            $stmt->bindParam(3, $datos['telefono'], PDO::PARAM_STR);
            $stmt->bindParam(4, $direccion, PDO::PARAM_STR);
            $stmt->bindParam(5, $datos['password'], PDO::PARAM_STR);  // ⬅️ Directo sin hash
            $stmt->bindParam(6, $idTipoCliente, PDO::PARAM_INT);
            $stmt->bindParam(7, $canalCliente, PDO::PARAM_STR);
            if (!$stmt->execute()) {
                $errorInfo = $stmt->errorInfo();
                return ['error' => 'Error: ' . $errorInfo[2]];
            }
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($resultado && isset($resultado['IdCliente'])) {
                return [
                    'success' => true,
                    'mensaje' => 'Cliente registrado exitosamente',
                    'id_cliente' => $resultado['IdCliente']
                ];
            } else {
                return ['error' => 'Error al registrar cliente'];
            }
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'email ya está registrado') !== false) {
                return ['error' => 'El email ya está registrado'];
            }
            return ['error' => 'Error al registrar: ' . $e->getMessage()];
        }
    }
    /**
     * Verificar si existe email
     */
    public function existeEmail($email, $tipo = 'cliente') {
        try {
            if ($tipo === 'cliente') {
                $query = "SELECT COUNT(*) as total FROM Clientes WHERE Email = ?";
            } else {
                $query = "SELECT COUNT(*) as total FROM Empleados WHERE Email = ?";
            }
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                return false;
            }
            $stmt->bindParam(1, $email, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>