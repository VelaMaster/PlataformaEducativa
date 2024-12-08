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
    $id_tarea = (int)$_POST['id_tarea']; // Convertir a entero para evitar inyecciones
    $id_alumno = $_SESSION['usuario'];

    // Verificar que el alumno existe en la base de datos
    $sqlAlumno = "SELECT id FROM alumnos WHERE id = ?";
    $stmtAlumno = $conexion->prepare($sqlAlumno);
    $stmtAlumno->bind_param("i", $id_alumno);
    $stmtAlumno->execute();
    $stmtAlumno->store_result();

    if ($stmtAlumno->num_rows == 0) {
        // El alumno no existe en la base de datos
        header("Location: gestionTareasAlumno.php?error=El alumno no existe en la base de datos.");
        exit;
    }

    // Obtener la información del archivo subido
    $archivoTmp = $_FILES['archivo']['tmp_name'];
    $archivoNombre = $_FILES['archivo']['name'];
    $archivoTamaño = $_FILES['archivo']['size'];

    // Definir el directorio de destino para almacenar el archivo
    $directorioDestino = "uploads/"; // Puedes cambiar esta ruta
    $archivoDestino = $directorioDestino . uniqid() . "_" . basename($archivoNombre); // Asegura nombres únicos

    // Validar que el archivo no sea demasiado grande (por ejemplo, 10 MB)
    if ($archivoTamaño > 10 * 1024 * 1024) { // 10 MB
        // Redirigir de nuevo a la página de carga si el archivo es demasiado grande
        header("Location: gestionTareasAlumno.php?error=El archivo es demasiado grande.");
        exit;
    }

    // Mover el archivo subido al directorio de destino
    if (move_uploaded_file($archivoTmp, $archivoDestino)) {
        // Guardar la ruta del archivo en la base de datos (no el archivo binario)
        $sql = "INSERT INTO entregas (id_tarea, id_alumno, archivo_entrega, fecha_entrega) VALUES (?, ?, ?, NOW())";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iis", $id_tarea, $id_alumno, $archivoDestino);

        if ($stmt->execute()) {
            // Redirigir a la página de gestión de tareas con un parámetro de éxito
            header("Location: gestionTareasAlumno.php?success=Archivo subido correctamente.");
        } else {
            // Redirigir si hubo un error al guardar en la base de datos
            header("Location: gestionTareasAlumno.php?error=Error al guardar los datos en la base de datos.");
        }
        $stmt->close();
    } else {
        // Redirigir si hubo un error al mover el archivo
        header("Location: gestionTareasAlumno.php?error=Error al mover el archivo.");
    }
} else {
    // Redirigir si faltan parámetros o el archivo no es válido
    header("Location: gestionTareasAlumno.php?error=Parámetros faltantes o archivo no válido.");
}

$conexion->close();
?>
