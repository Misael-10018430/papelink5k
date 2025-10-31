<?php
// Cargar configuración global (incluye session_start automático)
require_once __DIR__ . '/../../../config/config.php';

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }
        
        /* ============================================
           HEADER UNIFICADO
           ============================================ */
        
        .main-header {
            background-color: #2C3E50;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-top {
            padding: 20px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
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
            padding: 14px 28px;
            font-weight: bold;
            font-size: 22px;
            text-decoration: none;
            border-radius: 10px;
            letter-spacing: 1.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .logo a:hover {
            background-color: #e5533d;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
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
            padding: 14px 20px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            background-color: rgba(255,255,255,0.9);
            transition: all 0.3s ease;
        }
        
        .search-bar input:focus {
            outline: none;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(255,99,71,0.3);
        }
        
        .search-bar button {
            padding: 14px 28px;
            background-color: #FF6347;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .search-bar button:hover {
            background-color: #e5533d;
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        }
        
        /* Iconos */
        .header-icons {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .user-icon, .cart-icon {
            background-color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 24px;
            position: relative;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .user-icon:hover, .cart-icon:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        }
        
        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #e74c3c;
            color: white;
            font-size: 11px;
            font-weight: bold;
            padding: 3px 7px;
            border-radius: 50%;
            min-width: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        /* Menú de navegación integrado en el header */
        .header-nav {
            background-color: rgba(52, 73, 94, 0.9);
            padding: 0 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: center;
        }
        
        .nav-container ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .nav-container ul li {
            position: relative;
        }
        
        .nav-container ul li a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 18px 25px;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-container ul li a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 3px;
            background-color: #FF6347;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .nav-container ul li a:hover::after,
        .nav-container ul li a.active::after {
            width: 80%;
        }
        
        .nav-container ul li a:hover,
        .nav-container ul li a.active {
            background-color: rgba(255,99,71,0.15);
            color: #FF6347;
        }
        
        /* ============================================
           CARRUSEL DE IMÁGENES
           ============================================ */
        
        .banner-carousel {
            position: relative;
            width: 100%;
            height: 500px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        .carousel-container {
            display: flex;
            width: 100%;
            height: 100%;
            transition: transform 0.5s ease-in-out;
        }
        
        .carousel-slide {
            min-width: 100%;
            height: 100%;
            position: relative;
        }
        
        .carousel-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .carousel-indicators {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
        }
        
        .indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.5);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .indicator.active {
            background-color: #FF6347;
            width: 30px;
            border-radius: 6px;
        }
        
        .carousel-controls {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
        }
        
        .carousel-control {
            background-color: rgba(0,0,0,0.5);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .carousel-control:hover {
            background-color: rgba(0,0,0,0.8);
        }
        
        /* ============================================
           MENÚ LATERAL (MEJORADO)
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
        
        .side-menu .user-info {
            padding: 15px 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .side-menu .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #FF6347;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
        }
        
        .side-menu .user-details h4 {
            margin: 0;
            font-size: 16px;
            color: #333;
        }
        
        .side-menu .user-details p {
            margin: 5px 0 0;
            font-size: 13px;
            color: #666;
        }
        
        .side-menu .menu-section {
            margin-top: 20px;
        }
        
        .side-menu .menu-section-title {
            padding: 10px 20px;
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
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
        
        @media (max-width: 992px) {
            .nav-container {
                padding: 0 10px;
            }
            
            .nav-container ul li a {
                padding: 18px 18px;
                font-size: 15px;
            }
        }
        
        @media (max-width: 768px) {
            .header-top {
                flex-wrap: wrap;
            }
            
            .search-bar {
                order: 3;
                width: 100%;
                max-width: 100%;
            }
            
            .header-nav {
                display: none; /* Ocultamos el menú en pantallas pequeñas */
            }
            
            .side-menu {
                width: 280px;
                left: -300px;
            }
            
            .banner-carousel {
                height: 350px;
            }
        }
        
        /* Añadimos una clase para mostrar el menú en pantallas pequeñas */
        @media (max-width: 768px) {
            .header-nav.mobile-visible {
                display: block;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER UNIFICADO -->
    <header class="main-header">
        <!-- Parte superior del header con logo, búsqueda e iconos -->
        <div class="header-top">
            <div class="menu-toggle" id="menuToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <div class="logo">
                <a href="<?php echo BASE_URL; ?>">PAPELINK</a>
            </div>
            
            <div class="search-bar">
                <form method="GET" action="<?php echo BASE_URL; ?>view/cliente/productos.php" style="display: flex; gap: 10px; width: 100%;">
                    <input type="text" name="busqueda" placeholder="Buscar productos..." value="<?php echo htmlspecialchars($_GET['busqueda'] ?? ''); ?>">
                    <button type="submit">Buscar</button>
                </form>
            </div>
            
            <div class="header-icons">
                <a href="<?php echo $clienteLogueado ? 'mi_cuenta.php' : 'login.php'; ?>" class="user-icon">
                    <i class="fas fa-user"></i>
                </a>
                <a href="carrito.php" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count" id="carritoBadge" style="display: none;">0</span>
                </a>
            </div>
        </div>
        
        <!-- Menú de navegación integrado en el header -->
        <nav class="header-nav">
            <div class="nav-container">
                <ul>
                    <li>
                        <a href="<?php echo BASE_URL; ?>" class="<?php echo $paginaActual == 'index' ? 'active' : ''; ?>">
                            <i class="fas fa-home"></i> Inicio
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>view/cliente/productos.php" class="<?php echo $paginaActual == 'productos' ? 'active' : ''; ?>">
                            <i class="fas fa-box"></i> Todos los Productos
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>view/cliente/categorias.php" class="<?php echo $paginaActual == 'categorias' ? 'active' : ''; ?>">
                            <i class="fas fa-folder"></i> Categorías
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>view/cliente/marcas.php" class="<?php echo $paginaActual == 'marcas' ? 'active' : ''; ?>">
                            <i class="fas fa-tag"></i> Marcas
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    
    <!-- CARRUSEL DE IMÁGENES -->
    <div class="banner-carousel">
        <div class="carousel-container" id="carouselContainer">
            <!-- Slide 1 - Reemplaza la URL con tu imagen -->
            <div class="carousel-slide">
                <img src="https://dismartgt.com/cdn/shop/files/Portada_PROD._ESCOLAR.jpg?v=1621363855&width=1400" alt="Banner 1">
            </div>
            
            <!-- Slide 2 - Reemplaza la URL con tu imagen -->
            <div class="carousel-slide">
                <img src="https://www.comercializadoralumar.com/wp-content/uploads/2021/04/BANNER-PAPELERIA-1.jpg" alt="Banner 2">
            </div>
            
            <!-- Slide 3 - Reemplaza la URL con tu imagen -->
            <div class="carousel-slide">
                <img src="https://panafargo.com/wp-content/uploads/2025/05/Mesa-de-trabajo-18.png" alt="Banner 3">
            </div>
            
            <!-- Slide 4 - Reemplaza la URL con tu imagen -->
            <div class="carousel-slide">
                <img src="https://static.vecteezy.com/system/resources/previews/002/878/209/non_2x/back-to-school-banner-with-realistic-school-supplies-on-chalkboard-background-vector.jpg" alt="Banner 4">
            </div>
            
            <!-- Slide 5 - Reemplaza la URL con tu imagen -->
            <div class="carousel-slide">
                <img src="https://papeleriamarcel-ec.com/wp-content/uploads/2024/08/Banner-papeleria-marcel-2.webp" alt="Banner 5">
            </div>
        </div>
        
        <div class="carousel-indicators">
            <span class="indicator active" data-slide="0"></span>
            <span class="indicator" data-slide="1"></span>
            <span class="indicator" data-slide="2"></span>
            <span class="indicator" data-slide="3"></span>
            <span class="indicator" data-slide="4"></span>
        </div>
        
        <div class="carousel-controls">
            <button class="carousel-control" id="prevSlide">❮</button>
            <button class="carousel-control" id="nextSlide">❯</button>
        </div>
    </div>
    
    <!-- MENÚ LATERAL (MEJORADO) -->
    <div class="menu-overlay" id="menuOverlay"></div>
    <nav class="side-menu" id="sideMenu">
        <div class="menu-header">
            <h3>MENÚ</h3>
            <span class="close-menu" id="closeMenu">✕</span>
        </div>
        
        <?php if ($clienteLogueado): ?>
        <div class="user-info">
            <div class="user-avatar">
                <?php echo strtoupper(substr($nombreCliente, 0, 1)); ?>
            </div>
            <div class="user-details">
                <h4><?php echo htmlspecialchars($nombreCliente); ?></h4>
                <p>Cliente</p>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="menu-section">
            <div class="menu-section-title">Navegación</div>
            <ul>
                <li><a href="mis_pedidos.php"><i class="fas fa-clipboard-list"></i> Mis Pedidos</a></li>
                <li><a href="mi_cuenta.php"><i class="fas fa-user-circle"></i> Mi Cuenta</a></li>
                <li><a href="devoluciones.php"><i class="fas fa-undo"></i> Mis Devoluciones</a></li>
            </ul>
        </div>
        
        <div class="menu-section">
            <div class="menu-section-title">Mi Cuenta</div>
            <ul>
                <li><a href="<?php echo BASE_URL; ?>view/cliente/carrito.php"><i class="fas fa-shopping-cart"></i> Mi Carrito</a></li>
                <?php if ($clienteLogueado): ?>
                    <li><a href="<?php echo BASE_URL; ?>view/cliente/mis_pedidos.php"><i class="fas fa-clipboard-list"></i> Mis Pedidos</a></li>
                    <li><a href="<?php echo BASE_URL; ?>view/cliente/mi_cuenta.php"><i class="fas fa-user-circle"></i> Mi Cuenta</a></li>
                    <li><a href="<?php echo BASE_URL; ?>view/cliente/devoluciones.php"><i class="fas fa-undo"></i> Mis Devoluciones</a></li>
                    <li><a href="<?php echo BASE_URL; ?>controllers/AuthController.php?action=logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                <?php else: ?>
                    <li><a href="<?php echo BASE_URL; ?>view/cliente/login.php"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a></li>
                <?php endif; ?>
            </ul>
        </div>
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
            fetch('<?php echo BASE_URL; ?>controllers/CarritoController.php?action=contar')
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
                
        // Carrusel de imágenes
        document.addEventListener('DOMContentLoaded', function() {
            const carouselContainer = document.getElementById('carouselContainer');
            const indicators = document.querySelectorAll('.indicator');
            const prevButton = document.getElementById('prevSlide');
            const nextButton = document.getElementById('nextSlide');
            const slides = carouselContainer.children;
            let currentSlide = 0;
            const totalSlides = slides.length;
            
            // Función para mostrar el slide actual
            function showSlide(index) {
                if (index < 0) {
                    currentSlide = totalSlides - 1;
                } else if (index >= totalSlides) {
                    currentSlide = 0;
                } else {
                    currentSlide = index;
                }
                
                // Mover el contenedor
                carouselContainer.style.transform = `translateX(-${currentSlide * 100}%)`;
                
                // Actualizar indicadores
                indicators.forEach((indicator, i) => {
                    if (i === currentSlide) {
                        indicator.classList.add('active');
                    } else {
                        indicator.classList.remove('active');
                    }
                });
            }
            
            // Event listeners para los botones
            prevButton.addEventListener('click', () => {
                showSlide(currentSlide - 1);
            });
            
            nextButton.addEventListener('click', () => {
                showSlide(currentSlide + 1);
            });
            
            // Event listeners para los indicadores
            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => {
                    showSlide(index);
                });
            });
            
            // Cambio automático cada 10 segundos
            setInterval(() => {
                showSlide(currentSlide + 1);
            }, 10000);
        });
    </script>
</body>
</html>