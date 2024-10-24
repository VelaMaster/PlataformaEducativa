<?php
$usuario = $_POST['usuario'];
$contrasena = $_POST['contrasena'];
$role = $_POST['role'];
session_start();
$_SESSION['usuario'] = $usuario;
$conexion = mysqli_connect("localhost", "root", "", "peis");

if (!$conexion) {
    die("Conexión fallida: " . mysqli_connect_error());
}
if ($role == "Estudiante") {
    
    $consulta = "SELECT * FROM alumnos WHERE num_control = '$usuario' AND contrasena = '$contrasena'";
} elseif ($role == "Docente") {
    
    $consulta = "SELECT * FROM docentes WHERE num_control = '$usuario' AND contrasena = '$contrasena'";
}

$resultado = mysqli_query($conexion, $consulta);
$filas = mysqli_num_rows($resultado);

if ($filas > 0) {
    if ($role == "Estudiante") {
        header("location:alumno.html");
    } elseif ($role == "Docente") {
        header("location:inicioProfesor.html");
    }
} else {
    header("location:index.php?error=auth");
}
mysqli_free_result($resultado);
mysqli_close($conexion);
mysqli_free_result($resultado);
mysqli_close($conexion);
?>
