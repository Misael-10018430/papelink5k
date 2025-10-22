<?php
/**
 * Vista: Formulario de Empleado
 * Crear o editar empleados
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/EmpleadoController.php';

$idEmpleado = isset($_GET['id']) ? (int)$_GET['id'] : null;

$controller = new EmpleadoController();
$datos = $controller->mostrarFormulario($idEmpleado);

$titulo = ($datos['modo'] === 'editar') ? "Editar Empleado - Papelink" : "Nuevo Empleado - Papelink";
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
    .form-campo select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        font-family: Arial, sans-serif;
    }
    
    .form-campo input:focus,
    .form-campo select:focus {
        outline: none;
        border-color: #FF6347;
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
    
    .nota {
        background-color: #e3f2fd;
        color: #1565c0;
        padding: 12px;
        border-radius: 4px;
        font-size: 13px;
        margin-bottom: 20px;
    }
</style>

<div class="contenedor">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="dashboard.php">Dashboard</a>
        <span class="breadcrumb-separador">/</span>
        <a href="empleados.php">Empleados</a>
        <span class="breadcrumb-separador">/</span>
        <span><?php echo $datos['modo'] === 'editar' ? 'Editar' : 'Nuevo'; ?></span>
    </div>

    <h1 class="titulo-principal">
        <?php echo $datos['modo'] === 'editar' ? 'Editar Empleado' : 'Nuevo Empleado'; ?>
    </h1>
    <p class="subtitulo">
        <?php echo $datos['modo'] === 'editar' ? 'Actualiza la información del empleado' : 'Completa el formulario para agregar un nuevo empleado'; ?>
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

    <?php if ($datos['modo'] === 'editar'): ?>
        <div class="nota">
            <strong>Nota:</strong> No puedes cambiar el usuario ni la contraseña desde aquí. Para cambiar la contraseña, usa la opción "Gestionar Roles".
        </div>
    <?php endif; ?>

    <div class="tarjeta">
        <form method="POST" action="../../controllers/EmpleadoController.php?action=guardar">
            <?php if ($datos['modo'] === 'editar'): ?>
                <input type="hidden" name="id_empleado" value="<?php echo $datos['empleado']['IdEmpleado']; ?>">
            <?php endif; ?>

            <div class="form-grid">
                <!-- Nombre Completo -->
                <div class="form-campo" style="grid-column: 1 / -1;">
                    <label>Nombre Completo <span>*</span></label>
                    <input type="text" 
                           name="nombre" 
                           required
                           value="<?php echo isset($_SESSION['datos_form']['nombre']) ? htmlspecialchars($_SESSION['datos_form']['nombre']) : ($datos['empleado']['NombreCompleto'] ?? ''); ?>"
                           placeholder="Ej: Juan Pérez García">
                </div>

                <?php if ($datos['modo'] === 'crear'): ?>
                <!-- Usuario (solo al crear) -->
                <div class="form-campo">
                    <label>Usuario <span>*</span></label>
                    <input type="text" 
                           name="usuario" 
                           required
                           value="<?php echo isset($_SESSION['datos_form']['usuario']) ? htmlspecialchars($_SESSION['datos_form']['usuario']) : ''; ?>"
                           placeholder="Ej: jperez">
                </div>

                <!-- Contraseña (solo al crear) -->
                <div class="form-campo">
                    <label>Contraseña <span>*</span></label>
                    <input type="password" 
                           name="password" 
                           required
                           placeholder="Mínimo 6 caracteres">
                </div>
                <?php endif; ?>

                <!-- Email -->
                <div class="form-campo">
                    <label>Email</label>
                    <input type="email" 
                           name="email"
                           value="<?php echo isset($_SESSION['datos_form']['email']) ? htmlspecialchars($_SESSION['datos_form']['email']) : ($datos['empleado']['Email'] ?? ''); ?>"
                           placeholder="Ej: juan@papelink.com">
                </div>

                <?php if ($datos['modo'] === 'crear'): ?>
                <!-- Rol Inicial (solo al crear) -->
                <div class="form-campo">
                    <label>Rol Inicial</label>
                    <select name="id_rol">
                        <option value="">Sin rol asignado</option>
                        <?php foreach ($datos['roles'] as $rol): ?>
                            <option value="<?php echo $rol['IdRol']; ?>"
                                <?php echo (isset($_SESSION['datos_form']['id_rol']) && $_SESSION['datos_form']['id_rol'] == $rol['IdRol']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($rol['NombreRol']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
            </div>

            <div class="botones-grupo">
                <button type="submit" class="btn btn-naranja">
                    <?php echo $datos['modo'] === 'editar' ? 'Actualizar Empleado' : 'Crear Empleado'; ?>
                </button>
                <a href="empleados.php" class="btn btn-gris">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php 
    // Limpiar datos del formulario después de mostrarlos
    unset($_SESSION['datos_form']); 
?>

<?php include __DIR__ . '/includes/footer.php'; ?>