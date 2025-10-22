<?php
/**
 * Vista: Formulario de Proveedor
 * Crear o editar proveedores
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/ProveedorController.php';

$idProveedor = isset($_GET['id']) ? (int)$_GET['id'] : null;

$controller = new ProveedorController();
$datos = $controller->mostrarFormulario($idProveedor);

$titulo = ($datos['modo'] === 'editar') ? "Editar Proveedor - Papelink" : "Nuevo Proveedor - Papelink";
include __DIR__ . '/includes/header.php';
?>

<style>
    body {
        background-color: #f5f5f5;
        font-family: Arial, sans-serif;
    }
    
    .contenedor {
        padding: 25px;
        max-width: 900px;
        margin: 0 auto;
    }
    
    .breadcrumb {
        background: none;
        padding: 0;
        margin-bottom: 20px;
        font-size: 13px;
    }
    
    .breadcrumb a {
        color: #FF6347;
        text-decoration: none;
    }
    
    .breadcrumb a:hover {
        text-decoration: underline;
    }
    
    .breadcrumb-separador {
        color: #999;
        margin: 0 8px;
    }
    
    .titulo-principal {
        color: #2C3E50;
        font-size: 26px;
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .subtitulo {
        color: #7f8c8d;
        font-size: 14px;
        margin-bottom: 25px;
    }
    
    .tarjeta {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        padding: 30px;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .form-campo {
        margin-bottom: 20px;
    }
    
    .form-campo label {
        display: block;
        color: #2C3E50;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 8px;
    }
    
    .form-campo label span {
        color: #FF6347;
    }
    
    .form-campo input,
    .form-campo textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        font-family: Arial, sans-serif;
    }
    
    .form-campo input:focus,
    .form-campo textarea:focus {
        outline: none;
        border-color: #FF6347;
    }
    
    .form-campo textarea {
        resize: vertical;
        min-height: 80px;
    }
    
    .botones-grupo {
        display: flex;
        gap: 12px;
        margin-top: 30px;
    }
    
    .btn {
        padding: 11px 24px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        border: none;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s;
    }
    
    .btn-naranja {
        background-color: #FF6347;
        color: white;
    }
    
    .btn-naranja:hover {
        background-color: #e5533d;
    }
    
    .btn-gris {
        background-color: white;
        color: #2C3E50;
        border: 1px solid #ddd;
    }
    
    .btn-gris:hover {
        background-color: #f5f5f5;
    }
    
    .alerta {
        padding: 14px 18px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 14px;
    }
    
    .alerta-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .lista-errores {
        margin: 10px 0 0 0;
        padding-left: 20px;
    }
    
    .lista-errores li {
        margin-bottom: 5px;
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
        <div class="alerta alerta-error">
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
                <div class="form-campo" style="grid-column: 1 / -1;">
                    <label>Nombre del Proveedor <span>*</span></label>
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
                <div class="form-campo" style="grid-column: 1 / -1;">
                    <label>Dirección</label>
                    <textarea name="direccion" 
                              placeholder="Dirección completa del proveedor"><?php echo isset($_SESSION['datos_form']['direccion']) ? htmlspecialchars($_SESSION['datos_form']['direccion']) : ($datos['proveedor']['Direccion'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="botones-grupo">
                <button type="submit" class="btn btn-naranja">
                    <?php echo $datos['modo'] === 'editar' ? 'Actualizar Proveedor' : 'Crear Proveedor'; ?>
                </button>
                <a href="proveedores.php" class="btn btn-gris">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php 
    // Limpiar datos del formulario después de mostrarlos
    unset($_SESSION['datos_form']); 
?>

<?php include __DIR__ . '/includes/footer.php'; ?>