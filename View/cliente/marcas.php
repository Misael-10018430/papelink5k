<?php
require_once __DIR__ . '/../../controllers/MarcaController.php';
require_once __DIR__ . '/../../controllers/ProductoController.php';

$marcaController = new MarcaController();
$productoController = new ProductoController();

// Obtener todas las marcas activas
$marcas = $marcaController->listarActivas();

// Si se selecciona una marca, mostrar sus productos
$marcaSeleccionada = null;
$nombreMarcaSeleccionada = '';
$productos = [];

if (isset($_GET['id'])) {
    $idMarca = (int)$_GET['id'];
    
    // Buscar la marca seleccionada
    foreach ($marcas as $marca) {
        if ($marca['IdMarca'] == $idMarca) {
            $marcaSeleccionada = $marca;
            $nombreMarcaSeleccionada = $marca['NombreMarca'];
            break;
        }
    }
    
    // Obtener productos de la marca
    if ($marcaSeleccionada) {
        $_GET['marca'] = $idMarca;
        $productos = $productoController->listarCliente();
    }
}

include 'includes/header.php';
?>

<!-- ESTILOS ESPECÍFICOS PARA MARCAS -->
<style>
    .marcas-container {
        max-width: 1400px;
        margin: 30px auto;
        padding: 0 20px;
    }
    
    .titulo-principal {
        color: white;
        font-size: 36px;
        text-align: center;
        margin-bottom: 40px;
    }
    
    /* GRID DE MARCAS */
    .marcas-grid {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 30px;
        margin-bottom: 50px;
    }
    
    .marca-card {
        background-color: white;
        border-radius: 15px;
        padding: 30px;
        width: 280px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s;
        text-align: center;
        text-decoration: none;
        color: #333;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .marca-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }
    
    .marca-logo {
        width: 150px;
        height: 150px;
        object-fit: contain;
        margin-bottom: 20px;
        border: 2px solid #f5f5f5;
        border-radius: 10px;
        padding: 15px;
        background-color: #fff;
    }
    
    .marca-logo-placeholder {
        width: 150px;
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #FF6347 0%, #ff8566 100%);
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 48px;
        font-weight: bold;
        color: white;
    }
    
    .marca-nombre {
        font-size: 22px;
        font-weight: bold;
        margin-bottom: 10px;
        color: #2C3E50;
    }
    
    .marca-productos {
        font-size: 14px;
        color: #666;
        margin-bottom: 15px;
    }
    
    .marca-descripcion {
        font-size: 13px;
        color: #999;
        line-height: 1.5;
        margin-bottom: 20px;
        flex-grow: 1;
    }
    
    .btn-ver-productos {
        background-color: #FF6347;
        color: white;
        padding: 12px 30px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
        transition: background-color 0.3s;
        display: inline-block;
    }
    
    .btn-ver-productos:hover {
        background-color: #e5533d;
    }
    
    /* SECCIÓN DE PRODUCTOS DE LA MARCA */
    .btn-volver {
        display: inline-block;
        background-color: white;
        color: #333;
        padding: 12px 25px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
        margin-bottom: 30px;
        transition: all 0.3s;
    }
    
    .btn-volver:hover {
        background-color: #f5f5f5;
        transform: translateX(-5px);
    }
    
    .seccion-productos {
        background-color: white;
        border-radius: 15px;
        padding: 40px;
        margin-top: 30px;
    }
    
    .titulo-seccion {
        font-size: 28px;
        color: #2C3E50;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .subtitulo-seccion {
        color: #666;
        margin-bottom: 30px;
        font-size: 16px;
    }
    
    .productos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 25px;
    }
    
    .producto-card {
        background-color: #f9f9f9;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s;
        border: 2px solid transparent;
    }
    
    .producto-card:hover {
        border-color: #FF6347;
        transform: translateY(-5px);
    }
    
    .producto-imagen {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    
    .producto-nombre {
        font-size: 16px;
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
        min-height: 40px;
    }
    
    .producto-precio {
        font-size: 24px;
        color: #FF6347;
        font-weight: bold;
        margin-bottom: 15px;
    }
    
    .btn-ver-producto {
        background-color: #FF6347;
        color: white;
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        display: inline-block;
        font-weight: bold;
        transition: background-color 0.3s;
    }
    
    .btn-ver-producto:hover {
        background-color: #e5533d;
    }
    
    .sin-productos {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }
    
    .sin-productos-icono {
        font-size: 64px;
        margin-bottom: 20px;
    }
    
    @media (max-width: 768px) {
        .marcas-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }
        
        .marca-card {
            width: 100%;
        }
    }
