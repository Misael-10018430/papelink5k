<?php
require_once __DIR__ . '/../../controllers/MarcaController.php';

$marcaController = new MarcaController();

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion === 'crear') {
        $marcaController->crear();
    } elseif ($accion === 'actualizar') {
        $marcaController->actualizar();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion'])) {
    if ($_GET['accion'] === 'cambiar_estado' && isset($_GET['id']) && isset($_GET['estado'])) {
        $marcaController->cambiarEstado();
    }
}

// Obtener marcas con filtros
$estado = isset($_GET['estado']) ? (int)$_GET['estado'] : null;
$marcas = $marcaController->listarAdmin();

// Variables para el formulario
$accionForm = $_GET['accion'] ?? '';
$idMarca = $_GET['id'] ?? null;
$marcaEditar = null;

if ($accionForm === 'editar' && $idMarca) {
    foreach ($marcas as $marca) {
        if ($marca['IdMarca'] == $idMarca) {
            $marcaEditar = $marca;
            break;
        }
    }
}

include 'includes/header.php';
?>

<h1 class="titulo-pagina"> Gestión de Marcas</h1>

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

<div class="grid grid-2">
    <!-- COLUMNA IZQUIERDA: FORMULARIO -->
    <div>
        <div class="tarjeta">
            <h2><?php echo $accionForm === 'editar' ? ' Editar Marca' : ' Nueva Marca'; ?></h2>
            
            <form method="POST" action="marcas.php">
                <input type="hidden" name="accion" value="<?php echo $accionForm === 'editar' ? 'actualizar' : 'crear'; ?>">
                <?php if ($accionForm === 'editar' && $marcaEditar): ?>
                    <input type="hidden" name="id_marca" value="<?php echo $marcaEditar['IdMarca']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Nombre de la Marca: *</label>
                    <input type="text" 
                           name="nombre_marca" 
                           required
                           maxlength="50"
                           value="<?php echo $marcaEditar['NombreMarca'] ?? $_SESSION['datos_form']['nombre_marca'] ?? ''; ?>"
                           placeholder="Ej: Norma">
                </div>
                
                <div class="form-group">
                    <label>URL del Logo:</label>
                    <input type="text" 
                           name="logo_marca"
                           maxlength="255"
                           value="<?php echo $marcaEditar['LogoMarca'] ?? $_SESSION['datos_form']['logo_marca'] ?? ''; ?>"
                           placeholder="https://ejemplo.com/logo.png">
                    <small style="color: #666;">Opcional: URL de la imagen del logo de la marca</small>
                </div>
                
                <div class="form-group">
                    <label>Descripción:</label>
                    <textarea name="descripcion_marca" 
                              maxlength="500"
                              placeholder="Breve descripción de la marca..."><?php echo $marcaEditar['DescripcionMarca'] ?? $_SESSION['datos_form']['descripcion_marca'] ?? ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Sitio Web:</label>
                    <input type="url" 
                           name="sitio_web"
                           maxlength="255"
                           value="<?php echo $marcaEditar['SitioWeb'] ?? $_SESSION['datos_form']['sitio_web'] ?? ''; ?>"
                           placeholder="https://www.marca.com">
                    <small style="color: #666;">Opcional: Sitio web oficial de la marca</small>
                </div>
                
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-naranja">
                        <?php echo $accionForm === 'editar' ? ' Actualizar' : ' Crear Marca'; ?>
                    </button>
                    
                    <?php if ($accionForm === 'editar'): ?>
                        <a href="marcas.php" class="btn btn-blanco">❌ Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <?php unset($_SESSION['datos_form']); ?>
        
        <!-- INFORMACIÓN ADICIONAL -->
        <div class="tarjeta" style="margin-top: 20px; background-color: #f0f8ff; border-color: #2C3E50;">
            <h3>Información</h3>
            <p><strong>Total de marcas:</strong> <?php echo count($marcas); ?></p>
            <p><strong>Marcas activas:</strong> 
                <?php 
                    $activas = 0;
                    foreach ($marcas as $marca) {
                        if ($marca['Estado'] == 1) $activas++;
                    }
                    echo $activas;
                ?>
            </p>
            <p style="color: #666; font-size: 14px; margin-top: 10px;">
                Las marcas ayudan a los clientes a identificar y filtrar productos de fabricantes específicos. 
                Puedes agregar logos y enlaces al sitio web oficial para proporcionar más información.
            </p>
        </div>
    </div>
    
    <!-- COLUMNA DERECHA: LISTADO -->
    <div>
        <!-- FILTROS -->
        <div class="filtros">
            <form method="GET" action="marcas.php" style="display: flex; gap: 15px;">
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
                    <button type="submit" class="btn btn-naranja"> Filtrar</button>
                </div>
                
                <div class="form-group">
                    <label>&nbsp;</label>
                    <a href="marcas.php" class="btn btn-blanco"> Limpiar</a>
                </div>
            </form>
        </div>
        
        <!-- TABLA DE MARCAS -->
        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Marca</th>
                    <th>Productos</th>
                    <th>Web</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($marcas)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px;">
                            No se encontraron marcas
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($marcas as $marca): ?>
                        <tr>
                            <td><?php echo $marca['IdMarca']; ?></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <?php if (!empty($marca['LogoMarca'])): ?>
                                        <img src="<?php echo htmlspecialchars($marca['LogoMarca']); ?>" 
                                             alt="Logo" 
                                             style="width: 40px; height: 40px; object-fit: contain; border: 1px solid #ddd; border-radius: 4px; padding: 2px;">
                                    <?php endif; ?>
                                    <div>
                                        <strong><?php echo htmlspecialchars($marca['NombreMarca']); ?></strong>
                                        <?php if (!empty($marca['DescripcionMarca'])): ?>
                                            <br><small style="color: #666;">
                                                <?php echo htmlspecialchars(substr($marca['DescripcionMarca'], 0, 50)); ?>
                                                <?php echo strlen($marca['DescripcionMarca']) > 50 ? '...' : ''; ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-azul">
                                    <?php echo $marca['TotalProductos'] ?? 0; ?> productos
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($marca['SitioWeb'])): ?>
                                    <a href="<?php echo htmlspecialchars($marca['SitioWeb']); ?>" 
                                       target="_blank" 
                                       class="btn btn-blanco" 
                                       style="font-size: 12px; padding: 5px 10px;">
                                         Visitar
                                    </a>
                                <?php else: ?>
                                    <span style="color: #999;">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($marca['Estado'] == 1): ?>
                                    <span class="badge badge-verde">Activa</span>
                                <?php else: ?>
                                    <span class="badge badge-rojo">Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="acciones">
                                    <a href="marcas.php?accion=editar&id=<?php echo $marca['IdMarca']; ?>" 
                                       class="btn btn-blanco">
                                         Editar
                                    </a>
                                    
                                    <?php if ($marca['Estado'] == 1): ?>
                                        <a href="marcas.php?accion=cambiar_estado&id=<?php echo $marca['IdMarca']; ?>&estado=0" 
                                           class="btn btn-rojo"
                                           onclick="return confirmarAccion('¿Desactivar esta marca?')">
                                            
                                        </a>
                                    <?php else: ?>
                                        <a href="marcas.php?accion=cambiar_estado&id=<?php echo $marca['IdMarca']; ?>&estado=1" 
                                           class="btn btn-verde"
                                           onclick="return confirmarAccion('¿Activar esta marca?')">
                                            
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
```
