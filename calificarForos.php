<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    echo "<script>alert('Error: Usuario no autenticado.'); window.location.href = 'index.php';</script>";
    exit;
}

// Mostrar errores (para depuración)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de la base de datos
$servidor = "localhost";
$usuario_db = "root";
$contraseña_db = "";
$baseDatos = "peis";

// Establecer conexión con la base de datos
$conexion = new mysqli($servidor, $usuario_db, $contraseña_db, $baseDatos);

// Verificar si hay error en la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Procesar calificación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_respuesta'])) {
    $id_respuesta = intval($_POST['id_respuesta']);
    $calificacion = intval($_POST['calificacion']);
    $revisado = isset($_POST['revisado']) ? 1 : 0;

    if ($calificacion < 0 || $calificacion > 100) {
        echo "<script>alert('Error: La calificación debe ser un número entre 0 y 100.'); window.history.back();</script>";
        exit;
    }

    // Actualizar la calificación
    $sql_update = "UPDATE respuestas SET calificacion = ?, revisado = ? WHERE id = ?";
    $stmt_update = $conexion->prepare($sql_update);
    $stmt_update->bind_param("iii", $calificacion, $revisado, $id_respuesta);
    if ($stmt_update->execute()) {
        echo "<script>alert('Calificación actualizada con éxito.'); window.location.href = 'calificarForos.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar la calificación.'); window.history.back();</script>";
    }
    $stmt_update->close();
}

// Consultar las respuestas con información adicional
$sql = "
    SELECT 
        respuestas.id AS id_respuesta,
        cursos.nombre_curso AS materia,
        foros.nombre AS titulo_foro,
        foros.descripcion AS descripcion_foro,
        CONCAT(alumnos.nombre, ' ', alumnos.apellido_p, ' ', alumnos.apellido_m) AS nombre_estudiante,
        respuestas.fecha_creacion AS fecha_respuesta,
        respuestas.calificacion,
        respuestas.revisado
    FROM respuestas
    INNER JOIN foros ON respuestas.id_tema = foros.id
    INNER JOIN cursos ON foros.id_curso = cursos.id
    INNER JOIN alumnos ON respuestas.id_usuario = alumnos.id
    WHERE respuestas.tipo_usuario = 'alumno'
";
$resultado = $conexion->query($sql);

if (!$resultado) {
    die("Error en la consulta de respuestas: " . $conexion->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificar Respuestas de Foros - Profesor</title>
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/gestionForosProfesor.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/barradeNavegacion.css?v=<?php echo time(); ?>">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        main {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #343a40;
        }
        footer {
            text-align: center;
            padding: 10px;
            background-color: #343a40;
            color: white;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="barranavegacion">
 <div class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Plataforma educativa para Ingeniería en Sistemas</a>
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
                    <a class="nav-link" href="calendarioDocente.php">Calendario</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestionTareasProfesor.php">Asignar tareas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestionForosProfesor.php">Asignar foros</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="calificarTareas.php">Calificar tareas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="calificarForos.php">Calificar foros</a>  
                </li>
            </ul>
        </div>
    </div>
 </div>
</div>

<main class="container mt-4">
    <h2 class="mb-4">Calificar Respuestas de Foros</h2>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Materia</th>
                <th>Título del Foro</th>
                <th>Descripción</th>
                <th>Nombre del Estudiante</th>
                <th>Fecha Respuesta</th>
                <th>Calificar</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado->num_rows > 0): ?>
                <?php while ($row = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['materia']); ?></td>
                        <td><?php echo htmlspecialchars($row['titulo_foro']); ?></td>
                        <td><?php echo htmlspecialchars($row['descripcion_foro']); ?></td>
                        <td><?php echo htmlspecialchars($row['nombre_estudiante']); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha_respuesta']); ?></td>
                        <td>
                            <form action="calificarForos.php" method="POST" class="d-flex align-items-center">
                                <input type="hidden" name="id_respuesta" value="<?php echo $row['id_respuesta']; ?>">
                                <input type="number" name="calificacion" class="form-control me-2" placeholder="Calificación" min="0" max="100" required>
                                <div class="form-check me-2">
                                    <input class="form-check-input" type="checkbox" name="revisado" id="revisado-<?php echo $row['id_respuesta']; ?>">
                                    <label class="form-check-label" for="revisado-<?php echo $row['id_respuesta']; ?>">Revisado</label>
                                </div>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No hay respuestas disponibles.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<footer>
    <p>© 2024 Plataforma de Ingeniería en Sistemas</p>
</footer>

<script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conexion->close();
?>
