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

// Incluir la conexión global
require_once 'db.php';

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// 1. Obtener el ID del docente basado en num_control
$query_docente = "SELECT id FROM docentes WHERE num_control = '$num_control'";
$result_docente = $conexion->query($query_docente);

if ($result_docente && $row_docente = $result_docente->fetch_assoc()) {
    $id_docente = $row_docente['id'];
} else {
    echo "<script>alert('Error: Docente no encontrado.'); window.location.href = 'index.php';</script>";
    exit;
}

// 2. Obtener los grupos asignados al docente usando id_docente
$query_grupos = "
    SELECT g.id, g.nombre_grupo
    FROM grupos g
    WHERE g.id_docente = '$id_docente'";
$result_grupos = $conexion->query($query_grupos);

if (!$result_grupos) {
    die("Error en la consulta de grupos: " . $conexion->error);
}

// Validar selección de grupo
$id_grupo_seleccionado = isset($_GET['id_grupo']) ? intval($_GET['id_grupo']) : 0;

// Validar filtro de tareas
$filtro_tareas = isset($_GET['filtro']) ? $_GET['filtro'] : 'todas'; // Default: todas

// **Nuevo:** Obtener el número total de estudiantes en el grupo seleccionado
$total_estudiantes = 0;
if ($id_grupo_seleccionado > 0) {
    $query_total_estudiantes = "
        SELECT COUNT(*) AS total_estudiantes
        FROM grupo_alumnos
        WHERE id_grupo = $id_grupo_seleccionado";
    $result_total_estudiantes = $conexion->query($query_total_estudiantes);
    if ($result_total_estudiantes && $row_total = $result_total_estudiantes->fetch_assoc()) {
        $total_estudiantes = intval($row_total['total_estudiantes']);
    }
}

// Construir consulta de tareas según el filtro
$query_tareas = "
    SELECT t.titulo, t.descripcion, t.fecha_limite, t.id
    FROM tareas t
    INNER JOIN grupos g ON t.id_curso = g.id_curso
    WHERE g.id = $id_grupo_seleccionado AND g.id_docente = '$id_docente'";

// **Modificado:** Añadir condición según el filtro seleccionado
if ($filtro_tareas == 'no_calificadas') {
    // Tareas que tienen al menos una entrega sin calificar o alguna entrega pendiente
    $query_tareas .= " AND EXISTS (
        SELECT 1 
        FROM grupo_alumnos ga
        LEFT JOIN entregas e ON ga.num_control = e.id_alumno AND e.id_tarea = t.id
        WHERE ga.id_grupo = $id_grupo_seleccionado 
          AND (e.calificacion IS NULL OR e.id_tarea IS NULL)
    )";
} elseif ($filtro_tareas == 'calificadas') {
    if ($total_estudiantes > 0) {
        // Tareas donde el número de entregas calificadas es igual al número total de estudiantes
        $query_tareas .= " AND (
            SELECT COUNT(e.calificacion)
            FROM grupo_alumnos ga
            LEFT JOIN entregas e ON ga.num_control = e.id_alumno AND e.id_tarea = t.id
            WHERE ga.id_grupo = $id_grupo_seleccionado AND e.calificacion IS NOT NULL
        ) = $total_estudiantes";
    } else {
        // Si no hay estudiantes, no se mostrarán tareas calificadas
        $query_tareas .= " AND FALSE";
    }
}
$result_tareas = $conexion->query($query_tareas);

if (!$result_tareas) {
    die("Error en la consulta de tareas: " . $conexion->error);
}

// Validar selección de tarea
$id_tarea_seleccionada = isset($_GET['id_tarea']) ? intval($_GET['id_tarea']) : 0;

// Obtener las entregas de la tarea seleccionada
if ($id_tarea_seleccionada > 0) {
    $query_entregas = "
        SELECT e.archivo_entrega, e.fecha_entrega, e.calificacion, e.id AS id_entrega,
               CONCAT(a.nombre, ' ', a.apellido_p, ' ', a.apellido_m) AS alumno_nombre
        FROM entregas e
        INNER JOIN alumnos a ON e.id_alumno = a.id
        INNER JOIN grupo_alumnos ga ON ga.num_control = a.num_control
        WHERE e.id_tarea = $id_tarea_seleccionada
          AND ga.id_grupo = $id_grupo_seleccionado
    ";
    $result_entregas = $conexion->query($query_entregas);

    if (!$result_entregas) {
        die("Error en la consulta de entregas: " . $conexion->error);
    }
} else {
    $result_entregas = null;
}
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

