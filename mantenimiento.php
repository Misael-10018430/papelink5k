<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papelink - Mantenimiento</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }
        
        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 50px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
        
        .icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        
        h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        .btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
        }
        
        .status {
            margin-top: 30px;
            font-size: 0.9rem;
            opacity: 0.7;
        }
        
        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🔧</div>
        <h1>Sitio en Mantenimiento</h1>
        <p>Estamos trabajando para mejorar tu experiencia.<br>Por favor, inténtalo de nuevo en unos minutos.</p>
        <a href="/" class="btn">Reintentar</a>
        <div class="status">
            <small>Error de conexión a base de datos • <?php echo date('Y-m-d H:i:s'); ?></small>
        </div>
    </div>
    
    <script>
        // Auto-recargar cada 30 segundos
        setTimeout(function() {
            window.location.reload();
        }, 30000);
    </script>
</body>
</html>