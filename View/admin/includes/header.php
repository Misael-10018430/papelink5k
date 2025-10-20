<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que sea admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'empleado') {
    header('Location: login.php');
    exit;
}

// Variables del empleado
$nombreEmpleado = $_SESSION['nombre_usuario'] ?? 'Administrador';
$rolEmpleado = $_SESSION['rol_usuario'] ?? 'Empleado';

// Obtener la página actual
$paginaActual = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'Panel Admin'; ?> - Papelink</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            display: flex;
            min-height: 100vh;
        }
        
        /* ============================================
           SIDEBAR
           ============================================ */
        
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #2C3E50 0%, #34495e 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 25px 20px;
            background: #FF6347;
            text-align: center;
            border-bottom: 3px solid #e5533d;
        }
        
        .sidebar-header h2 {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        .sidebar-header p {
            font-size: 12px;
            margin-top: 5px;
            opacity: 0.9;
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
            letter-spacing: 1px;
            color: #95a5a6;
            font-weight: 600;
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        
        .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
            border-left-color: #FF6347;
            padding-left: 25px;
        }
        
        .menu-item.active {
            background: rgba(255, 99, 71, 0.2);
            border-left-color: #FF6347;
            font-weight: 600;
        }
        
        .menu-item i {
            margin-right: 12px;
            font-size: 18px;
            width: 20px;
            text-align: center;
        }
        
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 20px;
            background: rgba(0,0,0,0.2);
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: #FF6347;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
        }
        
        .user-details {
            flex: 1;
        }
        
        .user-details strong {
            display: block;
            font-size: 14px;
        }
        
        .user-details small {
            font-size: 11px;
            opacity: 0.8;
        }
        
        .btn-logout {
            display: block;
            width: 100%;
            padding: 10px;
            background: #e74c3c;
            color: white;
            text-align: center;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: background 0.3s;
        }
        
        .btn-logout:hover {
            background: #c0392b;
        }
        
        /* ============================================
           MAIN CONTENT
           ============================================ */
        
        .main-content {
            flex: 1;
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Top Bar */
        .top-bar {
            background: white;
            padding: 20px 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        .top-bar h1 {
            color: #2C3E50;
            font-size: 24px;
        }
        
        .top-bar-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .btn-cliente {
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: background 0.3s;
        }
        
        .btn-cliente:hover {
            background: #2980b9;
        }
        
        /* Contenedor Principal */
        .contenedor-principal {
            flex: 1;
            padding: 30px;
            max-width: 1600px;
            margin: 0 auto;
            width: 100%;
        }
        
        /* ============================================
           RESPONSIVE
           ============================================ */
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .contenedor-principal {
                padding: 15px;
            }
        }
        
        /* ============================================
           UTILIDADES
           ============================================ */
        
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-pendiente {
            background: #FFC107;
            color: #000;
        }
        
        .badge-proceso {
            background: #2196F3;
            color: white;
        }
        
        .badge-completado {
            background: #4CAF50;
            color: white;
        }
        
        .badge-cancelado {
            background: #F44336;
            color: white;
        }
        
        .btn-ver {
            background: #2C3E50;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            transition: background 0.3s;
            display: inline-block;
        }
        
        .btn-ver:hover {
            background: #1a252f;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>PAPELINK</h2>
            <p>Panel Administrativo</p>
        </div>
        
        <nav class="sidebar-menu">
            <!-- Sección Principal -->
            <div class="menu-section">
                <div class="menu-section-title">Principal</div>
                <a href="dashboard.php" class="menu-item <?php echo $paginaActual == 'dashboard' ? 'active' : ''; ?>">
                    <i></i> Dashboard
                </a>
            </div>
            
            <!-- Sección Ventas -->
            <div class="menu-section">
                <div class="menu-section-title">Ventas</div>
                <a href="pedidos.php" class="menu-item <?php echo $paginaActual == 'pedidos' ? 'active' : ''; ?>">
                    <i></i> Pedidos
                </a>
                <a href="envios.php" class="menu-item <?php echo $paginaActual == 'envios' ? 'active' : ''; ?>">
                    <i></i> Envíos
                </a>
                <a href="devoluciones.php" class="menu-item <?php echo $paginaActual == 'devoluciones' ? 'active' : ''; ?>">
                    <i></i> Devoluciones
                </a>
            </div>
            
            <!-- Sección Inventario -->
            <div class="menu-section">
                <div class="menu-section-title">Inventario</div>
                <a href="productos.php" class="menu-item <?php echo $paginaActual == 'productos' ? 'active' : ''; ?>">
                    <i></i> Productos
                </a>
                <a href="inventario.php" class="menu-item <?php echo $paginaActual == 'inventario' ? 'active' : ''; ?>">
                    <i></i> Inventario
                </a>
                <a href="categorias.php" class="menu-item <?php echo $paginaActual == 'categorias' ? 'active' : ''; ?>">
                    <i></i> Categorías
                </a>
                <a href="marcas.php" class="menu-item <?php echo $paginaActual == 'marcas' ? 'active' : ''; ?>">
                    <i></i> Marcas
                </a>
            </div>
            
            <!-- Sección Clientes -->
            <div class="menu-section">
                <div class="menu-section-title">Clientes</div>
                <a href="clientes.php" class="menu-item <?php echo $paginaActual == 'clientes' ? 'active' : ''; ?>">
                    <i></i> Clientes
                </a>
            </div>
            
            <!-- Sección Proveedores -->
            <div class="menu-section">
                <div class="menu-section-title">Compras</div>
                <a href="proveedores.php" class="menu-item <?php echo $paginaActual == 'proveedores' ? 'active' : ''; ?>">
                    <i></i> Proveedores
                </a>
            </div>
            
            <!-- Sección Reportes -->
            <div class="menu-section">
                <div class="menu-section-title">Reportes</div>
                <a href="reportes.php" class="menu-item <?php echo $paginaActual == 'reportes' ? 'active' : ''; ?>">
                    <i></i> Reportes
                </a>
            </div>
        </nav>
        
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($nombreEmpleado, 0, 1)); ?>
                </div>
                <div class="user-details">
                    <strong><?php echo htmlspecialchars($nombreEmpleado); ?></strong>
                    <small><?php echo htmlspecialchars($rolEmpleado); ?></small>
                </div>
            </div>
            <a href="../../controllers/AuthController.php?action=logout" class="btn-logout">
                Cerrar Sesión
            </a>
        </div>
    </aside>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <h1><?php echo $titulo ?? 'Panel Administrativo'; ?></h1>
            <div class="top-bar-actions">
                <a href="../cliente/index.php" class="btn-cliente" target="_blank">
                    Ver Tienda
                </a>
            </div>
        </div>