<?php
session_start();
require_once __DIR__ . '/config/Database.php';

echo "<h1>TEST SIMPLE</h1><pre>";

// ✅ Verificar si hay sesión activa
if (isset($_SESSION['cliente_id'])) {
    $idClienteSesion = (int)$_SESSION['cliente_id'];
    echo "✅ Hay sesión activa: IdCliente = {$idClienteSesion}\n\n";
} else {
    echo "❌ NO HAY SESIÓN ACTIVA\n";
    echo "Simulando sesión con IdCliente = 11 para pruebas...\n\n";
    $_SESSION['cliente_id'] = 11;
    $idClienteSesion = 11;
}

// Test 1: Conexión
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("❌ ERROR: No hay conexión a la base de datos");
}

echo "✅ Conexión a BD: OK\n\n";

// Test 2: Buscar pedidos del cliente en sesión
echo "=== BÚSQUEDA PARA IdCliente = {$idClienteSesion} ===\n";
$query = "SELECT IdPedido, NumeroPedido, IdCliente, Total, FechaPedido FROM Pedidos WHERE IdCliente = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$idClienteSesion]);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Total de pedidos encontrados: " . count($resultados) . "\n\n";

if (count($resultados) > 0) {
    echo "✅ PEDIDOS ENCONTRADOS:\n";
    foreach ($resultados as $r) {
        echo "  • IdPedido: {$r['IdPedido']}\n";
        echo "    Número: {$r['NumeroPedido']}\n";
        echo "    Total: \${$r['Total']}\n";
        echo "    Fecha: {$r['FechaPedido']}\n\n";
    }
} else {
    echo "❌ NO SE ENCONTRARON PEDIDOS PARA ESTE CLIENTE\n\n";
    
    // Verificar si existen pedidos en general
    $queryAll = "SELECT TOP 5 IdPedido, NumeroPedido, IdCliente FROM Pedidos ORDER BY FechaPedido DESC";
    $stmtAll = $conn->prepare($queryAll);
    $stmtAll->execute();
    $todos = $stmtAll->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($todos)) {
        echo "Últimos 5 pedidos en la BD (cualquier cliente):\n";
        foreach ($todos as $t) {
            echo "  • IdPedido: {$t['IdPedido']}, IdCliente: {$t['IdCliente']}, Número: {$t['NumeroPedido']}\n";
        }
        echo "\n⚠️ El IdCliente {$idClienteSesion} NO tiene pedidos asociados.\n";
    } else {
        echo "❌ NO HAY PEDIDOS EN LA BASE DE DATOS\n";
    }
}

// Test 3: Usar el modelo Pedido
echo "\n=== TEST CON MODELO PEDIDO ===\n";
require_once __DIR__ . '/models/Pedido.php';
$pedidoModel = new Pedido();
$pedidos = $pedidoModel->obtenerPorCliente($idClienteSesion, 100, null);

echo "Pedidos retornados por obtenerPorCliente(): " . count($pedidos) . "\n";

if (!empty($pedidos)) {
    echo "\n✅ PRIMER PEDIDO COMPLETO:\n";
    print_r($pedidos[0]);
} else {
    echo "\n❌ El método obtenerPorCliente() retornó array vacío\n";
}

echo "</pre>";
?>