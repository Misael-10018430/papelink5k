<?php
/**
 * Vista: Gestión de Empleados
 * Listado de empleados con filtros y acciones
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/Auth.php';
Auth::requiereAdministrador();
 $paginaActual = basename($_SERVER['PHP_SELF'], '.php');
require_once __DIR__ . '/../../controllers/EmpleadoController.php';
 $controller = new EmpleadoController();
 $datos = $controller->listar();
 $titulo = "Gestión de Empleados - Papelink";
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
--color-primario: #495057; /* Gris Oscuro para botones principales */
--color-primario-hover: #343a40; /* Gris más oscuro para hover */
--color-secundario: #6c757d; /* Gris medio para texto secundario */
--color-exito: #28a745; /* Verde estándar para éxito */
--color-peligro: #ffc107; /* Amarillo estándar para advertencia */
--color-peligro: #dc3545; /* Rojo estándar para peligro/errores */
--color-info: #17a2b8; /* Azul estándar para información */
--color-texto: #212529; /* Negro suave para texto principal */
--color-texto-claro: #6c757d; /* Gris para texto secundario */
--color-fondo: #f8f9fa; /* Fondo muy claro */
--color-blanco: #ffffff;
--color-borde: #dee2e6; /* Gris claro para bordes */
--border-radius: 4px; /* Bordes más sutiles */
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
max-width: 1400px;
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
COMPONENTES: TARJETAS
=================================== */
.tarjeta {
background: var(--color-blanco);
border: 1px solid var(--color-borde);
border-radius: var(--border-radius);
padding: 1.5rem;
margin-bottom: 1.5rem;
box-shadow: var(--sombra);
}
.grid-estadisticas {
display: grid;
grid-template-columns: repeat auto-fit, minmax(280px, 1fr);
gap: 1rem;
margin-bottom: 1.5rem;
}
.tarjeta-estadistica {
background: var(--color-blanco);
padding: 1.5rem;
border-radius: var(--border-radius);
border-left: 4px solid var(--color-primario);
box-shadow: var(--sombra);
}
.tarjeta-estadistica.resaltado-exito { border-left-color: var(--color-exito); }
.tarjeta-estadistica.resaltado-info { border-left-color: var(--color-info); }
.estadistica-label {
color: var(--color-texto-claro);
font-size: 0.75rem;
text-transform: uppercase;
margin-bottom: 0.5rem;
font-weight: 500;
}
.estadistica-numero {
color: var(--color-texto);
font-size: 2rem;
font-weight: 700;
margin: 0;
}

/* ===================================
COMPONENTES: FORMULARIOS Y FILTROS
=================================== */
.filtros-form {
display: flex;
gap: 0.75rem;
flex-wrap: wrap;
align-items: flex-end;
}
.campo {
flex: 1;
min-width: 180px;
}
.campo-chico { max-width: 200px; }
.campo label {
display: block;
color: var(--color-texto);
font-size: 0.8125rem;
font-weight: 500;
margin-bottom: 0.375rem;
}
.campo select {
width: 100%;
padding: 0.5625rem 0.75rem;
border: 1px solid var(--color-borde);
border-radius: var(--border-radius);
font-size: 0.875rem;
transition: border-color 0.2s, box-shadow 0.2s;
}
.campo select:focus {
outline: none;
border-color: var(--color-primario);
box-shadow: 0 0 0 2px rgba(73, 80, 87, 0.25);
}
.contenedor-izquierda { margin-left: auto; }

