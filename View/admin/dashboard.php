<?php
/**
 * Vista: Dashboard Principal
 * Panel principal para empleados y administradores
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/Auth.php';
Auth::checkEmpleadoLogin();

 $paginaActual = basename($_SERVER['PHP_SELF'], '.php');
require_once __DIR__ . '/../../controllers/InventarioController.php';
require_once __DIR__ . '/../../controllers/ProductoController.php';

 $inventarioController = new InventarioController();
 $productoController = new ProductoController();

// Obtener productos con stock bajo
 $productosStockBajo = $inventarioController->verStockBajo();

// Métricas simuladas
 $pedidosHoy = 47;
 $pedidosMes = 1234;
 $ingresosHoy = 18750.00;
 $ingresosMes = 287640.00;

include __DIR__ . '/includes/header.php';
?>

<!-- ===================================
     ESTILOS CSS PROFESIONAL Y SENCILLO
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
        --color-texto: #212529;          /* Negro suave para texto principal */
        --color-fondo: #f8f9fa;          /* Fondo muy claro */
        --color-blanco: #ffffff;
        --color-borde: #dee2e6;          /* Gris claro para bordes */
        --border-radius: 4px;            /* Bordes más sutiles */
        --sombra: 0 2px 4px rgba(0,0,0,0.05); /* Sombra muy ligera */
    }

    /* Reset y estilos base para el dashboard */
    .dashboard-wrapper {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        background-color: var(--color-fondo);
        color: var(--color-texto);
        padding: 2rem;
        line-height: 1.6;
    }
    
    .dashboard-wrapper h1 {
        font-size: 1.75rem;
        font-weight: 600;
        color: var(--color-texto);
        margin: 0 0 1.5rem 0;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--color-borde);
    }
    
    .dashboard-wrapper h2 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--color-texto);
        margin: 0 0 1rem 0;
    }
    
    /* Tarjeta de bienvenida */
    .tarjeta-bienvenida {
        background-color: var(--color-blanco);
        padding: 1.5rem;
        border-radius: var(--border-radius);
        border: 1px solid var(--color-borde);
        margin-bottom: 1.5rem;
    }
    
    .tarjeta-bienvenida p {
        margin: 0.25rem 0;
        color: var(--color-texto);
    }
    
    /* Grid system */
    .grid {
        display: grid;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .grid-4 {
        grid-template-columns: repeat(4, 1fr);
    }
    
    .grid-2 {
        grid-template-columns: repeat(2, 1fr);
    }
    
    /* Tarjetas de métricas */
    .tarjeta-metrica {
        background: var(--color-blanco);
        padding: 1.5rem;
        border-radius: var(--border-radius);
        border: 1px solid var(--color-borde);
        text-align: center;
    }
    
    .tarjeta-metrica h3 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--color-primario);
        margin: 0 0 0.5rem 0;
    }
    
    .tarjeta-metrica p {
        color: var(--color-secundario);
        font-size: 0.8rem;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* Secciones con contenido */
    .seccion-contenido {
        background: var(--color-blanco);
        padding: 1.5rem;
        border-radius: var(--border-radius);
        border: 1px solid var(--color-borde);
    }
    
    /* Alertas */
    .alerta {
        padding: 1rem 1.25rem;
        border-radius: var(--border-radius);
        margin-bottom: 1rem;
        border-left: 4px solid;
    }
    
    .alerta-exito { /* Antes alerta-verde */
        background-color: #d4edda;
        border-color: var(--color-exito);
        color: #155724;
    }
    
    .alerta-advertencia { /* Antes alerta-amarilla */
        background-color: #fff3cd;
        border-color: var(--color-advertencia);
        color: #856404;
    }
    
    .alerta strong {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 1rem;
    }
    
    .alerta small {
        color: #666;
        font-size: 0.8rem;
    }
    
    /* Tabla */
    .tabla {
        width: 100%;
        border-collapse: collapse;
    }
    
    .tabla thead {
        background-color: var(--color-fondo);
    }
    
    .tabla th {
        padding: 0.75rem 1rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.8rem;
        color: var(--color-texto);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--color-borde);
    }
    
    .tabla td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--color-borde);
        font-size: 0.9rem;
    }
    
    .tabla tbody tr:hover {
        background-color: rgba(0,0,0,.02);
    }
    
    .tabla td strong.texto-peligro { /* Estilo para el monto total */
        color: var(--color-peligro);
    }
    
    /* Botones */
    .btn {
        display: inline-block;
        padding: 0.75rem 1rem;
        border-radius: var(--border-radius);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
        border: 1px solid transparent;
        cursor: pointer;
        font-size: 0.875rem;
        text-align: center;
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
    
    .btn-exito { /* Antes btn-verde */
        background-color: var(--color-exito);
        color: var(--color-blanco);
        border-color: var(--color-exito);
    }
    
    .btn-exito:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
    
    .acceso-directo {
        padding: 1rem;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 60px;
        font-size: 0.9rem;
    }
    
    /* Responsive */
    @media (max-width: 1200px) {
        .grid-4 {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .grid-4,
        .grid-2 {
            grid-template-columns: 1fr;
        }
        .dashboard-wrapper {
            padding: 1rem;
        }
    }
</style>

<!-- WRAPPER DEL DASHBOARD -->
<div class="dashboard-wrapper">

    <h1>Dashboard Principal</h1>

    <!-- TARJETA DE BIENVENIDA -->
    <div class="tarjeta-bienvenida">
        <p><strong>Bienvenido:</strong> <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email_usuario']); ?></p>
        <p><strong>Rol:</strong> <?php echo htmlspecialchars($_SESSION['rol_usuario']); ?></p>
    </div>

    <!-- MÉTRICAS EN TIEMPO REAL -->
    <h2>Métricas en Tiempo Real</h2>
    <div class="grid grid-4">
        <div class="tarjeta-metrica">
            <h3><?php echo $pedidosHoy; ?></h3>
            <p>Pedidos Hoy</p>
        </div>
        
        <div class="tarjeta-metrica">
            <h3><?php echo number_format($pedidosMes, 0, '', ','); ?></h3>
            <p>Pedidos Este Mes</p>
        </div>
        
        <div class="tarjeta-metrica">
            <h3>$<?php echo number_format($ingresosHoy, 2); ?></h3>
            <p>Ingresos Hoy</p>
        </div>
        
        <div class="tarjeta-metrica">
            <h3>$<?php echo number_format($ingresosMes, 2); ?></h3>
            <p>Ingresos Este Mes</p>
        </div>
    </div>

    <!-- SECCIÓN DE DOS COLUMNAS -->
    <div class="grid grid-2">
        
        <!-- ALERTAS DE STOCK BAJO -->
        <div>
            <h2>Alertas de Stock Bajo</h2>
            <div class="seccion-contenido">
                <?php if (empty($productosStockBajo)): ?>
                    <div class="alerta alerta-exito">
                     No hay productos con stock bajo
                    </div>
                <?php else: ?>
                    <?php foreach ($productosStockBajo as $producto): ?>
                        <div class="alerta alerta-advertencia">
                            <strong><?php echo htmlspecialchars($producto['NombreProducto']); ?></strong>
                            <small>Código: <?php echo htmlspecialchars($producto['CodigoProducto']); ?></small>
                            <div style="margin: 10px 0;">
                                Stock actual: <strong style="color: var(--color-peligro);"><?php echo $producto['CantidadDisponible']; ?> unidades</strong><br>
                                Stock mínimo: <strong style="color: var(--color-advertencia);"><?php echo $producto['StockMinimo']; ?> unidades</strong>
                            </div>
                            <?php if (Auth::esAdministrador() || Auth::tieneFuncionalidad('INVENTARIO_AJUSTAR')): ?>
                            <a href="<?php echo BASE_URL; ?>view/admin/inventario.php?accion=ajustar&id=<?php echo $producto['IdProducto']; ?>" class="btn btn-primario">
                                Reabastecer
                            </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- PEDIDOS PENDIENTES -->
        <div>
            <h2>Pedidos Pendientes</h2>
            <div class="seccion-contenido">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>N° Pedido</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>PED-001</strong></td>
                            <td>Juan Pérez</td>
                            <td><strong class="texto-peligro">$450.00</strong></td>
                            <td>
                                <a href="#" class="btn btn-exito">Procesar</a>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>PED-002</strong></td>
                            <td>María García</td>
                            <td><strong class="texto-peligro">$820.00</strong></td>
                            <td>
                                <a href="#" class="btn btn-exito">Procesar</a>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>PED-003</strong></td>
                            <td>Carlos López</td>
                            <td><strong class="texto-peligro">$1,250.00</strong></td>
                            <td>
                                <a href="#" class="btn btn-exito">Procesar</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>

    <!-- ACCESOS DIRECTOS -->
    <h2>Accesos Directos</h2>
    <div class="grid grid-4">
        <?php if (Auth::esAdministrador() || Auth::tieneFuncionalidad('PRODUCTOS_CREAR')): ?>
        <a href="<?php echo BASE_URL; ?>view/admin/productos.php?accion=nuevo" class="btn btn-primario acceso-directo">
             Nuevo Producto
        </a>
        <?php endif; ?>
        
        <?php if (Auth::esAdministrador() || Auth::tieneFuncionalidad('PEDIDOS_VER')): ?>
        <a href="<?php echo BASE_URL; ?>view/admin/pedidos.php" class="btn btn-primario acceso-directo">
            Ver Pedidos
        </a>
        <?php endif; ?>
        
        <?php if (Auth::esAdministrador() || Auth::tieneFuncionalidad('REPORTES_VER')): ?>
        <a href="<?php echo BASE_URL; ?>view/admin/reportes.php" class="btn btn-primario acceso-directo">
            Ver Reportes
        </a>
        <?php endif; ?>
        
        <?php if (Auth::esAdministrador() || Auth::tieneFuncionalidad('INVENTARIO_VER')): ?>
        <a href="<?php echo BASE_URL; ?>view/admin/inventario.php" class="btn btn-primario acceso-directo">
            Gestionar Inventario
        </a>
        <?php endif; ?>
    </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>