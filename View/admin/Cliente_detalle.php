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
    redirect('view/admin/clientes.php');
    exit;
}

 $controller = new ClienteController();
 $datos = $controller->verDetalle($idCliente);
 $titulo = "Detalle del Cliente - Papelink";
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
        --color-advertencia: #ffc107;    /* Amarillo estándar para advertencia */
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
    .grid-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
    }
    @media (max-width: 992px) {
        .grid-layout { grid-template-columns: 1fr; }
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
    .tarjeta-titulo {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--color-texto);
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--color-borde);
    }

    /* ===================================
       NAVEGACIÓN (BREADCRUMB)
       =================================== */
    .breadcrumb {
        background: none;
        padding: 0;
        margin-bottom: 1.25rem;
        font-size: 0.8125rem;
    }
    .breadcrumb a {
        color: var(--color-primario);
        text-decoration: none;
    }
    .breadcrumb a:hover {
        text-decoration: underline;
    }
    .breadcrumb-separador {
        color: var(--color-texto-claro);
        margin: 0 0.5rem;
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
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.25rem;
    }
    .info-campo {
        margin-bottom: 1rem;
    }
    .info-campo.ancho-completo { grid-column: 1 / -1; }
    .info-label {
        color: var(--color-texto-claro);
        font-size: 0.75rem;
        text-transform: uppercase;
        margin-bottom: 0.375rem;
        font-weight: 500;
    }
    .info-valor {
        color: var(--color-texto);
        font-size: 0.9375rem;
        font-weight: 500;
    }

    /* ===================================
       COMPONENTES: ESTADÍSTICAS
       =================================== */
    .estadisticas-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 1rem;
    }
    .estadistica-box {
        text-align: center;
        padding: 1.25rem;
        background-color: var(--color-fondo);
        border: 1px solid var(--color-borde);
        border-radius: var(--border-radius);
    }
    .estadistica-numero {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0 0 0.25rem 0;
    }
    .estadistica-numero.primario { color: var(--color-primario); }
    .estadistica-numero.exito { color: var(--color-exito); }
    .estadistica-numero.advertencia { color: var(--color-advertencia); }
    .estadistica-numero.info { color: var(--color-info); }
    .estadistica-label {
        color: var(--color-texto-claro);
        font-size: 0.6875rem;
        text-transform: uppercase;
        font-weight: 500;
    }

    /* ===================================
       COMPONENTES: TABLA
       =================================== */
    .tabla {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8125rem;
    }
    .tabla thead {
        background-color: var(--color-fondo);
    }
    .tabla th {
        padding: 0.75rem;
        text-align: left;
        color: var(--color-texto);
        font-weight: 600;
        font-size: 0.6875rem;
        text-transform: uppercase;
        border-bottom: 2px solid var(--color-borde);
    }
    .tabla td {
        padding: 0.75rem;
        border-bottom: 1px solid var(--color-borde);
        color: var(--color-texto);
    }
    .tabla tbody tr:hover {
        background-color: rgba(0,0,0,.02);
    }
    .texto-valor-destacado { color: var(--color-peligro); font-weight: 600; }

    /* ===================================
       COMPONENTES: FORMULARIOS
       =================================== */
    .form-select {
        width: 100%;
        padding: 0.625rem 0.75rem;
        border: 1px solid var(--color-borde);
        border-radius: var(--border-radius);
        font-size: 0.875rem;
        margin-bottom: 0.9375rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-select:focus {
        outline: none;
        border-color: var(--color-primario);
        box-shadow: 0 0 0 2px rgba(73, 80, 87, 0.25);
    }

    /* ===================================
       COMPONENTES: BOTONES
       =================================== */
    .btn {
        padding: 0.625rem 1.25rem;
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
        width: 100%;
    }
    .btn-primario:hover {
        background-color: var(--color-primario-hover);
    }
    .btn-secundario { /* Mapeo de btn-gris */
        background-color: var(--color-blanco);
        color: var(--color-texto);
        border: 1px solid var(--color-borde);
        width: 100%;
        margin-bottom: 0.5rem;
    }
    .btn-secundario:hover {
        background-color: var(--color-fondo);
    }
    .btn-enlace {
        color: var(--color-primario);
        text-decoration: none;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .btn-enlace:hover {
        text-decoration: underline;
    }

    /* ===================================
       COMPONENTES: BADGES Y ALERTAS
       =================================== */
    .badge {
        display: inline-block;
        padding: 0.25em 0.6em;
        border-radius: 0.25rem;
        font-size: 0.6875rem;
        font-weight: 500;
        color: var(--color-blanco);
        text-transform: uppercase;
    }
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
    .texto-vacio {
        text-align: center;
        padding: 1.875rem;
        color: var(--color-texto-claro);
        font-size: 0.875rem;
    }
</style>

<div class="contenedor">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="<?php echo BASE_URL; ?>view/admin/dashboard.php">Dashboard</a>
        <span class="breadcrumb-separador">/</span>
        <a href="<?php echo BASE_URL; ?>view/admin/clientes.php">Clientes</a>
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
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
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
                        <div class="info-valor"><?php echo htmlspecialchars($datos['perfil']['NombreCliente']); ?></div>
                    </div>
                    <div class="info-campo">
                        <div class="info-label">Email</div>
                        <div class="info-valor"><?php echo htmlspecialchars($datos['perfil']['Email']); ?></div>
                    </div>
                    <div class="info-campo">
                        <div class="info-label">Teléfono</div>
                        <div class="info-valor"><?php echo htmlspecialchars($datos['perfil']['Telefono']); ?></div>
                    </div>
                    <div class="info-campo">
                        <div class="info-label">Canal de Registro</div>
                        <div class="info-valor"><?php echo htmlspecialchars($datos['perfil']['CanalCliente']); ?></div>
                    </div>
                    <div class="info-campo ancho-completo">
                        <div class="info-label">Dirección</div>
                        <div class="info-valor"><?php echo htmlspecialchars($datos['perfil']['Direccion']); ?></div>
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
                        <div class="estadistica-numero primario"><?php echo $datos['estadisticas']['TotalPedidos']; ?></div>
                        <div class="estadistica-label">Total Pedidos</div>
                    </div>
                    <div class="estadistica-box">
                        <div class="estadistica-numero exito">$<?php echo number_format($datos['estadisticas']['TotalGastado'], 2); ?></div>
                        <div class="estadistica-label">Total Gastado</div>
                    </div>
                    <div class="estadistica-box">
                        <div class="estadistica-numero advertencia"><?php echo $datos['estadisticas']['PedidosPendientes']; ?></div>
                        <div class="estadistica-label">Pendientes</div>
                    </div>
                    <div class="estadistica-box">
                        <div class="estadistica-numero info"><?php echo $datos['estadisticas']['PedidosCompletados']; ?></div>
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
                                <strong class="texto-valor-destacado"><?php echo $producto['CantidadTotal']; ?></strong>
                            </td>
                            <td style="text-align: center;"><?php echo $producto['VecesComprado']; ?> veces</td>
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
                    <div class="texto-vacio">Este cliente aún no ha realizado pedidos</div>
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
                                <td><?php echo date('d/m/Y H:i', strtotime($pedido['FechaPedido'])); ?></td>
                                <td>
                                    <strong class="texto-valor-destacado">
                                        $<?php echo number_format($pedido['Total'], 2); ?>
                                    </strong>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: <?php echo $pedido['ColorEstado']; ?>;">
                                        <?php echo htmlspecialchars($pedido['NombreEstado']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>view/admin/pedido_detalle.php?id=<?php echo $pedido['IdPedido']; ?>" class="btn-enlace">
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
                <h3 class="tarjeta-titulo">Tipo de Cliente</h3>
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
                    <button type="submit" class="btn btn-primario">Actualizar Tipo</button>
                </form>
            </div>
            <!-- Segmento -->
            <div class="tarjeta">
                <h3 class="tarjeta-titulo">Segmento</h3>
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
                    <button type="submit" class="btn btn-primario">Actualizar Segmento</button>
                </form>
            </div>
            <!-- Acciones Rápidas -->
            <div class="tarjeta">
                <h3 class="tarjeta-titulo">Acciones Rápidas</h3>
                <a href="<?php echo BASE_URL; ?>view/admin/clientes.php" class="btn btn-secundario">Volver a Clientes</a>
                <a href="<?php echo BASE_URL; ?>view/admin/pedidos.php?cliente=<?php echo $idCliente; ?>" class="btn btn-secundario">Ver Todos sus Pedidos</a>
            </div>
        </div>
    </div>
</div>

<script>
// Cambiar tipo de cliente
document.getElementById('formTipoCliente').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this); 
    fetch('<?php echo BASE_URL; ?>controllers/ClienteController.php?action=cambiarTipo', {
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
    
    fetch('<?php echo BASE_URL; ?>controllers/ClienteController.php?action=cambiarSegmento', {
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