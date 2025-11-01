<?php
require_once __DIR__ . '/../config/Database.php';
class Producto {
    private $conn;
    private $db;
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }  
    /**
     * Obtener todos los productos (para admin con filtros)
     */
    public function obtenerTodos($idCategoria = null, $idMarca = null, $estado = null, $busqueda = null, $pagina = 1, $porPagina = 20) {
        try {
            $sql = "EXEC sp_ObtenerProductosAdmin 
                    @IdCategoria = ?, 
                    @IdMarca = ?, 
                    @Estado = ?, 
                    @BusquedaNombre = ?, 
                    @Pagina = ?, 
                    @ProductosPorPagina = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idCategoria, $idMarca, $estado, $busqueda, $pagina, $porPagina]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerTodos: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Obtener productos para cliente (catálogo público)
     */
    public function obtenerParaCliente($idCategoria = null, $idMarca = null, $busqueda = null, $precioMin = null, $precioMax = null, $pagina = 1, $porPagina = 12) {
    try {

        $sql = "EXEC sp_ObtenerProductos 
                @IdCategoria = ?, 
                @IdMarca = ?, 
                @BusquedaNombre = ?, 
                @PrecioMin = ?, 
                @PrecioMax = ?, 
                @Pagina = ?, 
                @ProductosPorPagina = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $idCategoria, 
            $idMarca, 
            $busqueda, 
            $precioMin, 
            $precioMax, 
            $pagina, 
            $porPagina
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en obtenerParaCliente: " . $e->getMessage());
        return [];
    }
}
    /**
     * Obtener producto por ID
     */
    public function obtenerPorId($idProducto) {
        try {
            $sql = "EXEC sp_ObtenerDetalleProducto @IdProducto = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idProducto]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerPorId: " . $e->getMessage());
            return null;
        }
    }
    /**
     * Crear nuevo producto
     */
    public function crear($datos) {
        try {
            $sql = "EXEC sp_CrearProducto 
                    @IdCategoria = ?, 
                    @IdMarca = ?, 
                    @IdUnidad = ?, 
                    @CodigoProducto = ?, 
                    @NombreProducto = ?, 
                    @Descripcion = ?, 
                    @DescripcionCorta = ?, 
                    @PrecioUnitario = ?, 
                    @CostoUnitario = ?, 
                    @StockMinimo = ?, 
                    @CantidadInicial = ?,
                    @ImagenPrincipal = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $datos['idCategoria'],
                $datos['idMarca'],
                $datos['idUnidad'],
                $datos['codigoProducto'],
                $datos['nombreProducto'],
                $datos['descripcion'] ?? null,
                $datos['descripcionCorta'] ?? null,
                $datos['precioUnitario'],
                $datos['costoUnitario'],
                $datos['stockMinimo'] ?? 5,
                $datos['cantidadInicial'] ?? 0,
                $datos['imagenPrincipal'] ?? null
            ]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en crear: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    /**
     * Actualizar producto
     */
    public function actualizar($datos) {
        try {
            $sql = "EXEC sp_ActualizarProducto 
                    @IdProducto = ?, 
                    @IdCategoria = ?, 
                    @IdMarca = ?, 
                    @IdUnidad = ?, 
                    @NombreProducto = ?, 
                    @Descripcion = ?, 
                    @DescripcionCorta = ?, 
                    @PrecioUnitario = ?, 
                    @StockMinimo = ?,
                    @ImagenPrincipal = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $datos['idProducto'],
                $datos['idCategoria'],
                $datos['idMarca'],
                $datos['idUnidad'],
                $datos['nombreProducto'],
                $datos['descripcion'] ?? null,
                $datos['descripcionCorta'] ?? null,
                $datos['precioUnitario'],
                $datos['stockMinimo'],
                $datos['imagenPrincipal'] ?? null
            ]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en actualizar: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    /**
     * Cambiar estado de producto (activar/desactivar)
     */
    public function cambiarEstado($idProducto, $estado) {
        try {
            $sql = "EXEC sp_CambiarEstadoProducto @IdProducto = ?, @Estado = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idProducto, $estado]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en cambiarEstado: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    /**
     * Obtener productos relacionados
     */
    public function obtenerRelacionados($idProducto) {
        try {
            $sql = "EXEC sp_ObtenerProductosRelacionados @IdProducto = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idProducto]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerRelacionados: " . $e->getMessage());
            return [];
        }
    }
}
?>