<div class="container mt-4">
    <h1 class="mb-4">Calificar Tareas</h1>

    <!-- Selección de grupo -->
    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-6">
                <label for="id_grupo" class="form-label">Seleccionar Grupo:</label>
                <select name="id_grupo" id="id_grupo" class="form-select" onchange="this.form.submit()">
                    <option value="0">-- Seleccionar Grupo --</option>
                    <?php
                    // Verificar si hay grupos para mostrar
                    if ($result_grupos->num_rows > 0) {
                        while ($grupo = $result_grupos->fetch_assoc()):
                    ?>
                        <option value="<?= htmlspecialchars($grupo['id']) ?>" <?= $id_grupo_seleccionado == $grupo['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($grupo['nombre_grupo']) ?>
                        </option>
                    <?php
                        endwhile;
                    } else {
                        echo '<option value="0">No hay grupos disponibles</option>';
                    }
                    ?>
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
        <?php if ($result_tareas && $result_tareas->num_rows > 0): ?>
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
                                <a href="?id_grupo=<?= $id_grupo_seleccionado ?>&filtro=<?= htmlspecialchars($filtro_tareas) ?>&id_tarea=<?= $tarea['id'] ?>" class="btn btn-primary">Ver Entregas</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">No hay tareas que cumplan con el filtro seleccionado.</div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Modal de Confirmación para Recalificar -->
    <div class="modal fade" id="confirmRecalificarModal" tabindex="-1" aria-labelledby="confirmRecalificarModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header"> 
            <h5 class="modal-title" id="confirmRecalificarModalLabel">Recalificar Entrega</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            ¿Estás seguro de que deseas volver a calificar esta entrega?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <a href="#" id="confirmRecalificarBtn" class="btn btn-primary">Sí, recalificar</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabla de entregas -->
    <?php if ($id_tarea_seleccionada): ?>
        <h3>Entregas de la Tarea</h3>
        <?php if ($result_entregas && $result_entregas->num_rows > 0): ?>
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
                            <td>
                                <?php if ($entrega['archivo_entrega']): ?>
                                    <a href="<?= htmlspecialchars($entrega['archivo_entrega']) ?>" target="_blank">Descargar</a>
                                <?php else: ?>
                                    No Entregado
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($entrega['fecha_entrega']) ?: 'N/A' ?></td>
                            <td><?= $entrega['calificacion'] !== null ? htmlspecialchars($entrega['calificacion']) : 'Sin Calificar' ?></td>
                            <td><?= htmlspecialchars($entrega['alumno_nombre']) ?></td>
                            <td>
                                <?php if ($entrega['id_entrega']): ?>
                                    <?php if ($entrega['calificacion'] !== null): ?>
                                        <span class="badge bg-info ms-2 calificada-badge" data-bs-toggle="modal" data-bs-target="#confirmRecalificarModal" data-id-entrega="<?= $entrega['id_entrega'] ?>" style="cursor: pointer;">
                                            Calificada
                                        </span>
                                    <?php else: ?>
                                        <a href="calificarEntrega.php?id_entrega=<?= $entrega['id_entrega'] ?>" class="btn btn-success btn-sm">Calificar</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">No hay entregas para esta tarea.</div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
// Espera a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function () {
    var confirmRecalificarModal = document.getElementById('confirmRecalificarModal');
    var confirmRecalificarBtn = document.getElementById('confirmRecalificarBtn');

    // Escucha el evento que se dispara cuando el modal se muestra
    confirmRecalificarModal.addEventListener('show.bs.modal', function (event) {
        // Obtiene el elemento que disparó el modal
        var triggerElement = event.relatedTarget;
        // Extrae el id_entrega del atributo data
        var idEntrega = triggerElement.getAttribute('data-id-entrega');

        // Actualiza el href del botón de confirmación
        confirmRecalificarBtn.setAttribute('href', 'calificarEntrega.php?id_entrega=' + idEntrega);
    });
});
</script>

<script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conexion->close();
?>
