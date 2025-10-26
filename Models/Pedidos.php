<?php
require_once __DIR__ . '/../config/Database.php';

class Pedido {
    private $conn; 
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    /**
     * Crear pedido desde carrito usando procedimiento almacenado
     */
    public function crearDesdeCarrito($idCliente, $tipoEnvio, $direccion, $ciudad, $codigoPostal, $referencia = null) {
        try {
            // Llamar al procedimiento almacenado
            $query = "EXEC sp_CrearPedidoDesdeCarrito 
                      @IdCliente = :idCliente,
                      @TipoEnvio = :tipoEnvio,
                      @DireccionEnvio = :direccion,
                      @CiudadEnvio = :ciudad,
                      @CodigoPostalEnvio = :codigoPostal,
                      @ReferenciasAdicionales = :referencia";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idCliente', $idCliente);
            $stmt->bindParam(':tipoEnvio', $tipoEnvio);
            $stmt->bindParam(':direccion', $direccion);
            $stmt->bindParam(':ciudad', $ciudad);
            $stmt->bindParam(':codigoPostal', $codigoPostal);
            $stmt->bindParam(':referencia', $referencia);
            if ($stmt->execute()) {
                // Obtener el último pedido creado
                $queryUltimo = "SELECT TOP 1 IdPedido, NumeroPedido 
                               FROM Pedidos 
                               WHERE IdCliente = :idCliente 
                               ORDER BY FechaPedido DESC";
                $stmtUltimo = $this->conn->prepare($queryUltimo);
                $stmtUltimo->bindParam(':idCliente', $idCliente);
                $stmtUltimo->execute();
                $pedido = $stmtUltimo->fetch(PDO::FETCH_ASSOC);
                return [
                    'success' => true,
                    'idPedido' => $pedido['IdPedido'],
                    'numeroPedido' => $pedido['NumeroPedido'],
                    'mensaje' => 'Pedido creado exitosamente'
                ];
            } 
            return ['error' => 'Error al crear el pedido'];
        } catch (PDOException $e) {
            return ['error' => 'Error: ' . $e->getMessage()];
        }
    }
    /**
     * Obtener pedidos del cliente
     */
    public function obtenerPorCliente($idCliente, $limite = 20) {
        try {
            $query = "SELECT TOP :limite
                        p.IdPedido,
                        p.NumeroPedido,
                        p.FechaPedido,
                        p.EstadoPedido,
                        p.Total,
                        p.MetodoPago,
                        COUNT(dp.IdDetallePedido) as TotalProductos,
                        e.EstadoEnvio,
                        e.FechaEstimadaEntrega
                      FROM Pedidos p
                      LEFT JOIN DetallesPedido dp ON p.IdPedido = dp.IdPedido
                      LEFT JOIN Envios e ON p.IdPedido = e.IdPedido
                      WHERE p.IdCliente = :idCliente
                      GROUP BY p.IdPedido, p.NumeroPedido, p.FechaPedido, 
                               p.EstadoPedido, p.Total, p.MetodoPago,
                               e.EstadoEnvio, e.FechaEstimadaEntrega
                      ORDER BY p.FechaPedido DESC";  
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idCliente', $idCliente);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();  
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    /**
     * Obtener detalle de un pedido
     */
    public function obtenerDetalle($idPedido, $idCliente = null) {
        try {
            // Información del pedido
            $query = "SELECT 
                        p.*,
                        c.NombreCompleto as NombreCliente,
                        c.Email as EmailCliente,
                        c.Telefono as TelefonoCliente,
                        e.IdEnvio,
                        e.EstadoEnvio,
                        e.FechaEstimadaEntrega,
                        e.FechaEntrega,
                        e.NumeroGuia
                      FROM Pedidos p
                      INNER JOIN Clientes c ON p.IdCliente = c.IdCliente
                      LEFT JOIN Envios e ON p.IdPedido = e.IdPedido
                      WHERE p.IdPedido = :idPedido";
            if ($idCliente) {
                $query .= " AND p.IdCliente = :idCliente";
            }
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idPedido', $idPedido);
            if ($idCliente) {
                $stmt->bindParam(':idCliente', $idCliente);
            }
            $stmt->execute();  
            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$pedido) {
                return null;
            }
            // Obtener detalles de productos
            $queryDetalles = "SELECT 
                                dp.*,
                                p.NombreProducto,
                                p.CodigoProducto,
                                m.NombreMarca
                              FROM DetallesPedido dp
                              INNER JOIN Productos p ON dp.IdProducto = p.IdProducto
                              INNER JOIN Marcas m ON p.IdMarca = m.IdMarca
                              WHERE dp.IdPedido = :idPedido";
            $stmtDetalles = $this->conn->prepare($queryDetalles);
            $stmtDetalles->bindParam(':idPedido', $idPedido);
            $stmtDetalles->execute();
            $pedido['detalles'] = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);
            return $pedido;
        } catch (PDOException $e) {
            return null;
        }
    }
    /**
     * Obtener todos los pedidos (Admin)
     */
    public function obtenerTodos($estado = null, $fechaDesde = null, $fechaHasta = null, $pagina = 1, $limite = 20) {
        try {
            $offset = ($pagina - 1) * $limite;
            $query = "SELECT 
                        p.IdPedido,
                        p.NumeroPedido,
                        p.FechaPedido,
                        p.EstadoPedido,
                        p.Total,
                        p.MetodoPago,
                        c.NombreCompleto as NombreCliente,
                        c.Email as EmailCliente,
                        COUNT(dp.IdDetallePedido) as TotalProductos,
                        e.EstadoEnvio
                      FROM Pedidos p
                      INNER JOIN Clientes c ON p.IdCliente = c.IdCliente
                      LEFT JOIN DetallesPedido dp ON p.IdPedido = dp.IdPedido
                      LEFT JOIN Envios e ON p.IdPedido = e.IdPedido
                      WHERE 1=1";
            $params = [];
            if ($estado) {
                $query .= " AND p.EstadoPedido = :estado";
                $params[':estado'] = $estado;
            } 
            if ($fechaDesde) {
                $query .= " AND p.FechaPedido >= :fechaDesde";
                $params[':fechaDesde'] = $fechaDesde;
            }  
            if ($fechaHasta) {
                $query .= " AND p.FechaPedido <= :fechaHasta";
                $params[':fechaHasta'] = $fechaHasta;
            }
            $query .= " GROUP BY p.IdPedido, p.NumeroPedido, p.FechaPedido, 
                                 p.EstadoPedido, p.Total, p.MetodoPago,
                                 c.NombreCompleto, c.Email, e.EstadoEnvio
                        ORDER BY p.FechaPedido DESC
                        OFFSET :offset ROWS
                        FETCH NEXT :limite ROWS ONLY";
            $params[':offset'] = $offset;
            $params[':limite'] = $limite;
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                if ($key === ':offset' || $key === ':limite') {
                    $stmt->bindValue($key, $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($key, $value);
                }
            }
            $stmt->execute();  
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    /**
     * Cambiar estado del pedido
     */
    public function cambiarEstado($idPedido, $nuevoEstado) {
        try {
            $query = "UPDATE Pedidos 
                     SET EstadoPedido = :nuevoEstado 
                     WHERE IdPedido = :idPedido";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nuevoEstado', $nuevoEstado);
            $stmt->bindParam(':idPedido', $idPedido);
            if ($stmt->execute()) {
                return ['success' => true, 'mensaje' => 'Estado actualizado'];
            }  
            return ['error' => 'Error al actualizar estado']; 
        } catch (PDOException $e) {
            return ['error' => 'Error: ' . $e->getMessage()];
        }
    } 
    /**
     * Cancelar pedido
     */
    public function cancelar($idPedido, $idCliente = null) {
        try {
            // Verificar que el pedido esté en estado que permita cancelación
            $queryCheck = "SELECT EstadoPedido FROM Pedidos WHERE IdPedido = :idPedido";
            
            if ($idCliente) {
                $queryCheck .= " AND IdCliente = :idCliente";
            }
            $stmtCheck = $this->conn->prepare($queryCheck);
            $stmtCheck->bindParam(':idPedido', $idPedido);
            if ($idCliente) {
                $stmtCheck->bindParam(':idCliente', $idCliente);
            }
            $stmtCheck->execute();
            $pedido = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            if (!$pedido) {
                return ['error' => 'Pedido no encontrado'];
            }
            if (!in_array($pedido['EstadoPedido'], ['Pendiente', 'En Proceso'])) {
                return ['error' => 'El pedido no puede ser cancelado en su estado actual'];
            }        
            // Cancelar pedido
            $query = "UPDATE Pedidos 
                     SET EstadoPedido = 'Cancelado' 
                     WHERE IdPedido = :idPedido";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idPedido', $idPedido);
            
            if ($stmt->execute()) {
                return ['success' => true, 'mensaje' => 'Pedido cancelado exitosamente'];
            }
            return ['error' => 'Error al cancelar pedido'];
        } catch (PDOException $e) {
            return ['error' => 'Error: ' . $e->getMessage()];
        }
    }
}
?>