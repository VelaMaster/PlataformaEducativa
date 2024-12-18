<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require 'db.php';

if (!isset($_SESSION['usuario'])) {
    echo "<script>alert('Error: Usuario no autenticado.'); window.location.href = 'index.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Función para obtener el valor de un dato POST
    function obtenerValor($dato) {
        return is_array($dato) ? $dato[0] : $dato;
    }

    // Sanitizar datos de entrada
    $materia = $conexion->real_escape_string(obtenerValor($_POST['materia'] ?? ''));
    $titulo = $conexion->real_escape_string(obtenerValor($_POST['titulo'] ?? ''));
    $descripcion = $conexion->real_escape_string(obtenerValor($_POST['descripcion'] ?? ''));
    $fechaEntrega = $conexion->real_escape_string(obtenerValor($_POST['fechaEntrega'] ?? ''));
    $archivoPath = null;

    // Manejo del archivo subido
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $archivoNombre = basename($_FILES['archivo']['name']);
        $archivoTmp = $_FILES['archivo']['tmp_name'];
        $archivoPath = "uploads/" . $archivoNombre;

        // Crear el directorio 'uploads' si no existe
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        // Mover archivo al directorio destino
        if (!move_uploaded_file($archivoTmp, $archivoPath)) {
            echo "<script>alert('Error al subir el archivo.');</script>";
            $archivoPath = null;
        }
    }

    // Insertar la tarea en la base de datos
    $sqlTarea = "INSERT INTO tareas (id_curso, titulo, descripcion, archivo_tarea, fecha_creacion, fecha_limite) 
                 VALUES (?, ?, ?, ?, NOW(), ?)";
    $stmt = $conexion->prepare($sqlTarea);
    $stmt->bind_param("issss", $materia, $titulo, $descripcion, $archivoPath, $fechaEntrega);

    if ($stmt->execute()) {
        $tareaId = $conexion->insert_id;

        // Insertar rúbricas si existen
        if (!empty($_POST['criterio']) && !empty($_POST['descripcionCriterio']) && !empty($_POST['puntos'])) {
            $criterios = $_POST['criterio'];
            $descripciones = $_POST['descripcionCriterio'];
            $puntos = $_POST['puntos'];

            foreach ($criterios as $index => $criterio) {
                $criterio = $conexion->real_escape_string(obtenerValor($criterio));
                $descripcionRubrica = $conexion->real_escape_string(obtenerValor($descripciones[$index]));
                $puntosRubrica = (int) obtenerValor($puntos[$index]);

                $sqlRubrica = "INSERT INTO rubricas (id_tarea, criterio, descripcion, puntos) 
                               VALUES (?, ?, ?, ?)";
                $stmtRubrica = $conexion->prepare($sqlRubrica);
                $stmtRubrica->bind_param("issi", $tareaId, $criterio, $descripcionRubrica, $puntosRubrica);

                if (!$stmtRubrica->execute()) {
                    echo "Error al insertar rúbrica: " . $stmtRubrica->error . "<br>";
                }
            }
        }

        // Mensaje de éxito
        echo "<!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <title>Tarea Asignada</title>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'Tarea y rúbricas asignadas correctamente.',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                window.location.href = 'gestionTareasProfesor.php';
            });
        </script>
        </body>
        </html>";
        exit;
    } else {
        // Error al insertar tarea
        echo "<script>alert('Error al asignar la tarea: " . $stmt->error . "');</script>";
    }
}
?>
