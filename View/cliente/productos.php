<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/ProductoController.php';
require_once __DIR__ . '/../../controllers/CategoriaController.php';
/*require_once __DIR__ . '/../../controllers/MarcaController.php';*/

$productoController = new ProductoController();
$categoriaController = new CategoriaController();
/*$marcaController = new MarcaController();*/

// Obtener productos con filtros
$productos = $productoController->listarCliente();

// Obtener categorías para los filtros
$categorias = $categoriaController->listarActivas();

// Variables de filtros actuales
$categoriaSeleccionada = $_GET['categoria'] ?? '';
$precioMin = $_GET['precio_min'] ?? '';
$precioMax = $_GET['precio_max'] ?? '';
$busqueda = $_GET['busqueda'] ?? '';

// APLICAR FILTROS
if ($categoriaSeleccionada) {
    $productos = array_filter($productos, function($p) use ($categoriaSeleccionada) {
        return $p['IdCategoria'] == $categoriaSeleccionada;
    });
}

if ($busqueda) {
    $productos = array_filter($productos, function($p) use ($busqueda) {
        return stripos($p['NombreProducto'], $busqueda) !== false;
    });
}

if ($precioMin !== '') {
    $productos = array_filter($productos, function($p) use ($precioMin) {
        return $p['PrecioUnitario'] >= $precioMin;
    });
}

if ($precioMax !== '') {
    $productos = array_filter($productos, function($p) use ($precioMax) {
        return $p['PrecioUnitario'] <= $precioMax;
    });
}

// Obtener nombre de categoría seleccionada
$tituloFiltro = 'Todos los Productos';
if ($categoriaSeleccionada) {
    foreach ($categorias as $cat) {
        if ($cat['IdCategoria'] == $categoriaSeleccionada) {
            $tituloFiltro = 'Productos de ' . $cat['NombreCategoria'];
            break;
        }
    }
}
if ($busqueda) {
    $tituloFiltro = 'Resultados para: ' . htmlspecialchars($busqueda);
}

include 'includes/header.php';
?>
<!-- ESTILOS ESPECÍFICOS PARA PRODUCTOS -->
<style>
    /* CONTENEDOR CON FILTROS */
    .contenedor-con-filtros {
        max-width: 1400px;
        margin: 30px auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: 250px 1fr;
        gap: 30px;
    }
    
    /* PANEL DE FILTROS */
    .panel-filtros {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        height: fit-content;
        position: sticky;
        top: 100px;
    }
    
    .panel-filtros h3 {
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #FF6347;
        color: #333;
    }
    
    .filtro-grupo {
        margin-bottom: 20px;
    }
    
    .filtro-grupo label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: #333;
    }
    
    .filtro-grupo input,
    .filtro-grupo select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }
    
    .filtro-precio {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .filtro-precio input {
        flex: 1;
    }
    
    /* GRID DE PRODUCTOS */
    .grid-productos {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 25px;
    }
    
    .producto-card {
        background-color: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
        text-decoration: none;
        color: #333;
        display: flex;
        flex-direction: column;
    }
    
    .producto-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    .producto-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 4px;
        margin-bottom: 15px;
    }
    
    .producto-card h3 {
        font-size: 16px;
        margin-bottom: 10px;
        color: #333;
        min-height: 40px;
    }
    
    .producto-card .precio {
        font-size: 24px;
        font-weight: bold;
        color: #FF6347;
        margin: 10px 0;
    }
    
    .producto-card .disponibilidad {
        font-size: 14px;
        color: #27ae60;
        margin-bottom: 15px;
    }
    
    /* BOTONES */
    .btn {
        padding: 12px 25px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        text-decoration: none;
        display: inline-block;
        text-align: center;
        transition: all 0.3s;
        font-weight: bold;
    }
    
    .btn-naranja {
        background-color: #FF6347;
        color: white;
    }
    
    .btn-naranja:hover {
        background-color: #e5533d;
    }
    
    .btn-blanco {
        background-color: white;
        color: #333;
        border: 2px solid #ddd;
    }
    
    .btn-blanco:hover {
        background-color: #f5f5f5;
    }
    
    /* RESPONSIVE */
    @media (max-width: 768px) {
        .contenedor-con-filtros {
            grid-template-columns: 1fr;
        }
        
        .panel-filtros {
            position: static;
        }
        
        .grid-productos {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        }
    }
</style>

<!-- TÍTULO -->
<h1 style="color: white; margin: 30px auto; max-width: 1400px; padding: 0 20px; font-size: 32px;">
    <?php echo $tituloFiltro; ?>
</h1>

