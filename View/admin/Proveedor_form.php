<?php
/**
 * Vista: Formulario de Proveedor
 * Crear o editar proveedores
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/Auth.php';
Auth::requiereAlgunaFuncionalidad(['PROVEEDORES_VER', 'PROVEEDORES_GESTIONAR']);

 $paginaActual = basename($_SERVER['PHP_SELF'], '.php');

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/ProveedorController.php';

 $idProveedor = isset($_GET['id']) ? (int)$_GET['id'] : null;

 $controller = new ProveedorController();
 $datos = $controller->mostrarFormulario($idProveedor);

 $titulo = ($datos['modo'] === 'editar') ? "Editar Proveedor - Papelink" : "Nuevo Proveedor - Papelink";
include __DIR__ . '/includes/header.php';
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
        padding: 0;
    }

    /* ===================================
       LAYOUT Y CONTENEDOR
       =================================== */
    .contenedor {
        padding: 2rem;
        max-width: 900px;
        margin: 0 auto;
    }

    /* ===================================
       TIPOGRAFÍA
       =================================== */
    h1.titulo-principal {
        font-size: 1.75rem;
        font-weight: 600;
        color: var(--color-texto);
        margin-bottom: 0.25rem;
    }
    .subtitulo {
        color: var(--color-texto-claro);
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
    }

    /* ===================================
       NAVEGACIÓN (BREADCRUMB)
       =================================== */
    .breadcrumb {
        background: none;
        padding: 0;
        margin-bottom: 1.25rem;
        font-size: 0.8125rem;
    }
    .breadcrumb a {
        color: var(--color-primario);
        text-decoration: none;
    }
    .breadcrumb a:hover {
        text-decoration: underline;
    }
    .breadcrumb-separador {
        color: var(--color-texto-claro);
        margin: 0 0.5rem;
    }

    /* ===================================
       COMPONENTES: TARJETAS
       =================================== */
    .tarjeta {
        background: var(--color-blanco);
        border: 1px solid var(--color-borde);
        border-radius: var(--border-radius);
        padding: 2rem;
        box-shadow: var(--sombra);
    }
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .form-campo {
        margin-bottom: 1.5rem;
    }
    .form-campo.ancho-completo { grid-column: 1 / -1; }
    .form-campo label {
        display: block;
        color: var(--color-texto);
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    .form-campo label .requerido {
        color: var(--color-peligro);
    }
    .form-campo input,
    .form-campo textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--color-borde);
        border-radius: var(--border-radius);
        font-size: 0.875rem;
        font-family: inherit;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-campo input:focus,
    .form-campo textarea:focus {
        outline: none;
        border-color: var(--color-primario);
        box-shadow: 0 0 0 2px rgba(73, 80, 87, 0.25);
    }
    .form-campo textarea {
        resize: vertical;
        min-height: 5rem;
    }

    /* ===================================
       COMPONENTES: BOTONES
       =================================== */
    .botones-grupo {
        display: flex;
        gap: 0.75rem;
        margin-top: 2rem;
    }
    .btn {
        padding: 0.6875rem 1.5rem;
        border-radius: var(--border-radius);
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        border: 1px solid transparent;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s;
        text-align: center;
        line-height: 1.5;
    }
    .btn-primario { /* Mapeo de btn-naranja */
        background-color: var(--color-primario);
        color: var(--color-blanco);
    }
    .btn-primario:hover {
        background-color: var(--color-primario-hover);
    }
    .btn-secundario { /* Mapeo de btn-gris */
        background-color: var(--color-blanco);
        color: var(--color-texto);
        border-color: var(--color-borde);
    }
    .btn-secundario:hover {
        background-color: var(--color-fondo);
    }

    /* ===================================
       COMPONENTES: ALERTAS
       =================================== */
    .alerta {
        padding: 0.875rem 1.125rem;
        border-radius: var(--border-radius);
        margin-bottom: 1.25rem;
        font-size: 0.875rem;
        border: 1px solid;
    }
    .alerta-peligro { /* Mapeo de alerta-error */
        background-color: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
    }
    .lista-errores {
        margin: 0.5rem 0 0 0;
        padding-left: 1.25rem;
    }
    .lista-errores li {
        margin-bottom: 0.25rem;
    }
