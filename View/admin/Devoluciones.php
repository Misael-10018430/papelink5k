<?php
/**
 * Vista: Gestión de Devoluciones (Admin)
 * Panel administrativo para gestionar devoluciones de clientes
 */

require_once __DIR__ . '/../../config/config.php';

$titulo = "Gestión de Devoluciones - Papelink Admin";
include __DIR__ . '/includes/header.php';
?>

<style>
    /* ============================================
       ESTADÍSTICAS
       ============================================ */
    .estadisticas-devoluciones {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }
    
    .tarjeta-estadistica {
        background: white;
        border-left: 4px solid #FF6347;
        padding: 20px;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    
    .estadistica-valor {
        font-size: 32px;
        font-weight: 700;
        color: #2C3E50;
        margin-bottom: 5px;
    }
    
    .estadistica-label {
        font-size: 12px;
        color: #7f8c8d;
        text-transform: uppercase;
    }
    
    /* ============================================
       FILTROS Y BÚSQUEDA
       ============================================ */
    .panel-filtros {
        background: white;
        padding: 20px;
        border-radius: 6px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    
    .filtros-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        align-items: end;
    }
    
    .campo-filtro label {
        display: block;
        margin-bottom: 6px;
        color: #2C3E50;
        font-size: 13px;
        font-weight: 600;
    }
    
    .campo-filtro input,
    .campo-filtro select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }
    
    .busqueda-rapida {
        margin-bottom: 15px;
    }
    
    .busqueda-rapida input {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 4px;
        font-size: 14px;
    }
    
    .busqueda-rapida input:focus {
        outline: none;
        border-color: #FF6347;
    }
    
    .filtros-estado {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    
    .btn-filtro-estado {
        padding: 8px 16px;
        border: 2px solid #e0e0e0;
        background: white;
        color: #7f8c8d;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .btn-filtro-estado:hover {
        border-color: #FF6347;
        color: #FF6347;
    }
    
    .btn-filtro-estado.activo {
        background: #FF6347;
        color: white;
        border-color: #FF6347;
    }
    
    /* ============================================
       TABLA DE DEVOLUCIONES
       ============================================ */
    .tabla-devoluciones {
        background: white;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }
    
    .tabla-devoluciones table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .tabla-devoluciones thead {
        background-color: #34495e;
        color: white;
    }
    
    .tabla-devoluciones th {
        padding: 14px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
    }
    
    .tabla-devoluciones td {
        padding: 14px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 13px;
    }
    
    .tabla-devoluciones tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .info-cliente {
        display: flex;
        flex-direction: column;
    }
    
    .nombre-cliente {
        font-weight: 600;
        color: #2C3E50;
        margin-bottom: 3px;
    }
    
    .email-cliente {
        font-size: 12px;
        color: #7f8c8d;
    }
    
    /* ============================================
       BADGES
       ============================================ */
    .badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .badge-amarillo {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    
    .badge-azul {
        background-color: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }
    
    .badge-verde {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .badge-rojo {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    /* ============================================
       BOTONES DE ACCIÓN
       ============================================ */
    .acciones {
        display: flex;
        gap: 5px;
    }
    
    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
        border-radius: 3px;
        border: none;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }
    
    .btn-naranja {
        background-color: #FF6347;
        color: white;
    }
    
    .btn-naranja:hover {
        background-color: #e5533d;
    }
    
    .btn-verde {
        background-color: #27ae60;
        color: white;
    }
    
    .btn-verde:hover {
        background-color: #229954;
    }
    
    .btn-rojo {
        background-color: #e74c3c;
        color: white;
    }
    
    .btn-rojo:hover {
        background-color: #c0392b;
    }
    
    .btn-azul {
        background-color: #3498db;
        color: white;
    }
    
    .btn-azul:hover {
        background-color: #2980b9;
    }
    
    .btn-blanco {
        background-color: white;
        color: #333;
        border: 1px solid #ddd;
    }
    
    .btn-blanco:hover {
        background-color: #f8f9fa;
    }
    
    /* ============================================
       MODAL
       ============================================ */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        z-index: 9999;
        overflow-y: auto;
    }
    
    .modal.activo {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    
    .modal-contenido {
        background: white;
        border-radius: 8px;
        width: 100%;
        max-width: 900px;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
    }
    
    .modal-header {
        background: #34495e;
        color: white;
        padding: 20px 25px;
        border-radius: 8px 8px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-header h2 {
        margin: 0;
        font-size: 22px;
        font-weight: 600;
    }
    
    .btn-cerrar-modal {
        background: transparent;
        border: none;
        color: white;
        font-size: 28px;
        cursor: pointer;
        line-height: 1;
        padding: 0;
    }
    
    .modal-body {
        padding: 25px;
    }
    
    .modal-footer {
        padding: 20px 25px;
        border-top: 1px solid #e0e0e0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    
    /* ============================================
       LOADING Y SIN DATOS
       ============================================ */
    .loading {
        text-align: center;
        padding: 40px;
        color: #FF6347;
        font-size: 16px;
    }
    
    .sin-datos {
        text-align: center;
        padding: 60px 20px;
        color: #7f8c8d;
        background: #f8f9fa;
        border-radius: 6px;
    }
    
    /* ============================================
       RESPONSIVE
       ============================================ */
    @media (max-width: 768px) {
        .filtros-row {
            grid-template-columns: 1fr;
        }
        
        .estadisticas-devoluciones {
            grid-template-columns: 1fr;
        }
        
        .tabla-devoluciones {
            overflow-x: auto;
        }
        
        .acciones {
            flex-direction: column;
        }
        
        .btn-sm {
            width: 100%;
        }
    }
</style>

<div class="contenedor-principal">
    <h1 class="titulo-pagina">Gestión de Devoluciones</h1>

    <!-- Estadísticas -->
    <div id="estadisticas-devoluciones" class="estadisticas-devoluciones"></div>

    <!-- Panel de Filtros -->
    <div class="panel-filtros">
        <!-- Búsqueda Rápida -->
        <div class="busqueda-rapida">
            <input 
                type="text" 
                id="busqueda-input" 
                placeholder="🔍 Buscar por cliente, email, pedido o ID de devolución..."
                onkeyup="buscarDevoluciones(this.value)">
        </div>

        <!-- Filtros por Estado -->
        <div class="filtros-estado">
            <button class="btn-filtro-estado activo" data-estado="TODAS" onclick="cambiarFiltroEstado('TODAS', this)">
                Todas
            </button>
            <button class="btn-filtro-estado" data-estado="SOLICITADA" onclick="cambiarFiltroEstado('SOLICITADA', this)">
                Solicitadas
            </button>
            <button class="btn-filtro-estado" data-estado="APROBADA" onclick="cambiarFiltroEstado('APROBADA', this)">
                Aprobadas
            </button>
            <button class="btn-filtro-estado" data-estado="COMPLETADA" onclick="cambiarFiltroEstado('COMPLETADA', this)">
                Completadas
            </button>
            <button class="btn-filtro-estado" data-estado="RECHAZADA" onclick="cambiarFiltroEstado('RECHAZADA', this)">
                Rechazadas
            </button>
        </div>

        <!-- Filtros Avanzados -->
        <div class="filtros-row">
            <div class="campo-filtro">
                <label>Fecha Inicio</label>
                <input type="date" id="fecha-inicio">
            </div>
            <div class="campo-filtro">
                <label>Fecha Fin</label>
                <input type="date" id="fecha-fin">
            </div>
            <div>
                <button class="btn btn-naranja" onclick="aplicarFiltros()">
                    Aplicar Filtros
                </button>
            </div>
        </div>
    </div>

    <!-- Loading -->
    <div id="loading" class="loading" style="display: none;">
        Cargando devoluciones...
    </div>

    <!-- Tabla de Devoluciones -->
    <div id="tabla-devoluciones" class="tabla-devoluciones" style="display: none;"></div>

    <!-- Sin Datos -->
    <div id="sin-datos" class="sin-datos" style="display: none;">
        <h3>No hay devoluciones</h3>
        <p>No se encontraron devoluciones con los filtros seleccionados.</p>
    </div>
</div>

<!-- CONTINUARÁ EN PARTE 2 CON MODALES Y JAVASCRIPT -->

<!-- MODAL: DETALLE DE DEVOLUCIÓN -->
<div id="modal-detalle" class="modal">
    <div class="modal-contenido">
        <div class="modal-header">
            <h2>Detalle de Devolución</h2>
            <button class="btn-cerrar-modal" onclick="cerrarModalDetalle()">&times;</button>
        </div>
        <div class="modal-body" id="contenido-detalle"></div>
        <div class="modal-footer" id="acciones-detalle"></div>
    </div>
</div>

<!-- MODAL: CONFIRMACIÓN DE ACCIÓN -->
<div id="modal-confirmacion" class="modal">
    <div class="modal-contenido" style="max-width: 500px;">
        <div class="modal-header">
            <h2 id="titulo-confirmacion">Confirmar Acción</h2>
            <button class="btn-cerrar-modal" onclick="cerrarModalConfirmacion()">&times;</button>
        </div>
        <div class="modal-body">
            <p id="mensaje-confirmacion" style="font-size: 15px; color: #2C3E50; line-height: 1.6;"></p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-blanco" onclick="cerrarModalConfirmacion()">Cancelar</button>
            <button id="btn-confirmar-accion" class="btn btn-naranja">Confirmar</button>
        </div>
    </div>
</div>

<script>
// ============================================
// VARIABLES GLOBALES
// ============================================
let devolucionesData = [];
let estadoFiltroActual = 'TODAS';
let devolucionActual = null;

// ============================================
// CARGAR AL INICIO
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    cargarEstadisticas();
    cargarDevoluciones();
    
    // Establecer fecha por defecto (último mes)
    const hoy = new Date();
    const haceUnMes = new Date();
    haceUnMes.setMonth(haceUnMes.getMonth() - 1);
    
    document.getElementById('fecha-fin').valueAsDate = hoy;
    document.getElementById('fecha-inicio').valueAsDate = haceUnMes;
});

// ============================================
// CARGAR ESTADÍSTICAS
// ============================================
function cargarEstadisticas() {
    fetch('../../controllers/DevolucionAdminController.php?action=obtenerEstadisticas')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const stats = data.estadisticas;
                document.getElementById('estadisticas-devoluciones').innerHTML = `
                    <div class="tarjeta-estadistica">
                        <div class="estadistica-valor">${stats.TotalDevoluciones}</div>
                        <div class="estadistica-label">Total Devoluciones</div>
                    </div>
                    <div class="tarjeta-estadistica">
                        <div class="estadistica-valor">${stats.Solicitadas}</div>
                        <div class="estadistica-label">Solicitadas</div>
                    </div>
                    <div class="tarjeta-estadistica">
                        <div class="estadistica-valor">${stats.Aprobadas}</div>
                        <div class="estadistica-label">Aprobadas</div>
                    </div>
                    <div class="tarjeta-estadistica">
                        <div class="estadistica-valor">${stats.Completadas}</div>
                        <div class="estadistica-label">Completadas</div>
                    </div>
                    <div class="tarjeta-estadistica">
                        <div class="estadistica-valor">${stats.Rechazadas}</div>
                        <div class="estadistica-label">Rechazadas</div>
                    </div>
                `;
            }
        })
        .catch(error => console.error('Error:', error));
}

