<?php
// Configuración de conexión a la base de datos
$servername = "localhost";
$username = "root"; // Cambia esto si tu usuario de MySQL es diferente
$password = ""; // Cambia esto si tienes una contraseña para MySQL
$database = "PEIS"; // Cambia esto al nombre de tu base de datos si es diferente

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Variables de ejemplo para `id_tarea` e `id_alumno`
$id_tarea = 1;  // Reemplaza con el ID real de la tarea
$id_alumno = 123;  // Reemplaza con el ID real del alumno

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["archivo"])) {
    $nombreArchivo = $_FILES["archivo"]["name"];
    $rutaArchivo = "uploads/" . basename($nombreArchivo);

    // Verificar si el directorio "uploads" existe, si no, crearlo
    if (!is_dir("uploads")) {
        mkdir("uploads");
    }

    // Mover el archivo subido a la carpeta "uploads"
    if (move_uploaded_file($_FILES["archivo"]["tmp_name"], $rutaArchivo)) {
        // Insertar los detalles del archivo en la base de datos
        $stmt = $conn->prepare("INSERT INTO entregas (id_tarea, id_alumno, archivo_entrega, fecha_entrega) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $id_tarea, $id_alumno, $rutaArchivo);

        if ($stmt->execute()) {
            echo "El archivo se ha subido y guardado en la base de datos correctamente.";
        } else {
            echo "Error al guardar en la base de datos: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Hubo un error al subir el archivo.";
    }
}

$conn->close();
?>
