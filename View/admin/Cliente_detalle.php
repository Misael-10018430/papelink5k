<?php
/**
 * Vista: Detalle del Cliente
 * Información completa, estadísticas e historial de pedidos
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/ClienteController.php';

$idCliente = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$idCliente) {
    $_SESSION['error'] = 'ID de cliente inválido';
    header('Location: clientes.php');
    exit;
}

$controller = new ClienteController();
$datos = $controller->verDetalle($idCliente);

$titulo = "Detalle del Cliente - Papelink";
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
        grid-template-columns: 2fr 1fr;
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
        margin-bottom: 20px;
    }
    
    .tarjeta-titulo {
        color: #2C3E50;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .info-campo {
        margin-bottom: 15px;
    }
    
    .info-label {
        color: #7f8c8d;
        font-size: 12px;
        text-transform: uppercase;
        margin-bottom: 6px;
        font-weight: 500;
    }
    
    .info-valor {
        color: #2C3E50;
        font-size: 15px;
        font-weight: 500;
    }
    
    .estadisticas-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 15px;
        margin-bottom: 0;
    }
    
    .estadistica-box {
        text-align: center;
        padding: 20px 15px;
        background-color: #fff3e0;
        border-radius: 6px;
    }
    
    .estadistica-box.verde { background-color: #e8f5e9; }
    .estadistica-box.amarillo { background-color: #fff3e0; }
    .estadistica-box.azul { background-color: #e3f2fd; }
    
    .estadistica-numero {
        color: #FF6347;
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 5px 0;
    }
    
    .estadistica-numero.verde { color: #28a745; }
    .estadistica-numero.amarillo { color: #ffc107; }
    .estadistica-numero.azul { color: #2196f3; }
    
    .estadistica-label {
        color: #7f8c8d;
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 500;
    }
    
    .tabla {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    
    .tabla thead {
        background-color: #f8f9fa;
    }
    
    .tabla th {
        padding: 12px 10px;
        text-align: left;
        color: #2C3E50;
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        border-bottom: 2px solid #e0e0e0;
    }
    
    .tabla td {
        padding: 12px 10px;
        border-bottom: 1px solid #f0f0f0;
        color: #2C3E50;
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
        color: white;
    }
    
    .form-select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        margin-bottom: 15px;
    }
    
    .form-select:focus {
        outline: none;
        border-color: #FF6347;
    }
    
    .btn {
        padding: 10px 20px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        border: none;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s;
        text-align: center;
    }
    
    .btn-naranja {
        background-color: #FF6347;
        color: white;
        width: 100%;
    }
    
    .btn-naranja:hover {
        background-color: #e5533d;
    }
    
    .btn-gris {
        background-color: white;
        color: #2C3E50;
        border: 1px solid #ddd;
        width: 100%;
        margin-bottom: 8px;
    }
    
    .btn-gris:hover {
        background-color: #f5f5f5;
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
    
    .texto-vacio {
        text-align: center;
        padding: 30px;
        color: #7f8c8d;
        font-size: 14px;
    }
</style>

<div class="contenedor">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="dashboard.php">Dashboard</a>
        <span class="breadcrumb-separador">/</span>
        <a href="clientes.php">Clientes</a>
        <span class="breadcrumb-separador">/</span>
        <span>Detalle del Cliente</span>
    </div>

    <h1 class="titulo-principal">
        <?php echo htmlspecialchars($datos['perfil']['NombreCliente']); ?>
    </h1>
    <p class="subtitulo">
        Cliente desde el <?php echo date('d/m/Y', strtotime($datos['perfil']['FechaRegistro'])); ?>
    </p>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alerta alerta-exito">
            ✓ <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="grid-layout">
        <!-- Columna Izquierda -->
        <div>
            <!-- Información Personal -->
            <div class="tarjeta">
                <h2 class="tarjeta-titulo">Información Personal</h2>
                <div class="info-grid">
                    <div class="info-campo">
                        <div class="info-label">Nombre Completo</div>
                        <div class="info-valor">
                            <?php echo htmlspecialchars($datos['perfil']['NombreCliente']); ?>
                        </div>
                    </div>
                    <div class="info-campo">
                        <div class="info-label">Email</div>
                        <div class="info-valor">
                            <?php echo htmlspecialchars($datos['perfil']['Email']); ?>
                        </div>
                    </div>
                    <div class="info-campo">
                        <div class="info-label">Teléfono</div>
                        <div class="info-valor">
                            <?php echo htmlspecialchars($datos['perfil']['Telefono']); ?>
                        </div>
                    </div>
                    <div class="info-campo">
                        <div class="info-label">Canal de Registro</div>
                        <div class="info-valor">
                            <?php echo htmlspecialchars($datos['perfil']['CanalCliente']); ?>
                        </div>
                    </div>
                    <div class="info-campo" style="grid-column: 1 / -1;">
                        <div class="info-label">Dirección</div>
                        <div class="info-valor">
                            <?php echo htmlspecialchars($datos['perfil']['Direccion']); ?>
                        </div>
                    </div>
                    <div class="info-campo">
                        <div class="info-label">Último Acceso</div>
                        <div class="info-valor">
                            <?php echo $datos['perfil']['UltimoAcceso'] ? date('d/m/Y H:i', strtotime($datos['perfil']['UltimoAcceso'])) : 'Nunca'; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="tarjeta">
                <h2 class="tarjeta-titulo">Estadísticas de Compras</h2>
                <div class="estadisticas-grid">
                    <div class="estadistica-box">
                        <div class="estadistica-numero">
                            <?php echo $datos['estadisticas']['TotalPedidos']; ?>
                        </div>
                        <div class="estadistica-label">Total Pedidos</div>
                    </div>
                    <div class="estadistica-box verde">
                        <div class="estadistica-numero verde">
                            $<?php echo number_format($datos['estadisticas']['TotalGastado'], 2); ?>
                        </div>
                        <div class="estadistica-label">Total Gastado</div>
                    </div>
                    <div class="estadistica-box amarillo">
                        <div class="estadistica-numero amarillo">
                            <?php echo $datos['estadisticas']['PedidosPendientes']; ?>
                        </div>
                        <div class="estadistica-label">Pendientes</div>
                    </div>
                    <div class="estadistica-box azul">
                        <div class="estadistica-numero azul">
                            <?php echo $datos['estadisticas']['PedidosCompletados']; ?>
                        </div>
                        <div class="estadistica-label">Completados</div>
                    </div>
                </div>
            </div>

            <!-- Productos Más Comprados -->
            <?php if (!empty($datos['productos_top'])): ?>
            <div class="tarjeta">
                <h2 class="tarjeta-titulo">Productos Más Comprados</h2>
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th style="text-align: center;">Cantidad Total</th>
                            <th style="text-align: center;">Veces Comprado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datos['productos_top'] as $producto): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($producto['NombreProducto']); ?></td>
                            <td style="text-align: center;">
                                <strong style="color: #FF6347;"><?php echo $producto['CantidadTotal']; ?></strong>
                            </td>
                            <td style="text-align: center;">
                                <?php echo $producto['VecesComprado']; ?> veces
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Historial de Pedidos -->
            <div class="tarjeta">
                <h2 class="tarjeta-titulo">Historial de Pedidos (Últimos 10)</h2>
                <?php if (empty($datos['historial'])): ?>
                    <div class="texto-vacio">
                        Este cliente aún no ha realizado pedidos
                    </div>
                <?php else: ?>
                    <table class="tabla">
                        <thead>
                            <tr>
                                <th>No. Pedido</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($datos['historial'] as $pedido): ?>
                            <tr>
                                <td><strong>#<?php echo $pedido['IdPedido']; ?></strong></td>
                                <td style="font-size: 12px;">
                                    <?php echo date('d/m/Y H:i', strtotime($pedido['FechaPedido'])); ?>
                                </td>
                                <td>
                                    <strong style="color: #FF6347;">
                                        $<?php echo number_format($pedido['Total'], 2); ?>
                                    </strong>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: <?php echo $pedido['ColorEstado']; ?>;">
                                        <?php echo htmlspecialchars($pedido['NombreEstado']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="pedido_detalle.php?id=<?php echo $pedido['IdPedido']; ?>" 
                                       style="color: #FF6347; text-decoration: none; font-size: 12px;">
                                        Ver
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Columna Derecha -->
        <div>
            <!-- Tipo de Cliente -->
            <div class="tarjeta">
                <h3 class="tarjeta-titulo" style="font-size: 16px;">Tipo de Cliente</h3>
                <form id="formTipoCliente">
                    <input type="hidden" name="id_cliente" value="<?php echo $idCliente; ?>">
                    <select name="id_tipo" class="form-select">
                        <?php foreach ($datos['tipos'] as $tipo): ?>
                            <option value="<?php echo $tipo['IdTipoCliente']; ?>"
                                <?php echo ($datos['perfil']['TipoCliente'] == $tipo['NombreTipo']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($tipo['NombreTipo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-naranja">
                        Actualizar Tipo
                    </button>
                </form>
            </div>

            <!-- Segmento -->
            <div class="tarjeta">
                <h3 class="tarjeta-titulo" style="font-size: 16px;">Segmento</h3>
                <form id="formSegmento">
                    <input type="hidden" name="id_cliente" value="<?php echo $idCliente; ?>">
                    <select name="id_segmento" class="form-select">
                        <option value="">Sin segmento</option>
                        <?php foreach ($datos['segmentos'] as $segmento): ?>
                            <option value="<?php echo $segmento['IdSegmento']; ?>"
                                <?php echo ($datos['perfil']['SegmentoCliente'] == $segmento['NombreSegmento']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($segmento['NombreSegmento']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-naranja">
                        Actualizar Segmento
                    </button>
                </form>
            </div>

            <!-- Acciones Rápidas -->
            <div class="tarjeta">
                <h3 class="tarjeta-titulo" style="font-size: 16px;">Acciones Rápidas</h3>
                <a href="clientes.php" class="btn btn-gris">
                    Volver a Clientes
                </a>
                <a href="pedidos.php?cliente=<?php echo $idCliente; ?>" class="btn btn-gris">
                    Ver Todos sus Pedidos
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Cambiar tipo de cliente
document.getElementById('formTipoCliente').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('../../controllers/ClienteController.php?action=cambiarTipo', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.mensaje);
            location.reload();
        } else {
            alert('Error: ' + data.mensaje);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar el tipo de cliente');
    });
});

// Cambiar segmento
document.getElementById('formSegmento').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('../../controllers/ClienteController.php?action=cambiarSegmento', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.mensaje);
            location.reload();
        } else {
            alert('Error: ' + data.mensaje);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar el segmento');
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>