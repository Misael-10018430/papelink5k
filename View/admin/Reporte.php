
<?php
/**
 * Vista: Reportes del Sistema
 * Generación de reportes de ventas, inventario y finanzas
 */
require_once __DIR__ . '/../../config/Auth.php';
//VERIFICAR PERMISO PARA REPORTES
Auth::requiereFuncionalidad('REPORTES_VER');
 $paginaActual = basename($_SERVER['PHP_SELF'], '.php');
require_once __DIR__ . '/../../config/config.php';
 $titulo = "Reportes del Sistema - Papelink";
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
        --color-advertencia: #ffc107;    /* Amarillo estándar para advertencia */
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
    .contenedor-principal {
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
    .subtitulo-reporte {
        color: var(--color-texto);
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 1.25rem;
    }

    /* ===================================
       COMPONENTES: SISTEMA DE TABS
       =================================== */
    .tabs-reportes {
        background: var(--color-blanco);
        border: 1px solid var(--color-borde);
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--sombra);
        margin-bottom: 1.5rem;
    }
    .tabs-header-reportes {
        display: flex;
        background-color: var(--color-fondo);
        border-bottom: 1px solid var(--color-borde);
        overflow-x: auto;
    }
    .tab-btn-reporte {
        flex: 1;
        min-width: 150px;
        padding: 1rem 1.25rem;
        background: none;
        border: none;
        color: var(--color-texto-claro);
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        border-bottom: 3px solid transparent;
        white-space: nowrap;
    }
    .tab-btn-reporte:hover {
        background-color: #e9ecef;
        color: var(--color-texto);
    }
    .tab-btn-reporte.activo {
        color: var(--color-primario);
        border-bottom-color: var(--color-primario);
        background-color: var(--color-blanco);
    }
    .tab-content-reporte {
        display: none;
        padding: 2rem;
    }
    .tab-content-reporte.activo {
        display: block;
    }

    /* ===================================
       COMPONENTES: FORMULARIOS Y FILTROS
       =================================== */
    .filtros-reporte {
        background: var(--color-fondo);
        padding: 1.5rem;
        border-radius: var(--border-radius);
        margin-bottom: 1.5rem;
    }
    .filtros-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        align-items: end;
    }
    .campo-filtro label {
        display: block;
        color: var(--color-texto);
        font-size: 0.8125rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .campo-filtro input,
    .campo-filtro select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--color-borde);
        border-radius: var(--border-radius);
        font-size: 0.875rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .campo-filtro input:focus,
    .campo-filtro select:focus {
        outline: none;
        border-color: var(--color-primario);
        box-shadow: 0 0 0 2px rgba(73, 80, 87, 0.25);
    }

    /* ===================================
       COMPONENTES: TARJETAS DE ESTADÍSTICAS
       =================================== */
    .estadisticas-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .estadistica-card {
        background: var(--color-blanco);
        border-left: 4px solid var(--color-primario);
        padding: 1.5rem;
        border-radius: var(--border-radius);
        box-shadow: var(--sombra);
    }
    .estadistica-valor {
        font-size: 2rem;
        font-weight: 700;
        color: var(--color-texto);
        margin-bottom: 0.5rem;
    }
    .estadistica-label {
        font-size: 0.75rem;
        color: var(--color-texto-claro);
        text-transform: uppercase;
        font-weight: 500;
    }

    /* ===================================
       COMPONENTES: TABLAS
       =================================== */
    .tabla-reporte {
        background: var(--color-blanco);
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--sombra);
        margin-bottom: 1.5rem;
    }
    .tabla-reporte table {
        width: 100%;
        border-collapse: collapse;
    }
    .tabla-reporte thead {
        background-color: var(--color-primario);
        color: var(--color-blanco);
    }
    .tabla-reporte th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    .tabla-reporte td {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid var(--color-borde);
        font-size: 0.875rem;
    }
    .tabla-reporte tbody tr:hover {
        background-color: rgba(0,0,0,.02);
    }
    .texto-pequeno { font-size: 0.8rem; color: var(--color-texto-claro); }

    /* ===================================
       COMPONENTES: ESTADOS ESPECIALES
       =================================== */
    .sin-datos {
        text-align: center;
        padding: 2.5rem;
        color: var(--color-texto-claro);
        background: var(--color-fondo);
        border-radius: var(--border-radius);
    }
    .loading {
        text-align: center;
        padding: 2.5rem;
        color: var(--color-primario);
        font-style: italic;
    }
    .posicion-destacada {
        font-size: 1.125rem;
        color: var(--color-primario);
    }
    .fila-stock-critico {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
    .valor-critico {
        color: var(--color-peligro);
        font-weight: 600;
    }
    .valor-faltante {
        color: var(--color-advertencia);
        font-weight: 600;
    }

    /* ===================================
       COMPONENTES: BOTONES
       =================================== */
    .btn {
        padding: 0.75rem 1.25rem;
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
</style>

<div class="contenedor-principal">
    <h1 class="titulo-pagina">Reportes del Sistema</h1>
    <div class="tabs-reportes">
        <!-- Tabs Header -->
        <div class="tabs-header-reportes">
            <button class="tab-btn-reporte activo" data-tab="ventas-periodo">Ventas por Período</button>
            <button class="tab-btn-reporte" data-tab="ventas-metodo">Ventas por Método de Pago</button>
            <button class="tab-btn-reporte" data-tab="ventas-cliente">Ventas por Cliente</button>
            <button class="tab-btn-reporte" data-tab="financiero">Reporte Financiero</button>
            <button class="tab-btn-reporte" data-tab="productos">Productos Más Vendidos</button>
            <button class="tab-btn-reporte" data-tab="inventario">Inventario Actual</button>
        </div>
        <!-- TAB 1: VENTAS POR PERÍODO -->
        <div id="tab-ventas-periodo" class="tab-content-reporte activo">
            <div class="filtros-reporte">
                <form id="form-ventas-periodo">
                    <div class="filtros-grid">
                        <div class="campo-filtro">
                            <label>Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" required>
                        </div>
                        <div class="campo-filtro">
                            <label>Fecha Fin</label>
                            <input type="date" name="fecha_fin" required>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primario">Generar Reporte</button>
                        </div>
                    </div>
                </form>
            </div>
            <div id="resultado-ventas-periodo" style="display: none;">
                <div id="estadisticas-ventas-periodo" class="estadisticas-cards"></div>
                <h3 class="subtitulo-reporte">Ventas por Día</h3>
                <div id="tabla-ventas-dia" class="tabla-reporte"></div>
                <h3 class="subtitulo-reporte">Ventas por Categoría</h3>
                <div id="tabla-ventas-categoria" class="tabla-reporte"></div>
            </div>
            <div id="loading-ventas-periodo" class="loading" style="display: none;">
                Generando reporte...
            </div>
        </div>
        <!-- TAB 2: VENTAS POR MÉTODO DE PAGO -->
        <div id="tab-ventas-metodo" class="tab-content-reporte">
            <div class="filtros-reporte">
                <form id="form-ventas-metodo">
                    <div class="filtros-grid">
                        <div class="campo-filtro">
                            <label>Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" required>
                        </div>
                        <div class="campo-filtro">
                            <label>Fecha Fin</label>
                            <input type="date" name="fecha_fin" required>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primario">Generar Reporte</button>
                        </div>
                    </div>
                </form>
            </div>
            <div id="resultado-ventas-metodo" style="display: none;">
                <h3 class="subtitulo-reporte">Ventas por Método de Pago</h3>
                <div id="tabla-ventas-metodo" class="tabla-reporte"></div>
            </div>
            <div id="loading-ventas-metodo" class="loading" style="display: none;">
                Generando reporte...
            </div>
        </div>
        <!-- TAB 3: VENTAS POR CLIENTE -->
        <div id="tab-ventas-cliente" class="tab-content-reporte">
            <div class="filtros-reporte">
                <form id="form-ventas-cliente">
                    <div class="filtros-grid">
                        <div class="campo-filtro">
                            <label>Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" required>
                        </div>
                        <div class="campo-filtro">
                            <label>Fecha Fin</label>
                            <input type="date" name="fecha_fin" required>
                        </div>
                        <div class="campo-filtro">
                            <label>Top Clientes</label>
                            <select name="top">
                                <option value="10">Top 10</option>
                                <option value="20" selected>Top 20</option>
                                <option value="50">Top 50</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primario">Generar Reporte</button>
                        </div>
                    </div>
                </form>
            </div>
            <div id="resultado-ventas-cliente" style="display: none;">
                <h3 class="subtitulo-reporte">Mejores Clientes</h3>
                <div id="tabla-ventas-cliente" class="tabla-reporte"></div>
            </div>
            <div id="loading-ventas-cliente" class="loading" style="display: none;">
                Generando reporte...
            </div>
        </div>
        <!-- TAB 4: REPORTE FINANCIERO -->
        <div id="tab-financiero" class="tab-content-reporte">
            <div class="filtros-reporte">
                <form id="form-financiero">
                    <div class="filtros-grid">
                        <div class="campo-filtro">
                            <label>Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" required>
                        </div>
                        <div class="campo-filtro">
                            <label>Fecha Fin</label>
                            <input type="date" name="fecha_fin" required>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primario">Generar Reporte</button>
                        </div>
                    </div>
                </form>
            </div>
            <div id="resultado-financiero" style="display: none;">
                <div id="estadisticas-financiero" class="estadisticas-cards"></div>
                <h3 class="subtitulo-reporte">Márgenes por Categoría</h3>
                <div id="tabla-margenes-categoria" class="tabla-reporte"></div>
                <h3 class="subtitulo-reporte">Top 10 Productos Más Rentables</h3>
                <div id="tabla-productos-rentables" class="tabla-reporte"></div>
            </div>
            <div id="loading-financiero" class="loading" style="display: none;">
                Generando reporte...
            </div>
        </div>
        <!-- TAB 5: PRODUCTOS MÁS VENDIDOS -->
        <div id="tab-productos" class="tab-content-reporte">
            <div class="filtros-reporte">
                <form id="form-productos">
                    <div class="filtros-grid">
                        <div class="campo-filtro">
                            <label>Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" required>
                        </div>
                        <div class="campo-filtro">
                            <label>Fecha Fin</label>
                            <input type="date" name="fecha_fin" required>
                        </div>
                        <div class="campo-filtro">
                            <label>Top Productos</label>
                            <select name="top">
                                <option value="10">Top 10</option>
                                <option value="20" selected>Top 20</option>
                                <option value="50">Top 50</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primario">Generar Reporte</button>
                        </div>
                    </div>
                </form>
            </div>
            <div id="resultado-productos" style="display: none;">
                <h3 class="subtitulo-reporte">Productos Más Vendidos</h3>
                <div id="tabla-productos" class="tabla-reporte"></div>
            </div>
            <div id="loading-productos" class="loading" style="display: none;">
                Generando reporte...
            </div>
        </div>
        <!-- TAB 6: INVENTARIO ACTUAL -->
        <div id="tab-inventario" class="tab-content-reporte">
            <div class="filtros-reporte">
                <button type="button" id="btn-inventario" class="btn btn-primario">Generar Reporte de Inventario</button>
            </div>
            <div id="resultado-inventario" style="display: none;">
                <div id="estadisticas-inventario" class="estadisticas-cards"></div>
                <h3 class="subtitulo-reporte">Inventario por Categoría</h3>
                <div id="tabla-inventario-categoria" class="tabla-reporte"></div>
                <h3 class="subtitulo-reporte">Productos con Stock Crítico</h3>
                <div id="tabla-stock-critico" class="tabla-reporte"></div>
            </div>
            <div id="loading-inventario" class="loading" style="display: none;">
                Generando reporte...
            </div>
        </div>

    </div>
</div>
<script>
// ============================================
// SISTEMA DE TABS
// ============================================
document.querySelectorAll('.tab-btn-reporte').forEach(btn => {
    btn.addEventListener('click', function() {
        const tabId = this.getAttribute('data-tab');
        document.querySelectorAll('.tab-btn-reporte').forEach(b => b.classList.remove('activo'));
        document.querySelectorAll('.tab-content-reporte').forEach(c => c.classList.remove('activo'));
        this.classList.add('activo');
        document.getElementById('tab-' + tabId).classList.add('activo');
    });
});

// ============================================
// FUNCIONES AUXILIARES
// ============================================
function formatearMoneda(valor) {
    return '$' + parseFloat(valor).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

function formatearPorcentaje(valor) {
    return parseFloat(valor).toFixed(2) + '%';
}

function formatearNumero(valor) {
    return parseFloat(valor).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function mostrarSinDatos(contenedor) {
    contenedor.innerHTML = '<div class="sin-datos">No se encontraron datos para el período seleccionado</div>';
}

// ============================================
// TAB 1: VENTAS POR PERÍODO
// ============================================
document.getElementById('form-ventas-periodo').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const loading = document.getElementById('loading-ventas-periodo');
    const resultado = document.getElementById('resultado-ventas-periodo');
    
    loading.style.display = 'block';
    resultado.style.display = 'none';
    
    fetch('../../controllers/ReporteController.php?action=generarVentasPorPeriodo', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        loading.style.display = 'none';
        if (data.success) {
            const { resumen, ventas_por_dia, ventas_por_categoria } = data.datos;
            document.getElementById('estadisticas-ventas-periodo').innerHTML = `
                <div class="estadistica-card">
                    <div class="estadistica-valor">${formatearNumero(resumen.TotalPedidos || 0)}</div>
                    <div class="estadistica-label">Total Pedidos</div>
                </div>
                <div class="estadistica-card">
                    <div class="estadistica-valor">${formatearNumero(resumen.ClientesUnicos || 0)}</div>
                    <div class="estadistica-label">Clientes Únicos</div>
                </div>
                <div class="estadistica-card">
                    <div class="estadistica-valor">${formatearMoneda(resumen.VentasTotales || 0)}</div>
                    <div class="estadistica-label">Ventas Totales</div>
                </div>
                <div class="estadistica-card">
                    <div class="estadistica-valor">${formatearMoneda(resumen.PromedioTicket || 0)}</div>
                    <div class="estadistica-label">Promedio por Pedido</div>
                </div>
            `;
            
            if (ventas_por_dia.length > 0) {
                let tablaHTML = '<table><thead><tr><th>Fecha</th><th>Pedidos</th><th>Ventas del Día</th></tr></thead><tbody>';
                ventas_por_dia.forEach(item => {
                    tablaHTML += `<tr><td>${item.Fecha}</td><td>${formatearNumero(item.TotalPedidos)}</td><td>${formatearMoneda(item.VentasDelDia)}</td></tr>`;
                });
                tablaHTML += '</tbody></table>';
                document.getElementById('tabla-ventas-dia').innerHTML = tablaHTML;
            } else {
                mostrarSinDatos(document.getElementById('tabla-ventas-dia'));
            }
            
            if (ventas_por_categoria.length > 0) {
                let tablaHTML = '<table><thead><tr><th>Categoría</th><th>Pedidos</th><th>Unidades</th><th>Ventas</th></tr></thead><tbody>';
                ventas_por_categoria.forEach(item => {
                    tablaHTML += `<tr><td><strong>${item.NombreCategoria}</strong></td><td>${formatearNumero(item.PedidosConCategoria)}</td><td>${formatearNumero(item.UnidadesVendidas)}</td><td>${formatearMoneda(item.VentasPorCategoria)}</td></tr>`;
                });
                tablaHTML += '</tbody></table>';
                document.getElementById('tabla-ventas-categoria').innerHTML = tablaHTML;
            } else {
                mostrarSinDatos(document.getElementById('tabla-ventas-categoria'));
            }
            resultado.style.display = 'block';
        } else {
            alert('Error: ' + data.mensaje);
        }
    })
    .catch(error => {
        loading.style.display = 'none';
        console.error('Error:', error);
        alert('Error al generar el reporte');
    });
});

// ============================================
// TAB 2: VENTAS POR MÉTODO DE PAGO
// ============================================
document.getElementById('form-ventas-metodo').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const loading = document.getElementById('loading-ventas-metodo');
    const resultado = document.getElementById('resultado-ventas-metodo');
    
    loading.style.display = 'block';
    resultado.style.display = 'none';
    
    fetch('../../controllers/ReporteController.php?action=generarVentasPorMetodoPago', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        loading.style.display = 'none';
        if (data.success && data.datos.length > 0) {
            let tablaHTML = '<table><thead><tr><th>Método de Pago</th><th>Total Pedidos</th><th>Ventas Totales</th><th>Promedio Ticket</th><th>% Pedidos</th></tr></thead><tbody>';
            data.datos.forEach(item => {
                tablaHTML += `<tr><td><strong>${item.NombreMetodo || 'Sin método'}</strong></td><td>${formatearNumero(item.TotalPedidos)}</td><td>${formatearMoneda(item.VentasTotales)}</td><td>${formatearMoneda(item.PromedioTicket)}</td><td>${formatearPorcentaje(item.PorcentajePedidos)}</td></tr>`;
            });
            tablaHTML += '</tbody></table>';
            document.getElementById('tabla-ventas-metodo').innerHTML = tablaHTML;
            resultado.style.display = 'block';
        } else {
            mostrarSinDatos(document.getElementById('tabla-ventas-metodo'));
            resultado.style.display = 'block';
        }
    })
    .catch(error => {
        loading.style.display = 'none';
        console.error('Error:', error);
        alert('Error al generar el reporte');
    });
});

