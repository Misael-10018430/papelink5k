<?php
session_start();
session_destroy();
setcookie(session_name(), '', time() - 3600, '/');
echo "Sesión limpiada. <a href='view/cliente/login.php'>Ir a login</a>";
?>