<?php
require_once __DIR__ . '/../config/Database.php';

class Carrito {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Obtener o crear carrito activo del cliente
     */
    private function obtenerCarritoActivo($idCliente) {
        try {
            // Buscar carrito activo
            $query = "SELECT IdCarrito FROM Carrito 
                     WHERE IdCliente = ? AND Estado = 'ACTIVO'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$idCliente]);
            $carrito = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($carrito) {
                return $carrito['IdCarrito'];
            }
            
            // Crear nuevo carrito
            $queryInsert = "INSERT INTO Carrito (IdCliente, FechaCreacion, Estado) 
                           VALUES (?, GETDATE(), 'ACTIVO')";
            $stmtInsert = $this->conn->prepare($queryInsert);
            $stmtInsert->execute([$idCliente]);
            
            return $this->conn->lastInsertId();
            
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Agregar producto al carrito
     */
    public function agregar($idCliente, $idProducto, $cantidad) {
        try {
            $idCarrito = $this->obtenerCarritoActivo($idCliente);
            
            if (!$idCarrito) {
                return ['error' => 'No se pudo crear el carrito'];
            }
            
            // Obtener precio actual del producto
            $queryPrecio = "SELECT PrecioUnitario FROM Productos WHERE IdProducto = ?";
            $stmtPrecio = $this->conn->prepare($queryPrecio);
            $stmtPrecio->execute([$idProducto]);
            $producto = $stmtPrecio->fetch(PDO::FETCH_ASSOC);
            
            if (!$producto) {
                return ['error' => 'Producto no encontrado'];
            }
            
            // Verificar si ya existe en el detalle
            $queryCheck = "SELECT IdDetalleCarrito, Cantidad 
                          FROM Detalle_Carrito 
                          WHERE IdCarrito = ? AND IdProducto = ?";
            $stmtCheck = $this->conn->prepare($queryCheck);
            $stmtCheck->execute([$idCarrito, $idProducto]);
            $existe = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            
            if ($existe) {
                // Actualizar cantidad
                $nuevaCantidad = $existe['Cantidad'] + $cantidad;
                $queryUpdate = "UPDATE Detalle_Carrito 
                               SET Cantidad = ? 
                               WHERE IdDetalleCarrito = ?";
                $stmtUpdate = $this->conn->prepare($queryUpdate);
                
                if ($stmtUpdate->execute([$nuevaCantidad, $existe['IdDetalleCarrito']])) {
                    return ['success' => true, 'mensaje' => 'Cantidad actualizada en el carrito'];
                }
            } else {
                // Insertar nuevo item
                $query = "INSERT INTO Detalle_Carrito 
                         (IdCarrito, IdProducto, Cantidad, PrecioUnitarioSnapshot)
                         VALUES (?, ?, ?, ?)";
                $stmt = $this->conn->prepare($query);
                
                if ($stmt->execute([$idCarrito, $idProducto, $cantidad, $producto['PrecioUnitario']])) {
                    return ['success' => true, 'mensaje' => 'Producto agregado al carrito'];
                }
            }
            
            return ['error' => 'Error al agregar al carrito'];
            
        } catch (PDOException $e) {
            return ['error' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener carrito del cliente
     */
    public function obtenerPorCliente($idCliente) {
        try {
            $query = "SELECT 
                        dc.IdDetalleCarrito as IdCarrito,
                        dc.IdProducto,
                        dc.Cantidad,
                        dc.PrecioUnitarioSnapshot as PrecioUnitario,
                        p.CodigoProducto,
                        p.NombreProducto,
                        p.ImagenPrincipal as ImagenProducto,
                        m.NombreMarca,
                        i.CantidadDisponible,
                        (dc.Cantidad * dc.PrecioUnitarioSnapshot) as Subtotal
                      FROM Carrito c
                      INNER JOIN Detalle_Carrito dc ON c.IdCarrito = dc.IdCarrito
                      INNER JOIN Productos p ON dc.IdProducto = p.IdProducto
                      INNER JOIN Marcas m ON p.IdMarca = m.IdMarca
                      INNER JOIN Inventarios i ON p.IdProducto = i.IdProducto
                      WHERE c.IdCliente = ?
                      AND c.Estado = 'ACTIVO'
                      AND p.Estado = 1
                      ORDER BY dc.IdDetalleCarrito DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$idCliente]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Actualizar cantidad de un item del carrito
     */
    public function actualizarCantidad($idDetalleCarrito, $cantidad) {
        try {
            if ($cantidad <= 0) {
                return $this->eliminar($idDetalleCarrito);
            }
            
            $query = "UPDATE Detalle_Carrito 
                     SET Cantidad = ? 
                     WHERE IdDetalleCarrito = ?";
            
            $stmt = $this->conn->prepare($query);
            
            if ($stmt->execute([$cantidad, $idDetalleCarrito])) {
                return ['success' => true, 'mensaje' => 'Cantidad actualizada'];
            }
            
            return ['error' => 'Error al actualizar cantidad'];
            
        } catch (PDOException $e) {
            return ['error' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Eliminar producto del carrito
     */
    public function eliminar($idDetalleCarrito) {
        try {
            $query = "DELETE FROM Detalle_Carrito WHERE IdDetalleCarrito = ?";
            
            $stmt = $this->conn->prepare($query);
            
            if ($stmt->execute([$idDetalleCarrito])) {
                return ['success' => true, 'mensaje' => 'Producto eliminado del carrito'];
            }
            
            return ['error' => 'Error al eliminar producto'];
            
        } catch (PDOException $e) {
            return ['error' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Vaciar carrito completo
     */
    public function vaciar($idCliente) {
        try {
            // Obtener IdCarrito
            $queryCarrito = "SELECT IdCarrito FROM Carrito 
                            WHERE IdCliente = ? AND Estado = 'ACTIVO'";
            $stmtCarrito = $this->conn->prepare($queryCarrito);
            $stmtCarrito->execute([$idCliente]);
            $carrito = $stmtCarrito->fetch(PDO::FETCH_ASSOC);
            
            if (!$carrito) {
                return ['success' => true, 'mensaje' => 'Carrito ya está vacío'];
            }
            
            // Eliminar todos los detalles
            $query = "DELETE FROM Detalle_Carrito WHERE IdCarrito = ?";
            $stmt = $this->conn->prepare($query);
            
            if ($stmt->execute([$carrito['IdCarrito']])) {
                return ['success' => true, 'mensaje' => 'Carrito vaciado'];
            }
            
            return ['error' => 'Error al vaciar carrito'];
            
        } catch (PDOException $e) {
            return ['error' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener totales del carrito
     */
    public function obtenerTotales($idCliente) {
        try {
            $query = "SELECT 
                        COUNT(dc.IdDetalleCarrito) as TotalProductos,
                        SUM(dc.Cantidad) as TotalUnidades,
                        SUM(dc.Cantidad * dc.PrecioUnitarioSnapshot) as Subtotal
                      FROM Carrito c
                      INNER JOIN Detalle_Carrito dc ON c.IdCarrito = dc.IdCarrito
                      INNER JOIN Productos p ON dc.IdProducto = p.IdProducto
                      WHERE c.IdCliente = ?
                      AND c.Estado = 'ACTIVO'
                      AND p.Estado = 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$idCliente]);
            
            $totales = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Calcular IVA y total
            $subtotal = $totales['Subtotal'] ?? 0;
            $iva = $subtotal * 0.16;
            $total = $subtotal + $iva;
            
            return [
                'totalProductos' => $totales['TotalProductos'] ?? 0,
                'totalUnidades' => $totales['TotalUnidades'] ?? 0,
                'subtotal' => $subtotal,
                'iva' => $iva,
                'total' => $total
            ];
            
        } catch (PDOException $e) {
            return [
                'totalProductos' => 0,
                'totalUnidades' => 0,
                'subtotal' => 0,
                'iva' => 0,
                'total' => 0
            ];
        }
    }
    
    /**
     * Contar items en el carrito
     */
    public function contarItems($idCliente) {
        try {
            $query = "SELECT COUNT(dc.IdDetalleCarrito) as total 
                     FROM Carrito c
                     INNER JOIN Detalle_Carrito dc ON c.IdCarrito = dc.IdCarrito
                     WHERE c.IdCliente = ? AND c.Estado = 'ACTIVO'";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$idCliente]);
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] ?? 0;
            
        } catch (PDOException $e) {
            return 0;
        }
    }
}
?>