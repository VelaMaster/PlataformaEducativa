<?php
session_start();
if (isset($_SESSION['usuario'])) {
    $num_control = $_SESSION['usuario'];
} else {
    echo "<script>alert('Error: Usuario no autenticado.'); window.location.href = 'index.php';</script>";
    exit;
}

// Mostrar errores (para depuración)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";
$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener los grupos asignados al docente
$query_grupos = "
    SELECT g.id_grupo, g.nombre_grupo
    FROM grupos g
    WHERE g.id_docente = '$num_control'";
$result_grupos = $conexion->query($query_grupos);

// Validar selección de grupo
$id_grupo_seleccionado = isset($_GET['id_grupo']) ? intval($_GET['id_grupo']) : 0;

// Validar filtro de tareas
$filtro_tareas = isset($_GET['filtro']) ? $_GET['filtro'] : 'todas'; // Default: todas

// Construir consulta de tareas según el filtro
$query_tareas = "
    SELECT t.titulo, t.descripcion, t.fecha_limite, t.id_tarea
    FROM tareas t
    INNER JOIN grupos g ON t.id_curso = g.id_curso
    WHERE g.id_grupo = $id_grupo_seleccionado AND g.id_docente = '$num_control'";

// Añadir condición según el filtro seleccionado
if ($filtro_tareas == 'no_calificadas') {
    $query_tareas .= " AND NOT EXISTS (
        SELECT 1 FROM entregas e WHERE e.id_tarea = t.id_tarea AND e.calificacion IS NOT NULL
    )";
} elseif ($filtro_tareas == 'calificadas') {
    $query_tareas .= " AND EXISTS (
        SELECT 1 FROM entregas e WHERE e.id_tarea = t.id_tarea AND e.calificacion IS NOT NULL
    )";
}
$result_tareas = $conexion->query($query_tareas);

// Validar selección de tarea
$id_tarea_seleccionada = isset($_GET['id_tarea']) ? intval($_GET['id_tarea']) : 0;

// Obtener las entregas de la tarea seleccionada
$query_entregas = "
    SELECT e.archivo_entrega, e.fecha_entrega, e.calificacion, e.id_entrega,
           CONCAT(a.nombre, ' ', a.apellido_p, ' ', a.apellido_m) AS alumno_nombre
    FROM entregas e
    INNER JOIN alumnos a ON e.id_alumno = a.num_control
    WHERE e.id_tarea = $id_tarea_seleccionada";
$result_entregas = $conexion->query($query_entregas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificar Tareas</title>
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/barradeNavegacion.css">
    <link rel="stylesheet" href="css/calificarTareas.css?v=<?php echo time(); ?>">
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
                    <a class="nav-link" href="calendarioProfesor.php">Calendario</a>
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

<div class="container mt-4">
    <h1 class="mb-4">Calificar Tareas</h1>

    <!-- Selección de grupo -->
    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-6">
                <label for="id_grupo" class="form-label">Seleccionar Grupo:</label>
                <select name="id_grupo" id="id_grupo" class="form-select" onchange="this.form.submit()">
                    <option value="0">-- Seleccionar Grupo --</option>
                    <?php while ($grupo = $result_grupos->fetch_assoc()): ?>
                        <option value="<?= $grupo['id_grupo'] ?>" <?= $id_grupo_seleccionado == $grupo['id_grupo'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($grupo['nombre_grupo']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="filtro" class="form-label">Filtrar Tareas:</label>
                <select name="filtro" id="filtro" class="form-select" onchange="this.form.submit()">
                    <option value="todas" <?= $filtro_tareas == 'todas' ? 'selected' : '' ?>>Todas las tareas</option>
                    <option value="no_calificadas" <?= $filtro_tareas == 'no_calificadas' ? 'selected' : '' ?>>Tareas No calificadas</option>
                    <option value="calificadas" <?= $filtro_tareas == 'calificadas' ? 'selected' : '' ?>>Tareas Calificadas</option>
                </select>
            </div>
        </div>
    </form>

    <!-- Tabla de tareas -->
    <?php if ($id_grupo_seleccionado): ?>
        <h3>Tareas</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Fecha Límite</th>
                    <th>Seleccionar</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($tarea = $result_tareas->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($tarea['titulo']) ?></td>
                        <td><?= htmlspecialchars($tarea['descripcion']) ?></td>
                        <td><?= htmlspecialchars($tarea['fecha_limite']) ?></td>
                        <td>
                            <a href="?id_grupo=<?= $id_grupo_seleccionado ?>&filtro=<?= $filtro_tareas ?>&id_tarea=<?= $tarea['id_tarea'] ?>" class="btn btn-primary">Ver Entregas</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Tabla de entregas -->
    <?php if ($id_tarea_seleccionada): ?>
        <h3>Entregas de la Tarea</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Archivo</th>
                    <th>Fecha de Entrega</th>
                    <th>Calificación</th>
                    <th>Alumno</th>
                    <th>Seleccionar</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($entrega = $result_entregas->fetch_assoc()): ?>
                    <tr>
                        <td><a href="<?= htmlspecialchars($entrega['archivo_entrega']) ?>" target="_blank">Descargar</a></td>
                        <td><?= htmlspecialchars($entrega['fecha_entrega']) ?></td>
                        <td><?= $entrega['calificacion'] !== null ? htmlspecialchars($entrega['calificacion']) : 'Sin Calificar' ?></td>
                        <td><?= htmlspecialchars($entrega['alumno_nombre']) ?></td>
                        <td>
                            <a href="calificarEntrega.php?id_entrega=<?= $entrega['id_entrega'] ?>" class="btn btn-success">Calificar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conexion->close();
?>