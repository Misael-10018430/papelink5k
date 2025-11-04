<?php
require_once __DIR__ . '/../../config/config.php';
// =======================================================
// FIX 1: El archivo es 'Pedidos.php' (plural)
// =======================================================
require_once __DIR__ . '/../../models/Pedidos.php';

// Verificar que el cliente esté logueado
if (!isset($_SESSION['cliente_id'])) {
    $_SESSION['error'] = 'Debe iniciar sesión';
    header('Location: login.php');
    exit;
}

// Verificar que se haya proporcionado un ID de pedido
if (!isset($_GET['pedido'])) {
    $_SESSION['error'] = 'No se especificó un pedido';
    header('Location: index.php');
    exit;
}

$idPedido = (int)$_GET['pedido'];
$idCliente = $_SESSION['cliente_id'];

// Obtener detalles del pedido
$pedidoModel = new Pedido();
$pedido = $pedidoModel->obtenerDetalle($idPedido, $idCliente);

// Si no se encuentra el pedido, redirigir
if (empty($pedido)) {
    $_SESSION['error'] = 'Pedido no encontrado';
    header('Location: mis_pedidos.php');
    exit;
}

// El SP almacena los productos en 'detalles'
$productos = $pedido['detalles'] ?? [];

// Obtener información del envío (Esta función SÍ existe en tu modelo)
$envioData = $pedidoModel->obtenerEnvio($idPedido);

if (!$envioData) {
    $envio = [
        'EstadoEnvio' => 'No aplica - Recoger en Sucursal',
        'DireccionCompleta' => $pedido['DireccionSnapshot'] ?? 'Sucursal Principal',
        'FechaEstimadaEntrega' => null
    ];
} else {
    $envio = [
        'EstadoEnvio' => $envioData['EstadoEnvio'] ?? 'Pendiente',
        'DireccionCompleta' => $envioData['DireccionCompleta'] ?? $pedido['DireccionSnapshot'],
        'FechaEstimadaEntrega' => $envioData['FechaEstimadaEntrega']
    ];
}

$titulo = "Pedido Confirmado - Papelink";
include __DIR__ . '/includes/header.php';

// =======================================================
// FIX 4: Calculamos el Subtotal manualmente
// =======================================================
$subtotalCalculado = ($pedido['Total'] ?? 0) - ($pedido['IVA'] ?? 0) - ($pedido['CostoEnvio'] ?? 0);
?>

