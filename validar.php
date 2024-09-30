<?php
$usuario = $_POST['usuario'];
$contrasena = $_POST['contrasena'];
session_start();
$_SESSION['usuario'] = $usuario;

$conexion = mysqli_connect("localhost", "root", "", "plataformaweb");

if (!$conexion) {
    die("Conexión fallida: " . mysqli_connect_error());
}

$consulta = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND contrasena = '$contrasena'";
$resultado = mysqli_query($conexion, $consulta);
$filas = mysqli_num_rows($resultado);

if ($filas) {
    header("location:home.php");
} else {
    header("location:index.php?error=auth");
}

mysqli_free_result($resultado);
mysqli_close($conexion);
?>
