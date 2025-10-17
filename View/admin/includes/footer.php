</main>
    </div>
    
    <script>
        // Confirmar antes de eliminar/desactivar
        function confirmarAccion(mensaje) {
            return confirm(mensaje || '¿Estás seguro de realizar esta acción?');
        }
        
        // Auto-cerrar mensajes después de 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            const mensajes = document.querySelectorAll('.mensaje-exito, .mensaje-error');
            mensajes.forEach(function(mensaje) {
                setTimeout(function() {
                    mensaje.style.display = 'none';
                }, 5000);
            });
        });
    </script>
</body>
</html>