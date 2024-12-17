<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    echo "<script>alert('Error: Usuario no autenticado.'); window.location.href = 'index.php';</script>";
    exit;
}

// Configuración de la base de datos
$servidor = "localhost";
$usuario_db = "root";
$contraseña_db = "";
$baseDatos = "peis";

// Conexión con la base de datos
$conexion = new mysqli($servidor, $usuario_db, $contraseña_db, $baseDatos);
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

    $sql_update = "UPDATE respuestas SET calificacion = ?, revisado = ? WHERE id = ?";
    $stmt_update = $conexion->prepare($sql_update);
    $stmt_update->bind_param("iii", $calificacion, $revisado, $id_respuesta);
    $stmt_update->execute();
    $stmt_update->close();
    echo "<script>alert('Calificación actualizada con éxito.'); window.location.href = 'calificarForos.php';</script>";
}

// Consultar las respuestas
$sql = "
    SELECT 
        respuestas.id AS id_respuesta,
        foros.nombre AS titulo_foro,
        foros.descripcion AS descripcion_foro,
        respuestas.contenido AS contenido_respuesta,
        CONCAT(alumnos.nombre, ' ', alumnos.segundo_nombre, ' ', alumnos.apellido_p, ' ', alumnos.apellido_m) AS nombre_estudiante,
        respuestas.fecha_creacion AS fecha_respuesta,
        respuestas.calificacion,
        respuestas.revisado
    FROM respuestas
    INNER JOIN foros ON respuestas.id_tema = foros.id
    INNER JOIN alumnos ON respuestas.id_usuario = alumnos.num_control
    WHERE respuestas.tipo_usuario = 'alumno'
    ORDER BY respuestas.fecha_creacion DESC
";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificar Respuestas de Foros</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #EDE0D4;
            font-family: 'Arial', sans-serif;
        }
        .navbar-custom {
            background-color: #E6861F;
            border-radius: 10px;
            padding: 10px;
        }
        .navbar-custom a {
            color: white !important;
            font-weight: bold;
        }
        main {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 30px;
            margin-top: 20px;
        }
        h2 {
            color: #343a40;
            font-weight: bold;
            margin-bottom: 20px;
        }
        table {
            font-size: 14px;
            text-align: center;
            vertical-align: middle;
        }
        th {
            background-color: #343a40;
            color: white;
            text-align: center;
        }
        td {
            padding: 5px;
            vertical-align: middle;
        }
        tr {
            height: 40px; /* Altura más pequeña */
        }
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
            font-weight: bold;
        }
        footer {
            background-color: #E6861F;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<!-- Barra de Navegación -->
<div class="container mt-3">
    <div class="navbar-custom d-flex justify-content-between align-items-center">
        <span class="navbar-brand mb-0 h5">Plataforma educativa para Ingeniería en Sistemas</span>
        <div>
            <a href="inicioProfesor.php" class="me-3">Inicio</a>
            <a href="calendarioDocente.php" class="me-3">Calendario</a>
            <a href="gestionTareasProfesor.php" class="me-3">Asignar tareas</a>
            <a href="gestionForosProfesor.php" class="me-3">Asignar foros</a>
            <a href="calificarTareas.php" class="me-3">Calificar tareas</a>
            <a href="calificarForos.php">Calificar foros</a>
        </div>
    </div>
</div>

<!-- Contenido Principal -->
<main class="container">
    <h2>Calificar Respuestas de Foros</h2>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Título del Foro</th>
                <th>Descripción</th>
                <th>Contenido</th>
                <th>Nombre del Estudiante</th>
                <th>Fecha Respuesta</th>
                <th>Calificar</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado->num_rows > 0): ?>
                <?php while ($row = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['titulo_foro']); ?></td>
                        <td><?php echo htmlspecialchars($row['descripcion_foro']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($row['contenido_respuesta'])); ?></td>
                        <td><?php echo htmlspecialchars($row['nombre_estudiante']); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha_respuesta']); ?></td>
                        <td>
                            <form action="calificarForos.php" method="POST">
                                <input type="hidden" name="id_respuesta" value="<?php echo $row['id_respuesta']; ?>">
                                <input type="number" name="calificacion" class="form-control mb-2" placeholder="Calificación" min="0" max="100" required>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="revisado">
                                    <label class="form-check-label">Revisado</label>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Guardar</button>
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

<!-- Footer -->
<footer>
    <p>© 2024 Plataforma educativa para Ingeniería en Sistemas</p>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conexion->close();
?>
