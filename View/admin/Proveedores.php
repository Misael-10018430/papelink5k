<?php
/**
 * Vista: Gestión de Proveedores
 * Listado de proveedores con filtros y acciones
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/Auth.php';
Auth::requiereAlgunaFuncionalidad(['PROVEEDORES_VER', 'PROVEEDORES_GESTIONAR']);

 $paginaActual = basename($_SERVER['PHP_SELF'], '.php');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/ProveedorController.php';

 $controller = new ProveedorController();
 $datos = $controller->listar();

 $titulo = "Gestión de Proveedores - Papelink";
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
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
        color: var(--color-texto);
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        border-bottom: 2px solid var(--color-borde);
    }
    .tabla td {
        padding: 0.875rem 0.75rem;
        border-bottom: 1px solid var(--color-borde);
        color: var(--color-texto);
        font-size: 0.875rem;
    }
    .tabla tbody tr:hover {
        background-color: rgba(0,0,0,.02);
    }
    .texto-centrado { text-align: center; }
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
    .badge-exito { background-color: #d4edda; color: #155724; } /* Mapeo de badge-verde */
    .badge-peligro { background-color: #f8d7da; color: #721c24; } /* Mapeo de badge-rojo */
    
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
    .alerta-error {
        background-color: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
    }
    .texto-vacio {
        text-align: center;
        padding: 2.5rem;
        color: var(--color-texto-claro);
    }

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
    }
</style>

<div class="contenedor">
    <h1 class="titulo-principal">Gestión de Proveedores</h1>
    <p class="subtitulo">Administra y consulta información de tus proveedores</p>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alerta alerta-exito">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alerta alerta-error">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Filtros y Botón Nuevo -->
    <div class="tarjeta">
        <form method="GET" action="proveedores.php" class="filtros-form">
            <div class="campo" style="max-width: 200px;">
                <label>Estado</label>
                <select name="estado">
                    <option value="">Todos</option>
                    <option value="1" <?php echo (isset($_GET['estado']) && $_GET['estado'] == '1') ? 'selected' : ''; ?>>Activos</option>
                    <option value="0" <?php echo (isset($_GET['estado']) && $_GET['estado'] == '0') ? 'selected' : ''; ?>>Inactivos</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primario">Filtrar</button>
            
            <?php if (isset($_GET['estado'])): ?>
                <a href="proveedores.php" class="btn btn-secundario">Limpiar</a>
            <?php endif; ?>
            
            <div style="margin-left: auto;">
                <a href="proveedor_form.php" class="btn btn-exito">Nuevo Proveedor</a>
            </div>
        </form>
    </div>

    <!-- Estadísticas -->
    <div class="grid-estadisticas">
        <div class="tarjeta-estadistica">
            <div class="estadistica-label">Total de Proveedores</div>
            <div class="estadistica-numero"><?php echo count($datos['proveedores']); ?></div>
        </div>
        <div class="tarjeta-estadistica resaltado-exito">
            <div class="estadistica-label">Proveedores Activos</div>
            <div class="estadistica-numero">
                <?php 
                    $activos = array_filter($datos['proveedores'], function($p) { return $p['Estado'] == 1; });
                    echo count($activos); 
                ?>
            </div>
        </div>
        <div class="tarjeta-estadistica resaltado-info">
            <div class="estadistica-label">Total Compras Realizadas</div>
            <div class="estadistica-numero">
                <?php 
                    $totalCompras = array_sum(array_column($datos['proveedores'], 'TotalCompras'));
                    echo $totalCompras; 
                ?>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="tabla-wrapper">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Proveedor</th>
                    <th>Contacto</th>
                    <th>Dirección</th>
                    <th class="texto-centrado">Compras</th>
                    <th class="texto-centrado">Estado</th>
                    <th class="texto-centrado">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($datos['proveedores'])): ?>
                    <tr>
                        <td colspan="6" class="texto-vacio">
                            No se encontraron proveedores
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($datos['proveedores'] as $proveedor): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 500;">
                                    <?php echo htmlspecialchars($proveedor['NombreProveedor']); ?>
                                </div>
                                <div class="texto-small texto-secundario">
                                    ID: <?php echo $proveedor['IdProveedor']; ?>
                                </div>
                            </td>
                            <td>
                                <div><?php echo htmlspecialchars($proveedor['Email'] ?? 'Sin email'); ?></div>
                                <div class="texto-small texto-secundario">
                                    <?php echo htmlspecialchars($proveedor['Telefono'] ?? 'Sin teléfono'); ?>
                                </div>
                            </td>
                            <td class="texto-small">
                                <?php echo htmlspecialchars($proveedor['Direccion'] ?? 'Sin dirección'); ?>
                            </td>
                            <td class="texto-centrado">
                                <strong><?php echo $proveedor['TotalCompras']; ?></strong>
                            </td>
                            <td class="texto-centrado">
                                <label class="toggle">
                                    <input type="checkbox" class="estado-toggle"
                                           <?php echo $proveedor['Estado'] ? 'checked' : ''; ?>
                                           data-id="<?php echo $proveedor['IdProveedor']; ?>">
                                    <span class="toggle-slider"></span>
                                </label>
                            </td>
                            <td class="texto-centrado">
                                <div class="acciones-grupo">
                                    <a href="proveedor_form.php?id=<?php echo $proveedor['IdProveedor']; ?>" 
                                       class="btn btn-secundario btn-chico">
                                        Editar
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
        const idProveedor = this.getAttribute('data-id');
        const nuevoEstado = this.checked ? 1 : 0;
        
        fetch('<?php echo BASE_URL; ?>controllers/ProveedorController.php?action=cambiarEstado', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id_proveedor=${idProveedor}&estado=${nuevoEstado}`
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