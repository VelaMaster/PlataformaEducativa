<?php
session_start();
session_unset();
session_destroy();

// Redirigir al formulario de inicio de sesión
header("Location: iniciarSesion.html");
exit();
?>