// ============================================
// TAB 3: VENTAS POR CLIENTE
// ============================================
document.getElementById('form-ventas-cliente').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const loading = document.getElementById('loading-ventas-cliente');
    const resultado = document.getElementById('resultado-ventas-cliente');
    
    loading.style.display = 'block';
    resultado.style.display = 'none';
    
    fetch('../../controllers/ReporteController.php?action=generarVentasPorCliente', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        loading.style.display = 'none';
        if (data.success && data.datos.length > 0) {
            let tablaHTML = '<table><thead><tr><th>Cliente</th><th>Email</th><th>Tipo</th><th>Pedidos</th><th>Total Gastado</th><th>Promedio</th><th>Última Compra</th></tr></thead><tbody>';
            data.datos.forEach((item, index) => {
                tablaHTML += `<tr><td><strong>${index + 1}. ${item.NombreCliente}</strong></td><td>${item.Email}</td><td>${item.TipoCliente}</td><td>${formatearNumero(item.TotalPedidos)}</td><td>${formatearMoneda(item.TotalGastado)}</td><td>${formatearMoneda(item.PromedioGasto)}</td><td>${new Date(item.UltimaCompra).toLocaleDateString()}</td></tr>`;
            });
            tablaHTML += '</tbody></table>';
            document.getElementById('tabla-ventas-cliente').innerHTML = tablaHTML;
            resultado.style.display = 'block';
        } else {
            mostrarSinDatos(document.getElementById('tabla-ventas-cliente'));
            resultado.style.display = 'block';
        }
    })
    .catch(error => {
        loading.style.display = 'none';
        console.error('Error:', error);
        alert('Error al generar el reporte');
    });
});

