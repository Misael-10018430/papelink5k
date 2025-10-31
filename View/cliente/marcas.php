<?php
require_once __DIR__ . '/../../config/config.php';
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

$titulo = $marcaSeleccionada ? "Productos de " . $nombreMarcaSeleccionada . " - Papelink" : "Marcas - Papelink";

// ⭐ IMPORTANTE: Incluir header SIN variables adicionales
// El header debe leer directamente de $_SESSION
include 'includes/header.php';
?>


<!-- ESTILOS ESPECÍFICOS PARA MARCAS -->
<style>
    .marcas-container {
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
    
    /* GRID DE MARCAS */
    .marcas-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
        margin-bottom: 60px;
    }
    
    .marca-card {
        background-color: #ffffff;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        text-align: center;
        text-decoration: none;
        color: #333;
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        overflow: hidden;
    }
    
    .marca-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(255, 99, 71, 0.15);
    }
    
    .marca-logo {
        width: 120px;
        height: 120px;
        object-fit: contain;
        margin-bottom: 20px;
        border-radius: 12px;
        background-color: #f8f9fa;
        padding: 20px;
        transition: transform 0.3s ease;
    }
    
    .marca-card:hover .marca-logo {
        transform: scale(1.05);
    }
    
    .marca-logo-placeholder {
        width: 120px;
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        margin-bottom: 20px;
        font-size: 42px;
        font-weight: 700;
        color: #FF6347;
        transition: transform 0.3s ease;
    }
    
    .marca-card:hover .marca-logo-placeholder {
        transform: scale(1.05);
    }
    
    .marca-nombre {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #2c3e50;
        transition: color 0.3s ease;
    }
    
    .marca-card:hover .marca-nombre {
        color: #FF6347;
    }
    
    .marca-descripcion {
        font-size: 14px;
        color: #666;
        line-height: 1.6;
        margin-bottom: 25px;
        flex-grow: 1;
    }
    
    .btn-ver-productos {
        background: linear-gradient(135deg, #FF6347 0%, #ff7a5c 100%);
        color: white;
        padding: 12px 30px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        display: inline-block;
        box-shadow: 0 4px 15px rgba(255, 99, 71, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .btn-ver-productos:hover {
        background: linear-gradient(135deg, #e5533d 0%, #FF6347 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 99, 71, 0.4);
    }
    
    /* SECCIÓN DE PRODUCTOS DE LA MARCA */
    .btn-volver {
        display: inline-flex;
        align-items: center;
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
    
    .productos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 25px;
    }
    
    .producto-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 16px;
        padding: 25px;
        text-align: center;
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
        height: 180px;
        object-fit: cover;
        border-radius: 12px;
        margin-bottom: 15px;
        transition: transform 0.3s ease;
    }
    
    .producto-card:hover .producto-imagen {
        transform: scale(1.05);
    }
    
    .producto-nombre {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
        min-height: 40px;
        line-height: 1.4;
        transition: color 0.3s ease;
    }
    
    .producto-card:hover .producto-nombre {
        color: #FF6347;
    }
    
    .producto-precio {
        font-size: 22px;
        color: #FF6347;
        font-weight: 700;
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
    
    /* Línea separadora con estilo */
    .divider {
        height: 2px;
        background: linear-gradient(90deg, transparent, #FF6347, transparent);
        margin: 30px 0;
        opacity: 0.3;
    }
    
    @media (max-width: 768px) {
        .marcas-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .productos-grid {
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
        }
        
        .titulo-principal {
            font-size: 28px;
        }
    }
</style>

<div class="marcas-container">
    <h1 class="titulo-principal">Explora Nuestras Marcas</h1>
    
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
                            <?php echo htmlspecialchars(substr($marca['DescripcionMarca'], 0, 120)); ?>
                            <?php if (strlen($marca['DescripcionMarca']) > 120): ?>...<?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <span class="btn-ver-productos">
                        Ver Productos
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
                         style="width: 40px; height: 40px; object-fit: contain; border-radius: 8px;">
                <?php endif; ?>
                <?php echo htmlspecialchars($nombreMarcaSeleccionada); ?>
            </div>
            
            <p class="subtitulo-seccion">
                <?php echo count($productos); ?> productos encontrados
            </p>
            
            <?php if (!empty($marcaSeleccionada['DescripcionMarca'])): ?>
                <div class="divider"></div>
                <p style="color: #666; margin-bottom: 30px; line-height: 1.6; font-size: 15px;">
                    <?php echo htmlspecialchars($marcaSeleccionada['DescripcionMarca']); ?>
                </p>
            <?php endif; ?>
            
            <?php if (!empty($marcaSeleccionada['SitioWeb'])): ?>
                <p style="margin-bottom: 30px;">
                    <a href="<?php echo htmlspecialchars($marcaSeleccionada['SitioWeb']); ?>" 
                       target="_blank"
                       style="color: #FF6347; text-decoration: none; font-weight: 600; font-size: 14px;">
                         Visitar sitio web oficial →
                    </a>
                </p>
            <?php endif; ?>
            
            <?php if (empty($productos)): ?>
                <div class="sin-productos">
                    <div class="sin-productos-icono"></div>
                    <h3 style="font-weight: 600; color: #2c3e50; margin-bottom: 10px;">No hay productos disponibles</h3>
                    <p style="font-size: 15px;">Esta marca aún no tiene productos en nuestro catálogo</p>
                </div>
            <?php else: ?>
                <div class="productos-grid">
                    <?php foreach ($productos as $producto): ?>
                        <div class="producto-card">
                            <?php
                            $rutaImagenProducto = !empty($producto['ImagenPrincipal']) 
                                ? '../../assets/img/productos/' . $producto['ImagenPrincipal']
                                : 'https://via.placeholder.com/240x180/f8f9fa/666666?text=' . urlencode(substr($producto['NombreProducto'], 0, 15));
                            ?>
                            <img src="<?php echo $rutaImagenProducto; ?>" 
                                 alt="<?php echo htmlspecialchars($producto['NombreProducto']); ?>"
                                 class="producto-imagen"
                                 onerror="this.src='https://via.placeholder.com/240x180/f8f9fa/666666?text=Sin+Imagen'">
                            
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