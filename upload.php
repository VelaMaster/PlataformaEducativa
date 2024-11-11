<<<<<<< HEAD
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
=======
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

    // Obtener la información del archivo subido
    $archivoTmp = $_FILES['archivo']['tmp_name'];
    $archivoNombre = $_FILES['archivo']['name'];
    $archivoTamaño = $_FILES['archivo']['size'];

    // Definir el directorio de destino para almacenar el archivo
    $directorioDestino = "uploads/"; // Puedes cambiar esta ruta
    $archivoDestino = $directorioDestino . basename($archivoNombre);

    // Validar que el archivo no sea demasiado grande (por ejemplo, 10 MB)
    if ($archivoTamaño > 10 * 1024 * 1024) { // 10 MB
        echo "<script>alert('El archivo es demasiado grande.'); window.history.back();</script>";
        exit;
    }

    // Mover el archivo subido al directorio de destino
    if (move_uploaded_file($archivoTmp, $archivoDestino)) {
        // Guardar la ruta del archivo en la base de datos
        $sql = "INSERT INTO entregas (id_tarea, id_alumno, archivo_entrega, fecha_entrega) VALUES (?, ?, ?, NOW())";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iis", $id_tarea, $num_control, $archivoDestino);

        if ($stmt->execute()) {
            echo "<script>alert('Archivo subido correctamente.'); window.location.href = 'gestionTareasAlumno.php';</script>";
        } else {
            echo "<script>alert('Error al guardar los datos en la base de datos.'); window.history.back();</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Error al mover el archivo.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Error: Parámetros faltantes o archivo no válido.'); window.history.back();</script>";
}

$conexion->close();
?>
>>>>>>> a2a1b46207def8b4e85769ff9f52bde6c6ef020e
