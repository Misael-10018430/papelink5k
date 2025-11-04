<?php
require_once __DIR__ . '/../config/Database.php';

class Pedido {
    public $conn; 
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Crear pedido desde carrito usando SP
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
            error_log("Error en obtenerEnvio: " . $e->getMessage());
            return null;
        }
    }
/**
     * Obtener pedidos del cliente (Versión de ACERO - Sin Envios)
     */
    public function obtenerPorCliente($idCliente, $limite = 20, $estadoFiltro = null) {
        try {
            // Query base - HE QUITADO LOS JOINS DE ENVIOS
            $query = "SELECT 
                        p.IdPedido,
                        p.NumeroPedido,
                        p.FechaPedido,
                        p.Total,
                        p.IdEstadoPedido,
                        p.IdMetodoPago,
                        p.IdTipoEntrega,
                        ep.NombreEstado as EstadoPedido,
                        mp.NombreMetodo as MetodoPago,
                        (SELECT COUNT(*) FROM DetallesPedido WHERE IdPedido = p.IdPedido) as TotalProductos,
                        
                        /* Columnas de envío eliminadas temporalmente */
                        NULL as EstadoEnvio, 
                        NULL as FechaEntregaEstimada

                      FROM Pedidos p
                      LEFT JOIN EstadosPedido ep ON p.IdEstadoPedido = ep.IdEstadoPedido
                      LEFT JOIN MetodosPago mp ON p.IdMetodoPago = mp.IdMetodoPago
                      
                      /* JOINS de envío eliminados temporalmente 
                      LEFT JOIN Envios e ON p.IdPedido = e.IdPedido
                      LEFT JOIN EstadosEnvio ee ON e.IdEstadoEnvio = ee.IdEstadoEnvio
                      */
                      
                      WHERE p.IdCliente = ?";
            
            // Array de parámetros unificado
            $params = [$idCliente];

            if ($estadoFiltro) {
                $query .= " AND ep.NombreEstado = ?";
                $params[] = $estadoFiltro;
            }
            
            $query .= " ORDER BY p.FechaPedido DESC";
            
            if ($limite) {
                $query .= " OFFSET 0 ROWS FETCH NEXT ? ROWS ONLY";
                $params[] = $limite;
            }
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            // =======================================================
            error_log("Error en obtenerPorCliente: " . $e->getMessage()); // <-- ¡ARREGLADO! Era ->
            // =======================================================
            return [];
        }
    }
    /**
 * Obtener detalle completo de un pedido (USANDO SP CORRECTAMENTE)
 */
