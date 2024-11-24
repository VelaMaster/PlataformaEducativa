<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $materia = $conexion->real_escape_string($_POST['materia']);
    $titulo = $conexion->real_escape_string($_POST['titulo']);
    $descripcion = $conexion->real_escape_string($_POST['descripcion']);
    $fechaEntrega = $conexion->real_escape_string($_POST['fechaEntrega']);
    $archivoPath = null;

    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == UPLOAD_ERR_OK) {
        $archivoNombre = $_FILES['archivo']['name'];
        $archivoTmp = $_FILES['archivo']['tmp_name'];
        $archivoPath = "uploads/" . basename($archivoNombre);

        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        if (!move_uploaded_file($archivoTmp, $archivoPath)) {
            echo "<script>alert('Error al subir el archivo.');</script>";
            $archivoPath = null;
        }
    }

    $sql = "INSERT INTO tareas (id_curso, titulo, descripcion, fecha_creacion, fecha_limite, archivo_tarea) VALUES ('$materia', '$titulo', '$descripcion', NOW(), '$fechaEntrega', '$archivoPath')";

    if ($conexion->query($sql) === TRUE) {
        $tarea_id = $conexion->insert_id;

        if (isset($_POST['criterio']) && isset($_POST['descripcionCriterio']) && isset($_POST['puntos'])) {
            $criterios = $_POST['criterio'];
            $descripciones = $_POST['descripcionCriterio'];
            $puntos = $_POST['puntos'];

            for ($i = 0; $i < count($criterios); $i++) {
                $criterio = $conexion->real_escape_string($criterios[$i]);
                $descripcionRubrica = $conexion->real_escape_string($descripciones[$i]);
                $puntaje_maximo = (int)$puntos[$i];

                $sqlRubrica = "INSERT INTO rubricas (id_tarea, criterio, descripcion, puntos) VALUES ('$tarea_id', '$criterio', '$descripcionRubrica', '$puntaje_maximo')";
                if (!$conexion->query($sqlRubrica)) {
                    echo "Error al insertar rúbrica: " . $conexion->error . "<br>";
                }
            }
            $mensajeExito = 'Tarea y rúbrica asignadas con éxito.';
        } else {
            $mensajeExito = 'Tarea asignada sin rúbrica.';
        }

        // Mostrar mensaje de éxito con SweetAlert2
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
                text: '$mensajeExito',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                window.location.href = 'gestionTareasProfesor.php';
            });
        </script>
        </body>
        </html>";
        exit();
    } else {
        echo "<script>alert('Error al asignar la tarea: " . $conexion->error . "');</script>";
    }

    $conexion->close();
}
?>