// ============================================
// TAB 4: REPORTE FINANCIERO
// ============================================
document.getElementById('form-financiero').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const loading = document.getElementById('loading-financiero');
    const resultado = document.getElementById('resultado-financiero');
    
    loading.style.display = 'block';
    resultado.style.display = 'none';
    
    fetch('../../controllers/ReporteController.php?action=generarReporteFinanciero', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        loading.style.display = 'none';
        if (data.success) {
            const { resumen_financiero, margenes_por_categoria, productos_rentables } = data.datos;
            document.getElementById('estadisticas-financiero').innerHTML = `
                <div class="estadistica-card">
                    <div class="estadistica-valor">${formatearMoneda(resumen_financiero.VentasBrutas || 0)}</div>
                    <div class="estadistica-label">Ventas Brutas</div>
                </div>
                <div class="estadistica-card">
                    <div class="estadistica-valor">${formatearMoneda(resumen_financiero.CostosMercancia || 0)}</div>
                    <div class="estadistica-label">Costos Mercancía</div>
                </div>
                <div class="estadistica-card">
                    <div class="estadistica-valor">${formatearMoneda(resumen_financiero.GananciaBruta || 0)}</div>
                    <div class="estadistica-label">Ganancia Bruta</div>
                </div>
                <div class="estadistica-card">
                    <div class="estadistica-valor">${formatearPorcentaje(resumen_financiero.PorcentajeMargen || 0)}</div>
                    <div class="estadistica-label">Margen de Ganancia</div>
                </div>
            `;
            
            if (margenes_por_categoria.length > 0) {
                let tablaHTML = '<table><thead><tr><th>Categoría</th><th>Ventas Brutas</th><th>Costos</th><th>Ganancia Bruta</th><th>% Margen</th></tr></thead><tbody>';
                margenes_por_categoria.forEach(item => {
                    tablaHTML += `<tr><td><strong>${item.NombreCategoria}</strong></td><td>${formatearMoneda(item.VentasBrutas)}</td><td>${formatearMoneda(item.CostosMercancia)}</td><td>${formatearMoneda(item.GananciaBruta)}</td><td>${formatearPorcentaje(item.PorcentajeMargen)}</td></tr>`;
                });
                tablaHTML += '</tbody></table>';
                document.getElementById('tabla-margenes-categoria').innerHTML = tablaHTML;
            } else {
                mostrarSinDatos(document.getElementById('tabla-margenes-categoria'));
            }
            
            if (productos_rentables.length > 0) {
                let tablaHTML = '<table><thead><tr><th>Producto</th><th>Unidades</th><th>Ventas</th><th>Costos</th><th>Ganancia Neta</th><th>% Margen</th></tr></thead><tbody>';
                productos_rentables.forEach((item, index) => {
                    tablaHTML += `<tr><td><strong>${index + 1}. ${item.NombreProducto}</strong><br><small class="texto-pequeno">${item.CodigoProducto}</small></td><td>${formatearNumero(item.UnidadesVendidas)}</td><td>${formatearMoneda(item.VentasTotales)}</td><td>${formatearMoneda(item.CostosTotales)}</td><td>${formatearMoneda(item.GananciaNeta)}</td><td>${formatearPorcentaje(item.PorcentajeMargen)}</td></tr>`;
                });
                tablaHTML += '</tbody></table>';
                document.getElementById('tabla-productos-rentables').innerHTML = tablaHTML;
            } else {
                mostrarSinDatos(document.getElementById('tabla-productos-rentables'));
            }
            resultado.style.display = 'block';
        } else {
            alert('Error: ' + data.mensaje);
        }
    })
    .catch(error => {
        loading.style.display = 'none';
        console.error('Error:', error);
        alert('Error al generar el reporte');
    });
});

