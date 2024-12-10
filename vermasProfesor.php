<!DOCTYPE html>
<html lang="es">
<head>
<?php
    session_start();
    include('db.php');
    if (!isset($_SESSION['num_control'])) {
        header('Location: login.php');
        exit();
    }
    $docente_num_control = $_SESSION['num_control'];
    $query_docente = "SELECT id FROM docentes WHERE num_control = '$docente_num_control'";
    $result_docente = mysqli_query($conexion, $query_docente);
    $docente_data = mysqli_fetch_assoc($result_docente);
    $docente_id = $docente_data['id'];
    if (isset($_GET['id_curso'])) {
        $id_curso = intval($_GET['id_curso']);
        $query_verificar_curso = "SELECT * FROM grupos WHERE id_curso = $id_curso AND id_docente = '$docente_id'";
        $result_verificar_curso = mysqli_query($conexion, $query_verificar_curso);

        if (mysqli_num_rows($result_verificar_curso) > 0) {
            $query_curso = "SELECT nombre_curso FROM cursos WHERE id = $id_curso";
            $result_curso = mysqli_query($conexion, $query_curso);
            if ($row_curso = mysqli_fetch_assoc($result_curso)) {
                $nombre_curso = $row_curso['nombre_curso'];
            } else {
                $nombre_curso = "Curso desconocido";
            }
            $query_grupos = "SELECT * FROM grupos WHERE id_docente = '$docente_id' AND id_curso = $id_curso";
            $result_grupos = mysqli_query($conexion, $query_grupos);
            $grupos = array();
            while ($row_grupo = mysqli_fetch_assoc($result_grupos)) {
                $grupo_id = $row_grupo['id'];
                $grupo_nombre = $row_grupo['nombre_grupo'];
                $query_alumnos = "SELECT alumnos.* FROM alumnos
                    JOIN grupo_alumnos ON alumnos.num_control = grupo_alumnos.num_control
                    WHERE grupo_alumnos.id_grupo = '$grupo_id'";
                $result_alumnos = mysqli_query($conexion, $query_alumnos);
                $alumnos = array();
                while ($row_alumno = mysqli_fetch_assoc($result_alumnos)) {
                    $alumnos[] = $row_alumno;
                }
                $grupos[] = array(
                    'id_grupo' => $grupo_id,
                    'nombre_grupo' => $grupo_nombre,
                    'alumnos' => $alumnos
                );
            }
            $query_tareas = "SELECT * FROM tareas WHERE id_curso = $id_curso";
            $result_tareas = mysqli_query($conexion, $query_tareas);
            $tareas = array();
            while ($row_tarea = mysqli_fetch_assoc($result_tareas)) {
                $tareas[] = $row_tarea;
            }
        } else {
            echo "No tiene acceso a este curso.";
            exit();
        }

    } else {
        echo "ID de curso no proporcionado.";
        exit();
    }
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opciones del Profesor</title>
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/barradeNavegacion.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/vermasProfesor.css?v=<?php echo time(); ?>">
</head>
<body>
<div class="barranavegacion">
    <div class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Plataforma educativa para Ingeniería en Sistemas</a>
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
<div class="container mt-5">
    <h1 class="text-center mb-4"><?php echo htmlspecialchars($nombre_curso); ?></h1>
    <!-- Tabs -->
    <ul class="nav nav-tabs" id="profesorTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="grupo-tab" data-bs-toggle="tab" data-bs-target="#grupo" type="button" role="tab" aria-controls="grupo" aria-selected="true">
                Lista del Grupo
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tareas-tab" data-bs-toggle="tab" data-bs-target="#tareas" type="button" role="tab" aria-controls="tareas" aria-selected="false">
                Lista de Tareas
            </button>
        </li>
    </ul>
    <div class="tab-content mt-4" id="profesorTabsContent">
        <!-- Tab de Lista del Grupo -->
        <div class="tab-pane fade show active" id="grupo" role="tabpanel" aria-labelledby="grupo-tab">
            <?php foreach ($grupos as $grupo): ?>
                <h3><?php echo htmlspecialchars($grupo['nombre_grupo']); ?></h3>
                <p>Estudiantes inscritos: <?php echo count($grupo['alumnos']); ?></p>
                <?php foreach ($grupo['alumnos'] as $alumno): ?>
                    <div class="estudiante">
                        <span><?php echo htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellido_p'] . ' ' . $alumno['apellido_m']); ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>

        <!-- Tab de Lista de Tareas -->
        <div class="tab-pane fade" id="tareas" role="tabpanel" aria-labelledby="tareas-tab">
            <?php
            $meses_espanol = array(
                '01' => 'enero',
                '02' => 'febrero',
                '03' => 'marzo',
                '04' => 'abril',
                '05' => 'mayo',
                '06' => 'junio',
                '07' => 'julio',
                '08' => 'agosto',
                '09' => 'septiembre',
                '10' => 'octubre',
                '11' => 'noviembre',
                '12' => 'diciembre',
            );
            $tareas_por_mes = array();
            foreach ($tareas as $tarea) {
                $fecha_limite = $tarea['fecha_limite'];
                $mes_num = date('m', strtotime($fecha_limite));
                $mes = $meses_espanol[$mes_num];
                $tareas_por_mes[$mes][] = $tarea;
            }
            foreach ($tareas_por_mes as $mes => $tareas_mes):
            ?>
                <h4><?php echo ucfirst(htmlspecialchars($mes)); ?></h4>
                <?php foreach ($tareas_mes as $tarea): ?>
                    <div class="tarea">
                        <?php echo htmlspecialchars($tarea['titulo']); ?>
                        <button class="btn-ver-mas" onclick="window.location.href='detallesTarea.php?id_tarea=<?php echo $tarea['id']; ?>';">
                            Ver más
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
<script>
function toggleVerMas(element) {
    const tareas = document.querySelectorAll('.tarea');
    tareas.forEach(tarea => {
        if (tarea !== element) {
            tarea.classList.remove('active');
        }
    });
    element.classList.toggle('active');
}
</script>
<script>
document.querySelectorAll('.tarea').forEach(tarea => {
    let timeout;
    tarea.addEventListener('mouseenter', () => {
        const btnVerMas = tarea.querySelector('.btn-ver-mas');
        clearTimeout(timeout);
        btnVerMas.style.opacity = '1';
        btnVerMas.style.visibility = 'visible';
    });
    tarea.addEventListener('mouseleave', () => {
        const btnVerMas = tarea.querySelector('.btn-ver-mas');
        timeout = setTimeout(() => {
            btnVerMas.style.opacity = '0';
            btnVerMas.style.visibility = 'hidden';
        }, 30000);
    });
});
</script>
</body>
</html>
