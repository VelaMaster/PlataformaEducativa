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
    function obtenerValor($dato) {
        if (is_array($dato)) {
            return $dato[0];
        }
        return $dato;
    }
    $materia = $conexion->real_escape_string(obtenerValor($_POST['materia'] ?? ''));
    $titulo = $conexion->real_escape_string(obtenerValor($_POST['titulo'] ?? ''));
    $descripcion = $conexion->real_escape_string(obtenerValor($_POST['descripcion'] ?? ''));
    $fechaEntrega = $conexion->real_escape_string(obtenerValor($_POST['fechaEntrega'] ?? ''));
    $archivoPath = null;
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $archivoNombre = basename($_FILES['archivo']['name']);
        $archivoTmp = $_FILES['archivo']['tmp_name'];
        $archivoPath = "uploads/" . $archivoNombre;

        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        if (!move_uploaded_file($archivoTmp, $archivoPath)) {
            echo "<script>alert('Error al subir el archivo.');</script>";
            $archivoPath = null;
        }
    }
    $sqlTarea = "INSERT INTO tareas (id_curso, titulo, descripcion, archivo_tarea, fecha_creacion, fecha_limite) 
                 VALUES (?, ?, ?, ?, NOW(), ?)";

    $stmt = $conexion->prepare($sqlTarea);
    $stmt->bind_param("issss", $materia, $titulo, $descripcion, $archivoPath, $fechaEntrega);

    if ($stmt->execute()) {
        $tareaId = $conexion->insert_id;
        if (!empty($_POST['criterio']) && !empty($_POST['descripcionCriterio']) && !empty($_POST['puntos'])) {
            $criterios = $_POST['criterio'];
            $descripciones = $_POST['descripcionCriterio'];
            $puntos = $_POST['puntos'];

            foreach ($criterios as $index => $criterio) {
                $criterio = $conexion->real_escape_string(obtenerValor($criterio));
                $descripcionRubrica = $conexion->real_escape_string(obtenerValor($descripciones[$index]));
                $puntosRubrica = (int)obtenerValor($puntos[$index]);

                $sqlRubrica = "INSERT INTO rubricas (id_tarea, criterio, descripcion, puntos) 
                               VALUES (?, ?, ?, ?)";

                $stmtRubrica = $conexion->prepare($sqlRubrica);
                $stmtRubrica->bind_param("issi", $tareaId, $criterio, $descripcionRubrica, $puntosRubrica);

                if (!$stmtRubrica->execute()) {
                    echo "Error al insertar rúbrica: " . $stmtRubrica->error . "<br>";
                }
            }
        }
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
        echo "<script>alert('Error al asignar la tarea: " . $stmt->error . "');</script>";
    }
}
?>
