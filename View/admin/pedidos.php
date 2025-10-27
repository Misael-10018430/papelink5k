<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/Auth.php';
require_once __DIR__ . '/../../controllers/PedidoController.php';

// VERIFICAR PERMISOS PARA PEDIDOS
Auth::requiereAlgunaFuncionalidad(['PEDIDOS_VER', 'PEDIDOS_GESTIONAR']);

 $paginaActual = basename($_SERVER['PHP_SELF'], '.php');
 $pedidoController = new PedidoController();
 $pedidos = $pedidoController->listar();

 $titulo = "Gestión de Pedidos";
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
    @media (max-width: 1024px) {
        .grid-4 { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 600px) {
        .grid-4 { grid-template-columns: 1fr; }
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
    .form-group input[type="date"],
    .form-group select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--color-borde);
        border-radius: var(--border-radius);
        font-size: 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
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
        border-bottom: 2px solid var(--color-borde);
    }
    .tabla td {
        padding: 1rem;
        vertical-align: middle;
        border-top: 1px solid var(--color-borde);
    }
    .tabla tbody tr:hover { background-color: rgba(0,0,0,.02); }
    .tabla .texto-peligro { color: var(--color-peligro); }

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
<h1 class="titulo-pagina">Gestión de Pedidos</h1>

<!-- Mensajes -->
<?php if (isset($_SESSION['exito'])): ?>
    <div class="mensaje-exito">
        <?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="mensaje-error">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!-- Estadísticas Rápidas -->
<div class="grid grid-4" style="margin-bottom: 30px;">
    <div class="tarjeta-metrica">
        <h3><?php echo count($pedidos); ?></h3>
        <p>Total Pedidos</p>
    </div>
    <div class="tarjeta-metrica">
        <h3><?php echo count(array_filter($pedidos, fn($p) => $p['EstadoPedido'] === 'Pendiente')); ?></h3>
        <p>Pendientes</p>
    </div>
    <div class="tarjeta-metrica">
        <h3><?php echo count(array_filter($pedidos, fn($p) => $p['EstadoPedido'] === 'Completado')); ?></h3>
        <p>Completados</p>
    </div>
    <div class="tarjeta-metrica">
        <h3>$<?php echo number_format(array_sum(array_column($pedidos, 'Total')), 2); ?></h3>
        <p>Total Vendido</p>
    </div>
</div>

<!-- Filtros -->
<div class="filtros">
    <h3 style="margin: 0 0 15px 0; font-size: 1rem;">Filtros de Búsqueda</h3>
    <form method="GET" action="<?php echo BASE_URL; ?>view/admin/pedidos.php">
        <div class="grid grid-4">
            <div class="form-group" style="margin-bottom: 0;">
                <label>Estado:</label>
                <select name="estado">
                    <option value="">Todos los estados</option>
                    <option value="Pendiente" <?php echo ($_GET['estado'] ?? '') === 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="En Proceso" <?php echo ($_GET['estado'] ?? '') === 'En Proceso' ? 'selected' : ''; ?>>En Proceso</option>
                    <option value="Completado" <?php echo ($_GET['estado'] ?? '') === 'Completado' ? 'selected' : ''; ?>>Completado</option>
                    <option value="Cancelado" <?php echo ($_GET['estado'] ?? '') === 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>Fecha Desde:</label>
                <input type="date" name="fecha_desde" value="<?php echo $_GET['fecha_desde'] ?? ''; ?>">
            </div>           
            <div class="form-group" style="margin-bottom: 0;">
                <label>Fecha Hasta:</label>
                <input type="date" name="fecha_hasta" value="<?php echo $_GET['fecha_hasta'] ?? ''; ?>">
            </div>   
            <div class="form-group" style="margin-bottom: 0;">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primario" style="width: 100%;">Filtrar</button>
            </div>
        </div>
    </form>
</div>

<!-- Tabla de Pedidos -->
<?php if (empty($pedidos)): ?>
    <div class="tarjeta" style="text-align: center; padding: 60px;">
        <h2 style="color: var(--color-texto-claro); font-weight: normal;">No hay pedidos para mostrar</h2>
        <p style="color: var(--color-texto-claro); margin-top: 10px;">Intenta ajustar los filtros de búsqueda</p>
    </div>
<?php else: ?>
    <table class="tabla">
        <thead>
            <tr>
                <th>Número</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Tipo Entrega</th>
                <th>Productos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pedidos as $pedido): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($pedido['NumeroPedido']); ?></strong></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($pedido['FechaPedido'])); ?></td>
                    <td>
                        <?php echo htmlspecialchars($pedido['NombreCliente']); ?><br>
                        <small style="color: var(--color-texto-claro);"><?php echo htmlspecialchars($pedido['Telefono'] ?? 'N/A'); ?></small>
                    </td>
                    <td><strong class="texto-peligro">$<?php echo number_format($pedido['Total'], 2); ?></strong></td>
                    <td>
                        <?php
                        // Mapeo de clases de badges a las nuevas clases profesionales
                        $badgeClass = '';
                        switch ($pedido['EstadoPedido']) {
                            case 'Pendiente': $badgeClass = 'badge-advertencia'; break;
                            case 'En Proceso': $badgeClass = 'badge-info'; break;
                            case 'Completado': $badgeClass = 'badge-exito'; break;
                            case 'Cancelado': $badgeClass = 'badge-peligro'; break;
                        }
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>">
                            <?php echo htmlspecialchars($pedido['EstadoPedido']); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($pedido['TipoEntrega']); ?></td>
                    <td><?php echo $pedido['TotalProductos']; ?> items</td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>view/admin/pedido_detalle.php?id=<?php echo $pedido['IdPedido']; ?>" class="btn btn-blanco">
                            Ver Detalle
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>