<?php
session_start();
include('db.php');

// Verificar si el docente ha iniciado sesión
if (!isset($_SESSION['num_control'])) {
    header('Location: login.php');
    exit();
}
$docente_num_control = $_SESSION['num_control'];

// Verificar si se ha proporcionado id
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Convertir a entero para seguridad

    // Verificar que la tarea pertenece a un curso del docente
    $query_verificar_tarea = "
        SELECT t.*, c.nombre_curso
        FROM tareas t
        JOIN cursos c ON t.id_curso = c.id_curso
        JOIN grupos g ON c.id_curso = g.id_curso
        WHERE t.id = $id AND g.id_docente = '$docente_num_control'
        GROUP BY t.id
    ";
    $result_verificar_tarea = mysqli_query($conexion, $query_verificar_tarea);

    if (mysqli_num_rows($result_verificar_tarea) > 0) {
        // Obtener detalles de la tarea
        $tarea = mysqli_fetch_assoc($result_verificar_tarea);
    } else {
        echo "No tiene acceso a esta tarea.";
        exit();
    }
} else {
    echo "ID de tarea no proporcionado.";
    exit();
}

// Función para obtener la extensión del archivo
function obtenerExtension($archivo) {
    return pathinfo($archivo, PATHINFO_EXTENSION);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles de la Tarea - <?php echo htmlspecialchars($tarea['titulo']); ?></title>
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/detallesTarea.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/barradeNavegacion.css?v=<?php echo time(); ?>">
</head>
<body>
<div class="barranavegacion">
 <div class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Plataforma educativa para Ingenieria en Sistemas</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="inicioProfesor.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="calendarioDocente.php">Calendario</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestionTareasProfesor.php">Asignar tareas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="calificarTareas.php">Calificar tareas</a>
                </li>
            </ul>
        </div>
    </div>
 </div>
</div>

        <!-- Título de la página -->
        <div class="titulo">
            <h1 class="text-center mb-4"><?php echo htmlspecialchars($tarea['titulo']); ?></h1>
        </div>

        <!-- Detalles de la tarea -->
        <div class="detalles-tarea">
            <div class="tarea-detalles">
                <h3>Descripción de la tarea</h3>
                <p><?php echo nl2br(htmlspecialchars($tarea['descripcion'])); ?></p>

                <h4>Detalles</h4>
                <ul>
                    <li><strong>Curso:</strong> <?php echo htmlspecialchars($tarea['nombre_curso']); ?></li>
                    <li><strong>Fecha de creación:</strong> <?php echo htmlspecialchars($tarea['fecha_creacion']); ?></li>
                    <li><strong>Fecha límite:</strong> <?php echo htmlspecialchars($tarea['fecha_limite']); ?></li>
                </ul>
            </div>
        </div>
        <!-- Previsualización del archivo adjunto -->
        <div class="archivo-adjunto-container">
            <?php if (!empty($tarea['archivo_tarea'])): ?>
                <div class="archivo-adjunto">
                    <h3>Archivo adjunto</h3>
                    <?php
                    $archivo = htmlspecialchars($tarea['archivo_tarea']);
                    $extension = strtolower(obtenerExtension($archivo));

                    // Mostrar previsualización según el tipo de archivo
                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                        echo '<img src="' . $archivo . '" alt="Imagen adjunta" class="img-fluid">';
                    } elseif ($extension == 'pdf') {
                        echo '<embed src="' . $archivo . '" type="application/pdf" width="100%" height="600px" />';
                    } elseif (in_array($extension, ['mp4', 'webm', 'ogg'])) {
                        echo '<video controls width="100%"><source src="' . $archivo . '" type="video/' . $extension . '"></video>';
                    } elseif (in_array($extension, ['mp3', 'wav', 'ogg'])) {
                        echo '<audio controls><source src="' . $archivo . '" type="audio/' . $extension . '"></audio>';
                    } elseif (in_array($extension, ['txt'])) {
                        $contenido = file_get_contents($archivo);
                        echo '<pre>' . htmlspecialchars($contenido) . '</pre>';
                    } else {
                        echo '<p>No se puede previsualizar este tipo de archivo. <a href="' . $archivo . '" download>Descargar archivo</a></p>';
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>  
<br>
<br>
    <script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>