<!-- CONTENEDOR CON FILTROS -->
<div class="contenedor-con-filtros">
    <!-- PANEL DE FILTROS LATERAL -->
    <aside class="panel-filtros">
        <h3>🔍 Filtros</h3>
        
        <form method="GET" action="productos.php">
            <?php if ($busqueda): ?>
                <input type="hidden" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>">
            <?php endif; ?>
            
            <!-- FILTRO POR PRECIO -->
            <div class="filtro-grupo">
                <label>Precio:</label>
                <div class="filtro-precio">
                    <input type="number" 
                           name="precio_min" 
                           placeholder="Min" 
                           step="0.01"
                           value="<?php echo htmlspecialchars($precioMin); ?>">
                    <span>-</span>
                    <input type="number" 
                           name="precio_max" 
                           placeholder="Max" 
                           step="0.01"
                           value="<?php echo htmlspecialchars($precioMax); ?>">
                </div>
                <div style="margin-top: 10px; color: #666; font-size: 12px;">
                    Ejemplo: $0 - $1000
                </div>
            </div>
            
            <!-- FILTRO POR MARCA -->
            <div class="filtro-grupo">
                <label>Marca:</label>
                <select name="marca">
                    <option value="">Todas las marcas disponibles</option>
                    <?php foreach ($marcas as $marca): ?>
                        <option value="<?php echo $marca['IdMarca']; ?>"
                                <?php echo ($marcaSeleccionada == $marca['IdMarca']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($marca['NombreMarca']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- FILTRO POR CATEGORÍA -->
            <div class="filtro-grupo">
                <label>Categoría:</label>
                <select name="categoria">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['IdCategoria']; ?>"
                                <?php echo ($categoriaSeleccionada == $cat['IdCategoria']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['NombreCategoria']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- BOTONES -->
            <button type="submit" class="btn btn-naranja" style="width: 100%; margin-bottom: 10px;">
                Aplicar filtros
            </button>
            
            <a href="productos.php" class="btn btn-blanco" style="width: 100%; display: block; text-align: center;">
                Limpiar filtros
            </a>
        </form>
    </aside>
    
    <!-- ÁREA DE PRODUCTOS -->
    <div>
        <!-- INFORMACIÓN DE RESULTADOS -->
        <div style="background-color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <strong style="color: #333;">
                    <?php echo count($productos); ?> productos encontrados
                </strong>
            </div>
            
            <div>
                <select style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    <option>Ordenar por: Destacados</option>
                </select>
            </div>
        </div>
        
        <!-- GRID DE PRODUCTOS -->
        <?php if (empty($productos)): ?>
            <div style="background-color: white; padding: 60px; border-radius: 8px; text-align: center;">
                <h2 style="color: #666; margin-bottom: 15px;">No se encontraron productos</h2>
                <p style="color: #999; margin-bottom: 20px;">
                    Intenta ajustar los filtros o buscar con otros términos
                </p>
                <a href="productos.php" class="btn btn-naranja">
                    Ver todos los productos
                </a>
            </div>
        <?php else: ?>
            <div class="grid-productos">
    <?php foreach ($productos as $producto): ?>
        <div class="producto-card">
            <a href="producto_detalle.php?id=<?php echo $producto['IdProducto']; ?>" style="text-decoration: none; color: inherit;">
                <?php
                // Determinar la ruta de la imagen
                $rutaImagenProducto = !empty($producto['ImagenPrincipal']) 
                    ? '../../assets/img/productos/' . $producto['ImagenPrincipal']
                    : 'https://via.placeholder.com/250x250/f5f5f5/666666?text=' . urlencode(substr($producto['NombreProducto'], 0, 15));
                ?>
                <img src="<?php echo $rutaImagenProducto; ?>" 
                     alt="<?php echo htmlspecialchars($producto['NombreProducto']); ?>"
                     onerror="this.src='https://via.placeholder.com/250x250/f5f5f5/666666?text=Sin+Imagen'">
                
                <h3><?php echo htmlspecialchars($producto['NombreProducto']); ?></h3>
            </a>
            
            <p class="precio">$<?php echo number_format($producto['PrecioUnitario'], 2); ?></p>
            
            <?php if ($producto['Disponible']): ?>
                <p class="disponibilidad">Disponible</p>
                <button class="btn btn-naranja" style="width: 100%; margin-top: auto;">
                    🛒 Agregar al carrito
                </button>
            <?php else: ?>
                <p style="color: #e74c3c; margin-bottom: 10px;">Agotado</p>
                <button class="btn btn-blanco" style="width: 100%; margin-top: auto;" disabled>
                    No disponible
                </button>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>