<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
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
    $id_tarea = (int) $_POST['id_tarea'];
    $num_control = $_SESSION['usuario'];

    // Obtener el `id_alumno` correspondiente
    $stmt = $conexion->prepare("SELECT id FROM alumnos WHERE num_control = ?");
    $stmt->bind_param("s", $num_control);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_alumno = $row['id'];
    } else {
        header("Location: gestionTareasAlumno.php?error=Alumno no encontrado.");
        exit;
    }
    

    // Validar que la tarea existe
    $stmt = $conexion->prepare("SELECT id FROM tareas WHERE id = ?");
    $stmt->bind_param("i", $id_tarea);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        header("Location: gestionTareasAlumno.php?error=Tarea no encontrada.");
        exit;
    }

    // Obtener la información del archivo subido
    $archivoTmp = $_FILES['archivo']['tmp_name'];
    $archivoNombre = $_FILES['archivo']['name'];
    $archivoTamaño = $_FILES['archivo']['size'];

    $directorioDestino = "uploads/";
    $archivoDestino = $directorioDestino . basename($archivoNombre);

    if ($archivoTamaño > 10 * 1024 * 1024) {
        header("Location: gestionTareasAlumno.php?error=El archivo es demasiado grande.");
        exit;
    }

    if (move_uploaded_file($archivoTmp, $archivoDestino)) {
        $sql = "INSERT INTO entregas (id_tarea, id_alumno, archivo_entrega, fecha_entrega) VALUES (?, ?, ?, NOW())";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iis", $id_tarea, $id_alumno, $archivoDestino);

        if ($stmt->execute()) {
            header("Location: gestionTareasAlumno.php?success=Archivo subido correctamente.");
        } else {
            header("Location: gestionTareasAlumno.php?error=Error al guardar los datos.");
        }
    } else {
        header("Location: gestionTareasAlumno.php?error=Error al mover el archivo.");
    }
}

$conexion->close();
?>

  