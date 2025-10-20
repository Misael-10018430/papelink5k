<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/PedidoController.php';

// Verificar que sea admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'empleado') {
    $_SESSION['error'] = 'Acceso denegado';
    header('Location: login.php');
    exit;
}

$pedidoController = new PedidoController();
$pedidos = $pedidoController->listar();

$titulo = "Gestión de Pedidos";
include __DIR__ . '/includes/header.php';
?>

<h1 class="titulo-pagina">Gestión de Pedidos</h1>

<!-- Mensajes -->
<?php if (isset($_SESSION['exito'])): ?>
    <div class="mensaje-exito">
        <?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="mensaje-error">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!-- Estadísticas Rápidas -->
<div class="grid grid-4" style="margin-bottom: 30px;">
    <div class="tarjeta-metrica">
        <h3><?php echo count($pedidos); ?></h3>
        <p>Total Pedidos</p>
    </div>
    <div class="tarjeta-metrica">
        <h3><?php echo count(array_filter($pedidos, fn($p) => $p['EstadoPedido'] === 'Pendiente')); ?></h3>
        <p>Pendientes</p>
    </div>
    <div class="tarjeta-metrica">
        <h3><?php echo count(array_filter($pedidos, fn($p) => $p['EstadoPedido'] === 'Completado')); ?></h3>
        <p>Completados</p>
    </div>
    <div class="tarjeta-metrica">
        <h3>$<?php echo number_format(array_sum(array_column($pedidos, 'Total')), 2); ?></h3>
        <p>Total Vendido</p>
    </div>
</div>

<!-- Filtros -->
<div class="filtros">
    <h3 style="margin: 0 0 15px 0; color: #2C3E50; font-size: 16px;">Filtros de Búsqueda</h3>
    <form method="GET" action="pedidos.php">
        <div class="grid grid-4">
            <div class="form-group" style="margin-bottom: 0;">
                <label>Estado:</label>
                <select name="estado">
                    <option value="">Todos los estados</option>
                    <option value="Pendiente" <?php echo ($_GET['estado'] ?? '') === 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="En Proceso" <?php echo ($_GET['estado'] ?? '') === 'En Proceso' ? 'selected' : ''; ?>>En Proceso</option>
                    <option value="Completado" <?php echo ($_GET['estado'] ?? '') === 'Completado' ? 'selected' : ''; ?>>Completado</option>
                    <option value="Cancelado" <?php echo ($_GET['estado'] ?? '') === 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label>Fecha Desde:</label>
                <input type="date" name="fecha_desde" value="<?php echo $_GET['fecha_desde'] ?? ''; ?>">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label>Fecha Hasta:</label>
                <input type="date" name="fecha_hasta" value="<?php echo $_GET['fecha_hasta'] ?? ''; ?>">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-naranja" style="width: 100%;">Filtrar</button>
            </div>
        </div>
    </form>
</div>

<!-- Tabla de Pedidos -->
<?php if (empty($pedidos)): ?>
    <div class="tarjeta" style="text-align: center; padding: 60px;">
        <h2 style="color: #7f8c8d; font-weight: normal;">No hay pedidos para mostrar</h2>
        <p style="color: #95a5a6; margin-top: 10px;">Intenta ajustar los filtros de búsqueda</p>
    </div>
<?php else: ?>
    <table class="tabla">
        <thead>
            <tr>
                <th>Número</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Tipo Entrega</th>
                <th>Productos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pedidos as $pedido): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($pedido['NumeroPedido']); ?></strong></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($pedido['FechaPedido'])); ?></td>
                    <td>
                        <?php echo htmlspecialchars($pedido['NombreCliente']); ?><br>
                        <small style="color: #7f8c8d;"><?php echo htmlspecialchars($pedido['Telefono'] ?? 'N/A'); ?></small>
                    </td>
                    <td><strong style="color: #FF6347;">$<?php echo number_format($pedido['Total'], 2); ?></strong></td>
                    <td>
                        <?php
                        $badgeClass = '';
                        switch ($pedido['EstadoPedido']) {
                            case 'Pendiente': $badgeClass = 'badge-amarillo'; break;
                            case 'En Proceso': $badgeClass = 'badge-azul'; break;
                            case 'Completado': $badgeClass = 'badge-verde'; break;
                            case 'Cancelado': $badgeClass = 'badge-rojo'; break;
                        }
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>">
                            <?php echo htmlspecialchars($pedido['EstadoPedido']); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($pedido['TipoEntrega']); ?></td>
                    <td><?php echo $pedido['TotalProductos']; ?> items</td>
                    <td>
                        <a href="pedido_detalle.php?id=<?php echo $pedido['IdPedido']; ?>" class="btn btn-blanco">
                            Ver Detalle
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>