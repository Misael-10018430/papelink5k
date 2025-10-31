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
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }
    
    .titulo-principal {
        color: #2c3e50;
        font-size: 36px;
        text-align: center;
        margin-bottom: 50px;
        font-weight: 700;
        position: relative;
        display: inline-block;
        width: 100%;
    }
    
    .titulo-principal::after {
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
    
    .categorias-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
        margin-bottom: 60px;
    }
    
    .categoria-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        text-decoration: none;
        color: #333;
        position: relative;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    .categoria-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(255, 99, 71, 0.15);
    }
    
    .categoria-imagen-container {
        position: relative;
        height: 200px;
        overflow: hidden;
    }
    
    .categoria-imagen {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .categoria-card:hover .categoria-imagen {
        transform: scale(1.05);
    }
    
    .categoria-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(0deg, rgba(44, 62, 80, 0.7) 0%, rgba(44, 62, 80, 0.3) 50%, rgba(44, 62, 80, 0.1) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .categoria-card:hover .categoria-overlay {
        opacity: 1;
    }
    
    .categoria-content {
        padding: 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .categoria-icono {
        font-size: 48px;
        margin-bottom: 15px;
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
        transition: transform 0.3s ease;
    }
    
    .categoria-card:hover .categoria-icono {
        transform: scale(1.1);
    }
    
    .categoria-nombre {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #2c3e50;
        transition: color 0.3s ease;
    }
    
    .categoria-card:hover .categoria-nombre {
        color: #FF6347;
    }
    
    .categoria-descripcion {
        font-size: 14px;
        color: #666;
        line-height: 1.5;
        margin-bottom: 20px;
        flex-grow: 1;
    }
    
    .categoria-contador {
        font-size: 14px;
        color: #666;
        background-color: #f8f9fa;
        padding: 8px 20px;
        border-radius: 20px;
        display: inline-block;
        margin-top: auto;
        transition: all 0.3s ease;
    }
    
    .categoria-card:hover .categoria-contador {
        background-color: #FF6347;
        color: white;
    }
    
    .seccion-productos {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 20px;
        padding: 40px;
        margin-top: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 99, 71, 0.1);
    }
    
    .titulo-seccion {
        font-size: 28px;
        color: #2c3e50;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 15px;
        font-weight: 700;
    }
    
    .subtitulo-seccion {
        color: #666;
        margin-bottom: 30px;
        font-size: 16px;
        font-weight: 400;
    }
    
    .btn-volver {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        color: #2c3e50;
        padding: 12px 25px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 30px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    
    .btn-volver:hover {
        background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
        transform: translateX(-5px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
    }
    
    .productos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 25px;
        margin-top: 30px;
    }
    
    .producto-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 16px;
        padding: 25px;
        text-align: center;
        text-decoration: none;
        color: #333;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid rgba(255, 99, 71, 0.1);
        position: relative;
        overflow: hidden;
    }
    
    .producto-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(255, 99, 71, 0.15);
        border-color: rgba(255, 99, 71, 0.2);
    }
    
    .producto-imagen {
        width: 100%;
        height: 200px;
        object-fit: contain;
        border-radius: 12px;
        margin-bottom: 15px;
        background-color: white;
        padding: 10px;
        transition: transform 0.3s ease;
    }
    
    .producto-card:hover .producto-imagen {
        transform: scale(1.05);
    }
    
    .producto-nombre {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 10px;
        min-height: 40px;
        color: #2c3e50;
        transition: color 0.3s ease;
    }
    
    .producto-card:hover .producto-nombre {
        color: #FF6347;
    }
    
    .producto-precio {
        font-size: 24px;
        font-weight: 700;
        color: #FF6347;
        margin-bottom: 15px;
    }
    
    .btn-ver-producto {
        background: linear-gradient(135deg, #FF6347 0%, #ff7a5c 100%);
        color: white;
        padding: 10px 25px;
        border-radius: 25px;
        text-decoration: none;
        display: inline-block;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(255, 99, 71, 0.3);
    }
    
    .btn-ver-producto:hover {
        background: linear-gradient(135deg, #e5533d 0%, #FF6347 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(255, 99, 71, 0.4);
    }
    
    .sin-productos {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }
    
    .sin-productos-icono {
        font-size: 64px;
        margin-bottom: 20px;
        color: #dee2e6;
    }
    
    .breadcrumbs {
        margin-bottom: 30px;
        color: #666;
        font-size: 14px;
    }
    
    .breadcrumbs a {
        color: #FF6347;
        text-decoration: none;
        font-weight: 500;
    }
    
    .breadcrumbs a:hover {
        text-decoration: underline;
    }
    
    /* Línea separadora con estilo */
    .divider {
        height: 2px;
        background: linear-gradient(90deg, transparent, #FF6347, transparent);
        margin: 30px 0;
        opacity: 0.3;
    }
    
    @media (max-width: 768px) {
        .categorias-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .categoria-imagen-container {
            height: 160px;
        }
        
        .categoria-content {
            padding: 20px;
        }
        
        .categoria-icono {
            font-size: 40px;
        }
        
        .categoria-nombre {
            font-size: 18px;
        }
        
        .titulo-principal {
            font-size: 28px;
        }
        
        .productos-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }
    }
</style>

<div class="categorias-container">
    
    <?php if (!$categoriaSeleccionada): ?>
        <!-- VISTA DE TODAS LAS CATEGORÍAS -->
        
        <h1 class="titulo-principal">Explora Nuestras Categorías</h1>
        
        <?php if (empty($categorias)): ?>
            <div class="seccion-productos">
                <div class="sin-productos">
                    <h3>No hay categorías disponibles</h3>
                    <p>Vuelve pronto para ver nuestras categorías de productos</p>
                </div>
            </div>
        <?php else: ?>
            <div class="categorias-grid">
                <?php 
                // Iconos para categorías (puedes personalizarlos según tus categorías)
                $iconos = [
                    'Papelería' => '',
                    'Cuadernos' => '',
                    'Útiles Escolares' => '',
                    'Oficina' => '',
                    'Arte' => '',
                    'Impresión' => '',
                    'Tecnología' => '',
                    'Librería' => '',
                    'Manualidades' => '',
                    'Escritorio' => ''
                ];
                
                
                // Imágenes de referencia para categorías (AQUÍ PUEDES CAMBIAR LAS URL)
                $imagenesCategoria = [
                    'Papelería' => 'https://th.bing.com/th/id/R.d475d165db328614b132c1787656022b?rik=DEp6jSf%2bpDMxOg&riu=http%3a%2f%2fwww.graciacomerc.com%2fmedia%2fCategorias%2fpapeleria.jpg&ehk=IGp51QiN0Ch6rTY8nH0BFAVJoNIU5p4IzrRNVfr7eyQ%3d&risl=&pid=ImgRaw&r=0',
                    'Cuadernos' => 'https://th.bing.com/th/id/R.018acf30eb1864f4b459979d80d784b5?rik=T02czkyj46hHrQ&pid=ImgRaw&r=0',
                    'Útiles Escolares' => 'https://cdn.grupoelcorteingles.es/statics/manager/contents/images/uploads/2024/05/B10CHs_XR.jpeg?impolicy=Resize&width=1920&height=1080',
                    'Oficina' => 'https://www.gradnja.rs/wp-content/uploads/2018/08/enterijer-instana-novi-sad-sonja-brstina-07.jpg',
                    'Arte' => 'https://img.freepik.com/foto-gratis/conjunto-materiales-arte-pintura-abstracta_23-2147895411.jpg?size=626&ext=jpg',
                    'Impresión' => 'https://www.fotocopiasuniverso.online/wp-content/uploads/2024/02/calidad-de-impresion-online.jpg',
                    'Tecnología' => 'https://consumer.huawei.com/content/dam/huawei-cbg-site/latam/mx/mkt/plp/new/desktops-new/matestation-x-series/matestation-x-series-1.jpg',
                    'Librería' => 'https://images.adsttc.com/media/images/5954/5c6d/b22e/38be/e300/0021/slideshow/230517__-__Libreria_Quade_ph_G_Viramonte-6177.jpg?1498700901',
                    'Manualidades' => 'https://www.educaciontrespuntocero.com/wp-content/uploads/2023/09/DESTACADA-MANUALIDADES.jpg',
                    'Escritorio' => 'https://www.lavanguardia.com/files/og_thumbnail/files/fp/uploads/2022/09/16/63247e68f34f1.r_d.1147-624-5658.jpeg'
                ];
                
                foreach ($categorias as $categoria): 
                    // Buscar icono o usar uno por defecto
                    $icono = '';
                    foreach ($iconos as $key => $value) {
                        if (stripos($categoria['NombreCategoria'], $key) !== false) {
                            $icono = $value;
                            break;
                        }
                    }
                    
                    // Buscar imagen o usar una por defecto
                    $imagenCategoria = 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80';
                    foreach ($imagenesCategoria as $key => $value) {
                        if (stripos($categoria['NombreCategoria'], $key) !== false) {
                            $imagenCategoria = $value;
                            break;
                        }
                    }
                ?>
                    <a href="categorias.php?id=<?php echo $categoria['IdCategoria']; ?>" class="categoria-card">
                        <div class="categoria-imagen-container">
                            <img src="<?php echo $imagenCategoria; ?>" 
                                 alt="<?php echo htmlspecialchars($categoria['NombreCategoria']); ?>"
                                 class="categoria-imagen">
                            <div class="categoria-overlay"></div>
                        </div>
                        <div class="categoria-content">
                            <div class="categoria-icono"><?php echo $icono; ?></div>
                            <div class="categoria-nombre">
                                <?php echo htmlspecialchars($categoria['NombreCategoria']); ?>
                            </div>
                            <div class="categoria-descripcion">
                                Explora nuestra selección de productos de <?php echo strtolower(htmlspecialchars($categoria['NombreCategoria'])); ?>
                            </div>
                            <div class="categoria-contador">
                                Ver productos →
                            </div>
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
                 <?php echo htmlspecialchars($nombreCategoriaSeleccionada); ?>
            </div>
            <p class="subtitulo-seccion">
                <?php echo count($productos); ?> productos encontrados en esta categoría
            </p>
            
            <?php if (empty($productos)): ?>
                <div class="sin-productos">
                    <div class="sin-productos-icono">🔍</div>
                    <h3>No hay productos en esta categoría</h3>
                    <p>Explora otras categorías para encontrar lo que buscas</p>
                    <a href="categorias.php" style="display: inline-block; margin-top: 20px; padding: 12px 30px; background: linear-gradient(135deg, #FF6347 0%, #ff7a5c 100%); color: white; text-decoration: none; border-radius: 30px; font-weight: 600; box-shadow: 0 4px 15px rgba(255, 99, 71, 0.3);">
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
                                : 'https://via.placeholder.com/250x200/f8f9fa/666666?text=' . urlencode(substr($producto['NombreProducto'], 0, 15));
                            ?>
                            <img src="<?php echo $rutaImagen; ?>" 
                                 alt="<?php echo htmlspecialchars($producto['NombreProducto']); ?>"
                                 class="producto-imagen"
                                 onerror="this.src='https://via.placeholder.com/250x200/f8f9fa/666666?text=Sin+Imagen'">
                            
                            <div class="producto-nombre">
                                <?php echo htmlspecialchars($producto['NombreProducto']); ?>
                            </div>
                            
                            <div class="producto-precio">
                                $<?php echo number_format($producto['PrecioUnitario'], 2); ?>
                            </div>
                            
                            <?php if ($producto['Disponible']): ?>
                                <a href="producto_detalle.php?id=<?php echo $producto['IdProducto']; ?>" class="btn-ver-producto">
                                    Ver Detalles
                                </a>
                            <?php else: ?>
                                <button class="btn-ver-producto" style="background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%); cursor: not-allowed;" disabled>
                                    Agotado
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