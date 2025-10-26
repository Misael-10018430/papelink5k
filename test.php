<?php
/**
 * Script de verificación de configuración para Vercel
 * Acceder a: tu-dominio.vercel.app/test.php
 */

// Solo permitir en desarrollo y staging
if (getenv('VERCEL_ENV') === 'production') {
    http_response_code(404);
    exit('Not found');
}

require_once __DIR__ . '/config/config.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papelink - Test de Configuración</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #27ae60; }
        .error { color: #e74c3c; }
        .warning { color: #f39c12; }
        .info { color: #3498db; }
        .section { margin: 20px 0; padding: 15px; border-left: 4px solid #3498db; background: #f8f9fa; }
        .test-item { margin: 10px 0; padding: 8px; border-radius: 5px; }
        .test-pass { background: #d4edda; border-left: 4px solid #28a745; }
        .test-fail { background: #f8d7da; border-left: 4px solid #dc3545; }
        .test-warn { background: #fff3cd; border-left: 4px solid #ffc107; }
        pre { background: #2c3e50; color: white; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Papelink - Test de Configuración</h1>
        <p><em>Verificación del estado del sistema en Vercel</em></p>
        
        <!-- INFORMACIÓN DEL ENTORNO -->
        <div class="section">
            <h2>🌍 Información del Entorno</h2>
            <div class="test-item test-pass">
                <strong>Entorno Vercel:</strong> <?php echo getenv('VERCEL_ENV') ?: 'Local'; ?>
            </div>
            <div class="test-item test-pass">
                <strong>Región:</strong> <?php echo getenv('VERCEL_REGION') ?: 'N/A'; ?>
            </div>
            <div class="test-item test-pass">
                <strong>URL Base:</strong> <?php echo BASE_URL; ?>
            </div>
            <div class="test-item test-pass">
                <strong>Zona Horaria:</strong> <?php echo date_default_timezone_get(); ?>
            </div>
            <div class="test-item test-pass">
                <strong>Fecha/Hora:</strong> <?php echo date('Y-m-d H:i:s T'); ?>
            </div>
        </div>

        <!-- CONFIGURACIÓN PHP -->
        <div class="section">
            <h2>🐘 Configuración PHP</h2>
            <div class="test-item test-pass">
                <strong>Versión PHP:</strong> <?php echo PHP_VERSION; ?>
            </div>
            <div class="test-item test-pass">
                <strong>Límite de memoria:</strong> <?php echo ini_get('memory_limit'); ?>
            </div>
            <div class="test-item test-pass">
                <strong>Tiempo máximo ejecución:</strong> <?php echo ini_get('max_execution_time'); ?>s
            </div>
            <div class="test-item test-pass">
                <strong>Límite archivo subida:</strong> <?php echo ini_get('upload_max_filesize'); ?>
            </div>
        </div>

        <!-- EXTENSIONES PHP -->
        <div class="section">
            <h2>🔧 Extensiones PHP</h2>
            <?php
            $extensiones_requeridas = ['pdo', 'pdo_mysql', 'json', 'curl', 'openssl', 'fileinfo'];
            foreach ($extensiones_requeridas as $ext) {
                $cargada = extension_loaded($ext);
                $class = $cargada ? 'test-pass' : 'test-fail';
                $status = $cargada ? '✅' : '❌';
                echo "<div class='test-item {$class}'><strong>{$status} {$ext}:</strong> " . ($cargada ? 'Disponible' : 'NO disponible') . "</div>";
            }
            ?>
        </div>

        <!-- PRUEBA DE CONEXIÓN A BASE DE DATOS -->
        <div class="section">
            <h2>🗄️ Conexión a Base de Datos</h2>
            <?php
            try {
                $db = new Database();
                $conexion = $db->getConnection();
                
                if ($conexion) {
                    echo '<div class="test-item test-pass"><strong>✅ Conexión:</strong> Exitosa</div>';
                    
                    // Probar una consulta simple
                    try {
                        $stmt = $conexion->query("SELECT 1 as test");
                        $resultado = $stmt->fetch();
                        echo '<div class="test-item test-pass"><strong>✅ Consulta de prueba:</strong> Exitosa</div>';
                    } catch (Exception $e) {
                        echo '<div class="test-item test-fail"><strong>❌ Consulta de prueba:</strong> Error - ' . $e->getMessage() . '</div>';
                    }
                    
                    // Mostrar información de la base de datos
                    try {
                        $stmt = $conexion->query("SELECT DATABASE() as db_name");
                        $resultado = $stmt->fetch();
                        echo '<div class="test-item test-pass"><strong>📊 Base de datos:</strong> ' . $resultado['db_name'] . '</div>';
                    } catch (Exception $e) {
                        echo '<div class="test-item test-warn"><strong>⚠️ Info DB:</strong> No disponible</div>';
                    }
                    
                } else {
                    echo '<div class="test-item test-fail"><strong>❌ Conexión:</strong> Fallida</div>';
                }
            } catch (Exception $e) {
                echo '<div class="test-item test-fail"><strong>❌ Error de conexión:</strong> ' . $e->getMessage() . '</div>';
            }
            ?>
        </div>

        <!-- CONFIGURACIÓN DE BASE DE DATOS -->
        <div class="section">
            <h2>🔐 Configuración de Base de Datos</h2>
            <div class="test-item test-pass">
                <strong>Host:</strong> <?php echo getenv('DB_HOST') ?: 'PapelinkSk.mssql.somee.com'; ?>
            </div>
            <div class="test-item test-pass">
                <strong>Base de datos:</strong> <?php echo getenv('DB_NAME') ?: 'PapelinkSk'; ?>
            </div>
            <div class="test-item test-pass">
                <strong>Usuario:</strong> <?php echo getenv('DB_USER') ?: 'Misa_SQLLogin_1'; ?>
            </div>
            <div class="test-item test-pass">
                <strong>Password:</strong> <?php echo getenv('DB_PASSWORD') ? '***configurado***' : '***no configurado***'; ?>
            </div>
        </div>

        <!-- VARIABLES DE ENTORNO -->
        <div class="section">
            <h2>🔑 Variables de Entorno</h2>
            <?php
            $env_vars = ['VERCEL', 'VERCEL_ENV', 'VERCEL_URL', 'VERCEL_REGION', 'DB_HOST', 'DB_NAME', 'DB_USER'];
            foreach ($env_vars as $var) {
                $valor = getenv($var);
                $class = $valor ? 'test-pass' : 'test-warn';
                $display_valor = $valor ?: 'No configurada';
                if (strpos($var, 'PASSWORD') !== false && $valor) {
                    $display_valor = '***configurada***';
                }
                echo "<div class='test-item {$class}'><strong>{$var}:</strong> {$display_valor}</div>";
            }
            ?>
        </div>

        <!-- PRUEBA DE CONTROLADORES -->
        <div class="section">
            <h2>🎮 Prueba de Controladores</h2>
            <?php
            $controladores = [
                'ProductoController' => __DIR__ . '/controllers/ProductoController.php',
                'CategoriaController' => __DIR__ . '/controllers/CategoriaController.php',
                'MarcaController' => __DIR__ . '/controllers/MarcaController.php'
            ];
            
            foreach ($controladores as $nombre => $archivo) {
                if (file_exists($archivo)) {
                    try {
                        require_once $archivo;
                        if (class_exists($nombre)) {
                            echo "<div class='test-item test-pass'><strong>✅ {$nombre}:</strong> Disponible</div>";
                        } else {
                            echo "<div class='test-item test-fail'><strong>❌ {$nombre}:</strong> Clase no encontrada</div>";
                        }
                    } catch (Exception $e) {
                        echo "<div class='test-item test-fail'><strong>❌ {$nombre}:</strong> Error - {$e->getMessage()}</div>";
                    }
                } else {
                    echo "<div class='test-item test-fail'><strong>❌ {$nombre}:</strong> Archivo no encontrado</div>";
                }
            }
            ?>
        </div>

        <!-- INFORMACIÓN ADICIONAL -->
        <div class="section">
            <h2>Información Adicional</h2>
            <pre><?php
            echo "Directorio actual: " . __DIR__ . "\n";
            echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "\n";
            echo "Script Name: " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "\n";
            echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "\n";
            echo "HTTP Host: " . ($_SERVER['HTTP_HOST'] ?? 'N/A') . "\n";
            ?></pre>
        </div>

        <div class="section" style="border-left-color: #27ae60; background: #d4edda;">
            <h3>Estado General</h3>
            <p>
                <?php
                $db_test = false;
                try {
                    $db = new Database();
                    $db_test = $db->testConnection();
                } catch (Exception $e) {
                    // Silenciar error para el resumen
                }
                
                if ($db_test) {
                    echo '<span class="success"> <strong>Sistema funcionando correctamente</strong> - Listo para producción</span>';
                } else {
                    echo '<span class="warning"> <strong>Sistema funcionando con limitaciones</strong> - Verificar configuración de base de datos</span>';
                }
                ?>
            </p>
        </div>
    </div>
</body>
</html>