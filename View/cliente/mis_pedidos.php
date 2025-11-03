<?php
/**
 * Vista: Mis Pedidos del Cliente
 */

// ORDEN CORRECTO
require_once __DIR__ . '/../../config/config.php';

// Verificar que el cliente esté logueado
if (!isset($_SESSION['cliente_id'])) {
    $_SESSION['error'] = 'Debe iniciar sesión para ver sus pedidos';
    header('Location: ' . BASE_URL . 'view/cliente/login.php');
    exit;
}

require_once __DIR__ . '/../../controllers/PedidoController.php';

// Obtener pedidos del cliente
$pedidoController = new PedidoController();
$pedidos = $pedidoController->misPedidos();



// Incluir header
$titulo = "Mis Pedidos - Papelink";
include_once __DIR__ . '/includes/header.php';
?>
<div class="contenedor-principal">
<style>
    .titulo-seccion {
        background-color: white;
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .titulo-seccion h1 {
        color: #2C3E50;
        margin-bottom: 10px;
    }
    
    .titulo-seccion p {
        color: #666;
        font-size: 14px;
    }
    
    .pedidos-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .pedido-card {
        background-color: white;
        border-radius: 8px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .pedido-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
    
    .pedido-header {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
        margin-bottom: 20px;
    }
    
    .pedido-info-item {
        display: flex;
        flex-direction: column;
    }
    
    .pedido-info-item label {
        font-size: 12px;
        color: #666;
        margin-bottom: 5px;
        text-transform: uppercase;
        font-weight: bold;
    }
    
    .pedido-info-item span {
        font-size: 16px;
        color: #333;
        font-weight: 500;
    }
    
    .numero-pedido {
        color: #FF6347 !important;
        font-weight: bold !important;
        font-size: 18px !important;
    }
    
    .estado-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: bold;
        text-transform: uppercase;
    }
    
    .estado-pendiente {
        background-color: #FFC107;
        color: #333;
    }
    
    .estado-proceso {
        background-color: #2196F3;
        color: white;
    }
    
    .estado-enviado {
        background-color: #9C27B0;
        color: white;
    }
    
    .estado-completado {
        background-color: #27AE60;
        color: white;
    }
    
    .estado-cancelado {
        background-color: #E74C3C;
        color: white;
    }
    
    .pedido-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    
    .pedido-resumen {
        display: flex;
        gap: 30px;
        align-items: center;
    }
    
    .pedido-resumen-item {
        text-align: center;
    }
    
    .pedido-resumen-item .label {
        font-size: 12px;
        color: #666;
        display: block;
        margin-bottom: 5px;
    }
    
    .pedido-resumen-item .valor {
        font-size: 20px;
        font-weight: bold;
        color: #333;
    }
    
    .pedido-resumen-item .valor.total {
        color: #FF6347;
        font-size: 24px;
    }
    
    .pedido-acciones {
        display: flex;
        gap: 10px;
    }
    
    .btn-detalle {
        background-color: #2C3E50;
        color: white;
        padding: 12px 25px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: bold;
        transition: background-color 0.3s;
        border: none;
        cursor: pointer;
    }
    
    .btn-detalle:hover {
        background-color: #1a252f;
    }
    
    .btn-cancelar {
        background-color: #E74C3C;
        color: white;
        padding: 12px 25px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: bold;
        transition: background-color 0.3s;
        border: none;
        cursor: pointer;
    }
    
    .btn-cancelar:hover {
        background-color: #c0392b;
    }
    
    .sin-pedidos {
        background-color: white;
        border-radius: 8px;
        padding: 60px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .sin-pedidos-icono {
        font-size: 80px;
        margin-bottom: 20px;
    }
    
    .sin-pedidos h2 {
        color: #333;
        margin-bottom: 15px;
    }
    
    .sin-pedidos p {
        color: #666;
        margin-bottom: 30px;
        font-size: 16px;
    }
    
    .filtros-pedidos {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .filtros-pedidos label {
        font-weight: bold;
        color: #333;
    }
    
    .filtros-pedidos select {
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
    }
    
    .alerta-info {
        background-color: #E3F2FD;
        border-left: 4px solid #2196F3;
        padding: 15px 20px;
        border-radius: 4px;
        margin-bottom: 20px;
        color: #1565C0;
    }
    
    @media (max-width: 768px) {
        .pedido-header {
            grid-template-columns: 1fr;
        }
        
        .pedido-body {
            flex-direction: column;
            align-items: stretch;
        }
        
        .pedido-resumen {
            flex-direction: column;
            gap: 15px;
        }
        
        .pedido-acciones {
            flex-direction: column;
        }
    }
</style>

<!-- Mensajes de sesión -->
<?php if (isset($_SESSION['exito'])): ?>
    <div style="background-color: #27AE60; color: white; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
        ✅ <?php echo $_SESSION['exito']; ?>
    </div>
    <?php unset($_SESSION['exito']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div style="background-color: #E74C3C; color: white; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
        ❌ <?php echo $_SESSION['error']; ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!-- Título de la sección -->
<div class="titulo-seccion">
    <h1>Mis Pedidos</h1>
    <p>Historial completo de todos tus pedidos realizados en Papelink</p>
</div>

<!-- Filtros (opcional) -->
<div class="filtros-pedidos">
    <label>Filtrar por estado:</label>
    <select onchange="window.location.href='mis_pedidos.php?estado=' + this.value">
        <option value="">Todos los estados</option>
        <option value="Pendiente" <?php echo (isset($_GET['estado']) && $_GET['estado'] === 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
        <option value="En Proceso" <?php echo (isset($_GET['estado']) && $_GET['estado'] === 'En Proceso') ? 'selected' : ''; ?>>En Proceso</option>
        <option value="Enviado" <?php echo (isset($_GET['estado']) && $_GET['estado'] === 'Enviado') ? 'selected' : ''; ?>>Enviado</option>
        <option value="Completado" <?php echo (isset($_GET['estado']) && $_GET['estado'] === 'Completado') ? 'selected' : ''; ?>>Completado</option>
        <option value="Cancelado" <?php echo (isset($_GET['estado']) && $_GET['estado'] === 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
    </select>
    
    <span style="margin-left: auto; color: #666;">
        Total de pedidos: <strong><?php echo count($pedidos); ?></strong>
    </span>
</div>

<?php if (empty($pedidos)): ?>
    <!-- Sin pedidos -->
    <div class="sin-pedidos">
        <h2>Aún no tienes pedidos</h2>
        <p>Comienza a comprar en nuestra tienda y tus pedidos aparecerán aquí</p>
        <a href="productos.php" class="btn btn-naranja">Ver productos</a>
    </div>
<?php else: ?>
    <!-- Listado de pedidos -->
    <div class="pedidos-container">
        <?php foreach ($pedidos as $pedido): ?>
            <div class="pedido-card">
                <!-- Header del pedido -->
                <div class="pedido-header">
                    <div class="pedido-info-item">
                        <label>Número de Pedido</label>
                        <span class="numero-pedido"><?php echo htmlspecialchars($pedido['NumeroPedido']); ?></span>
                    </div>
                    
                    <div class="pedido-info-item">
                        <label>Fecha</label>
                        <span><?php echo date('d/m/Y', strtotime($pedido['FechaPedido'])); ?></span>
                    </div>
                    
                    <div class="pedido-info-item">
                        <label>Estado del Pedido</label>
                        <span>
                            <?php
                            $estadoClase = '';
                            switch ($pedido['EstadoPedido']) {
                                case 'Pendiente':
                                    $estadoClase = 'estado-pendiente';
                                    break;
                                case 'En Proceso':
                                    $estadoClase = 'estado-proceso';
                                    break;
                                case 'Enviado':
                                    $estadoClase = 'estado-enviado';
                                    break;
                                case 'Completado':
                                    $estadoClase = 'estado-completado';
                                    break;
                                case 'Cancelado':
                                    $estadoClase = 'estado-cancelado';
                                    break;
                            }
                            ?>
                            <span class="estado-badge <?php echo $estadoClase; ?>">
                                <?php echo htmlspecialchars($pedido['EstadoPedido']); ?>
                            </span>
                        </span>
                    </div>
                    
                    <?php if (isset($pedido['EstadoEnvio'])): ?>
                    <div class="pedido-info-item">
                        <label>Estado del Envío</label>
                        <span><?php echo htmlspecialchars($pedido['EstadoEnvio']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Body del pedido -->
                <div class="pedido-body">
                    <div class="pedido-resumen">
                        <div class="pedido-resumen-item">
                            <span class="label">Productos</span>
                            <span class="valor"><?php echo $pedido['TotalProductos']; ?></span>
                        </div>
                        
                        <div class="pedido-resumen-item">
                            <span class="label">Método de Pago</span>
                            <span class="valor"><?php echo htmlspecialchars($pedido['MetodoPago'] ?? 'N/A'); ?></span>
                        </div>
                        
                        <div class="pedido-resumen-item">
                            <span class="label">Total</span>
                            <span class="valor total">$<?php echo number_format($pedido['Total'], 2); ?></span>
                        </div>
                        
                        <?php if (isset($pedido['FechaEstimadaEntrega']) && $pedido['EstadoPedido'] !== 'Completado' && $pedido['EstadoPedido'] !== 'Cancelado'): ?>
                        <div class="pedido-resumen-item">
                            <span class="label">Entrega Estimada</span>
                            <span class="valor" style="font-size: 14px;">
                                <?php echo date('d/m/Y', strtotime($pedido['FechaEstimadaEntrega'])); ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="pedido-acciones">
                        <a href="Pedidos.php?id=<?php echo $pedido['IdPedido']; ?>" class="btn-detalle">
                            Ver Detalle
                        </a>
                        
                        <?php if (in_array($pedido['EstadoPedido'], ['Pendiente', 'En Proceso'])): ?>
                        <button onclick="confirmarCancelacion(<?php echo $pedido['IdPedido']; ?>, '<?php echo htmlspecialchars($pedido['NumeroPedido']); ?>')" 
                                class="btn-cancelar">
                            Cancelar Pedido
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
function confirmarCancelacion(idPedido, numeroPedido) {
    if (confirm('¿Está seguro que desea cancelar el pedido ' + numeroPedido + '?\n\nEsta acción no se puede deshacer.')) {
        window.location.href = '<?php echo BASE_URL; ?>controllers/PedidoController.php?action=cancelar&id=' + idPedido + '&confirmar=si';
    }
}

// Filtrar pedidos en tiempo real (opcional)
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const estadoFiltro = urlParams.get('estado');
    
    if (estadoFiltro) {
        const cards = document.querySelectorAll('.pedido-card');
        cards.forEach(card => {
            const estadoBadge = card.querySelector('.estado-badge');
            if (estadoBadge && estadoBadge.textContent.trim() !== estadoFiltro) {
                card.style.display = 'none';
            }
        });
    }
});
</script>
</div>
<?php include_once __DIR__ . '/includes/footer.php'; ?>