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

<div class="contenedor-principal">
    <style>
        .estadisticas-rapidas {
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
        
        .stat-card.naranja {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .stat-card.verde {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .stat-card.azul {
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
            margin-bottom: 5px;
        }
        
        .filtros-container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .filtros-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .filtro-group {
            display: flex;
            flex-direction: column;
        }
        
        .filtro-group label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        
        .filtro-group select,
        .filtro-group input {
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .btn-filtrar {
            background: #FF6347;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            align-self: flex-end;
        }
        
        .tabla-pedidos {
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
        
        .badge-proceso {
            background: #2196F3;
            color: white;
        }
        
        .badge-completado {
            background: #4CAF50;
            color: white;
        }
        
        .badge-cancelado {
            background: #F44336;
            color: white;
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
        
        .sin-pedidos {
            text-align: center;
            padding: 60px;
            color: #666;
        }
        
        .sin-pedidos-icono {
            font-size: 60px;
            margin-bottom: 15px;
        }
    </style>

    <!-- Título -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="color: #2C3E50;">📋 Gestión de Pedidos</h1>
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

    <!-- Estadísticas Rápidas -->
    <div class="estadisticas-rapidas">
        <div class="stat-card">
            <h3>Total Pedidos</h3>
            <div class="numero"><?php echo count($pedidos); ?></div>
        </div>
        <div class="stat-card naranja">
            <h3>Pendientes</h3>
            <div class="numero">
                <?php echo count(array_filter($pedidos, fn($p) => $p['EstadoPedido'] === 'Pendiente')); ?>
            </div>
        </div>
        <div class="stat-card verde">
            <h3>Completados</h3>
            <div class="numero">
                <?php echo count(array_filter($pedidos, fn($p) => $p['EstadoPedido'] === 'Completado')); ?>
            </div>
        </div>
        <div class="stat-card azul">
            <h3>Total Vendido</h3>
            <div class="numero">
                $<?php echo number_format(array_sum(array_column($pedidos, 'Total')), 2); ?>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filtros-container">
        <h3 style="margin: 0 0 15px 0; color: #2C3E50;">🔍 Filtros de Búsqueda</h3>
        <form method="GET" action="pedidos.php">
            <div class="filtros-grid">
                <div class="filtro-group">
                    <label>Estado:</label>
                    <select name="estado">
                        <option value="">Todos los estados</option>
                        <option value="Pendiente" <?php echo ($_GET['estado'] ?? '') === 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="En Proceso" <?php echo ($_GET['estado'] ?? '') === 'En Proceso' ? 'selected' : ''; ?>>En Proceso</option>
                        <option value="Completado" <?php echo ($_GET['estado'] ?? '') === 'Completado' ? 'selected' : ''; ?>>Completado</option>
                        <option value="Cancelado" <?php echo ($_GET['estado'] ?? '') === 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>
                
                <div class="filtro-group">
                    <label>Fecha Desde:</label>
                    <input type="date" name="fecha_desde" value="<?php echo $_GET['fecha_desde'] ?? ''; ?>">
                </div>
                
                <div class="filtro-group">
                    <label>Fecha Hasta:</label>
                    <input type="date" name="fecha_hasta" value="<?php echo $_GET['fecha_hasta'] ?? ''; ?>">
                </div>
                
                <div class="filtro-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn-filtrar">Filtrar</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabla de Pedidos -->
    <?php if (empty($pedidos)): ?>
        <div class="sin-pedidos">
            <div class="sin-pedidos-icono">📦</div>
            <h2>No hay pedidos para mostrar</h2>
            <p>Intenta ajustar los filtros de búsqueda</p>
        </div>
    <?php else: ?>
        <div class="tabla-pedidos">
            <table>
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
                                <small style="color: #666;"><?php echo htmlspecialchars($pedido['Telefono'] ?? 'N/A'); ?></small>
                            </td>
                            <td><strong style="color: #FF6347;">$<?php echo number_format($pedido['Total'], 2); ?></strong></td>
                            <td>
                                <?php
                                $badgeClass = '';
                                switch ($pedido['EstadoPedido']) {
                                    case 'Pendiente': $badgeClass = 'badge-pendiente'; break;
                                    case 'En Proceso': $badgeClass = 'badge-proceso'; break;
                                    case 'Completado': $badgeClass = 'badge-completado'; break;
                                    case 'Cancelado': $badgeClass = 'badge-cancelado'; break;
                                }
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>">
                                    <?php echo htmlspecialchars($pedido['EstadoPedido']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($pedido['TipoEntrega']); ?></td>
                            <td><?php echo $pedido['TotalProductos']; ?> items</td>
                            <td>
                                <a href="pedido_detalle.php?id=<?php echo $pedido['IdPedido']; ?>" class="btn-ver">
                                    Ver Detalle
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