/* ===================================
COMPONENTES: BOTONES
=================================== */
.btn {
padding: 0.5625rem 1.125rem;
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
.btn-exito { /* Mapeo de btn-verde */
background-color: var(--color-exito);
color: var(--color-blanco);
}
.btn-exito:hover {
background-color: #218838;
}
.btn-info { /* Mapeo de btn-azul */
background-color: var(--color-info);
color: var(--color-blanco);
}
.btn-info:hover {
background-color: #138496;
}
.btn-chico {
padding: 0.375rem 0.75rem;
font-size: 0.75rem;
}

/* ===================================
COMPONENTES: TABLA
=================================== */
.tabla-wrapper {
background: var(--color-blanco);
border: 1px solid var(--color-borde);
border-radius: var(--border-radius);
overflow: hidden;
box-shadow: var(--sombra);
}
.tabla {
width: 100%;
border-collapse: collapse;
}
.tabla thead {
background-color: var(--color-fondo);
}
.tabla th {
padding: 0.875rem 0.75rem;
text-align: left;
font-weight: 600;
font-size: 0.75rem;
text-transform: uppercase;
border-bottom: 2px solid var(--color-borde);
}
.tabla td {
padding: 0.875rem 0.75rem;
border-top: 1px solid var(--color-borde);
color: var(--color-texto);
font-size: 0.875rem;
}
.tabla tbody tr:hover {
background-color: rgba(0,0,0,.02);
}
.texto-centro { text-align: center; }
.texto-small { font-size: 0.75rem; }
.texto-secundario { color: var(--color-texto-claro); }

/* ===================================
COMPONENTES: BADGES Y ALERTAS
=================================== */
.badge {
display: inline-block;
padding: 0.25em 0.6em;
border-radius: 0.25rem;
font-size: 0.6875rem;
font-weight: 500;
}
.badge-info { /* Mapeo de badge-azul */
background-color: #d1ecf1;
color: #0c5460;
}
.badge-espaciado { margin-right: 0.3125rem; }
.texto-roles-vacio { color: var(--color-texto-claro);font-size: 0.75rem; }

.alerta {
padding: 0.875rem 1.125rem;
border-radius: var(--border-radius);
margin-bottom: 1.25rem;
font-size: 0.875rem;
border: 1px solid;
}
.alerta-exito { /* Mapeo de alerta-exito */
background-color: #d4edda;
color: #155724;
border-color: #c3e6cb;
}
.alerta-peligro { /* Mapeo de alerta-error */
background-color: #f8d7da;
color: #721c24;
border-color: #f5c6cb;
}
.texto-vacio { text-align: center;padding: 2.5rem;color: var(--color-texto-claro); }

/* ===================================
COMPONENTES: TOGGLE (SWITCH)
=================================== */
.toggle {
position: relative;
display: inline-block;
width: 42px;
height: 22px;
}
.toggle input { opacity: 0; width: 0; height: 0; }
.toggle-slider {
position: absolute;
cursor: pointer;
top: 0;
left: 0;
right: 0;
bottom: 0;
background-color: var(--color-secundario);
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
background-color: var(--color-blanco);
transition: .3s;
border-radius: 50%;
}
input:checked + .toggle-slider { background-color: var(--color-exito); }
input:checked + .toggle-slider:before { transform: translateX(20px); }

/* ===================================
COMPONENTES: ACCIONES
=================================== */
.acciones-grupo {
display: flex;
gap: 0.5rem;
flex-wrap: wrap;
}
</style>

<div class="contenedor">
    <h1 class="titulo-principal">Gestión de Empleados</h1>
    <p class="subtitulo">Administra empleados, roles y permisos del sistema</p>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alerta alerta-exito">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alerta alerta-peligro">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Filtros y Botón Nuevo -->
    <div class="tarjeta">
        <form method="GET" action="<?php echo BASE_URL; ?>view/admin/empleados.php" class="filtros-form">
            <div class="campo campo-chico">
                <label>Estado</label>
                <select name="estado">
                    <option value="">Todos</option>
                    <option value="1" <?php echo (isset($_GET['estado']) && $_GET['estado'] == '1') ? 'selected' : ''; ?>>Activos</option>
                    <option value="0" <?php echo (isset($_GET['estado']) && $_GET['estado'] == '0') ? 'selected' : ''; ?>>Inactivos</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primario">Filtrar</button>
            
            <?php if (isset($_GET['estado'])): ?>
                <a href="<?php echo BASE_URL; ?>view/admin/empleados.php" class="btn btn-secundario">Limpiar</a>
            <?php endif; ?>
            
            <div class="contenedor-izquierda">
                <a href="<?php echo BASE_URL; ?>view/admin/empleado_form.php" class="btn btn-exito">Nuevo Empleado</a>
            </div>
        </form>
    </div>

    <!-- Estadísticas -->
    <div class="grid-estadisticas">
        <div class="tarjeta-estadistica">
            <div class="estadistica-label">Total de Empleados</div>
            <div class="estadistica-numero"><?php echo count($datos['empleados']); ?></div>
        </div>
        <div class="tarjeta-estadistica resaltado-exito">
            <div class="estadistica-label">Empleados Activos</div>
            <div class="estadistica-numero">
                <?php 
                    $activos = array_filter($datos['empleados'], function($e) { return $e['Estado'] == 1; });
                    echo count($activos); 
                ?>
            </div>
        </div>
        <div>
        <div class="tarjeta-estadistica resaltado-info">
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
                    <th class="texto-centro">Estado</th>
                    <th class="texto-centro">Acciones</th>
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
                                <div class="texto-small texto-secundario">
                                    ID: <?php echo $empleado['IdEmpleado']; ?>
                                </div>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($empleado['Usuario']); ?></strong>
                            </td>
                            <td class="texto-small">
                                <?php echo htmlspecialchars($empleado['Email'] ?? 'Sin email'); ?>
                            </td>
                            <td>
                                <label class="toggle">
                                    <input type="checkbox" class="estado-toggle"
                                           <?php echo $empleado['Estado'] ? 'checked' : ''; ?>
                                           data-id="<?php echo $empleado['IdEmpleado']; ?>">
                                    <span class="toggle-slider"></span>
                                </label>
                            </td>
                            <td class="texto-centro">
                                <div class="acciones-grupo">
                                    <a href="<?php echo BASE_URL; ?>view/admin/empleado_form.php?id=<?php echo $empleado['IdEmpleado']; ?>" 
                                       class="btn btn-secundario btn-chico">
                                        Editar
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>view/admin/empleado_roles.php?id=<?php echo $empleado['IdEmpleado']; ?>" 
                                       class="btn btn-info btn-chico">
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
        
        fetch('<?php echo BASE_URL; ?>controllers/EmpleadoController.php?action=cambiarEstado', {
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
            alert('error al cambiar el estado');
            this.checked = !this.checked;
        });
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>