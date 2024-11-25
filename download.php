<?php
// download.php

// Verificar si se ha pasado el nombre del archivo
if (isset($_GET['file'])) {
    $file = $_GET['file'];

    // Asegurarse de que el archivo exista
    $file_path = __DIR__ . "/uploads/" . basename($file); // Ajusta el path si es necesario
    if (file_exists($file_path)) {
        // Determinar el tipo de contenido del archivo
        $mime_type = mime_content_type($file_path);
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: inline; filename="' . basename($file_path) . '"'); // inline para visualizar

        readfile($file_path);
        exit;
    } else {
        echo "El archivo no existe.";
    }
} else {
    echo "No se especificó ningún archivo.";
}
