<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Papelink</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Montserrat', Arial, Helvetica, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: flex-end; /* Cambiado para alinear a la derecha */
            position: relative;
            overflow: hidden;
            padding-right: 50px; /* Añadido padding para separar del borde */
        }
        
        /* Imagen de fondo con overlay */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://plus.unsplash.com/premium_photo-1661605688338-eb941025d432?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1169');
            background-size: cover;
            background-position: center;
            filter: brightness(0.5);
            z-index: -1;
        }
        
        .login-wrapper {
            display: flex;
            width: 100%;
            max-width: 1400px;
            height: 100vh;
            align-items: center;
            justify-content: space-between; /* Distribuye el espacio */
        }
        
        .brand-section {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: left;
            margin-left: -460px; /* Margen negativo para mover más a la izquierda */
            padding-left: 20px;
            max-width: 50%; /* Limita el ancho para dar más espacio al formulario */
        }
        
        .brand-title {
            color: white;
            font-size: 100px;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.7);
            letter-spacing: -2px;
            line-height: 1;
        }
        
        .brand-subtitle {
            color: white;
            font-size: 36px;
            font-weight: 300;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.7);
            letter-spacing: 0.5px;
        }
        
        .login-container {
            width: 550px; /* Ancho fijo mayor */
            height: 650px; /* Altura fija */
            background-color: rgba(255, 255, 255, 0.58);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            padding: 50px 40px;
            border-radius: 13px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            justify-content: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .login-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;

        }
        
        .logo {
            background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
            color: white;
            padding: 18px;
            text-align: center;
            font-size: 28px;
            font-weight: 600;
            border-radius: 12px;
            margin-bottom: 30px;
            letter-spacing: 1px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
            
        h2 {
            color: #2C3E50;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .subtitulo {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
            font-weight: 400;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: rgba(255, 255, 255, 0.8);
        }
        
        .form-group i {
            position: absolute;
            left: 15px;
            top: 42px;
            color: #999;
            font-size: 18px;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #FF6347;
            box-shadow: 0 0 0 3px rgba(255, 99, 71, 0.2);
        }
        
        .btn-login {
            width: 100%;
            padding: 16px;
            background-color: #FF6347;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;

        }
        
        
        .mensaje-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            font-size: 14px;
        }
        
        .volver {
            text-align: center;
            margin-top: 25px;
        }
        
        .volver a {
            color: #FF6347;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .volver a:hover {
            color: #e5533d;
            text-decoration: underline;
        }
        
        /* Responsive design */
        @media (max-width: 1200px) {
            .brand-section {
                padding-left: 40px;
            }
            
            .brand-title {
                font-size: 80px;
            }
            
            .brand-subtitle {
                font-size: 30px;
            }
        }
        
        @media (max-width: 992px) {
            body {
                justify-content: center;
                padding-right: 0;
            }
            
            .login-wrapper {
                flex-direction: column;
                height: auto;
                justify-content: center;
            }
            
            .brand-section {
                padding: 20px;
                text-align: center;
                padding-left: 20px;
                max-width: 100%;
                margin-bottom: 30px;
            }
            
            .brand-title {
                font-size: 60px;
            }
            
            .brand-subtitle {
                font-size: 24px;
            }
            
            .login-container {
                width: 90%;
                max-width: 500px;
                height: auto;
                margin: 0 auto;
            }
        }
        
        @media (max-width: 576px) {
            .brand-title {
                font-size: 48px;
            }
            
            .brand-subtitle {
                font-size: 20px;
            }
            
            .login-container {
                padding: 30px 25px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="brand-section">
            <div class="brand-title">Papelink</div>
            <div class="brand-subtitle">Acceso administrativo</div>
        </div>
        
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
                <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                     <?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="../../controllers/AuthController.php">
                <input type="hidden" name="accion" value="login_admin">
                
                <div class="form-group">
                    <label>Email:</label>
                    <i class="fas fa-envelope"></i>
                    <input type="email" 
                           name="email" 
                           required 
                           placeholder="tu@email.com"
                           value="<?php echo $_SESSION['email_anterior'] ?? ''; unset($_SESSION['email_anterior']); ?>">
                </div>
                
                <div class="form-group">
                    <label>Contraseña:</label>
                    <i class="fas fa-lock"></i>
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
                <a href="../cliente/index.php">← Volver al dashboard Cliente</a>
            </div>
        </div>
    </div>
</body>
</html>