// ============================================
// CARGAR DEVOLUCIONES
// ============================================
function cargarDevoluciones() {
    const loading = document.getElementById('loading');
    const tabla = document.getElementById('tabla-devoluciones');
    const sinDatos = document.getElementById('sin-datos');
    
    loading.style.display = 'block';
    tabla.style.display = 'none';
    sinDatos.style.display = 'none';
    
    const fechaInicio = document.getElementById('fecha-inicio').value;
    const fechaFin = document.getElementById('fecha-fin').value;
    
    let url = `../../controllers/DevolucionAdminController.php?action=listarDevoluciones&estado=${estadoFiltroActual}`;
    if (fechaInicio) url += `&fecha_inicio=${fechaInicio}`;
    if (fechaFin) url += `&fecha_fin=${fechaFin}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';
            
            if (data.success && data.devoluciones.length > 0) {
                devolucionesData = data.devoluciones;
                mostrarDevoluciones(devolucionesData);
            } else {
                sinDatos.style.display = 'block';
            }
        })
        .catch(error => {
            loading.style.display = 'none';
            console.error('Error:', error);
            alert('Error al cargar las devoluciones');
        });
}

// ============================================
// MOSTRAR DEVOLUCIONES EN TABLA
// ============================================
function mostrarDevoluciones(devoluciones) {
    const tabla = document.getElementById('tabla-devoluciones');
    
    let html = `
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Pedido</th>
                    <th>Fecha</th>
                    <th>Productos</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    devoluciones.forEach(dev => {
        html += `
            <tr>
                <td><strong>#${dev.IdDevolucion}</strong></td>
                <td>
                    <div class="info-cliente">
                        <span class="nombre-cliente">${dev.NombreCliente}</span>
                        <span class="email-cliente">${dev.Email}</span>
                    </div>
                </td>
                <td>#${dev.NumeroPedido}</td>
                <td>${formatearFecha(dev.FechaSolicitud)}</td>
                <td>${dev.TotalProductos}</td>
                <td>${formatearMoneda(dev.MontoTotal)}</td>
                <td><span class="badge ${getBadgeClass(dev.EstadoDevolucion)}">${dev.EstadoDevolucion}</span></td>
                <td>
                    <div class="acciones">
                        <button class="btn-sm btn-naranja" onclick="verDetalle(${dev.IdDevolucion})">Ver</button>
                        ${getBotonAccion(dev)}
                    </div>
                </td>
            </tr>
        `;
    });
    
    html += '</tbody></table>';
    tabla.innerHTML = html;
    tabla.style.display = 'block';
}

