<?php
/**
 * Clase de conexión a la base de datos
 * MySQL para Somee.com hosting
 */
if (!class_exists('Database')) {
    class Database {
        // Configuración para Somee.com MySQL
        private $host;
        private $db_name;
        private $username;
        private $password;
        public $conn;
        
        public function __construct() {
            // Usar variables de entorno en producción (Vercel)
            // o valores por defecto para desarrollo local
            $this->host = getenv('DB_HOST') ?: 'PapelinkSk.mssql.somee.com';
            $this->db_name = getenv('DB_NAME') ?: 'PapelinkSk';
            $this->username = getenv('DB_USER') ?: 'Misa_SQLLogin_1';
            $this->password = getenv('DB_PASSWORD') ?: 'vzmb7ytjhk';
        }
        
        /**
         * Obtener conexión a la base de datos
         * @return PDO|null
         */
        public function getConnection() {
            $this->conn = null;           
            try {
                // Conexión con PDO para SQL Server (corregido)
                $dsn = "sqlsrv:Server={$this->host};Database={$this->db_name}";
                
                $this->conn = new PDO(
                    $dsn,
                    $this->username, 
                    $this->password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        // Eliminada la opción específica de MySQL
                    ]
                );
                
            } catch(PDOException $exception) {
                error_log("Database Connection Error: " . $exception->getMessage());
                
                // En desarrollo mostrar error, en producción no
                if (getenv('VERCEL_ENV') !== 'production') {
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