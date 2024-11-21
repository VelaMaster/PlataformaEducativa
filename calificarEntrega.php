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

// Validar si se pasó un id_entrega
if (!isset($_GET['id_entrega']) || empty($_GET['id_entrega'])) {
    echo "<script>alert('Error: No se especificó ninguna entrega.'); window.location.href = 'calificarTareas.php';</script>";
    exit;
}

$id_entrega = intval($_GET['id_entrega']);

// Obtener detalles de la entrega
$query_entrega = "
    SELECT e.id_tarea, e.archivo_entrega, e.fecha_entrega, e.calificacion, e.retroalimentacion,
           CONCAT(a.nombre, ' ', a.apellido_p, ' ', a.apellido_m) AS alumno_nombre,
           t.titulo AS titulo_tarea, t.descripcion AS descripcion_tarea
    FROM entregas e
    INNER JOIN alumnos a ON e.id_alumno = a.num_control
    INNER JOIN tareas t ON e.id_tarea = t.id_tarea
    WHERE e.id_entrega = $id_entrega";
$result_entrega = $conexion->query($query_entrega);

if ($result_entrega->num_rows == 0) {
    echo "<script>alert('Error: La entrega no existe.'); window.location.href = 'calificarTareas.php';</script>";
    exit;
}

$entrega = $result_entrega->fetch_assoc();

// Obtener rúbrica asociada a la tarea
$query_rubrica = "
    SELECT id_rubrica, criterio, descripcion, puntos
    FROM rubricas
    WHERE id_tarea = " . intval($entrega['id_tarea']);
$result_rubrica = $conexion->query($query_rubrica);

// Guardar calificación y retroalimentación si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $calificacion_total = isset($_POST['calificacion_total']) ? intval($_POST['calificacion_total']) : null;
    $retroalimentacion = isset($_POST['retroalimentacion']) ? $conexion->real_escape_string($_POST['retroalimentacion']) : null;

    if ($calificacion_total === null || $calificacion_total < 0 || $calificacion_total > 100) {
        echo "<script>alert('Error: La calificación debe ser un número entre 0 y 100.');</script>";
    } else {
        $query_actualizar = "
            UPDATE entregas 
            SET calificacion = $calificacion_total, retroalimentacion = '$retroalimentacion' 
            WHERE id_entrega = $id_entrega";
        if ($conexion->query($query_actualizar) === TRUE) {
            echo "<script>alert('Calificación y retroalimentación guardadas correctamente.'); window.location.href = 'calificarTareas.php';</script>";
        } else {
            echo "<script>alert('Error al guardar la calificación.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificar Tarea</title>
    <link rel="stylesheet" href="css/calificarEntrega.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/barradeNavegacion.css?v=<?php echo time(); ?>">
    <script>
        function actualizarCalificacionTotal() {
            const inputs = document.querySelectorAll('.calificacion-criterio');
            let total = 0;

            inputs.forEach(input => {
                const value = parseInt(input.value) || 0;
                total += value;
            });

            document.getElementById('calificacion_total').value = total > 100 ? 100 : total; // Máximo de 100
        }
    </script>
</head>
<body>
<div class="barranavegacion">
 <div class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Plataforma educativa para Ingenieria en Sistemas</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" 
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
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
    <h1 class="mb-4">Calificar Entrega</h1>

    <!-- Detalles de la entrega -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Detalles de la Entrega</h5>
        </div>
        <div class="card-body">
            <p><strong>Alumno:</strong> <?= htmlspecialchars($entrega['alumno_nombre']) ?></p>
            <p><strong>Tarea:</strong> <?= htmlspecialchars($entrega['titulo_tarea']) ?></p>
            <p><strong>Descripción:</strong> <?= htmlspecialchars($entrega['descripcion_tarea']) ?></p>
            <p><strong>Fecha de Entrega:</strong> <?= htmlspecialchars($entrega['fecha_entrega']) ?></p>
            <p><strong>Archivo:</strong> <a href="<?= htmlspecialchars($entrega['archivo_entrega']) ?>" target="_blank">Descargar</a></p>
            <p><strong>Calificación Actual:</strong> <?= $entrega['calificacion'] !== null ? htmlspecialchars($entrega['calificacion']) : 'Sin Calificar' ?></p>
            <p><strong>Retroalimentación Actual:</strong> <?= $entrega['retroalimentacion'] !== null ? htmlspecialchars($entrega['retroalimentacion']) : 'Sin Retroalimentación' ?></p>
        </div>
    </div>

    <!-- Mostrar Rúbrica -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Rúbrica de Evaluación</h5>
        </div>
        <div class="card-body">
            <?php if ($result_rubrica->num_rows > 0): ?>
                <form method="POST" id="formCalificarEntrega" oninput="actualizarCalificacionTotal()">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Criterio</th>
                                <th>Descripción</th>
                                <th>Puntos</th>
                                <th>Calificación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($rubrica = $result_rubrica->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($rubrica['criterio']) ?></td>
                                    <td><?= htmlspecialchars($rubrica['descripcion']) ?></td>
                                    <td><?= htmlspecialchars($rubrica['puntos']) ?></td>
                                    <td>
                                        <input type="number" class="form-control calificacion-criterio" 
                                               max="<?= $rubrica['puntos'] ?>" 
                                               name="calificaciones[<?= $rubrica['id_rubrica'] ?>]" 
                                               value="0">
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <div class="mb-3">
                        <label for="calificacion_total" class="form-label">Calificación Total (0-100):</label>
                        <input type="number" id="calificacion_total" name="calificacion_total" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="retroalimentacion" class="form-label">Retroalimentación:</label>
                        <textarea id="retroalimentacion" name="retroalimentacion" class="form-control" rows="4"><?= htmlspecialchars($entrega['retroalimentacion'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="calificarTareas.php" class="btn btn-secondary">Volver</a>
                </form>
            <?php else: ?>
                <p>No se ha definido una rúbrica para esta tarea.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
<script>
    function actualizarCalificacionTotal() {
        const inputs = document.querySelectorAll('.calificacion-criterio');
        let total = 0;

        inputs.forEach(input => {
            const value = parseInt(input.value) || 0;
            total += value;
        });

        document.getElementById('calificacion_total').value = total > 100 ? 100 : total; // Máximo de 100
    }

    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('formCalificarEntrega');
        var calificacionTotalInput = document.getElementById('calificacion_total');

        form.addEventListener('submit', function(event) {
            var calificacionTotal = calificacionTotalInput.value.trim();

            // Verificar si la calificación total está vacía o no es un número válido
            if (calificacionTotal === '' || isNaN(calificacionTotal)) {
                event.preventDefault(); // Evitar el envío del formulario
                alert('Error: La calificación total no puede estar vacía o contener valores inválidos.');
            }

            // Opcional: Verificar si la calificación total está dentro del rango 0-100
            if (calificacionTotal !== '' && !isNaN(calificacionTotal)) {
                var calificacionValue = parseInt(calificacionTotal, 10);
                if (calificacionValue < 0 || calificacionValue > 100) {
                    event.preventDefault(); // Evitar el envío del formulario
                    alert('Error: La calificación total debe estar entre 0 y 100.');
                }
            }
        });
    });
</script>

</body>
</html>

<?php
$conexion->close();
?>
