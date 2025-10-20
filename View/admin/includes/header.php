<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definir BASE_PATH para el admin (solo si no existe)
if (!defined('ADMIN_BASE_PATH')) {
    define('ADMIN_BASE_PATH', '/papelink5k/view/admin/');
}

// Verificar que sea un empleado logueado
$empleadoLogueado = $_SESSION['empleado_id'] ?? null;
$nombreEmpleado = $_SESSION['nombre_empleado'] ?? null;
$rolEmpleado = $_SESSION['rol_empleado'] ?? null;

// Si no está logueado, redirigir al login del admin
if (!$empleadoLogueado) {
    header('Location: ' . ADMIN_BASE_PATH . 'login.php');
    exit;
}

// Obtener la página actual
$paginaActual = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papelink Admin - <?php echo htmlspecialchars(ucfirst($paginaActual)); ?></title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f7fa;
            color: #2c3e50;
            display: flex;
            min-height: 100vh;
        }
        
        /* ============================================
           SIDEBAR
           ============================================ */
        
        .admin-sidebar {
            width: 260px;
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 25px 20px;
            background-color: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-logo {
            font-size: 24px;
            font-weight: bold;
            color: #FF6347;
            text-decoration: none;
            display: block;
            text-align: center;
            padding: 10px;
            background-color: white;
            border-radius: 8px;
            transition: transform 0.3s;
        }
        
        .sidebar-logo:hover {
            transform: scale(1.05);
        }
        
        .sidebar-user {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FF6347, #ff8c7a);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            margin: 0 auto 10px;
            color: white;
        }
        
        .user-name {
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 16px;
        }
        
        .user-role {
            font-size: 12px;
            color: #bdc3c7;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-section {
            margin-bottom: 30px;
        }
        
        .menu-section-title {
            padding: 10px 20px;
            font-size: 11px;
            text-transform: uppercase;
            color: #95a5a6;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .menu-item:hover {
            background-color: rgba(255,255,255,0.1);
            border-left-color: #FF6347;
            padding-left: 25px;
        }
        
        .menu-item.active {
            background-color: rgba(255,99,71,0.2);
            border-left-color: #FF6347;
            color: white;
        }
        
        .menu-item-icon {
            font-size: 18px;
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }
        
        .menu-item-text {
            flex: 1;
            font-size: 14px;
        }
        
        .menu-logout {
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: 20px;
            padding-top: 20px;
        }
        
        /* ============================================
           MAIN CONTENT
           ============================================ */
        
        .admin-main {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .admin-header {
            background-color: white;
            padding: 20px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .page-title {
            font-size: 24px;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .btn-header {
            padding: 8px 16px;
            background-color: #ecf0f1;
            color: #2c3e50;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-header:hover {
            background-color: #bdc3c7;
        }
        
        .btn-primary {
            background-color: #FF6347;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #e5533d;
        }
        
        .admin-content {
            padding: 30px;
            flex: 1;
        }
        
        /* ============================================
           BREADCRUMBS
           ============================================ */
        
        .breadcrumbs {
            background-color: white;
            padding: 15px 30px;
            border-bottom: 1px solid #ecf0f1;
            font-size: 14px;
            color: #7f8c8d;
        }
        
        .breadcrumbs a {
            color: #FF6347;
            text-decoration: none;
        }
        
        .breadcrumbs a:hover {
            text-decoration: underline;
        }
        
        /* ============================================
           RESPONSIVE
           ============================================ */
        
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-260px);
                transition: transform 0.3s;
            }
            
            .admin-sidebar.active {
                transform: translateX(0);
            }
            
            .admin-main {
                margin-left: 0;
            }
            
            .mobile-menu-toggle {
                display: block;
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 1001;
                background-color: #2c3e50;
                color: white;
                border: none;
                padding: 10px;
                border-radius: 6px;
                cursor: pointer;
            }
        }
        
        .mobile-menu-toggle {
            display: none;
        }
    </style>
</head>
<body>
    <!-- SIDEBAR -->
    <aside class="admin-sidebar" id="adminSidebar">
        <!-- Logo -->
        <div class="sidebar-header">
            <a href="<?php echo ADMIN_BASE_PATH; ?>dashboard.php" class="sidebar-logo">
                PAPELINK
            </a>
        </div>
        
        <!-- Info del Usuario -->
        <div class="sidebar-user">
            <div class="user-avatar">
                <?php echo strtoupper(substr($nombreEmpleado ?? 'A', 0, 1)); ?>
            </div>
            <div class="user-name"><?php echo htmlspecialchars($nombreEmpleado ?? 'Empleado'); ?></div>
            <div class="user-role"><?php echo htmlspecialchars($rolEmpleado ?? 'Staff'); ?></div>
        </div>
        
        <!-- Menú -->
        <nav class="sidebar-menu">
            <!-- Sección Principal -->
            <div class="menu-section">
                <div class="menu-section-title">Principal</div>
                <a href="<?php echo ADMIN_BASE_PATH; ?>dashboard.php" 
                   class="menu-item <?php echo $paginaActual == 'dashboard' ? 'active' : ''; ?>">
                    <span class="menu-item-icon"></span>
                    <span class="menu-item-text">Dashboard</span>
                </a>
            </div>
            
            <!-- Sección Gestión -->
            <div class="menu-section">
                <div class="menu-section-title">Gestión</div>
                <a href="<?php echo ADMIN_BASE_PATH; ?>productos.php" 
                   class="menu-item <?php echo $paginaActual == 'productos' ? 'active' : ''; ?>">
                    <span class="menu-item-icon">📦</span>
                    <span class="menu-item-text">Productos</span>
                </a>
                <a href="<?php echo ADMIN_BASE_PATH; ?>categorias.php" 
                   class="menu-item <?php echo $paginaActual == 'categorias' ? 'active' : ''; ?>">
                    <span class="menu-item-text">Categorías</span>
                </a>
                <a href="<?php echo ADMIN_BASE_PATH; ?>marcas.php" 
                   class="menu-item <?php echo $paginaActual == 'marcas' ? 'active' : ''; ?>">
                    <span class="menu-item-text">Marcas</span>
                </a>
            </div>
            
            <!-- Sección Ventas -->
            <div class="menu-section">
                <div class="menu-section-title">Ventas</div>
                <a href="<?php echo ADMIN_BASE_PATH; ?>pedidos.php" 
                   class="menu-item <?php echo $paginaActual == 'pedidos' ? 'active' : ''; ?>">
                    <span class="menu-item-text">Pedidos</span>
                </a>
                <a href="<?php echo ADMIN_BASE_PATH; ?>clientes.php" 
                   class="menu-item <?php echo $paginaActual == 'clientes' ? 'active' : ''; ?>">
                    <span class="menu-item-text">Clientes</span>
                </a>
            </div>
            
            <!-- Sección Sistema -->
            <div class="menu-section">
                <div class="menu-section-title">Sistema</div>
                <a href="<?php echo ADMIN_BASE_PATH; ?>usuarios.php" 
                   class="menu-item <?php echo $paginaActual == 'usuarios' ? 'active' : ''; ?>">
                    <span class="menu-item-text">Usuarios</span>
                </a>
                <a href="<?php echo ADMIN_BASE_PATH; ?>reportes.php" 
                   class="menu-item <?php echo $paginaActual == 'reportes' ? 'active' : ''; ?>">
                    <span class="menu-item-text">Reportes</span>
                </a>
            </div>
            
            <!-- Logout -->
            <div class="menu-logout">
                <a href="../../controllers/AuthController.php?action=logout_admin" class="menu-item">
                    <span class="menu-item-text">Cerrar Sesión</span>
                </a>
            </div>
        </nav>
    </aside>
    
    <!-- MAIN CONTENT -->
    <main class="admin-main">
        <!-- Header superior -->
        <header class="admin-header">
            <h1 class="page-title">
                <?php 
                $titulos = [
                    'dashboard' => 'Dashboard Principal',
                    'productos' => ' Gestión de Productos',
                    'categorias' => ' Gestión de Categorías',
                    'marcas' => ' Gestión de Marcas',
                    'pedidos' => ' Gestión de Pedidos',
                    'clientes' => ' Gestión de Clientes',
                    'usuarios' => ' Gestión de Usuarios',
                    'reportes' => ' Reportes y Estadísticas'
                ];
                echo $titulos[$paginaActual] ?? ucfirst($paginaActual);
                ?>
            </h1>
            
            <div class="header-actions">
                <a href="../cliente/index.php" class="btn-header" target="_blank">
                     Ver Tienda
                </a>
                <span class="btn-header">
                    <?php echo date('d/m/Y'); ?>
                </span>
            </div>
        </header>
        
        <!-- Breadcrumbs -->
        <div class="breadcrumbs">
            <a href="<?php echo ADMIN_BASE_PATH; ?>dashboard.php">Inicio</a>
            <span> / </span>
            <span><?php echo ucfirst($paginaActual); ?></span>
        </div>
        
        <!-- Contenido de la página -->
        <div class="admin-content">