// ============================================
// TAB 5: PRODUCTOS MÁS VENDIDOS
// ============================================
document.getElementById('form-productos').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const loading = document.getElementById('loading-productos');
    const resultado = document.getElementById('resultado-productos');
    
    loading.style.display = 'block';
    resultado.style.display = 'none';
    
    fetch('../../controllers/ReporteController.php?action=generarProductosMasVendidos', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        loading.style.display = 'none';
        if (data.success && data.datos.length > 0) {
            let tablaHTML = '<table><thead><tr><th>Posición</th><th>Producto</th><th>Categoría</th><th>Marca</th><th>Unidades Vendidas</th><th>Pedidos</th><th>Ventas Totales</th><th>Precio Promedio</th></tr></thead><tbody>';
            data.datos.forEach((item, index) => {
                tablaHTML += `<tr><td><strong class="posicion-destacada">#${index + 1}</strong></td><td><strong>${item.NombreProducto}</strong><br><small class="texto-pequeno">${item.CodigoProducto}</small></td><td>${item.NombreCategoria}</td><td>${item.NombreMarca}</td><td>${formatearNumero(item.UnidadesVendidas)}</td><td>${formatearNumero(item.PedidosConProducto)}</td><td>${formatearMoneda(item.VentasTotales)}</td><td>${formatearMoneda(item.PrecioPromedio)}</td></tr>`;
            });
            tablaHTML += '</tbody></table>';
            document.getElementById('tabla-productos').innerHTML = tablaHTML;
            resultado.style.display = 'block';
        } else {
            mostrarSinDatos(document.getElementById('tabla-productos'));
            resultado.style.display = 'block';
        }
    })
    .catch(error => {
        loading.style.display = 'none';
        console.error('Error:', error);
        alert('Error al generar el reporte');
    });
});

