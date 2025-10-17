<?php
require_once __DIR__ . '/../../controllers/CategoriaController.php';
require_once __DIR__ . '/../../controllers/ProductoController.php';

$categoriaController = new CategoriaController();
$productoController = new ProductoController();

// Obtener todas las categorías activas
$categorias = $categoriaController->listarActivas();

// Si se seleccionó una categoría, obtener sus productos
$categoriaSeleccionada = isset($_GET['id']) ? (int)$_GET['id'] : null;
$nombreCategoriaSeleccionada = 'Todas las Categorías';
$productos = [];

if ($categoriaSeleccionada) {
    // Obtener productos de la categoría seleccionada
    $_GET['categoria'] = $categoriaSeleccionada;
    $productos = $productoController->listarCliente();
    
    // Obtener nombre de la categoría
    foreach ($categorias as $cat) {
        if ($cat['IdCategoria'] == $categoriaSeleccionada) {
            $nombreCategoriaSeleccionada = $cat['NombreCategoria'];
            break;
        }
    }
}

include 'includes/header.php';
?>

<style>
    .categorias-container {
        max-width: 1600px;
        margin: 30px auto;
        padding: 0 20px 90px 30px;
    }
    
    .titulo-principal {
        color: #000000;
        font-size: 36px;
        text-align: center;
        margin-bottom: 220px;
    }
    
    .categorias-grid {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 30px;
        margin-bottom: 8px;
    }
    
    .categoria-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 15px;
        padding: 30px;
        text-align: center;
        text-decoration: none;
        color: #2C3E50;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        width: 280px;
    }
    
    .categoria-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #FF6347, #ff8c7a);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    
    .categoria-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 8px 25px rgba(255, 99, 71, 0.3);
    }
    
    .categoria-icono {
        font-size: 64px;
        margin-bottom: 20px;
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
    }
    
    .categoria-nombre {
        font-size: 22px;
        font-weight: bold;
        margin-bottom: 10px;
        color: #2C3E50;
    }
    
    .categoria-contador {
        font-size: 14px;
        color: #666;
        background-color: #e8f4f8;
        padding: 5px 15px;
        border-radius: 20px;
        display: inline-block;
        margin-top: 10px;
    }
    
    .seccion-productos {
        background-color: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .titulo-seccion {
        color: #2C3E50;
        font-size: 28px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .subtitulo-seccion {
        color: #666;
        font-size: 16px;
        margin-bottom: 30px;
    }
    
    .btn-volver {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background-color: #FF6347;
        color: white;
        padding: 12px 25px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
        transition: background-color 0.3s;
        margin-bottom: 30px;
    }
    
    .btn-volver:hover {
        background-color: #e5533d;
    }
    
    .productos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 25px;
        margin-top: 30px;
    }
    
    .producto-card {
        background-color: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        text-decoration: none;
        color: #333;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .producto-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    .producto-imagen {
        width: 100%;
        height: 200px;
        object-fit: contain;
        border-radius: 8px;
        margin-bottom: 15px;
        background-color: white;
        padding: 10px;
    }
    
    .producto-nombre {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 10px;
        min-height: 40px;
        color: #2C3E50;
    }
    
    .producto-precio {
        font-size: 24px;
        font-weight: bold;
        color: #FF6347;
        margin-bottom: 15px;
    }
    
    .btn-ver-producto {
        display: block;
        width: 100%;
        padding: 10px;
        background-color: #FF6347;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
    }
    
    .btn-ver-producto:hover {
        background-color: #e5533d;
    }
    
    .sin-productos {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }
    
    .sin-productos-icono {
        font-size: 64px;
        margin-bottom: 20px;
    }
    
    .breadcrumbs {
        margin-bottom: 30px;
        color: rgba(255,255,255,0.8);
        font-size: 14px;
    }
    
    .breadcrumbs a {
        color: #FF6347;
        text-decoration: none;
    }
    
    .breadcrumbs a:hover {
        text-decoration: underline;
    }
    
    @media (max-width: 768px) {
        .categorias-grid {
            gap: 15px;
        }
        
        .categoria-card {
            padding: 20px;
            width: 150px;
        }
        
        .categoria-icono {
            font-size: 48px;
        }
        
        .categoria-nombre {
            font-size: 18px;
        }
        
        .titulo-principal {
            font-size: 28px;
        }
        
        .productos-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="categorias-container">
    
    <?php if (!$categoriaSeleccionada): ?>
        <!-- VISTA DE TODAS LAS CATEGORÍAS -->
        
        <h1 class="titulo-principal">
            🏷️ Explora Nuestras Categorías
        </h1>
        
        <?php if (empty($categorias)): ?>
            <div class="seccion-productos">
                <div class="sin-productos">
                    <div class="sin-productos-icono">📦</div>
                    <h3>No hay categorías disponibles</h3>
                    <p>Vuelve pronto para ver nuestras categorías de productos</p>
                </div>
            </div>
        <?php else: ?>
            <div class="categorias-grid">
                <?php 
                // Iconos para categorías (puedes personalizarlos según tus categorías)
                $iconos = [
                    'Papelería' => '📝',
                    'Cuadernos' => '📓',
                    'Útiles Escolares' => '✏️',
                    'Oficina' => '💼',
                    'Arte' => '🎨',
                    'Impresión' => '🖨️',
                    'Tecnología' => '💻',
                    'Librería' => '📚',
                    'Manualidades' => '✂️',
                    'Escritorio' => '🪑'
                ];
                
                foreach ($categorias as $categoria): 
                    // Buscar icono o usar uno por defecto
                    $icono = '📦';
                    foreach ($iconos as $key => $value) {
                        if (stripos($categoria['NombreCategoria'], $key) !== false) {
                            $icono = $value;
                            break;
                        }
                    }
                ?>
                    <a href="categorias.php?id=<?php echo $categoria['IdCategoria']; ?>" class="categoria-card">
                        <div class="categoria-icono"><?php echo $icono; ?></div>
                        <div class="categoria-nombre">
                            <?php echo htmlspecialchars($categoria['NombreCategoria']); ?>
                        </div>
                        <div class="categoria-contador">
                            Ver productos →
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
    <?php else: ?>
        <!-- VISTA DE PRODUCTOS DE UNA CATEGORÍA -->
     <div class="breadcrumbs">
            <a href="index.php">Inicio</a> / 
            <a href="categorias.php">Categorías</a> / 
            <span><?php echo htmlspecialchars($nombreCategoriaSeleccionada); ?></span>
        </div>
        
        <a href="categorias.php" class="btn-volver">
            ← Volver a Categorías
        </a>
        
        <div class="seccion-productos">
            <div class="titulo-seccion">
                📦 <?php echo htmlspecialchars($nombreCategoriaSeleccionada); ?>
            </div>
            <p class="subtitulo-seccion">
                <?php echo count($productos); ?> productos encontrados en esta categoría
            </p>
            
            <?php if (empty($productos)): ?>
                <div class="sin-productos">
                    <div class="sin-productos-icono">🔍</div>
                    <h3>No hay productos en esta categoría</h3>
                    <p>Explora otras categorías para encontrar lo que buscas</p>
                    <a href="categorias.php" style="display: inline-block; margin-top: 20px; padding: 12px 30px; background-color: #FF6347; color: white; text-decoration: none; border-radius: 8px; font-weight: bold;">
                        Ver Todas las Categorías
                    </a>
                </div>
            <?php else: ?>
                <div class="productos-grid">
                    <?php foreach ($productos as $producto): ?>
                        <div class="producto-card">
                            <?php
                            // Determinar la ruta de la imagen
                            $rutaImagen = !empty($producto['ImagenPrincipal']) 
                                ? '../../assets/img/productos/' . $producto['ImagenPrincipal']
                                : 'https://via.placeholder.com/250x200/f5f5f5/666666?text=' . urlencode(substr($producto['NombreProducto'], 0, 15));
                            ?>
                            <img src="<?php echo $rutaImagen; ?>" 
                                 alt="<?php echo htmlspecialchars($producto['NombreProducto']); ?>"
                                 class="producto-imagen"
                                 onerror="this.src='https://via.placeholder.com/250x200/f5f5f5/666666?text=Sin+Imagen'">
                            
                            <div class="producto-nombre">
                                <?php echo htmlspecialchars($producto['NombreProducto']); ?>
                            </div>
                            
                            <div class="producto-precio">
                                $<?php echo number_format($producto['PrecioUnitario'], 2); ?>
                            </div>
                            
                            <?php if ($producto['Disponible']): ?>
                                <a href="producto_detalle.php?id=<?php echo $producto['IdProducto']; ?>" class="btn-ver-producto">
                                    Ver Producto
                                </a>
                            <?php else: ?>
                                <button class="btn-ver-producto" style="background-color: #ccc; cursor: not-allowed;" disabled>
                                    No Disponible
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
    <?php endif; ?>
    
</div>

<?php include 'includes/footer.php'; ?>