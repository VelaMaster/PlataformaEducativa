<?php
// eliminarTarea.php
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";

$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$id= $_GET['id'];
$sql = "DELETE FROM tareas WHERE id = $id";

if ($conexion->query($sql) === TRUE) {
    // Redirigir de nuevo a listarTareas.php después de eliminar
    header("Location: listarTareas.php");
    exit(); // Asegurarse de que no se ejecute código adicional
} else {
    echo "Error al eliminar la tarea: " . $conexion->error;
}

$conexion->close();
?>
