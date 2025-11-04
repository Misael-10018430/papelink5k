<?php
require_once __DIR__ . '/../config/Database.php';

class Pedido {
    public $conn; 
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    








/**
 * Crear pedido desde carrito (VERSIÓN FINAL)
 */
public function crearDesdeCarrito($idCliente, $tipoEnvio, $direccion, $ciudad, $codigoPostal, $referencia = null) {
    try {
        // Mapear tipo de envío a ID
        $idTipoEntrega = ($tipoEnvio === 'Domicilio') ? 2 : 1;
        
        // ID de método de pago (Efectivo = 1)
        $idMetodoPago = 1;
        
        // Construir dirección completa
        $direccionCompleta = $direccion . ', ' . $ciudad . ', CP: ' . $codigoPostal;
        
        // Notas del cliente
        $notasCliente = !empty($referencia) ? $referencia : null;
        
        // Llamar al SP (que genera el número automáticamente)
        $query = "EXEC sp_CrearPedidoDesdeCarrito 
                  @IdCliente = ?,
                  @IdMetodoPago = ?,
                  @IdTipoEntrega = ?,
                  @DireccionEnvio = ?,
                  @NotasCliente = ?";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->execute([
            $idCliente,
            $idMetodoPago,
            $idTipoEntrega,
            $direccionCompleta,
            $notasCliente
        ]);
        
        // El SP retorna IdPedido, NumeroPedido y Total
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado && isset($resultado['IdPedido'])) {
            return [
                'success' => true,
                'id_pedido' => $resultado['IdPedido'],
                'numero_pedido' => $resultado['NumeroPedido'],
                'total' => $resultado['Total'],
                'mensaje' => 'Pedido creado exitosamente'
            ];
        }
        
        return ['error' => 'Error al crear el pedido'];
        
    } catch (PDOException $e) {
        error_log("Error en crearDesdeCarrito: " . $e->getMessage());
        return ['error' => 'Error al crear el pedido: ' . $e->getMessage()];
    }
}



    /**
 * Obtener información del envío
 */
public function obtenerEnvio($idPedido) {
    try {
        $query = "SELECT 
                    e.IdEnvio,
                    es.NombreEstado as EstadoEnvio,
                    e.DireccionEnvio as DireccionCompleta,
                    e.FechaEntregaEstimada as FechaEstimadaEntrega,
                    e.FechaEntrega,
                    e.NumeroGuia
                  FROM Envios e
                  LEFT JOIN EstadosEnvio es ON e.IdEstadoEnvio = es.IdEstadoEnvio
                  WHERE e.IdPedido = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$idPedido]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return null;
    }
}








/**
 * Obtener pedidos del cliente (VERSIÓN CORREGIDA)
 */
