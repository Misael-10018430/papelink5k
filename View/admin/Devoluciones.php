<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/Auth.php';

// ✅ VERIFICAR PERMISOS PARA DEVOLUCIONES
Auth::requiereFuncionalidad('DEVOLUCIONES_VER');

 $paginaActual = basename($_SERVER['PHP_SELF'], '.php');

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/DevolucionAdminController.php';

// ✅ NO CREAR INSTANCIA AQUÍ - Se hará por AJAX

 $titulo = "Gestión de Devoluciones";
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
        --color-secundario: #6c757d;     /* Gris medio para botones secundarios */
        --color-exito: #28a745;          /* Verde estándar para éxito */
        --color-error: #dc3545;          /* Rojo estándar para error */
        --color-advertencia: #ffc107;    /* Amarillo estándar para advertencia */
        --color-info: #17a2b8;           /* Azul estándar para información */
        --color-texto: #212529;          /* Negro suave para texto */
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

    /* ===================================
       SISTEMA DE GRID
       =================================== */
    .grid {
        display: grid;
        gap: 1.5rem;
    }

    .grid-4 {
        grid-template-columns: repeat(4, 1fr);
    }
    .grid-5 {
        grid-template-columns: repeat(5, 1fr);
    }

    @media (max-width: 1200px) {
        .grid-5 { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 1024px) {
        .grid-4, .grid-5 { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 600px) {
        .grid-4, .grid-5 { grid-template-columns: 1fr; }
    }

    /* ===================================
       COMPONENTES: TARJETAS
       =================================== */
    .tarjeta, .tarjeta-metrica {
        background: var(--color-blanco);
        padding: 1.5rem;
        border-radius: var(--border-radius);
        border: 1px solid var(--color-borde);
    }

    .tarjeta-metrica {
        text-align: center;
    }

    .tarjeta-metrica h3 {
        font-size: 2rem;
        font-weight: 600;
        color: var(--color-primario);
        margin: 0 0 0.5rem 0;
    }

    .tarjeta-metrica p {
        color: var(--color-texto-claro);
        margin: 0;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--color-texto);
    }
    .form-group input[type="text"],
    .form-group input[type="date"],
    .form-group select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--color-borde);
        border-radius: var(--border-radius);
        font-size: 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-group input[type="text"]:focus,
    .form-group input[type="date"]:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--color-primario);
        box-shadow: 0 0 0 2px rgba(73, 80, 87, 0.25);
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
    .btn-blanco {
        background-color: var(--color-blanco);
        color: var(--color-texto);
        border-color: var(--color-borde);
    }
    .btn-blanco:hover {
        background-color: #e2e6ea;
        border-color: #dae0e5;
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
        border-bottom: 1px solid var(--color-borde);
    }
    .tabla td {
        padding: 1rem;
        vertical-align: middle;
        border-top: 1px solid var(--color-borde);
    }
    .tabla tbody tr:hover { background-color: rgba(0,0,0,.02); }

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
    .badge-advertencia { background-color: #fff3cd; color: #856404; } /* Mapeo de badge-amarillo */
</style>

<!-- CONTENIDO DE LA PÁGINA -->
<h1 class="titulo-pagina">Gestión de Devoluciones</h1>

<!-- MENSAJES -->
<div id="mensaje-container"></div>

<!-- ESTADÍSTICAS -->
<div class="grid grid-5" style="margin-bottom: 30px;">
    <div class="tarjeta-metrica">
        <h3 id="stat-total">0</h3>
        <p>TOTAL DEVOLUCIONES</p>
    </div>
    <div class="tarjeta-metrica">
        <h3 id="stat-solicitadas">0</h3>
        <p>SOLICITADAS</p>
    </div>
    <div class="tarjeta-metrica">
        <h3 id="stat-aprobadas">0</h3>
        <p>APROBADAS</p>
    </div>
    <div class="tarjeta-metrica">
        <h3 id="stat-completadas">0</h3>
        <p>COMPLETADAS</p>
    </div>
    <div class="tarjeta-metrica">
        <h3 id="stat-rechazadas">0</h3>
        <p>RECHAZADAS</p>
    </div>
</div>

<!-- FILTROS -->
<div class="filtros">
    <h3 style="margin: 0 0 15px 0; font-size: 1rem;">Filtros de Búsqueda</h3>
    <form id="form-filtros">
        <div class="grid grid-4">
            <div class="form-group" style="margin-bottom: 0;">
                <label>Buscar:</label>
                <input type="text" id="busqueda" name="busqueda" 
                       placeholder="Cliente, email, pedido o ID...">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label>Estado:</label>
                <select id="estado" name="estado">
                    <option value="TODAS">Todos los estados</option>
                    <option value="Solicitada">Solicitada</option>
                    <option value="Aprobada">Aprobada</option>
                    <option value="Completada">Completada</option>
                    <option value="Rechazada">Rechazada</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label>Fecha Inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label>Fecha Fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin">
            </div>
        </div>
        
        <div style="margin-top: 15px; display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primario">Aplicar Filtros</button>
            <button type="button" onclick="limpiarFiltros()" class="btn btn-blanco">Limpiar</button>
        </div>
    </form>
</div>

<!-- TABLA DE DEVOLUCIONES -->
<div id="tabla-container">
    <div style="text-align: center; padding: 60px;">
        <p style="color: var(--color-texto-claro);">Cargando devoluciones...</p>
    </div>
</div>

<script>
// Cargar estadísticas al inicio
cargarEstadisticas();
cargarDevoluciones();

// Enviar formulario de filtros
document.getElementById('form-filtros').addEventListener('submit', function(e) {
    e.preventDefault();
    cargarDevoluciones();
});

function cargarEstadisticas() {
    fetch('<?php echo BASE_URL; ?>controllers/DevolucionAdminController.php?action=obtenerEstadisticas')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const stats = data.estadisticas;
                document.getElementById('stat-total').textContent = stats.Total || 0;
                document.getElementById('stat-solicitadas').textContent = stats.Solicitada || 0;
                document.getElementById('stat-aprobadas').textContent = stats.Aprobada || 0;
                document.getElementById('stat-completadas').textContent = stats.Completada || 0;
                document.getElementById('stat-rechazadas').textContent = stats.Rechazada || 0;
            }
        })
        .catch(error => console.error('Error:', error));
}

