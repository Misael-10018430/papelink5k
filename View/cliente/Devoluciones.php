<?php
/**
 * Vista: Mis Devoluciones (Cliente)
 * Gestión de devoluciones de pedidos
 */

require_once __DIR__ . '/../../config/config.php';

// Verificar que el cliente esté autenticado
if (!isset($_SESSION['cliente_id'])) {
    header('Location: login.php');
    exit();
}

$titulo = "Mis Devoluciones - Papelink";
include __DIR__ . '/includes/header.php';
?>
<style>
    /* ============================================
       ESTILOS GENERALES
       ============================================ */
    body {
        background-color: #f0f0f0; /* Mantener gris claro para fondo */
        font-family: Arial, Helvetica, sans-serif;
        margin: 0;
        padding: 0;
        color: #2C3E50;
    }

    .contenedor-devoluciones {
        max-width: 1000px;  /* Reducido un poco para mejor enfoque visual */
        margin: 40px auto;  /* Espacio vertical amplio para mejor respiración */
        padding: 20px 30px;
        background: white;
        border-radius: 8px; /* Bordes redondeados para modernidad */
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .titulo-seccion {
        font-size: 30px;
        font-weight: 700;
        margin-bottom: 30px;
        color: #2C3E50;
        letter-spacing: 0.03em;
    }

    /* ============================================
       BOTÓN SOLICITAR NUEVA DEVOLUCIÓN
       ============================================ */
    .btn-solicitar-devolucion {
        display: block;
        width: 100%;
        background-color: #FF6347;
        color: white;
        font-weight: 700;
        font-size: 18px;
        padding: 15px 0;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        box-shadow: 0 3px 8px rgba(255,99,71,0.4);
        transition: background-color 0.3s ease;
        margin-bottom: 30px;
        text-align: center;
    }

    .btn-solicitar-devolucion:hover {
        background-color: #e5533d;
        box-shadow: 0 5px 12px rgba(229,83,61,0.6);
    }

    /* ============================================
       BARRA DE FILTROS
       ============================================ */
    .filtros-estado {
        display: flex;
        gap: 15px;
        margin-bottom: 40px;
        flex-wrap: wrap;
        justify-content: flex-start;
    }

    .btn-filtro {
        padding: 8px 22px;
        border: 2px solid #e0e0e0;
        background: white;
        color: #7f8c8d;
        border-radius: 30px; /* Píldoras más modernas */
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
        min-width: 100px;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.07);
    }

    .btn-filtro:hover {
        border-color: #FF6347;
        color: #FF6347;
        box-shadow: 0 3px 8px rgba(255,99,71,0.4);
    }

    .btn-filtro.activo {
        background: #FF6347;
        color: white;
        border-color: #FF6347;
        box-shadow: 0 4px 10px rgba(255,99,71,0.6);
    }

    /* ============================================
       ESTADO SIN DEVOLUCIONES (CAJA INFORMATIVA)
       ============================================ */
    .sin-datos {
        text-align: center;
        padding: 80px 40px;
        color: #7f8c8d;
        background: #f9fafb; /* Más claro que antes para mayor limpieza */
        border-radius: 8px;
        box-shadow: inset 0 0 10px #e0e0e0;
        font-size: 17px;
        font-weight: 600;
        line-height: 1.4;
        max-width: 600px;
        margin: 0 auto;
        user-select: none;
    }

    .sin-datos strong {
        color: #2C3E50;
        font-weight: 700;
        font-size: 20px;
        display: block;
        margin-bottom: 8px;
    }

    /* ============================================
       RESPONSIVE
       ============================================ */
    @media (max-width: 768px) {
        .contenedor-devoluciones {
            padding: 20px 15px;
            margin: 20px auto;
        }

        .btn-solicitar-devolucion {
            font-size: 16px;
            padding: 12px 0;
        }

        .filtros-estado {
            gap: 10px;
            justify-content: center;
        }

        .btn-filtro {
            min-width: auto;
            padding: 8px 14px;
            font-size: 13px;
            flex: 1 1 40%;
            max-width: 140px;
        }

        .sin-datos {
            padding: 50px 20px;
            font-size: 15px;
            max-width: 100%;
        }
    }
</style>

