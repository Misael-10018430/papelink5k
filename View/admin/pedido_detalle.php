<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/Auth.php';
Auth::requiereFuncionalidad('PEDIDOS_VER');

$paginaActual = basename($_SERVER['PHP_SELF'], '.php');


require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/PedidoController.php';

// Verificar que sea admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'empleado') {
    $_SESSION['error'] = 'Acceso denegado';
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = 'ID de pedido no especificado';
    header('Location: pedidos.php');
    exit;
}

$pedidoController = new PedidoController();
$detalle = $pedidoController->verDetalleAdmin();

if (!$detalle) {
    $_SESSION['error'] = 'Pedido no encontrado';
    header('Location: pedidos.php');
    exit;
}

$titulo = "Detalle del Pedido";
include __DIR__ . '/includes/header.php';
?>

<div class="contenedor-principal">
    <style>
        .detalle-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
        }
        
        .detalle-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .detalle-card h2 {
            color: #2C3E50;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-item label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .info-item span {
            font-size: 15px;
            color: #333;
        }
        
        .tabla-productos {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .tabla-productos th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        
        .tabla-productos td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        
        .resumen-totales {
            text-align: right;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .resumen-totales div {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }
        
        .total-final {
            font-size: 20px;
            font-weight: bold;
            color: #FF6347;
            padding-top: 15px;
            border-top: 2px solid #dee2e6;
        }
        
        .form-estado {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        
        .form-estado select,
        .form-estado textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        
        .btn-actualizar {
            background: #FF6347;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
        }
        
        .btn-actualizar:hover {
            background: #e5533d;
        }
    </style>

    <!-- Encabezado -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h1 style="color: #2C3E50; margin-bottom: 5px;">Pedido #<?php echo htmlspecialchars($detalle['NumeroPedido']); ?></h1>
            <p style="color: #666;">Fecha: <?php echo date('d/m/Y H:i', strtotime($detalle['FechaPedido'])); ?></p>
        </div>
        <a href="pedidos.php" class="btn-ver" style="background: #2C3E50; color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px;">
            ← Volver a Pedidos
        </a>
    </div>

    <!-- Mensajes -->
    <?php if (isset($_SESSION['exito'])): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            ✓ <?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?>
        </div>
    <?php endif; ?>

    <!-- Contenido Principal -->
    <div class="detalle-container">
        <!-- Columna Izquierda -->
        <div>
            <!-- Información del Cliente -->
            <div class="detalle-card">
                <h2>Información del Cliente</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Nombre:</label>
                        <span><?php echo htmlspecialchars($detalle['NombreClienteSnapshot']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Teléfono:</label>
                        <span><?php echo htmlspecialchars($detalle['TelefonoSnapshot']); ?></span>
                    </div>
                    <div class="info-item" style="grid-column: 1 / -1;">
                        <label>Email:</label>
                        <span><?php echo htmlspecialchars($detalle['EmailSnapshot']); ?></span>
                    </div>
                    <div class="info-item" style="grid-column: 1 / -1;">
                        <label>Dirección de Entrega:</label>
                        <span><?php echo htmlspecialchars($detalle['DireccionSnapshot']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Productos del Pedido -->
            <div class="detalle-card" style="margin-top: 25px;">
                <h2>Productos del Pedido</h2>
                <table class="tabla-productos">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Código</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalle['detalles'] as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['NombreProducto']); ?></td>
                                <td><?php echo htmlspecialchars($item['CodigoProducto']); ?></td>
                                <td><?php echo $item['Cantidad']; ?></td>
                                <td>$<?php echo number_format($item['PrecioUnitario'], 2); ?></td>
                                <td><strong>$<?php echo number_format($item['Subtotal'], 2); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="resumen-totales">
                    <div>
                        <span>Subtotal:</span>
                        <span>$<?php echo number_format($detalle['Total'] - $detalle['IVA'] - $detalle['CostoEnvio'], 2); ?></span>
                    </div>
                    <div>
                        <span>IVA (16%):</span>
                        <span>$<?php echo number_format($detalle['IVA'], 2); ?></span>
                    </div>
                    <div>
                        <span>Costo de Envío:</span>
                        <span>$<?php echo number_format($detalle['CostoEnvio'], 2); ?></span>
                    </div>
                    <div class="total-final">
                        <span>TOTAL:</span>
                        <span>$<?php echo number_format($detalle['Total'], 2); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha -->
        <div>
            <!-- Estado del Pedido -->
            <div class="detalle-card">
                <h2>Gestionar Estado</h2>
                <form method="POST" action="<?php echo BASE_URL; ?>controllers/PedidoController.php?action=cambiar_estado" class="form-estado">
                    <input type="hidden" name="id_pedido" value="<?php echo $detalle['IdPedido']; ?>">
                    
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Estado Actual:</label>
                    <div style="background: white; padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: center;">
                        <?php
                        $badgeClass = '';
                        switch ($detalle['EstadoPedido']) {
                            case 'Pendiente': $badgeClass = 'badge-pendiente'; break;
                            case 'En Proceso': $badgeClass = 'badge-proceso'; break;
                            case 'Completado': $badgeClass = 'badge-completado'; break;
                            case 'Cancelado': $badgeClass = 'badge-cancelado'; break;
                        }
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>" style="font-size: 14px; padding: 8px 16px;">
                            <?php echo htmlspecialchars($detalle['EstadoPedido']); ?>
                        </span>
                    </div>

                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Cambiar a:</label>
                    <select name="estado" required>
                        <option value="">Seleccionar estado...</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="En Proceso">En Proceso</option>
                        <option value="Completado">Completado</option>
                        <option value="Cancelado">Cancelado</option>
                    </select>

                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Notas Internas:</label>
                    <textarea name="notas_internas" rows="3" placeholder="Agregar notas sobre el cambio..."><?php echo htmlspecialchars($detalle['NotasInternas'] ?? ''); ?></textarea>

                    <input type="hidden" name="redirigir" value="detalle">
                    <button type="submit" class="btn-actualizar">Actualizar Estado</button>
                </form>
            </div>

            <!-- Información Adicional -->
            <div class="detalle-card" style="margin-top: 25px;">
                <h2>Información Adicional</h2>
                <div class="info-item" style="margin-bottom: 15px;">
                    <label>Método de Pago:</label>
                    <span><?php echo htmlspecialchars($detalle['MetodoPago']); ?></span>
                </div>
                <div class="info-item" style="margin-bottom: 15px;">
                    <label>Tipo de Entrega:</label>
                    <span><?php echo htmlspecialchars($detalle['TipoEntrega']); ?></span>
                </div>
                <?php if ($detalle['NotasCliente']): ?>
                <div class="info-item">
                    <label>Notas del Cliente:</label>
                    <span style="font-style: italic; color: #666;"><?php echo htmlspecialchars($detalle['NotasCliente']); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>