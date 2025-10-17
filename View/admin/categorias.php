<?php
require_once __DIR__ . '/../../controllers/CategoriaController.php';

$categoriaController = new CategoriaController();

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion === 'crear') {
        $categoriaController->crear();
    } elseif ($accion === 'actualizar') {
        $categoriaController->actualizar();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion'])) {
    if ($_GET['accion'] === 'cambiar_estado' && isset($_GET['id']) && isset($_GET['estado'])) {
        $categoriaController->cambiarEstado();
    }
}

// Obtener categorías con filtros
$estado = isset($_GET['estado']) ? (int)$_GET['estado'] : null;
$categorias = $categoriaController->listarAdmin();

// Variables para el formulario
$accionForm = $_GET['accion'] ?? '';
$idCategoria = $_GET['id'] ?? null;
$categoriaEditar = null;

if ($accionForm === 'editar' && $idCategoria) {
    foreach ($categorias as $cat) {
        if ($cat['IdCategoria'] == $idCategoria) {
            $categoriaEditar = $cat;
            break;
        }
    }
}

include 'includes/header.php';
?>

<h1 class="titulo-pagina">Gestión de Categorías</h1>

<!-- MENSAJES -->
<?php if (isset($_SESSION['exito'])): ?>
    <div class="mensaje-exito">
        <?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="mensaje-error">
        ❌ <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="grid grid-2">
    <!-- COLUMNA IZQUIERDA: FORMULARIO -->
    <div>
        <div class="tarjeta">
            <h2><?php echo $accionForm === 'editar' ? 'Editar Categoría' : 'Nueva Categoría'; ?></h2>
            
            <form method="POST" action="categorias.php">
                <input type="hidden" name="accion" value="<?php echo $accionForm === 'editar' ? 'actualizar' : 'crear'; ?>">
                <?php if ($accionForm === 'editar' && $categoriaEditar): ?>
                    <input type="hidden" name="id_categoria" value="<?php echo $categoriaEditar['IdCategoria']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Nombre de la Categoría: *</label>
                    <input type="text" 
                           name="nombre_categoria" 
                           required
                           maxlength="50"
                           value="<?php echo $categoriaEditar['NombreCategoria'] ?? $_SESSION['datos_form']['nombre_categoria'] ?? ''; ?>"
                           placeholder="Ej: Papelería">
                </div>
                
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-naranja">
                        <?php echo $accionForm === 'editar' ? 'Actualizar' : 'Crear Categoría'; ?>
                    </button>
                    
                    <?php if ($accionForm === 'editar'): ?>
                        <a href="categorias.php" class="btn btn-blanco">❌ Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <?php unset($_SESSION['datos_form']); ?>
        
        <!-- INFORMACIÓN ADICIONAL -->
        <div class="tarjeta" style="margin-top: 20px; background-color: #f0f8ff; border-color: #2C3E50;">
            <h3>Información</h3>
            <p><strong>Total de categorías:</strong> <?php echo count($categorias); ?></p>
            <p><strong>Categorías activas:</strong> 
                <?php 
                    $activas = 0;
                    foreach ($categorias as $cat) {
                        if ($cat['Estado'] == 1) $activas++;
                    }
                    echo $activas;
                ?>
            </p>
            <p style="color: #666; font-size: 14px; margin-top: 10px;">
                Las categorías se utilizan para organizar los productos en el catálogo. 
                Al desactivar una categoría, los productos asociados no se eliminarán pero 
                no aparecerán en los filtros del cliente.
            </p>
        </div>
    </div>
    
    <!-- COLUMNA DERECHA: LISTADO -->
    <div>
        <!-- FILTROS -->
        <div class="filtros">
            <form method="GET" action="categorias.php" style="display: flex; gap: 15px;">
                <div class="form-group">
                    <label>Estado:</label>
                    <select name="estado">
                        <option value="">Todas</option>
                        <option value="1" <?php echo ($estado === 1) ? 'selected' : ''; ?>>Activas</option>
                        <option value="0" <?php echo ($estado === 0) ? 'selected' : ''; ?>>Inactivas</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-naranja">Filtrar</button>
                </div>
                
                <div class="form-group">
                    <label>&nbsp;</label>
                    <a href="categorias.php" class="btn btn-blanco">Limpiar</a>
                </div>
            </form>
        </div>
        
        <!-- TABLA DE CATEGORÍAS -->
        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Productos</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categorias)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px;">
                            No se encontraron categorías
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categorias as $categoria): ?>
                        <tr>
                            <td><?php echo $categoria['IdCategoria']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($categoria['NombreCategoria']); ?></strong>
                            </td>
                            <td>
                                <span class="badge badge-azul">
                                    <?php echo $categoria['TotalProductos'] ?? 0; ?> productos
                                </span>
                            </td>
                            <td>
                                <?php if ($categoria['Estado'] == 1): ?>
                                    <span class="badge badge-verde">Activa</span>
                                <?php else: ?>
                                    <span class="badge badge-rojo">Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="acciones">
                                    <a href="categorias.php?accion=editar&id=<?php echo $categoria['IdCategoria']; ?>" 
                                       class="btn btn-blanco">
                                        Editar
                                    </a>
                                    
                                    <?php if ($categoria['Estado'] == 1): ?>
                                        <a href="categorias.php?accion=cambiar_estado&id=<?php echo $categoria['IdCategoria']; ?>&estado=0" 
                                           class="btn btn-rojo"
                                           onclick="return confirmarAccion('¿Desactivar esta categoría?')">
                                            
                                        </a>
                                    <?php else: ?>
                                        <a href="categorias.php?accion=cambiar_estado&id=<?php echo $categoria['IdCategoria']; ?>&estado=1" 
                                           class="btn btn-verde"
                                           onclick="return confirmarAccion('¿Activar esta categoría?')">
                                            
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>