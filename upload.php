<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    echo "<script>alert('Error: Usuario no autenticado.'); window.location.href = 'index.php';</script>";
    exit;
}

// Conexión a la base de datos
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";

$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Validar que 'id_tarea' está en $_POST y que el archivo fue subido
if (isset($_POST['id_tarea']) && isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0) {
    $id_tarea = (int) $_POST['id_tarea'];  // Convertir a entero para evitar inyecciones
    $num_control = $_SESSION['usuario'];

    // Leer archivo
    $archivoTmp = $_FILES['archivo']['tmp_name'];
    $archivoContenido = addslashes(file_get_contents($archivoTmp));

    // Insertar en la tabla 'entregas'
    $sql = "INSERT INTO entregas (id_tarea, id_alumno, archivo_entrega, fecha_entrega) VALUES (?, ?, ?, NOW())";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iis", $id_tarea, $num_control, $archivoContenido);

    if ($stmt->execute()) {
        echo "<script>alert('Archivo subido correctamente.'); window.location.href = 'gestionTareasAlumno.php';</script>";
    } else {
        echo "<script>alert('Error al subir el archivo.'); window.history.back();</script>";
    }
    $stmt->close();
} else {
    echo "<script>alert('Error: Parámetros faltantes.'); window.history.back();</script>";
}

$conexion->close();
?>
