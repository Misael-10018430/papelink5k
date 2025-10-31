<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/Auth.php';
Auth::requiereFuncionalidad('PRODUCTOS_EDITAR');
 $paginaActual = basename($_SERVER['PHP_SELF'], '.php');
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
    .tarjeta h2, .tarjeta h3 {
        color: var(--color-texto);
        margin-top: 0;
    }
    .tarjeta h2 { font-size: 1.25rem; margin-bottom: 1.5rem; }
    .tarjeta h3 { font-size: 1rem; margin-bottom: 0.5rem; }

    /* ===================================
       SISTEMA DE GRID
       =================================== */
    .grid {
        display: grid;
        gap: 1.5rem;
    }
    .grid-2 { grid-template-columns: repeat(2, 1fr); }
    @media (max-width: 768px) {
        .grid-2 { grid-template-columns: 1fr; }
    }

    /* ===================================
       COMPONENTES: TARJETAS
       =================================== */
    .tarjeta {
        background: var(--color-blanco);
        padding: 1.5rem;
        border-radius: var(--border-radius);
        border: 1px solid var(--color-borde);
    }
    .tarjeta-informacion { /* Tarjeta de información especial */
        background-color: #eef2f7;
        border-color: var(--color-primario);
    }
    .texto-informativo {
        color: var(--color-texto-claro);
        font-size: 0.875rem;
        margin-top: 0.5rem;
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
    .filtros form {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
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
    .form-group select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--color-borde);
        border-radius: var(--border-radius);
        font-size: 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--color-primario);
        box-shadow: 0 0 0 2px rgba(73, 80, 87, 0.25);
    }
    .botones-formulario {
        margin-top: 1.25rem;
        display: flex;
        gap: 0.625rem;
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
    .btn-primario { /* Mapeo de btn-naranja */
        background-color: var(--color-primario);
        color: var(--color-blanco);
        border-color: var(--color-primario);
    }
    .btn-primario:hover {
        background-color: var(--color-primario-hover);
        border-color: var(--color-primario-hover);
    }
    .btn-secundario { /* Mapeo de btn-blanco */
        background-color: var(--color-blanco);
        color: var(--color-texto);
        border-color: var(--color-borde);
    }
    .btn-secundario:hover {
        background-color: #e2e6ea;
        border-color: #dae0e5;
    }
    .btn-exito { /* Mapeo de btn-verde */
        background-color: var(--color-exito);
        color: var(--color-blanco);
        border-color: var(--color-exito);
    }
    .btn-exito:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
    .btn-peligro { /* Mapeo de btn-rojo */
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
    .celda-vacia {
        text-align: center;
        padding: 2rem;
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
    .badge-exito { background-color: #d4edda; color: #155724; } /* Mapeo de badge-verde */
    .badge-peligro { background-color: #f8d7da; color: #721c24; } /* Mapeo de badge-rojo */
    .badge-info { background-color: #d1ecf1; color: #0c5460; } /* Mapeo de badge-azul */

    /* ===================================
       COMPONENTES: ACCIONES
       =================================== */
    .acciones {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
</style>

<!-- CONTENIDO DE LA PÁGINA -->
<h1 class="titulo-pagina">Gestión de Categorías</h1>

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
                <div class="botones-formulario">
                    <button type="submit" class="btn btn-primario">
                        <?php echo $accionForm === 'editar' ? 'Actualizar' : 'Crear Categoría'; ?>
                    </button>
                    <?php if ($accionForm === 'editar'): ?>
                        <a href="categorias.php" class="btn btn-secundario">Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <?php unset($_SESSION['datos_form']); ?>
        <!-- INFORMACIÓN ADICIONAL -->
        <div class="tarjeta tarjeta-informacion" style="margin-top: 1.25rem;">
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
            <p class="texto-informativo">
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
            <form method="GET" action="categorias.php">
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
                    <button type="submit" class="btn btn-primario">Filtrar</button>
                </div>
                <div class="form-group">
                    <label>&nbsp;</label>
                    <a href="categorias.php" class="btn btn-secundario">Limpiar</a>
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
                        <td colspan="5" class="celda-vacia">
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
                                <span class="badge badge-info">
                                    <?php echo $categoria['TotalProductos'] ?? 0; ?> productos
                                </span>
                            </td>
                            <td>
                                <?php if ($categoria['Estado'] == 1): ?>
                                    <span class="badge badge-exito">Activa</span>
                                <?php else: ?>
                                    <span class="badge badge-peligro">Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="acciones">
                                    <a href="categorias.php?accion=editar&id=<?php echo $categoria['IdCategoria']; ?>" 
                                       class="btn btn-secundario">
                                        Editar
                                    </a>
                                    <?php if ($categoria['Estado'] == 1): ?>
                                        <a href="categorias.php?accion=cambiar_estado&id=<?php echo $categoria['IdCategoria']; ?>&estado=0" 
                                           class="btn btn-peligro"
                                           onclick="return confirmarAccion('¿Desactivar esta categoría?')">
                                        </a>
                                    <?php else: ?>
                                        <a href="categorias.php?accion=cambiar_estado&id=<?php echo $categoria['IdCategoria']; ?>&estado=1" 
                                           class="btn btn-exito"
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