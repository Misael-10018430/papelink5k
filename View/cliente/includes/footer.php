
 <style>
    /* ===== FOOTER ===== */
    body{
      background-color: #e8e8e8;
    }
    .footer {
    min-height: 150px; /* Altura mínima sugerida */
    }










    .footer {
      background-color: #2C3E50;
      color: #ccc;
      padding: 20px 10px;
      margin-top: 60px;
      font-size: 14px;
    }

    .footer-container {
      max-width: 1000px;   /* 🔹 Controla lo ancho del footer */
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 30px;
    }

    .footer-section h3 {
      color: #FF6347;
      margin-bottom: 25px;
    }

    .footer-section p {
      line-height: 1.6;
    }

    .footer-links {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .footer-links li {
      margin-bottom: 8px;
    }

    .footer-links a {
      color: #ccc;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .footer-links a:hover {
      color: #FF6347;
    }

    .footer-bottom {
      text-align: center;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid #34495e;
      font-size: 13px;
    }
  </style>
</head>
<body>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="footer-container">
      
      <!-- Logo / Descripción -->
      <div class="footer-section">
        <h3>Papelink</h3>
        <p>
          Tu tienda de papelería y útiles escolares de confianza.
        </p>
      </div>

      <!-- Enlaces rápidos -->
      <div class="footer-section">
        <h3>Enlaces Rápidos</h3>
        <ul class="footer-links">
          <li><a href="index.php">Inicio</a></li>
          <li><a href="productos.php">Productos</a></li>
          <li><a href="#">Quiénes Somos</a></li>
          <li><a href="#">Contacto</a></li>
        </ul>
      </div>

      <!-- Contacto -->
      <div class="footer-section">
        <h3>Contacto</h3>
        <p>
          Tuxtla Gutiérrez, Chiapas<br>
          Tel: 916-186-8451<br>
          Email: contacto@papelink.com
        </p>
      </div>

    </div>

    <!-- Derechos reservados -->
    <div class="footer-bottom">
      © 2025 Papelink. Todos los derechos reservados.
    </div>
  </footer>

  <script>
    // Función para selector de cantidad
    function cambiarCantidad(accion) {
      const input = document.getElementById('cantidad');
      let valor = parseInt(input.value) || 1;
      
      if (accion === 'incrementar') {
        valor++;
      } else if (accion === 'decrementar' && valor > 1) {
        valor--;
      }
      
      input.value = valor;
    }
  </script>
</body>
</html>