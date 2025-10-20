<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definir la ruta base
define('BASE_PATH', '/papelink5k/view/cliente/');
// Variables del cliente
$clienteLogueado = $_SESSION['cliente_id'] ?? null;
$nombreCliente = $_SESSION['nombre_cliente'] ?? null;

// Obtener la página actual
$paginaActual = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papelink - <?php echo htmlspecialchars(ucfirst($paginaActual)); ?></title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }
        
        /* ============================================
           HEADER
           ============================================ */
        
        .main-header {
            background-color: #2C3E50;
            padding: 20px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }
        
        /* Menú hamburguesa */
        .menu-toggle {
            display: flex;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            padding: 10px;
            background-color: transparent;
            border: none;
            justify-content: center;
            align-items: center;
            min-width: 45px;
            min-height: 45px;
        }
        
        .menu-toggle span {
            width: 28px;
            height: 3px;
            background-color: white;
            border-radius: 2px;
            transition: all 0.3s;
            display: block;
        }
        
        /* Logo */
        .logo a {
            background-color: #FF6347;
            color: white;
            padding: 10px 20px;
            font-weight: bold;
            font-size: 18px;
            text-decoration: none;
            border-radius: 6px;
            letter-spacing: 1px;
        }
        
        .logo a:hover {
            background-color: #e5533d;
        }
        
        /* Barra de búsqueda */
        .search-bar {
            flex: 1;
            max-width: 600px;
            display: flex;
            gap: 10px;
        }
        
        .search-bar input {
            flex: 1;
            padding: 15px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .search-bar button {
            padding: 10px 20px;
            background-color: #FF6347;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        
        .search-bar button:hover {
            background-color: #e5533d;
        }
        
        /* Iconos */
        .header-icons {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .user-icon, .cart-icon {
            background-color: white;
            width: 45px;
            height: 45px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 22px;
            position: relative;
            transition: transform 0.2s;
        }
        
        .user-icon:hover, .cart-icon:hover {
            transform: translateY(-2px);
        }
        
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #e74c3c;
            color: white;
            font-size: 11px;
            font-weight: bold;
            padding: 3px 7px;
            border-radius: 10px;
            min-width: 20px;
            text-align: center;
        }
        
        /* ============================================
           NAVEGACIÓN SECUNDARIA
           ============================================ */
        
        .nav-secundaria {
            background-color: #34495e;
            padding: 0;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .nav-secundaria-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            gap: 0;
            padding: 0 20px;
        }
        
        .nav-secundaria a {
            color: white;
            text-decoration: none;
            padding: 20px 30px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s;
            font-size: 20px;
            font-weight: 500;
        }
        
        .nav-secundaria a:hover,
        .nav-secundaria a.activo {
            background-color: #FF6347;
        }
        
        /* ============================================
           MENÚ LATERAL
           ============================================ */
        
        .side-menu {
            position: fixed;
            top: 0;
            left: -320px;
            width: 300px;
            height: 100vh;
            background-color: #ffffff;
            transition: left 0.3s ease;
            z-index: 2000;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.5);
            overflow-y: auto;
        }
        
        .side-menu.active {
            left: 0;
        }
        
        .menu-header {
            background-color: #FF6347;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .menu-header h3 {
            color: #ffffff;
            font-size: 20px;
            margin: 0;
        }
        
        .close-menu {
            font-size: 28px;
            color: #ffffff;
            cursor: pointer;
            transition: transform 0.3s;
            background: none;
            border: none;
            padding: 0;
            line-height: 1;
        }
        
        .close-menu:hover {
            transform: rotate(90deg);
        }
        
        .side-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .side-menu ul li {
            border-bottom: 1px solid #f0f0f0;
        }
        
        .side-menu ul li a {
            display: block;
            padding: 15px 20px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 15px;
        }
        
        .side-menu ul li a:hover {
            background-color: #f8f9fa;
            color: #FF6347;
            padding-left: 30px;
        }
        
        /* ============================================
           OVERLAY
           ============================================ */
        
        .menu-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1999;
        }
        
        .menu-overlay.active {
            display: block;
        }
        
        /* ============================================
           RESPONSIVE
           ============================================ */
        
        @media (max-width: 768px) {
            .main-header {
                flex-wrap: wrap;
            }
            
            .search-bar {
                order: 3;
                width: 100%;
                max-width: 100%;
            }
            
            .nav-secundaria {
                display: none;
            }
            
            .side-menu {
                width: 280px;
                left: -300px;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header class="main-header">
        <div class="menu-toggle" id="menuToggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
        
        <div class="logo">
            <a href="<?php echo BASE_PATH; ?>index.php">PAPELINK</a>
        </div>
        
        <div class="search-bar">
            <form method="GET" action="<?php echo BASE_PATH; ?>productos.php" style="display: flex; gap: 10px; width: 100%;">
                <input type="text" name="busqueda" placeholder="Buscar productos..." value="<?php echo htmlspecialchars($_GET['busqueda'] ?? ''); ?>">
                <button type="submit">Buscar</button>
            </form>
        </div>
        
        <div class="header-icons">
            <a href="<?php echo $clienteLogueado ? BASE_PATH . 'mi_cuenta.php' : BASE_PATH . 'login.php'; ?>" class="user-icon">👤</a>
            <a href="<?php echo BASE_PATH; ?>carrito.php" class="cart-icon">
                🛒 <span class="cart-count" id="carritoBadge" style="display: none;">0</span>
            </a>
        </div>
    </header>
    
    <!-- NAVEGACIÓN SECUNDARIA -->
    <nav class="nav-secundaria">
        <div class="nav-secundaria-container">
            <a href="<?php echo BASE_PATH; ?>index.php" class="<?php echo $paginaActual == 'index' ? 'activo' : ''; ?>">
                🏠 Inicio
            </a>
            <a href="<?php echo BASE_PATH; ?>productos.php" class="<?php echo $paginaActual == 'productos' ? 'activo' : ''; ?>">
                📦 Todos los productos
            </a>
            <a href="<?php echo BASE_PATH; ?>categorias.php">
                📁 Categorías
            </a>
            <a href="<?php echo BASE_PATH; ?>marcas.php">
                🏷️ Marcas
            </a>
        </div>
    </nav>
    
    <!-- MENÚ LATERAL -->
    <div class="menu-overlay" id="menuOverlay"></div>
    <nav class="side-menu" id="sideMenu">
        <div class="menu-header">
            <h3>MENÚ</h3>
            <span class="close-menu" id="closeMenu">✕</span>
        </div>
        <ul>
            <li><a href="<?php echo BASE_PATH; ?>index.php">Inicio</a></li>
            <li><a href="<?php echo BASE_PATH; ?>productos.php">Productos</a></li>
            <li><a href="<?php echo BASE_PATH; ?>categorias.php">Categorías</a></li>
            <li><a href="<?php echo BASE_PATH; ?>marcas.php">Marcas</a></li>
            <li><a href="<?php echo BASE_PATH; ?>carrito.php">Mi Carrito</a></li>
            <?php if ($clienteLogueado): ?>
                <li><a href="<?php echo BASE_PATH; ?>mis_pedidos.php">Mis Pedidos</a></li>
                <li><a href="<?php echo BASE_PATH; ?>mi_cuenta.php">Mi Cuenta</a></li>
                <li><a href="../../controllers/AuthController.php?action=logout">Cerrar Sesión</a></li>
            <?php else: ?>
                <li><a href="<?php echo BASE_PATH; ?>login.php">Iniciar Sesión</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    
    <script>
        // Menú lateral
        const menuToggle = document.getElementById('menuToggle');
        const sideMenu = document.getElementById('sideMenu');
        const closeMenu = document.getElementById('closeMenu');
        const menuOverlay = document.getElementById('menuOverlay');

        menuToggle.addEventListener('click', () => {
            sideMenu.classList.add('active');
            menuOverlay.classList.add('active');
        });

        closeMenu.addEventListener('click', () => {
            sideMenu.classList.remove('active');
            menuOverlay.classList.remove('active');
        });

        menuOverlay.addEventListener('click', () => {
            sideMenu.classList.remove('active');
            menuOverlay.classList.remove('active');
        });

        // Actualizar contador del carrito
        <?php if ($clienteLogueado): ?>
        function actualizarContadorCarrito() {
            fetch('../../controllers/CarritoController.php?action=contar')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('carritoBadge');
                    if (badge && data.cantidad > 0) {
                        badge.textContent = data.cantidad;
                        badge.style.display = 'block';
                    }
                })
                .catch(error => console.log('Error:', error));
        }
        actualizarContadorCarrito();
        <?php endif; ?>
    </script>
</body>
</html>