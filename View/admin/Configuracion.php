<?php
/**
 * Vista: Configuración del Sistema
 * Gestión de configuraciones generales, roles y estados
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/ConfiguracionController.php';

$controller = new ConfiguracionController();
$datos = $controller->index();

$titulo = "Configuración del Sistema - Papelink";
include __DIR__ . '/includes/header.php';

// Función helper para obtener valor de configuración
function getConfig($configuraciones, $clave, $default = '') {
    return isset($configuraciones[$clave]) ? $configuraciones[$clave]['Valor'] : $default;
}
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
    
    .tabs-container {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        overflow: hidden;
    }
    
    .tabs-header {
        display: flex;
        background-color: #f8f9fa;
        border-bottom: 2px solid #e0e0e0;
    }
    
    .tab-btn {
        flex: 1;
        padding: 15px 20px;
        background: none;
        border: none;
        color: #7f8c8d;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        border-bottom: 3px solid transparent;
    }
    
    .tab-btn:hover {
        background-color: #f0f0f0;
    }
    
    .tab-btn.active {
        color: #FF6347;
        border-bottom-color: #FF6347;
        background-color: white;
    }
    
    .tab-content {
        display: none;
        padding: 30px;
    }
    
    .tab-content.active {
        display: block;
    }
    
    .seccion {
        margin-bottom: 30px;
        padding-bottom: 30px;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .seccion:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    
    .seccion-titulo {
        color: #2C3E50;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }
    
    .form-campo {
        margin-bottom: 0;
    }
    
    .form-campo label {
        display: block;
        color: #2C3E50;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 6px;
    }
    
    .form-campo input,
    .form-campo select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }
    
    .form-campo input:focus,
    .form-campo select:focus {
        outline: none;
        border-color: #FF6347;
    }
    
    .form-campo input[readonly] {
        background-color: #f8f9fa;
        color: #999;
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
    
    .btn-naranja:disabled {
        background-color: #ccc;
        cursor: not-allowed;
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
    
    .rol-card {
        background: #f8f9fa;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .rol-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .rol-nombre {
        color: #2C3E50;
        font-size: 16px;
        font-weight: 600;
    }
    
    .rol-descripcion {
        color: #7f8c8d;
        font-size: 13px;
        margin-bottom: 15px;
    }
    
    .funcionalidades-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 10px;
    }
    
    .funcionalidad-item {
        background: white;
        padding: 10px 12px;
        border-radius: 4px;
        border-left: 3px solid #17a2b8;
        font-size: 13px;
        color: #2C3E50;
    }
    
    .estado-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        background: #f8f9fa;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        margin-bottom: 10px;
    }
    
    .estado-nombre {
        color: #2C3E50;
        font-weight: 500;
        font-size: 14px;
    }
    
    .badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .badge-activo {
        background-color: #d4edda;
        color: #155724;
    }
</style>

<div class="contenedor">
    <h1 class="titulo-principal">Configuración del Sistema</h1>
    <p class="subtitulo">Gestiona las configuraciones generales, roles y estados del sistema</p>

    <div id="mensaje-alerta"></div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alerta alerta-exito">
            ✓ <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="tabs-container">
        <!-- Tabs Header -->
        <div class="tabs-header">
            <button class="tab-btn active" data-tab="general">Configuración General</button>
            <button class="tab-btn" data-tab="roles">Roles y Permisos</button>
            <button class="tab-btn" data-tab="estados">Estados del Sistema</button>
        </div>

        <!-- TAB 1: CONFIGURACIÓN GENERAL -->
        <div id="tab-general" class="tab-content active">
            <form id="formConfiguracion">
                
                <!-- Información de la Empresa -->
                <div class="seccion">
                    <h2 class="seccion-titulo">Información de la Empresa</h2>
                    <div class="form-grid">
                        <div class="form-campo">
                            <label>Nombre de la Empresa</label>
                            <input type="text" name="NOMBRE_EMPRESA" 
                                   value="<?php echo htmlspecialchars(getConfig($datos['configuraciones'], 'NOMBRE_EMPRESA')); ?>">
                        </div>
                        <div class="form-campo">
                            <label>Teléfono</label>
                            <input type="text" name="TELEFONO_EMPRESA" 
                                   value="<?php echo htmlspecialchars(getConfig($datos['configuraciones'], 'TELEFONO_EMPRESA')); ?>">
                        </div>
                        <div class="form-campo">
                            <label>Email</label>
                            <input type="email" name="EMAIL_EMPRESA" 
                                   value="<?php echo htmlspecialchars(getConfig($datos['configuraciones'], 'EMAIL_EMPRESA')); ?>">
                        </div>
                        <div class="form-campo" style="grid-column: 1 / -1;">
                            <label>Dirección</label>
                            <input type="text" name="DIRECCION_EMPRESA" 
                                   value="<?php echo htmlspecialchars(getConfig($datos['configuraciones'], 'DIRECCION_EMPRESA')); ?>">
                        </div>
                    </div>
                </div>

                <!-- Configuración Financiera -->
                <div class="seccion">
                    <h2 class="seccion-titulo">Configuración Financiera</h2>
                    <div class="form-grid">
                        <div class="form-campo">
                            <label>Moneda</label>
                            <input type="text" name="MONEDA" 
                                   value="<?php echo htmlspecialchars(getConfig($datos['configuraciones'], 'MONEDA', 'MXN')); ?>">
                        </div>
                        <div class="form-campo">
                            <label>Tasa de IVA (%)</label>
                            <input type="number" step="0.01" name="TASA_IVA" 
                                   value="<?php echo htmlspecialchars(getConfig($datos['configuraciones'], 'TASA_IVA', '0.16')); ?>">
                        </div>
                    </div>
                </div>

                <!-- Configuración de Inventario -->
                <div class="seccion">
                    <h2 class="seccion-titulo">Configuración de Inventario</h2>
                    <div class="form-grid">
                        <div class="form-campo">
                            <label>Stock Mínimo para Alerta</label>
                            <input type="number" name="STOCK_MINIMO_ALERTA" 
                                   value="<?php echo htmlspecialchars(getConfig($datos['configuraciones'], 'STOCK_MINIMO_ALERTA', '5')); ?>">
                        </div>
                    </div>
                </div>

                <!-- Configuración de Ventas -->
                <div class="seccion">
                    <h2 class="seccion-titulo">Configuración de Ventas y Envíos</h2>
                    <div class="form-grid">
                        <div class="form-campo">
                            <label>Días Permitidos para Devolución</label>
                            <input type="number" name="DIAS_DEVOLUCION" 
                                   value="<?php echo htmlspecialchars(getConfig($datos['configuraciones'], 'DIAS_DEVOLUCION', '30')); ?>">
                        </div>
                        <div class="form-campo">
                            <label>Días Estimados para Entrega</label>
                            <input type="number" name="DIAS_ENTREGA_ESTIMADA" 
                                   value="<?php echo htmlspecialchars(getConfig($datos['configuraciones'], 'DIAS_ENTREGA_ESTIMADA', '5')); ?>">
                        </div>
                        <div class="form-campo">
                            <label>Costo de Envío Estándar</label>
                            <input type="number" step="0.01" name="COSTO_ENVIO_ESTANDAR" 
                                   value="<?php echo htmlspecialchars(getConfig($datos['configuraciones'], 'COSTO_ENVIO_ESTANDAR', '50.00')); ?>">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-naranja">Guardar Configuraciones</button>
            </form>
        </div>

        <!-- TAB 2: ROLES Y PERMISOS -->
        <div id="tab-roles" class="tab-content">
            <div class="seccion">
                <h2 class="seccion-titulo">Roles y Funcionalidades Asignadas</h2>
                
                <?php if (empty($datos['roles'])): ?>
                    <p style="text-align: center; color: #999; padding: 40px;">No hay roles configurados</p>
                <?php else: ?>
                    <?php foreach ($datos['roles'] as $rol): ?>
                        <div class="rol-card">
                            <div class="rol-header">
                                <div class="rol-nombre"><?php echo htmlspecialchars($rol['NombreRol']); ?></div>
                                <span class="badge badge-activo">
                                    <?php echo count($rol['funcionalidades']); ?> funcionalidades
                                </span>
                            </div>
                            <div class="rol-descripcion">
                                <?php echo htmlspecialchars($rol['Descripcion']); ?>
                            </div>
                            
                            <?php if (!empty($rol['funcionalidades'])): ?>
                                <div class="funcionalidades-grid">
                                    <?php foreach ($rol['funcionalidades'] as $func): ?>
                                        <div class="funcionalidad-item">
                                            <?php echo htmlspecialchars($func['Descripcion']); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p style="color: #999; font-size: 13px;">Sin funcionalidades asignadas</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- TAB 3: ESTADOS DEL SISTEMA -->
        <div id="tab-estados" class="tab-content">
            
            <!-- Estados de Pedidos -->
            <div class="seccion">
                <h2 class="seccion-titulo">Estados de Pedidos</h2>
                <?php if (empty($datos['estados_pedido'])): ?>
                    <p style="text-align: center; color: #999;">No hay estados configurados</p>
                <?php else: ?>
                    <?php foreach ($datos['estados_pedido'] as $estado): ?>
                        <div class="estado-item">
                            <span class="estado-nombre"><?php echo htmlspecialchars($estado['NombreEstado']); ?></span>
                            <span class="badge badge-activo">Activo</span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Estados de Envíos -->
            <div class="seccion">
                <h2 class="seccion-titulo">Estados de Envíos</h2>
                <?php if (empty($datos['estados_envio'])): ?>
                    <p style="text-align: center; color: #999;">No hay estados configurados</p>
                <?php else: ?>
                    <?php foreach ($datos['estados_envio'] as $estado): ?>
                        <div class="estado-item">
                            <span class="estado-nombre"><?php echo htmlspecialchars($estado['NombreEstado']); ?></span>
                            <span class="badge badge-activo">Activo</span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Sistema de Tabs
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tabId = this.getAttribute('data-tab');
        
        // Desactivar todos los tabs
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        // Activar tab seleccionado
        this.classList.add('active');
        document.getElementById('tab-' + tabId).classList.add('active');
    });
});

// Guardar configuraciones
document.getElementById('formConfiguracion').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Guardando...';
    
    fetch('../../controllers/ConfiguracionController.php?action=guardar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarAlerta(data.mensaje, 'exito');
        } else {
            mostrarAlerta(data.mensaje, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarAlerta('Error al guardar las configuraciones', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Guardar Configuraciones';
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
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    setTimeout(() => {
        alertaDiv.innerHTML = '';
    }, 5000);
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>