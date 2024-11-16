<?php
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
    $materia = $_POST['materia'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fechaEntrega = $_POST['fechaEntrega'];
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
    $sql = "INSERT INTO tareas (id_curso, titulo, descripcion, fecha_limite, archivo_tarea) VALUES ('$materia', '$titulo', '$descripcion', '$fechaEntrega', '$archivoPath')";
    if ($conexion->query($sql) === TRUE) {
        $tarea_id = $conexion->insert_id;

        if (isset($_POST['criterios']) && isset($_POST['descripciones']) && isset($_POST['puntos'])) {
            $criterios = $_POST['criterios'];
            $descripciones = $_POST['descripciones'];
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
            echo "<script>alert('Tarea y rúbrica asignadas con éxito.'); window.location.href = 'gestionTareasProfesor.php';</script>";
        } else {
            echo "<script>alert('Tarea asignada sin rúbrica.'); window.location.href = 'gestionTareasProfesor.php';</script>";
        }
    } else {
        echo "<script>alert('Error al asignar la tarea: " . $conexion->error . "');</script>";
    }

    $conexion->close();
}
?>