<div class="contenedor-devoluciones">
    <h1 class="titulo-seccion">Mis Devoluciones</h1>

    <!-- Botón para solicitar nueva devolución -->
    <button class="btn btn-naranja btn-grande" onclick="abrirModalSolicitud()">
        + Solicitar Nueva Devolución
    </button>

    <!-- Filtros por estado -->
    <div class="filtros-estado">
        <button class="btn-filtro activo" data-estado="TODAS">Todas</button>
        <button class="btn-filtro" data-estado="SOLICITADA">Solicitadas</button>
        <button class="btn-filtro" data-estado="APROBADA">Aprobadas</button>
        <button class="btn-filtro" data-estado="RECHAZADA">Rechazadas</button>
        <button class="btn-filtro" data-estado="PROCESADA">Procesadas</button>
    </div>

    <!-- Loading -->
    <div id="loading" class="loading" style="display: none;">
        Cargando devoluciones...
    </div>

    <!-- Lista de devoluciones -->
    <div id="lista-devoluciones" class="lista-devoluciones"></div>

    <!-- Sin devoluciones -->
    <div id="sin-devoluciones" class="sin-datos" style="display: none;">
        <h3>No tienes devoluciones</h3>
        <p>Cuando solicites una devolución, aparecerá aquí.</p>
    </div>
</div>

<!-- CONTINUARÁ EN PARTE 2 CON MODALES Y JAVASCRIPT -->

<!-- MODAL: SOLICITAR DEVOLUCIÓN -->
<div id="modal-solicitud" class="modal">
    <div class="modal-contenido">
        <div class="modal-header">
            <h2>Solicitar Devolución</h2>
            <button class="btn-cerrar-modal" onclick="cerrarModalSolicitud()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-solicitud-devolucion">
                <!-- Paso 1: Seleccionar pedido -->
                <div id="paso-pedido">
                    <div class="form-group">
                        <label>Selecciona el pedido que deseas devolver:</label>
                        <select id="select-pedido" class="form-control" required>
                            <option value="">-- Selecciona un pedido --</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-naranja" onclick="cargarProductosPedido()">
                        Siguiente
                    </button>
                </div>

                <!-- Paso 2: Seleccionar productos -->
                <div id="paso-productos" style="display: none;">
                    <h3 style="color: #2C3E50; margin-bottom: 15px;">Selecciona los productos a devolver:</h3>
                    <div id="tabla-productos-container"></div>

                    <div class="form-group" style="margin-top: 25px;">
                        <label>Motivo de la devolución:</label>
                        <textarea id="motivo-devolucion" required placeholder="Describe el motivo de tu devolución..."></textarea>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="button" class="btn btn-blanco" onclick="volverSeleccionPedido()">
                            Atrás
                        </button>
                        <button type="submit" class="btn btn-naranja" style="flex: 1;">
                            Solicitar Devolución
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: DETALLE DEVOLUCIÓN -->
<div id="modal-detalle" class="modal">
    <div class="modal-contenido">
        <div class="modal-header">
            <h2>Detalle de Devolución</h2>
            <button class="btn-cerrar-modal" onclick="cerrarModalDetalle()">&times;</button>
        </div>
        <div class="modal-body" id="contenido-detalle"></div>
        <div class="modal-footer">
            <button class="btn btn-blanco" onclick="cerrarModalDetalle()">Cerrar</button>
        </div>
    </div>
</div>

<script>
// ============================================
// VARIABLES GLOBALES
// ============================================
let devolucionesData = [];
let estadoFiltroActual = 'TODAS';

// ============================================
// CARGAR DEVOLUCIONES AL INICIO
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    cargarDevoluciones();
    configurarFiltros();
});

// ============================================
// CONFIGURAR FILTROS
// ============================================
function configurarFiltros() {
    document.querySelectorAll('.btn-filtro').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.btn-filtro').forEach(b => b.classList.remove('activo'));
            this.classList.add('activo');
            estadoFiltroActual = this.getAttribute('data-estado');
            filtrarDevoluciones();
        });
    });
}