</style>

<div class="marcas-container">
    <h1 class="titulo-principal">🏷️ Explora Nuestras Marcas</h1>
    
    <?php if (!$marcaSeleccionada): ?>
        <!-- VISTA DE GRID DE MARCAS -->
        <div class="marcas-grid">
            <?php foreach ($marcas as $marca): ?>
                <a href="marcas.php?id=<?php echo $marca['IdMarca']; ?>" class="marca-card">
                    <?php if (!empty($marca['LogoMarca'])): ?>
                        <img src="<?php echo htmlspecialchars($marca['LogoMarca']); ?>" 
                             alt="<?php echo htmlspecialchars($marca['NombreMarca']); ?>"
                             class="marca-logo"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="marca-logo-placeholder" style="display: none;">
                            <?php echo strtoupper(substr($marca['NombreMarca'], 0, 1)); ?>
                        </div>
                    <?php else: ?>
                        <div class="marca-logo-placeholder">
                            <?php echo strtoupper(substr($marca['NombreMarca'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="marca-nombre">
                        <?php echo htmlspecialchars($marca['NombreMarca']); ?>
                    </div>
                    <?php if (!empty($marca['DescripcionMarca'])): ?>
                        <div class="marca-descripcion">
                            <?php echo htmlspecialchars($marca['DescripcionMarca']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <span class="btn-ver-productos">
                        Ver Productos →
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
        
    <?php else: ?>
        <!-- VISTA DE PRODUCTOS DE LA MARCA -->
        <a href="marcas.php" class="btn-volver">
            ← Volver a Marcas
        </a>
        
        <div class="seccion-productos">
            <div class="titulo-seccion">
                <?php if (!empty($marcaSeleccionada['LogoMarca'])): ?>
                    <img src="<?php echo htmlspecialchars($marcaSeleccionada['LogoMarca']); ?>" 
                         alt="Logo"
                         style="width: 50px; height: 50px; object-fit: contain;">
                <?php endif; ?>
                <?php echo htmlspecialchars($nombreMarcaSeleccionada); ?>
            </div>
            
            <p class="subtitulo-seccion">
                <?php echo count($productos); ?> productos encontrados
            </p>
            
            <?php if (!empty($marcaSeleccionada['DescripcionMarca'])): ?>
                <p style="color: #666; margin-bottom: 30px; line-height: 1.6;">
                    <?php echo htmlspecialchars($marcaSeleccionada['DescripcionMarca']); ?>
                </p>
            <?php endif; ?>
            
            <?php if (!empty($marcaSeleccionada['SitioWeb'])): ?>
                <p style="margin-bottom: 30px;">
                    <a href="<?php echo htmlspecialchars($marcaSeleccionada['SitioWeb']); ?>" 
                       target="_blank"
                       style="color: #FF6347; text-decoration: none; font-weight: bold;">
                        🌐 Visitar sitio web oficial →
                    </a>
                </p>
            <?php endif; ?>
            
            <?php if (empty($productos)): ?>
                <div class="sin-productos">
                    <div class="sin-productos-icono">🔍</div>
                    <h3>No hay productos disponibles</h3>
                    <p>Esta marca aún no tiene productos disponibles en nuestro catálogo</p>
                </div>
            <?php else: ?>
                <div class="productos-grid">
                    <?php foreach ($productos as $producto): ?>
                        <div class="producto-card">
                            <?php
                            $rutaImagenProducto = !empty($producto['ImagenPrincipal']) 
                                ? '../../assets/img/productos/' . $producto['ImagenPrincipal']
                                : 'https://via.placeholder.com/250x200/f5f5f5/666666?text=' . urlencode(substr($producto['NombreProducto'], 0, 15));
                            ?>
                            <img src="<?php echo $rutaImagenProducto; ?>" 
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