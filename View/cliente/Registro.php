<?php
session_start();
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
            background: linear-gradient(135deg, #2C3E50 0%, #1a1a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .registro-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 450px;
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
            transition: background-color 0.3s;
        }
        
        .btn-registro:hover {
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
        
        .mensaje-error ul {
            margin-left: 20px;
            margin-top: 5px;
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
        
        .password-requisitos {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="registro-container">
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
        
        <form method="POST" action="../../controllers/AuthController.php">
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
        
        <div class="volver">
            ¿Ya tienes cuenta? <a href="login.php"><strong>Inicia sesión aquí</strong></a>
        </div>
        
        <div class="volver" style="margin-top: 10px;">
            <a href="index.php">← Volver al inicio</a>
        </div>
    </div>
</body>
</html>
<?php
// Limpiar datos del formulario
unset($_SESSION['datos_form']);
?>