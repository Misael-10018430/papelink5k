<?php
require_once __DIR__ . '/../../controllers/ProductoController.php';
require_once __DIR__ . '/../../controllers/CategoriaController.php';
require_once __DIR__ . '/../../controllers/MarcaController.php';

$productoController = new ProductoController();
$categoriaController = new CategoriaController();
$marcaController = new MarcaController();

// Intentar obtener productos de la BD
try {
    $productosDestacados = $productoController->listarCliente();
    
    // Asegurar que siempre tengamos 5 productos para mostrar
    if (empty($productosDestacados) || count($productosDestacados) < 5) {
        // Si hay menos de 5 productos reales, completar con ejemplos
        $productosReales = $productosDestacados ?? [];
        $cantidadFaltante = 5 - count($productosReales);
        
        $productosEjemplo = [
            ['IdProducto' => 'ej1', 'NombreProducto' => 'Cuaderno Profesional', 'PrecioUnitario' => 45.00, 'Disponible' => true, 'CantidadDisponible' => 20],
            ['IdProducto' => 'ej2', 'NombreProducto' => 'Bolígrafos Gel (Pack 10)', 'PrecioUnitario' => 85.00, 'Disponible' => true, 'CantidadDisponible' => 15],
            ['IdProducto' => 'ej3', 'NombreProducto' => 'Carpeta con Broche', 'PrecioUnitario' => 28.00, 'Disponible' => true, 'CantidadDisponible' => 30],
            ['IdProducto' => 'ej4', 'NombreProducto' => 'Juego de Geometría', 'PrecioUnitario' => 65.00, 'Disponible' => true, 'CantidadDisponible' => 12],
            ['IdProducto' => 'ej5', 'NombreProducto' => 'Tijeras Escolares', 'PrecioUnitario' => 35.00, 'Disponible' => false, 'CantidadDisponible' => 0]
        ];
        
        // Combinar productos reales con ejemplos
        $productosDestacados = array_merge(
            $productosReales,
            array_slice($productosEjemplo, 0, $cantidadFaltante)
        );
    } else {
        // Si hay más de 5, solo tomar los primeros 5
         $productosDestacados = $productosDestacados;
    }
} catch (Exception $e) {
    // En caso de error, usar solo productos de ejemplo
    $productosDestacados = [
        ['IdProducto' => 'ej1', 'NombreProducto' => 'Cuaderno Profesional', 'PrecioUnitario' => 45.00, 'Disponible' => true, 'CantidadDisponible' => 20],
        ['IdProducto' => 'ej2', 'NombreProducto' => 'Bolígrafos Gel (Pack 10)', 'PrecioUnitario' => 85.00, 'Disponible' => true, 'CantidadDisponible' => 15],
        ['IdProducto' => 'ej3', 'NombreProducto' => 'Carpeta con Broche', 'PrecioUnitario' => 28.00, 'Disponible' => true, 'CantidadDisponible' => 30],
        ['IdProducto' => 'ej4', 'NombreProducto' => 'Juego de Geometría', 'PrecioUnitario' => 65.00, 'Disponible' => true, 'CantidadDisponible' => 12],
        ['IdProducto' => 'ej5', 'NombreProducto' => 'Tijeras Escolares', 'PrecioUnitario' => 35.00, 'Disponible' => false, 'CantidadDisponible' => 0]
    ];
}

// Intentar obtener categorías de la BD
try {
    $categorias = $categoriaController->listarActivas();
    
    // Asegurar que siempre tengamos exactamente 3 categorías para mostrar
    if (empty($categorias) || count($categorias) < 3) {
        // Categorías predefinidas del negocio
        $categoriasBase = [
            ['IdCategoria' => 1, 'NombreCategoria' => 'Escolar'],
            ['IdCategoria' => 2, 'NombreCategoria' => 'Oficina'],
            ['IdCategoria' => 3, 'NombreCategoria' => 'Arte']
        ];
        
        // Si hay algunas categorías reales, combinarlas
        if (!empty($categorias)) {
            $categorias = array_merge($categorias, $categoriasBase);
        } else {
            $categorias = $categoriasBase;
        }
    }
    
    // Siempre mostrar solo las primeras 3
    $categorias = array_slice($categorias, 0, 3);
    
} catch (Exception $e) {
    // En caso de error, usar categorías predefinidas
    $categorias = [
        ['IdCategoria' => 1, 'NombreCategoria' => 'Escolar'],
        ['IdCategoria' => 2, 'NombreCategoria' => 'Oficina'],
        ['IdCategoria' => 3, 'NombreCategoria' => 'Arte']
    ];
}

