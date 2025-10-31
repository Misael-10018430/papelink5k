<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/ProductoController.php';
require_once __DIR__ . '/../../controllers/CategoriaController.php';
require_once __DIR__ . '/../../controllers/MarcaController.php';

$productoController = new ProductoController();
$categoriaController = new CategoriaController();
$marcaController = new MarcaController();

// Obtener productos destacados de la BD
$productosDestacados = [];
$categorias = [];

try {
    // Obtener productos reales de la base de datos
    $productosDestacados = $productoController->listarCliente();
    
    // Limitar a máximo 10 productos para las 2 filas
    if (count($productosDestacados) > 10) {
        $productosDestacados = array_slice($productosDestacados, 0, 10);
    }
    
} catch (Exception $e) {
    error_log("Error al cargar productos: " . $e->getMessage());
    $productosDestacados = [];
}

try {
    // Obtener categorías activas de la BD
    $categorias = $categoriaController->listarActivas();
    
    // Limitar a las primeras 3 categorías
    if (count($categorias) > 3) {
        $categorias = array_slice($categorias, 0, 3);
    }
    
} catch (Exception $e) {
    error_log("Error al cargar categorías: " . $e->getMessage());
    $categorias = [];
}

$titulo = "Papelink - Papelería y Oficina";
include 'includes/header.php';
?>

<!-- ESTILOS ESPECÍFICOS PARA ESTA PÁGINA -->
<style>
/* Reset del contenedor principal */
.titulo-seccion,
.titulo-seccion-blanco {
    color: #000000 !important;
}

.contenedor-principal {
    max-width: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
}

.hero-banner {
    width: 100%;
    margin: 0;
    padding: 0;
}

.hero-banner img {
    width: 100%;
    height: auto;
    display: block;
    max-height: 400px;
    object-fit: cover;
}

.seccion-productos-destacados {
    background-color: #ffffff;
    padding: 60px 20px;
    width: 100%;
    position: relative;
    overflow: hidden;
    min-height: 400px;
}

.contenedor-interno {
    max-width: 1600px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.titulo-seccion {
    text-align: center;
    color: #2c3e50;
    font-size: 36px;
    margin-bottom: 50px;
    font-weight: 700;
    position: relative;
    display: inline-block;
    width: 100%;
}

.titulo-seccion::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #FF6347, #ff7a5c);
    border-radius: 2px;
}

.mensaje-sin-productos {
    text-align: center;
    padding: 80px 20px;
    color: #7f8c8d;
}

.mensaje-sin-productos i {
    font-size: 80px;
    color: #bdc3c7;
    margin-bottom: 20px;
}

.mensaje-sin-productos h3 {
    font-size: 24px;
    color: #2c3e50;
    margin-bottom: 10px;
}

.mensaje-sin-productos p {
    font-size: 16px;
    color: #95a5a6;
}

.grid-productos-horizontal {
    display: flex;
    flex-direction: column;
    gap: 40px;
}

.fila-container {
    position: relative;
    width: 100%;
}

