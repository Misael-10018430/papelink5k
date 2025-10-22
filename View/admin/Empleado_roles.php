<?php
/**
 * Vista: Gestión de Roles del Empleado
 * Asignar y remover roles
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/EmpleadoController.php';

$idEmpleado = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$controller = new EmpleadoController();
$datos = $controller->gestionarRoles($idEmpleado);

$titulo = "Gestión de Roles - Papelink";
include __DIR__ . '/includes/header.php';
?>

<style>
    body {
        background-color: #f5f5f5;
        font-family: Arial, sans-serif;
    }
    
    .contenedor {
        padding: 25px;
        max-width: 1200px;
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
    
    .grid-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    @media (max-width: 992px) {
        .grid-layout {
            grid-template-columns: 1fr;
        }
    }
    
    .tarjeta {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        padding: 25px;
    }
    
    .tarjeta-titulo {
        color: #2C3E50;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .rol-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        margin-bottom: 12px;
        transition: all 0.2s;
    }
    
    .rol-item:hover {
        background-color: #f8f9fa;
    }
    
    .rol-info h4 {
        color: #2C3E50;
        font-size: 15px;
        font-weight: 600;
        margin: 0 0 5px 0;
    }
    
    .rol-info p {
        color: #7f8c8d;
        font-size: 13px;
        margin: 0;
    }
    
    .form-asignar {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .form-asignar select {
        flex: 1;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }
    
    .form-asignar select:focus {
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
    
    .btn-rojo {
        background-color: #dc3545;
        color: white;
        padding: 6px 14px;
        font-size: 12px;
    }
    
    .btn-rojo:hover {
        background-color: #c82333;
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
        padding: 30px;
        color: #7f8c8d;
        background-color: #f8f9fa;
        border-radius: 6px;
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

    <div style="margin-bottom: 20px;">
        <a href="empleados.php" class="btn btn-gris">← Volver a Empleados</a>
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
                            <button class="btn btn-rojo btn-remover" 
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
                        <button type="submit" class="btn btn-naranja">Asignar</button>
                    </form>

                    <div style="border-top: 1px solid #e0e0e0; padding-top: 20px; margin-top: 20px;">
                        <h3 style="color: #2C3E50; font-size: 15px; font-weight: 600; margin-bottom: 15px;">
                            Roles Disponibles
                        </h3>
                        <?php foreach ($datos['roles_disponibles'] as $rol): ?>
                            <div style="padding: 10px; border-left: 3px solid #17a2b8; background-color: #f8f9fa; margin-bottom: 10px; border-radius: 4px;">
                                <div style="font-weight: 500; color: #2C3E50; margin-bottom: 3px;">
                                    <?php echo htmlspecialchars($rol['NombreRol']); ?>
                                </div>
                                <div style="font-size: 12px; color: #7f8c8d;">
                                    <?php echo htmlspecialchars($rol['Descripcion']); ?>
                                </div>
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
    
    fetch('../../controllers/EmpleadoController.php?action=asignarRol', {
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
        
        fetch('../../controllers/EmpleadoController.php?action=removerRol', {
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
    const claseAlerta = tipo === 'exito' ? 'alerta-exito' : 'alerta-error';
    const icono = tipo === 'exito' ? '✓' : '❌';
    
    alertaDiv.innerHTML = `
        <div class="alerta ${claseAlerta}">
            ${icono} ${mensaje}
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