<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/CarritoController.php';

// Verificar que el cliente esté logueado
if (!isset($_SESSION['cliente_id'])) {
    $_SESSION['error'] = 'Debe iniciar sesión para continuar';
    header('Location: login.php');
    exit;
}

// Obtener carrito directamente desde el modelo
require_once __DIR__ . '/../../models/Carrito.php';
$carritoModel = new Carrito();

$itemsCarrito = $carritoModel->obtenerPorCliente($_SESSION['cliente_id']);
$totales = $carritoModel->obtenerTotales($_SESSION['cliente_id']);

// Si el carrito está vacío, redirigir
if (empty($itemsCarrito)) {
    $_SESSION['error'] = 'Tu carrito está vacío';
    header('Location: carrito.php');
    exit;
}

// Obtener datos del formulario si hay errores
$datosForm = isset($_SESSION['datos_form']) ? $_SESSION['datos_form'] : [];
unset($_SESSION['datos_form']);

$titulo = "Checkout - Papelink";
include __DIR__ . '/includes/header.php';

?>

<div class="checkout-container">
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <a href="index.php">Inicio</a> / 
        <a href="carrito.php">Carrito</a> / 
        <span>Checkout</span>
    </div>

    <h1 class="titulo-pagina">Finalizar Compra</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php 
            echo htmlspecialchars($_SESSION['error']); 
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['errores'])): ?>
        <div class="alert alert-error">
            <strong>Por favor corrija los siguientes errores:</strong>
            <ul>
                <?php foreach ($_SESSION['errores'] as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['errores']); ?>
    <?php endif; ?>

    <div class="checkout-contenido">
        <!-- Formulario de checkout -->
        <div class="checkout-form">
            <!-- Indicador de pasos -->
            <div class="pasos-checkout">
                <div class="paso activo">
                    <span class="numero">1</span>
                    <span class="texto">Información de Envío</span>
                </div>
                <div class="paso">
                    <span class="numero">2</span>
                    <span class="texto">Método de Pago</span>
                </div>
                <div class="paso">
                    <span class="numero">3</span>
                    <span class="texto">Confirmación</span>
                </div>
            </div>

            <form action="../../controllers/PedidoController.php?action=crear" method="POST" id="formCheckout">
                <!-- Sección 1: Información de Envío -->
                <div class="seccion-form">
                    <h2>Información de Envío</h2>
                    
                    <!-- Tipo de envío -->
                    <div class="form-group">
                        <label for="tipo_envio">Tipo de Envío *</label>
                        <div class="opciones-envio">
                            <label class="opcion-envio">
                                <input type="radio" 
                                       name="tipo_envio" 
                                       value="Domicilio" 
                                       <?php echo (!isset($datosForm['tipo_envio']) || $datosForm['tipo_envio'] === 'Domicilio') ? 'checked' : ''; ?>
                                       onchange="actualizarCostoEnvio()">
                                <div class="opcion-contenido">
                                    <strong>Envío a Domicilio</strong>
                                    <span class="costo-envio">$50.00</span>
                                    <p>Entrega en 3-5 días hábiles</p>
                                </div>
                            </label>
                            
                            <label class="opcion-envio">
                                <input type="radio" 
                                       name="tipo_envio" 
                                       value="Sucursal"
                                       <?php echo (isset($datosForm['tipo_envio']) && $datosForm['tipo_envio'] === 'Sucursal') ? 'checked' : ''; ?>
                                       onchange="actualizarCostoEnvio()">
                                <div class="opcion-contenido">
                                    <strong> Recoger en Sucursal</strong>
                                    <span class="costo-envio gratis">GRATIS</span>
                                    <p>Disponible en 24 horas</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div class="form-group">
                        <label for="direccion_envio">Dirección Completa *</label>
                        <input type="text" 
                               id="direccion_envio" 
                               name="direccion_envio" 
                               placeholder="Calle, número, colonia" 
                               required
                               value="<?php echo isset($datosForm['direccion_envio']) ? htmlspecialchars($datosForm['direccion_envio']) : ''; ?>">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="ciudad_envio">Ciudad *</label>
                            <input type="text" 
                                   id="ciudad_envio" 
                                   name="ciudad_envio" 
                                   placeholder="Tuxtla Gutiérrez" 
                                   required
                                   value="<?php echo isset($datosForm['ciudad_envio']) ? htmlspecialchars($datosForm['ciudad_envio']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="codigo_postal_envio">Código Postal *</label>
                            <input type="text" 
                                   id="codigo_postal_envio" 
                                   name="codigo_postal_envio" 
                                   placeholder="29000" 
                                   maxlength="5" 
                                   required
                                   value="<?php echo isset($datosForm['codigo_postal_envio']) ? htmlspecialchars($datosForm['codigo_postal_envio']) : ''; ?>">
                        </div>
                    </div>

                    <!-- Referencias adicionales -->
                    <div class="form-group">
                        <label for="referencias_adicionales">Referencias Adicionales (Opcional)</label>
                        <textarea id="referencias_adicionales" 
                                  name="referencias_adicionales" 
                                  rows="3" 
                                  placeholder="Casa color azul, portón café, entre calles..."><?php echo isset($datosForm['referencias_adicionales']) ? htmlspecialchars($datosForm['referencias_adicionales']) : ''; ?></textarea>
                    </div>
                </div>

                <!-- Sección 2: Método de Pago -->
                <div class="seccion-form">
                    <h2> Método de Pago</h2>
                    
                    <div class="info-pago">
                        <div class="metodo-pago-item">
                            <input type="radio" name="metodo_pago" value="Efectivo" checked>
                            <label>
                                <strong>Pago en Efectivo</strong>
                                <p>Paga al recibir tu pedido</p>
                            </label>
                        </div>

                        <div class="metodo-pago-item disabled">
                            <input type="radio" name="metodo_pago" value="Tarjeta" disabled>
                            <label>
                                <strong>Tarjeta de Crédito/Débito</strong>
                                <p>Próximamente disponible</p>
                            </label>
                        </div>

                        <div class="metodo-pago-item disabled">
                            <input type="radio" name="metodo_pago" value="Transferencia" disabled>
                            <label>
                                <strong>Transferencia Bancaria</strong>
                                <p>Próximamente disponible</p>
                            </label>
                        </div>
                    </div>

                    <div class="nota-pago">
                        <strong>Nota:</strong> Por el momento solo aceptamos pago en efectivo al momento de la entrega.
                    </div>
                </div>

                <!-- Términos y condiciones -->
                <div class="seccion-form">
                    <div class="terminos">
                        <label>
                            <input type="checkbox" name="acepta_terminos" required>
                            Acepto los <a href="#" target="_blank">términos y condiciones</a> y la <a href="#" target="_blank">política de privacidad</a>
                        </label>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="acciones-checkout">
                    <a href="carrito.php" class="btn-secundario">
                        ← Volver al Carrito
                    </a>
                    <button type="submit" class="btn-finalizar">
                        Finalizar Compra →
                    </button>
                </div>
            </form>
        </div>

        <!-- Resumen del pedido (sidebar) -->
        <div class="resumen-pedido-checkout">
            <div class="resumen-card">
                <h2>Resumen del Pedido</h2>
                
                <!-- Productos -->
                <div class="productos-resumen">
                    <h3>Productos (<?php echo count($itemsCarrito); ?>)</h3>
                    <?php foreach ($itemsCarrito as $item): ?>
                        <div class="producto-resumen-item">
                            <img src="<?php echo !empty($item['ImagenProducto']) ? '../../assets/img/productos/' . htmlspecialchars($item['ImagenProducto']) : 'https://via.placeholder.com/50x50?text=Producto'; ?>"
                                 alt="<?php echo htmlspecialchars($item['NombreProducto']); ?>">
                            <div class="producto-info-resumen">
                                <p class="nombre"><?php echo htmlspecialchars($item['NombreProducto']); ?></p>
                                <p class="cantidad">Cantidad: <?php echo $item['Cantidad']; ?></p>
                            </div>
                            <div class="precio-resumen">
                                $<?php echo number_format($item['Subtotal'], 2); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="separador"></div>

                <!-- Totales -->
                <div class="totales-resumen">
                    <div class="linea-total">
                        <span>Subtotal:</span>
                        <span id="subtotal-display">$<?php echo number_format($totales['subtotal'], 2); ?></span>
                    </div>
                    <div class="linea-total">
                        <span>IVA (16%):</span>
                        <span id="iva-display">$<?php echo number_format($totales['iva'], 2); ?></span>
                    </div>
                    <div class="linea-total">
                        <span>Envío:</span>
                        <span id="envio-display" class="costo-envio-display">$50.00</span>
                    </div>
                    <div class="separador"></div>
                    <div class="linea-total total-final">
                        <span><strong>Total a Pagar:</strong></span>
                        <span id="total-display"><strong>$<?php echo number_format($totales['total'] + 50, 2); ?></strong></span>
                    </div>
                </div>

                <!-- Seguridad -->
                <div class="info-seguridad">
                    <p> <strong>Compra Segura</strong></p>
                    <p>Tus datos están protegidos</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.checkout-container {
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

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-error ul {
    margin: 0.5rem 0 0 1.5rem;
}

/* Layout principal */
.checkout-contenido {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 2rem;
}

/* Pasos del checkout */
.pasos-checkout {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.paso {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    position: relative;
}

.paso:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 20px;
    left: 50%;
    width: 100%;
    height: 2px;
    background-color: #ddd;
    z-index: 0;
}

.paso.activo:not(:last-child)::after {
    background-color: #FF6347;
}

.paso .numero {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #ddd;
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 0.5rem;
    position: relative;
    z-index: 1;
}

.paso.activo .numero {
    background-color: #FF6347;
    color: white;
}

.paso .texto {
    font-size: 0.875rem;
    color: #666;
    text-align: center;
}

.paso.activo .texto {
    color: #2C3E50;
    font-weight: 600;
}

/* Formulario */
.checkout-form {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.seccion-form {
    margin-bottom: 2rem;
}

.seccion-form h2 {
    color: #2C3E50;
    margin-bottom: 1.5rem;
    font-size: 1.5rem;
    border-bottom: 2px solid #FF6347;
    padding-bottom: 0.5rem;
}

/* Opciones de envío */
.opciones-envio {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.opcion-envio {
    position: relative;
    cursor: pointer;
}

.opcion-envio input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.opcion-contenido {
    border: 2px solid #ddd;
    border-radius: 8px;
    padding: 1.5rem;
    transition: all 0.3s;
    background: white;
}

.opcion-envio input[type="radio"]:checked + .opcion-contenido {
    border-color: #FF6347;
    background-color: #fff5f4;
}

.opcion-contenido strong {
    display: block;
    margin-bottom: 0.5rem;
    color: #2C3E50;
}

.opcion-contenido .costo-envio {
    display: inline-block;
    background-color: #FF6347;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.875rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.opcion-contenido .costo-envio.gratis {
    background-color: #27AE60;
}

.opcion-contenido p {
    font-size: 0.875rem;
    color: #666;
    margin: 0;
}

/* Form groups */
.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #2C3E50;
    font-weight: 500;
}

.form-group input[type="text"],
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.form-group input[type="text"]:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #FF6347;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

/* Método de pago */
.info-pago {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.metodo-pago-item {
    border: 2px solid #ddd;
    border-radius: 8px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
    transition: all 0.3s;
}

.metodo-pago-item:hover:not(.disabled) {
    border-color: #FF6347;
}

.metodo-pago-item.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.metodo-pago-item input[type="radio"] {
    width: 20px;
    height: 20px;
}

.metodo-pago-item label {
    flex: 1;
    cursor: pointer;
}

.metodo-pago-item label strong {
    display: block;
    margin-bottom: 0.25rem;
    color: #2C3E50;
}

.metodo-pago-item label p {
    margin: 0;
    font-size: 0.875rem;
    color: #666;
}

.nota-pago {
    background-color: #e7f3ff;
    border-left: 4px solid #2196F3;
    padding: 1rem;
    border-radius: 4px;
    margin-top: 1rem;
    font-size: 0.875rem;
    color: #1976D2;
}

/* Términos */
.terminos {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
}

.terminos label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.terminos input[type="checkbox"] {
    width: 18px;
    height: 18px;
}

.terminos a {
    color: #FF6347;
    text-decoration: none;
}

.terminos a:hover {
    text-decoration: underline;
}

/* Botones de acción */
.acciones-checkout {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    margin-top: 2rem;
}

.btn-secundario {
    background-color: #6c757d;
    color: white;
    padding: 1rem 2rem;
    border-radius: 8px;
    text-decoration: none;
    transition: background-color 0.3s;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
}

.btn-secundario:hover {
    background-color: #5a6268;
}

.btn-finalizar {
    background-color: #FF6347;
    color: white;
    padding: 1rem 2rem;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
    transition: background-color 0.3s;
}

.btn-finalizar:hover {
    background-color: #e5533d;
}

/* Resumen del pedido */
.resumen-pedido-checkout {
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

.resumen-card h3 {
    margin: 0 0 1rem 0;
    color: #2C3E50;
    font-size: 1rem;
}

/* Productos en resumen */
.productos-resumen {
    margin-bottom: 1rem;
}

.producto-resumen-item {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.producto-resumen-item:last-child {
    border-bottom: none;
}

.producto-resumen-item img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 6px;
}

.producto-info-resumen {
    flex: 1;
}

.producto-info-resumen .nombre {
    margin: 0 0 0.25rem 0;
    font-size: 0.875rem;
    color: #2C3E50;
}

.producto-info-resumen .cantidad {
    margin: 0;
    font-size: 0.75rem;
    color: #666;
}

.precio-resumen {
    font-weight: 600;
    color: #FF6347;
}

/* Totales */
.separador {
    height: 1px;
    background-color: #ddd;
    margin: 1rem 0;
}

.totales-resumen {
    margin-top: 1rem;
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

.linea-total.total-final {
    font-size: 1.25rem;
    color: #2C3E50;
    margin-top: 1rem;
}

.linea-total.total-final span:last-child {
    color: #FF6347;
}

.costo-envio-display.gratis {
    color: #27AE60;
}

/* Info de seguridad */
.info-seguridad {
    background-color: #f0f8ff;
    padding: 1rem;
    border-radius: 8px;
    margin-top: 1.5rem;
    text-align: center;
}

.info-seguridad p {
    margin: 0.25rem 0;
    font-size: 0.875rem;
}

.info-seguridad p:first-child {
    color: #2C3E50;
}

.info-seguridad p:last-child {
    color: #666;
}

/* Responsive */
@media (max-width: 1024px) {
    .checkout-contenido {
        grid-template-columns: 1fr;
    }
    
    .resumen-pedido-checkout {
        position: static;
    }
}

@media (max-width: 768px) {
    .pasos-checkout {
        flex-direction: column;
        gap: 1rem;
    }
    
    .paso::after {
        display: none;
    }
    
    .opciones-envio {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .acciones-checkout {
        flex-direction: column;
    }
    
    .btn-secundario,
    .btn-finalizar {
        width: 100%;
        text-align: center;
    }
}
</style>

<script>
// Actualizar costo de envío en tiempo real
function actualizarCostoEnvio() {
    const tipoEnvio = document.querySelector('input[name="tipo_envio"]:checked').value;
    const subtotal = <?php echo $totales['subtotal']; ?>;
    const iva = <?php echo $totales['iva']; ?>;
    
    let costoEnvio = 0;
    let textoEnvio = '';
    
    if (tipoEnvio === 'Domicilio') {
        costoEnvio = 50.00;
        textoEnvio = '$50.00';
    } else {
        costoEnvio = 0;
        textoEnvio = 'GRATIS';
    }
    
    const total = subtotal + iva + costoEnvio;
    
    // Actualizar displays
    document.getElementById('envio-display').textContent = textoEnvio;
    document.getElementById('total-display').innerHTML = '<strong>$' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '</strong>';
    
    // Agregar/quitar clase gratis
    const envioDisplay = document.getElementById('envio-display');
    if (costoEnvio === 0) {
        envioDisplay.classList.add('gratis');
    } else {
        envioDisplay.classList.remove('gratis');
    }
}

// Validación del formulario
document.getElementById('formCheckout').addEventListener('submit', function(e) {
    const aceptaTerminos = document.querySelector('input[name="acepta_terminos"]').checked;
    
    if (!aceptaTerminos) {
        e.preventDefault();
        alert('Debe aceptar los términos y condiciones para continuar');
        return false;
    }
});

// Validación de código postal (solo números)
document.getElementById('codigo_postal_envio').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>