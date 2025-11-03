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
    /* ===================================
       DISEÑO PROFESIONAL UNIFICADO
       =================================== */
    :root {
        --color-primario: #2C3E50;
        --color-secundario: #34495E;
        --color-acento: #FF6347;
        --color-exito: #27AE60;
        --color-advertencia: #F39C12;
        --color-peligro: #E74C3C;
        --color-info: #3498DB;
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

    /* Encabezado */
    .header-pedidos {
        background: var(--color-blanco);
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 25px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .header-pedidos h1 {
        color: var(--color-primario);
        font-size: 28px;
        font-weight: 600;
        margin: 0;
    }

    .header-pedidos p {
        color: var(--color-texto-claro);
        font-size: 14px;
        margin: 5px 0 0 0;
    }

    .total-pedidos {
        background: var(--color-acento);
        color: var(--color-blanco);
        padding: 12px 24px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 16px;
    }

    /* Mensajes */
    .mensaje {
        padding: 15px 20px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-weight: 500;
        border-left: 4px solid;
    }

    .mensaje-exito {
        background-color: #D5F4E6;
        color: #0F5132;
        border-color: var(--color-exito);
    }

    .mensaje-error {
        background-color: #F8D7DA;
        color: #842029;
        border-color: var(--color-peligro);
    }

    /* Filtros */
    .filtros-container {
        background: var(--color-blanco);
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }

    .filtros-container label {
        font-weight: 600;
        color: var(--color-primario);
        margin-right: 15px;
        font-size: 15px;
    }

    .filtros-container select {
        padding: 10px 15px;
        border: 2px solid var(--color-borde);
        border-radius: 6px;
        font-size: 14px;
        color: var(--color-texto);
        background: var(--color-blanco);
        cursor: pointer;
        transition: border-color 0.3s;
        min-width: 200px;
    }

    .filtros-container select:focus {
        outline: none;
        border-color: var(--color-acento);
    }

    /* Listado de pedidos */
    .pedidos-lista {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .pedido-item {
        background: var(--color-blanco);
        border-radius: 8px;
        padding: 25px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        border-left: 4px solid var(--color-acento);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .pedido-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }

    .pedido-contenido {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
        gap: 25px;
        align-items: center;
    }

    .pedido-principal {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .pedido-numero {
        font-size: 18px;
        font-weight: 700;
        color: var(--color-acento);
        margin: 0;
    }

    .pedido-fecha {
        font-size: 14px;
        color: var(--color-texto-claro);
    }

    .pedido-info {
        text-align: center;
    }

    .pedido-info-label {
        display: block;
        font-size: 11px;
        text-transform: uppercase;
        color: var(--color-texto-claro);
        font-weight: 600;
        margin-bottom: 5px;
        letter-spacing: 0.5px;
    }

    .pedido-info-valor {
        display: block;
        font-size: 16px;
        font-weight: 600;
        color: var(--color-primario);
    }

    .pedido-total {
        font-size: 24px !important;
        color: var(--color-acento) !important;
    }

    .pedido-estado {
        text-align: center;
    }

    .badge-estado {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-pendiente {
        background-color: #FFF3CD;
        color: #856404;
    }

    .badge-proceso {
        background-color: #D1ECF1;
        color: #0C5460;
    }

    .badge-enviado {
        background-color: #E2D9F3;
        color: #5A1E8E;
    }

    .badge-completado {
        background-color: #D5F4E6;
        color: #0F5132;
    }

    .badge-cancelado {
        background-color: #F8D7DA;
        color: #842029;
    }

    .pedido-acciones {
        display: flex;
        gap: 10px;
        flex-direction: column;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        text-align: center;
        cursor: pointer;
        border: none;
        transition: all 0.3s;
        white-space: nowrap;
    }

    .btn-ver {
        background-color: var(--color-primario);
        color: var(--color-blanco);
    }

    .btn-ver:hover {
        background-color: var(--color-secundario);
        transform: translateY(-1px);
    }

    .btn-cancelar {
        background-color: var(--color-peligro);
        color: var(--color-blanco);
    }

    .btn-cancelar:hover {
        background-color: #C0392B;
        transform: translateY(-1px);
    }

    /* Sin pedidos */
    .sin-pedidos {
        background: var(--color-blanco);
        border-radius: 8px;
        padding: 80px 40px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }

    .sin-pedidos h2 {
        color: var(--color-primario);
        font-size: 24px;
        margin-bottom: 15px;
    }

    .sin-pedidos p {
        color: var(--color-texto-claro);
        font-size: 16px;
        margin-bottom: 30px;
    }

    .btn-productos {
        background-color: var(--color-acento);
        color: var(--color-blanco);
        padding: 14px 32px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        font-size: 16px;
        display: inline-block;
        transition: all 0.3s;
    }

    .btn-productos:hover {
        background-color: #E5533D;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 99, 71, 0.3);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-pedidos {
            flex-direction: column;
            text-align: center;
        }

        .pedido-contenido {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .pedido-info,
        .pedido-estado {
            text-align: left;
        }

        .pedido-acciones {
            flex-direction: row;
        }

        .filtros-container select {
            width: 100%;
        }
    }
</style>

<!-- Mensajes -->
<?php if (isset($_SESSION['exito'])): ?>
    <div class="mensaje mensaje-exito">
        <?php echo htmlspecialchars($_SESSION['exito']); unset($_SESSION['exito']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="mensaje mensaje-error">
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!-- Header -->
<div class="header-pedidos">
    <div>
        <h1>Mis Pedidos</h1>
        <p>Historial completo de todos tus pedidos realizados en Papelink</p>
    </div>
    <div class="total-pedidos">
        Total de pedidos: <?php echo count($pedidos); ?>
    </div>
</div>
<!-- Filtros -->
<div class="filtros-container">
    <label>Filtrar por estado:</label>
    <select onchange="window.location.href='mis_pedidos.php?estado=' + this.value">
        <option value="">Todos los estados</option>
        <option value="Pendiente" <?php echo (isset($_GET['estado']) && $_GET['estado'] === 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
        <option value="En Proceso" <?php echo (isset($_GET['estado']) && $_GET['estado'] === 'En Proceso') ? 'selected' : ''; ?>>En Proceso</option>
        <option value="Enviado" <?php echo (isset($_GET['estado']) && $_GET['estado'] === 'Enviado') ? 'selected' : ''; ?>>Enviado</option>
        <option value="Completado" <?php echo (isset($_GET['estado']) && $_GET['estado'] === 'Completado') ? 'selected' : ''; ?>>Completado</option>
        <option value="Cancelado" <?php echo (isset($_GET['estado']) && $_GET['estado'] === 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
    </select>
</div>

<?php if (empty($pedidos)): ?>
    <!-- Sin pedidos -->
    <div class="sin-pedidos">
        <h2>Aún no tienes pedidos</h2>
        <p>Comienza a comprar en nuestra tienda y tus pedidos aparecerán aquí</p>
        <a href="productos.php" class="btn-productos">Ver Productos</a>
    </div>
<?php else: ?>
    <!-- Listado de pedidos -->
    <div class="pedidos-lista">
        <?php foreach ($pedidos as $pedido): ?>
            <div class="pedido-item">
                <div class="pedido-contenido">
                    <!-- Información principal -->
                    <div class="pedido-principal">
                        <h3 class="pedido-numero"><?php echo htmlspecialchars($pedido['NumeroPedido']); ?></h3>
                        <span class="pedido-fecha">
                            Fecha: <?php echo date('d/m/Y H:i', strtotime($pedido['FechaPedido'])); ?>
                        </span>
                        <?php if (!empty($pedido['MetodoPago'])): ?>
                        <span class="pedido-fecha">
                            Método de pago: <?php echo htmlspecialchars($pedido['MetodoPago']); ?>
                        </span>
                        <?php endif; ?>
                    </div>

                    <!-- Información de productos -->
                    <div class="pedido-info">
                        <span class="pedido-info-label">Productos</span>
                        <span class="pedido-info-valor"><?php echo $pedido['TotalProductos']; ?> item<?php echo $pedido['TotalProductos'] != 1 ? 's' : ''; ?></span>
                    </div>

                    <!-- Total -->
                    <div class="pedido-info">
                        <span class="pedido-info-label">Total</span>
                        <span class="pedido-info-valor pedido-total">$<?php echo number_format($pedido['Total'], 2); ?></span>
                    </div>

                    <!-- Estado y acciones -->
                    <div style="display: flex; flex-direction: column; gap: 15px; align-items: flex-end;">
                        <div class="pedido-estado">
                            <?php
                            $estadoClase = '';
                            switch ($pedido['EstadoPedido']) {
                                case 'Pendiente':
                                    $estadoClase = 'badge-pendiente';
                                    break;
                                case 'En Proceso':
                                    $estadoClase = 'badge-proceso';
                                    break;
                                case 'Enviado':
                                    $estadoClase = 'badge-enviado';
                                    break;
                                case 'Completado':
                                    $estadoClase = 'badge-completado';
                                    break;
                                case 'Cancelado':
                                    $estadoClase = 'badge-cancelado';
                                    break;
                                default:
                                    $estadoClase = 'badge-pendiente';
                            }
                            ?>
                            <span class="badge-estado <?php echo $estadoClase; ?>">
                                <?php echo htmlspecialchars($pedido['EstadoPedido']); ?>
                            </span>
                        </div>

                        <div class="pedido-acciones">
                            <!-- ✅ CORRECTO -->
                            <a href="<?php echo BASE_URL; ?>view/cliente/pedido_detalle.php?id=<?php echo $pedido['IdPedido']; ?>" 
                            class="btn btn-ver">
                                Ver Detalle
                            </a>
                            
                            <?php if (in_array($pedido['EstadoPedido'], ['Pendiente', 'En Proceso'])): ?>
                            <button onclick="confirmarCancelacion(<?php echo $pedido['IdPedido']; ?>, '<?php echo htmlspecialchars($pedido['NumeroPedido']); ?>')" 
                                    class="btn btn-cancelar">
                                Cancelar
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Información adicional (si hay envío) -->
                <?php if (!empty($pedido['EstadoEnvio']) || !empty($pedido['FechaEntregaEstimada'])): ?>
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ECF0F1; display: flex; gap: 30px; font-size: 13px; color: #7F8C8D;">
                    <?php if (!empty($pedido['EstadoEnvio'])): ?>
                    <span>
                        <strong>Estado de envío:</strong> <?php echo htmlspecialchars($pedido['EstadoEnvio']); ?>
                    </span>
                    <?php endif; ?>
                    
                    <?php if (!empty($pedido['FechaEntregaEstimada']) && !in_array($pedido['EstadoPedido'], ['Completado', 'Cancelado'])): ?>
                    <span>
                        <strong>Entrega estimada:</strong> <?php echo date('d/m/Y', strtotime($pedido['FechaEntregaEstimada'])); ?>
                    </span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
/**
 * Confirmar cancelación de pedido
 */
function confirmarCancelacion(idPedido, numeroPedido) {
    if (confirm('¿Está seguro que desea cancelar el pedido ' + numeroPedido + '?\n\nEsta acción no se puede deshacer.')) {
        window.location.href = '<?php echo BASE_URL; ?>controllers/PedidoController.php?action=cancelar&id=' + idPedido + '&confirmar=si';
    }
}

/**
 * Filtrado en tiempo real (opcional)
 */
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const estadoFiltro = urlParams.get('estado');
    
    if (estadoFiltro && estadoFiltro !== '') {
        const items = document.querySelectorAll('.pedido-item');
        let visibles = 0;
        
        items.forEach(item => {
            const badge = item.querySelector('.badge-estado');
            if (badge && badge.textContent.trim().toLowerCase() !== estadoFiltro.toLowerCase()) {
                item.style.display = 'none';
            } else {
                visibles++;
            }
        });
        
        // Actualizar contador
        const totalElement = document.querySelector('.total-pedidos');
        if (totalElement) {
            totalElement.textContent = 'Pedidos filtrados: ' + visibles;
        }
    }
});
</script>

</div>
<?php include_once __DIR__ . '/includes/footer.php'; ?>
