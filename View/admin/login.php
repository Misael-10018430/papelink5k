<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Papelink</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, #020202ff 0%, #1a1a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
        }
        
        .logo {
        background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%); /* ← Cambiar esto */
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
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
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
            transition: background-color 0.3s;
        }
        
        .btn-login:hover {
            background-color: #e5533d;
        }
        
        .mensaje-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        
        .volver {
            text-align: center;
            margin-top: 20px;
        }
        
        .volver a {
            color: #FF6347;
            text-decoration: none;
        }
        
        .volver a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">PAPELINK</div>
        
        <h2>Panel Administrativo</h2>
        <p class="subtitulo">Ingresa tus credenciales</p>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mensaje-error">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['exito'])): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px;">
                 <?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="../../controllers/AuthController.php">
            <input type="hidden" name="accion" value="login_admin">
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" 
                       name="email" 
                       required 
                       placeholder="tu@email.com"
                       value="<?php echo $_SESSION['email_anterior'] ?? ''; unset($_SESSION['email_anterior']); ?>">
            </div>
            
            <div class="form-group">
                <label>Contraseña:</label>
                <input type="password" 
                       name="password" 
                       required 
                       placeholder="••••••••">
            </div>
            
            <button type="submit" class="btn-login">
                Iniciar Sesión
            </button>
        </form>
        
        <div class="volver">
            <a href="Location: ../view/cliente/index.php">← Volver al inicio</a>
        </div>
        
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #999; font-size: 14px;">
            <strong>Credenciales de prueba:</strong><br>
            Email: admin@papelink.com<br>
            Contraseña: admin123
        </div>
    </div>
</body>
</html>

