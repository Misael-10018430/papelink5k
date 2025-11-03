<?php
/**
 * Vista: Detalle del Pedido (Cliente)
 */

require_once __DIR__ . '/../../config/config.php';

// Verificar que el cliente esté logueado
if (!isset($_SESSION['cliente_id'])) {
    $_SESSION['error'] = 'Debe iniciar sesión';
    header('Location: ' . BASE_URL . 'view/cliente/login.php');
    exit;
}

// Verificar que se pasó el ID del pedido
if (!isset($_GET['id'])) {
    $_SESSION['error'] = 'ID de pedido no especificado';
    header('Location: mis_pedidos.php');
    exit;
}

require_once __DIR__ . '/../../controllers/PedidoController.php';

$pedidoController = new PedidoController();
$idPedido = (int)$_GET['id'];

// Obtener detalle del pedido
$pedido = $pedidoController->verDetalle($idPedido);

// Verificar que el pedido existe y pertenece al cliente
if (!$pedido || $pedido['IdCliente'] != $_SESSION['cliente_id']) {
    $_SESSION['error'] = 'Pedido no encontrado o no tienes permiso para verlo';
    header('Location: mis_pedidos.php');
    exit;
}

$titulo = "Detalle del Pedido - Papelink";
include_once __DIR__ . '/includes/header.php';
?>

<div class="contenedor-principal">
<style>
    :root {
        --color-primario: #2C3E50;
        --color-acento: #FF6347;
        --color-exito: #27AE60;
        --color-texto: #2C3E50;
        --color-texto-claro: #7F8C8D;
        --color-fondo: #ECF0F1;
        --color-blanco: #FFFFFF;
        --color-borde: #BDC3C7;
    }

    .contenedor-principal {
        max-width: 1200px;
        margin: 0 auto;
        padding: 30px 20px;
        background-color: var(--color-fondo);
    }

    .header-detalle {
        background: var(--color-blanco);
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 25px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .header-detalle h1 {
        color: var(--color-primario);
        font-size: 24px;
        font-weight: 600;
        margin: 0;
    }

    .btn-volver {
        background-color: var(--color-primario);
        color: var(--color-blanco);
        padding: 12px 24px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-volver:hover {
        background-color: #1a252f;
        transform: translateY(-1px);
    }

    .grid-detalle {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 25px;
    }

    .card {
        background: var(--color-blanco);
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }

    .card h2 {
        color: var(--color-primario);
        font-size: 18px;
        font-weight: 600;
        margin: 0 0 20px 0;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--color-fondo);
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid var(--color-fondo);
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: var(--color-texto-claro);
        font-size: 14px;
    }

    .info-valor {
        font-weight: 600;
        color: var(--color-primario);
        font-size: 14px;
    }

    .info-valor.destacado {
        color: var(--color-acento);
        font-size: 20px;
    }

    .badge-estado {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .badge-pendiente {
        background-color: #FFF3CD;
        color: #856404;
    }

    .badge-completado {
        background-color: #D5F4E6;
        color: #0F5132;
    }

    .productos-lista {
        margin-top: 20px;
    }

    .producto-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        background: var(--color-fondo);
        border-radius: 6px;
        margin-bottom: 10px;
    }

    .producto-nombre {
        font-weight: 600;
        color: var(--color-primario);
    }

    .producto-cantidad {
        color: var(--color-texto-claro);
        font-size: 14px;
    }

    .producto-precio {
        font-weight: 700;
        color: var(--color-acento);
        font-size: 16px;
    }

    @media (max-width: 768px) {
        .grid-detalle {
            grid-template-columns: 1fr;
        }

        .header-detalle {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<!-- Header -->
<div class="header-detalle">
    <div>
        <h1>Pedido <?php echo htmlspecialchars($pedido['NumeroPedido']); ?></h1>
        <p style="color: var(--color-texto-claro); margin: 5px 0 0 0;">
            Realizado el <?php echo date('d/m/Y H:i', strtotime($pedido['FechaPedido'])); ?>
        </p>
    </div>
    <a href="<?php echo BASE_URL; ?>view/cliente/mis_pedidos.php" class="btn-volver">Volver a Mis Pedidos</a>
</div>

<!-- Contenido -->
<div class="grid-detalle">
    <!-- Columna izquierda: Productos -->
    <div>
        <div class="card">
            <h2>Productos del Pedido</h2>
            <div class="productos-lista">
                <?php if (!empty($pedido['detalles'])): ?>
                    <?php foreach ($pedido['detalles'] as $detalle): ?>
                        <div class="producto-item">
                            <div>
                                <div class="producto-nombre">
                                    <?php echo htmlspecialchars($detalle['NombreProducto']); ?>
                                </div>
                                <div class="producto-cantidad">
                                    Cantidad: <?php echo $detalle['Cantidad']; ?> × $<?php echo number_format($detalle['PrecioUnitario'], 2); ?>
                                </div>
                            </div>
                            <div class="producto-precio">
                                $<?php echo number_format($detalle['Subtotal'], 2); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--color-texto-claro); text-align: center; padding: 20px;">
                        No se encontraron productos
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Dirección de envío (si aplica) -->
        <?php if (!empty($pedido['DireccionEnvioSnapshot'])): ?>
        <div class="card" style="margin-top: 25px;">
            <h2>Información de Envío</h2>
            <div class="info-item">
                <span class="info-label">Dirección:</span>
                <span class="info-valor"><?php echo htmlspecialchars($pedido['DireccionEnvioSnapshot']); ?></span>
            </div>
            <?php if (!empty($pedido['FechaEntregaEstimada'])): ?>
            <div class="info-item">
                <span class="info-label">Entrega estimada:</span>
                <span class="info-valor"><?php echo date('d/m/Y', strtotime($pedido['FechaEntregaEstimada'])); ?></span>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Columna derecha: Resumen -->
    <div>
        <div class="card">
            <h2>Resumen del Pedido</h2>
            
            <div class="info-item">
                <span class="info-label">Estado:</span>
                <span class="info-valor">
                    <span class="badge-estado badge-<?php echo strtolower($pedido['EstadoPedido']); ?>">
                        <?php echo htmlspecialchars($pedido['EstadoPedido']); ?>
                    </span>
                </span>
            </div>

            <div class="info-item">
                <span class="info-label">Método de Pago:</span>
                <span class="info-valor"><?php echo htmlspecialchars($pedido['MetodoPago'] ?? 'N/A'); ?></span>
            </div>

            <div class="info-item">
                <span class="info-label">Tipo de Entrega:</span>
                <span class="info-valor"><?php echo htmlspecialchars($pedido['TipoEntrega'] ?? 'N/A'); ?></span>
            </div>

            <div class="info-item" style="margin-top: 20px; padding-top: 20px; border-top: 2px solid var(--color-fondo);">
                <span class="info-label" style="font-size: 16px;">Subtotal:</span>
                <span class="info-valor">$<?php echo number_format($pedido['Subtotal'] ?? $pedido['Total'], 2); ?></span>
            </div>

            <?php if (!empty($pedido['IVA'])): ?>
            <div class="info-item">
                <span class="info-label">IVA (16%):</span>
                <span class="info-valor">$<?php echo number_format($pedido['IVA'], 2); ?></span>
            </div>
            <?php endif; ?>

            <div class="info-item" style="padding-top: 15px; border-top: 2px solid var(--color-primario);">
                <span class="info-label" style="font-size: 18px; color: var(--color-primario);">TOTAL:</span>
                <span class="info-valor destacado">$<?php echo number_format($pedido['Total'], 2); ?></span>
            </div>
        </div>
    </div>
</div>

</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>