<?php
// test.php
header('Content-Type: text/plain');

echo "Variables de entorno:\n";
echo "DB_HOST: " . getenv('DB_HOST') . "\n";
echo "DB_NAME: " . getenv('DB_NAME') . "\n";
echo "DB_USER: " . getenv('DB_USER') . "\n";
echo "DB_PASSWORD: " . (getenv('DB_PASSWORD') ? "***CONFIGURADO***" : "***NO CONFIGURADO***") . "\n\n";

echo "Intentando conexión...\n";
try {
    $dsn = "sqlsrv:Server=" . getenv('DB_HOST') . ";Database=" . getenv('DB_NAME');
    $conn = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASSWORD'));
    echo "Conexión exitosa\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>