// ============================================
// TAB 6: INVENTARIO ACTUAL
// ============================================
document.getElementById('btn-inventario').addEventListener('click', function() {
    const loading = document.getElementById('loading-inventario');
    const resultado = document.getElementById('resultado-inventario');
    
    loading.style.display = 'block';
    resultado.style.display = 'none';
    
    fetch('../../controllers/ReporteController.php?action=generarInventarioActual', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        loading.style.display = 'none';
        if (data.success) {
            const { resumen, inventario_por_categoria, stock_critico } = data.datos;
            document.getElementById('estadisticas-inventario').innerHTML = `
                <div class="estadistica-card">
                    <div class="estadistica-valor">${formatearNumero(resumen.TotalProductos || 0)}</div>
                    <div class="estadistica-label">Total Productos</div>
                </div>
                <div class="estadistica-card">
                    <div class="estadistica-valor">${formatearNumero(resumen.TotalUnidadesDisponibles || 0)}</div>
                    <div class="estadistica-label">Unidades Disponibles</div>
                </div>
                <div class="estadistica-card">
                    <div class="estadistica-valor">${formatearNumero(resumen.TotalUnidadesReservadas || 0)}</div>
                    <div class="estadistica-label">Unidades Reservadas</div>
                </div>
                <div class="estadistica-card">
                    <div class="estadistica-valor">${formatearMoneda(resumen.ValorInventarioDisponible || 0)}</div>
                    <div class="estadistica-label">Valor Inventario</div>
                </div>
            `;
            
            if (inventario_por_categoria.length > 0) {
                let tablaHTML = '<table><thead><tr><th>Categoría</th><th>Total Productos</th><th>Unidades Disponibles</th><th>Valor Inventario</th></tr></thead><tbody>';
                inventario_por_categoria.forEach(item => {
                    tablaHTML += `<tr><td><strong>${item.NombreCategoria}</strong></td><td>${formatearNumero(item.TotalProductos)}</td><td>${formatearNumero(item.UnidadesDisponibles)}</td><td>${formatearMoneda(item.ValorInventario)}</td></tr>`;
                });
                tablaHTML += '</tbody></table>';
                document.getElementById('tabla-inventario-categoria').innerHTML = tablaHTML;
            } else {
                mostrarSinDatos(document.getElementById('tabla-inventario-categoria'));
            }
            
            if (stock_critico.length > 0) {
                let tablaHTML = '<table><thead><tr><th>Producto</th><th>Categoría</th><th>Stock Disponible</th><th>Stock Mínimo</th><th>Unidades Faltantes</th></tr></thead><tbody>';
                stock_critico.forEach(item => {
                    tablaHTML += `<tr class="fila-stock-critico"><td><strong>${item.NombreProducto}</strong><br><small class="texto-pequeno">${item.CodigoProducto}</small></td><td>${item.NombreCategoria}</td><td><strong class="valor-critico">${formatearNumero(item.CantidadDisponible)}</strong></td><td>${formatearNumero(item.StockMinimo)}</td><td><strong class="valor-faltante">${formatearNumero(item.UnidadesFaltantes)}</strong></td></tr>`;
                });
                tablaHTML += '</tbody></table>';
                document.getElementById('tabla-stock-critico').innerHTML = tablaHTML;
            } else {
                document.getElementById('tabla-stock-critico').innerHTML = '<div class="sin-datos">No hay productos con stock crítico</div>';
            }
            resultado.style.display = 'block';
        } else {
            alert('Error: ' + data.mensaje);
        }
    })
    .catch(error => {
        loading.style.display = 'none';
        console.error('Error:', error);
        alert('Error al generar el reporte');
    });
});
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>