</style>

<div class="contenedor">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="dashboard.php">Dashboard</a>
        <span class="breadcrumb-separador">/</span>
        <a href="proveedores.php">Proveedores</a>
        <span class="breadcrumb-separador">/</span>
        <span><?php echo $datos['modo'] === 'editar' ? 'Editar' : 'Nuevo'; ?></span>
    </div>

    <h1 class="titulo-principal">
        <?php echo $datos['modo'] === 'editar' ? 'Editar Proveedor' : 'Nuevo Proveedor'; ?>
    </h1>
    <p class="subtitulo">
        <?php echo $datos['modo'] === 'editar' ? 'Actualiza la información del proveedor' : 'Completa el formulario para agregar un nuevo proveedor'; ?>
    </p>

    <?php if (isset($_SESSION['errores'])): ?>
        <div class="alerta alerta-peligro">
            <strong>Por favor corrige los siguientes errores:</strong>
            <ul class="lista-errores">
                <?php 
                    foreach ($_SESSION['errores'] as $error) {
                        echo "<li>$error</li>";
                    }
                    unset($_SESSION['errores']);
                ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="tarjeta">
        <form method="POST" action="../../controllers/ProveedorController.php?action=guardar">
            <?php if ($datos['modo'] === 'editar'): ?>
                <input type="hidden" name="id_proveedor" value="<?php echo $datos['proveedor']['IdProveedor']; ?>">
            <?php endif; ?>

            <div class="form-grid">
                <!-- Nombre del Proveedor -->
                <div class="form-campo ancho-completo">
                    <label>Nombre del Proveedor <span class="requerido">*</span></label>
                    <input type="text" 
                           name="nombre" 
                           required
                           value="<?php echo isset($_SESSION['datos_form']['nombre']) ? htmlspecialchars($_SESSION['datos_form']['nombre']) : ($datos['proveedor']['NombreProveedor'] ?? ''); ?>"
                           placeholder="Ej: Distribuidora ABC">
                </div>

                <!-- Teléfono -->
                <div class="form-campo">
                    <label>Teléfono</label>
                    <input type="tel" 
                           name="telefono"
                           value="<?php echo isset($_SESSION['datos_form']['telefono']) ? htmlspecialchars($_SESSION['datos_form']['telefono']) : ($datos['proveedor']['Telefono'] ?? ''); ?>"
                           placeholder="Ej: 9611234567">
                </div>

                <!-- Email -->
                <div class="form-campo">
                    <label>Email</label>
                    <input type="email" 
                           name="email"
                           value="<?php echo isset($_SESSION['datos_form']['email']) ? htmlspecialchars($_SESSION['datos_form']['email']) : ($datos['proveedor']['Email'] ?? ''); ?>"
                           placeholder="Ej: proveedor@ejemplo.com">
                </div>

                <!-- Dirección -->
                <div class="form-campo ancho-completo">
                    <label>Dirección</label>
                    <textarea name="direccion" 
                              placeholder="Dirección completa del proveedor"><?php echo isset($_SESSION['datos_form']['direccion']) ? htmlspecialchars($_SESSION['datos_form']['direccion']) : ($datos['proveedor']['Direccion'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="botones-grupo">
                <button type="submit" class="btn btn-primario">
                    <?php echo $datos['modo'] === 'editar' ? 'Actualizar Proveedor' : 'Crear Proveedor'; ?>
                </button>
                <a href="proveedores.php" class="btn btn-secundario">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php 
    // Limpiar datos del formulario después de mostrarlos
    unset($_SESSION['datos_form']); 
?>

<?php include __DIR__ . '/includes/footer.php'; ?>