<div class="confirmacion-container">
    <div class="confirmacion-header">
        <div class="icono-exito">✓</div>
        <h1>¡Pedido Realizado con Éxito!</h1>
        <p class="mensaje-principal">Tu pedido ha sido registrado correctamente</p>
    </div>

    <div class="confirmacion-contenido">
        <div class="seccion-confirmacion">
            <div class="info-pedido-principal">
                <div class="info-item destacado">
                    <span class="etiqueta">Número de Pedido:</span>
                    <span class="valor numero-pedido"><?php echo htmlspecialchars($pedido['NumeroPedido']); ?></span>
                </div>
                
                <div class="info-grid">
                    <div class="info-item">
                        <span class="etiqueta">Fecha:</span>
                        <span class="valor"><?php echo date('d/m/Y H:i', strtotime($pedido['FechaPedido'])); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <span class="etiqueta">Estado:</span>
                        <span class="badge badge-<?php echo strtolower(str_replace(' ', '.', $pedido['EstadoPedido'] ?? 'pendiente')); ?>">
                            <?php echo htmlspecialchars($pedido['EstadoPedido'] ?? 'Pendiente'); ?>
                        </span>
                    </div>
                    
                    <div class="info-item">
                        <span class="etiqueta">Método de Pago:</span>
                        <span class="valor"><?php echo htmlspecialchars($pedido['MetodoPago'] ?? 'N/A'); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <span class="etiqueta">Tipo de Envío:</span>
                        <span class="valor"><?php echo htmlspecialchars($pedido['TipoEntrega'] ?? 'N/A'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="seccion-confirmacion">
            <h2>Productos Ordenados</h2>
            <div class="tabla-productos">
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td>
                                    <div class="producto-info">
                                        <strong><?php echo htmlspecialchars($producto['NombreProducto'] ?? 'Producto'); ?></strong>
                                        <span class="codigo">Código: <?php echo htmlspecialchars($producto['CodigoProducto'] ?? 'N/A'); ?></span>
                                    </div>
                                </td>
                                <td class="centrado"><?php echo $producto['Cantidad'] ?? 0; ?></td>
                                <td>$<?php echo number_format($producto['PrecioUnitario'] ?? 0, 2); ?></td>
                                <td class="precio-destacado">$<?php echo number_format($producto['Subtotal'] ?? 0, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="totales-confirmacion">
                <div class="linea-total">
                    <span>Subtotal:</span>
                    <span>$<?php echo number_format($subtotalCalculado, 2); ?></span>
                </div>
                <div class="linea-total">
                    <span>IVA (16%):</span>
                    <span>$<?php echo number_format($pedido['IVA'] ?? 0, 2); ?></span>
                </div>
                <div class="linea-total">
                    <span>Costo de Envío:</span>
                    <span><?php echo ($pedido['CostoEnvio'] ?? 0) > 0 ? '$' . number_format($pedido['CostoEnvio'], 2) : 'GRATIS'; ?></span>
                </div>
                <div class="separador"></div>
                <div class="linea-total total-final">
                    <span><strong>Total:</strong></span>
                    <span class="total-valor"><strong>$<?php echo number_format($pedido['Total'] ?? 0, 2); ?></strong></span>
                </div>
            </div>
        </div>

        <div class="seccion-confirmacion">
            <h2>Información de Envío</h2>
            <div class="info-envio-detalle">
                <div class="envio-item">
                    <strong>Estado:</strong>
                    <span class="badge badge-<?php echo strtolower(str_replace(' ', '.', $envio['EstadoEnvio'])); ?>">
                        <?php echo htmlspecialchars($envio['EstadoEnvio']); ?>
                    </span>
                </div>
                
                <div class="envio-item">
                    <strong>Dirección de Entrega:</strong>
                    <p><?php echo htmlspecialchars($envio['DireccionCompleta']); ?></p>
                </div>
                        
                <?php if (!empty($pedido['NotasCliente'])): ?>
                    <div class="envio-item">
                        <strong>Referencias:</strong>
                        <p><?php echo htmlspecialchars($pedido['NotasCliente']); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($envio['FechaEstimadaEntrega'])): ?>
                    <div class="envio-item">
                        <strong>Fecha Estimada de Entrega:</strong>
                        <p><?php echo date('d/m/Y', strtotime($envio['FechaEstimadaEntrega'])); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="seccion-confirmacion proximos-pasos">
            <h2>Próximos Pasos</h2>
            <div class="pasos-lista">
                <div class="paso-item">
                    <div class="paso-numero">1</div>
                    <div class="paso-contenido">
                        <h3>Confirmación</h3>
                        <p>Recibirás un correo electrónico con los detalles de tu pedido</p>
                    </div>
                </div>
                
                <div class="paso-item">
                    <div class="paso-numero">2</div>
                    <div class="paso-contenido">
                        <h3>Preparación</h3>
                        <p>Prepararemos tu pedido con cuidado y lo empaquetaremos</p>
                    </div>
                </div>
                
                <div class="paso-item">
                    <div class="paso-numero">3</div>
                    <div class="paso-contenido">
                        <h3>Envío</h3>
                        <p><?php echo ($pedido['TipoEntrega'] ?? '') === 'Envío a Domicilio' ? 'Te lo enviaremos a tu dirección' : 'Podrás recogerlo en nuestra sucursal'; ?></p>
                    </div>
                </div>
                
                <div class="paso-item">
                    <div class="paso-numero">4</div>
                    <div class="paso-contenido">
                        <h3>Entrega</h3>
                        <p>Recibirás tu pedido y realizarás el pago en efectivo</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="seccion-confirmacion info-adicional">
            <div class="info-box">
                <h3>¿Necesitas Ayuda?</h3>
                <p>Si tienes alguna pregunta sobre tu pedido, no dudes en contactarnos:</p>
                <ul>
                    <li> Teléfono: 916-186-8451</li>
                    <li>Email: contacto@papelink.com</li>
                    <li> Horario: Lunes a Viernes de 9:00 AM - 6:00 PM</li>
                </ul>
            </div>
        </div>

        <div class="acciones-confirmacion">
            <a href="mis_pedidos.php" class="btn btn-secundario">
                Ver Mis Pedidos
            </a>
            <a href="productos.php" class="btn btn-primary">
                Seguir Comprando
            </a>
        </div>
    </div>
</div>

<style>
.confirmacion-container {
    max-width: 1000px;
    margin: 2rem auto;
    padding: 0 1rem;
}

/* Header de confirmación */
.confirmacion-header {
    text-align: center;
    background: linear-gradient(135deg, #27AE60 0%, #229954 100%);
    color: white;
    padding: 3rem 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
}

.icono-exito {
    width: 80px;
    height: 80px;
    background: white;
    color: #27AE60;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: bold;
    margin: 0 auto 1.5rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    animation: checkmark 0.5s ease-in-out;
}

@keyframes checkmark {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.confirmacion-header h1 {
    margin: 0 0 0.5rem 0;
    font-size: 2rem;
}

.mensaje-principal {
    margin: 0;
    font-size: 1.125rem;
    opacity: 0.95;
}

/* Contenido */
.confirmacion-contenido {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.seccion-confirmacion {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.seccion-confirmacion h2 {
    margin: 0 0 1.5rem 0;
    color: #2C3E50;
    font-size: 1.5rem;
    border-bottom: 2px solid #FF6347;
    padding-bottom: 0.5rem;
}

/* Info principal del pedido */
.info-pedido-principal {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.info-item.destacado {
    background: linear-gradient(135deg, #FF6347 0%, #e5533d 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.info-item.destacado .etiqueta {
    font-size: 1rem;
}

.numero-pedido {
    font-size: 1.5rem;
    font-weight: bold;
    letter-spacing: 1px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.info-item .etiqueta {
    color: #666;
    font-size: 0.875rem;
}

.info-item .valor {
    color: #2C3E50;
    font-weight: 600;
    font-size: 1rem;
}

/* Badges */
.badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}

.badge-pendiente {
    background-color: #FFC107;
    color: #000;
}

.badge-en.proceso, .badge-en.proceso {
    background-color: #2196F3;
    color: white;
}

.badge-enviado {
    background-color: #9C27B0;
    color: white;
}

.badge-completado {
    background-color: #4CAF50;
    color: white;
}

/* Tabla de productos */
.tabla-productos {
    overflow-x: auto;
}

.tabla-productos table {
    width: 100%;
    border-collapse: collapse;
}

.tabla-productos thead {
    background-color: #2C3E50;
    color: white;
}

.tabla-productos th,
.tabla-productos td {
    padding: 1rem;
    text-align: left;
}

.tabla-productos tbody tr {
    border-bottom: 1px solid #eee;
}

.tabla-productos tbody tr:hover {
    background-color: #f8f9fa;
}

.producto-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.producto-info .codigo {
    font-size: 0.75rem;
    color: #666;
}

.centrado {
    text-align: center;
}

.precio-destacado {
    color: #FF6347;
    font-weight: 600;
}

/* Totales */
.totales-confirmacion {
    margin-top: 1.5rem;
    padding: 1.5rem;
    background-color: #f8f9fa;
    border-radius: 8px;
}

.linea-total {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
    color: #666;
}

.linea-total span:last-child {
    color: #2C3E50;
    font-weight: 500;
}

.separador {
    height: 1px;
    background-color: #ddd;
    margin: 1rem 0;
}

.linea-total.total-final {
    font-size: 1.5rem;
    color: #2C3E50;
    margin-top: 1rem;
}

.total-valor {
    color: #FF6347;
}

/* Info de envío */
.info-envio-detalle {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.envio-item strong {
    display: block;
    color: #2C3E50;
    margin-bottom: 0.5rem;
}

.envio-item p {
    margin: 0.25rem 0;
    color: #666;
}

/* Próximos pasos */
.proximos-pasos {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.pasos-lista {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

.paso-item {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.paso-numero {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #FF6347 0%, #e5533d 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    flex-shrink: 0;
}

.paso-contenido h3 {
    margin: 0 0 0.5rem 0;
    color: #2C3E50;
    font-size: 1rem;
}

.paso-contenido p {
    margin: 0;
    color: #666;
    font-size: 0.875rem;
}

/* Info adicional */
.info-adicional {
    background-color: #e7f3ff;
    border-left: 4px solid #2196F3;
}

.info-box h3 {
    color: #2C3E50;
    margin: 0 0 1rem 0;
}

.info-box p {
    margin: 0 0 1rem 0;
    color: #666;
}

.info-box ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.info-box li {
    padding: 0.5rem 0;
    color: #2C3E50;
}

/* Botones de acción */
.acciones-confirmacion {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 1rem;
}

.btn {
    padding: 1rem 2rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
    font-size: 1rem;
}

.btn-primary {
    background-color: #FF6347;
    color: white;
}

.btn-primary:hover {
    background-color: #e5533d;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 99, 71, 0.3);
}

.btn-secundario {
    background-color: #2C3E50;
    color: white;
}

.btn-secundario:hover {
    background-color: #1a252f;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(44, 62, 80, 0.3);
}

.btn-outline {
    background-color: white;
    color: #2C3E50;
    border: 2px solid #2C3E50;
}

.btn-outline:hover {
    background-color: #2C3E50;
    color: white;
}

/* Print styles */
@media print {
    .confirmacion-container {
        max-width: 100%;
    }
    
    .acciones-confirmacion {
        display: none;
    }
    
    .confirmacion-header {
        background: #27AE60;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .confirmacion-header {
        padding: 2rem 1rem;
    }
    
    .confirmacion-header h1 {
        font-size: 1.5rem;
    }
    
    .icono-exito {
        width: 60px;
        height: 60px;
        font-size: 2rem;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .pasos-lista {
        grid-template-columns: 1fr;
    }
    
    .acciones-confirmacion {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        text-align: center;
    }
    
    .tabla-productos {
        font-size: 0.875rem;
    }
    
    .tabla-productos th,
    .tabla-productos td {
        padding: 0.5rem;
    }
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>