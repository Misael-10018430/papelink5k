<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/Auth.php';
Auth::requiereFuncionalidad('PEDIDOS_VER');
 $paginaActual = basename($_SERVER['PHP_SELF'], '.php');
require_once __DIR__ . '/../../controllers/EnvioController.php';
 $envioController = new EnvioController();
 $envios = $envioController->listar();
 $titulo = "Gestión de Envíos";
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

    /* ============================================
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
    .btn-blanco {
        background-color: var(--color-blanco);
        color: var(--color-texto);
        border-color: var(--color-borde);
    }
    .btn-blanco:hover {
        background-color: #e2e6ea;
        border-color: #dae0e5;
    }
</style>

<!-- CONTENIDO DE LA PÁGINA -->
<h1 class="titulo-pagina">Gestión de Envíos</h1>

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

<!-- Estadísticas -->
<div class="grid grid-4" style="margin-bottom: 30px;">
    <div class="tarjeta-metrica">
        <h3><?php echo count($envios); ?></h3>
        <p>Total Envíos</p>
    </div>
    <div class="tarjeta-metrica">
        <h3><?php echo count(array_filter($envios, fn($e) => $e['EstadoEnvio'] === 'Pendiente')); ?></h3>
        <p>Pendientes</p>
    </div>
    <div class="tarjeta-metrica">
        <h3><?php echo count(array_filter($envios, fn($e) => $e['EstadoEnvio'] === 'En Tránsito')); ?></h3>
        <p>En Tránsito</p>
    </div>
    <div class="tarjeta-metrica">
        <h3><?php echo count(array_filter($envios, fn($e) => isset($e['DiasParaEntrega']) && $e['DiasParaEntrega'] <= 2)); ?></h3>
        <p>Urgentes (≤2 días)</p>
    </div>
</div>

<!-- Tabla de Envíos -->
<?php if (empty($envios)): ?>
    <div class="tarjeta" style="text-align: center; padding: 60px;">
        <h2 style="color: var(--color-texto-claro); font-weight: normal;">No hay envíos pendientes</h2>
        <p style="color: var(--color-texto-claro); margin-top: 10px;">Todos los envíos han sido completados o no hay pedidos con envío</p>
    </div>
<?php else: ?>
    <table class="tabla">
        <thead>
            <tr>
                <th>Pedido</th>
                <th>Cliente</th>
                <th>Dirección</th>
                <th>Fecha Estimada</th>
                <th>Días Restantes</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($envios as $envio): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($envio['NumeroPedido']); ?></strong><br>
                        <small style="color: var(--color-texto-claro);">ID: <?php echo $envio['IdEnvio']; ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($envio['NombreCliente']); ?></td>
                    <td>
                        <small style="color: var(--color-texto-claro);">
                            <?php echo htmlspecialchars(substr($envio['DireccionEnvio'], 0, 40)); ?>...
                        </small>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($envio['FechaEntregaEstimada'])); ?></td>
                    <td>
                        <?php
                        $dias = $envio['DiasParaEntrega'] ?? 0;
                        $textoUrgencia = $dias < 0 ? 'ATRASADO' : ($dias == 0 ? 'HOY' : $dias . ' días');
                        // Mapeo de clases de badges a las nuevas clases profesionales
                        $badgeUrgencia = $dias <= 2 ? 'badge-peligro' : 'badge-info';
                        ?>
                        <span class="badge <?php echo $badgeUrgencia; ?>">
                            <?php echo $textoUrgencia; ?>
                        </span>
                    </td>
                    <td>
                        <?php
                        // Mapeo de clases de badges a las nuevas clases profesionales
                        $badgeClass = '';
                        switch ($envio['EstadoEnvio']) {
                            case 'Pendiente': $badgeClass = 'badge-advertencia'; break;
                            case 'En Tránsito': $badgeClass = 'badge-info'; break;
                            case 'Entregado': $badgeClass = 'badge-exito'; break;
                            case 'Cancelado': $badgeClass = 'badge-peligro'; break;
                        }
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>">
                            <?php echo htmlspecialchars($envio['EstadoEnvio']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>view/admin/envio_detalle.php?id=<?php echo $envio['IdEnvio']; ?>" class="btn btn-blanco">
                            Gestionar
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>