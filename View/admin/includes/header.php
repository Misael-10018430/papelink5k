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
$emailEmpleado = $_SESSION['email_usuario'] ?? '';
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
        /* ============================================
           RESET Y BASE
           ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            min-height: 100vh;
        }
        
        /* ============================================
           SIDEBAR
           ============================================ */
        .sidebar {
            width: 220px;
            background-color: #34495e;
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }
        
        .logo-admin {
            background-color: #FF6347;
            padding: 25px 20px;
            text-align: center;
        }
        
        .logo-admin h1 {
            margin: 0;
            font-size: 22px;
            letter-spacing: 2px;
            font-weight: bold;
            color: white;
        }
        
        .logo-admin p {
            margin: 8px 0 0 0;
            font-size: 11px;
            color: rgba(255,255,255,0.9);
            font-weight: normal;
        }
        
        /* Menú */
        .menu-admin {
            padding: 25px 0;
        }
        
        .menu-admin a {
            display: block;
            padding: 14px 25px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
            font-size: 14px;
        }
        
        .menu-admin a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: #FF6347;
        }
        
        .menu-admin a.activo {
            background-color: rgba(255, 99, 71, 0.15);
            border-left-color: #FF6347;
            font-weight: bold;
        }
        
        .menu-seccion {
            margin: 30px 0 15px 0;
        }
        
        .menu-seccion h3 {
            padding: 8px 25px;
            font-size: 10px;
            text-transform: uppercase;
            color: #95a5a6;
            margin: 0;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        /* Usuario en Sidebar */
        .user-admin-sidebar {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 20px;
            background-color: rgba(0,0,0,0.2);
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .btn-cerrar-sesion {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #e74c3c;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
            transition: background 0.3s;
        }
        
        .btn-cerrar-sesion:hover {
            background-color: #c0392b;
        }
        
        /* ============================================
           CONTENIDO PRINCIPAL
           ============================================ */
        .contenido-admin {
            margin-left: 220px;
            flex: 1;
            min-height: 100vh;
            width: calc(100% - 220px);
        }
        
        /* Header Superior con datos del usuario */
        .top-bar {
            background-color: white;
            padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .user-info-top {
            text-align: right;
        }
        
        .user-info-top strong {
            display: block;
            color: #2C3E50;
            font-size: 14px;
            margin-bottom: 3px;
        }
        
        .user-info-top span {
            display: block;
            color: #7f8c8d;
            font-size: 12px;
        }
        
        .contenedor-principal {
            padding: 0 30px 30px 30px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .titulo-pagina {
            color: #2C3E50;
            margin-bottom: 25px;
            font-size: 26px;
            font-weight: normal;
        }
        
        /* ============================================
           MENSAJES
           ============================================ */
        .mensaje-exito {
            background-color: #d4edda;
            color: #155724;
            padding: 14px 18px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 3px solid #28a745;
            font-size: 14px;
        }
        
        .mensaje-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 14px 18px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 3px solid #dc3545;
            font-size: 14px;
        }
        
        /* ============================================
           TARJETAS Y GRID
           ============================================ */
        .tarjeta {
            background-color: white;
            padding: 25px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            border: 1px solid #e0e0e0;
        }
        
        .tarjeta h2 {
            color: #2C3E50;
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: 600;
        }
        
        .tarjeta h3 {
            color: #2C3E50;
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: 600;
        }
        
        .grid {
            display: grid;
            gap: 20px;
        }
        
        .grid-2 {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .grid-3 {
            grid-template-columns: repeat(3, 1fr);
        }
        
        .grid-4 {
            grid-template-columns: repeat(4, 1fr);
        }
        
        /* ============================================
           FORMULARIOS
           ============================================ */
        .form-group {
            margin-bottom: 18px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 7px;
            color: #2C3E50;
            font-weight: 600;
            font-size: 13px;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #FF6347;
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .form-group small {
            display: block;
            margin-top: 5px;
            color: #7f8c8d;
            font-size: 12px;
        }
        
        /* ============================================
           BOTONES
           ============================================ */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            text-align: center;
        }
        
        .btn-naranja {
            background-color: #FF6347;
            color: white;
        }
        
        .btn-naranja:hover {
            background-color: #e5533d;
        }
        
        .btn-blanco {
            background-color: white;
            color: #333;
            border: 1px solid #ddd;
        }
        
        .btn-blanco:hover {
            background-color: #f8f9fa;
            border-color: #bbb;
        }
        
        .btn-verde {
            background-color: #27ae60;
            color: white;
        }
        
        .btn-verde:hover {
            background-color: #229954;
        }
        
        .btn-rojo {
            background-color: #e74c3c;
            color: white;
        }
        
        .btn-rojo:hover {
            background-color: #c0392b;
        }
        
        .btn-azul {
            background-color: #3498db;
            color: white;
        }
        
        .btn-azul:hover {
            background-color: #2980b9;
        }
        
        /* ============================================
           TABLAS
           ============================================ */
        .tabla {
            width: 100%;
            background-color: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            border-collapse: collapse;
            border: 1px solid #e0e0e0;
        }
        
        .tabla thead {
            background-color: #34495e;
            color: white;
        }
        
        .tabla th {
            padding: 14px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
        }
        
        .tabla tbody tr {
            border-bottom: 1px solid #eee;
            transition: background 0.2s;
        }
        
        .tabla tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .tabla td {
            padding: 14px;
            font-size: 13px;
        }
        
        /* ============================================
           BADGES
           ============================================ */
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-verde {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .badge-rojo {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .badge-amarillo {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .badge-azul {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        /* ============================================
           FILTROS
           ============================================ */
        .filtros {
            background-color: white;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            border: 1px solid #e0e0e0;
        }
        
        /* ============================================
           ACCIONES
           ============================================ */
        .acciones {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        /* ============================================
           MÉTRICAS - DISEÑO SIMPLE CON BORDE NARANJA
           ============================================ */
        .tarjeta-metrica {
            background-color: white;
            border: 2px solid #FF6347;
            color: #2C3E50;
            padding: 25px;
            border-radius: 6px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        
        .tarjeta-metrica h3 {
            font-size: 32px;
            margin-bottom: 8px;
            color: #FF6347;
            font-weight: bold;
        }
        
        .tarjeta-metrica p {
            font-size: 13px;
            color: #7f8c8d;
            margin: 0;
            font-weight: normal;
        }
        
        /* ============================================
           ALERTAS
           ============================================ */
        .alerta {
            padding: 14px;
            border-radius: 4px;
            margin-bottom: 15px;
            border-left: 3px solid;
            font-size: 13px;
        }
        
        .alerta-verde {
            background-color: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }
        
        .alerta-amarilla {
            background-color: #fff3cd;
            color: #856404;
            border-left-color: #ffc107;
        }
        
        .alerta-roja {
            background-color: #f8d7da;
            color: #721c24;
            border-left-color: #dc3545;
        }
        
        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 1024px) {
            .grid-4 {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .contenido-admin {
                margin-left: 0;
                width: 100%;
            }
            
            .contenedor-principal {
                padding: 0 15px 15px 15px;
            }
            
            .top-bar {
                padding: 12px 15px;
            }
            
            .grid-2,
            .grid-3,
            .grid-4 {
                grid-template-columns: 1fr;
            }
            
            .tabla {
                font-size: 12px;
            }
            
            .tabla th,
            .tabla td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="logo-admin">
            <h1>PAPELINK</h1>
            <p>Panel Administrativo</p>
        </div>
        
        <nav class="menu-admin">
            <a href="dashboard.php" class="<?php echo $paginaActual == 'dashboard' ? 'activo' : ''; ?>">
                Dashboard
            </a>
            
            <div class="menu-seccion">
                <h3>Ventas</h3>
                <a href="pedidos.php" class="<?php echo $paginaActual == 'pedidos' || $paginaActual == 'pedido_detalle' ? 'activo' : ''; ?>">
                    Pedidos
                </a>
                <a href="envios.php" class="<?php echo $paginaActual == 'envios' || $paginaActual == 'envio_detalle' ? 'activo' : ''; ?>">
                    Envíos
                </a>
                <a href="devoluciones.php" class="<?php echo $paginaActual == 'devoluciones' || $paginaActual == 'devolucion_detalle' ? 'activo' : ''; ?>">
                    Devoluciones
                </a>
            </div>
            
            <div class="menu-seccion">
                <h3>Inventario</h3>
                <a href="productos.php" class="<?php echo $paginaActual == 'productos' ? 'activo' : ''; ?>">
                    Productos
                </a>
                <a href="inventario.php" class="<?php echo $paginaActual == 'inventario' ? 'activo' : ''; ?>">
                    Inventario
                </a>
                <a href="categorias.php" class="<?php echo $paginaActual == 'categorias' ? 'activo' : ''; ?>">
                    Categorías
                </a>
                <a href="marcas.php" class="<?php echo $paginaActual == 'marcas' ? 'activo' : ''; ?>">
                    Marcas
                </a>
            </div>
            
            <div class="menu-seccion">
                <h3>Gestión</h3>
                <a href="clientes.php" class="<?php echo $paginaActual == 'clientes' ? 'activo' : ''; ?>">
                    Clientes
                </a>
                <a href="proveedores.php" class="<?php echo $paginaActual == 'proveedores' ? 'activo' : ''; ?>">
                    Proveedores
                </a>
            </div>
        </nav>
        
        <div class="user-admin-sidebar">
            <a href="../../controllers/AuthController.php?action=logout" class="btn-cerrar-sesion">
                Cerrar Sesión
            </a>
        </div>
    </aside>
    
    <!-- CONTENIDO PRINCIPAL -->
    <main class="contenido-admin">
        <!-- Top Bar con Información del Usuario -->
        <div class="top-bar">
            <div></div>
            <div class="user-info-top">
                <strong><?php echo htmlspecialchars($nombreEmpleado); ?></strong>
                <span><?php echo htmlspecialchars($emailEmpleado); ?> | <?php echo htmlspecialchars($rolEmpleado); ?></span>
            </div>
        </div>
        
        <div class="contenedor-principal">