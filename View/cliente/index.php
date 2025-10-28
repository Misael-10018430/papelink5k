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
            ['IdProducto' => 'ej1', 'NombreProducto' => 'Cuaderno Profesional', 'PrecioUnitario' => 45.00, 'Disponible' => true, 'CantidadDisponible' => 20, 'Descuento' => 10],
            ['IdProducto' => 'ej2', 'NombreProducto' => 'Bolígrafos Gel (Pack 10)', 'PrecioUnitario' => 85.00, 'Disponible' => true, 'CantidadDisponible' => 15, 'Nuevo' => true],
            ['IdProducto' => 'ej3', 'NombreProducto' => 'Carpeta con Broche', 'PrecioUnitario' => 28.00, 'Disponible' => true, 'CantidadDisponible' => 30],
            ['IdProducto' => 'ej4', 'NombreProducto' => 'Juego de Geometría', 'PrecioUnitario' => 65.00, 'Disponible' => true, 'CantidadDisponible' => 12, 'Descuento' => 15],
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
        ['IdProducto' => 'ej1', 'NombreProducto' => 'Cuaderno Profesional', 'PrecioUnitario' => 45.00, 'Disponible' => true, 'CantidadDisponible' => 20, 'Descuento' => 10],
        ['IdProducto' => 'ej2', 'NombreProducto' => 'Bolígrafos Gel (Pack 10)', 'PrecioUnitario' => 85.00, 'Disponible' => true, 'CantidadDisponible' => 15, 'Nuevo' => true],
        ['IdProducto' => 'ej3', 'NombreProducto' => 'Carpeta con Broche', 'PrecioUnitario' => 28.00, 'Disponible' => true, 'CantidadDisponible' => 30],
        ['IdProducto' => 'ej4', 'NombreProducto' => 'Juego de Geometría', 'PrecioUnitario' => 65.00, 'Disponible' => true, 'CantidadDisponible' => 12, 'Descuento' => 15],
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
/* SECCIÓN PRODUCTOS DESTACADOS - DISEÑO PROFESIONAL */
/* ========================================== */
.seccion-productos-destacados {
    background-color: #ffffff; /* Cambiado a blanco puro */
    padding: 60px 20px;
    width: 100%;
    position: relative;
    overflow: hidden;
}

