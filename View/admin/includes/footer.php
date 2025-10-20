</div> <!-- Cierre de admin-content -->
    </main> <!-- Cierre de admin-main -->
    
    <!-- Script para menú móvil -->
    <script>
        // Toggle menú móvil
        const menuToggle = document.createElement('button');
        menuToggle.className = 'mobile-menu-toggle';
        menuToggle.innerHTML = '☰';
        menuToggle.onclick = function() {
            document.getElementById('adminSidebar').classList.toggle('active');
        };
        document.body.appendChild(menuToggle);
        
        // Cerrar menú al hacer clic en un enlace (móvil)
        if (window.innerWidth <= 768) {
            document.querySelectorAll('.menu-item').forEach(item => {
                item.addEventListener('click', function() {
                    document.getElementById('adminSidebar').classList.remove('active');
                });
            });
        }
    </script>
</body>
</html>