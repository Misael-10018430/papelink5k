<?php
require_once __DIR__ . '/../../config/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - Papelink</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            overflow: hidden;
        }
        
        .background-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://images.unsplash.com/photo-1664735245380-75ad87571bca?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1194');
            background-size: cover;
            background-position: center;
            filter: brightness(0.7);
            z-index: -1;
        }
        
        .login-wrapper {
            display: flex;
            width: 100%;
            height: 100vh;
        }
        
        .brand-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 100px;
            color: white;
        }
        
        .brand-name {
            font-size: 8rem;
            font-weight: bold;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
            margin-bottom: 20px;
            letter-spacing: 2px;
        }
        
        .brand-tagline {
            font-size: 1.5rem;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.5);
            text-align: center;
            max-width: 600px;
        }
        
        .login-section {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }
        
        .login-container {
            background-color: rgba(255, 255, 255, 0.42);
            backdrop-filter: blur(10px);
            padding: 60px;
            border-radius: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 460px;
        }
        
        .logo {
            background-color: #FF6347;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        h2 {
            color: #2C3E50;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .subtitulo {
            text-align: center;
            color: #eaeaeaff;
            margin-bottom: 30px;
            font-size: 15px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #FF6347;
        }
        
        .btn-registro {
            width: 100%;
            padding: 14px;
            background-color: #FF6347;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 12px;
        }
        
        .btn-registro:hover {
            background-color: #e5533d;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 99, 71, 0.3);
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background-color: white;
            color: #333;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            background-color: #f5f5f5;
            border-color: #FF6347;
            color: #FF6347;
        }
        
        .mensaje-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            font-size: 14px;
        }
        
        .mensaje-error ul {
            margin-left: 20px;
            margin-top: 5px;
        }
        
        .volver {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        
        .volver a {
            color: #FF6347;
            text-decoration: none;
            font-weight: 500;
        }
        
        .volver a:hover {
            text-decoration: underline;
        }
        
        .separador {
            text-align: center;
            margin: 20px 0;
            color: #999;
            font-size: 13px;
        }
        
        .admin-section {
            margin-top: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
            border-radius: 8px;
            text-align: center;
        }
        
        .admin-section .titulo-admin {
            color: white;
            margin-bottom: 12px;
            font-size: 14px;
            font-weight: normal;
        }
        
        .admin-section .titulo-admin strong {
            font-size: 15px;
        }
        
        .btn-admin {
            display: inline-block;
            background: white;
            color: #2c3e50;
            padding: 10px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
        }
        
        .password-requisitos {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
            }
            
            .brand-section {
                flex: none;
                height: 30vh;
            }
            
            .brand-name {
                font-size: 2.5rem;
            }
            
            .brand-tagline {
                font-size: 1.2rem;
            }
            
            .login-section {
                flex: 1;
                padding: 20px;
            }
            
            .login-container {
                padding: 30px 20px;
            }
            
            .logo {
                font-size: 28px;
                padding: 15px;
            }
            
            h2 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="background-container"></div>
    
    <div class="login-wrapper">
        <div class="brand-section">
            <div class="brand-name">PAPELINK</div>
            <div class="brand-tagline">Variedad de productos a tu alcanze</div>
        </div>
        
        <div class="login-section">
            <div class="login-container">
                <div class="logo">PAPELINK</div>
                
                <h2>Crear Cuenta Nueva</h2>
                <p class="subtitulo">Regístrate para empezar a comprar</p>
                
                <?php if (isset($_SESSION['errores']) && !empty($_SESSION['errores'])): ?>
                    <div class="mensaje-error">
                        <strong>Por favor corrige los siguientes errores:</strong>
                        <ul>
                            <?php foreach ($_SESSION['errores'] as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php unset($_SESSION['errores']); ?>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo BASE_URL; ?>controllers/AuthController.php">
                    <input type="hidden" name="accion" value="registro_cliente">
                    
                    <div class="form-group">
                        <label>Nombre completo:</label>
                        <input type="text" 
                               name="nombre" 
                               required 
                               placeholder="Juan Pérez"
                               value="<?php echo $_SESSION['datos_form']['nombre'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" 
                               name="email" 
                               required 
                               placeholder="tu@email.com"
                               value="<?php echo $_SESSION['datos_form']['email'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Teléfono:</label>
                        <input type="tel" 
                               name="telefono" 
                               required 
                               placeholder="916-123-4567"
                               value="<?php echo $_SESSION['datos_form']['telefono'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Dirección (opcional):</label>
                        <input type="text" 
                               name="direccion" 
                               placeholder="Tuxtla Gutiérrez, Chiapas"
                               value="<?php echo $_SESSION['datos_form']['direccion'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Contraseña:</label>
                        <input type="password" 
                               name="password" 
                               required 
                               placeholder="••••••••">
                        <div class="password-requisitos">
                            Mínimo 6 caracteres
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Confirmar contraseña:</label>
                        <input type="password" 
                               name="confirmar_password" 
                               required 
                               placeholder="••••••••">
                    </div>
                    
                    <button type="submit" class="btn-registro">
                        Crear Cuenta
                    </button>
                </form>
                
                <div class="separador">━━━━━━━━━ o ━━━━━━━━━</div>
                
                <a href="login.php" class="btn-login">
                    Iniciar Sesión
                </a>
                
                <div class="volver">
                    <a href="<?php echo BASE_URL; ?>">← Continuar sin cuenta</a>
                </div>
                
                <!-- Sección para empleados -->
                <div class="admin-section">
                    <div class="titulo-admin">
                        <strong>¿Eres empleado?</strong><br>
                        <span style="font-size: 12px;">Accede al panel administrativo</span>
                    </div>
                    <a href="<?php echo BASE_URL; ?>view/admin/login.php" class="btn-admin">
                        Acceso Administrativo →
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
// Limpiar datos del formulario
unset($_SESSION['datos_form']);
?>