.contenedor-interno {
    max-width: 1600px; /* Aumentado para ocupar más ancho */
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.titulo-seccion {
    text-align: center;
    color: #2c3e50; /* Mantenido oscuro para contraste con fondo blanco */
    font-size: 36px;
    margin-bottom: 50px;
    font-weight: 700;
    position: relative;
    display: inline-block;
    width: 100%;
}

/* Subrayado decorativo del título */
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

/* Contenedor principal de filas */
.grid-productos-horizontal {
    display: flex;
    flex-direction: column;
    gap: 40px;
}

/* Contenedor para cada fila con flechas */
.fila-container {
    position: relative;
    width: 100%;
}

/* Cada fila con scroll oculto */
.fila-productos {
    display: flex;
    gap: 25px; /* Reducido para aprovechar mejor el espacio */
    overflow-x: hidden; /* Cambiado de auto a hidden */
    overflow-y: hidden;
    padding: 20px 60px 40px; /* Aumentado el padding lateral para espacio para flechas */
    scroll-behavior: smooth;
    cursor: default; /* Cambiado de grab a default */
    scrollbar-width: none; /* Ocultar scrollbar en Firefox */
    -ms-overflow-style: none; /* Ocultar scrollbar en IE y Edge */
}

/* Ocultar scrollbar en Chrome, Safari, Opera */
.fila-productos::-webkit-scrollbar {
    display: none; /* Ocultar scrollbar completamente */
}

/* Flechas de navegación */
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

/* Card de producto - Diseño Profesional */
.card-producto {
    background-color: #e9ecef; /* Cambiado a un gris más marcado */
    border-radius: 16px;
    min-width: 320px; /* Aumentado de 280px */
    max-width: 320px; /* Aumentado de 280px */
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

/* Contenedor de imagen del producto */
.producto-imagen-container {
    position: relative;
    overflow: hidden;
    height: 240px; /* Aumentado ligeramente para mantener proporción */
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

/* Etiquetas de producto (Nuevo, Descuento) */
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

/* Contenido de la tarjeta */
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

/* Contenedor de precio */
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

/* Indicador de stock */
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

/* Botones de acción */
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

/* ========================================== */
/* SECCIÓN CATEGORÍAS - DISEÑO PROFESIONAL */
/* ========================================== */
.seccion-categorias {
    background-color: #ffffff; /* Cambiado a blanco puro */
    padding: 70px 20px;
    width: 100%;
    position: relative;
    overflow: hidden;
}

.titulo-seccion-blanco {
    text-align: center;
    color: #2c3e50; /* Cambiado a oscuro para contraste con fondo blanco */
    font-size: 36px;
    margin-bottom: 50px;
    font-weight: 700;
    position: relative;
    display: inline-block;
    width: 100%;
}

/* Subrayado decorativo del título */
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

.grid-categorias {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    max-width: 1600px; /* Aumentado para coincidir con productos */
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

/* Efecto de superposición al hacer hover */
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

/* Nombre de la categoría */
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

/* ========================================== */
/* RESPONSIVE */
/* ========================================== */
@media (max-width: 992px) {
    .grid-categorias {
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
    }
    
    .card-producto {
        min-width: 280px; /* Ajustado para pantallas medianas */
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
        min-width: 240px; /* Ajustado para pantallas pequeñas */
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
            <div class="fila-container">
                <div class="flecha-navegacion flecha-izquierda" onclick="moverFila('fila1', -1)">
                    <i class="fas fa-chevron-left"></i>
                </div>
                <div class="flecha-navegacion flecha-derecha" onclick="moverFila('fila1', 1)">
                    <i class="fas fa-chevron-right"></i>
                </div>
                
                <div class="fila-productos" id="fila1">
                    <?php foreach ($fila1 as $producto): ?>
                        <a href="<?php echo isset($producto['IdProducto']) && is_numeric($producto['IdProducto']) ? 'producto_detalle.php?id=' . $producto['IdProducto'] : '#'; ?>" 
                           class="card-producto"
                           <?php echo !is_numeric($producto['IdProducto']) ? 'onclick="event.preventDefault(); alert(\'⚠️ Producto de ejemplo. Agrega productos reales desde el panel de administración.\');"' : ''; ?>>
                            
                            <div class="producto-imagen-container">
                                <?php
                                // Determinar la ruta de la imagen
                                if (!empty($producto['ImagenPrincipal'])) {
                                    $rutaImagen = '../../assets/img/productos/' . $producto['ImagenPrincipal'];
                                } else {
                                    $rutaImagen = 'https://picsum.photos/seed/' . urlencode($producto['NombreProducto']) . '/300x200.jpg';
                                }
                                ?>
                                <img src="<?php echo $rutaImagen; ?>" 
                                     alt="<?php echo htmlspecialchars($producto['NombreProducto']); ?>"
                                     onerror="this.src='https://picsum.photos/seed/producto/300x200.jpg'">
                                
                                <!-- Etiquetas de producto -->
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
                                
                                <?php if (is_numeric($producto['IdProducto'])): ?>
                                    <button class="btn-agregar" 
                                            onclick="agregarAlCarrito(<?php echo $producto['IdProducto']; ?>, '<?php echo addslashes($producto['NombreProducto']); ?>')">
                                        <i class="fas fa-shopping-cart"></i> Agregar al carrito
                                    </button>
                                <?php else: ?>
                                    <button class="btn-agregar btn-agotado" 
                                            onclick="alert('⚠️ Producto de ejemplo. Agrega productos reales desde el panel de administración.')">
                                        <i class="fas fa-shopping-cart"></i> Agregar al carrito
                                    </button>
                                <?php endif; ?>
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
                        <a href="<?php echo isset($producto['IdProducto']) && is_numeric($producto['IdProducto']) ? 'producto_detalle.php?id=' . $producto['IdProducto'] : '#'; ?>" 
                           class="card-producto"
                           <?php echo !is_numeric($producto['IdProducto']) ? 'onclick="event.preventDefault(); alert(\'⚠️ Producto de ejemplo. Agrega productos reales desde el panel de administración.\');"' : ''; ?>>
                            
                            <div class="producto-imagen-container">
                                <?php
                                // Determinar la ruta de la imagen
                                if (!empty($producto['ImagenPrincipal'])) {
                                    $rutaImagen = '../../assets/img/productos/' . $producto['ImagenPrincipal'];
                                } else {
                                    $rutaImagen = 'https://picsum.photos/seed/' . urlencode($producto['NombreProducto']) . '/300x200.jpg';
                                }
                                ?>
                                <img src="<?php echo $rutaImagen; ?>" 
                                     alt="<?php echo htmlspecialchars($producto['NombreProducto']); ?>"
                                     onerror="this.src='https://picsum.photos/seed/producto/300x200.jpg'">
                                
                                <!-- Etiquetas de producto -->
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
                                
                                <?php if (is_numeric($producto['IdProducto'])): ?>
                                    <button class="btn-agregar" 
                                            onclick="agregarAlCarrito(<?php echo $producto['IdProducto']; ?>, '<?php echo addslashes($producto['NombreProducto']); ?>')">
                                        <i class="fas fa-shopping-cart"></i> Agregar al carrito
                                    </button>
                                <?php else: ?>
                                    <button class="btn-agregar btn-agotado" 
                                            onclick="alert('⚠️ Producto de ejemplo. Agrega productos reales desde el panel de administración.')">
                                        <i class="fas fa-shopping-cart"></i> Agregar al carrito
                                    </button>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
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
                'Escolar' => 'https://as1.ftcdn.net/v2/jpg/03/61/04/32/1000_F_361043232_teu7Amyr5GsxKS1HsbSh3WRY6ExBKyQ3.jpg',
                'Oficina' => 'https://tse3.mm.bing.net/th/id/OIP.r42540i1ISVnP2kt8Wf8-wHaE-?rs=1&pid=ImgDetMain&o=7&rm=3',
                'Arte' => 'https://img.freepik.com/foto-gratis/conjunto-materiales-arte-pintura-abstracta_23-2147895411.jpg?size=626&ext=jpg'
            ];
            
            foreach ($categorias as $categoria): 
                $nombreCat = $categoria['NombreCategoria'];
                $imagenUrl = $imagenesCategoria[$nombreCat] ?? 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80';
            ?>
                <div class="card-categoria">
                    <a href=" productos.php?categoria=<?php echo $categoria['IdCategoria']; ?>">
                        <img src="<?php echo $imagenUrl; ?>" 
                             alt="<?php echo htmlspecialchars($nombreCat); ?>">
                        
                        <div class="categoria-nombre"><?php echo htmlspecialchars($nombreCat); ?></div>
                        <span class="btn-categoria">
                            Ver productos
                        </span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
// Función para mover las filas de productos
function moverFila(filaId, direccion) {
    const fila = document.getElementById(filaId);
    const scrollAmount = 345; // Ancho de la tarjeta + gap
    
    // Calcular la nueva posición
    const nuevaPosicion = fila.scrollLeft + (scrollAmount * direccion);
    
    // Aplicar scroll suave
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
    fetch(BASE_URL + 'controllers/CarritoController.php?action=agregar', {
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
    fetch(BASE_URL + 'controllers/CarritoController.php?action=contar')
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

// Inicializar las filas para asegurar que estén en la posición correcta
document.addEventListener('DOMContentLoaded', function() {
    const filas = document.querySelectorAll('.fila-productos');
    filas.forEach(fila => {
        fila.scrollLeft = 0;
    });
});
</script>
<?php include 'includes/footer.php'; ?>