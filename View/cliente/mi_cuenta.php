<?php
// Incluir el archivo de configuración principal.
require_once __DIR__ . '/../../config/config.php';

// Verificar que el cliente esté logueado
if (!isset($_SESSION['cliente_id'])) {
    $_SESSION['error'] = 'Debe iniciar sesión para acceder';
    header('Location: ' . BASE_URL . 'view/cliente/login.php');
    exit;
}

 $titulo = "Mi Cuenta - Papelink";
include __DIR__ . '/includes/header.php';

// Obtener datos del cliente desde la BD
require_once __DIR__ . '/../../models/Usuario.php';
 $usuarioModel = new Usuario();

// Query para obtener info del cliente
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $query = "SELECT * FROM Clientes WHERE IdCliente = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['cliente_id']]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $_SESSION['error'] = 'Error al cargar datos';
    $cliente = [];
}
?>

<div class="mi-cuenta-container">
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <a href="<?php echo BASE_URL; ?>">Inicio</a> / <span>Mi Cuenta</span>
    </div>

    <!-- Mensajes -->
    <?php if (isset($_SESSION['exito'])): ?>
        <div class="alert alert-success">
            ✓ <?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            ❌ <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="cuenta-layout">
        <!-- Sidebar Menú -->
        <aside class="cuenta-sidebar">
            <div class="perfil-header">
                <div class="avatar">
                    <?php echo strtoupper(substr($cliente['NombreCliente'] ?? 'U', 0, 1)); ?>
                </div>
                <h3><?php echo htmlspecialchars($cliente['NombreCliente'] ?? 'Usuario'); ?></h3>
                <p class="email"><?php echo htmlspecialchars($cliente['Email'] ?? ''); ?></p>
            </div>

            <nav class="menu-cuenta">
                <a href="#inicio" class="menu-item active" onclick="mostrarSeccion('inicio')">
                    <span class="icono"></span> Inicio
                </a>
                <a href="#pedidos" class="menu-item" onclick="mostrarSeccion('pedidos')">
                    <span class="icono"></span> Mis Pedidos
                </a>
                <a href="#datos" class="menu-item" onclick="mostrarSeccion('datos')">
                    <span class="icono"></span> Mis Datos
                </a>
                <a href="#direcciones" class="menu-item" onclick="mostrarSeccion('direcciones')">
                    <span class="icono"></span> Direcciones
                </a>
                <a href="<?php echo BASE_URL; ?>controllers/AuthController.php?action=logout" class="menu-item logout">
                    <span class="icono"></span> Cerrar Sesión
                </a>
            </nav>
        </aside>

        <!-- Contenido Principal -->
        <main class="cuenta-contenido">
            <!-- SECCIÓN: Inicio -->
            <section id="seccion-inicio" class="seccion-cuenta active">
                <h1>¡Bienvenido, <?php echo htmlspecialchars(explode(' ', $cliente['NombreCliente'] ?? 'Usuario')[0]); ?>!</h1>
                
                <div class="resumen-cuenta">
                    <div class="resumen-card">
                        <div class="resumen-icono"></div>
                        <div class="resumen-info">
                            <h3>Mis Pedidos</h3>
                            <p>Ver historial completo</p>
                        </div>
                        <button onclick="mostrarSeccion('pedidos')" class="btn-ver">Ver →</button>
                    </div>

                    <div class="resumen-card">
                        <div class="resumen-icono"></div>
                        <div class="resumen-info">
                            <h3>Información Personal</h3>
                            <p>Actualizar datos</p>
                        </div>
                        <button onclick="mostrarSeccion('datos')" class="btn-ver">Ver →</button>
                    </div>

                    <div class="resumen-card">
                        <div class="resumen-icono"></div>
                        <div class="resumen-info">
                            <h3>Direcciones</h3>
                            <p>Gestionar direcciones</p>
                        </div>
                        <button onclick="mostrarSeccion('direcciones')" class="btn-ver">Ver →</button>
                    </div>
                </div>

                <div class="accesos-rapidos">
                    <h2>Accesos Rápidos</h2>
                    <div class="botones-rapidos">
                        <a href="<?php echo BASE_URL; ?>view/cliente/productos.php" class="btn-rapido">Ver Productos</a>
                        <a href="<?php echo BASE_URL; ?>view/cliente/carrito.php" class="btn-rapido">Mi Carrito</a>
                        <a href="<?php echo BASE_URL; ?>view/cliente/mis_pedidos.php" class="btn-rapido">Mis Pedidos</a>
                    </div>
                </div>
            </section>

            <!-- SECCIÓN: Mis Pedidos -->
            <section id="seccion-pedidos" class="seccion-cuenta">
                <h1>Mis Pedidos</h1>
                <p>Ver el historial completo de pedidos</p>
                <br>
                <a href="<?php echo BASE_URL; ?>view/cliente/mis_pedidos.php" class="btn btn-primary">Ver Todos los Pedidos →</a>
            </section>

            <!-- SECCIÓN: Mis Datos -->
            <section id="seccion-datos" class="seccion-cuenta">
                <h1>Información Personal</h1>
                
                <form method="POST" action="<?php echo BASE_URL; ?>controllers/ClienteController.php?action=actualizar_datos" class="form-datos">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nombre Completo:</label>
                            <input type="text" name="nombre" value="<?php echo htmlspecialchars($cliente['NombreCliente'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($cliente['Email'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Teléfono:</label>
                            <input type="tel" name="telefono" value="<?php echo htmlspecialchars($cliente['Telefono'] ?? ''); ?>">
                        </div>

                        <div class="form-group full-width">
                            <label>Dirección:</label>
                            <textarea name="direccion" rows="3"><?php echo htmlspecialchars($cliente['Direccion'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>

                <hr style="margin: 40px 0;">

                <h2>Cambiar Contraseña</h2>
                <form method="POST" action="<?php echo BASE_URL; ?>controllers/ClienteController.php?action=cambiar_contrasena" class="form-password">
                    <div class="form-group">
                        <label>Contraseña Actual:</label>
                        <input type="password" name="password_actual" required>
                    </div>

                    <div class="form-group">
                        <label>Nueva Contraseña:</label>
                        <input type="password" name="password_nueva" required>
                    </div>

                    <div class="form-group">
                        <label>Confirmar Nueva Contraseña:</label>
                        <input type="password" name="password_confirmar" required>
                    </div>

                    <button type="submit" class="btn btn-secondary">Cambiar Contraseña</button>
                </form>
            </section>

            <!-- SECCIÓN: Direcciones -->
            <section id="seccion-direcciones" class="seccion-cuenta">
                <h1>📍 Mis Direcciones</h1>
                
                <div class="direccion-principal">
                    <h3>Dirección Principal</h3>
                    <p><?php echo htmlspecialchars($cliente['Direccion'] ?? 'No hay dirección registrada'); ?></p>
                    <button onclick="mostrarSeccion('datos')" class="btn btn-secondary">Editar Dirección</button>
                </div>
            </section>
        </main>
    </div>
</div>

<style>
.mi-cuenta-container {
    max-width: 1400px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.breadcrumbs {
    margin-bottom: 2rem;
    color: #666;
}

.breadcrumbs a {
    color: #FF6347;
    text-decoration: none;
}

.cuenta-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 2rem;
}

/* Sidebar */
.cuenta-sidebar {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    height: fit-content;
    position: sticky;
    top: 100px;
}

.perfil-header {
    text-align: center;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 2px solid #f0f0f0;
}

.avatar {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #FF6347, #ff7a5c);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: bold;
    margin: 0 auto 1rem;
}

.perfil-header h3 {
    margin: 0 0 0.5rem 0;
    color: #2C3E50;
}

.perfil-header .email {
    color: #666;
    font-size: 0.875rem;
}

.menu-cuenta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1rem;
    border-radius: 8px;
    text-decoration: none;
    color: #666;
    transition: all 0.3s;
}

.menu-item .icono {
    font-size: 1.25rem;
}

.menu-item:hover {
    background-color: #f8f9fa;
    color: #FF6347;
}

.menu-item.active {
    background-color: #FF6347;
    color: white;
}

.menu-item.logout {
    color: #dc3545;
    margin-top: 1rem;
    border-top: 1px solid #f0f0f0;
    padding-top: 1.5rem;
}

.menu-item.logout:hover {
    background-color: #dc3545;
    color: white;
}

/* Contenido */
.cuenta-contenido {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    min-height: 600px;
}

.seccion-cuenta {
    display: none;
}

.seccion-cuenta.active {
    display: block;
}

.seccion-cuenta h1 {
    color: #2C3E50;
    margin-bottom: 1rem;
}

/* Resumen Cards */
.resumen-cuenta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin: 2rem 0;
}

