</div> <!-- Cierre de main-content -->
    
    <script>
        // Cerrar sesión con confirmación
        document.querySelectorAll('.btn-logout').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!confirm('¿Estás seguro que deseas cerrar sesión?')) {
                    e.preventDefault();
                }
            });
        });
        
        // Menu responsive
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.querySelector('.sidebar');
        
        if (menuToggle && sidebar) {
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
        }
        
        // Auto-hide alerts después de 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('[style*="background: #d4edda"], [style*="background: #f8d7da"]');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });
        
        // Confirmación para acciones destructivas
        document.querySelectorAll('[data-confirm]').forEach(element => {
            element.addEventListener('click', function(e) {
                const message = this.getAttribute('data-confirm');
                if (!confirm(message)) {
                    e.preventDefault();
                }
            });
        });
        
        // Tooltips simples
        document.querySelectorAll('[title]').forEach(element => {
            element.addEventListener('mouseenter', function() {
                const title = this.getAttribute('title');
                if (title) {
                    const tooltip = document.createElement('div');
                    tooltip.className = 'tooltip';
                    tooltip.textContent = title;
                    tooltip.style.cssText = `
                        position: absolute;
                        background: #2C3E50;
                        color: white;
                        padding: 5px 10px;
                        border-radius: 4px;
                        font-size: 12px;
                        pointer-events: none;
                        z-index: 10000;
                    `;
                    document.body.appendChild(tooltip);
                    
                    const rect = this.getBoundingClientRect();
                    tooltip.style.left = rect.left + 'px';
                    tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
                    
                    this.addEventListener('mouseleave', function() {
                        tooltip.remove();
                    }, { once: true });
                }
            });
        });
    </script>
</body>
</html>