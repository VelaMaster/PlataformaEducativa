<?php
// Verificar si se recibió el archivo como parámetro
if (!isset($_GET['archivo_tarea']) || empty($_GET['archivo_tarea'])) {
    die("No se especificó un archivo para previsualizar.");
}

// Escapar el nombre del archivo para evitar inyecciones
$archivo = basename($_GET['archivo_tarea']); // Sanitiza el nombre del archivo
$rutaArchivo = __DIR__ . "/uploads/" . $archivo; // Ruta completa

// Verificar si el archivo existe
if (!file_exists($rutaArchivo)) {
    die("El archivo no existe en el servidor.");
}

// Obtener el tipo MIME del archivo
$mime = mime_content_type($rutaArchivo);

// Establecer encabezados según el tipo de archivo
if (strstr($mime, "image")) {
    // Si es una imagen
    header("Content-Type: $mime");
    readfile($rutaArchivo);
} elseif ($mime === "application/pdf") {
    // Si es un PDF
    header("Content-Type: application/pdf");
    header("Content-Disposition: inline; filename=\"$archivo\"");
    readfile($rutaArchivo);
} else {
    // Para otros archivos
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"$archivo\"");
    readfile($rutaArchivo);
}
?>
