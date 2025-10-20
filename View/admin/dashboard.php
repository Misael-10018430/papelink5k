<?php

// Ahora cargar los controladores
require_once __DIR__ . '/../../controllers/InventarioController.php';
require_once __DIR__ . '/../../controllers/ProductoController.php';

$inventarioController = new InventarioController();
$productoController = new ProductoController();

// Obtener productos con stock bajo
$productosStockBajo = $inventarioController->verStockBajo();

// Métricas simuladas (después conectarás con tus procedures reales)
$pedidosHoy = 47;
$pedidosMes = 1234;
$ingresosHoy = 18750.00;
$ingresosMes = 287640.00;

include 'includes/header.php';
?>

<h1 class="titulo-pagina">Dashboard Principal</h1>

<!-- DATOS DEL USUARIO LOGUEADO -->
<!--<div style="background-color: #f0f0f0; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
    <p><strong>Bienvenido:</strong> <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email_usuario']); ?></p>
    <p><strong>Rol:</strong> <?php echo htmlspecialchars($_SESSION['rol_usuario']); ?></p>
</div>
-->

<!-- MÉTRICAS EN TIEMPO REAL -->
<section>
    <h2 style="color: #FF6347; margin-bottom: 15px;">Métricas en Tiempo Real</h2>
    
    <div class="grid grid-4">
        <div class="tarjeta-metrica">
            <h3><?php echo $pedidosHoy; ?></h3>
            <p>Pedidos Hoy</p>
        </div>
        
        <div class="tarjeta-metrica">
            <h3><?php echo number_format($pedidosMes, 0, '', ','); ?></h3>
            <p>Pedidos Este Mes</p>
        </div>
        
        <div class="tarjeta-metrica">
            <h3>$<?php echo number_format($ingresosHoy, 2); ?></h3>
            <p>ingresos Hoy</p>
        </div>
        
        <div class="tarjeta-metrica">
            <h3>$<?php echo number_format($ingresosMes, 2); ?></h3>
            <p>Ingresos Este Mes</p>
        </div>
    </div>
</section>

<div class="grid grid-2" style="margin-top: 30px;">
    <!-- ALERTAS DE STOCK BAJO -->
    <section>
        <h2 style="color: #FF6347; margin-bottom: 15px;">Alertas de Stock Bajo</h2>
        
        <?php if (empty($productosStockBajo)): ?>
            <div class="alerta alerta-verde">
                No hay productos con stock bajo
            </div>
        <?php else: ?>
            <?php foreach ($productosStockBajo as $producto): ?>
                <div class="alerta alerta-amarilla">
                    <strong><?php echo htmlspecialchars($producto['NombreProducto']); ?></strong><br>
                    Stock actual: <?php echo $producto['CantidadDisponible']; ?> unidades<br>
                    Stock mínimo: <?php echo $producto['StockMinimo']; ?> unidades<br>
                    <a href="inventario.php?accion=ajustar&id=<?php echo $producto['IdProducto']; ?>" class="btn btn-naranja" style="margin-top: 10px;">
                         Reabastecer
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
    





















    <!-- PEDIDOS PENDIENTES (simulado) -->
    <section>
        <h2 style="color: #FF6347; margin-bottom: 15px;">Pedidos Pendientes por Procesar</h2>
        
        <table class="tabla">
            <thead>
                <tr>
                    <th>N° Pedido</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <a href="#" class="btn btn-verde"> Procesar</a>
            </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <a href="#" class="btn btn-verde">Procesar</a>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <a href="#" class="btn btn-verde">Procesar</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </section>
</div>
















<!-- ACCESOS DIRECTOS -->
<section style="margin-top: 30px;">
    <h2 style="color: #FF6347; margin-bottom: 15px;">Accesos Directos</h2>
    
    <div class="grid grid-4">
        <a href="productos.php?accion=nuevo" class="btn btn-naranja" style="padding: 15px; text-align: center;">
            Nuevo Producto
        </a>
        <a href="#" class="btn btn-naranja" style="padding: 15px; text-align: center;">
            Procesar Pedidos
        </a>
        <a href="#" class="btn btn-naranja" style="padding: 15px; text-align: center;">
            Ver Reportes
        </a>
        <a href="#" class="btn btn-naranja" style="padding: 15px; text-align: center;">
            Nueva Compra
        </a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>