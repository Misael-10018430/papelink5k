<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/Auth.php';
Auth::requiereFuncionalidad('PEDIDOS_VER');

 $paginaActual = basename($_SERVER['PHP_SELF'], '.php');

require_once __DIR__ . '/../../controllers/EnvioController.php';

// Verificar que sea admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'empleado') {
    $_SESSION['error'] = 'Acceso denegado';
    redirect('view/admin/login.php');
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = 'ID de envío no especificado';
    redirect('view/admin/envios.php');
    exit;
}

 $envioController = new EnvioController();
 $envio = $envioController->verDetalle();
 $estados = $envioController->obtenerEstados();

if (!$envio) {
    $_SESSION['error'] = 'Envío no encontrado';
    redirect('view/admin/envios.php');
    exit;
}

 $titulo = "Detalle del Envío";
include __DIR__ . '/includes/header.php';
?>

<div class="contenedor-principal">
    <style>
        .detalle-envio {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
        }
        
        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .card h2 {
            color: #2C3E50;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .info-item {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-item label {
            display: block;
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .info-item span {
            display: block;
            font-size: 15px;
            color: #333;
        }
        
        .form-actualizar {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        
        .form-actualizar select,
        .form-actualizar input,
        .form-actualizar textarea {
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
        
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #ddd;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -25px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #2196F3;
            border: 3px solid white;
            box-shadow: 0 0 0 2px #2196F3;
        }
        
        .timeline-item.completado::before {
            background: #4CAF50;
            box-shadow: 0 0 0 2px #4CAF50;
        }
    </style>

    <!-- Encabezado -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h1 style="color: #2C3E50; margin-bottom: 5px;">Envío #<?php echo $envio['IdEnvio']; ?></h1>
            <p style="color: #666;">Pedido: <?php echo htmlspecialchars($envio['NumeroPedido']); ?></p>
        </div>
        <a href="<?php echo BASE_URL; ?>view/admin/envios.php" class="btn-ver" style="background: #2C3E50; color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px;">
            ← Volver a Envíos
        </a>
    </div>

    <!-- Mensajes -->
    <?php if (isset($_SESSION['exito'])): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            ✓ <?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?>
        </div>
    <?php endif; ?>

    <!-- Contenido -->
    <div class="detalle-envio">
        <!-- Columna Izquierda -->
        <div>
            <!-- Información del Envío -->
            <div class="card">
                <h2>📦 Información del Envío</h2>
                
                <div class="info-item">
                    <label>Cliente:</label>
                    <span><?php echo htmlspecialchars($envio['NombreClienteSnapshot']); ?></span>
                </div>
                
                <div class="info-item">
                    <label>Teléfono:</label>
                    <span><?php echo htmlspecialchars($envio['TelefonoSnapshot']); ?></span>
                </div>
                
                <div class="info-item">
                    <label>Dirección de Entrega:</label>
                    <span><?php echo htmlspecialchars($envio['DireccionEnvio']); ?></span>
                </div>
                
                <div class="info-item">
                    <label>Fecha Estimada de Entrega:</label>
                    <span><?php echo date('d/m/Y', strtotime($envio['FechaEntregaEstimada'])); ?></span>
                </div>
                
                <?php if ($envio['FechaEnvio']): ?>
                <div class="info-item">
                    <label>Fecha de Envío:</label>
                    <span><?php echo date('d/m/Y H:i', strtotime($envio['FechaEnvio'])); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($envio['FechaEntregaReal']): ?>
                <div class="info-item">
                    <label>Fecha de Entrega Real:</label>
                    <span><?php echo date('d/m/Y H:i', strtotime($envio['FechaEntregaReal'])); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($envio['Observaciones']): ?>
                <div class="info-item">
                    <label>Observaciones:</label>
                    <span style="font-style: italic; color: #666;">
                        <?php echo htmlspecialchars($envio['Observaciones']); ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Timeline del Envío -->
            <div class="card" style="margin-top: 25px;">
                <h2>Historial de Estados</h2>
                <div class="timeline">
                    <div class="timeline-item completado">
                        <strong>Pedido Creado</strong><br>
                        <small style="color: #666;">Envío generado automáticamente</small>
                    </div>
                    <?php if ($envio['FechaEnvio']): ?>
                    <div class="timeline-item completado">
                        <strong>Enviado</strong><br>
                        <small style="color: #666;">
                            <?php echo date('d/m/Y H:i', strtotime($envio['FechaEnvio'])); ?>
                        </small>
                    </div>
                    <?php endif; ?>
                    <?php if ($envio['FechaEntregaReal']): ?>
                    <div class="timeline-item completado">
                        <strong>Entregado</strong><br>
                        <small style="color: #666;">
                            <?php echo date('d/m/Y H:i', strtotime($envio['FechaEntregaReal'])); ?>
                        </small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Columna Derecha -->
        <div>
            <!-- Estado Actual -->
            <div class="card">
                <h2>Estado Actual</h2>
                <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <?php
                    $badgeClass = '';
                    switch ($envio['EstadoEnvio']) {
                        case 'Pendiente': $badgeClass = 'badge-pendiente'; break;
                        case 'En Tráito': $badgeClass = 'badge-transito'; break;
                        case 'Entregado': $badgeClass = 'badge-entregado'; break;
                        case 'Cancelado': $badgeClass = 'badge-cancelado'; break;
                    }
                    ?>
                    <span class="badge <?php echo $badgeClass; ?>" style="font-size: 16px; padding: 10px 20px;">
                        <?php echo htmlspecialchars($envio['EstadoEnvio']); ?>
                    </span>
                </div>
            </div>

            <!-- Actualizar Envío -->
            <div class="card" style="margin-top: 25px;">
                <h2>✏️️ Actualizar Envío</h2>
                <form method="POST" action="<?php echo BASE_URL; ?>controllers/EnvioController.php?action=actualizar" class="form-actualizar">
                    <input type="hidden" name="id_envio" value="<?php echo $envio['IdEnvio']; ?>">
                    
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Estado:</label>
                    <select name="id_estado_envio">
                        <option value="">No cambiar</option>
                        <?php foreach ($estados as $estado): ?>
                            <option value="<?php echo $estado['IdEstadoEnvio']; ?>"
                                <?php echo $estado['IdEstadoEnvio'] == $envio['IdEstadoEnvio'] ? 'selected' : ''; ?>
                                <?php echo htmlspecialchars($estado['NombreEstado']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Fecha de Envío:</label>
                    <input type="datetime-local" name="fecha_envio" 
                           value="<?php echo $envio['FechaEnvio'] ? date('Y-m-d\TH:i', strtotime($envio['FechaEnvio'])) : ''; ?>">

                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Fecha Estimada de Entrega:</label>
                    <input type="datetime-local" name="fecha_entrega_estimada" 
                           value="<?php echo date('Y-m-d\TH:i', strtotime($envio['FechaEntregaEstimada'])); ?>">

                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Observaciones:</label>
                    <textarea name="observaciones" rows="4" placeholder="Agregar observaciones sobre el envío..."><?php echo htmlspecialchars($envio['Observaciones'] ?? ''); ?></textarea>

                    <button type="submit" class="btn-actualizar">💾 Guardar Cambios</button>
                </form>
            </div>

            <!-- Acciones Rápidas -->
            <div class="card" style="margin-top: 25px;">
                <h2>⚡ Acciones Rápidas</h2>
                <a href="<?php echo BASE_URL; ?>view/admin/pedido_detalle.php?id=<?php echo $envio['IdPedido']; ?>" 
                   style="display: block; background: #2C3E50; color: white; padding: 12px; text-align: center; border-radius: 6px; text-decoration: none; margin-bottom: 10px;">
                    Ver Pedido Completo
                </a>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/includes/footer.php'; ?>