// ============================================
// CARGAR DEVOLUCIONES
// ============================================
function cargarDevoluciones() {
    const loading = document.getElementById('loading');
    const lista = document.getElementById('lista-devoluciones');
    const sinDatos = document.getElementById('sin-devoluciones');
    
    loading.style.display = 'block';
    lista.style.display = 'none';
    sinDatos.style.display = 'none';
    
    fetch('<?php echo BASE_URL; ?>controllers/DevolucionController.php?action=listarDevoluciones')
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';
            
            if (data.success && data.devoluciones.length > 0) {
                devolucionesData = data.devoluciones;
                filtrarDevoluciones();
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
// FILTRAR DEVOLUCIONES
// ============================================
function filtrarDevoluciones() {
    const lista = document.getElementById('lista-devoluciones');
    
    let devolucionesFiltradas = devolucionesData;
    if (estadoFiltroActual !== 'TODAS') {
        devolucionesFiltradas = devolucionesData.filter(d => d.EstadoDevolucion === estadoFiltroActual);
    }
    
    if (devolucionesFiltradas.length === 0) {
        lista.innerHTML = '<div class="sin-datos"><p>No hay devoluciones con este estado</p></div>';
        lista.style.display = 'block';
        return;
    }
    
    let html = '';
    devolucionesFiltradas.forEach(devolucion => {
        html += `
            <div class="tarjeta-devolucion">
                <div class="devolucion-header">
                    <span class="devolucion-numero">Devolución #${devolucion.IdDevolucion}</span>
                    <span class="badge ${getBadgeClass(devolucion.EstadoDevolucion)}">${devolucion.EstadoDevolucion}</span>
                </div>
                
                <div class="devolucion-info">
                    <div class="info-item">
                        <span class="info-label">Pedido</span>
                        <span class="info-valor">#${devolucion.NumeroPedido}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Fecha Solicitud</span>
                        <span class="info-valor">${formatearFecha(devolucion.FechaSolicitud)}</span>
                    </div>
                </div>
                
                <div class="devolucion-motivo">
                    <strong style="color: #2C3E50; font-size: 13px;">Motivo:</strong>
                    <p>${devolucion.Motivo}</p>
                </div>
                
                <div class="devolucion-acciones">
                    <button class="btn btn-blanco" onclick="verDetalle(${devolucion.IdDevolucion})">
                        Ver Detalle
                    </button>
                </div>
            </div>
        `;
    });
    
    lista.innerHTML = html;
    lista.style.display = 'grid';
}

// ============================================
// MODAL SOLICITUD - ABRIR
// ============================================
function abrirModalSolicitud() {
    document.getElementById('modal-solicitud').classList.add('activo');
    cargarPedidosDevolvibles();
    
    // Reset form
    document.getElementById('paso-pedido').style.display = 'block';
    document.getElementById('paso-productos').style.display = 'none';
    document.getElementById('select-pedido').value = '';
    document.getElementById('motivo-devolucion').value = '';
}

function cerrarModalSolicitud() {
    document.getElementById('modal-solicitud').classList.remove('activo');
}

// ============================================
// CARGAR PEDIDOS DEVOLVIBLES
// ============================================
function cargarPedidosDevolvibles() {
    const select = document.getElementById('select-pedido');
    select.innerHTML = '<option value="">Cargando...</option>';
    
    fetch('<?php echo BASE_URL; ?>controllers/DevolucionController.php?action=obtenerPedidosDevolvibles')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                select.innerHTML = '<option value="">-- Selecciona un pedido --</option>';
                
                if (data.pedidos.length === 0) {
                    select.innerHTML = '<option value="">No hay pedidos disponibles para devolver</option>';
                    return;
                }
                
                data.pedidos.forEach(pedido => {
                    select.innerHTML += `
                        <option value="${pedido.IdPedido}">
                            Pedido #${pedido.NumeroPedido} - ${formatearFecha(pedido.FechaPedido)} - ${formatearMoneda(pedido.Total)}
                        </option>
                    `;
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            select.innerHTML = '<option value="">Error al cargar pedidos</option>';
        });
}

// ============================================
// CARGAR PRODUCTOS DEL PEDIDO
// ============================================
function cargarProductosPedido() {
    const idPedido = document.getElementById('select-pedido').value;
    
    if (!idPedido) {
        alert('Por favor selecciona un pedido');
        return;
    }
    
    fetch(`<?php echo BASE_URL; ?>controllers/DevolucionController.php?action=obtenerDetallePedido&id=${idPedido}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.detalle.productos.length > 0) {
                mostrarProductosPedido(data.detalle.productos);
                document.getElementById('paso-pedido').style.display = 'none';
                document.getElementById('paso-productos').style.display = 'block';
            } else {
                alert('No se pudieron cargar los productos del pedido');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los productos');
        });
}

// ============================================
// MOSTRAR PRODUCTOS EN TABLA
// ============================================
function mostrarProductosPedido(productos) {
    const container = document.getElementById('tabla-productos-container');
    
    let html = `
        <table class="tabla-productos">
            <thead>
                <tr>
                    <th style="width: 50px;">
                        <input type="checkbox" id="select-all" onchange="toggleTodosProductos()" class="checkbox-producto">
                    </th>
                    <th>Producto</th>
                    <th style="width: 120px;">Cant. Comprada</th>
                    <th style="width: 120px;">Cant. Devolver</th>
                    <th style="width: 100px;">Precio Unit.</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    productos.forEach(producto => {
        html += `
            <tr>
                <td>
                    <input type="checkbox" class="checkbox-producto producto-checkbox" 
                           data-id="${producto.IdProducto}" 
                           data-max="${producto.Cantidad}"
                           onchange="toggleProducto(this)">
                </td>
                <td><strong>${producto.NombreProducto}</strong></td>
                <td>${producto.Cantidad}</td>
                <td>
                    <input type="number" 
                           class="input-cantidad" 
                           id="cant-${producto.IdProducto}" 
                           min="1" 
                           max="${producto.Cantidad}" 
                           value="${producto.Cantidad}"
                           disabled>
                </td>
                <td>${formatearMoneda(producto.PrecioUnitario)}</td>
            </tr>
        `;
    });
    
    html += '</tbody></table>';
    container.innerHTML = html;
}

// ============================================
// TOGGLE PRODUCTOS
// ============================================
function toggleTodosProductos() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.producto-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
        const idProducto = checkbox.getAttribute('data-id');
        document.getElementById(`cant-${idProducto}`).disabled = !selectAll.checked;
    });
}