include 'includes/header.php';
?>

<!-- ESTILOS ESPECÍFICOS PARA ESTA PÁGINA -->
<style>
/* Reset del contenedor principal */
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

/* ========================================== */
/* BANNER HERO */
/* ========================================== */
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

.seccion-categorias,
.seccion-productos-destacados {
    min-height: 500px;
}

/* ========================================== */
/* SECCIÓN PRODUCTOS DESTACADOS - 2 FILAS INDEPENDIENTES */
/* ========================================== */
.seccion-productos-destacados {
    background-color: #e8e8e8;
    padding: 40px 20px;
    width: 100%;
}

.contenedor-interno {
    max-width: 1400px;
    margin: 0 auto;
}

.titulo-seccion {
    text-align: center;
    color: #000000ff;
    font-size: 32px;
    margin-bottom: 40px;
    font-weight: bold;
}

/* Contenedor principal de filas */
.grid-productos-horizontal {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

/* Cada fila con scroll independiente */
.fila-productos {
    display: flex;
    gap: 25px;
    overflow-x: auto;
    overflow-y: hidden;
    padding: 15px 5px;
    scroll-behavior: smooth;
    cursor: grab;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
}

/* Ocultar scrollbar en todos los navegadores */
.fila-productos::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}

.fila-productos:active {
    cursor: grabbing;
}

