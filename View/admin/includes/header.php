<?php
// Cargar configuración global (incluye session_start automático)
require_once __DIR__ . '/../../../config/config.php';

// Cargar sistema de autenticación
require_once __DIR__ . '/../../../config/Auth.php';

// Obtener la página actual
$paginaActual = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'Panel Administrativo - Papelink'; ?></title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<style>
    /* Reset general */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f5f5f5;
        overflow-x: hidden;
    }
    
    /* ===== SIDEBAR ===== */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 250px;
        height: 100vh;
        background: #2c3e50;
        color: white;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }
    
    .logo-admin {
        padding: 20px;
        text-align: center;
        background: #FF6347;
        border-bottom: 3px solid #e5533d;
    }
    
    .logo-admin h1 {
        margin: 0;
        font-size: 24px;
        font-weight: bold;
        letter-spacing: 1px;
        color: white;
    }
    
    .logo-admin p {
        margin: 5px 0 0;
        font-size: 12px;
        opacity: 0.9;
        color: white;
    }
    
    .menu-admin {
        padding: 0;
    }
    
    .menu-admin a {
        display: block;
        padding: 14px 20px;
        color: white;
        text-decoration: none;
        transition: all 0.3s;
        border-left: 3px solid transparent;
        font-size: 14px;
    }
    
    .menu-admin a:hover {
        background: #34495e;
        border-left-color: #FF6347;
        padding-left: 25px;
    }
    
    .menu-admin a.activo {
        background: #34495e;
        border-left-color: #FF6347;
        font-weight: bold;
    }
    
    .menu-seccion {
        padding: 20px 20px 8px;
        margin-top: 15px;
    }
    
    .menu-seccion h3 {
        font-size: 11px;
        font-weight: bold;
        color: #95a5a6;
        letter-spacing: 1.5px;
        margin: 0;
        text-transform: uppercase;
        border-bottom: 1px solid #34495e;
        padding-bottom: 8px;
    }
    
    .sidebar::-webkit-scrollbar {
        width: 6px;
    }
    
    .sidebar::-webkit-scrollbar-track {
        background: #2c3e50;
    }
    
    .sidebar::-webkit-scrollbar-thumb {
        background: #34495e;
        border-radius: 3px;
    }
    
    /* ===== TOPBAR ===== */
    .topbar {
        position: fixed;
        top: 0;
        left: 250px;
        right: 0;
        height: 60px;
        background: white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        z-index: 999;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 30px;
    }
    
    .usuario-info {
        display: flex;
        flex-direction: column;
    }
    
    .usuario-nombre {
        font-weight: 600;
        color: #2C3E50;
        font-size: 14px;
    }
    
    .usuario-rol {
        font-size: 12px;
        color: #7f8c8d;
    }
    
    .btn-cerrar-sesion {
        background-color: #e74c3c;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s;
        font-size: 14px;
    }
    
    .btn-cerrar-sesion:hover {
        background-color: #c0392b;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    /* ===== CONTENIDO PRINCIPAL ===== */
    .contenido-principal {
        margin-left: 250px;
        margin-top: 60px;
        padding: 25px;
        min-height: calc(100vh - 60px);
        background-color: #f5f5f5;
    }
    
    /* ===== MENSAJES ===== */
    .mensaje-exito {
        background-color: #d4edda;
        color: #155724;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #28a745;
    }
    
    .mensaje-error {
        background-color: #f8d7da;
        color: #721c24;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #dc3545;
    }
    
    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-250px);
        }
        
        .topbar,
        .contenido-principal {
            margin-left: 0;
            left: 0;
        }
    }
</style>







