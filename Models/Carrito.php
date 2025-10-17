<?php
require_once __DIR__ . '/../config/Database.php';

class Carrito {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Agregar producto al carrito
     */
    public function agregar($idCliente, $idProducto, $cantidad) {
        try {
            // Verificar si ya existe en el carrito
            $queryCheck = "SELECT IdCarrito, Cantidad 
                          FROM Carritos 
                          WHERE IdCliente = :idCliente 
                          AND IdProducto = :idProducto";
            
            $stmtCheck = $this->conn->prepare($queryCheck);
            $stmtCheck->bindParam(':idCliente', $idCliente);
            $stmtCheck->bindParam(':idProducto', $idProducto);
            $stmtCheck->execute();
            
            $existe = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            
            if ($existe) {
                // Actualizar cantidad
                $nuevaCantidad = $existe['Cantidad'] + $cantidad;
                
                $queryUpdate = "UPDATE Carritos 
                               SET Cantidad = :cantidad 
                               WHERE IdCarrito = :idCarrito";
                
                $stmtUpdate = $this->conn->prepare($queryUpdate);
                $stmtUpdate->bindParam(':cantidad', $nuevaCantidad);
                $stmtUpdate->bindParam(':idCarrito', $existe['IdCarrito']);
                
                if ($stmtUpdate->execute()) {
                    return ['success' => true, 'mensaje' => 'Cantidad actualizada en el carrito'];
                }
            } else {
                // Insertar nuevo item
                $query = "INSERT INTO Carritos (IdCliente, IdProducto, Cantidad, FechaAgregado)
                         VALUES (:idCliente, :idProducto, :cantidad, GETDATE())";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':idCliente', $idCliente);
                $stmt->bindParam(':idProducto', $idProducto);
                $stmt->bindParam(':cantidad', $cantidad);
                
                if ($stmt->execute()) {
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
                        c.IdCarrito,
                        c.IdProducto,
                        c.Cantidad,
                        c.FechaAgregado,
                        p.CodigoProducto,
                        p.NombreProducto,
                        p.PrecioUnitario,
                        m.NombreMarca,
                        i.CantidadDisponible,
                        (c.Cantidad * p.PrecioUnitario) as Subtotal
                      FROM Carritos c
                      INNER JOIN Productos p ON c.IdProducto = p.IdProducto
                      INNER JOIN Marcas m ON p.IdMarca = m.IdMarca
                      INNER JOIN Inventarios i ON p.IdProducto = i.IdProducto
                      WHERE c.IdCliente = :idCliente
                      AND p.Estado = 1
                      ORDER BY c.FechaAgregado DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idCliente', $idCliente);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Actualizar cantidad de un item del carrito
     */
    public function actualizarCantidad($idCarrito, $cantidad) {
        try {
            if ($cantidad <= 0) {
                return $this->eliminar($idCarrito);
            }
            
            $query = "UPDATE Carritos 
                     SET Cantidad = :cantidad 
                     WHERE IdCarrito = :idCarrito";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':cantidad', $cantidad);
            $stmt->bindParam(':idCarrito', $idCarrito);
            
            if ($stmt->execute()) {
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
    public function eliminar($idCarrito) {
        try {
            $query = "DELETE FROM Carritos WHERE IdCarrito = :idCarrito";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idCarrito', $idCarrito);
            
            if ($stmt->execute()) {
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
            $query = "DELETE FROM Carritos WHERE IdCliente = :idCliente";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idCliente', $idCliente);
            
            if ($stmt->execute()) {
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
                        COUNT(*) as TotalProductos,
                        SUM(c.Cantidad) as TotalUnidades,
                        SUM(c.Cantidad * p.PrecioUnitario) as Subtotal
                      FROM Carritos c
                      INNER JOIN Productos p ON c.IdProducto = p.IdProducto
                      WHERE c.IdCliente = :idCliente
                      AND p.Estado = 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idCliente', $idCliente);
            $stmt->execute();
            
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
            $query = "SELECT COUNT(*) as total FROM Carritos WHERE IdCliente = :idCliente";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idCliente', $idCliente);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] ?? 0;
            
        } catch (PDOException $e) {
            return 0;
        }
    }
}
?>