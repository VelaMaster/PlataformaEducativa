<?php
session_start();

// Verificar si el usuario está autenticado
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

$num_control = $_SESSION['usuario'];  // Obtener el ID del alumno desde la sesión
$id_tarea = $_GET['id'];  // Obtener el ID de la tarea desde la URL

// Comprobar si se ha subido un archivo
if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0) {
    // Obtener información del archivo subido
    $archivoTmp = $_FILES['archivo']['tmp_name'];
    $archivoNombre = $_FILES['archivo']['name'];
    $archivoContenido = addslashes(file_get_contents($archivoTmp));  // Convertir el archivo a binario

    // Insertar el archivo en la base de datos
    $sql = "INSERT INTO entregas (id_tarea, id_alumno, archivo_entrega, fecha_entrega) 
            VALUES (?, ?, ?, NOW())";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iis", $id_tarea, $num_control, $archivoContenido);  // ID de la tarea, ID del alumno y contenido binario del archivo

    if ($stmt->execute()) {
        echo "<script>alert('Archivo subido correctamente.'); window.location.href = 'verTarea.php?id=$id_tarea';</script>";
    } else {
        echo "Error al guardar el archivo en la base de datos: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Error: No se ha subido ningún archivo.";
}

$conexion->close();
?>
