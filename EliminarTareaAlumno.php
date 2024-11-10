<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    echo "<script>alert('Error: Usuario no autenticado.'); window.location.href = 'index.php';</script>";
    exit;
}

$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";

$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$id_alumno = $_SESSION['usuario'];
$id_tarea = isset($_POST['id_tarea']) ? (int)$_POST['id_tarea'] : 0;

if ($id_tarea > 0) {
    // Obtener la ruta del archivo
    $sql = "SELECT archivo_entrega FROM entregas WHERE id_tarea = $id_tarea AND id_alumno = $id_alumno";
    $resultado = $conexion->query($sql);
    
    if ($resultado && $resultado->num_rows > 0) {
        $entrega = $resultado->fetch_assoc();
        $archivoEliminar = $entrega['archivo_entrega'];
        
        // Eliminar la tarea de la base de datos
        $sqlEliminar = "DELETE FROM entregas WHERE id_tarea = $id_tarea AND id_alumno = $id_alumno";
        if ($conexion->query($sqlEliminar) === TRUE) {
            // Eliminar el archivo físico
            if (file_exists($archivoEliminar)) {
                unlink($archivoEliminar);
            }
            echo "<script>alert('Tarea eliminada correctamente.'); window.location.href = 'gestionTareasAlumno.php';</script>";
        } else {
            echo "<script>alert('Error al eliminar la tarea.'); window.location.href = 'gestionTareasAlumno.php';</script>";
        }
    } else {
        echo "<script>alert('No se encontró la entrega.'); window.location.href = 'gestionTareasAlumno.php';</script>";
    }
} else {
    echo "<script>alert('Tarea inválida.'); window.location.href = 'gestionTareasAlumno.php';</script>";
}

$conexion->close();
?>
