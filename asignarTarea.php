<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

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

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Función para manejar valores que puedan ser arreglos
    function obtenerValor($dato) {
        if (is_array($dato)) {
            return $dato[0]; // Tomar el primer valor del arreglo
        }
        return $dato; // Retornar el valor si no es un arreglo
    }

    // Recibir datos del formulario y procesar posibles arreglos
    $materia = $conexion->real_escape_string(obtenerValor($_POST['materia'] ?? ''));
    $titulo = $conexion->real_escape_string(obtenerValor($_POST['titulo'] ?? ''));
    $descripcion = $conexion->real_escape_string(obtenerValor($_POST['descripcion'] ?? ''));
    $fechaEntrega = $conexion->real_escape_string(obtenerValor($_POST['fechaEntrega'] ?? ''));

    // Manejar archivo adjunto
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

    // Insertar tarea en la base de datos
    $sqlTarea = "INSERT INTO tareas (id_curso, titulo, descripcion, fecha_creacion, fecha_limite, archivo_tarea) 
                 VALUES ('$materia', '$titulo', '$descripcion', NOW(), '$fechaEntrega', '$archivoPath')";

    if ($conexion->query($sqlTarea) === TRUE) {
        $tareaId = $conexion->insert_id;

        // Insertar rúbricas asociadas (si existen)
        if (!empty($_POST['criterio']) && !empty($_POST['descripcionCriterio']) && !empty($_POST['puntos'])) {
            $criterios = $_POST['criterio'];
            $descripciones = $_POST['descripcionCriterio'];
            $puntos = $_POST['puntos'];

            foreach ($criterios as $index => $criterio) {
                $criterio = $conexion->real_escape_string(obtenerValor($criterio));
                $descripcionRubrica = $conexion->real_escape_string(obtenerValor($descripciones[$index]));
                $puntosRubrica = (int)obtenerValor($puntos[$index]);

                $sqlRubrica = "INSERT INTO rubricas (id_tarea, criterio, descripcion, puntos) 
                               VALUES ('$tareaId', '$criterio', '$descripcionRubrica', '$puntosRubrica')";

                if (!$conexion->query($sqlRubrica)) {
                    echo "Error al insertar rúbrica: " . $conexion->error . "<br>";
                }
            }
        }

        // Confirmación de éxito con SweetAlert
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
        echo "<script>alert('Error al asignar la tarea: " . $conexion->error . "');</script>";
    }
}

$conexion->close();
?>
