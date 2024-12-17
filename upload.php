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
    $num_control = $_SESSION['usuario'];

    // Validar num_control
    if (!ctype_digit($num_control)) {
        error_log("Error: num_control no es numérico.");
        header("Location: gestionTareasAlumno.php?error=ID de alumno inválido.");
        exit;
    }

    // Validar id_tarea
    if (!ctype_digit((string)$id_tarea)) {
        error_log("Error: id_tarea no es numérico.");
        header("Location: gestionTareasAlumno.php?error=ID de tarea inválido.");
        exit;
    }

    // Verificar que el alumno existe en la base de datos (usando num_control)
    $sqlAlumno = "SELECT id FROM alumnos WHERE num_control = ?";
    $stmtAlumno = $conexion->prepare($sqlAlumno);
    $stmtAlumno->bind_param("i", $num_control);
    $stmtAlumno->execute();
    $stmtAlumno->store_result();

    if ($stmtAlumno->num_rows == 0) {
        // El alumno no existe en la base de datos
        header("Location: gestionTareasAlumno.php?error=Alumno no encontrado en la base de datos.");
        exit;
    }

    // Obtener el ID del alumno
    $stmtAlumno->bind_result($id_alumno);
    $stmtAlumno->fetch();

    // Obtener la información del archivo subido
    $archivoTmp = $_FILES['archivo']['tmp_name'];
    $archivoNombre = $_FILES['archivo']['name'];
    $archivoTamaño = $_FILES['archivo']['size'];

    // Limpiar el nombre del archivo para evitar problemas con caracteres especiales
    $archivoNombreSeguro = str_replace(' ', '_', basename($archivoNombre)); // Cambiar los espacios por guiones bajos
    $archivoNombreSeguro = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $archivoNombreSeguro); // Eliminar otros caracteres especiales

    // Definir el directorio de destino para almacenar el archivo
    $directorioDestino = "uploads/"; // Puedes cambiar esta ruta
    if (!is_dir($directorioDestino)) {
        mkdir($directorioDestino, 0777, true);
    }

    // Verificar permisos de escritura en el directorio
    if (!is_writable($directorioDestino)) {
        error_log("Error: No se puede escribir en el directorio '$directorioDestino'.");
        header("Location: gestionTareasAlumno.php?error=No se puede guardar el archivo.");
        exit;
    }

    // Crear el nombre único para el archivo
    $archivoDestino = $directorioDestino . uniqid() . "_" . $archivoNombreSeguro;

    // Validar que el archivo no sea demasiado grande (por ejemplo, 10 MB)
    if ($archivoTamaño > 10 * 1024 * 1024) { // 10 MB
        header("Location: gestionTareasAlumno.php?error=El archivo es demasiado grande.");
        exit;
    }

    // Mover el archivo subido al directorio de destino
    if (move_uploaded_file($archivoTmp, $archivoDestino)) {
        // Guardar la ruta del archivo en la base de datos
        $sql = "INSERT INTO entregas (id_tarea, id_alumno, archivo_entrega, fecha_entrega) VALUES (?, ?, ?, NOW())";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iis", $id_tarea, $id_alumno, $archivoDestino);

        if ($stmt->execute()) {
            header("Location: gestionTareasAlumno.php?success=Archivo subido correctamente.");
        } else {
            error_log("Error en la base de datos: " . $stmt->error);
            header("Location: gestionTareasAlumno.php?error=Error al guardar en la base de datos.");
        }
        $stmt->close();
    } else {
        error_log("Error al mover archivo: " . print_r(error_get_last(), true));
        header("Location: gestionTareasAlumno.php?error=Error al mover el archivo.");
    }
} else {
    // Mostrar un error más detallado
    if (!isset($_POST['id_tarea'])) {
        error_log("Error: id_tarea faltante.");
    }
    if (!isset($_FILES['archivo'])) {
        error_log("Error: Archivo no subido.");
    } elseif ($_FILES['archivo']['error'] != 0) {
        error_log("Error en archivo: " . $_FILES['archivo']['error']);
    }
    header("Location: gestionTareasAlumno.php?error=Parámetros faltantes o archivo no válido.");
}

$conexion->close();
?>
