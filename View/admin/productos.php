<?php
require_once __DIR__ . '/../../controllers/ProductoController.php';
require_once __DIR__ . '/../../controllers/CategoriaController.php';
require_once __DIR__ . '/../../controllers/MarcaController.php';

$productoController = new ProductoController();
$categoriaController = new CategoriaController();
$marcaController = new MarcaController();

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion === 'crear') {
        $productoController->crear();
    } elseif ($accion === 'actualizar') {
        $productoController->actualizar();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion'])) {
    if ($_GET['accion'] === 'cambiar_estado' && isset($_GET['id']) && isset($_GET['estado'])) {
        $productoController->cambiarEstado();
    }
}

// Obtener productos con filtros
$productos = $productoController->listarAdmin();

// Obtener categorías y marcas para los filtros
$categorias = $categoriaController->listarActivas();
$marcas = $marcaController->listarActivas();

// Variables para el formulario
$accionForm = $_GET['accion'] ?? '';
$idProducto = $_GET['id'] ?? null;
$productoEditar = null;

if ($accionForm === 'editar' && $idProducto) {
    $productoEditar = $productoController->verDetalle();
}

include 'includes/header.php';
?>

<h1 class="titulo-pagina"> Gestión de Productos</h1>

<!-- MENSAJES -->
<?php if (isset($_SESSION['exito'])): ?>
    <div class="mensaje-exito">
         <?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="mensaje-error">
         <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['errores'])): ?>
    <div class="mensaje-error">
        <strong> Errores en el formulario:</strong>
        <ul>
            <?php foreach ($_SESSION['errores'] as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php unset($_SESSION['errores']); ?>
<?php endif; ?>

<?php if ($accionForm === '' || $accionForm === 'listar'): ?>
    <!-- VISTA DE LISTADO -->
    
    <!-- FILTROS -->
    <div class="filtros">
        <form method="GET" action="productos.php" style="display: flex; gap: 15px; flex-wrap: wrap; width: 100%;">
            <div class="form-group">
                <label>Categoría:</label>
                <select name="categoria">
                    <option value="">Todas</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['IdCategoria']; ?>" 
                                <?php echo (isset($_GET['categoria']) && $_GET['categoria'] == $cat['IdCategoria']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['NombreCategoria']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Marca:</label>
                <select name="marca">
                    <option value="">Todas</option>
                    <?php foreach ($marcas as $marca): ?>
                        <option value="<?php echo $marca['IdMarca']; ?>"
                                <?php echo (isset($_GET['marca']) && $_GET['marca'] == $marca['IdMarca']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($marca['NombreMarca']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Estado:</label>
                <select name="estado">
                    <option value="">Todos</option>
                    <option value="1" <?php echo (isset($_GET['estado']) && $_GET['estado'] == '1') ? 'selected' : ''; ?>>Activos</option>
                    <option value="0" <?php echo (isset($_GET['estado']) && $_GET['estado'] == '0') ? 'selected' : ''; ?>>Inactivos</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Buscar:</label>
                <input type="text" name="busqueda" placeholder="Nombre del producto..." 
                       value="<?php echo $_GET['busqueda'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-naranja"> Filtrar</button>
            </div>
            
            <div class="form-group">
                <label>&nbsp;</label>
                <a href="productos.php" class="btn btn-blanco"> Limpiar</a>
            </div>
        </form>
    </div>
    
    <!-- BOTÓN NUEVO PRODUCTO -->
    <div style="margin-bottom: 20px;">
        <a href="productos.php?accion=nuevo" class="btn btn-naranja">
             Nuevo Producto
        </a>
    </div>
    <div class="form-group">
    <label>Imagen del Producto:</label>
    <?php if ($accionForm === 'editar' && !empty($productoEditar['ImagenPrincipal'])): ?>
        <div style="margin-bottom: 10px;">
            <img src="../../assets/img/productos/<?php echo $productoEditar['ImagenPrincipal']; ?>" 
                 alt="Imagen actual" 
                 style="max-width: 200px; border: 1px solid #ddd; padding: 5px;">
        </div>
    <?php endif; ?>
    <input type="file" name="imagen_producto" accept="image/jpeg,image/png,image/jpg,image/webp">
    <small>Formatos permitidos: JPG, PNG, WEBP. Tamaño máximo: 2MB</small>
</div>
    <!-- TABLA DE PRODUCTOS -->
    <table class="tabla">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Marca</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($productos)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 30px;">
                        No se encontraron productos
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($producto['CodigoProducto']); ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($producto['NombreProducto']); ?></strong>
                            <?php if ($producto['AlertaStockBajo']): ?>
                                <span class="badge badge-rojo"> Stock Bajo</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($producto['NombreCategoria']); ?></td>
                        <td><?php echo htmlspecialchars($producto['NombreMarca']); ?></td>
                        <td>$<?php echo number_format($producto['PrecioUnitario'], 2); ?></td>
                        <td>
                            <strong><?php echo $producto['CantidadDisponible']; ?></strong>
                            <?php if ($producto['CantidadReservada'] > 0): ?>
                                <br><small style="color: #666;">Reservado: <?php echo $producto['CantidadReservada']; ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($producto['Estado'] == 1): ?>
                                <span class="badge badge-verde">Activo</span>
                            <?php else: ?>
                                <span class="badge badge-rojo">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="acciones">
                                <a href="productos.php?accion=editar&id=<?php echo $producto['IdProducto']; ?>" 
                                   class="btn btn-blanco">
                                     Editar
                                </a>
                                
                                <?php if ($producto['Estado'] == 1): ?>
                                    <a href="productos.php?accion=cambiar_estado&id=<?php echo $producto['IdProducto']; ?>&estado=0" 
                                       class="btn btn-rojo"
                                       onclick="return confirmarAccion('¿Desactivar este producto?')">
                                         Desactivar
                                    </a>
                                <?php else: ?>
                                    <a href="productos.php?accion=cambiar_estado&id=<?php echo $producto['IdProducto']; ?>&estado=1" 
                                       class="btn btn-verde"
                                       onclick="return confirmarAccion('¿Activar este producto?')">
                                         Activar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

<?php elseif ($accionForm === 'nuevo' || $accionForm === 'editar'): ?>
    <!-- FORMULARIO DE PRODUCTO -->
    
    <div style="margin-bottom: 20px;">
        <a href="productos.php" class="btn btn-blanco">← Volver al listado</a>
    </div>
    
    <div class="tarjeta">
        <h2><?php echo $accionForm === 'nuevo' ? 'Crear Nuevo Producto' : 'Editar Producto'; ?></h2>
        
        <form method="POST" action="productos.php" enctype="multipart/form-data">
            <input type="hidden" name="accion" value="<?php echo $accionForm === 'nuevo' ? 'crear' : 'actualizar'; ?>">
            <?php if ($accionForm === 'editar'): ?>
                <input type="hidden" name="id_producto" value="<?php echo $productoEditar['IdProducto']; ?>">
            <?php endif; ?>
            
            <div class="grid grid-2">
                <!-- CÓDIGO PRODUCTO (solo en crear) -->
                <?php if ($accionForm === 'nuevo'): ?>
                    <div class="form-group">
                        <label>Código de Producto: *</label>
                        <input type="text" name="codigo_producto" required
                               value="<?php echo $_SESSION['datos_form']['codigo_producto'] ?? ''; ?>"
                               placeholder="Ej: PROD-001">
                    </div>
                <?php endif; ?>
                
                <!-- NOMBRE -->
                <div class="form-group">
                    <label>Nombre del Producto: *</label>
                    <input type="text" name="nombre_producto" required
                           value="<?php echo $productoEditar['NombreProducto'] ?? $_SESSION['datos_form']['nombre_producto'] ?? ''; ?>"
                           placeholder="Ej: Cuaderno A4 Profesional">
                </div>
                
                <!-- CATEGORÍA -->
                <div class="form-group">
                    <label>Categoría: *</label>
                    <select name="id_categoria" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat['IdCategoria']; ?>"
                                    <?php echo (isset($productoEditar['IdCategoria']) && $productoEditar['IdCategoria'] == $cat['IdCategoria']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['NombreCategoria']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- MARCA -->
                <div class="form-group">
                    <label>Marca: *</label>
                    <select name="id_marca" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($marcas as $marca): ?>
                            <option value="<?php echo $marca['IdMarca']; ?>"
                                    <?php echo (isset($productoEditar['IdMarca']) && $productoEditar['IdMarca'] == $marca['IdMarca']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($marca['NombreMarca']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- UNIDAD DE MEDIDA -->
                <div class="form-group">
                    <label>Unidad de Medida: *</label>
                    <select name="id_unidad" required>
                        <option value="1" <?php echo (isset($productoEditar['IdUnidad']) && $productoEditar['IdUnidad'] == 1) ? 'selected' : ''; ?>>Pieza</option>
                        <option value="2" <?php echo (isset($productoEditar['IdUnidad']) && $productoEditar['IdUnidad'] == 2) ? 'selected' : ''; ?>>Caja</option>
                        <option value="3" <?php echo (isset($productoEditar['IdUnidad']) && $productoEditar['IdUnidad'] == 3) ? 'selected' : ''; ?>>Paquete</option>
                    </select>
                </div>
                
                <!-- PRECIO UNITARIO -->
                <div class="form-group">
                    <label>Precio Unitario: *</label>
                    <input type="number" name="precio_unitario" step="0.01" min="0.01" required
                           value="<?php echo $productoEditar['PrecioUnitario'] ?? $_SESSION['datos_form']['precio_unitario'] ?? ''; ?>"
                           placeholder="0.00">
                </div>
                
                <!-- COSTO UNITARIO (solo en crear) -->
                <?php if ($accionForm === 'nuevo'): ?>
                    <div class="form-group">
                        <label>Costo Unitario: *</label>
                        <input type="number" name="costo_unitario" step="0.01" min="0" required
                               value="<?php echo $_SESSION['datos_form']['costo_unitario'] ?? ''; ?>"
                               placeholder="0.00">
                    </div>
                <?php endif; ?>
                
                <!-- STOCK MÍNIMO -->
                <div class="form-group">
                    <label>Stock Mínimo: *</label>
                    <input type="number" name="stock_minimo" min="1" required
                           value="<?php echo $productoEditar['StockMinimo'] ?? $_SESSION['datos_form']['stock_minimo'] ?? '5'; ?>">
                </div>
                
                <!-- CANTIDAD INICIAL (solo en crear) -->
                <?php if ($accionForm === 'nuevo'): ?>
                    <div class="form-group">
                        <label>Cantidad Inicial:</label>
                        <input type="number" name="cantidad_inicial" min="0" 
                               value="<?php echo $_SESSION['datos_form']['cantidad_inicial'] ?? '0'; ?>">
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- IMAGEN DEL PRODUCTO -->
            <div class="form-group">
                <label>Imagen del Producto:</label>
                <?php if ($accionForm === 'editar' && !empty($productoEditar['ImagenPrincipal'])): ?>
                    <div style="margin-bottom: 10px;">
                        <img src="../../assets/img/productos/<?php echo $productoEditar['ImagenPrincipal']; ?>" 
                             alt="Imagen actual" 
                             style="max-width: 200px; border: 1px solid #ddd; padding: 5px; border-radius: 8px;">
                        <p style="font-size: 12px; color: #666; margin-top: 5px;">
                            Imagen actual: <?php echo $productoEditar['ImagenPrincipal']; ?>
                        </p>
                    </div>
                <?php endif; ?>
                <input type="file" name="imagen_producto" accept="image/jpeg,image/png,image/jpg,image/webp">
                <small style="color: #666;">Formatos: JPG, PNG, WEBP. Tamaño máximo: 2MB. 
                    <?php if ($accionForm === 'editar'): ?>
                        (Dejar vacío para mantener la imagen actual)
                    <?php endif; ?>
                </small>
            </div>
            
            <!-- DESCRIPCIÓN CORTA -->
            <div class="form-group">
                <label>Descripción Corta:</label>
                <input type="text" name="descripcion_corta" maxlength="255"
                       value="<?php echo $productoEditar['DescripcionCorta'] ?? $_SESSION['datos_form']['descripcion_corta'] ?? ''; ?>"
                       placeholder="Breve descripción (máx. 255 caracteres)">
            </div>
            
            <!-- DESCRIPCIÓN COMPLETA -->
            <div class="form-group">
                <label>Descripción Completa:</label>
                <textarea name="descripcion" placeholder="Descripción detallada del producto..."><?php echo $productoEditar['Descripcion'] ?? $_SESSION['datos_form']['descripcion'] ?? ''; ?></textarea>
            </div>
            
            <!-- BOTONES -->
            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <button type="submit" class="btn btn-naranja">
                    <?php echo $accionForm === 'nuevo' ? '➕ Crear Producto' : '✏️ Actualizar Producto'; ?>
                </button>
                <a href="productos.php" class="btn btn-blanco">❌ Cancelar</a>
            </div>
        </form>
    </div>
    
    <?php unset($_SESSION['datos_form']); ?>
<?php endif; ?>
<?php include 'includes/footer.php'; ?>