<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>TEST - Sesión de Cliente</h1>";

require_once __DIR__ . '/../../config/config.php';
echo "<p>✅ Config cargado</p>";

echo "<h2>Información de Sesión:</h2>";
echo "<pre>";
echo "session_status(): " . session_status() . "\n";
echo "Session ID: " . session_id() . "\n";
echo "\n<strong>Contenido de \$_SESSION:</strong>\n";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Verificaciones:</h2>";

if (isset($_SESSION['cliente_id'])) {
    echo "<p style='color: green;'>✅ cliente_id existe: " . $_SESSION['cliente_id'] . "</p>";
} else {
    echo "<p style='color: red;'>❌ cliente_id NO existe</p>";
}

if (isset($_SESSION['tipo_usuario'])) {
    echo "<p>Tipo de usuario: " . $_SESSION['tipo_usuario'] . "</p>";
}

if (isset($_SESSION['nombre_cliente'])) {
    echo "<p>Nombre: " . $_SESSION['nombre_cliente'] . "</p>";
}

echo "<hr>";
echo "<h2>Probar carga de controller:</h2>";

require_once __DIR__ . '/../../controllers/PedidoController.php';
echo "<p>✅ PedidoController cargado</p>";

if (isset($_SESSION['cliente_id'])) {
    $pedidoController = new PedidoController();
    echo "<p>✅ Controller instanciado</p>";
    
    $pedidos = $pedidoController->misPedidos();
    echo "<p>✅ misPedidos() ejecutado</p>";
    echo "<p>Pedidos encontrados: <strong>" . count($pedidos) . "</strong></p>";
    
    if (!empty($pedidos)) {
        echo "<h3>Primer pedido:</h3>";
        echo "<pre>";
        print_r($pedidos[0]);
        echo "</pre>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ No se puede probar misPedidos() sin sesión de cliente</p>";
}

echo "<hr>";
echo "<h1 style='color: green;'>✅ TEST COMPLETADO</h1>";
?>
```

