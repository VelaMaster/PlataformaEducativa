<?php
// download.php

// Verificar si se ha pasado el nombre del archivo
if (isset($_GET['file'])) {
    $file = $_GET['file'];

    // Asegurarse de que el archivo exista
    $file_path = "uploads/" . $file; // Ajusta el path si es necesario
    if (file_exists($file_path)) {
        // Forzar la descarga del archivo
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        readfile($file_path);
        exit;
    } else {
        echo "El archivo no existe.";
    }
} else {
    echo "No se especificó ningún archivo.";
}
?>
