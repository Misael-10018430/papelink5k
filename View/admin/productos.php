<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/Auth.php';
Auth::checkEmpleadoLogin();
Auth::requiereAlgunaFuncionalidad(['PRODUCTOS_VER', 'PRODUCTOS_CREAR', 'PRODUCTOS_EDITAR']);

 $paginaActual = basename($_SERVER['PHP_SELF'], '.php');

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
 $accion = $_GET['accion'] ?? '';
 $accionForm = $_GET['accion'] ?? '';
 $idProducto = $_GET['id'] ?? null;
 $productoEditar = null;
if ($accionForm === 'editar' && $idProducto) {
    $productoEditar = $productoController->verDetalle();
}

include 'includes/header.php';
?>

<!-- ===================================
     ESTILOS CSS PROFESIONAL INTEGRADOS
     =================================== -->
<style>
    /* ===================================
       VARIABLES DE COLOR Y ESTILOS GENERALES
       =================================== */
    :root {
        --color-primario: #495057;       /* Gris Oscuro para botones principales */
        --color-primario-hover: #343a40; /* Gris más oscuro para hover */
        --color-secundario: #6c757d;     /* Gris medio para texto secundario */
        --color-exito: #28a745;          /* Verde estándar para éxito */
        --color-advertencia: #ffc107;    /* Amarillo estándar para advertencia */
        --color-peligro: #dc3545;        /* Rojo estándar para peligro/errores */
        --color-info: #17a2b8;           /* Azul estándar para información */
        --color-texto: #212529;          /* Negro suave para texto principal */
        --color-texto-claro: #6c757d;    /* Gris para texto secundario */
        --color-fondo: #f8f9fa;          /* Fondo muy claro */
        --color-blanco: #ffffff;
        --color-borde: #dee2e6;          /* Gris claro para bordes */
        --border-radius: 4px;            /* Bordes más sutiles */
        --sombra: 0 2px 4px rgba(0,0,0,0.05); /* Sombra muy ligera */
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        background-color: var(--color-fondo);
        color: var(--color-texto);
        line-height: 1.6;
        margin: 0;
        padding: 2rem;
    }

    /* ===================================
       TIPOGRAFÍA
       =================================== */
    h1.titulo-pagina {
        font-size: 1.75rem;
        font-weight: 600;
        color: var(--color-texto);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--color-borde);
    }
    .tarjeta h2 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--color-texto);
        margin-top: 0;
        margin-bottom: 1.5rem;
    }

    /* ===================================
       SISTEMA DE GRID
       =================================== */
    .grid {
        display: grid;
        gap: 1.5rem;
    }
    .grid-2 {
        grid-template-columns: repeat(2, 1fr);
    }
    @media (max-width: 768px) {
        .grid-2 { grid-template-columns: 1fr; }
    }

    /* ===================================
       COMPONENTES: TARJETAS
       =================================== */
    .tarjeta, .tarjeta-metrica {
        background: var(--color-blanco);
        padding: 1.5rem;
        border-radius: var(--border-radius);
        border: 1px solid var(--color-borde);
    }
    .tarjeta-metrica {
        text-align: center;
    }
    .tarjeta-metrica h3 {
        font-size: 2rem;
        font-weight: 600;
        color: var(--color-primario);
        margin: 0 0 0.5rem 0;
    }
    .tarjeta-metrica p {
        color: var(--color-texto-claro);
        margin: 0;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ===================================
       COMPONENTES: MENSAJES
       =================================== */
    .mensaje-exito, .mensaje-error {
        padding: 1rem 1.25rem;
        border-radius: var(--border-radius);
        margin-bottom: 1.5rem;
        border: 1px solid;
        font-weight: 500;
    }
    .mensaje-exito {
        background-color: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
    }
    .mensaje-error {
        background-color: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
    }
    .mensaje-error ul {
        margin: 0.5rem 0 0 0;
        padding-left: 20px;
    }

    /* ===================================
       COMPONENTES: FILTROS Y FORMULARIOS
       =================================== */
    .filtros {
        background: var(--color-blanco);
        padding: 1.5rem;
        border-radius: var(--border-radius);
        border: 1px solid var(--color-borde);
        margin-bottom: 1.5rem;
    }
    .form-group {
        margin-bottom: 1rem;
    }
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--color-texto);
    }
    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group input[type="file"],
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--color-borde);
        border-radius: var(--border-radius);
        font-size: 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--color-primario);
        box-shadow: 0 0 0 2px rgba(73, 80, 87, 0.25);
    }
    .form-group small {
        color: var(--color-texto-claro);
        font-size: 0.8rem;
        margin-top: 0.25rem;
        display: block;
    }

    /* ===================================
       COMPONENTES: BOTONES
       =================================== */
    .btn {
        display: inline-block;
        padding: 0.75rem 1rem;
        border: 1px solid transparent;
        border-radius: var(--border-radius);
        font-size: 0.875rem;
        font-weight: 500;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        transition: background-color 0.2s, border-color 0.2s;
        line-height: 1.5;
    }
    .btn-primario {
        background-color: var(--color-primario);
        color: var(--color-blanco);
        border-color: var(--color-primario);
    }
    .btn-primario:hover {
        background-color: var(--color-primario-hover);
        border-color: var(--color-primario-hover);
    }
    .btn-blanco {
        background-color: var(--color-blanco);
        color: var(--color-texto);
        border-color: var(--color-borde);
    }
    .btn-blanco:hover {
        background-color: #e2e6ea;
        border-color: #dae0e5;
    }
    .btn-exito {
        background-color: var(--color-exito);
        color: var(--color-blanco);
        border-color: var(--color-exito);
    }
    .btn-exito:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
    .btn-peligro {
        background-color: var(--color-peligro);
        color: var(--color-blanco);
        border-color: var(--color-peligro);
    }
    .btn-peligro:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }

    /* ===================================
       COMPONENTES: TABLA
       =================================== */
    .tabla {
        width: 100%;
        background: var(--color-blanco);
        border-radius: var(--border-radius);
        border: 1px solid var(--color-borde);
        overflow: hidden;
        border-collapse: collapse;
    }
    .tabla thead { background-color: var(--color-fondo); }
    .tabla th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--color-texto);
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--color-borde);
    }
    .tabla td {
        padding: 1rem;
        vertical-align: middle;
        border-top: 1px solid var(--color-borde);
    }
    .tabla tbody tr:hover { background-color: rgba(0,0,0,.02); }
    .tabla td[colspan] {
        text-align: center;
        color: var(--color-texto-claro);
    }

    /* ===================================
       COMPONENTES: BADGES
       =================================== */
    .badge {
        display: inline-block;
        padding: 0.25em 0.6em;
        font-size: 0.75em;
        font-weight: 600;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
        text-transform: uppercase;
    }
    .badge-exito { background-color: #d4edda; color: #155724; }
    .badge-peligro { background-color: #f8d7da; color: #721c24; }

    /* ===================================
       COMPONENTES: ACCIONES Y UTILIDADES
       =================================== */
    .acciones {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .contenedor-botones { margin-bottom: 1.25rem; }
    .imagen-previa {
        max-width: 200px;
        border: 1px solid var(--color-borde);
        padding: 5px;
        border-radius: var(--border-radius);
        margin-bottom: 0.5rem;
    }
    .botones-formulario {
        margin-top: 1.25rem;
        display: flex;
        gap: 0.625rem;
    }
</style>

<!-- CONTENIDO DE LA PÁGINA -->
<h1 class="titulo-pagina">Gestión de Productos</h1>

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
        <strong>Errores en el formulario:</strong>
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
        <form method="GET" action="<?php echo BASE_URL; ?>view/admin/productos.php" class="form-filtros">
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
                <button type="submit" class="btn btn-primario">Filtrar</button>
            </div>            
            <div class="form-group">
                <a href="<?php echo BASE_URL; ?>view/admin/productos.php" class="btn btn-blanco">Limpiar</a>
            </div>
        </form>
    </div>

    <!-- BOTÓN NUEVO PRODUCTO -->
    <div class="contenedor-botones">
        <?php if (Auth::esAdministrador() || Auth::tieneFuncionalidad('PRODUCTOS_CREAR')): ?>
        <a href="<?php echo BASE_URL; ?>view/admin/productos.php?accion=nuevo" class="btn btn-primario">
            Nuevo Producto
        </a>
        <?php endif; ?>
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
                    <td colspan="8" class="celda-vacia">
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
                        <span class="badge badge-peligro">Stock Bajo</span>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($producto['NombreCategoria']); ?></td>
                <td><?php echo htmlspecialchars($producto['NombreMarca']); ?></td>
                <td>$<?php echo number_format($producto['PrecioUnitario'], 2); ?></td>
                <td>
                    <strong><?php echo $producto['CantidadDisponible']; ?></strong>
                    <?php if ($producto['CantidadReservada'] > 0): ?>
                        <br><small class="texto-secundario">Reservado: <?php echo $producto['CantidadReservada']; ?></small>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($producto['Estado'] == 1): ?>
                        <span class="badge badge-exito">Activo</span>
                    <?php else: ?>
                        <span class="badge badge-peligro">Inactivo</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="acciones">
                        <?php if (Auth::esAdministrador() || Auth::tieneFuncionalidad('PRODUCTOS_EDITAR')): ?>
                        <a href="<?php echo BASE_URL; ?>view/admin/productos.php?accion=editar&id=<?php echo $producto['IdProducto']; ?>" 
                           class="btn btn-blanco">
                            Editar
                        </a>
                        <?php endif; ?>           
                        <?php if (Auth::esAdministrador() || Auth::tieneFuncionalidad('PRODUCTOS_ELIMINAR')): ?>
                            <?php if ($producto['Estado'] == 1): ?>
                                <a href="<?php echo BASE_URL; ?>view/admin/productos.php?accion=cambiar_estado&id=<?php echo $producto['IdProducto']; ?>&estado=0" 
                                   class="btn btn-peligro"
                                   onclick="return confirmarAccion('¿Desactivar este producto?')">
                                    Desactivar
                                </a>
                            <?php else: ?>
                                <a href="<?php echo BASE_URL; ?>view/admin/productos.php?accion=cambiar_estado&id=<?php echo $producto['IdProducto']; ?>&estado=1" 
                                   class="btn btn-exito"
                                   onclick="return confirmarAccion('¿Activar este producto?')">
                                    Activar
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if (!Auth::esAdministrador() && 
                                  !Auth::tieneFuncionalidad('PRODUCTOS_EDITAR') && 
                                  !Auth::tieneFuncionalidad('PRODUCTOS_ELIMINAR')): ?>
                            <span class="texto-secundario">Sin permisos</span>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</tbody>
    </table>
<?php elseif ($accionForm === 'nuevo' || $accionForm === 'editar'): ?>   
    <?php 
    if ($accionForm === 'nuevo') {
        if (!Auth::esAdministrador() && !Auth::tieneFuncionalidad('PRODUCTOS_CREAR')) {
            $_SESSION['error'] = 'No tiene permisos para crear productos';
            redirect('view/admin/productos.php');
            exit();
        }
    } elseif ($accionForm === 'editar') {
        if (!Auth::esAdministrador() && !Auth::tieneFuncionalidad('PRODUCTOS_EDITAR')) {
            $_SESSION['error'] = 'No tiene permisos para editar productos';
            redirect('view/admin/productos.php');
            exit();
        }
    }
    ?>

    <!-- FORMULARIO DE PRODUCTO -->   
    <div class="contenedor-botones">
        <a href="<?php echo BASE_URL; ?>view/admin/productos.php" class="btn btn-blanco">Volver al listado</a>
    </div>   
    <div class="tarjeta">
        <h2><?php echo $accionForm === 'nuevo' ? 'Crear Nuevo Producto' : 'Editar Producto'; ?></h2>
        
        <form method="POST" action="<?php echo BASE_URL; ?>view/admin/productos.php" enctype="multipart/form-data">
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
                    <div class="contenedor-imagen-previa">
                        <img src="<?php echo BASE_URL; ?>assets/img/productos/<?php echo $productoEditar['ImagenPrincipal']; ?>" 
                             alt="Imagen actual" 
                             class="imagen-previa">
                        <p class="texto-secundario">
                            Imagen actual: <?php echo $productoEditar['ImagenPrincipal']; ?>
                        </p>
                    </div>
                <?php endif; ?>
                <input type="file" name="imagen_producto" accept="image/jpeg,image/png,image/jpg,image/webp">
                <small class="texto-secundario">Formatos: JPG, PNG, WEBP. Tamaño máximo: 2MB. 
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
                <textarea name="descripcion" rows="5" placeholder="Descripción detallada del producto..."><?php echo $productoEditar['Descripcion'] ?? $_SESSION['datos_form']['descripcion'] ?? ''; ?></textarea>
            </div>            
            <!-- BOTONES -->
            <div class="botones-formulario">
                <button type="submit" class="btn btn-primario">
                    <?php echo $accionForm === 'nuevo' ? 'Crear Producto' : 'Actualizar Producto'; ?>
                </button>
                <a href="<?php echo BASE_URL; ?>view/admin/productos.php" class="btn btn-blanco">Cancelar</a>
            </div>
        </form>
    </div>    
    <?php unset($_SESSION['datos_form']); ?>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>