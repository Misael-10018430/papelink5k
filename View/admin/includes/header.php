<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si hay usuario logueado (simulado por ahora)
$nombreUsuario = $_SESSION['nombre_usuario'] ?? 'Administrador';
$rolUsuario = $_SESSION['rol_usuario'] ?? 'Administrador';

// Obtener la página actual
$paginaActual = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papelink Admin - <?php echo ucfirst($paginaActual); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f5f5f5;
        }
        
        /* HEADER SUPERIOR NARANJA */
        .header-top {
            background-color: #FF6347;
            color: white;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: bold;
        }
        
        /* NAVEGACIÓN SUPERIOR AZUL OSCURO */
        .nav-superior {
            background-color: #2C3E50;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        
        .logo-admin {
            background-color: #FF6347;
            color: white;
            padding: 15px 25px;
            font-weight: bold;
            font-size: 16px;
        }
        
        .nav-items {
            display: flex;
            gap: 0;
            flex: 1;
            margin-left: 20px;
        }
        
        .nav-items a {
            color: white;
            text-decoration: none;
            padding: 18px 25px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s;
        }
        
        .nav-items a:hover {
            background-color: #34495e;
        }
        
        .user-info {
            color: white;
            padding: 10px 20px;
            font-size: 14px;
        }
        
        /* CONTENEDOR PRINCIPAL */
        .container-principal {
            display: flex;
            min-height: calc(100vh - 100px);
        }
        
        /* MENÚ LATERAL */
        .menu-lateral {
            width: 250px;
            background-color: #2C3E50;
            color: white;
            padding: 20px 0;
        }
        
        .menu-lateral h3 {
            color: white;
            padding: 15px 20px;
            font-size: 14px;
            border-bottom: 1px solid #34495e;
            margin-bottom: 10px;
        }
        
        .menu-lateral a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
            transition: all 0.3s;
        }
        
        .menu-lateral a:hover {
            background-color: #34495e;
            padding-left: 30px;
        }
        
        .menu-lateral a.activo {
            background-color: #FF6347;
            font-weight: bold;
        }
        
        .menu-lateral .submenu {
            padding-left: 20px;
        }
        
        .menu-lateral .submenu a {
            font-size: 14px;
            padding: 10px 20px;
        }
        
        /* CONTENIDO PRINCIPAL */
        .contenido-principal {
            flex: 1;
            padding: 30px;
            background-color: white;
            margin: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        /* TÍTULO DE PÁGINA */
        .titulo-pagina {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #FF6347;
        }
        
        /* BOTONES */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
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
            background-color: #f5f5f5;
        }
        
        .btn-verde {
            background-color: #27ae60;
            color: white;
        }
        
        .btn-rojo {
            background-color: #e74c3c;
            color: white;
        }
        
        /* TABLAS */
        .tabla {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .tabla th {
            background-color: #2C3E50;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        
        .tabla td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        
        .tabla tr:hover {
            background-color: #f5f5f5;
        }
        
        .tabla tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        /* TARJETAS */
        .tarjeta {
            background-color: white;
            border: 2px solid #FF6347;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .tarjeta-metrica {
            background-color: white;
            border: 2px solid #FF6347;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            min-width: 200px;
        }
        
        .tarjeta-metrica h3 {
            font-size: 32px;
            color: #FF6347;
            margin-bottom: 10px;
        }
        
        .tarjeta-metrica p {
            color: #666;
            font-size: 14px;
        }
        
        /* ALERTAS */
        .alerta {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .alerta-amarilla {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
        }
        
        .alerta-roja {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        
        .alerta-verde {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }
        
        /* FORMULARIOS */
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        /* FILTROS */
        .filtros {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: end;
        }
        
        .filtros .form-group {
            margin-bottom: 0;
            min-width: 200px;
        }
        
        /* BADGES */
        .badge {
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .badge-verde {
            background-color: #d4edda;
            color: #155724;
        }
        
        .badge-rojo {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .badge-amarillo {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .badge-azul {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        /* GRID */
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
        
        /* ACCIONES EN TABLA */
        .acciones {
            display: flex;
            gap: 5px;
        }
        
        .acciones .btn {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        /* PAGINACIÓN */
        .paginacion {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        
        .paginacion a {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }
        
        .paginacion a:hover {
            background-color: #FF6347;
            color: white;
        }
        
        .paginacion a.activo {
            background-color: #2C3E50;
            color: white;
        }
        
        /* MENSAJES */
        .mensaje-exito,
        .mensaje-error {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .mensaje-exito {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .mensaje-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* RESPONSIVE */
        @media (max-width: 768px) {
            .container-principal {
                flex-direction: column;
            }
            
            .menu-lateral {
                width: 100%;
            }
            
            .grid-2, .grid-3, .grid-4 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER SUPERIOR NARANJA -->
    <div class="header-top">
        🏠 DASHBOARD PRINCIPAL (/admin/<?php echo $paginaActual; ?>)
    </div>
    
    <!-- NAVEGACIÓN SUPERIOR -->
    <nav class="nav-superior">
        <div class="logo-admin">
            PAPELINK<br>ADMIN
        </div>
        
        <div class="nav-items">
            <a href="dashboard.php">
                📊 Dashboard
            </a>
            <a href="productos.php">
                📦 Productos
            </a>
            <a href="#">
                📋 Pedidos
            </a>
            <a href="#">
                📈 Reportes
            </a>
            <a href="#">
                ⚙️ Configuración
            </a>
            <a href="../../index.php">
                🚪 Salir
            </a>
        </div>
        
        <div class="user-info">
            Bienvenido, <strong><?php echo $nombreUsuario; ?></strong><br>
            (<?php echo $rolUsuario; ?>)
        </div>
    </nav>
    
    <!-- CONTENEDOR PRINCIPAL -->
    <div class="container-principal">
        <!-- MENÚ LATERAL -->
        <aside class="menu-lateral">
            <h3>☰ Navegación</h3>
            
            <a href="dashboard.php" class="<?php echo $paginaActual == 'dashboard' ? 'activo' : ''; ?>">
                📊 Dashboard
            </a>
            
            <h3>☰ Catálogo</h3>
            <div class="submenu">
                <a href="productos.php" class="<?php echo $paginaActual == 'productos' ? 'activo' : ''; ?>">
                    • Productos
                </a>
                <a href="categorias.php" class="<?php echo $paginaActual == 'categorias' ? 'activo' : ''; ?>">
                    • Categorías
                </a>
                <a href="marcas.php" class="<?php echo $paginaActual == 'marcas' ? 'activo' : ''; ?>">
                    • Marcas
                </a>
            </div>
            
            <h3>☰ Inventario</h3>
            <a href="inventario.php" class="<?php echo $paginaActual == 'inventario' ? 'activo' : ''; ?>">
                📦 Inventario
            </a>
            
            <h3>☰ Pedidos & Ventas</h3>
            <div class="submenu">
                <a href="#">• Pedidos</a>
                <a href="#">• Envíos</a>
                <a href="#">• Devoluciones</a>
            </div>
            
            <h3>☰ Compras</h3>
            <div class="submenu">
                <a href="#">• Proveedores</a>
                <a href="#">• Órdenes de Compra</a>
            </div>
            
            <h3>☰ Clientes</h3>
            <a href="#">👥 Clientes</a>
            
            <h3>☰ Empleados</h3>
            <a href="#">👤 Empleados</a>
            
            <h3>☰ Reportes</h3>
            <a href="#">📊 Reportes</a>
            
            <h3>☰ Sistema</h3>
            <a href="#">⚙️ Configuración</a>
        </aside>
        
        <!-- CONTENIDO PRINCIPAL -->
        <main class="contenido-principal">