/* Card de producto */
.card-producto {
    background: linear-gradient(145deg, #ffffff, #f5f5f5);
    border-radius: 15px;
    padding: 25px;
    min-width: 300px;
    max-width: 300px;
    flex-shrink: 0;
    text-align: center;
    text-decoration: none;
    color: #333;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}

/* Efecto de brillo sutil 
.card-producto::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, 
        transparent, 
        rgba(255, 255, 255, 0.3), 
        transparent
    );
    transition: left 0.5s;
}*/

.card-producto:hover::before {
    left: 100%;
}

.card-producto:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 25px rgba(255, 99, 71, 0.2);
    background: linear-gradient(145deg, #ffffff, #fff8f7);
}

.card-producto img {
    width: 100%;
    height: 160px;
    object-fit: contain;
    border-radius: 10px;
    margin-bottom: 15px;
    background-color: #fafafa;
    padding: 12px;
    transition: transform 0.3s ease;
}

.card-producto:hover img {
    transform: scale(1.08);
}

.card-producto h3 {
    font-size: 15px;
    color: #2c3e50;
    margin-bottom: 12px;
    min-height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1.4;
    font-weight: 600;
}

.card-producto .precio {
    font-size: 24px;
    font-weight: bold;
    color: #FF6347;
    margin: 12px 0;
    text-shadow: 0 2px 4px rgba(255, 99, 71, 0.1);
}

.card-producto .btn-agregar {
    background: linear-gradient(135deg, #FF6347 0%, #ff7a5c 100%);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 25px;
    cursor: pointer;
    font-size: 13px;
    font-weight: bold;
    width: 100%;
    transition: all 0.3s ease;
    margin-top: auto;
    box-shadow: 0 4px 12px rgba(255, 99, 71, 0.3);
}

.card-producto .btn-agregar:hover {
    background: linear-gradient(135deg, #e5533d 0%, #ff6347 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(255, 99, 71, 0.4);
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

/* ========================================== */
/* SECCIÓN CATEGORÍAS */
/* ========================================== */
.seccion-categorias {
    background-color: #e8e8e8;
    padding: 60px 20px;
    width: 100%;
}

.titulo-seccion-blanco {
    text-align: center;
    color: #000000;
    font-size: 26px;
    margin-bottom: 40px;
    font-weight: bold;
}

.grid-categorias {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    max-width: 1400px;
    margin: 0 auto;
}

.card-categoria {
    text-align: center;
    text-decoration: none;
    display: block;
    transition: transform 0.3s;
}

.card-categoria:hover {
    transform: translateY(-8px);
}

.card-categoria img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    transition: all 0.3s;
}

.card-categoria:hover img {
    box-shadow: 0 10px 20px rgba(255, 99, 71, 0.3);
}

.btn-categoria {
    display: inline-block;
    background-color: #FF6347;
    color: white;
    padding: 15px 40px;
    border-radius: 30px;
    font-size: 18px;
    font-weight: bold;
    text-decoration: none;
    transition: all 0.3s;
    box-shadow: 0 4px 8px rgba(255, 99, 71, 0.3);
}

.btn-categoria:hover {
    background-color: #e5533d;
    transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(255, 99, 71, 0.5);
}

/* ========================================== */
/* RESPONSIVE */
/* ========================================== */
@media (max-width: 992px) {
    .grid-categorias {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .card-producto {
        min-width: 250px;
        max-width: 250px;
    }
}

@media (max-width: 768px) {
    .grid-categorias {
        grid-template-columns: 1fr;
    }
    
    .card-producto {
        min-width: 200px;
        max-width: 200px;
    }
    
    .titulo-seccion,
    .titulo-seccion-blanco {
        font-size: 24px;
    }
}
</style>

<!-- ============================================ -->
<!-- 1. BANNER PRINCIPAL -->
<!-- ============================================ -->
<div class="hero-banner">
    <img src="../../assets/img/banner_back_to_school.jpg" 
         alt="Back to School - Papelink" 
         onerror="this.src='https://www.auros.com.co/wp-content/uploads/2020/09/1banner-intertno-papeleria-1.jpg'">
</div>

<!-- ============================================ -->
<!-- 2. PRODUCTOS DESTACADOS -->
<!-- ============================================ -->
<!-- ============================================ -->
<!-- 2. PRODUCTOS DESTACADOS -->
<!-- ============================================ -->
<section class="seccion-productos-destacados">
    <div class="contenedor-interno">
        <h2 class="titulo-seccion">Productos Destacados</h2>
        
        <div class="grid-productos-horizontal">
            <?php 
            // Dividir productos en 2 filas
            $totalProductos = count($productosDestacados);
            $mitad = ceil($totalProductos / 2);
            
            $fila1 = array_slice($productosDestacados, 0, $mitad);
            $fila2 = array_slice($productosDestacados, $mitad);
            ?>
            
            <!-- FILA 1 -->
            <div class="fila-productos">
                <?php foreach ($fila1 as $producto): ?>
                    <a href="<?php echo isset($producto['IdProducto']) && is_numeric($producto['IdProducto']) ? 'producto_detalle.php?id=' . $producto['IdProducto'] : '#'; ?>" 
                       class="card-producto"
                       <?php echo !is_numeric($producto['IdProducto']) ? 'onclick="event.preventDefault(); alert(\'⚠️ Producto de ejemplo. Agrega productos reales desde el panel de administración.\');"' : ''; ?>>
                        
                        <?php
                        // Determinar la ruta de la imagen
                        if (!empty($producto['ImagenPrincipal'])) {
                            $rutaImagen = '../../assets/img/productos/' . $producto['ImagenPrincipal'];
                        } else {
                            $rutaImagen = 'https://via.placeholder.com/300x200/f5f5f5/666666?text=' . urlencode(substr($producto['NombreProducto'], 0, 15));
                        }
                        ?>
                        <img src="<?php echo $rutaImagen; ?>" 
                             alt="<?php echo htmlspecialchars($producto['NombreProducto']); ?>"
                             onerror="this.src='https://via.placeholder.com/300x200/f5f5f5/666666?text=Sin+Imagen'">
                        
                        <h3><?php echo htmlspecialchars($producto['NombreProducto']); ?></h3>
                        
                        <p class="precio">
                            $<?php echo number_format($producto['PrecioUnitario'], 2); ?>
                        </p>
                        
                        <?php if (is_numeric($producto['IdProducto'])): ?>
                            <button class="btn-agregar" 
                                    onclick="agregarAlCarrito(<?php echo $producto['IdProducto']; ?>, '<?php echo addslashes($producto['NombreProducto']); ?>')">
                                🛒 Agregar al carrito
                            </button>
                        <?php else: ?>
                            <button class="btn-agregar btn-agotado" 
                                    onclick="alert('⚠️ Producto de ejemplo. Agrega productos reales desde el panel de administración.')">
                                🛒 Agregar al carrito
                            </button>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- FILA 2 -->
            <?php if (!empty($fila2)): ?>
            <div class="fila-productos">
                <?php foreach ($fila2 as $producto): ?>
                    <a href="<?php echo isset($producto['IdProducto']) && is_numeric($producto['IdProducto']) ? 'producto_detalle.php?id=' . $producto['IdProducto'] : '#'; ?>" 
                       class="card-producto"
                       <?php echo !is_numeric($producto['IdProducto']) ? 'onclick="event.preventDefault(); alert(\'⚠️ Producto de ejemplo. Agrega productos reales desde el panel de administración.\');"' : ''; ?>>
                        
                        <?php
                        // Determinar la ruta de la imagen
                        if (!empty($producto['ImagenPrincipal'])) {
                            $rutaImagen = '../../assets/img/productos/' . $producto['ImagenPrincipal'];
                        } else {
                            $rutaImagen = 'https://via.placeholder.com/300x200/f5f5f5/666666?text=' . urlencode(substr($producto['NombreProducto'], 0, 15));
                        }
                        ?>
                        <img src="<?php echo $rutaImagen; ?>" 
                             alt="<?php echo htmlspecialchars($producto['NombreProducto']); ?>"
                             onerror="this.src='https://via.placeholder.com/300x200/f5f5f5/666666?text=Sin+Imagen'">
                        
                        <h3><?php echo htmlspecialchars($producto['NombreProducto']); ?></h3>
                        
                        <p class="precio">
                            $<?php echo number_format($producto['PrecioUnitario'], 2); ?>
                        </p>
                        
                        <?php if (is_numeric($producto['IdProducto'])): ?>
                            <button class="btn-agregar" 
                                    onclick="agregarAlCarrito(<?php echo $producto['IdProducto']; ?>, '<?php echo addslashes($producto['NombreProducto']); ?>')">
                                🛒 Agregar al carrito
                            </button>
                        <?php else: ?>
                            <button class="btn-agregar btn-agotado" 
                                    onclick="alert('⚠️ Producto de ejemplo. Agrega productos reales desde el panel de administración.')">
                                🛒 Agregar al carrito
                            </button>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 3. CATEGORÍAS PRINCIPALES -->
<!-- ============================================ -->
<section class="seccion-categorias">
    <div class="contenedor-interno">
        <h2 class="titulo-seccion-blanco">Compra por Categoría</h2>
        
        <div class="grid-categorias">
            <?php 
            // Imágenes específicas para cada categoría
            $imagenesCategoria = [
                'Escolar' => 'https://10mejores.es/wp-content/uploads/2018/10/banner-papeleria.jpg',
                'Oficina' => 'https://www.desmon.com/wp-content/uploads/2022/10/decorar-paredes-de-oficina.jpg',
                'Arte' => 'https://img77.uenicdn.com/image/upload/v1588922645/business/81239b97-f89e-4275-8e73-da010838b6b0.jpg'
            ];
            
            foreach ($categorias as $categoria): 
                $nombreCat = $categoria['NombreCategoria'];
                $imagenUrl = $imagenesCategoria[$nombreCat] ?? 'https://via.placeholder.com/500x250/34495e/ffffff?text=' . urlencode($nombreCat);
            ?>
                <div class="card-categoria">
                    <a href="productos.php?categoria=<?php echo $categoria['IdCategoria']; ?>">
                        <img src="<?php echo $imagenUrl; ?>" 
                             alt="<?php echo htmlspecialchars($nombreCat); ?>">
                        
                        <span class="btn-categoria">
                            <?php echo htmlspecialchars($nombreCat); ?>
                        </span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
// Función para agregar al carrito
function agregarAlCarrito(idProducto, nombreProducto) {
    // Verificar si el usuario está logueado
    <?php if (!isset($_SESSION['cliente_id'])): ?>
        alert('Debes iniciar sesión para agregar productos al carrito');
        window.location.href = 'login.php';
        return;
    <?php endif; ?>
    
    // Enviar petición al servidor
    fetch('../../controllers/CarritoController.php?action=agregar', {
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
    fetch('../../controllers/CarritoController.php?action=contar')
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
</script>

<?php include 'includes/footer.php'; ?>