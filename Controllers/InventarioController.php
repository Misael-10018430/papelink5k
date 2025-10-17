<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Inventario.php';
class InventarioController {
    private $inventarioModel;
    public function __construct() {
        $this->inventarioModel = new Inventario();
    }
    /**
     * Ver inventario completo
     */
    public function verInventario() {
        $idCategoria = $_GET['categoria'] ?? null;
        $soloStockBajo = isset($_GET['stock_bajo']) ? (int)$_GET['stock_bajo'] : 0;
        $inventario = $this->inventarioModel->obtenerCompleto($idCategoria, $soloStockBajo);   
        return $inventario;
    }
    /**
     * Ver productos con stock bajo (alertas)
     */
    public function verStockBajo() {
        return $this->inventarioModel->obtenerStockBajo();
    }
    /**
     * Ajustar inventario manualmente
     */
    public function ajustar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: inventario.php");
            exit();
        }
        // Validar datos
        $errores = $this->validarAjuste($_POST);
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            header("Location: inventario.php?accion=ajustar");
            exit();
        }
        $idProducto = (int)$_POST['id_producto'];
        $tipoAjuste = $_POST['tipo_ajuste']; // ENTRADA, SALIDA, AJUSTE
        $cantidad = (int)$_POST['cantidad'];
        $motivo = trim($_POST['motivo']);
        $nuevoCosto = !empty($_POST['nuevo_costo']) ? (float)$_POST['nuevo_costo'] : null;
        // Realizar ajuste
        $resultado = $this->inventarioModel->ajustar($idProducto, $tipoAjuste, $cantidad, $motivo, $nuevoCosto);
        if (isset($resultado['error'])) {
            $_SESSION['error'] = 'Error al ajustar inventario: ' . $resultado['error'];
        } else {
            $_SESSION['exito'] = 'Inventario ajustado exitosamente';
        }
        header("Location: inventario.php");
        exit();
    }
    /**
     * Actualizar costo unitario
     */
    public function actualizarCosto() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: inventario.php");
            exit();
        }
        if (empty($_POST['id_producto']) || empty($_POST['nuevo_costo'])) {
            $_SESSION['error'] = 'Datos incompletos';
            header("Location: inventario.php");
            exit();
        }
        $idProducto = (int)$_POST['id_producto'];
        $nuevoCosto = (float)$_POST['nuevo_costo'];
        if ($nuevoCosto < 0) {
            $_SESSION['error'] = 'El costo no puede ser negativo';
            header("Location: inventario.php");
            exit();
        }
        $resultado = $this->inventarioModel->actualizarCosto($idProducto, $nuevoCosto);      
        if (isset($resultado['error'])) {
            $_SESSION['error'] = 'Error al actualizar costo: ' . $resultado['error'];
        } else {
            $_SESSION['exito'] = 'Costo actualizado exitosamente';
        }       
        header("Location: inventario.php");
        exit();
    }    
    /**
     * Validar datos de ajuste
     */
    private function validarAjuste($datos) {
        $errores = [];        
        if (empty($datos['id_producto'])) {
            $errores[] = 'Debe seleccionar un producto';
        }        
        if (empty($datos['tipo_ajuste']) || !in_array($datos['tipo_ajuste'], ['ENTRADA', 'SALIDA', 'AJUSTE'])) {
            $errores[] = 'Tipo de ajuste inválido';
        }        
        if (empty($datos['cantidad']) || $datos['cantidad'] <= 0) {
            $errores[] = 'La cantidad debe ser mayor a cero';
        }       
        if (empty($datos['motivo'])) {
            $errores[] = 'El motivo es obligatorio';
        }        
        return $errores;
    }
}
?>