public function obtenerPorCliente($idCliente, $limite = 20, $estadoFiltro = null) {
    try {
        $query = "SELECT 
                    p.IdPedido,
                    p.NumeroPedido,
                    p.FechaPedido,
                    p.Total,
                    p.IdEstadoPedido,
                    p.IdMetodoPago,
                    p.IdTipoEntrega
                  FROM Pedidos p
                  WHERE p.IdCliente = ?
                  ORDER BY p.FechaPedido DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            error_log("Error al preparar query obtenerPorCliente");
            return [];
        }
        
        $ejecutado = $stmt->execute([(int)$idCliente]);
        
        if (!$ejecutado) {
            error_log("Error al ejecutar query obtenerPorCliente");
            return [];
        }
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($pedidos)) {
            return [];
        }
        $resultado = [];
        
        foreach ($pedidos as $pedido) {
            $pedidoCompleto = [
                'IdPedido' => $pedido['IdPedido'],
                'NumeroPedido' => $pedido['NumeroPedido'],
                'FechaPedido' => $pedido['FechaPedido'],
                'Total' => $pedido['Total'],
                'IdEstadoPedido' => $pedido['IdEstadoPedido'],
                'IdMetodoPago' => $pedido['IdMetodoPago'],
                'IdTipoEntrega' => $pedido['IdTipoEntrega']
            ];
            
            // Obtener nombre del estado
            try {
                $stmtEstado = $this->conn->prepare("SELECT NombreEstado FROM EstadosPedido WHERE IdEstadoPedido = ?");
                if ($stmtEstado && $stmtEstado->execute([$pedido['IdEstadoPedido']])) {
                    $estado = $stmtEstado->fetch(PDO::FETCH_ASSOC);
                    $pedidoCompleto['EstadoPedido'] = $estado ? $estado['NombreEstado'] : 'Pendiente';
                } else {
                    $pedidoCompleto['EstadoPedido'] = 'Pendiente';
                }
            } catch (Exception $e) {
                error_log("Error obteniendo estado pedido: " . $e->getMessage());
                $pedidoCompleto['EstadoPedido'] = 'Pendiente';
            }
            
            // Obtener método de pago
            try {
                if ($pedido['IdMetodoPago']) {
                    $stmtMetodo = $this->conn->prepare("SELECT NombreMetodo FROM MetodosPago WHERE IdMetodo = ?");
                    if ($stmtMetodo && $stmtMetodo->execute([$pedido['IdMetodoPago']])) {
                        $metodo = $stmtMetodo->fetch(PDO::FETCH_ASSOC);
                        $pedidoCompleto['MetodoPago'] = $metodo ? $metodo['NombreMetodo'] : 'N/A';
                    } else {
                        $pedidoCompleto['MetodoPago'] = 'N/A';
                    }
                } else {
                    $pedidoCompleto['MetodoPago'] = 'N/A';
                }
            } catch (Exception $e) {
                error_log("Error obteniendo método pago: " . $e->getMessage());
                $pedidoCompleto['MetodoPago'] = 'N/A';
            }

            try {
                $stmtCount = $this->conn->prepare("SELECT COUNT(*) as total FROM DetallesPedido WHERE IdPedido = ?");
                if ($stmtCount && $stmtCount->execute([$pedido['IdPedido']])) {
                    $count = $stmtCount->fetch(PDO::FETCH_ASSOC);
                    $pedidoCompleto['TotalProductos'] = $count ? (int)$count['total'] : 0;
                } else {
                    error_log("Error al contar productos para pedido " . $pedido['IdPedido']);
                    $pedidoCompleto['TotalProductos'] = 0;
                }
            } catch (Exception $e) {
                error_log("Error contando productos: " . $e->getMessage());
                $pedidoCompleto['TotalProductos'] = 0;
            }
            
            // Obtener info de envío
            try {
                $stmtEnvio = $this->conn->prepare("SELECT e.FechaEntregaEstimada, ee.NombreEstado 
                                                   FROM Envios e 
                                                   LEFT JOIN EstadosEnvio ee ON e.IdEstadoEnvio = ee.IdEstadoEnvio 
                                                   WHERE e.IdPedido = ?");
                if ($stmtEnvio && $stmtEnvio->execute([$pedido['IdPedido']])) {
                    $envio = $stmtEnvio->fetch(PDO::FETCH_ASSOC);
                    $pedidoCompleto['FechaEntregaEstimada'] = $envio ? $envio['FechaEntregaEstimada'] : null;
                    $pedidoCompleto['EstadoEnvio'] = $envio ? $envio['NombreEstado'] : null;
                } else {
                    $pedidoCompleto['FechaEntregaEstimada'] = null;
                    $pedidoCompleto['EstadoEnvio'] = null;
                }
            } catch (Exception $e) {
                error_log("Error obteniendo envío: " . $e->getMessage());
                $pedidoCompleto['FechaEntregaEstimada'] = null;
                $pedidoCompleto['EstadoEnvio'] = null;
            }
            
            // Agregar al resultado
            $resultado[] = $pedidoCompleto;
        }
        
        // Aplicar filtro de estado si existe
        if ($estadoFiltro) {
            $resultado = array_filter($resultado, function($p) use ($estadoFiltro) {
                return isset($p['EstadoPedido']) && $p['EstadoPedido'] === $estadoFiltro;
            });
            $resultado = array_values($resultado);
        }
        
        // ✅ Aplicar límite
        if ($limite && count($resultado) > $limite) {
            $resultado = array_slice($resultado, 0, $limite);
        }
        
        return $resultado;
        
    } catch (PDOException $e) {
        error_log("Error PDO en obtenerPorCliente: " . $e->getMessage());
        return [];
    } catch (Exception $e) {
        error_log("Error general en obtenerPorCliente: " . $e->getMessage());
        return [];
    }
}


















    /**
     * Obtener detalle de un pedido
     */
    public function obtenerDetalle($idPedido, $idCliente = null) {
    try {
        // Información del pedido CON JOINS a las tablas de estados
        $query = "SELECT 
                    p.*,
                    c.NombreCompleto as NombreCliente,
                    c.Email as EmailCliente,
                    c.Telefono as TelefonoCliente,
                    ep.NombreEstado as EstadoPedido,
                    mp.NombreMetodo as MetodoPago,
                    te.NombreTipo as TipoEnvio,
                    e.IdEnvio,
                    ee.NombreEstado as EstadoEnvio,
                    e.FechaEntregaEstimada as FechaEstimadaEntrega,
                    e.FechaEntrega,
                    e.NumeroGuia
                  FROM Pedidos p
                  INNER JOIN Clientes c ON p.IdCliente = c.IdCliente
                  LEFT JOIN EstadosPedido ep ON p.IdEstadoPedido = ep.IdEstadoPedido
                  LEFT JOIN MetodosPago mp ON p.IdMetodoPago = mp.IdMetodoPago
                  LEFT JOIN TiposEntrega te ON p.IdTipoEntrega = te.IdTipoEntrega
                  LEFT JOIN Envios e ON p.IdPedido = e.IdPedido
                  LEFT JOIN EstadosEnvio ee ON e.IdEstadoEnvio = ee.IdEstadoEnvio
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
                            (dp.Cantidad * dp.PrecioUnitario) as Subtotal,
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
        error_log("Error en obtenerDetalle: " . $e->getMessage());
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