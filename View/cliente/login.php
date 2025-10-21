<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Papelink</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, #0a0a0aff 0%, #1a1a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 420px;
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
            color: #666;
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
        
        .btn-login {
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
        
        .btn-login:hover {
            background-color: #e5533d;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 99, 71, 0.3);
        }
        
        .btn-registro {
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
        
        .btn-registro:hover {
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
        
        .mensaje-exito {
            background-color: #d4edda;
            color: #155724;
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            font-size: 14px;
        }
        
        /* NUEVO: Mensaje especial de registro exitoso */
        .mensaje-registro-exitoso {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }
        
        .mensaje-registro-exitoso .icono {
            font-size: 40px;
            margin-bottom: 10px;
        }
        
        .mensaje-registro-exitoso h3 {
            margin-bottom: 8px;
            font-size: 18px;
        }
        
        .mensaje-registro-exitoso p {
            font-size: 14px;
            opacity: 0.95;
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
        
        .info-box {
            background-color: #e8f4f8;
            border-left: 4px solid #3498db;
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #2c3e50;
        }
        
        @media (max-width: 480px) {
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
    <div class="login-container">
        <div class="logo">PAPELINK</div>
        
        <h2>Bienvenido</h2>
        <p class="subtitulo">Inicia sesión para continuar</p>
        
        <?php if (isset($_SESSION['registro_exitoso']) && $_SESSION['registro_exitoso']): ?>
            <!-- MENSAJE ESPECIAL DE REGISTRO EXITOSO -->
            <div class="mensaje-registro-exitoso">
                <div class="icono">🎉</div>
                <h3>¡Cuenta creada exitosamente!</h3>
                <p>
                    Hola <strong><?php echo htmlspecialchars($_SESSION['nombre_registrado'] ?? 'Usuario'); ?></strong>,<br>
                    tu cuenta ha sido creada. Ahora puedes iniciar sesión.
                </p>
            </div>
            <?php 
                unset($_SESSION['registro_exitoso']); 
                unset($_SESSION['nombre_registrado']);
                // Pre-llenar el email si existe
                if (isset($_SESSION['email_registrado'])) {
                    $_SESSION['email_anterior'] = $_SESSION['email_registrado'];
                    unset($_SESSION['email_registrado']);
                }
            ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mensaje-error">
                ❌ <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['exito']) && !isset($_SESSION['registro_exitoso'])): ?>
            <div class="mensaje-exito">
                ✓ <?php echo htmlspecialchars($_SESSION['exito']); unset($_SESSION['exito']); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="../../controllers/AuthController.php">
            <input type="hidden" name="accion" value="login_unificado">
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" 
                       name="email" 
                       required 
                       placeholder="tu@email.com"
                       autocomplete="email"
                       value="<?php echo isset($_SESSION['email_anterior']) ? htmlspecialchars($_SESSION['email_anterior']) : ''; unset($_SESSION['email_anterior']); ?>">
            </div>
            
            <div class="form-group">
                <label>Contraseña:</label>
                <input type="password" 
                       name="password" 
                       required 
                       placeholder="••••••••"
                       autocomplete="current-password">
            </div>
            
            <button type="submit" class="btn-login">
                Iniciar Sesión
            </button>
        </form>
        
        <div class="separador">━━━━━━━━━ o ━━━━━━━━━</div>
        
        <a href="registro.php" class="btn-registro">
            Crear cuenta nueva
        </a>
        
        <div class="volver">
            <a href="index.php">← Continuar sin cuenta</a>
        </div>
        
        <!-- Sección para empleados -->
        <div class="admin-section">
            <div class="titulo-admin">
                <strong>¿Eres empleado?</strong><br>
                <span style="font-size: 12px;">Accede al panel administrativo</span>
            </div>
            <a href="../admin/login.php" class="btn-admin">
                Acceso Administrativo →
            </a>
        </div>
    </div>
</body>
</html>
```
