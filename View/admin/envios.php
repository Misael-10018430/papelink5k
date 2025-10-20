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

<div class="contenedor-principal">
    <style>
        .estadisticas-envios {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .stat-card.pendiente {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .stat-card.transito {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .stat-card.entregado {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        
        .stat-card h3 {
            font-size: 14px;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        
        .stat-card .numero {
            font-size: 36px;
            font-weight: bold;
        }
        
        .tabla-envios {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: #2C3E50;
            color: white;
        }
        
        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        tbody tr {
            border-bottom: 1px solid #eee;
            transition: background 0.3s;
        }
        
        tbody tr:hover {
            background: #f8f9fa;
        }
        
        td {
            padding: 15px;
        }
        
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-pendiente {
            background: #FFC107;
            color: #000;
        }
        
        .badge-transito {
            background: #2196F3;
            color: white;
        }
        
        .badge-entregado {
            background: #4CAF50;
            color: white;
        }
        
        .badge-cancelado {
            background: #F44336;
            color: white;
        }
        
        .dias-restantes {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .dias-restantes.urgente {
            background: #ffebee;
            color: #c62828;
        }
        
        .dias-restantes.normal {
            background: #e3f2fd;
            color: #1565c0;
        }
        
        .btn-ver {
            background: #2C3E50;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            transition: background 0.3s;
        }
        
        .btn-ver:hover {
            background: #1a252f;
        }
        
        .sin-envios {
            text-align: center;
            padding: 60px;
            color: #666;
        }
        
        .sin-envios-icono {
            font-size: 60px;
            margin-bottom: 15px;
        }
    </style>

    <!-- Título -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="color: #2C3E50;">🚚 Gestión de Envíos</h1>
        <a href="dashboard.php" class="btn-ver">← Volver al Dashboard</a>
    </div>

    <!-- Mensajes -->
    <?php if (isset($_SESSION['exito'])): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            ✓ <?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            ✕ <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Estadísticas -->
    <div class="estadisticas-envios">
        <div class="stat-card">
            <h3>Total Envíos</h3>
            <div class="numero"><?php echo count($envios); ?></div>
        </div>
        <div class="stat-card pendiente">
            <h3>Pendientes</h3>
            <div class="numero">
                <?php echo count(array_filter($envios, fn($e) => $e['EstadoEnvio'] === 'Pendiente')); ?>
            </div>
        </div>
        <div class="stat-card transito">
            <h3>En Tránsito</h3>
            <div class="numero">
                <?php echo count(array_filter($envios, fn($e) => $e['EstadoEnvio'] === 'En Tránsito')); ?>
            </div>
        </div>
        <div class="stat-card entregado">
            <h3>Urgentes (≤2 días)</h3>
            <div class="numero">
                <?php echo count(array_filter($envios, fn($e) => $e['DiasParaEntrega'] <= 2)); ?>
            </div>
        </div>
    </div>

    <!-- Tabla de Envíos -->
    <?php if (empty($envios)): ?>
        <div class="sin-envios">
            <h2>No hay envíos pendientes</h2>
            <p>Todos los envíos han sido completados o no hay pedidos con envío</p>
        </div>
    <?php else: ?>
        <div class="tabla-envios">
            <table>
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
                                <small style="color: #666;">ID: <?php echo $envio['IdEnvio']; ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($envio['NombreCliente']); ?></td>
                            <td>
                                <small style="color: #666;">
                                    <?php echo htmlspecialchars(substr($envio['DireccionEnvio'], 0, 40)); ?>...
                                </small>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($envio['FechaEntregaEstimada'])); ?></td>
                            <td>
                                <?php
                                $dias = $envio['DiasParaEntrega'];
                                $claseUrgencia = $dias <= 2 ? 'urgente' : 'normal';
                                $textoUrgencia = $dias < 0 ? 'ATRASADO' : ($dias == 0 ? 'HOY' : $dias . ' días');
                                ?>
                                <span class="dias-restantes <?php echo $claseUrgencia; ?>">
                                    <?php echo $textoUrgencia; ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $badgeClass = '';
                                switch ($envio['EstadoEnvio']) {
                                    case 'Pendiente': $badgeClass = 'badge-pendiente'; break;
                                    case 'En Tránsito': $badgeClass = 'badge-transito'; break;
                                    case 'Entregado': $badgeClass = 'badge-entregado'; break;
                                    case 'Cancelado': $badgeClass = 'badge-cancelado'; break;
                                }
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>">
                                    <?php echo htmlspecialchars($envio['EstadoEnvio']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="envio_detalle.php?id=<?php echo $envio['IdEnvio']; ?>" class="btn-ver">
                                    Gestionar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>