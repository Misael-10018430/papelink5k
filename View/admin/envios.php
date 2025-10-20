<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/EnvioController.php';

// Verificar que sea admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'empleado') {
    $_SESSION['error'] = 'Acceso denegado';
    header('Location: login.php');
    exit;
}

$envioController = new EnvioController();
$envios = $envioController->listar();

$titulo = "Gestión de Envíos";
include __DIR__ . '/includes/header.php';
?>

<h1 class="titulo-pagina">Gestión de Envíos</h1>

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

<!-- Estadísticas -->
<div class="grid grid-4" style="margin-bottom: 30px;">
    <div class="tarjeta-metrica">
        <h3><?php echo count($envios); ?></h3>
        <p>Total Envíos</p>
    </div>
    <div class="tarjeta-metrica">
        <h3><?php echo count(array_filter($envios, fn($e) => $e['EstadoEnvio'] === 'Pendiente')); ?></h3>
        <p>Pendientes</p>
    </div>
    <div class="tarjeta-metrica">
        <h3><?php echo count(array_filter($envios, fn($e) => $e['EstadoEnvio'] === 'En Tránsito')); ?></h3>
        <p>En Tránsito</p>
    </div>
    <div class="tarjeta-metrica">
        <h3><?php echo count(array_filter($envios, fn($e) => isset($e['DiasParaEntrega']) && $e['DiasParaEntrega'] <= 2)); ?></h3>
        <p>Urgentes (≤2 días)</p>
    </div>
</div>

<!-- Tabla de Envíos -->
<?php if (empty($envios)): ?>
    <div class="tarjeta" style="text-align: center; padding: 60px;">
        <h2 style="color: #7f8c8d; font-weight: normal;">No hay envíos pendientes</h2>
        <p style="color: #95a5a6; margin-top: 10px;">Todos los envíos han sido completados o no hay pedidos con envío</p>
    </div>
<?php else: ?>
    <table class="tabla">
        <thead>
            <tr>
                <th>Pedido</th>
                <th>Cliente</th>
                <th>Dirección</th>
                <th>Fecha Estimada</th>
                <th>Días Restantes</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($envios as $envio): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($envio['NumeroPedido']); ?></strong><br>
                        <small style="color: #7f8c8d;">ID: <?php echo $envio['IdEnvio']; ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($envio['NombreCliente']); ?></td>
                    <td>
                        <small style="color: #7f8c8d;">
                            <?php echo htmlspecialchars(substr($envio['DireccionEnvio'], 0, 40)); ?>...
                        </small>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($envio['FechaEntregaEstimada'])); ?></td>
                    <td>
                        <?php
                        $dias = $envio['DiasParaEntrega'] ?? 0;
                        $textoUrgencia = $dias < 0 ? 'ATRASADO' : ($dias == 0 ? 'HOY' : $dias . ' días');
                        $badgeUrgencia = $dias <= 2 ? 'badge-rojo' : 'badge-azul';
                        ?>
                        <span class="badge <?php echo $badgeUrgencia; ?>">
                            <?php echo $textoUrgencia; ?>
                        </span>
                    </td>
                    <td>
                        <?php
                        $badgeClass = '';
                        switch ($envio['EstadoEnvio']) {
                            case 'Pendiente': $badgeClass = 'badge-amarillo'; break;
                            case 'En Tránsito': $badgeClass = 'badge-azul'; break;
                            case 'Entregado': $badgeClass = 'badge-verde'; break;
                            case 'Cancelado': $badgeClass = 'badge-rojo'; break;
                        }
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>">
                            <?php echo htmlspecialchars($envio['EstadoEnvio']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="envio_detalle.php?id=<?php echo $envio['IdEnvio']; ?>" class="btn btn-blanco">
                            Gestionar
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>