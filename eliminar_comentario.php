<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$num_control = $_SESSION['usuario'];

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "peis");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Verificar si se ha proporcionado el id_respuesta y el id_foro
if (!isset($_GET['id_respuesta']) || !isset($_GET['id_foro'])) {
    echo "ID del comentario o del foro no especificado.";
    exit();
}

$id_respuesta = intval($_GET['id_respuesta']);
$id_foro = intval($_GET['id_foro']);

// Verificar que el comentario pertenece al usuario actual
$sql_verificar = "SELECT id FROM respuestas WHERE id = ? AND id_usuario = ?";
$stmt_verificar = $conexion->prepare($sql_verificar);
$stmt_verificar->bind_param("is", $id_respuesta, $num_control);
$stmt_verificar->execute();
$resultado_verificar = $stmt_verificar->get_result();

if ($resultado_verificar->num_rows === 0) {
    echo "No tienes permiso para eliminar este comentario.";
    exit();
}

// Eliminar el comentario
$sql_eliminar = "DELETE FROM respuestas WHERE id = ?";
$stmt_eliminar = $conexion->prepare($sql_eliminar);
$stmt_eliminar->bind_param("i", $id_respuesta);
$stmt_eliminar->execute();

// Redirigir al foro después de eliminar el comentario
header("Location: responder_foro.php?id_foro=" . $id_foro);
exit();
?>
