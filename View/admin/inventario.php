<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/Auth.php';

// ✅ VERIFICAR PERMISOS PARA INVENTARIO
Auth::requiereAlgunaFuncionalidad(['INVENTARIO_VER', 'INVENTARIO_AJUSTAR']);

 $paginaActual = basename($_SERVER['PHP_SELF'], '.php');

 $puede_ajustar = Auth::tieneFuncionalidad('INVENTARIO_AJUSTAR');


require_once __DIR__ . '/../../controllers/InventarioController.php';
require_once __DIR__ . '/../../controllers/CategoriaController.php';

 $inventarioController = new InventarioController();
 $categoriaController = new CategoriaController();

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion === 'ajustar') {
        $inventarioController->ajustar();
    } elseif ($accion === 'actualizar_costo') {
        $inventarioController->actualizarCosto();
    }
}

// Obtener inventario con filtros
 $idCategoria = $_GET['categoria'] ?? null;
 $soloStockBajo = isset($_GET['stock_bajo']) ? 1 : 0;

 $inventario = $inventarioController->verInventario();
 $categorias = $categoriaController->listarActivas();

// Variables para formulario de ajuste
 $accionForm = $_GET['accion'] ?? '';
 $idProductoAjustar = $_GET['id'] ?? null;

include 'includes/header.php';
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
    .tarjeta h2, .tarjeta h3 {
        color: var(--color-texto);
        margin-top: 0;
    }
    .tarjeta h2 { font-size: 1.25rem; margin-bottom: 1.5rem; }
    .tarjeta h3 { font-size: 1rem; margin-bottom: 0.5rem; }

    /* ===================================
       SISTEMA DE GRID
       =================================== */
    .grid {
        display: grid;
        gap: 1.5rem;
    }
    .grid-2 { grid-template-columns: repeat(2, 1fr); }
    .grid-3 { grid-template-columns: repeat(3, 1fr); }
    @media (max-width: 1024px) {
        .grid-3 { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .grid-2, .grid-3 { grid-template-columns: 1fr; }
    }

    /* ===================================
       COMPONENTES: TARJETAS
       =================================== */
    .tarjeta {
        background: var(--color-blanco);
        padding: 1.5rem;
        border-radius: var(--border-radius);
        border: 1px solid var(--color-borde);
        margin-bottom: 1.5rem;
    }
    .tarjeta-resumen { /* Tarjeta de resumen especial */
        background-color: #eef2f7;
        border-color: var(--color-primario);
    }
    .tarjeta-resumen p {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
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
       COMPONENTES: ACCIONES Y BOTONES
       =================================== */
    .acciones {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
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
        background-color: var(--color-secundario);
        color: var(--color-blanco);
    }
    .btn:hover {
        background-color: #5a6268;
        border-color: #545b62;
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
    .btn-secundario { /* Mapeo de btn-blanco */
        background-color: var(--color-blanco);
        color: var(--color-texto);
        border-color: var(--color-borde);
    }
    .btn-secundario:hover {
        background-color: #e2e6ea;
        border-color: #dae0e5;
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
    .form-group {
        margin-bottom: 1rem;
    }
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--color-texto);
    }
    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--color-borde);
        border-radius: var(--border-radius);
        font-size: 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--color-primario);
        box-shadow: 0 0 0 2px rgba(73, 80, 87, 0.25);
    }
    .form-group label input[type="checkbox"] {
        margin-right: 0.5rem;
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
    .tabla td[colspan] {
        text-align: center;
        color: var(--color-texto-claro);
    }
    .fila-stock-bajo { /* Fila resaltada para stock bajo */
        background-color: rgba(255, 193, 7, 0.1) !important;
    }

    /* ===================================
       COMPONENTES: BADGES Y TEXTO COLOREADO
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
    .badge-advertencia { background-color: #fff3cd; color: #856404; } /* Mapeo de badge-amarillo */
    .badge-peligro { background-color: #f8d7da; color: #721c24; } /* Mapeo de badge-rojo */
    
    .stock-normal { color: var(--color-exito); }
    .stock-medio { color: var(--color-advertencia); }
    .stock-bajo { color: var(--color-peligro); }
    .valor-total { color: var(--color-primario); }
    .texto-peligro { color: var(--color-peligro); }
</style>

<!-- CONTENIDO DE LA PÁGINA -->
<h1 class="titulo-pagina">Gestión de Inventario</h1>
<div class="acciones">
    <button onclick="verInventario()" class="btn">Ver Inventario</button>
    
    <?php if ($puede_ajustar): ?>
        <button onclick="ajustarInventario()" class="btn">Ajustar Cantidades</button>
    <?php endif; ?>
</div>

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

<?php if ($accionForm === '' || $accionForm === 'ver'): ?>
    <!-- VISTA DE INVENTARIO -->
    
    <!-- FILTROS -->
    <div class="filtros">
        <form method="GET" action="inventario.php" style="display: flex; gap: 15px; flex-wrap: wrap;">
            <div class="form-group">
                <label>Categoría:</label>
                <select name="categoria">
                    <option value="">Todas</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['IdCategoria']; ?>"
                                <?php echo ($idCategoria == $cat['IdCategoria']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['NombreCategoria']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>&nbsp;</label>
                <label style="display: flex; align-items: center; gap: 5px;">
                    <input type="checkbox" name="stock_bajo" <?php echo $soloStockBajo ? 'checked' : ''; ?>>
                    Solo productos con stock bajo
                </label>
            </div>
            
            <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primario">Filtrar</button>
            </div>
            
            <div class="form-group">
                <label>&nbsp;</label>
                <a href="inventario.php" class="btn btn-secundario">Limpiar</a>
            </div>
        </form>
    </div>
    
    <!-- RESUMEN -->
    <div class="tarjeta tarjeta-resumen">
        <div class="grid grid-3">
            <div>
                <h3>Total Productos:</h3>
                <p><?php echo count($inventario); ?></p>
            </div>
            <div>
                <h3>Valor Total Inventario:</h3>
                <p class="valor-total">
                    $<?php 
                        $valorTotal = 0;
                        foreach ($inventario as $item) {
                            $valorTotal += ($item['CantidadDisponible'] ?? 0) * ($item['CostoUnitario'] ?? 0);
                        }
                        echo number_format($valorTotal, 2);
                    ?>
                </p>
            </div>
            <div>
                <h3>Productos con Stock Bajo:</h3>
                <p class="texto-peligro">
                    <?php 
                        $stockBajoCount = 0;
                        foreach ($inventario as $item) {
                            if ($item['NivelStock'] === 'BAJO') {
                                $stockBajoCount++;
                            }
                        }
                        echo $stockBajoCount;
                    ?>
                </p>
            </div>
        </div>
    </div>
    
    <!-- INDICADORES -->
    <div style="margin: 20px 0; padding: 15px; background-color: var(--color-fondo); border-radius: var(--border-radius); border: 1px solid var(--color-borde);">
        <strong>Leyenda:</strong>
        <span class="badge badge-exito" style="margin-left: 10px;">Stock Normal</span>
        <span class="badge badge-advertencia" style="margin-left: 10px;">Stock Medio</span>
        <span class="badge badge-peligro" style="margin-left: 10px;">Stock Bajo</span>
    </div>
    
    <!-- TABLA DE INVENTARIO -->
    <table class="tabla">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Disponible</th>
                <th>Reservado</th>
                <th>En Revisión</th>
                <th>Stock Mínimo</th>
                <th>Costo Unit.</th>
                <th>Valor Total</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($inventario)): ?>
                <tr>
                    <td colspan="10" style="text-align: center; padding: 30px;">
                        No se encontraron productos en inventario
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($inventario as $item): ?>
                    <tr class="<?php echo $item['NivelStock'] === 'BAJO' ? 'fila-stock-bajo' : ''; ?>">
                        <td>
                            <strong><?php echo htmlspecialchars($item['NombreProducto']); ?></strong><br>
                            <small style="color: var(--color-texto-claro);"><?php echo htmlspecialchars($item['CodigoProducto']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($item['NombreCategoria']); ?></td>
                        <td>
                            <strong class="stock-<?php echo strtolower($item['NivelStock']); ?>" style="font-size: 16px;">
                                <?php echo $item['CantidadDisponible']; ?>
                            </strong>
                        </td>
                        <td><?php echo $item['CantidadReservada'] ?? 0; ?></td>
                        <td><?php echo $item['CantidadEnRevision'] ?? 0; ?></td>
                        <td><?php echo $item['StockMinimo']; ?></td>
                        <td>$<?php echo number_format($item['CostoUnitario'], 2); ?></td>
                        <td>
                            <strong class="valor-total">$<?php echo number_format($item['ValorInventario'] ?? 0, 2); ?></strong>
                        </td>
                        <td>
                            <?php if ($item['NivelStock'] === 'BAJO'): ?>
                                <span class="badge badge-peligro">BAJO</span>
                            <?php elseif ($item['NivelStock'] === 'MEDIO'): ?>
                                <span class="badge badge-advertencia">MEDIO</span>
                            <?php else: ?>
                                <span class="badge badge-exito">NORMAL</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="acciones">
                                <a href="inventario.php?accion=ajustar&id=<?php echo $item['IdProducto']; ?>" 
                                   class="btn btn-primario">
                                    Ajustar
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

<?php elseif ($accionForm === 'ajustar'): ?>
    <!-- FORMULARIO DE AJUSTE DE INVENTARIO -->
    
    <?php
    // Obtener información del producto
    $productoInfo = null;
    foreach ($inventario as $item) {
        if ($item['IdProducto'] == $idProductoAjustar) {
            $productoInfo = $item;
            break;
        }
    }
    ?>
    
    <div style="margin-bottom: 20px;">
        <a href="inventario.php" class="btn btn-secundario">← Volver al inventario</a>
    </div>
    
    <?php if ($productoInfo): ?>
        <div class="tarjeta">
            <h2>Ajustar Inventario</h2>
            
            <!-- INFO DEL PRODUCTO -->
            <div class="tarjeta-resumen" style="margin-bottom: 20px;">
                <h3><?php echo htmlspecialchars($productoInfo['NombreProducto']); ?></h3>
                <p><strong>Código:</strong> <?php echo htmlspecialchars($productoInfo['CodigoProducto']); ?></p>
                <p><strong>Stock Actual:</strong> <?php echo $productoInfo['CantidadDisponible']; ?> unidades</p>
                <p><strong>Stock Reservado:</strong> <?php echo $productoInfo['CantidadReservada'] ?? 0; ?> unidades</p>
                <p><strong>Costo Unitario:</strong> $<?php echo number_format($productoInfo['CostoUnitario'], 2); ?></p>
            </div>
            
            <form method="POST" action="inventario.php">
                <input type="hidden" name="accion" value="ajustar">
                <input type="hidden" name="id_producto" value="<?php echo $idProductoAjustar; ?>">
                
                <div class="grid grid-2">
                    <!-- TIPO DE AJUSTE -->
                    <div class="form-group">
                        <label>Tipo de Ajuste: *</label>
                        <select name="tipo_ajuste" required>
                            <option value="">Seleccione...</option>
                            <option value="ENTRADA">Entrada (Agregar stock)</option>
                            <option value="SALIDA">Salida (Restar stock)</option>
                            <option value="AJUSTE">Ajuste (Establecer cantidad exacta)</option>
                        </select>
                    </div>
                    
                    <!-- CANTIDAD -->
                    <div class="form-group">
                        <label>Cantidad: *</label>
                        <input type="number" name="cantidad" min="1" required>
                    </div>
                    
                    <!-- NUEVO COSTO (opcional) -->
                    <div class="form-group">
                        <label>Nuevo Costo Unitario (opcional):</label>
                        <input type="number" name="nuevo_costo" step="0.01" min="0" 
                               placeholder="Dejar vacío para no cambiar">
                    </div>
                </div>
                
                <!-- MOTIVO -->
                <div class="form-group">
                    <label>Motivo del Ajuste: *</label>
                    <textarea name="motivo" required placeholder="Ej: Recepción de compra, inventario físico, corrección de error, etc."></textarea>
                </div>
                
                <!-- BOTONES -->
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primario">Realizar Ajuste</button>
                    <a href="inventario.php" class="btn btn-secundario">Cancelar</a>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="mensaje-error">
            Producto no encontrado
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>