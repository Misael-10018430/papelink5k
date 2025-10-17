<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/CarritoController.php';

// Verificar que el cliente esté logueado
if (!isset($_SESSION['cliente_id'])) {
    $_SESSION['error'] = 'Debe iniciar sesión para ver su carrito';
    header('Location: login.php');
    exit;
}

$carritoController = new CarritoController();

// Obtener carrito completo (items y totales)
$carritoData = $carritoController->ver();

// Extraer items y totales del array retornado
$itemsCarrito = $carritoData['items'];
$totales = $carritoData['totales'];

$titulo = "Mi Carrito - Papelink";
include __DIR__ . '/includes/header.php';
?>
<div class="carrito-container">
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <a href="index.php">Inicio</a> / <span>Carrito de Compras</span>
    </div>

    <h1 class="titulo-pagina">Mi Carrito de Compras</h1>

    <?php if (isset($_SESSION['exito'])): ?>
        <div class="alert alert-success">
            <?php 
            echo htmlspecialchars($_SESSION['exito']); 
            unset($_SESSION['exito']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php 
            echo htmlspecialchars($_SESSION['error']); 
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (empty($itemsCarrito)): ?>
        <!-- Carrito vacío -->
        <div class="carrito-vacio">
            <div class="icono-carrito-vacio">🛒</div>
            <h2>Tu carrito está vacío</h2>
            <p>¡Agrega productos para comenzar tu compra!</p>
            <a href="productos.php" class="btn btn-primary">Ver Productos</a>
        </div>
    <?php else: ?>
        <!-- Carrito con productos -->
        <div class="carrito-contenido">
            <!-- Tabla de productos -->
            <div class="carrito-items">
                <table class="tabla-carrito">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                       <?php foreach ($itemsCarrito as $item): ?>
                                <tr class="item-carrito" data-id="<?php echo $item['IdCarrito']; ?>">
                                    <td class="producto-info">
                                        <?php
                                        // Determinar la ruta de la imagen del producto
                                        if (!empty($item['ImagenProducto'])) {
                                            $rutaImagenCarrito = '../../assets/img/productos/' . $item['ImagenProducto'];
                                        } else {
                                            $rutaImagenCarrito = 'https://via.placeholder.com/80x80/f5f5f5/666666?text=Sin+Imagen';
                                        }
                                        ?>
                                        <img src="<?php echo $rutaImagenCarrito; ?>" 
                                            alt="<?php echo htmlspecialchars($item['NombreProducto']); ?>"
                                            class="producto-imagen"
                                            onerror="this.src='https://via.placeholder.com/80x80/f5f5f5/666666?text=Sin+Imagen'">
                                        <div class="producto-detalles">
                                            <h3><?php echo htmlspecialchars($item['NombreProducto']); ?></h3>
                                            <p class="codigo-producto">Código: <?php echo htmlspecialchars($item['CodigoProducto']); ?></p>
                                            <?php if ($item['CantidadDisponible'] < $item['Cantidad']): ?>
                                                <span class="badge badge-warning">⚠️ Stock limitado: <?php echo $item['CantidadDisponible']; ?> disponibles</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="precio">
                                        $<?php echo number_format($item['PrecioUnitario'], 2); ?>
                                    </td>
                                    <td class="cantidad">
                                        <div class="cantidad-controls">
                                            <button class="btn-cantidad" onclick="cambiarCantidad(<?php echo $item['IdCarrito']; ?>, -1, <?php echo $item['Cantidad']; ?>, <?php echo $item['CantidadDisponible']; ?>)">-</button>
                                            <input type="number" 
                                                class="input-cantidad" 
                                                id="cantidad-<?php echo $item['IdCarrito']; ?>"
                                                value="<?php echo $item['Cantidad']; ?>" 
                                                min="1" 
                                                max="<?php echo $item['CantidadDisponible']; ?>"
                                                onchange="actualizarCantidad(<?php echo $item['IdCarrito']; ?>, this.value, <?php echo $item['CantidadDisponible']; ?>)">
                                            <button class="btn-cantidad" onclick="cambiarCantidad(<?php echo $item['IdCarrito']; ?>, 1, <?php echo $item['Cantidad']; ?>, <?php echo $item['CantidadDisponible']; ?>)">+</button>
                                        </div>
                                    </td>
                                    <td class="subtotal">
                                        <strong>$<?php echo number_format($item['Subtotal'], 2); ?></strong>
                                    </td>
                                    <td class="acciones">
                                        <button class="btn-eliminar" onclick="confirmarEliminacion(<?php echo $item['IdCarrito']; ?>, '<?php echo htmlspecialchars(addslashes($item['NombreProducto'])); ?>')">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Botón vaciar carrito -->
                <div class="acciones-carrito">
                    <button class="btn-vaciar" onclick="confirmarVaciarCarrito()">
                        Vaciar Carrito
                    </button>
                    <a href="productos.php" class="btn-continuar">
                        ← Continuar Comprando
                    </a>
                </div>
            </div>

            <!-- Resumen del pedido -->
            <div class="resumen-pedido">
                <div class="resumen-card">
                    <h2>Resumen del Pedido</h2>
                    
                    <div class="resumen-detalle">
                        <div class="linea-resumen">
                            <span>Subtotal:</span>
                            <span class="valor">$<?php echo number_format($totales['subtotal'], 2); ?></span>
                        </div>
                        <div class="linea-resumen">
                            <span>IVA (16%):</span>
                            <span class="valor">$<?php echo number_format($totales['iva'], 2); ?></span>
                        </div>
                        <div class="linea-resumen envio-info">
                            <span>Costo de Envío:</span>
                            <span class="valor">Se calcula en checkout</span>
                        </div>
                        <div class="separador"></div>
                        <div class="linea-resumen total">
                            <span><strong>Total:</strong></span>
                            <span class="valor"><strong>$<?php echo number_format($totales['total'], 2); ?></strong></span>
                        </div>
                    </div>

                    <div class="info-envio">
                        <p><strong>Costo de envío:</strong></p>
                        <ul>
                            <li>• Domicilio: $50.00</li>
                            <li>• Recoger en sucursal: Gratis</li>
                        </ul>
                    </div>

                    <a href="checkout.php" class="btn-checkout">
                        Proceder al Pago →
                    </a>

                    <div class="metodos-pago">
                        <p>💳 Aceptamos:</p>
                        <div class="iconos-pago">
                            <span>VISA</span>
                            <span>Mastercard</span>
                            <span>PayPal</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.carrito-container {
    max-width: 1400px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.breadcrumbs {
    margin-bottom: 2rem;
    color: #666;
}

.breadcrumbs a {
    color: #FF6347;
    text-decoration: none;
}

.breadcrumbs a:hover {
    text-decoration: underline;
}

.titulo-pagina {
    font-size: 2rem;
    margin-bottom: 2rem;
    color: #2C3E50;
}

/* Alertas */
.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Carrito vacío */
.carrito-vacio {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.icono-carrito-vacio {
    font-size: 5rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.carrito-vacio h2 {
    color: #2C3E50;
    margin-bottom: 0.5rem;
}

.carrito-vacio p {
    color: #666;
    margin-bottom: 2rem;
}

/* Contenido del carrito */
.carrito-contenido {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 2rem;
}

.carrito-items {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Tabla */
.tabla-carrito {
    width: 100%;
    border-collapse: collapse;
}

.tabla-carrito thead {
    background-color: #2C3E50;
    color: white;
}

.tabla-carrito th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
}

.tabla-carrito tbody tr {
    border-bottom: 1px solid #eee;
}

.tabla-carrito tbody tr:hover {
    background-color: #f8f9fa;
}

.tabla-carrito td {
    padding: 1rem;
    vertical-align: middle;
}

/* Producto info */
.producto-info {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.producto-imagen {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #ddd;
}

.producto-detalles h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    color: #2C3E50;
}

.codigo-producto {
    font-size: 0.875rem;
    color: #666;
    margin: 0;
}

.badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    border-radius: 4px;
    margin-top: 0.25rem;
}

.badge-warning {
    background-color: #FFC107;
    color: #000;
}

/* Precio */
.precio {
    font-size: 1.125rem;
    color: #2C3E50;
    font-weight: 500;
}

/* Cantidad */
.cantidad-controls {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-cantidad {
    background-color: #2C3E50;
    color: white;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1.125rem;
    transition: background-color 0.3s;
}

.btn-cantidad:hover {
    background-color: #FF6347;
}

.input-cantidad {
    width: 60px;
    text-align: center;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

/* Subtotal */
.subtotal strong {
    color: #FF6347;
    font-size: 1.125rem;
}

/* Botones */
.btn-eliminar {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-eliminar:hover {
    background-color: #c82333;
}

.acciones-carrito {
    display: flex;
    justify-content: space-between;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #eee;
}

.btn-vaciar {
    background-color: #6c757d;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-vaciar:hover {
    background-color: #5a6268;
}

.btn-continuar {
    background-color: #2C3E50;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    text-decoration: none;
    transition: background-color 0.3s;
}

.btn-continuar:hover {
    background-color: #1a252f;
}

/* Resumen del pedido */
.resumen-pedido {
    position: sticky;
    top: 100px;
    height: fit-content;
}

.resumen-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.resumen-card h2 {
    margin: 0 0 1.5rem 0;
    color: #2C3E50;
    font-size: 1.5rem;
}

.resumen-detalle {
    margin-bottom: 1.5rem;
}

.linea-resumen {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
    color: #666;
}

.linea-resumen .valor {
    color: #2C3E50;
    font-weight: 500;
}

.linea-resumen.envio-info .valor {
    font-size: 0.875rem;
    color: #666;
    font-style: italic;
}

.separador {
    height: 1px;
    background-color: #ddd;
    margin: 1rem 0;
}

.linea-resumen.total {
    font-size: 1.25rem;
    color: #2C3E50;
}

.linea-resumen.total .valor {
    color: #FF6347;
}

.info-envio {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    font-size: 0.875rem;
}

.info-envio ul {
    list-style: none;
    padding: 0;
    margin: 0.5rem 0 0 0;
}

.info-envio li {
    margin: 0.25rem 0;
    color: #666;
}

.btn-checkout {
    display: block;
    width: 100%;
    background-color: #FF6347;
    color: white;
    padding: 1rem;
    text-align: center;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.125rem;
    transition: background-color 0.3s;
    margin-bottom: 1rem;
}

.btn-checkout:hover {
    background-color: #e5533d;
}

.metodos-pago {
    text-align: center;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.metodos-pago p {
    margin: 0 0 0.5rem 0;
    font-size: 0.875rem;
    color: #666;
}

.iconos-pago {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
}

.iconos-pago span {
    background-color: #f8f9fa;
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.75rem;
    color: #666;
}

.btn-primary {
    background-color: #FF6347;
    color: white;
    padding: 1rem 2rem;
    border-radius: 8px;
    text-decoration: none;
    display: inline-block;
    transition: background-color 0.3s;
}

.btn-primary:hover {
    background-color: #e5533d;
}

/* Responsive */
@media (max-width: 1024px) {
    .carrito-contenido {
        grid-template-columns: 1fr;
    }
    
    .resumen-pedido {
        position: static;
    }
}

@media (max-width: 768px) {
    .tabla-carrito {
        font-size: 0.875rem;
    }
    
    .producto-info {
        flex-direction: column;
        text-align: center;
    }
    
    .tabla-carrito th,
    .tabla-carrito td {
        padding: 0.5rem;
    }
    
    .acciones-carrito {
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn-vaciar,
    .btn-continuar {
        width: 100%;
        text-align: center;
    }
}
</style>

<script>
// Cambiar cantidad con botones +/-
function cambiarCantidad(idCarrito, cambio, cantidadActual, stockDisponible) {
    const nuevaCantidad = cantidadActual + cambio;
    
    if (nuevaCantidad < 1) {
        alert('La cantidad mínima es 1');
        return;
    }
    
    if (nuevaCantidad > stockDisponible) {
        alert('No hay suficiente stock disponible. Máximo: ' + stockDisponible);
        return;
    }
    
    actualizarCantidad(idCarrito, nuevaCantidad, stockDisponible);
}

// Actualizar cantidad
function actualizarCantidad(idCarrito, cantidad, stockDisponible) {
    cantidad = parseInt(cantidad);
    
    if (cantidad < 1 || cantidad > stockDisponible) {
        alert('Cantidad inválida. Disponible: ' + stockDisponible);
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '../../controllers/CarritoController.php?action=actualizar';
    
    const inputId = document.createElement('input');
    inputId.type = 'hidden';
    inputId.name = 'id_carrito';
    inputId.value = idCarrito;
    form.appendChild(inputId);
    
    const inputCantidad = document.createElement('input');
    inputCantidad.type = 'hidden';
    inputCantidad.name = 'cantidad';
    inputCantidad.value = cantidad;
    form.appendChild(inputCantidad);
    
    document.body.appendChild(form);
    form.submit();
}

// Confirmar eliminación
function confirmarEliminacion(idCarrito, nombreProducto) {
    if (confirm('¿Eliminar "' + nombreProducto + '" del carrito?')) {
        window.location.href = '../../controllers/CarritoController.php?action=eliminar&id=' + idCarrito;
    }
}

// Confirmar vaciar carrito
function confirmarVaciarCarrito() {
    if (confirm('¿Está seguro que desea vaciar todo el carrito?')) {
        window.location.href = '../../controllers/CarritoController.php?action=vaciar';
    }
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>