function toggleProducto(checkbox) {
    const idProducto = checkbox.getAttribute('data-id');
    const inputCantidad = document.getElementById(`cant-${idProducto}`);
    inputCantidad.disabled = !checkbox.checked;
}

// ============================================
// VOLVER A SELECCIÓN DE PEDIDO
// ============================================
function volverSeleccionPedido() {
    document.getElementById('paso-productos').style.display = 'none';
    document.getElementById('paso-pedido').style.display = 'block';
}

// ============================================
// ENVIAR SOLICITUD DE DEVOLUCIÓN
// ============================================
document.getElementById('form-solicitud-devolucion').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const idPedido = document.getElementById('select-pedido').value;
    const motivo = document.getElementById('motivo-devolucion').value;
    
    // Obtener productos seleccionados
    const checkboxes = document.querySelectorAll('.producto-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Debes seleccionar al menos un producto para devolver');
        return;
    }
    
    const productos = [];
    checkboxes.forEach(checkbox => {
        const idProducto = checkbox.getAttribute('data-id');
        const cantidad = document.getElementById(`cant-${idProducto}`).value;
        const maxCantidad = checkbox.getAttribute('data-max');
        
        if (parseInt(cantidad) > parseInt(maxCantidad)) {
            alert('La cantidad a devolver no puede ser mayor a la comprada');
            return;
        }
        
        productos.push({
            IdProducto: parseInt(idProducto),
            Cantidad: parseInt(cantidad)
        });
    });
    
    if (productos.length === 0) return;
    
    const formData = new FormData();
    formData.append('id_pedido', idPedido);
    formData.append('motivo', motivo);
    formData.append('productos', JSON.stringify(productos));
    
    // Enviar solicitud
    fetch('<?php echo BASE_URL; ?>controllers/DevolucionController.php?action=solicitarDevolucion',{
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.mensaje);
            cerrarModalSolicitud();
            cargarDevoluciones();
        } else {
            alert('Error: ' + data.mensaje);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al solicitar la devolución');
    });
});

// ============================================
// VER DETALLE DE DEVOLUCIÓN
// ============================================
function verDetalle(idDevolucion) {
    document.getElementById('modal-detalle').classList.add('activo');
    document.getElementById('contenido-detalle').innerHTML = '<div class="loading">Cargando...</div>';
    
    fetch(`<?php echo BASE_URL; ?>controllers/DevolucionController.php?action=obtenerDetalle&id=${idDevolucion}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarDetalleDevolucion(data.detalle);
            } else {
                document.getElementById('contenido-detalle').innerHTML = '<p>Error al cargar el detalle</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('contenido-detalle').innerHTML = '<p>Error al cargar el detalle</p>';
        });
}

function cerrarModalDetalle() {
    document.getElementById('modal-detalle').classList.remove('activo');
}

// ============================================
// MOSTRAR DETALLE DEVOLUCIÓN
// ============================================
function mostrarDetalleDevolucion(detalle) {
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
        <div style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="color: #2C3E50; margin: 0;">Devolución #${info.IdDevolucion}</h3>
                <span class="badge ${getBadgeClass(info.EstadoDevolucion)}">${info.EstadoDevolucion}</span>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 20px;">
                <div>
                    <div class="info-label">Pedido Original</div>
                    <div class="info-valor">#${info.NumeroPedido}</div>
                </div>
                <div>
                    <div class="info-label">Fecha Solicitud</div>
                    <div class="info-valor">${formatearFecha(info.FechaSolicitud)}</div>
                </div>
            </div>
            
            <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                <strong style="color: #2C3E50;">Motivo:</strong>
                <p style="margin: 5px 0 0 0; color: #7f8c8d;">${info.Motivo}</p>
            </div>
        </div>
        
        <h4 style="color: #2C3E50; margin-bottom: 15px;">Productos a Devolver</h4>
        <table class="tabla-productos">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                ${htmlProductos}
            </tbody>
            <tfoot>
                <tr style="background: #f8f9fa; font-weight: 600;">
                    <td colspan="3" style="text-align: right;">TOTAL A REEMBOLSAR:</td>
                    <td>${formatearMoneda(totalDevolucion)}</td>
                </tr>
            </tfoot>
        </table>
    `;
    
    document.getElementById('contenido-detalle').innerHTML = html;
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
        'APROBADA': 'badge-verde',
        'RECHAZADA': 'badge-rojo',
        'PROCESADA': 'badge-azul'
    };
    return badges[estado] || 'badge-azul';
}
</script>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>