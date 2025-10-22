<?php
/**
 * Vista: Gestión de Empleados
 * Listado de empleados con filtros y acciones
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/EmpleadoController.php';

$controller = new EmpleadoController();
$datos = $controller->listar();

$titulo = "Gestión de Empleados - Papelink";
include __DIR__ . '/includes/header.php';
?>

<style>
    body {
        background-color: #f5f5f5;
        font-family: Arial, sans-serif;
    }
    
    .contenedor {
        padding: 25px;
        max-width: 1400px;
        margin: 0 auto;
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
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .grid-estadisticas {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .tarjeta-estadistica {
        background: white;
        border-left: 4px solid #FF6347;
        padding: 20px;
        border-radius: 6px;
    }
    
    .tarjeta-estadistica.verde { border-left-color: #28a745; }
    .tarjeta-estadistica.azul { border-left-color: #17a2b8; }
    
    .estadistica-label {
        color: #7f8c8d;
        font-size: 12px;
        text-transform: uppercase;
        margin-bottom: 8px;
        font-weight: 500;
    }
    
    .estadistica-numero {
        color: #2C3E50;
        font-size: 30px;
        font-weight: 700;
    }
    
    .filtros-form {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: flex-end;
    }
    
    .campo {
        flex: 1;
        min-width: 180px;
    }
    
    .campo label {
        display: block;
        color: #2C3E50;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 6px;
    }
    
    .campo select {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }
    
    .campo select:focus {
        outline: none;
        border-color: #FF6347;
    }
    
    .btn {
        padding: 9px 18px;
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
    
    .btn-verde {
        background-color: #28a745;
        color: white;
    }
    
    .btn-verde:hover {
        background-color: #218838;
    }
    
    .btn-azul {
        background-color: #17a2b8;
        color: white;
    }
    
    .btn-azul:hover {
        background-color: #138496;
    }
    
    .tabla-wrapper {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        overflow: hidden;
    }
    
    .tabla {
        width: 100%;
        border-collapse: collapse;
    }
    
    .tabla thead {
        background-color: #f8f9fa;
    }
    
    .tabla th {
        padding: 14px 12px;
        text-align: left;
        color: #2C3E50;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        border-bottom: 2px solid #e0e0e0;
    }
    
    .tabla td {
        padding: 14px 12px;
        border-bottom: 1px solid #f0f0f0;
        color: #2C3E50;
        font-size: 14px;
    }
    
    .tabla tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 500;
    }
    
    .badge-azul {
        background-color: #e3f2fd;
        color: #1976d2;
    }
    
    .toggle {
        position: relative;
        display: inline-block;
        width: 42px;
        height: 22px;
    }
    
    .toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .3s;
        border-radius: 22px;
    }
    
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }
    
    input:checked + .toggle-slider {
        background-color: #28a745;
    }
    
    input:checked + .toggle-slider:before {
        transform: translateX(20px);
    }
    
    .alerta {
        padding: 14px 18px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 14px;
    }
    
    .alerta-exito {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .alerta-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .texto-vacio {
        text-align: center;
        padding: 40px;
        color: #7f8c8d;
    }
    
    .acciones-grupo {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
</style>

<div class="contenedor">
    <h1 class="titulo-principal">Gestión de Empleados</h1>
    <p class="subtitulo">Administra empleados, roles y permisos del sistema</p>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alerta alerta-exito">
            ✓ <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alerta alerta-error">
            ❌ <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Filtros y Botón Nuevo -->
    <div class="tarjeta">
        <form method="GET" action="empleados.php" class="filtros-form">
            <div class="campo" style="max-width: 200px;">
                <label>Estado</label>
                <select name="estado">
                    <option value="">Todos</option>
                    <option value="1" <?php echo (isset($_GET['estado']) && $_GET['estado'] == '1') ? 'selected' : ''; ?>>Activos</option>
                    <option value="0" <?php echo (isset($_GET['estado']) && $_GET['estado'] == '0') ? 'selected' : ''; ?>>Inactivos</option>
                </select>
            </div>

            <button type="submit" class="btn btn-naranja">Filtrar</button>
            
            <?php if (isset($_GET['estado'])): ?>
                <a href="empleados.php" class="btn btn-gris">Limpiar</a>
            <?php endif; ?>
            
            <div style="margin-left: auto;">
                <a href="empleado_form.php" class="btn btn-verde">Nuevo Empleado</a>
            </div>
        </form>
    </div>

    <!-- Estadísticas -->
    <div class="grid-estadisticas">
        <div class="tarjeta-estadistica">
            <div class="estadistica-label">Total de Empleados</div>
            <div class="estadistica-numero"><?php echo count($datos['empleados']); ?></div>
        </div>
        <div class="tarjeta-estadistica verde">
            <div class="estadistica-label">Empleados Activos</div>
            <div class="estadistica-numero">
                <?php 
                    $activos = array_filter($datos['empleados'], function($e) { return $e['Estado'] == 1; });
                    echo count($activos); 
                ?>
            </div>
        </div>
        <div class="tarjeta-estadistica azul">
            <div class="estadistica-label">Roles Activos</div>
            <div class="estadistica-numero"><?php echo count($datos['roles']); ?></div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="tabla-wrapper">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Roles Asignados</th>
                    <th style="text-align: center;">Estado</th>
                    <th style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($datos['empleados'])): ?>
                    <tr>
                        <td colspan="6" class="texto-vacio">
                            No se encontraron empleados
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($datos['empleados'] as $empleado): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 500;">
                                    <?php echo htmlspecialchars($empleado['NombreCompleto']); ?>
                                </div>
                                <div style="font-size: 12px; color: #7f8c8d;">
                                    ID: <?php echo $empleado['IdEmpleado']; ?>
                                </div>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($empleado['Usuario']); ?></strong>
                            </td>
                            <td style="font-size: 13px;">
                                <?php echo htmlspecialchars($empleado['Email'] ?? 'Sin email'); ?>
                            </td>
                            <td>
                                <?php if (!empty($empleado['Roles'])): ?>
                                    <?php 
                                        $roles = explode(', ', $empleado['Roles']);
                                        foreach ($roles as $rol): 
                                    ?>
                                        <span class="badge badge-azul" style="margin-right: 5px;">
                                            <?php echo htmlspecialchars($rol); ?>
                                        </span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span style="color: #999; font-size: 12px;">Sin roles</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <label class="toggle">
                                    <input type="checkbox" class="estado-toggle"
                                           <?php echo $empleado['Estado'] ? 'checked' : ''; ?>
                                           data-id="<?php echo $empleado['IdEmpleado']; ?>">
                                    <span class="toggle-slider"></span>
                                </label>
                            </td>
                            <td style="text-align: center;">
                                <div class="acciones-grupo">
                                    <a href="empleado_form.php?id=<?php echo $empleado['IdEmpleado']; ?>" 
                                       class="btn btn-gris" style="padding: 6px 12px; font-size: 12px;">
                                        Editar
                                    </a>
                                    <a href="empleado_roles.php?id=<?php echo $empleado['IdEmpleado']; ?>" 
                                       class="btn btn-azul" style="padding: 6px 12px; font-size: 12px;">
                                        Roles
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.querySelectorAll('.estado-toggle').forEach(toggle => {
    toggle.addEventListener('change', function() {
        const idEmpleado = this.getAttribute('data-id');
        const nuevoEstado = this.checked ? 1 : 0;
        
        fetch('../../controllers/EmpleadoController.php?action=cambiarEstado', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id_empleado=${idEmpleado}&estado=${nuevoEstado}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.mensaje);
            } else {
                alert('Error: ' + data.mensaje);
                this.checked = !this.checked;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cambiar el estado');
            this.checked = !this.checked;
        });
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>