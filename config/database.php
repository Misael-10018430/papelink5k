<?php
/**
 * Clase de conexión a la base de datos
 * Conexión a SQL Server para Azure
 */
if (!class_exists('Database')) {
    class Database {
        // Configuración para la base de datos
        private $host;
        private $db_name;
        private $username;
        private $password;
        public $conn;
        
        public function __construct() {
            // Usar variables de entorno en producción (Azure)
            // o valores por defecto para desarrollo local
            $this->host = getenv('DB_HOST') ?: 'localhost';
            $this->db_name = getenv('DB_NAME') ?: 'papelink_local';
            $this->username = getenv('DB_USER') ?: 'root';
            $this->password = getenv('DB_PASSWORD') ?: '';
        }
        
        /**
         * Obtener conexión a la base de datos
         * @return PDO|null
         */
        public function getConnection() {
            $this->conn = null;           
            try {
                // Conexión con PDO para SQL Server (Formato correcto para Azure)
                $dsn = "sqlsrv:Server={$this->host};Database={$this->db_name}";
                
                $this->conn = new PDO(
                    $dsn,
                    $this->username, 
                    $this->password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
                
            } catch(PDOException $exception) {
                error_log("Database Connection Error: " . $exception->getMessage());
                
                // CAMBIO CLAVE: Lógica para mostrar errores solo en desarrollo local
                // Si la variable de entorno DB_HOST existe, asumimos que estamos en producción (Azure) y no mostramos errores.
                if (!getenv('DB_HOST')) {
                    echo "Error de conexión: " . $exception->getMessage();
                }
            }            
            return $this->conn;
        }
                
        /**
         * Cerrar conexión
         */
        public function closeConnection() {
            $this->conn = null;
        }
        
        /**
         * Verificar si la conexión está activa
         * @return bool
         */
        public function isConnected() {
            return $this->conn !== null;
        }
        
        /**
         * Probar conexión
         * @return bool
         */
        public function testConnection() {
            try {
                $conn = $this->getConnection();
                if ($conn) {
                    $stmt = $conn->query("SELECT 1");
                    return $stmt !== false;
                }
                return false;
            } catch (Exception $e) {
                return false;
            }
        }
    }
}
?>