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
    $id_tarea = (int) $_POST['id_tarea'];  // Convertir a entero para evitar inyecciones
    $num_control = $_SESSION['usuario'];

    // Obtener la información del archivo subido
    $archivoTmp = $_FILES['archivo']['tmp_name'];
    $archivoNombre = $_FILES['archivo']['name'];
    $archivoTamaño = $_FILES['archivo']['size'];

    // Definir el directorio de destino para almacenar el archivo
    $directorioDestino = "uploads/"; // Puedes cambiar esta ruta
    $archivoDestino = $directorioDestino . basename($archivoNombre);

    // Validar que el archivo no sea demasiado grande (por ejemplo, 10 MB)
    if ($archivoTamaño > 10 * 1024 * 1024) { // 10 MB
        // Redirigir de nuevo a la página de carga si el archivo es demasiado grande
        header("Location: gestionTareasAlumno.php?error=El archivo es demasiado grande.");
        exit;
    }

    // Mover el archivo subido al directorio de destino
    if (move_uploaded_file($archivoTmp, $archivoDestino)) {
        // Guardar la ruta del archivo en la base de datos
        $sql = "INSERT INTO entregas (id_tarea, id_alumno, archivo_entrega, fecha_entrega) VALUES (?, ?, ?, NOW())";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iis", $id_tarea, $num_control, $archivoDestino);

        if ($stmt->execute()) {
            // Redirigir a la página de gestión de tareas con un parámetro de éxito
            header("Location: gestionTareasAlumno.php?success=Archivo subido correctamente.");
        } else {
            // Redirigir si hubo un error al guardar en la base de datos
            header("Location: gestionTareasAlumno.php?error=Error al guardar los datos.");
        }
        $stmt->close();
    } else {
        // Redirigir si hubo un error al mover el archivo
        header("Location: gestionTareasAlumno.php?error=Error al mover el archivo.");
    }
} else {
    // Redirigir si faltan parámetros o el archivo no es válido
    header("Location: gestionTareasAlumno.php?error=Error: Parámetros faltantes o archivo no válido.");
}

$conexion->close();


/*  NUEVO
session_start();  // Inicia la sesión para acceder a las variables de sesión

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {  // Verifica si la variable de sesión 'usuario' está definida
    echo "<script>alert('Error: Usuario no autenticado.'); window.location.href = 'index.php';</script>";
    exit;  // Redirecciona al usuario a 'index.php' si no está autenticado y muestra un mensaje de alerta
}

// Conexión a la base de datos
$servidor = "localhost";         // Nombre del servidor
$usuario = "root";               // Nombre de usuario de la base de datos
$contraseña = "";                // Contraseña del usuario de la base de datos
$baseDatos = "peis";             // Nombre de la base de datos

$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);  // Crea la conexión con la base de datos

// Verificar si la conexión fue exitosa
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);  // Termina la ejecución si ocurre un error en la conexión
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
*/
?>

  