<?php
/**
 * Clase de conexión a la base de datos
 * SQL Server con PDO
 */
if (!class_exists('Database')) {
    class Database {
        // Configuración de SQL Server
        private $host = ".\SQLEXPRESS";
        private $db_name = "Papelink";
        private $username = "webmaster";
        private $password = "Unach2025*";
        public $conn;
        /**
         * Obtener conexión a la base de datos
         * @return PDO|null
         */
        public function getConnection() {
            $this->conn = null;           
            try {
                // Conexión con PDO y driver sqlsrv (SQL Server)
                $this->conn = new PDO(
                    "sqlsrv:Server={$this->host};Database={$this->db_name}", 
                    $this->username, 
                    $this->password
                );               
                // Configurar PDO para lanzar excepciones en errores
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);                
                // Modo de obtención por defecto: array asociativo
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);                
            } catch(PDOException $exception) {
                echo "Error de conexión: " . $exception->getMessage();
                error_log("Database Connection Error: " . $exception->getMessage());
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
    }
}
?>