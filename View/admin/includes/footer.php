</main>
    
    <script>
        // Confirmación para acciones
        function confirmarAccion(mensaje) {
            return confirm(mensaje || '¿Está seguro de realizar esta acción?');
        }
        
        // Auto-hide de mensajes después de 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            const mensajes = document.querySelectorAll('.mensaje-exito, .mensaje-error');
            mensajes.forEach(mensaje => {
                setTimeout(() => {
                    mensaje.style.transition = 'opacity 0.5s';
                    mensaje.style.opacity = '0';
                    setTimeout(() => mensaje.remove(), 500);
                }, 5000);
            });
        });
        
        // Confirmación para cerrar sesión
        const btnCerrarSesion = document.querySelector('.btn-cerrar-sesion');
        if (btnCerrarSesion) {
            btnCerrarSesion.addEventListener('click', function(e) {
                if (!confirm('¿Está seguro que desea cerrar sesión?')) {
                    e.preventDefault();
                }
            });
        }
    </script>
</body>
</html>