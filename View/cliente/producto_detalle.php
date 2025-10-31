<?php
require_once __DIR__ . '/../../controllers/ProductoController.php';

// Verificar que se proporcionó un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = 'No se especificó un producto';
    header('Location: productos.php');
    exit;
}

$idProducto = (int)$_GET['id'];

// Obtener el producto
$productoController = new ProductoController();
$producto = $productoController->obtenerPorId($idProducto);

// Si no se encuentra el producto, redirigir
if (!$producto) {
    $_SESSION['error'] = 'Producto no encontrado';
    header('Location: productos.php');
    exit;
}

// Obtener productos relacionados
$productosRelacionados = $productoController->obtenerRelacionados($idProducto);

include 'includes/header.php';
?>

<style>
    .detalle-container {
        max-width: 1400px;
        margin: 30px auto;
        padding: 0 20px;
    }
    
    .breadcrumbs {
        margin-bottom: 30px;
        color: #999;
        font-size: 14px;
    }
    
    .breadcrumbs a {
        color: #FF6347;
        text-decoration: none;
    }
    
    .breadcrumbs a:hover {
        text-decoration: underline;
    }
    
    .detalle-producto {
        background-color: white;
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 50px;
        margin-bottom: 30px;
    }
    
    .galeria-producto {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .imagen-principal {
        width: 100%;
        height: 500px;
        object-fit: contain;
        border-radius: 12px;
        background-color: #f8f9fa;
        padding: 20px;
        border: 2px solid #eee;
    }
    
    .info-producto {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .titulo-producto {
        font-size: 32px;
        color: #2C3E50;
        margin: 0;
        line-height: 1.2;
    }
    
    .codigo-producto {
        color: #666;
        font-size: 14px;
    }
    
    .precio-grande {
        font-size: 48px;
        font-weight: bold;
        color: #FF6347;
        margin: 20px 0;
    }
    
    .disponibilidad {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 15px;
        border-radius: 8px;
        font-weight: bold;
    }
    
    .disponibilidad.disponible {
        background-color: #d4edda;
        color: #155724;
    }
    
    .disponibilidad.agotado {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .selector-cantidad {
        display: flex;
        align-items: center;
        gap: 20px;
        margin: 30px 0;
    }
    
    .selector-cantidad label {
        font-weight: bold;
        color: #2C3E50;
    }
    
    .cantidad-controls {
        display: flex;
        align-items: center;
        gap: 15px;
        border: 2px solid #ddd;
        border-radius: 8px;
        padding: 5px;
    }
    
    .btn-cantidad {
        width: 40px;
        height: 40px;
        border: none;
        background-color: #FF6347;
        color: white;
        font-size: 20px;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    
    .btn-cantidad:hover {
        background-color: #e5533d;
    }
    
    .btn-cantidad:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }
    
    .input-cantidad {
        width: 80px;
        text-align: center;
        padding: 10px;
        border: none;
        font-size: 18px;
        font-weight: bold;
        color: #2C3E50;
    }
    
    .acciones-producto {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-top: 30px;
    }
    
    .btn-agregar-carrito {
        background-color: #FF6347;
        color: white;
        padding: 18px 40px;
        border: none;
        border-radius: 8px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .btn-agregar-carrito:hover {
        background-color: #e5533d;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 99, 71, 0.3);
    }
    
    .btn-agregar-carrito:disabled {
        background-color: #ccc;
        cursor: not-allowed;
        transform: none;
    }
    
    .btn-comprar-ahora {
        background-color: #2C3E50;
        color: white;
        padding: 18px 40px;
        border: none;
        border-radius: 8px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .btn-comprar-ahora:hover {
        background-color: #1a252f;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(44, 62, 80, 0.3);
    }
    
    .info-adicional {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
    }
    
    .info-adicional h4 {
        color: #2C3E50;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .info-adicional ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .info-adicional li {
        padding: 10px 0;
        border-bottom: 1px solid #dee2e6;
        color: #666;
    }
    
    .info-adicional li:last-child {
        border-bottom: none;
    }
    
    .tabs-container {
        background-color: white;
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .tabs-nav {
        display: flex;
        gap: 30px;
        border-bottom: 2px solid #eee;
        margin-bottom: 30px;
    }
    
    .tab-button {
        background: none;
        border: none;
        padding: 15px 0;
        font-size: 16px;
        font-weight: bold;
        color: #666;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
    }
    
    .tab-button.active {
        color: #FF6347;
        border-bottom-color: #FF6347;
    }
    
    .tab-content {
        display: none;
    }
    
    .tab-content.active {
        display: block;
    }
    
    .descripcion-producto {
        color: #666;
        line-height: 1.8;
        font-size: 16px;
    }
    
    .descripcion-producto h3 {
        color: #2C3E50;
        margin: 20px 0 10px 0;
    }
    
    .descripcion-producto ul {
        margin-left: 20px;
    }
    
    .descripcion-producto li {
        margin: 8px 0;
    }
    
    .productos-relacionados {
        background-color: white;
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-top: 30px;
    }
    
    .productos-relacionados h2 {
        color: #2C3E50;
        margin-bottom: 30px;
        font-size: 28px;
    }
    
    .grid-relacionados {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    
    .producto-relacionado {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        text-decoration: none;
        color: #333;
        transition: all 0.3s;
    }
    
    .producto-relacionado:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .producto-relacionado img {
        width: 100%;
        height: 150px;
        object-fit: contain;
        margin-bottom: 10px;
    }
    
    .producto-relacionado h4 {
        font-size: 14px;
        margin-bottom: 10px;
        min-height: 40px;
    }
    
    .producto-relacionado .precio {
        font-size: 18px;
        font-weight: bold;
        color: #FF6347;
    }
    
    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
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
    
    @media (max-width: 1024px) {
        .detalle-producto {
            grid-template-columns: 1fr;
        }
        
        .grid-relacionados {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .detalle-producto {
            padding: 20px;
        }
        
        .titulo-producto {
            font-size: 24px;
        }
        
        .precio-grande {
            font-size: 36px;
        }
        
        .imagen-principal {
            height: 300px;
        }
        
        .tabs-nav {
            flex-direction: column;
            gap: 0;
        }
        
        .tab-button {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .grid-relacionados {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="detalle-container">
    <div class="breadcrumbs">
        <a href="index.php">Inicio</a> / 
        <a href="productos.php">Productos</a> / 
        <span><?php echo htmlspecialchars($producto['NombreProducto']); ?></span>
    </div>

    <?php if (isset($_SESSION['exito'])): ?>
        <div class="alert alert-success">
            ✅ <?php echo htmlspecialchars($_SESSION['exito']); unset($_SESSION['exito']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            ❌ <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="detalle-producto">
        <div class="galeria-producto">
            <?php
            // Determinar la ruta de la imagen principal
            $rutaImagenDetalle = !empty($producto['ImagenPrincipal']) 
                ? '../../assets/img/productos/' . $producto['ImagenPrincipal']
                : 'https://via.placeholder.com/600x500/f5f5f5/666666?text=' . urlencode(substr($producto['NombreProducto'], 0, 20));
            ?>
            <img src="<?php echo $rutaImagenDetalle; ?>" 
                 alt="<?php echo htmlspecialchars($producto['NombreProducto']); ?>" 
                 class="imagen-principal"
                 onerror="this.src='https://via.placeholder.com/600x500/f5f5f5/666666?text=Sin+Imagen'">
        </div>

        <div class="info-producto">
            <h1 class="titulo-producto"><?php echo htmlspecialchars($producto['NombreProducto']); ?></h1>
            
            <p class="codigo-producto">
                <strong>Código:</strong> <?php echo htmlspecialchars($producto['CodigoProducto']); ?> |
                <strong>Marca:</strong> <?php echo htmlspecialchars($producto['NombreMarca'] ?? 'Sin marca'); ?> |
                <strong>Categoría:</strong> <?php echo htmlspecialchars($producto['NombreCategoria'] ?? 'General'); ?>
            </p>

            <div class="precio-container">
                <div class="precio-grande">$<?php echo number_format($producto['PrecioUnitario'], 2); ?></div>
            </div>

            <div class="disponibilidad <?php echo $producto['Disponible'] ? 'disponible' : 'agotado'; ?>">
                <?php if ($producto['Disponible']): ?>
                    ✅ Disponible - <?php echo $producto['CantidadDisponible']; ?> unidades en stock
                <?php else: ?>
                    ❌ Producto agotado temporalmente
                <?php endif; ?>
            </div>

            <?php if ($producto['Disponible']): ?>
                <form method="POST" action="../../controllers/CarritoController.php?action=agregar" id="formAgregarCarrito">
                    <input type="hidden" name="id_producto" value="<?php echo $producto['IdProducto']; ?>">
                    
                    <div class="selector-cantidad">
                        <label>Cantidad:</label>
                        <div class="cantidad-controls">
                            <button type="button" class="btn-cantidad" onclick="cambiarCantidad(-1)">-</button>
                            <input type="number" 
                                   name="cantidad" 
                                   id="cantidad" 
                                   value="1" 
                                   min="1" 
                                   max="<?php echo $producto['CantidadDisponible']; ?>" 
                                   class="input-cantidad"
                                   readonly>
                            <button type="button" class="btn-cantidad" onclick="cambiarCantidad(1)">+</button>
                        </div>
                        <span style="color: #666; font-size: 14px;">
                            Máximo: <?php echo $producto['CantidadDisponible']; ?> unidades
                        </span>
                    </div>

                    <div class="acciones-producto">
                        <button type="submit" class="btn-agregar-carrito">
                            🛒 Agregar al Carrito
                        </button>
                        
                        <button type="button" class="btn-comprar-ahora" onclick="comprarAhora()">
                            ⚡ Comprar Ahora
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="acciones-producto">
                    <button class="btn-agregar-carrito" disabled>
                        ❌ Producto No Disponible
                    </button>
                </div>
            <?php endif; ?>

            <div class="info-adicional">
                <h4> Información de Envío</h4>
                <ul>
                    <li>Envío a domicilio: $50.00 (3-5 días hábiles)</li>
                    <li>Recoger en sucursal: GRATIS (24 horas)</li>
                    <li>Pago contra entrega disponible</li>
                    <li>↩Devoluciones dentro de 30 días</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="tabs-container">
        <div class="tabs-nav">
            <button class="tab-button active" onclick="cambiarTab(event, 'descripcion')">
                Descripción
            </button>
            <button class="tab-button" onclick="cambiarTab(event, 'especificaciones')">
                Especificaciones
            </button>
            <button class="tab-button" onclick="cambiarTab(event, 'envio')">
                Envío y Devoluciones
            </button>
        </div>

        <div id="descripcion" class="tab-content active">
            <div class="descripcion-producto">
                <h3>Descripción del Producto</h3>
                <p><?php echo nl2br(htmlspecialchars($producto['Descripcion'] ?? 'Sin descripción disponible.')); ?></p>
            </div>
        </div>

        <div id="especificaciones" class="tab-content">
            <div class="descripcion-producto">
                <h3>Especificaciones Técnicas</h3>
                <ul>
                    <li><strong>Código:</strong> <?php echo htmlspecialchars($producto['CodigoProducto']); ?></li>
                    <li><strong>Marca:</strong> <?php echo htmlspecialchars($producto['NombreMarca'] ?? 'Sin marca'); ?></li>
                    <li><strong>Categoría:</strong> <?php echo htmlspecialchars($producto['NombreCategoria'] ?? 'General'); ?></li>
                    <li><strong>Disponibilidad:</strong> <?php echo $producto['Disponible'] ? 'En stock' : 'Agotado'; ?></li>
                    <li><strong>Unidades disponibles:</strong> <?php echo $producto['CantidadDisponible']; ?></li>
                </ul>
            </div>
        </div>

        <div id="envio" class="tab-content">
            <div class="descripcion-producto">
                <h3>Política de Envío</h3>
                <p>Ofrecemos dos opciones de entrega:</p>
                <ul>
                    <li><strong>Envío a domicilio:</strong> $50.00 - Entrega en 3-5 días hábiles</li>
                    <li><strong>Recoger en sucursal:</strong> GRATIS - Disponible en 24 horas</li>
                </ul>
                
                <h3>Política de Devoluciones</h3>
                <ul>
                    <li>Tienes 30 días para devolver tu producto</li>
                    <li>El producto debe estar sin usar y en su empaque original</li>
                    <li>Reembolso completo del valor del producto</li>
                </ul>
            </div>
        </div>
    </div>

    <?php if (!empty($productosRelacionados)): ?>
    <div class="productos-relacionados">
        <h2>🔗 Productos Relacionados</h2>
        <div class="grid-relacionados">
            <?php foreach ($productosRelacionados as $relacionado): ?>
                <a href="producto_detalle.php?id=<?php echo $relacionado['IdProducto']; ?>" class="producto-relacionado">
                    <?php
                    // Determinar la ruta de imagen para productos relacionados
                    $rutaImagenRelacionado = !empty($relacionado['ImagenPrincipal']) 
                        ? '../../assets/img/productos/' . $relacionado['ImagenPrincipal']
                        : 'https://via.placeholder.com/200x150/f5f5f5/666666?text=' . urlencode(substr($relacionado['NombreProducto'], 0, 10));
                    ?>
                    <img src="<?php echo $rutaImagenRelacionado; ?>" 
                         alt="<?php echo htmlspecialchars($relacionado['NombreProducto']); ?>"
                         onerror="this.src='https://via.placeholder.com/200x150/f5f5f5/666666?text=Sin+Imagen'">
                    <h4><?php echo htmlspecialchars($relacionado['NombreProducto']); ?></h4>
                    <p class="precio">$<?php echo number_format($relacionado['PrecioUnitario'], 2); ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<script>
function cambiarCantidad(cambio) {
    const input = document.getElementById('cantidad');
    let cantidad = parseInt(input.value);
    const max = parseInt(input.max);
    const min = parseInt(input.min);
    
    cantidad += cambio;
    
    if (cantidad >= min && cantidad <= max) {
        input.value = cantidad;
    }
}

function cambiarTab(event, tabId) {
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    document.getElementById(tabId).classList.add('active');
    event.target.classList.add('active');
}

function comprarAhora() {
    const form = document.getElementById('formAgregarCarrito');
    const formData = new FormData(form);
    
    fetch('../../controllers/CarritoController.php?action=agregar', {
        method: 'POST',
        body: formData
    })
    .then(() => {
        window.location.href = 'checkout.php';
    })
    .catch(error => {
        console.error('Error:', error);
        form.submit();
        setTimeout(() => {
            window.location.href = 'checkout.php';
        }, 500);
    });
}
</script>

<?php include 'includes/footer.php'; ?>