<!-- ========== SIDEBAR ========== -->
<div class="sidebar">
    <div class="logo-admin">
        <h1>PAPELINK</h1>
        <p>Panel Administrativo</p>
        <small style="display: block; margin-top: 10px; color: white; font-size: 0.85em;">
            <?php echo htmlspecialchars($_SESSION['nombre_usuario'] ?? 'Usuario'); ?>
            <br>
            <span style="font-size: 0.8em; opacity: 0.8;">
                <?php echo htmlspecialchars($_SESSION['rol_usuario'] ?? 'Empleado'); ?>
            </span>
        </small>
    </div>
    <div class="menu-admin">
        <!-- Dashboard - Todos tienen acceso -->
        <a href="dashboard.php" class="<?php echo ($paginaActual ?? '') == 'dashboard' ? 'activo' : ''; ?>">
            Dashboard
        </a>
        <!-- VENTAS -->
        <?php if (Auth::esAdministrador() || 
                  Auth::tieneAlgunaFuncionalidad(['PEDIDOS_VER', 'PEDIDOS_GESTIONAR', 'ENVIOS_VER'])): ?>
        <div class="menu-seccion">
            <h3>VENTAS</h3>
        </div>
        <?php if (Auth::esAdministrador() || 
                  Auth::tieneAlgunaFuncionalidad(['PEDIDOS_VER', 'PEDIDOS_GESTIONAR'])): ?>
        <a href="pedidos.php" class="<?php echo ($paginaActual ?? '') == 'pedidos' ? 'activo' : ''; ?>">
            Pedidos
        </a>
        <?php endif; ?>
        <?php if (Auth::esAdministrador() || Auth::tieneFuncionalidad('ENVIOS_VER')): ?>
        <a href="envios.php" class="<?php echo ($paginaActual ?? '') == 'envios' ? 'activo' : ''; ?>">
            Envíos
        </a>
        <?php endif; ?>   
        <?php if (Auth::esAdministrador() || Auth::tieneFuncionalidad('DEVOLUCIONES_VER')): ?>
        <a href="devoluciones.php" class="<?php echo ($paginaActual ?? '') == 'devoluciones' ? 'activo' : ''; ?>">
            Devoluciones
        </a>
        <?php endif; ?>
        <?php endif; ?>
        









        <!-- ========== INVENTARIO ========== -->
        <?php if (Auth::esAdministrador() || 
                  Auth::tieneAlgunaFuncionalidad(['PRODUCTOS_VER', 'PRODUCTOS_CREAR', 'PRODUCTOS_EDITAR', 'INVENTARIO_VER', 'INVENTARIO_AJUSTAR'])): ?>
        <div class="menu-seccion">
            <h3>INVENTARIO</h3>
        </div>
        <?php if (Auth::esAdministrador() || 
                  Auth::tieneAlgunaFuncionalidad(['PRODUCTOS_VER', 'PRODUCTOS_CREAR', 'PRODUCTOS_EDITAR'])): ?>
        <a href="productos.php" class="<?php echo ($paginaActual ?? '') == 'productos' ? 'activo' : ''; ?>">
           Productos
        </a>
        <?php endif; ?>
        <?php if (Auth::esAdministrador() || 
                  Auth::tieneAlgunaFuncionalidad(['INVENTARIO_VER', 'INVENTARIO_AJUSTAR'])): ?>
        <a href="inventario.php" class="<?php echo ($paginaActual ?? '') == 'inventario' ? 'activo' : ''; ?>">
            Inventario
        </a>
        <?php endif; ?>
        <?php if (Auth::esAdministrador() || Auth::tieneFuncionalidad('PRODUCTOS_EDITAR')): ?>
        <a href="categorias.php" class="<?php echo ($paginaActual ?? '') == 'categorias' ? 'activo' : ''; ?>">
             Categorías
        </a>
        <a href="marcas.php" class="<?php echo ($paginaActual ?? '') == 'marcas' ? 'activo' : ''; ?>">
            Marcas
        </a>
        <?php endif; ?>
        <?php endif; ?>












        <!-- ========== GESTIÓN ========== -->
        <?php if (Auth::esAdministrador() || 
                  Auth::tieneAlgunaFuncionalidad(['CLIENTES_VER', 'CLIENTES_EDITAR', 'PROVEEDORES_VER', 'PROVEEDORES_GESTIONAR'])): ?>
        <div class="menu-seccion">
            <h3>GESTIÓN</h3>
        </div>
        <?php if (Auth::esAdministrador() || 
                  Auth::tieneAlgunaFuncionalidad(['CLIENTES_VER', 'CLIENTES_EDITAR'])): ?>
        <a href="clientes.php" class="<?php echo ($paginaActual ?? '') == 'clientes' ? 'activo' : ''; ?>">
             Clientes
        </a>
        <?php endif; ?>
        <?php if (Auth::esAdministrador() || 
                  Auth::tieneAlgunaFuncionalidad(['PROVEEDORES_VER', 'PROVEEDORES_GESTIONAR'])): ?>
        <a href="proveedores.php" class="<?php echo ($paginaActual ?? '') == 'proveedores' ? 'activo' : ''; ?>">
            Proveedores
        </a>
        <?php endif; ?>
        <?php endif; ?>
        <!-- ========== ADMINISTRACIÓN ========== -->
        <?php if (Auth::esAdministrador() || 
                  Auth::tieneAlgunaFuncionalidad(['USUARIOS_VER', 'USUARIOS_GESTIONAR', 'CONFIGURACION_VER', 'REPORTES_VER'])): ?>
        <div class="menu-seccion">
            <h3>ADMINISTRACIÓN</h3>
        </div>
        <?php if (Auth::esAdministrador()): ?>
        <a href="empleados.php" class="<?php echo ($paginaActual ?? '') == 'empleados' ? 'activo' : ''; ?>">
            Empleados
        </a>
        <?php endif; ?>
        <?php if (Auth::esAdministrador() || 
                  Auth::tieneAlgunaFuncionalidad(['CONFIGURACION_VER', 'CONFIGURACION_EDITAR'])): ?>
        <a href="configuracion.php" class="<?php echo ($paginaActual ?? '') == 'configuracion' ? 'activo' : ''; ?>">
            Configuración
        </a>
        <?php endif; ?>
        <?php if (Auth::esAdministrador() || Auth::tieneFuncionalidad('REPORTES_VER')): ?>
        <a href="reporte.php" class="<?php echo ($paginaActual ?? '') == 'reportes' ? 'activo' : ''; ?>">
            Reportes
        </a>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>












<!-- ========== TOPBAR ========== -->
<div class="topbar">
    <div class="usuario-info">
        <span class="usuario-nombre">
            <?php echo htmlspecialchars($_SESSION['nombre_usuario'] ?? 'Usuario'); ?>
        </span>
        <span class="usuario-rol">
            <?php echo htmlspecialchars($_SESSION['rol_usuario'] ?? 'Empleado'); ?>
        </span>
    </div>
    <a href="../../controllers/AuthController.php?action=logout" class="btn-cerrar-sesion">
        Cerrar Sesión
    </a>
</div>
<!-- ========== CONTENIDO PRINCIPAL ========== -->
<div class="contenido-principal">
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
    <?php if (isset($_SESSION['errores'])): ?>
        <div class="mensaje-error">
            <ul style="margin: 0; padding-left: 20px;">
                <?php foreach ($_SESSION['errores'] as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['errores']); ?>
    <?php endif; ?>