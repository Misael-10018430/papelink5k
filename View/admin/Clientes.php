<?php
/**
 * Vista: Gestión de Clientes
 * Listado y gestión de clientes del sistema
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/Auth.php';

// ✅ VERIFICAR PERMISOS PARA CLIENTES
Auth::requiereAlgunaFuncionalidad(['CLIENTES_VER', 'CLIENTES_EDITAR']);

 $paginaActual = basename($_SERVER['PHP_SELF'], '.php');
require_once __DIR__ . '/../../controllers/ClienteController.php';

 $clienteController = new ClienteController();

// ✅ USAR EL MÉTODO LISTAR QUE YA EXISTE
 $datos = $clienteController->listar();
 $clientes = $datos['clientes'];
 $tipos = $datos['tipos'];
 $segmentos = $datos['segmentos'];
 $paginacion = $datos['paginacion'];

 $titulo = "Gestión de Clientes";
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
        font-size: 0.875rem;
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
    .form-group input[type="text"], .form-group select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--color-borde);
        border-radius: var(--border-radius);
        font-size: 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-group input[type="text"]:focus, .form-group select:focus {
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
    .btn-primario { /* Antes btn-naranja */
        background-color: var(--color-primario);
        color: var(--color-blanco);
        border-color: var(--color-primario);
    }
    .btn-primario:hover {
        background-color: var(--color-primario-hover);
        border-color: var(--color-primario-hover);
    }
    .btn-secundario { /* Antes btn-azul */
        background-color: var(--color-secundario);
        color: var(--color-blanco);
        border-color: var(--color-secundario);
    }
    .btn-secundario:hover {
        background-color: #5a6268;
        border-color: #545b62;
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
    .badge-exito { background-color: #d4edda; color: #155724; } /* Antes badge-verde */
    .badge-peligro { background-color: #f8d7da; color: #721c24; } /* Antes badge-rojo */
    .badge-info { background-color: #d1ecf1; color: #0c5460; } /* Antes badge-azul */
    .badge-secundario { background-color: #e2e3e5; color: #383d41; } /* Antes badge-gris */

    /* ===================================
       COMPONENTES: ACCIONES
       =================================== */
    .acciones {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .acciones .btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.8rem;
    }
</style>

<!-- CONTENIDO DE LA PÁGINA -->
<h1 class="titulo-pagina">Gestión de Clientes</h1>

<!-- MENSAJES -->
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

<!-- ESTADÍSTICAS -->
<div class="grid grid-4">
    <div class="tarjeta-metrica">
        <h3><?php echo $paginacion['total']; ?></h3>
        <p>Total Clientes</p>
    </div>
    <div class="tarjeta-metrica">
        <h3><?php echo count(array_filter($clientes, fn($c) => $c['Estado'] == 1)); ?></h3>
        <p>Activos</p>
    </div>
    <div class="tarjeta-metrica">
        <h3><?php echo count(array_filter($clientes, fn($c) => ($c['IdTipoCliente'] ?? 0) == 2)); ?></h3>
        <p>Frecuentes</p>
    </div>
    <div class="tarjeta-metrica">
        <h3><?php echo count(array_filter($clientes, fn($c) => ($c['CanalCliente'] ?? '') == 'DIGITAL')); ?></h3>
        <p>Canal Digital</p>
    </div>
</div>

<!-- FILTROS -->
<div class="filtros">
    <h3 style="margin: 0 0 15px 0; font-size: 1rem;">Filtros de Búsqueda</h3>
    <form method="GET" action="<?php echo BASE_URL; ?>view/admin/clientes.php">
        <div class="grid grid-4">
            <div class="form-group" style="margin-bottom: 0;">
                <label>Buscar:</label>
                <input type="text" name="busqueda" placeholder="Nombre, email o teléfono..." 
                       value="<?php echo htmlspecialchars($_GET['busqueda'] ?? ''); ?>">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label>Tipo Cliente:</label>
                <select name="tipo">
                    <option value="">Todos</option>
                    <?php foreach ($tipos as $tipo): ?>
                        <option value="<?php echo $tipo['IdTipoCliente']; ?>" 
                                <?php echo ($_GET['tipo'] ?? '') == $tipo['IdTipoCliente'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tipo['NombreTipoCliente']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label>Estado:</label>
                <select name="estado">
                    <option value="">Todos</option>
                    <option value="1" <?php echo ($_GET['estado'] ?? '') === '1' ? 'selected' : ''; ?>>Activos</option>
                    <option value="0" <?php echo ($_GET['estado'] ?? '') === '0' ? 'selected' : ''; ?>>Inactivos</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primario" style="width: 100%;">Filtrar</button>
            </div>
        </div>
    </form>
</div>

<!-- TABLA DE CLIENTES -->
<?php if (empty($clientes)): ?>
    <div class="tarjeta" style="text-align: center; padding: 60px;">
        <h2 style="color: var(--color-texto-claro); font-weight: normal;">No hay clientes registrados</h2>
        <p style="color: var(--color-texto-claro); margin-top: 10px;">Los clientes aparecerán aquí cuando se registren en el sistema</p>
    </div>
<?php else: ?>
    <table class="tabla">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Contacto</th>
                <th>Tipo</th>
                <th>Canal</th>
                <th>Pedidos</th>
                <th>Total Gastado</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?php echo $cliente['IdCliente']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($cliente['NombreCliente']); ?></strong><br>
                        <small style="color: var(--color-texto-claro);">
                            Registro: <?php echo date('d/m/Y', strtotime($cliente['FechaRegistro'])); ?>
                        </small>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($cliente['Email']); ?><br>
                        <?php echo htmlspecialchars($cliente['Telefono'] ?? 'N/A'); ?>
                    </td>
                    <td>
                        <span class="badge <?php echo ($cliente['IdTipoCliente'] ?? 0) == 2 ? 'badge-info' : 'badge-secundario'; ?>">
                            <?php echo htmlspecialchars($cliente['NombreTipoCliente'] ?? 'N/A'); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($cliente['CanalCliente'] ?? 'N/A'); ?></td>
                    <td>
                        <strong><?php echo $cliente['TotalPedidos'] ?? 0; ?></strong> pedidos
                    </td>
                    <td>
                        <strong style="color: var(--color-exito);">$<?php echo number_format($cliente['TotalGastado'] ?? 0, 2); ?></strong>
                    </td>
                    <td>
                        <?php if ($cliente['Estado'] == 1): ?>
                            <span class="badge badge-exito">Activo</span>
                        <?php else: ?>
                            <span class="badge badge-peligro">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="acciones">
                            <a href="<?php echo BASE_URL; ?>view/admin/cliente_detalle.php?id=<?php echo $cliente['IdCliente']; ?>" 
                               class="btn btn-blanco">
                                Ver
                            </a>
                            
                            <?php if (Auth::esAdministrador() || Auth::tieneFuncionalidad('CLIENTES_EDITAR')): ?>
                            <a href="<?php echo BASE_URL; ?>view/admin/cliente_editar.php?id=<?php echo $cliente['IdCliente']; ?>" 
                               class="btn btn-secundario">
                                Editar
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- PAGINACIÓN -->
    <?php if ($paginacion['total_paginas'] > 1): ?>
        <div style="margin-top: 20px; display: flex; justify-content: center; gap: 5px;">
            <?php for ($i = 1; $i <= $paginacion['total_paginas']; $i++): ?>
                <a href="<?php echo BASE_URL; ?>view/admin/clientes.php?pagina=<?php echo $i; ?><?php echo isset($_GET['busqueda']) ? '&busqueda=' . urlencode($_GET['busqueda']) : ''; ?><?php echo isset($_GET['tipo']) ? '&tipo=' . $_GET['tipo'] : ''; ?><?php echo isset($_GET['estado']) ? '&estado=' . $_GET['estado'] : ''; ?>" 
                   class="btn <?php echo $paginacion['pagina_actual'] == $i ? 'btn-primario' : 'btn-blanco'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>