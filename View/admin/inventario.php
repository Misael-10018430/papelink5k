<?php
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

<h1 class="titulo-pagina">📦 Gestión de Inventario</h1>

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
                <button type="submit" class="btn btn-naranja">Filtrar</button>
            </div>
            
            <div class="form-group">
                <label>&nbsp;</label>
                <a href="inventario.php" class="btn btn-blanco">Limpiar</a>
            </div>
        </form>
    </div>
    
    <!-- RESUMEN -->
    <div class="tarjeta" style="background-color: #f0f8ff; border-color: #2C3E50;">
        <div class="grid grid-3">
            <div>
                <h3 style="color: #2C3E50;">Total Productos:</h3>
                <p style="font-size: 24px; font-weight: bold;"><?php echo count($inventario); ?></p>
            </div>
            <div>
                <h3 style="color: #2C3E50;">Valor Total Inventario:</h3>
                <p style="font-size: 24px; font-weight: bold; color: #27ae60;">
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
                <h3 style="color: #e74c3c;">Productos con Stock Bajo:</h3>
                <p style="font-size: 24px; font-weight: bold; color: #e74c3c;">
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
    <div style="margin: 20px 0; padding: 15px; background-color: #f9f9f9; border-radius: 5px;">
        <strong>Leyenda:</strong>
        <span class="badge badge-verde" style="margin-left: 10px;">Stock Normal</span>
        <span class="badge badge-amarillo" style="margin-left: 10px;">Stock Medio</span>
        <span class="badge badge-rojo" style="margin-left: 10px;">Stock Bajo</span>
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
                    <tr style="<?php echo $item['NivelStock'] === 'BAJO' ? 'background-color: #fff3cd;' : ''; ?>">
                        <td>
                            <strong><?php echo htmlspecialchars($item['NombreProducto']); ?></strong><br>
                            <small style="color: #666;"><?php echo htmlspecialchars($item['CodigoProducto']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($item['NombreCategoria']); ?></td>
                        <td>
                            <strong style="font-size: 16px; 
                                <?php 
                                    if ($item['NivelStock'] === 'BAJO') echo 'color: #e74c3c;';
                                    elseif ($item['NivelStock'] === 'MEDIO') echo 'color: #f39c12;';
                                    else echo 'color: #27ae60;';
                                ?>">
                                <?php echo $item['CantidadDisponible']; ?>
                            </strong>
                        </td>
                        <td><?php echo $item['CantidadReservada'] ?? 0; ?></td>
                        <td><?php echo $item['CantidadEnRevision'] ?? 0; ?></td>
                        <td><?php echo $item['StockMinimo']; ?></td>
                        <td>$<?php echo number_format($item['CostoUnitario'], 2); ?></td>
                        <td>
                            <strong>$<?php echo number_format($item['ValorInventario'] ?? 0, 2); ?></strong>
                        </td>
                        <td>
                            <?php if ($item['NivelStock'] === 'BAJO'): ?>
                                <span class="badge badge-rojo">BAJO</span>
                            <?php elseif ($item['NivelStock'] === 'MEDIO'): ?>
                                <span class="badge badge-amarillo">MEDIO</span>
                            <?php else: ?>
                                <span class="badge badge-verde">NORMAL</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="acciones">
                                <a href="inventario.php?accion=ajustar&id=<?php echo $item['IdProducto']; ?>" 
                                   class="btn btn-naranja">
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
        <a href="inventario.php" class="btn btn-blanco">← Volver al inventario</a>
    </div>
    
    <?php if ($productoInfo): ?>
        <div class="tarjeta">
            <h2>Ajustar Inventario</h2>
            
            <!-- INFO DEL PRODUCTO -->
            <div style="background-color: #f0f8ff; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
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
                    <button type="submit" class="btn btn-naranja">Realizar Ajuste</button>
                    <a href="inventario.php" class="btn btn-blanco">Cancelar</a>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="mensaje-error">
            ❌ Producto no encontrado
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>