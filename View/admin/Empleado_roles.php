
<?php
/**
 * Vista: Gestión de Roles del Empleado
 * Asignar y remover roles
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/Auth.php';
Auth::requiereAdministrador();
require_once __DIR__ . '/../../controllers/EmpleadoController.php';


$paginaActual = basename($_SERVER['PHP_SELF'], '.php');
$idEmpleado = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$controller = new EmpleadoController();
$datos = $controller->gestionarRoles($idEmpleado);

 $titulo = "Gestión de Roles - Papelink";
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
        max-width: 1200px;
        margin: 0 auto;
    }
    .grid-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    @media (max-width: 992px) {
        .grid-layout { grid-template-columns: 1fr; }
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
    .tarjeta-titulo {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--color-texto);
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--color-borde);
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
        padding: 1.5rem;
        box-shadow: var(--sombra);
    }
    .rol-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        border: 1px solid var(--color-borde);
        border-radius: var(--border-radius);
        margin-bottom: 0.75rem;
        transition: background-color 0.2s;
    }
    .rol-item:hover {
        background-color: var(--color-fondo);
    }
    .rol-info h4 {
        color: var(--color-texto);
        font-size: 0.9375rem;
        font-weight: 600;
        margin: 0 0 0.3125rem 0;
    }
    .rol-info p {
        color: var(--color-texto-claro);
        font-size: 0.8125rem;
        margin: 0;
    }
    .lista-roles-disponibles {
        border-top: 1px solid var(--color-borde);
        padding-top: 1.25rem;
        margin-top: 1.25rem;
    }
    .lista-roles-disponibles h3 {
        color: var(--color-texto);
        font-size: 0.9375rem;
        font-weight: 600;
        margin-bottom: 0.9375rem;
    }
    .rol-disponible-item {
        padding: 0.625rem;
        border-left: 3px solid var(--color-info);
        background-color: var(--color-fondo);
        margin-bottom: 0.625rem;
        border-radius: var(--border-radius);
    }
    .rol-disponible-item div:first-child {
        font-weight: 500;
        color: var(--color-texto);
        margin-bottom: 0.1875rem;
    }
    .rol-disponible-item div:last-child {
        font-size: 0.75rem;
        color: var(--color-texto-claro);
    }

    /* ===================================
       COMPONENTES: FORMULARIOS
       =================================== */
    .form-asignar {
        display: flex;
        gap: 0.625rem;
        margin-bottom: 1.25rem;
    }
    .form-asignar select {
        flex: 1;
        padding: 0.625rem 0.75rem;
        border: 1px solid var(--color-borde);
        border-radius: var(--border-radius);
        font-size: 0.875rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-asignar select:focus {
        outline: none;
        border-color: var(--color-primario);
        box-shadow: 0 0 0 2px rgba(73, 80, 87, 0.25);
    }

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
    .btn-peligro { /* Mapeo de btn-rojo */
        background-color: var(--color-peligro);
        color: var(--color-blanco);
        padding: 0.375rem 0.875rem;
        font-size: 0.75rem;
    }
    .btn-peligro:hover {
        background-color: #c82333;
    }
    .btn-volver {
        margin-bottom: 1.25rem;
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
    .alerta-exito {
        background-color: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
    }
    .alerta-peligro { /* Mapeo de alerta-error */
        background-color: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
    }
    .texto-vacio {
        text-align: center;
        padding: 1.875rem;
        color: var(--color-texto-claro);
        background-color: var(--color-fondo);
        border-radius: var(--border-radius);
    }
</style>

<div class="contenedor">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="dashboard.php">Dashboard</a>
        <span class="breadcrumb-separador">/</span>
        <a href="empleados.php">Empleados</a>
        <span class="breadcrumb-separador">/</span>
        <span>Gestión de Roles</span>
    </div>

    <h1 class="titulo-principal">
        Gestión de Roles: <?php echo htmlspecialchars($datos['empleado']['NombreCompleto']); ?>
    </h1>
    <p class="subtitulo">
        Asigna o remueve roles para definir los permisos del empleado
    </p>

    <div>
        <a href="empleados.php" class="btn btn-secundario btn-volver">← Volver a Empleados</a>
    </div>

    <div id="mensaje-alerta"></div>

    <div class="grid-layout">
        <!-- Columna Izquierda: Roles Asignados -->
        <div>
            <div class="tarjeta">
                <h2 class="tarjeta-titulo">Roles Asignados</h2>
                
                <?php if (empty($datos['roles_asignados'])): ?>
                    <div class="texto-vacio">
                        Este empleado no tiene roles asignados
                    </div>
                <?php else: ?>
                    <?php foreach ($datos['roles_asignados'] as $rol): ?>
                        <div class="rol-item">
                            <div class="rol-info">
                                <h4><?php echo htmlspecialchars($rol['NombreRol']); ?></h4>
                                <p><?php echo htmlspecialchars($rol['Descripcion']); ?></p>
                            </div>
                            <button class="btn btn-peligro btn-remover" 
                                    data-id-rol="<?php echo $rol['IdRol']; ?>"
                                    data-nombre-rol="<?php echo htmlspecialchars($rol['NombreRol']); ?>">
                                Remover
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Columna Derecha: Asignar Nuevo Rol -->
        <div>
            <div class="tarjeta">
                <h2 class="tarjeta-titulo">Asignar Nuevo Rol</h2>
                
                <?php if (empty($datos['roles_disponibles'])): ?>
                    <div class="texto-vacio">
                        No hay más roles disponibles para asignar
                    </div>
                <?php else: ?>
                    <form id="formAsignarRol" class="form-asignar">
                        <input type="hidden" name="id_empleado" value="<?php echo $idEmpleado; ?>">
                        <select name="id_rol" required>
                            <option value="">Seleccionar rol...</option>
                            <?php foreach ($datos['roles_disponibles'] as $rol): ?>
                                <option value="<?php echo $rol['IdRol']; ?>">
                                    <?php echo htmlspecialchars($rol['NombreRol']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primario">Asignar</button>
                    </form>

                    <div class="lista-roles-disponibles">
                        <h3>Roles Disponibles</h3>
                        <?php foreach ($datos['roles_disponibles'] as $rol): ?>
                            <div class="rol-disponible-item">
                                <div><?php echo htmlspecialchars($rol['NombreRol']); ?></div>
                                <div><?php echo htmlspecialchars($rol['Descripcion']); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Asignar rol
document.getElementById('formAsignarRol')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const idRol = formData.get('id_rol');
    const selectElement = this.querySelector('select[name="id_rol"]');
    const nombreRol = selectElement.options[selectElement.selectedIndex].text;
    
    if (!idRol) {
        mostrarAlerta('Por favor selecciona un rol', 'error');
        return;
    }
    
    if (!confirm(`¿Deseas asignar el rol "${nombreRol}"?`)) {
        return;
    }
    
    fetch('<?php echo BASE_URL; ?>controllers/EmpleadoController.php?action=asignarRol', {
        method: 'POST',
        body: new URLSearchParams(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarAlerta(data.mensaje, 'exito');
            setTimeout(() => location.reload(), 1500);
        } else {
            mostrarAlerta(data.mensaje, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarAlerta('Error al asignar el rol', 'error');
    });
});

// Remover rol
document.querySelectorAll('.btn-remover').forEach(btn => {
    btn.addEventListener('click', function() {
        const idRol = this.getAttribute('data-id-rol');
        const nombreRol = this.getAttribute('data-nombre-rol');
        const idEmpleado = <?php echo $idEmpleado; ?>;
        
        if (!confirm(`¿Deseas remover el rol "${nombreRol}"?`)) {
            return;
        }
        
        fetch('<?php echo BASE_URL; ?>controllers/EmpleadoController.php?action=removerRol', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id_empleado=${idEmpleado}&id_rol=${idRol}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarAlerta(data.mensaje, 'exito');
                setTimeout(() => location.reload(), 1500);
            } else {
                mostrarAlerta(data.mensaje, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarAlerta('Error al remover el rol', 'error');
        });
    });
});

// Función para mostrar alertas
function mostrarAlerta(mensaje, tipo) {
    const alertaDiv = document.getElementById('mensaje-alerta');
    const claseAlerta = tipo === 'exito' ? 'alerta-exito' : 'alerta-peligro';
    
    alertaDiv.innerHTML = `
        <div class="alerta ${claseAlerta}">
            ${mensaje}
        </div>
    `;
    
    // Scroll hacia arriba
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
        alertaDiv.innerHTML = '';
    }, 5000);
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>