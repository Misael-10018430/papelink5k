<?php
/**
 * Vista: Gestión de Clientes
 * Listado de clientes con filtros y acciones
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/ClienteController.php';

$controller = new ClienteController();
$datos = $controller->listar();

$titulo = "Gestión de Clientes - Papelink";
include __DIR__ . '/includes/header.php';
?>

<style>
    body {
        background-color: #f5f5f5;
        font-family: Arial, sans-serif;
    }
    
    .contenedor {
        padding: 25px;
        max-width: 1400px;
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
    
    .tarjeta {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .grid-estadisticas {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .tarjeta-estadistica {
        background: white;
        border-left: 4px solid #FF6347;
        padding: 20px;
        border-radius: 6px;
    }
    
    .tarjeta-estadistica.verde { border-left-color: #28a745; }
    .tarjeta-estadistica.azul { border-left-color: #17a2b8; }
    
    .estadistica-label {
        color: #7f8c8d;
        font-size: 12px;
        text-transform: uppercase;
        margin-bottom: 8px;
        font-weight: 500;
    }
    
    .estadistica-numero {
        color: #2C3E50;
        font-size: 30px;
        font-weight: 700;
    }
    
    .filtros-form {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: flex-end;
    }
    
    .campo {
        flex: 1;
        min-width: 180px;
    }
    
    .campo label {
        display: block;
        color: #2C3E50;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 6px;
    }
    
    .campo input,
    .campo select {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }
    
    .campo input:focus,
    .campo select:focus {
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
    
    .tabla-wrapper {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        overflow: hidden;
    }
    
    .tabla {
        width: 100%;
        border-collapse: collapse;
    }
    
    .tabla thead {
        background-color: #f8f9fa;
    }
    
    .tabla th {
        padding: 14px 12px;
        text-align: left;
        color: #2C3E50;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        border-bottom: 2px solid #e0e0e0;
    }
    
    .tabla td {
        padding: 14px 12px;
        border-bottom: 1px solid #f0f0f0;
        color: #2C3E50;
        font-size: 14px;
    }
    
    .tabla tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 500;
    }
    
    .badge-azul {
        background-color: #e3f2fd;
        color: #1976d2;
    }
    
    .badge-morado {
        background-color: #f3e5f5;
        color: #7b1fa2;
    }
    
    .badge-gris {
        background-color: #f0f0f0;
        color: #757575;
    }
    
    .toggle {
        position: relative;
        display: inline-block;
        width: 42px;
        height: 22px;
    }
    
    .toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
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
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }
    
    input:checked + .toggle-slider {
        background-color: #28a745;
    }
    
    input:checked + .toggle-slider:before {
        transform: translateX(20px);
    }
    
    .paginacion {
        display: flex;
        justify-content: center;
        gap: 6px;
        padding: 20px;
        flex-wrap: wrap;
    }
    
    .paginacion a {
        padding: 7px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        color: #2C3E50;
        text-decoration: none;
        font-size: 13px;
    }
    
    .paginacion a:hover {
        background-color: #FF6347;
        color: white;
        border-color: #FF6347;
    }
    
    .paginacion .activo {
        background-color: #FF6347;
        color: white;
        border-color: #FF6347;
    }
    
    .paginacion .disabled {
        opacity: 0.5;
        pointer-events: none;
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
        padding: 40px;
        color: #7f8c8d;
    }
</style>

<div class="contenedor">
    <h1 class="titulo-principal">Gestión de Clientes</h1>
    <p class="subtitulo">Administra y consulta información de tus clientes</p>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alerta alerta-exito">
            ✓ <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alerta alerta-error">
            ❌ <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="tarjeta">
        <form method="GET" action="clientes.php" class="filtros-form">
            <div class="campo">
                <label>Tipo de Cliente</label>
                <select name="tipo">
                    <option value="">Todos los tipos</option>
                    <?php foreach ($datos['tipos'] as $tipo): ?>
                        <option value="<?php echo $tipo['IdTipoCliente']; ?>" 
                            <?php echo (isset($_GET['tipo']) && $_GET['tipo'] == $tipo['IdTipoCliente']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tipo['NombreTipo']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label>Segmento</label>
                <select name="segmento">
                    <option value="">Todos los segmentos</option>
                    <?php foreach ($datos['segmentos'] as $segmento): ?>
                        <option value="<?php echo $segmento['IdSegmento']; ?>"
                            <?php echo (isset($_GET['segmento']) && $_GET['segmento'] == $segmento['IdSegmento']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($segmento['NombreSegmento']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo" style="max-width: 140px;">
                <label>Estado</label>
                <select name="estado">
                    <option value="">Todos</option>
                    <option value="1" <?php echo (isset($_GET['estado']) && $_GET['estado'] == '1') ? 'selected' : ''; ?>>Activos</option>
                    <option value="0" <?php echo (isset($_GET['estado']) && $_GET['estado'] == '0') ? 'selected' : ''; ?>>Inactivos</option>
                </select>
            </div>

            <div class="campo">
                <label>Buscar Cliente</label>
                <input type="text" name="busqueda" 
                       placeholder="Nombre del cliente..."
                       value="<?php echo isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : ''; ?>">
            </div>

            <button type="submit" class="btn btn-naranja">Filtrar</button>
            
            <?php if (isset($_GET['tipo']) || isset($_GET['segmento']) || isset($_GET['estado']) || isset($_GET['busqueda'])): ?>
                <a href="clientes.php" class="btn btn-gris">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Estadísticas -->
    <div class="grid-estadisticas">
        <div class="tarjeta-estadistica">
            <div class="estadistica-label">Total de Clientes</div>
            <div class="estadistica-numero"><?php echo number_format($datos['paginacion']['total']); ?></div>
        </div>
        <div class="tarjeta-estadistica verde">
            <div class="estadistica-label">Clientes Activos</div>
            <div class="estadistica-numero">
                <?php 
                    $activos = array_filter($datos['clientes'], function($c) { return $c['Estado'] == 1; });
                    echo number_format(count($activos)); 
                ?>
            </div>
        </div>
        <div class="tarjeta-estadistica azul">
            <div class="estadistica-label">Mostrando</div>
            <div class="estadistica-numero"><?php echo count($datos['clientes']); ?></div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="tabla-wrapper">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Contacto</th>
                    <th>Tipo</th>
                    <th>Segmento</th>
                    <th style="text-align: center;">Pedidos</th>
                    <th>Total Gastado</th>
                    <th>Registro</th>
                    <th style="text-align: center;">Estado</th>
                    <th style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($datos['clientes'])): ?>
                    <tr>
                        <td colspan="9" class="texto-vacio">
                            No se encontraron clientes con los filtros seleccionados
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($datos['clientes'] as $cliente): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 500;">
                                    <?php echo htmlspecialchars($cliente['NombreCliente']); ?>
                                </div>
                                <div style="font-size: 12px; color: #7f8c8d;">
                                    ID: <?php echo $cliente['IdCliente']; ?>
                                </div>
                            </td>
                            <td>
                                <div><?php echo htmlspecialchars($cliente['Email']); ?></div>
                                <div style="font-size: 12px; color: #7f8c8d;">
                                    <?php echo htmlspecialchars($cliente['Telefono']); ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-azul">
                                    <?php echo htmlspecialchars($cliente['TipoCliente']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($cliente['SegmentoCliente'])): ?>
                                    <span class="badge badge-morado">
                                        <?php echo htmlspecialchars($cliente['SegmentoCliente']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-gris">Sin segmento</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <strong><?php echo $cliente['TotalPedidos']; ?></strong>
                            </td>
                            <td>
                                <strong style="color: #FF6347;">
                                    $<?php echo number_format($cliente['TotalGastado'], 2); ?>
                                </strong>
                            </td>
                            <td style="font-size: 12px; color: #7f8c8d;">
                                <?php echo date('d/m/Y', strtotime($cliente['FechaRegistro'])); ?>
                            </td>
                            <td style="text-align: center;">
                                <label class="toggle">
                                    <input type="checkbox" class="estado-toggle"
                                           <?php echo $cliente['Estado'] ? 'checked' : ''; ?>
                                           data-id="<?php echo $cliente['IdCliente']; ?>">
                                    <span class="toggle-slider"></span>
                                </label>
                            </td>
                            <td style="text-align: center;">
                                <a href="cliente_detalle.php?id=<?php echo $cliente['IdCliente']; ?>" 
                                   class="btn btn-naranja">
                                    Ver Detalle
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Paginación -->
        <?php if ($datos['paginacion']['total_paginas'] > 1): ?>
            <div class="paginacion">
                <?php 
                $paginaActual = $datos['paginacion']['pagina_actual'];
                $totalPaginas = $datos['paginacion']['total_paginas'];
                
                $queryParams = $_GET;
                unset($queryParams['pagina']);
                $queryString = http_build_query($queryParams);
                $queryString = $queryString ? '&' . $queryString : '';
                ?>

                <a href="?pagina=<?php echo ($paginaActual - 1) . $queryString; ?>" 
                   class="<?php echo ($paginaActual == 1) ? 'disabled' : ''; ?>">
                    Anterior
                </a>

                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <a href="?pagina=<?php echo $i . $queryString; ?>" 
                       class="<?php echo ($i == $paginaActual) ? 'activo' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <a href="?pagina=<?php echo ($paginaActual + 1) . $queryString; ?>" 
                   class="<?php echo ($paginaActual == $totalPaginas) ? 'disabled' : ''; ?>">
                    Siguiente
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.querySelectorAll('.estado-toggle').forEach(toggle => {
    toggle.addEventListener('change', function() {
        const idCliente = this.getAttribute('data-id');
        const nuevoEstado = this.checked ? 1 : 0;
        
        fetch('../../controllers/ClienteController.php?action=cambiarEstado', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id_cliente=${idCliente}&estado=${nuevoEstado}`
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