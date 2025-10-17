document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const sideMenu = document.getElementById('sideMenu');
    const closeMenu = document.getElementById('closeMenu');
    
    // Crear overlay si no existe
    let overlay = document.querySelector('.menu-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'menu-overlay';
        document.body.appendChild(overlay);
    }
    
    if (menuToggle && sideMenu) {
        // Abrir menú
        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Abriendo menú...'); // Para debug
            menuToggle.classList.toggle('active'); 
            sideMenu.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
        
        // Cerrar menú
        function cerrarMenu() {
            menuToggle.classList.remove('active');
            sideMenu.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        
        if (closeMenu) {
            closeMenu.addEventListener('click', cerrarMenu);
        }
        
        overlay.addEventListener('click', cerrarMenu);
        
        // Cerrar con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sideMenu.classList.contains('active')) {
                cerrarMenu();
            }
        });
    } else {
        console.error('No se encontraron elementos del menú');
    }
    
    // Actualizar contador de carrito
    function actualizarContadorCarrito() {
        fetch('../../controllers/CarritoController.php?action=contar')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('carritoBadge');
                if (badge) {
                    if (data.cantidad > 0) {
                        badge.textContent = data.cantidad;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            })
            .catch(error => {
                console.log('Error al actualizar contador:', error);
            });
    }
    
    actualizarContadorCarrito();
});

function confirmarCerrarSesion() {
    if (confirm('¿Está seguro que desea cerrar sesión?')) {
        window.location.href = '../../controllers/AuthController.php?action=logout';
    }
}