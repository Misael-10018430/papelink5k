<?php
/**
 * Vista: Reportes del Sistema
 * Generación de reportes de ventas, inventario y finanzas
 */

require_once __DIR__ . '/../../config/config.php';

$titulo = "Reportes del Sistema - Papelink";
include __DIR__ . '/includes/header.php';
?>

<style>
    .tabs-reportes {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 20px;
    }
    
    .tabs-header-reportes {
        display: flex;
        background-color: #f8f9fa;
        border-bottom: 2px solid #e0e0e0;
        overflow-x: auto;
    }
    
    .tab-btn-reporte {
        flex: 1;
        min-width: 150px;
        padding: 15px 20px;
        background: none;
        border: none;
        color: #7f8c8d;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        border-bottom: 3px solid transparent;
        white-space: nowrap;
    }
    
    .tab-btn-reporte:hover {
        background-color: #f0f0f0;
    }
    
    .tab-btn-reporte.activo {
        color: #FF6347;
        border-bottom-color: #FF6347;
        background-color: white;
    }
    
    .tab-content-reporte {
        display: none;
        padding: 30px;
    }
    
    .tab-content-reporte.activo {
        display: block;
    }
    
    .filtros-reporte {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 6px;
        margin-bottom: 25px;
    }
    
    .filtros-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        align-items: end;
    }
    
    .campo-filtro label {
        display: block;
        color: #2C3E50;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
    }
    
    .campo-filtro input,
    .campo-filtro select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }
    
    .estadisticas-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }
    
    .estadistica-card {
        background: white;
        border-left: 4px solid #FF6347;
        padding: 20px;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    
    .estadistica-valor {
        font-size: 28px;
        font-weight: 700;
        color: #2C3E50;
        margin-bottom: 5px;
    }
    
    .estadistica-label {
        font-size: 12px;
        color: #7f8c8d;
        text-transform: uppercase;
    }
    
    .tabla-reporte {
        background: white;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }
    
    .tabla-reporte table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .tabla-reporte thead {
        background-color: #34495e;
        color: white;
    }
    
    .tabla-reporte th {
        padding: 14px;
        text-align: left;
        font-weight: 600;
        font-size: 12px;
    }
    
    .tabla-reporte td {
        padding: 12px 14px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 13px;
    }
    
    .tabla-reporte tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .sin-datos {
        text-align: center;
        padding: 40px;
        color: #7f8c8d;
        background: #f8f9fa;
        border-radius: 6px;
    }
    
    .loading {
        text-align: center;
        padding: 40px;
        color: #FF6347;
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
                            <button type="submit" class="btn btn-naranja">Generar Reporte</button>
                        </div>
                    </div>
                </form>
            </div>

            <div id="resultado-ventas-periodo" style="display: none;">
                <div id="estadisticas-ventas-periodo" class="estadisticas-cards"></div>
                <h3 style="color: #2C3E50; margin-bottom: 15px;">Ventas por Día</h3>
                <div id="tabla-ventas-dia" class="tabla-reporte"></div>
                <h3 style="color: #2C3E50; margin-bottom: 15px; margin-top: 25px;">Ventas por Categoría</h3>
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
                            <button type="submit" class="btn btn-naranja">Generar Reporte</button>
                        </div>
                    </div>
                </form>
            </div>

            <div id="resultado-ventas-metodo" style="display: none;">
                <h3 style="color: #2C3E50; margin-bottom: 15px;">Ventas por Método de Pago</h3>
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
                            <button type="submit" class="btn btn-naranja">Generar Reporte</button>
                        </div>
                    </div>
                </form>
            </div>

            <div id="resultado-ventas-cliente" style="display: none;">
                <h3 style="color: #2C3E50; margin-bottom: 15px;">Mejores Clientes</h3>
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
                            <button type="submit" class="btn btn-naranja">Generar Reporte</button>
                        </div>
                    </div>
                </form>
            </div>

            <div id="resultado-financiero" style="display: none;">
                <div id="estadisticas-financiero" class="estadisticas-cards"></div>
                <h3 style="color: #2C3E50; margin-bottom: 15px;">Márgenes por Categoría</h3>
                <div id="tabla-margenes-categoria" class="tabla-reporte"></div>
                <h3 style="color: #2C3E50; margin-bottom: 15px; margin-top: 25px;">Top 10 Productos Más Rentables</h3>
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
                            <button type="submit" class="btn btn-naranja">Generar Reporte</button>
                        </div>
                    </div>
                </form>
            </div>

            <div id="resultado-productos" style="display: none;">
                <h3 style="color: #2C3E50; margin-bottom: 15px;">Productos Más Vendidos</h3>
                <div id="tabla-productos" class="tabla-reporte"></div>
            </div>

            <div id="loading-productos" class="loading" style="display: none;">
                Generando reporte...
            </div>
        </div>

        <!-- TAB 6: INVENTARIO ACTUAL -->
        <div id="tab-inventario" class="tab-content-reporte">
            <div class="filtros-reporte">
                <button type="button" id="btn-inventario" class="btn btn-naranja">Generar Reporte de Inventario</button>
            </div>

            <div id="resultado-inventario" style="display: none;">
                <div id="estadisticas-inventario" class="estadisticas-cards"></div>
                <h3 style="color: #2C3E50; margin-bottom: 15px;">Inventario por Categoría</h3>
                <div id="tabla-inventario-categoria" class="tabla-reporte"></div>
                <h3 style="color: #2C3E50; margin-bottom: 15px; margin-top: 25px;">Productos con Stock Crítico</h3>
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
                    tablaHTML += `<tr><td><strong>${index + 1}. ${item.NombreProducto}</strong><br><small style="color: #7f8c8d;">${item.CodigoProducto}</small></td><td>${formatearNumero(item.UnidadesVendidas)}</td><td>${formatearMoneda(item.VentasTotales)}</td><td>${formatearMoneda(item.CostosTotales)}</td><td>${formatearMoneda(item.GananciaNeta)}</td><td>${formatearPorcentaje(item.PorcentajeMargen)}</td></tr>`;
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
                tablaHTML += `<tr><td><strong style="font-size: 18px; color: #FF6347;">#${index + 1}</strong></td><td><strong>${item.NombreProducto}</strong><br><small style="color: #7f8c8d;">${item.CodigoProducto}</small></td><td>${item.NombreCategoria}</td><td>${item.NombreMarca}</td><td>${formatearNumero(item.UnidadesVendidas)}</td><td>${formatearNumero(item.PedidosConProducto)}</td><td>${formatearMoneda(item.VentasTotales)}</td><td>${formatearMoneda(item.PrecioPromedio)}</td></tr>`;
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
                    tablaHTML += `<tr style="background-color: #fff3cd;"><td><strong>${item.NombreProducto}</strong><br><small style="color: #7f8c8d;">${item.CodigoProducto}</small></td><td>${item.NombreCategoria}</td><td><strong style="color: #e74c3c;">${formatearNumero(item.CantidadDisponible)}</strong></td><td>${formatearNumero(item.StockMinimo)}</td><td><strong style="color: #856404;">${formatearNumero(item.UnidadesFaltantes)}</strong></td></tr>`;
                });
                tablaHTML += '</tbody></table>';
                document.getElementById('tabla-stock-critico').innerHTML = tablaHTML;
            } else {
                document.getElementById('tabla-stock-critico').innerHTML = '<div class="sin-datos">No hay productos con stock crítico 👍</div>';
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