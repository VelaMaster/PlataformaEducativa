<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['username'])) {
    header("Location: iniciarSesion.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/estiloiniciarSesion.css">
</head>
<body>
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>Has iniciado sesión correctamente.</p>
    <a href="logout.php">Cerrar Sesión</a>
</body>
</html>
