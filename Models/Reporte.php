<?php
/**
 * Modelo Reporte
 * Gestión de reportes del sistema
 */

require_once __DIR__ . '/../config/Database.php';

class Reporte {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Reporte de Ventas por Período
     * Devuelve 3 resultados: Resumen general, Ventas por día, Ventas por categoría
     */
    public function ventasPorPeriodo($fechaInicio, $fechaFin) {
        try {
            $sql = "EXEC sp_ReporteVentasPorPeriodo @FechaInicio = ?, @FechaFin = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$fechaInicio, $fechaFin]);
            
            // Resultado 1: Resumen general
            $resumenGeneral = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Resultado 2: Ventas por día
            $stmt->nextRowset();
            $ventasPorDia = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Resultado 3: Ventas por categoría
            $stmt->nextRowset();
            $ventasPorCategoria = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'resumen' => $resumenGeneral,
                'ventas_por_dia' => $ventasPorDia,
                'ventas_por_categoria' => $ventasPorCategoria
            ];

        } catch (PDOException $e) {
            error_log("Error en ventasPorPeriodo: " . $e->getMessage());
            return [
                'resumen' => [],
                'ventas_por_dia' => [],
                'ventas_por_categoria' => []
            ];
        }
    }

    /**
     * Reporte de Ventas por Método de Pago
     */
    public function ventasPorMetodoPago($fechaInicio, $fechaFin) {
        try {
            $sql = "EXEC sp_ReporteVentasPorMetodoPago @FechaInicio = ?, @FechaFin = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$fechaInicio, $fechaFin]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en ventasPorMetodoPago: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Reporte de Ventas por Cliente (Top 20)
     */
    public function ventasPorCliente($fechaInicio, $fechaFin, $top = 20) {
        try {
            $sql = "EXEC sp_ReporteVentasPorCliente @FechaInicio = ?, @FechaFin = ?, @Top = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$fechaInicio, $fechaFin, $top]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en ventasPorCliente: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Reporte Financiero
     * Devuelve 3 resultados: Resumen financiero, Márgenes por categoría, Productos más rentables
     */
    public function reporteFinanciero($fechaInicio, $fechaFin) {
        try {
            $sql = "EXEC sp_ReporteFinanciero @FechaInicio = ?, @FechaFin = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$fechaInicio, $fechaFin]);
            
            // Resultado 1: Resumen financiero general
            $resumenFinanciero = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Resultado 2: Márgenes por categoría
            $stmt->nextRowset();
            $margenesPorCategoria = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Resultado 3: Productos más rentables (Top 10)
            $stmt->nextRowset();
            $productosRentables = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'resumen_financiero' => $resumenFinanciero,
                'margenes_por_categoria' => $margenesPorCategoria,
                'productos_rentables' => $productosRentables
            ];

        } catch (PDOException $e) {
            error_log("Error en reporteFinanciero: " . $e->getMessage());
            return [
                'resumen_financiero' => [],
                'margenes_por_categoria' => [],
                'productos_rentables' => []
            ];
        }
    }

    /**
     * Reporte de Productos Más Vendidos (Top 20)
     */
    public function productosMasVendidos($fechaInicio, $fechaFin, $top = 20) {
        try {
            $sql = "EXEC sp_ReporteProductosMasVendidos @FechaInicio = ?, @FechaFin = ?, @Top = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$fechaInicio, $fechaFin, $top]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en productosMasVendidos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Reporte de Inventario Actual
     * Devuelve 3 resultados: Resumen general, Inventario por categoría, Productos con stock crítico
     */
    public function inventarioActual() {
        try {
            $sql = "EXEC sp_ReporteInventarioActual";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            
            // Resultado 1: Resumen general
            $resumenGeneral = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Resultado 2: Inventario por categoría
            $stmt->nextRowset();
            $inventarioPorCategoria = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Resultado 3: Productos con stock crítico
            $stmt->nextRowset();
            $stockCritico = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'resumen' => $resumenGeneral,
                'inventario_por_categoria' => $inventarioPorCategoria,
                'stock_critico' => $stockCritico
            ];

        } catch (PDOException $e) {
            error_log("Error en inventarioActual: " . $e->getMessage());
            return [
                'resumen' => [],
                'inventario_por_categoria' => [],
                'stock_critico' => []
            ];
        }
    }

    /**
     * Función helper para formatear moneda
     */
    public function formatoMoneda($valor) {
        return '$' . number_format($valor, 2, '.', ',');
    }

    /**
     * Función helper para formatear porcentaje
     */
    public function formatoPorcentaje($valor) {
        return number_format($valor, 2, '.', ',') . '%';
    }
}