// ============================================
// OBTENER BOTÓN DE ACCIÓN SEGÚN ESTADO
// ============================================
function getBotonAccion(devolucion) {
    switch(devolucion.EstadoDevolucion) {
        case 'SOLICITADA':
            return `
                <button class="btn-sm btn-verde" onclick="confirmarAccion(${devolucion.IdDevolucion}, 'aprobar')">Aprobar</button>
                <button class="btn-sm btn-rojo" onclick="confirmarAccion(${devolucion.IdDevolucion}, 'rechazar')">Rechazar</button>
            `;
        case 'APROBADA':
            return `<button class="btn-sm btn-azul" onclick="confirmarAccion(${devolucion.IdDevolucion}, 'completar')">Completar</button>`;
        case 'COMPLETADA':
            return `<button class="btn-sm btn-verde" onclick="confirmarAccion(${devolucion.IdDevolucion}, 'reintegrar')">Reintegrar</button>`;
        default:
            return '';
    }
}

// ============================================
// CAMBIAR FILTRO DE ESTADO
// ============================================
function cambiarFiltroEstado(estado, btn) {
    document.querySelectorAll('.btn-filtro-estado').forEach(b => b.classList.remove('activo'));
    btn.classList.add('activo');
    estadoFiltroActual = estado;
    cargarDevoluciones();
}