function cargarDevoluciones() {
    const estado = document.getElementById('estado').value;
    const fechaInicio = document.getElementById('fecha_inicio').value;
    const fechaFin = document.getElementById('fecha_fin').value;
    
    let url = '<?php echo BASE_URL; ?>controllers/DevolucionAdminController.php?action=listarDevoluciones';
    url += '&estado=' + encodeURIComponent(estado);
    if (fechaInicio) url += '&fecha_inicio=' + fechaInicio;
    if (fechaFin) url += '&fecha_fin=' + fechaFin;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarDevoluciones(data.devoluciones);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('tabla-container').innerHTML = 
                '<div class="mensaje-error">Error al cargar devoluciones</div>';
        });
}

function mostrarDevoluciones(devoluciones) {
    if (devoluciones.length === 0) {
        document.getElementById('tabla-container').innerHTML = `
            <div class="tarjeta" style="text-align: center; padding: 60px;">
                <h2 style="color: var(--color-texto-claro); font-weight: normal;">No hay devoluciones para mostrar</h2>
                <p style="color: var(--color-texto-claro); margin-top: 10px;">Las devoluciones aparecerán aquí cuando los clientes las soliciten</p>
            </div>
        `;
        return;
    }
    
    let html = '<table class="tabla"><thead><tr>';
    html += '<th>ID</th><th>Fecha</th><th>Cliente</th><th>Pedido</th>';
    html += '<th>Motivo</th><th>Monto</th><th>Estado</th><th>Acciones</th>';
    html += '</tr></thead><tbody>';
    
    devoluciones.forEach(dev => {
        // Mapeo de clases de badges a las nuevas clases profesionales
        const badgeClass = {
            'Solicitada': 'badge-advertencia',
            'Aprobada': 'badge-info',
            'Completada': 'badge-exito',
            'Rechazada': 'badge-peligro'
        }[dev.EstadoDevolucion] || 'badge-secundario';
        
        html += `<tr>
            <td><strong>#${dev.IdDevolucion}</strong></td>
            <td>${formatearFecha(dev.FechaSolicitud)}</td>
            <td>${dev.NombreCliente}<br><small style="color:var(--color-texto-claro);">${dev.Email}</small></td>
            <td><strong>${dev.NumeroPedido}</strong></td>
            <td><small>${dev.MotivoDevolucion.substring(0, 50)}...</small></td>
            <td><strong style="color:var(--color-error);">$${formatearPrecio(dev.MontoDevolucion)}</strong></td>
            <td><span class="badge ${badgeClass}">${dev.EstadoDevolucion}</span></td>
            <td>
                <button onclick="verDetalle(${dev.IdDevolucion})" class="btn btn-blanco">
                    Ver
                </button>
            </td>
        </tr>`;
    });
    
    html += '</tbody></table>';
    document.getElementById('tabla-container').innerHTML = html;
}

function formatearFecha(fecha) {
    const d = new Date(fecha);
    return d.toLocaleDateString('es-MX') + ' ' + d.toLocaleTimeString('es-MX', {hour: '2-digit', minute:'2-digit'});
}

function formatearPrecio(precio) {
    return parseFloat(precio || 0).toFixed(2);
}

function limpiarFiltros() {
    document.getElementById('form-filtros').reset();
    cargarDevoluciones();
}

function verDetalle(id) {
    window.location.href = 'devolucion_detalle.php?id=' + id;
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>