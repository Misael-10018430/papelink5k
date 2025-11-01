<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../config/Auth.php';
class ProductoController {
    private $productoModel;   
    public function __construct() {
        $this->productoModel = new Producto();
    }    
    /**
     * Listar productos (ADMIN)
     */
    public function listarAdmin() {
        //  VERIFICAR PERMISO PARA VER PRODUCTOS
        if (!Auth::esAdministrador() && !Auth::tieneFuncionalidad('PRODUCTOS_VER')) {
            $_SESSION['error'] = 'No tiene permisos para ver productos';
            header('Location: dashboard.php');
            exit();
        }
        $idCategoria = $_GET['categoria'] ?? null;
        $idMarca = $_GET['marca'] ?? null;
        $estado = isset($_GET['estado']) ? (int)$_GET['estado'] : null;
        $busqueda = $_GET['busqueda'] ?? null;
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $productos = $this->productoModel->obtenerTodos($idCategoria, $idMarca, $estado, $busqueda, $pagina, 20);
        return $productos;
    }
    /**
     * Listar productos (CLIENTE - catálogo público)
     */
    public function listarCliente() {
        $idCategoria = isset($_GET['categoria']) && $_GET['categoria'] !== '' ? (int)$_GET['categoria'] : null;
        $idMarca = isset($_GET['marca']) && $_GET['marca'] !== '' ? (int)$_GET['marca'] : null;
        $busqueda = isset($_GET['busqueda']) && $_GET['busqueda'] !== '' ? $_GET['busqueda'] : null;
        $precioMin = isset($_GET['precio_min']) && $_GET['precio_min'] !== '' ? (float)$_GET['precio_min'] : null;
        $precioMax = isset($_GET['precio_max']) && $_GET['precio_max'] !== '' ? (float)$_GET['precio_max'] : null;
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

        error_log("=== DEBUG ProductoController->listarCliente() ===");
        error_log("idCategoria: " . ($idCategoria ?? 'NULL'));
        error_log("idMarca: " . ($idMarca ?? 'NULL'));
        error_log("busqueda: " . ($busqueda ?? 'NULL'));
        error_log("precioMin: " . ($precioMin ?? 'NULL'));
        error_log("precioMax: " . ($precioMax ?? 'NULL'));
        error_log("pagina: " . $pagina);





















        $productos = $this->productoModel->obtenerParaCliente(
            $idCategoria, 
            $idMarca, 
            $busqueda, 
            $precioMin, 
            $precioMax, 
            $pagina, 
            12
        );
        return $productos;
    }
    /**
     * Ver detalle de un producto
     */
    public function verDetalle() {
        //  VERIFICAR PERMISO PARA VER PRODUCTOS
        if (!Auth::esAdministrador() && !Auth::tieneFuncionalidad('PRODUCTOS_VER')) {
            $_SESSION['error'] = 'No tiene permisos para ver detalles de productos';
            header('Location: dashboard.php');
            exit();
        }
        if (!isset($_GET['id'])) {
            header("Location: productos.php");
            exit();
        }
        $idProducto = (int)$_GET['id'];
        $producto = $this->productoModel->obtenerPorId($idProducto);
        
        if (!$producto) {
            header("Location: productos.php");
            exit();
        }
        return $producto;
    }
    /**
     * Crear producto
     */
    public function crear() {
        //  VERIFICAR PERMISO
        Auth::requiereFuncionalidad('PRODUCTOS_CREAR');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: productos.php");
            exit();
        }
        // Validar datos
        $errores = $this->validarDatos($_POST);
        
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_form'] = $_POST;
            header("Location: productos.php?accion=nuevo");
            exit();
        }
        // Manejar subida de imagen
        $nombreImagen = null;
        if (isset($_FILES['imagen_producto']) && $_FILES['imagen_producto']['error'] !== UPLOAD_ERR_NO_FILE) {
            $resultadoImagen = $this->subirImagen($_FILES['imagen_producto']);
            if (isset($resultadoImagen['error'])) {
                $_SESSION['error'] = $resultadoImagen['error'];
                $_SESSION['datos_form'] = $_POST;
                header("Location: productos.php?accion=nuevo");
                exit();
            }
            $nombreImagen = $resultadoImagen['nombre'];
        }
        // Preparar datos
        $datos = [
            'idCategoria' => (int)$_POST['id_categoria'],
            'idMarca' => (int)$_POST['id_marca'],
            'idUnidad' => (int)$_POST['id_unidad'],
            'codigoProducto' => trim($_POST['codigo_producto']),
            'nombreProducto' => trim($_POST['nombre_producto']),
            'descripcion' => trim($_POST['descripcion']) ?: null,
            'descripcionCorta' => trim($_POST['descripcion_corta']) ?: null,
            'precioUnitario' => (float)$_POST['precio_unitario'],
            'costoUnitario' => (float)$_POST['costo_unitario'],
            'stockMinimo' => isset($_POST['stock_minimo']) ? (int)$_POST['stock_minimo'] : 5,
            'cantidadInicial' => isset($_POST['cantidad_inicial']) ? (int)$_POST['cantidad_inicial'] : 0,
            'imagenPrincipal' => $nombreImagen
        ];
        // Crear producto
        $resultado = $this->productoModel->crear($datos);
        
        if (isset($resultado['error'])) {
            // Si falla, eliminar la imagen subida
            if ($nombreImagen) {
                $this->eliminarImagen($nombreImagen);
            }
            $_SESSION['error'] = 'Error al crear el producto: ' . $resultado['error'];
            $_SESSION['datos_form'] = $_POST;
            header("Location: productos.php?accion=nuevo");
        } else {
            $_SESSION['exito'] = 'Producto creado exitosamente';
            header("Location: productos.php");
        }
        exit();
    }
    /**
     * Actualizar producto
     */
    public function actualizar() {
        //  VERIFICAR PERMISO
        Auth::requiereFuncionalidad('PRODUCTOS_EDITAR');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: productos.php");
            exit();
        }
        if (!isset($_POST['id_producto'])) {
            $_SESSION['error'] = 'ID de producto no proporcionado';
            header("Location: productos.php");
            exit();
        }
        // Validar datos
        $errores = $this->validarDatos($_POST, true);
        
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_form'] = $_POST;
            header("Location: productos.php?accion=editar&id=" . $_POST['id_producto']);
            exit();
        }
        // Manejar subida de nueva imagen
        $nombreImagen = null;
        if (isset($_FILES['imagen_producto']) && $_FILES['imagen_producto']['error'] !== UPLOAD_ERR_NO_FILE) {
            $resultadoImagen = $this->subirImagen($_FILES['imagen_producto']);
            if (isset($resultadoImagen['error'])) {
                $_SESSION['error'] = $resultadoImagen['error'];
                $_SESSION['datos_form'] = $_POST;
                header("Location: productos.php?accion=editar&id=" . $_POST['id_producto']);
                exit();
            }
            $nombreImagen = $resultadoImagen['nombre'];
            
            // Eliminar imagen anterior si existe
            $productoActual = $this->productoModel->obtenerPorId($_POST['id_producto']);
            if ($productoActual && !empty($productoActual['ImagenPrincipal'])) {
                $this->eliminarImagen($productoActual['ImagenPrincipal']);
            }
        }
        // Preparar datos
        $datos = [
            'idProducto' => (int)$_POST['id_producto'],
            'idCategoria' => (int)$_POST['id_categoria'],
            'idMarca' => (int)$_POST['id_marca'],
            'idUnidad' => (int)$_POST['id_unidad'],
            'nombreProducto' => trim($_POST['nombre_producto']),
            'descripcion' => trim($_POST['descripcion']) ?: null,
            'descripcionCorta' => trim($_POST['descripcion_corta']) ?: null,
            'precioUnitario' => (float)$_POST['precio_unitario'],
            'stockMinimo' => (int)$_POST['stock_minimo'],
            'imagenPrincipal' => $nombreImagen
        ];
        // Actualizar producto
        $resultado = $this->productoModel->actualizar($datos);
        
        if (isset($resultado['error'])) {
            $_SESSION['error'] = 'Error al actualizar el producto: ' . $resultado['error'];
            $_SESSION['datos_form'] = $_POST;
            header("Location: productos.php?accion=editar&id=" . $_POST['id_producto']);
        } else {
            $_SESSION['exito'] = 'Producto actualizado exitosamente';
            header("Location: productos.php");
        }
        exit();
    }
    /**
     * Cambiar estado (activar/desactivar)
     */
    public function cambiarEstado() {
        //  VERIFICAR PERMISO - Puede usar PRODUCTOS_EDITAR o PRODUCTOS_ELIMINAR
        if (!Auth::esAdministrador() && 
            !Auth::tieneFuncionalidad('PRODUCTOS_EDITAR') && 
            !Auth::tieneFuncionalidad('PRODUCTOS_ELIMINAR')) {
            $_SESSION['error'] = 'No tiene permisos para cambiar el estado de productos';
            header('Location: productos.php');
            exit();
        }
        if (!isset($_GET['id']) || !isset($_GET['estado'])) {
            $_SESSION['error'] = 'Parámetros incompletos';
            header("Location: productos.php");
            exit();
        }
        $idProducto = (int)$_GET['id'];
        $estado = (int)$_GET['estado'];
        $resultado = $this->productoModel->cambiarEstado($idProducto, $estado);
        if (isset($resultado['error'])) {
            $_SESSION['error'] = 'Error al cambiar estado: ' . $resultado['error'];
        } else {
            $mensaje = $estado == 1 ? 'activado' : 'desactivado';
            $_SESSION['exito'] = "Producto $mensaje exitosamente";
        }
        header("Location: productos.php");
        exit();
    }
    /**
     * Obtener productos relacionados
     */
    public function obtenerRelacionados($idProducto) {
        return $this->productoModel->obtenerRelacionados($idProducto);
    }
    /**
     * Obtener producto por ID
     */
    public function obtenerPorId($idProducto) {
    // Solo verificar permisos si es un empleado intentando acceder desde el admin
    if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'empleado') {
        // Es empleado, verificar permisos
        if (!Auth::esAdministrador() && !Auth::tieneFuncionalidad('PRODUCTOS_VER')) {
            return null;
        }
    }
    return $this->productoModel->obtenerPorId($idProducto);
    }
    
    // Para clientes o público, permitir acceso
    /**
     * Subir imagen de producto
     */
    private function subirImagen($archivo) {
        // Validar que se subió un archivo
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'Error al subir el archivo'];
        }
        // Validar tamaño (máximo 2MB)
        if ($archivo['size'] > 2 * 1024 * 1024) {
            return ['error' => 'La imagen no debe superar 2MB'];
        }
        // Validar tipo de archivo por extensión
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($extension, $extensionesPermitidas)) {
            return ['error' => 'Formato de imagen no permitido. Use JPG, PNG o WEBP'];
        }
        // Validar el MIME type del archivo subido
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (!in_array($archivo['type'], $tiposPermitidos)) {
            return ['error' => 'Tipo de archivo no válido'];
        }
        // Generar nombre único
        $nombreArchivo = 'producto_' . uniqid() . '.' . $extension;
        // Crear carpeta si no existe
        $carpetaDestino = __DIR__ . '/../assets/img/productos/';
        if (!file_exists($carpetaDestino)) {
            mkdir($carpetaDestino, 0755, true);
        }
        // Ruta completa
        $rutaDestino = $carpetaDestino . $nombreArchivo;
        // Mover archivo
        if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            return ['error' => 'No se pudo guardar la imagen'];
        }
        return ['nombre' => $nombreArchivo];
    }
    /**
     * Eliminar imagen de producto
     */
    private function eliminarImagen($nombreArchivo) {
        $rutaArchivo = __DIR__ . '/../assets/img/productos/' . $nombreArchivo;
        if (file_exists($rutaArchivo)) {
            unlink($rutaArchivo);
        }
    }
    /**
     * Validar datos del formulario
     */
    private function validarDatos($datos, $esActualizacion = false) {
        $errores = [];  
        // Código de producto (solo en creación)
        if (!$esActualizacion) {
            if (empty($datos['codigo_producto'])) {
                $errores[] = 'El código de producto es obligatorio';
            }
        }
        // Nombre de producto
        if (empty($datos['nombre_producto'])) {
            $errores[] = 'El nombre del producto es obligatorio';
        }
        // Categoría
        if (empty($datos['id_categoria']) || !is_numeric($datos['id_categoria'])) {
            $errores[] = 'Debe seleccionar una categoría válida';
        }
        // Marca
        if (empty($datos['id_marca']) || !is_numeric($datos['id_marca'])) {
            $errores[] = 'Debe seleccionar una marca válida';
        }
        // Unidad de medida
        if (empty($datos['id_unidad']) || !is_numeric($datos['id_unidad'])) {
            $errores[] = 'Debe seleccionar una unidad de medida válida';
        }
        // Precio unitario
        if (empty($datos['precio_unitario']) || !is_numeric($datos['precio_unitario']) || $datos['precio_unitario'] <= 0) {
            $errores[] = 'El precio unitario debe ser mayor a cero';
        }
        // Costo unitario (solo en creación)
        if (!$esActualizacion) {
            if (empty($datos['costo_unitario']) || !is_numeric($datos['costo_unitario']) || $datos['costo_unitario'] < 0) {
                $errores[] = 'El costo unitario debe ser mayor o igual a cero';
            }
        }
        return $errores;
    }
    /**
     * Verificar si el usuario tiene permiso para una acción específica
     */
    public function tienePermiso($accion) {
        switch ($accion) {
            case 'ver':
                return Auth::esAdministrador() || Auth::tieneFuncionalidad('PRODUCTOS_VER');
            case 'crear':
                return Auth::esAdministrador() || Auth::tieneFuncionalidad('PRODUCTOS_CREAR');
            case 'editar':
                return Auth::esAdministrador() || Auth::tieneFuncionalidad('PRODUCTOS_EDITAR');
            case 'eliminar':
                return Auth::esAdministrador() || Auth::tieneFuncionalidad('PRODUCTOS_ELIMINAR');
            default:
                return false;
        }
    }
}
?>