.fila-productos {
    display: flex;
    gap: 25px;
    overflow-x: hidden;
    overflow-y: hidden;
    padding: 20px 60px 40px;
    scroll-behavior: smooth;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.fila-productos::-webkit-scrollbar {
    display: none;
}

.flecha-navegacion {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    background-color: #FF6347;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    font-size: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.flecha-navegacion:hover {
    background-color: #e5533d;
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
}

.flecha-izquierda {
    left: -100px;
}

.flecha-derecha {
    right: -100px;
}

.card-producto {
    background-color: #e9ecef;
    border-radius: 16px;
    min-width: 320px;
    max-width: 320px;
    flex-shrink: 0;
    text-align: center;
    text-decoration: none;
    color: #333;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}

.card-producto:hover {
    transform: translateY(-12px);
    box-shadow: 0 20px 40px rgba(255, 99, 71, 0.15);
}

.producto-imagen-container {
    position: relative;
    overflow: hidden;
    height: 240px;
    border-radius: 16px 16px 0 0;
    background-color: #f8f9fa;
}

.card-producto img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform 0.5s ease;
    padding: 20px;
}

.card-producto:hover img {
    transform: scale(1.05);
}

.producto-etiqueta {
    position: absolute;
    top: 15px;
    right: 15px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
    z-index: 2;
}

.etiqueta-nuevo {
    background-color: #2ecc71;
    color: white;
}

.etiqueta-descuento {
    background-color: #e74c3c;
    color: white;
}

.card-producto-content {
    padding: 20px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.card-producto h3 {
    font-size: 16px;
    color: #2c3e50;
    margin-bottom: 12px;
    min-height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1.4;
    font-weight: 600;
}

.precio-container {
    margin: 10px 0 15px;
}

.card-producto .precio {
    font-size: 24px;
    font-weight: 700;
    color: #FF6347;
    margin: 0;
}

.card-producto .precio-regular {
    font-size: 16px;
    color: #95a5a6;
    text-decoration: line-through;
    margin-right: 8px;
}

.card-producto .precio-descuento {
    font-size: 14px;
    color: #e74c3c;
    font-weight: 600;
}

.stock-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
    font-size: 13px;
}

.stock-disponible {
    color: #27ae60;
}

.stock-agotado {
    color: #e74c3c;
}

.stock-indicator i {
    margin-right: 5px;
}

.card-producto .btn-agregar {
    background: linear-gradient(135deg, #FF6347 0%, #ff7a5c 100%);
    color: white;
    border: none;
    padding: 14px 20px;
    border-radius: 30px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    width: 100%;
    transition: all 0.3s ease;
    margin-top: auto;
    box-shadow: 0 4px 15px rgba(255, 99, 71, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.card-producto .btn-agregar:hover {
    background: linear-gradient(135deg, #e5533d 0%, #ff6347 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 99, 71, 0.4);
}

.card-producto .btn-agregar:active {
    transform: translateY(0);
}

.card-producto .btn-agotado {
    background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
    cursor: not-allowed;
    box-shadow: none;
}

.card-producto .btn-agotado:hover {
    transform: none;
}

.seccion-categorias {
    background-color: #ffffff;
    padding: 70px 20px;
    width: 100%;
    position: relative;
    overflow: hidden;
    min-height: 400px;
}

.titulo-seccion-blanco {
    text-align: center;
    color: #2c3e50;
    font-size: 36px;
    margin-bottom: 50px;
    font-weight: 700;
    position: relative;
    display: inline-block;
    width: 100%;
}

.titulo-seccion-blanco::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #FF6347, #ff7a5c);
    border-radius: 2px;
}

.mensaje-sin-categorias {
    text-align: center;
    padding: 80px 20px;
    color: #7f8c8d;
}

.mensaje-sin-categorias i {
    font-size: 80px;
    color: #bdc3c7;
    margin-bottom: 20px;
}

.mensaje-sin-categorias h3 {
    font-size: 24px;
    color: #2c3e50;
    margin-bottom: 10px;
}

.mensaje-sin-categorias p {
    font-size: 16px;
    color: #95a5a6;
}

.grid-categorias {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    max-width: 1600px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.card-categoria {
    text-align: center;
    text-decoration: none;
    display: block;
    transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    overflow: hidden;
    border-radius: 16px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.card-categoria:hover {
    transform: translateY(-10px);
}

.card-categoria::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(0deg, rgba(44, 62, 80, 0.8) 0%, rgba(44, 62, 80, 0.4) 50%, rgba(44, 62, 80, 0.1) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 1;
}

.card-categoria:hover::before {
    opacity: 1;
}

.card-categoria img {
    width: 100%;
    height: 280px;
    object-fit: cover;
    border-radius: 16px;
    transition: transform 0.5s ease;
}

.card-categoria:hover img {
    transform: scale(1.05);
}

.btn-categoria {
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #FF6347;
    color: white;
    padding: 14px 30px;
    border-radius: 30px;
    font-size: 16px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(255, 99, 71, 0.3);
    z-index: 2;
    opacity: 0;
    transform: translateX(-50%) translateY(20px);
}

.card-categoria:hover .btn-categoria {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
}

.btn-categoria:hover {
    background-color: #e5533d;
    box-shadow: 0 6px 20px rgba(255, 99, 71, 0.5);
}

.categoria-nombre {
    position: absolute;
    top: 30px;
    left: 50%;
    transform: translateX(-50%);
    color: white;
    font-size: 24px;
    font-weight: 700;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    z-index: 2;
    opacity: 0;
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.card-categoria:hover .categoria-nombre {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
}

@media (max-width: 992px) {
    .grid-categorias {
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
    }
    .card-producto {
        min-width: 280px;
        max-width: 280px;
    }
    .titulo-seccion,
    .titulo-seccion-blanco {
        font-size: 32px;
    }
}

@media (max-width: 768px) {
    .grid-categorias {
        grid-template-columns: 1fr;
    }
    .card-producto {
        min-width: 240px;
        max-width: 240px;
    }
    .titulo-seccion,
    .titulo-seccion-blanco {
        font-size: 28px;
    }
    .seccion-productos-destacados {
        padding: 40px 15px;
    }
    .seccion-categorias {
        padding: 50px 15px;
    }
    .flecha-navegacion {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
    .flecha-izquierda {
        left: -50px;
    }
    .flecha-derecha {
        right: -50px;
    }
}
</style>

<!-- SECCIÓN PRODUCTOS DESTACADOS -->
<section class="seccion-productos-destacados">
    <div class="contenedor-interno">
        <h2 class="titulo-seccion">Productos Destacados</h2>

        <?php if (empty($productosDestacados)): ?>
            <div class="mensaje-sin-productos">
                <i class="fas fa-box-open"></i>
                <h3>No hay productos disponibles</h3>
                <p>Actualmente no tenemos productos en inventario. Por favor, vuelve más tarde.</p>
            </div>
        <?php else: ?>
            <div class="grid-productos-horizontal">
                <?php
                // Dividir productos en 2 filas
                $totalProductos = count($productosDestacados);
                $mitad = ceil($totalProductos / 2);

                $fila1 = array_slice($productosDestacados, 0, $mitad);
                $fila2 = array_slice($productosDestacados, $mitad);
                ?>

                <!-- FILA 1 -->
                <div class="fila-container">
                    <div class="flecha-navegacion flecha-izquierda" onclick="moverFila('fila1', -1)">
                        <i class="fas fa-chevron-left"></i>
                    </div>
                    <div class="flecha-navegacion flecha-derecha" onclick="moverFila('fila1', 1)">
                        <i class="fas fa-chevron-right"></i>
                    </div>

                    <div class="fila-productos" id="fila1">
                        <?php foreach ($fila1 as $producto): ?>
                            <a href="producto_detalle.php?id=<?php echo $producto['IdProducto']; ?>" class="card-producto">
                                <div class="producto-imagen-container">
                                    <?php
                                    if (!empty($producto['ImagenPrincipal'])) {
                                        $rutaImagen = asset('assets/img/productos/' . $producto['ImagenPrincipal']);
                                    } else {
                                        $rutaImagen = 'https://via.placeholder.com/300x200?text=' . urlencode($producto['NombreProducto']);
                                    }
                                    ?>
                                    <img src="<?php echo $rutaImagen; ?>"
                                         alt="<?php echo htmlspecialchars($producto['NombreProducto']); ?>"
                                         onerror="this.src='https://via.placeholder.com/300x200?text=Producto'">

                                    <?php if (isset($producto['Nuevo']) && $producto['Nuevo']): ?>
                                        <span class="producto-etiqueta etiqueta-nuevo">Nuevo</span>
                                    <?php endif; ?>

                                    <?php if (isset($producto['Descuento']) && $producto['Descuento'] > 0): ?>
                                        <span class="producto-etiqueta etiqueta-descuento">-<?php echo $producto['Descuento']; ?>%</span>
                                    <?php endif; ?>
                                </div>

                                <div class="card-producto-content">
                                    <h3><?php echo htmlspecialchars($producto['NombreProducto']); ?></h3>

                                    <div class="precio-container">
                                        <?php if (isset($producto['Descuento']) && $producto['Descuento'] > 0): ?>
                                            <span class="precio-regular">$<?php echo number_format($producto['PrecioUnitario'], 2); ?></span>
                                            <span class="precio">$<?php echo number_format($producto['PrecioUnitario'] * (1 - $producto['Descuento']/100), 2); ?></span>
                                            <span class="precio-descuento">Ahorra <?php echo $producto['Descuento']; ?>%</span>
                                        <?php else: ?>
                                            <span class="precio">$<?php echo number_format($producto['PrecioUnitario'], 2); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="stock-indicator <?php echo $producto['Disponible'] ? 'stock-disponible' : 'stock-agotado'; ?>">
                                        <i class="fas fa-<?php echo $producto['Disponible'] ? 'check-circle' : 'times-circle'; ?>"></i>
                                        <?php echo $producto['Disponible'] ? 'Disponible (' . $producto['CantidadDisponible'] . ' unidades)' : 'Agotado'; ?>
                                    </div>

                                    <button class="btn-agregar <?php echo !$producto['Disponible'] ? 'btn-agotado' : ''; ?>"
                                            <?php echo $producto['Disponible'] ? 'onclick="event.preventDefault(); agregarAlCarrito(' . $producto['IdProducto'] . ', \'' . addslashes($producto['NombreProducto']) . '\')"' : 'disabled'; ?>>
                                        <i class="fas fa-shopping-cart"></i> 
                                        <?php echo $producto['Disponible'] ? 'Agregar al carrito' : 'Agotado'; ?>
                                    </button>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- FILA 2 -->
                <?php if (!empty($fila2)): ?>
                    <div class="fila-container">
                        <div class="flecha-navegacion flecha-izquierda" onclick="moverFila('fila2', -1)">
                            <i class="fas fa-chevron-left"></i>
                        </div>
                        <div class="flecha-navegacion flecha-derecha" onclick="moverFila('fila2', 1)">
                            <i class="fas fa-chevron-right"></i>
                        </div>

                        <div class="fila-productos" id="fila2">
                            <?php foreach ($fila2 as $producto): ?>
                                <a href="producto_detalle.php?id=<?php echo $producto['IdProducto']; ?>" class="card-producto">
                                    <div class="producto-imagen-container">
                                        <?php
                                        if (!empty($producto['ImagenPrincipal'])) {
                                            $rutaImagen = asset('assets/img/productos/' . $producto['ImagenPrincipal']);
                                        } else {
                                            $rutaImagen = 'https://via.placeholder.com/300x200?text=' . urlencode($producto['NombreProducto']);
                                        }
                                        ?>
                                        <img src="<?php echo $rutaImagen; ?>"
                                             alt="<?php echo htmlspecialchars($producto['NombreProducto']); ?>"
                                             onerror="this.src='https://via.placeholder.com/300x200?text=Producto'">

                                        <?php if (isset($producto['Nuevo']) && $producto['Nuevo']): ?>
                                            <span class="producto-etiqueta etiqueta-nuevo">Nuevo</span>
                                        <?php endif; ?>

                                        <?php if (isset($producto['Descuento']) && $producto['Descuento'] > 0): ?>
                                            <span class="producto-etiqueta etiqueta-descuento">-<?php echo $producto['Descuento']; ?>%</span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="card-producto-content">
                                        <h3><?php echo htmlspecialchars($producto['NombreProducto']); ?></h3>

                                        <div class="precio-container">
                                            <?php if (isset($producto['Descuento']) && $producto['Descuento'] > 0): ?>
                                                <span class="precio-regular">$<?php echo number_format($producto['PrecioUnitario'], 2); ?></span>
                                                <span class="precio">$<?php echo number_format($producto['PrecioUnitario'] * (1 - $producto['Descuento']/100), 2); ?></span>
                                                <span class="precio-descuento">Ahorra <?php echo $producto['Descuento']; ?>%</span>
                                            <?php else: ?>
                                                <span class="precio">$<?php echo number_format($producto['PrecioUnitario'], 2); ?></span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="stock-indicator <?php echo $producto['Disponible'] ? 'stock-disponible' : 'stock-agotado'; ?>">
                                            <i class="fas fa-<?php echo $producto['Disponible'] ? 'check-circle' : 'times-circle'; ?>"></i>
                                            <?php echo $producto['Disponible'] ? 'Disponible (' . $producto['CantidadDisponible'] . ' unidades)' : 'Agotado'; ?>
                                        </div>

                                        <button class="btn-agregar <?php echo !$producto['Disponible'] ? 'btn-agotado' : ''; ?>"
                                                <?php echo $producto['Disponible'] ? 'onclick="event.preventDefault(); agregarAlCarrito(' . $producto['IdProducto'] . ', \'' . addslashes($producto['NombreProducto']) . '\')"' : 'disabled'; ?>>
                                            <i class="fas fa-shopping-cart"></i> 
                                            <?php echo $producto['Disponible'] ? 'Agregar al carrito' : 'Agotado'; ?>
                                        </button>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- SECCIÓN CATEGORÍAS PRINCIPALES -->
<section class="seccion-categorias">
    <div class="contenedor-interno">
        <h2 class="titulo-seccion-blanco">Compra por Categoría</h2>

        <?php if (empty($categorias)): ?>
            <div class="mensaje-sin-categorias">
                <i class="fas fa-folder-open"></i>
                <h3>No hay categorías disponibles</h3>
                <p>Actualmente no tenemos categorías configuradas. Por favor, vuelve más tarde.</p>
            </div>
        <?php else: ?>
            <div class="grid-categorias">
                <?php
                $imagenesCategoria = [
                    'Escolar' => 'https://as1.ftcdn.net/v2/jpg/03/61/04/32/1000_F_361043232_teu7Amyr5GsxKS1HsbSh3WRY6ExBKyQ3.jpg',
                    'Oficina' => 'https://tse3.mm.bing.net/th/id/OIP.r42540i1ISVnP2kt8Wf8-wHaE-?rs=1&pid=ImgDetMain',
                    'Arte' => 'https://img.freepik.com/foto-gratis/conjunto-materiales-arte-pintura-abstracta_23-2147895411.jpg'
                ];

                foreach ($categorias as $categoria):
                    $nombreCat = $categoria['NombreCategoria'];
                    $imagenUrl = $imagenesCategoria[$nombreCat] ?? 'https://via.placeholder.com/400x280?text=' . urlencode($nombreCat);
                ?>
                    <div class="card-categoria">
                        <a href="productos.php?categoria=<?php echo $categoria['IdCategoria']; ?>">
                            <img src="<?php echo $imagenUrl; ?>"
                                 alt="<?php echo htmlspecialchars($nombreCat); ?>">

                            <div class="categoria-nombre"><?php echo htmlspecialchars($nombreCat); ?></div>
                            <span class="btn-categoria">Ver productos</span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<script>
// Función para mover las filas de productos
function moverFila(filaId, direccion) {
    const fila = document.getElementById(filaId);
    const scrollAmount = 345; // Ancho de la tarjeta + gap

    const nuevaPosicion = fila.scrollLeft + (scrollAmount * direccion);

    fila.scrollTo({
        left: nuevaPosicion,
        behavior: 'smooth'
    });
}

// Función para agregar al carrito
function agregarAlCarrito(idProducto, nombreProducto) {
    // Verificar si el usuario está logueado
    <?php if (!isset($_SESSION['cliente_id'])): ?>
        alert('Debes iniciar sesión para agregar productos al carrito');
        window.location.href = 'login.php';
        return;
    <?php endif; ?>

    // Enviar petición al servidor
    fetch('<?php echo BASE_URL; ?>controllers/CarritoController.php?action=agregar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id_producto=' + idProducto + '&cantidad=1'
    })
    .then(response => response.text())
    .then(data => {
        alert('✅ ' + nombreProducto + ' agregado al carrito');
        actualizarContadorCarrito();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Error al agregar el producto');
    });
}

// Función para actualizar el contador del carrito
function actualizarContadorCarrito() {
    fetch('<?php echo BASE_URL; ?>controllers/CarritoController.php?action=contar')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('carritoBadge');
            if (badge && data.cantidad > 0) {
                badge.textContent = data.cantidad;
                badge.style.display = 'block';
            }
        })
        .catch(error => console.log('Error al actualizar contador:', error));
}

// Inicializar las filas
document.addEventListener('DOMContentLoaded', function() {
    const filas = document.querySelectorAll('.fila-productos');
    filas.forEach(fila => {
        fila.scrollLeft = 0;
    });
});
</script>

<?php include 'includes/footer.php'; ?>