.resumen-card {
    background: linear-gradient(145deg, #f8f9fa, #ffffff);
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: transform 0.3s;
}

.resumen-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.resumen-icono {
    font-size: 2.5rem;
}

.resumen-info h3 {
    margin: 0 0 0.25rem 0;
    color: #2C3E50;
}

.resumen-info p {
    margin: 0;
    color: #666;
    font-size: 0.875rem;
}

.btn-ver {
    margin-left: auto;
    background: #FF6347;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-ver:hover {
    background: #e5533d;
}

/* Accesos Rápidos */
.accesos-rapidos {
    margin-top: 3rem;
}

.accesos-rapidos h2 {
    color: #2C3E50;
    margin-bottom: 1rem;
}

.botones-rapidos {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.btn-rapido {
    background: white;
    border: 2px solid #FF6347;
    color: #FF6347;
    padding: 1rem;
    border-radius: 8px;
    text-decoration: none;
    text-align: center;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-rapido:hover {
    background: #FF6347;
    color: white;
}

/* Formularios */
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    margin-bottom: 0.5rem;
    color: #333;
    font-weight: 600;
}

.form-group input,
.form-group textarea {
    padding: 0.75rem;
    border: 2px solid #ddd;
    border-radius: 6px;
    font-size: 1rem;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #FF6347;
}

.btn {
    padding: 0.875rem 2rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-primary {
    background: #FF6347;
    color: white;
}

.btn-primary:hover {
    background: #e5533d;
}

.btn-secondary {
    background: #2C3E50;
    color: white;
}

.btn-secondary:hover {
    background: #1a252f;
}

/* Dirección Principal */
.direccion-principal {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 8px;
    border-left: 4px solid #FF6347;
}

.direccion-principal h3 {
    margin: 0 0 1rem 0;
    color: #2C3E50;
}

.direccion-principal p {
    margin: 0 0 1.5rem 0;
    color: #666;
}

/* Alertas */
.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Responsive */
@media (max-width: 1024px) {
    .cuenta-layout {
        grid-template-columns: 1fr;
    }
    
    .cuenta-sidebar {
        position: static;
    }
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function mostrarSeccion(seccion) {
    // Ocultar todas las secciones
    document.querySelectorAll('.seccion-cuenta').forEach(s => {
        s.classList.remove('active');
    });
    
    // Remover active de todos los menú items
    document.querySelectorAll('.menu-item').forEach(m => {
        m.classList.remove('active');
    });
    
    // Mostrar la sección seleccionada
    document.getElementById('seccion-' + seccion).classList.add('active');
    
    // Activar el menú correspondiente
    event.target.closest('.menu-item').classList.add('active');
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>