// ============================================
// APLICAR FILTROS
// ============================================
function aplicarFiltros() {
    cargarDevoluciones();
}

// ============================================
// BUSCAR DEVOLUCIONES
// ============================================
let timeoutBusqueda;
function buscarDevoluciones(termino) {
    clearTimeout(timeoutBusqueda);
    
    if (termino.length < 3) {
        cargarDevoluciones();
        return;
    }
    
    timeoutBusqueda = setTimeout(() => {
        fetch(`../../controllers/DevolucionAdminController.php?action=buscarDevoluciones&termino=${encodeURIComponent(termino)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.resultados.length > 0) {
                    devolucionesData = data.resultados;
                    mostrarDevoluciones(devolucionesData);
                    document.getElementById('sin-datos').style.display = 'none';
                    document.getElementById('tabla-devoluciones').style.display = 'block';
                } else {
                    document.getElementById('tabla-devoluciones').style.display = 'none';
                    document.getElementById('sin-datos').style.display = 'block';
                }
            })
            .catch(error => console.error('Error:', error));
    }, 300);
}

// ============================================
// VER DETALLE
// ============================================
function verDetalle(idDevolucion) {
    document.getElementById('modal-detalle').classList.add('activo');
    document.getElementById('contenido-detalle').innerHTML = '<div class="loading">Cargando...</div>';
    
    fetch(`../../controllers/DevolucionAdminController.php?action=obtenerDetalle&id=${idDevolucion}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                devolucionActual = data.detalle.informacion;
                mostrarDetalleDevolucion(data.detalle, data.cliente);
            } else {
                document.getElementById('contenido-detalle').innerHTML = '<p>Error al cargar el detalle</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('contenido-detalle').innerHTML = '<p>Error al cargar el detalle</p>';
        });
}

// ============================================
// MOSTRAR DETALLE COMPLETO
// ============================================
function mostrarDetalleDevolucion(detalle, cliente) {
    const info = detalle.informacion;
    const productos = detalle.productos;
    
    let totalDevolucion = 0;
    let htmlProductos = '';
    
    productos.forEach(producto => {
        const subtotal = producto.Cantidad * producto.PrecioUnitario;
        totalDevolucion += subtotal;
        
        htmlProductos += `
            <tr>
                <td><strong>${producto.NombreProducto}</strong></td>
                <td>${producto.Cantidad}</td>
                <td>${formatearMoneda(producto.PrecioUnitario)}</td>
                <td>${formatearMoneda(subtotal)}</td>
            </tr>
        `;
    });
    
    const html = `
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <h4 style="color: #2C3E50; margin-bottom: 10px;">Información de la Devolución</h4>
                <p><strong>ID:</strong> #${info.IdDevolucion}</p>
                <p><strong>Pedido:</strong> #${info.NumeroPedido}</p>
                <p><strong>Fecha Solicitud:</strong> ${formatearFecha(info.FechaSolicitud)}</p>
                <p><strong>Estado:</strong> <span class="badge ${getBadgeClass(info.EstadoDevolucion)}">${info.EstadoDevolucion}</span></p>
            </div>
            <div>
                <h4 style="color: #2C3E50; margin-bottom: 10px;">Información del Cliente</h4>
                <p><strong>Nombre:</strong> ${cliente.NombreCliente}</p>
                <p><strong>Email:</strong> ${cliente.Email}</p>
                <p><strong>Teléfono:</strong> ${cliente.Telefono || 'N/A'}</p>
            </div>
        </div>
        
        <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
            <strong style="color: #2C3E50;">Motivo del Cliente:</strong>
            <p style="margin: 5px 0 0 0; color: #7f8c8d;">${info.Motivo}</p>
        </div>
        
        <h4 style="color: #2C3E50; margin-bottom: 15px;">Productos a Devolver</h4>
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8f9fa;">
                <tr>
                    <th style="padding: 10px; text-align: left;">Producto</th>
                    <th style="padding: 10px; text-align: left;">Cantidad</th>
                    <th style="padding: 10px; text-align: left;">Precio Unit.</th>
                    <th style="padding: 10px; text-align: left;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                ${htmlProductos}
            </tbody>
            <tfoot style="background: #f8f9fa; font-weight: 600;">
                <tr>
                    <td colspan="3" style="padding: 10px; text-align: right;">TOTAL A REEMBOLSAR:</td>
                    <td style="padding: 10px;">${formatearMoneda(totalDevolucion)}</td>
                </tr>
            </tfoot>
        </table>
    `;
    
    document.getElementById('contenido-detalle').innerHTML = html;
    
    // Mostrar botones de acción según el estado
    mostrarBotonesAccion(info.IdDevolucion, info.EstadoDevolucion);
}

// ============================================
// MOSTRAR BOTONES DE ACCIÓN EN MODAL
// ============================================
function mostrarBotonesAccion(idDevolucion, estado) {
    const footer = document.getElementById('acciones-detalle');
    
    let botones = '<button class="btn btn-blanco" onclick="cerrarModalDetalle()">Cerrar</button>';
    
    switch(estado) {
        case 'SOLICITADA':
            botones += `
                <button class="btn btn-verde" onclick="confirmarAccion(${idDevolucion}, 'aprobar')">Aprobar</button>
                <button class="btn btn-rojo" onclick="confirmarAccion(${idDevolucion}, 'rechazar')">Rechazar</button>
            `;
            break;
        case 'APROBADA':
            botones += `<button class="btn btn-azul" onclick="confirmarAccion(${idDevolucion}, 'completar')">Completar</button>`;
            break;
        case 'COMPLETADA':
            botones += `<button class="btn btn-verde" onclick="confirmarAccion(${idDevolucion}, 'reintegrar')">Reintegrar a Disponible</button>`;
            break;
    }
    
    footer.innerHTML = botones;
}

// ============================================
// CONFIRMAR ACCIÓN
// ============================================
function confirmarAccion(idDevolucion, accion) {
    const modal = document.getElementById('modal-confirmacion');
    const titulo = document.getElementById('titulo-confirmacion');
    const mensaje = document.getElementById('mensaje-confirmacion');
    const btnConfirmar = document.getElementById('btn-confirmar-accion');
    
    let textoTitulo = '';
    let textoMensaje = '';
    let funcionConfirmar = null;
    
    switch(accion) {
        case 'aprobar':
            textoTitulo = 'Aprobar Devolución';
            textoMensaje = '¿Estás seguro de aprobar esta devolución? Los productos se marcarán como "En Revisión" cuando se complete.';
            funcionConfirmar = () => aprobarDevolucion(idDevolucion);
            break;
        case 'rechazar':
            textoTitulo = 'Rechazar Devolución';
            textoMensaje = '¿Estás seguro de rechazar esta devolución? Esta acción no reintegrará los productos al inventario.';
            funcionConfirmar = () => rechazarDevolucion(idDevolucion);
            break;
        case 'completar':
            textoTitulo = 'Completar Devolución';
            textoMensaje = '¿Estás seguro de completar esta devolución? Los productos se moverán automáticamente a "En Revisión".';
            funcionConfirmar = () => completarDevolucion(idDevolucion);
            break;
        case 'reintegrar':
            textoTitulo = 'Reintegrar Productos';
            textoMensaje = '¿Estás seguro de reintegrar estos productos? Se moverán de "En Revisión" a "Disponible" para la venta.';
            funcionConfirmar = () => reintegrarProductos(idDevolucion);
            break;
    }
    
    titulo.textContent = textoTitulo;
    mensaje.textContent = textoMensaje;
    btnConfirmar.onclick = funcionConfirmar;
    
    modal.classList.add('activo');
}

// ============================================
// ACCIONES: APROBAR, RECHAZAR, COMPLETAR, REINTEGRAR
// ============================================
function aprobarDevolucion(idDevolucion) {
    ejecutarAccion('aprobarDevolucion', idDevolucion, 'Devolución aprobada correctamente');
}

function rechazarDevolucion(idDevolucion) {
    ejecutarAccion('rechazarDevolucion', idDevolucion, 'Devolución rechazada correctamente');
}

function completarDevolucion(idDevolucion) {
    ejecutarAccion('completarDevolucion', idDevolucion, 'Devolución completada. Productos movidos a "En Revisión"');
}

function reintegrarProductos(idDevolucion) {
    ejecutarAccion('reintegrarProductos', idDevolucion, 'Productos reintegrados a inventario disponible');
}

function ejecutarAccion(accion, idDevolucion, mensajeExito) {
    const formData = new FormData();
    formData.append('id_devolucion', idDevolucion);
    
    fetch(`../../controllers/DevolucionAdminController.php?action=${accion}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(mensajeExito);
            cerrarModalConfirmacion();
            cerrarModalDetalle();
            cargarDevoluciones();
            cargarEstadisticas();
        } else {
            alert('Error: ' + data.mensaje);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la acción');
    });
}

// ============================================
// CERRAR MODALES
// ============================================
function cerrarModalDetalle() {
    document.getElementById('modal-detalle').classList.remove('activo');
}

function cerrarModalConfirmacion() {
    document.getElementById('modal-confirmacion').classList.remove('activo');
}

// ============================================
// FUNCIONES AUXILIARES
// ============================================
function formatearMoneda(valor) {
    return '$' + parseFloat(valor).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

function formatearFecha(fecha) {
    const date = new Date(fecha);
    return date.toLocaleDateString('es-MX', { year: 'numeric', month: '2-digit', day: '2-digit' });
}

function getBadgeClass(estado) {
    const badges = {
        'SOLICITADA': 'badge-amarillo',
        'APROBADA': 'badge-azul',
        'COMPLETADA': 'badge-verde',
        'RECHAZADA': 'badge-rojo'
    };
    return badges[estado] || 'badge-azul';
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>