public function obtenerDetalle($idPedido, $idCliente = null) {
    try {
        // Llamar al SP
        if ($idCliente) {
            $query = "EXEC sp_ObtenerDetallePedido @IdPedido = ?, @IdCliente = ?";
            $params = [$idPedido, $idCliente];
        } else {
            $query = "EXEC sp_ObtenerDetallePedido @IdPedido = ?";
            $params = [$idPedido];
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
        // ✅ RESULT SET 1: Información general del pedido
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$pedido) {
            error_log("Pedido no encontrado: IdPedido=" . $idPedido . ", IdCliente=" . ($idCliente ?? 'null'));
            return null;
        }
        
        // ✅ RESULT SET 2: Detalles de productos
        $stmt->nextRowset();
        $pedido['detalles'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // ✅ RESULT SET 3: Información de envío
        $stmt->nextRowset();
        $envio = $stmt->fetch(PDO::FETCH_ASSOC);
        $pedido['envio'] = $envio ? $envio : null;
        
        // Mapear campos para compatibilidad con las vistas
        $pedido['NombreCliente'] = $pedido['NombreClienteSnapshot'];
        $pedido['EmailCliente'] = $pedido['EmailSnapshot'];
        $pedido['TelefonoCliente'] = $pedido['TelefonoSnapshot'];
        $pedido['DireccionEnvioSnapshot'] = $pedido['DireccionSnapshot'];
        
        // Información de envío para compatibilidad
        if ($pedido['envio']) {
            $pedido['IdEnvio'] = $pedido['envio']['IdEnvio'];
            $pedido['EstadoEnvio'] = $pedido['envio']['EstadoEnvio'];
            $pedido['FechaEstimadaEntrega'] = $pedido['envio']['FechaEntregaEstimada'];
            $pedido['FechaEntrega'] = $pedido['envio']['FechaEntregaReal'];
            $pedido['NumeroGuia'] = null; // El SP no retorna NumeroGuia
        }
        
        return $pedido;
        
    } catch (PDOException $e) {
        error_log("Error en obtenerDetalle: " . $e->getMessage());
        
        // Si el error es del SP (pedido no autorizado)
        if (strpos($e->getMessage(), '50009') !== false) {
            error_log("Cliente " . $idCliente . " intentó acceder al pedido " . $idPedido . " sin autorización");
        }
        
        return null;
    }
    }

    /**
     * Obtener solo los productos de un pedido (FALLBACK)
     */
    public function obtenerDetalles($idPedido) {
    try {
        $query = "SELECT 
                    dp.IdDetallePedido,
                    dp.IdProducto,
                    dp.Cantidad,
                    dp.PrecioUnitario,
                    (dp.Cantidad * dp.PrecioUnitario) as Subtotal,
                    p.NombreProducto,
                    p.CodigoProducto,
                    m.NombreMarca
                  FROM DetallePedidos dp
                  INNER JOIN Productos p ON dp.IdProducto = p.IdProducto
                  INNER JOIN Marcas m ON p.IdMarca = m.IdMarca
                  WHERE dp.IdPedido = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$idPedido]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error en obtenerDetalles: " . $e->getMessage());
        return [];
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
                        p.Total,
                        ep.NombreEstado as EstadoPedido,
                        mp.NombreMetodo as MetodoPago,
                        c.NombreCliente as NombreCliente,
                        c.Email as EmailCliente,
                        (SELECT COUNT(*) FROM DetallesPedido WHERE IdPedido = p.IdPedido) as TotalProductos,
                        ee.NombreEstado as EstadoEnvio
                      FROM Pedidos p
                      INNER JOIN Clientes c ON p.IdCliente = c.IdCliente
                      LEFT JOIN EstadosPedido ep ON p.IdEstadoPedido = ep.IdEstadoPedido
                      LEFT JOIN MetodosPago mp ON p.IdMetodoPago = mp.IdMetodoPago
                      LEFT JOIN Envios e ON p.IdPedido = e.IdPedido
                      LEFT JOIN EstadosEnvio ee ON e.IdEstadoEnvio = ee.IdEstadoEnvio
                      WHERE 1=1";
            
            $params = [];
            
            if ($estado) {
                $query .= " AND ep.NombreEstado = :estado";
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
            
            $query .= " ORDER BY p.FechaPedido DESC
                        OFFSET :offset ROWS
                        FETCH NEXT :limite ROWS ONLY";
            
            $params[':offset'] = $offset;
            $params[':limite'] = $limite;
            
            // =======================================================
            $stmt = $this->conn->prepare($query); // <-- ¡ARREGLADO! Era -> en lugar de .
            // =======================================================
            
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
            error_log("Error en obtenerTodos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Cambiar estado del pedido
     */
    public function cambiarEstado($idPedido, $nuevoEstado) {
        try {
            // Obtener ID del estado
            $queryEstado = "SELECT IdEstadoPedido FROM EstadosPedido WHERE NombreEstado = ?";
            $stmtEstado = $this->conn->prepare($queryEstado);
            $stmtEstado->execute([$nuevoEstado]);
            $estado = $stmtEstado->fetch(PDO::FETCH_ASSOC);
            
            if (!$estado) {
                return ['error' => 'Estado no válido'];
            }
            
            // Actualizar pedido
            $query = "UPDATE Pedidos 
                     SET IdEstadoPedido = ? 
                     WHERE IdPedido = ?";
            
            $stmt = $this->conn->prepare($query);
            
            if ($stmt->execute([$estado['IdEstadoPedido'], $idPedido])) {
                return ['success' => true, 'mensaje' => 'Estado actualizado'];
            }
            
            return ['error' => 'Error al actualizar estado'];
            
        } catch (PDOException $e) {
            error_log("Error en cambiarEstado: " . $e->getMessage());
            return ['error' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Cancelar pedido
     */
    public function cancelarPedido($idPedido, $idCliente = null) {
        try {
            // Obtener el pedido completo
            $pedido = $this->obtenerDetalle($idPedido, $idCliente);
            
            if (!$pedido) {
                return ['error' => 'Pedido no encontrado'];
            }
            
            // Verificar que el estado permita cancelación
            if (!in_array($pedido['EstadoPedido'], ['Pendiente', 'En Proceso'])) {
                return ['error' => 'El pedido no puede ser cancelado en su estado actual: ' . $pedido['EstadoPedido']];
            }
            
            // Obtener ID del estado "Cancelado"
            $queryIdCancelado = "SELECT IdEstadoPedido FROM EstadosPedido WHERE NombreEstado = 'Cancelado'";
            $stmtIdCancelado = $this->conn->prepare($queryIdCancelado);
            $stmtIdCancelado->execute();
            $estadoCancelado = $stmtIdCancelado->fetch(PDO::FETCH_ASSOC);
            
            if (!$estadoCancelado) {
                return ['error' => 'No se encontró el estado Cancelado en la base de datos'];
            }
            
            // Actualizar pedido a estado Cancelado
            $query = "UPDATE Pedidos 
                     SET IdEstadoPedido = :idEstadoCancelado
                     WHERE IdPedido = :idPedido";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idEstadoCancelado', $estadoCancelado['IdEstadoPedido'], PDO::PARAM_INT);
            $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                // Restaurar inventario
                $queryRestaurar = "UPDATE i
                                  SET i.CantidadDisponible = i.CantidadDisponible + dp.Cantidad,
                                      i.CantidadReservada = CASE 
                                          WHEN i.CantidadReservada >= dp.Cantidad 
                                          THEN i.CantidadReservada - dp.Cantidad 
                                          ELSE 0 
                                      END
                                  FROM Inventarios i
                                  INNER JOIN DetallePedidos dp ON i.IdProducto = dp.IdProducto
                                  WHERE dp.IdPedido = :idPedido";
                
                $stmtRestaurar = $this->conn->prepare($queryRestaurar);
                $stmtRestaurar->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
                $stmtRestaurar->execute();
                
                return ['success' => true, 'mensaje' => 'Pedido cancelado exitosamente'];
            }
            
            return ['error' => 'Error al cancelar pedido'];
            
        } catch (PDOException $e) {
            error_log("Error en cancelarPedido: " . $e->getMessage());
            return ['error' => 'Error: ' . $e->getMessage()];
        }
    }
}
?>