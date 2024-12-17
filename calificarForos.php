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

// Establecer conexión con la base de datos
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

    // Actualizar la calificación y el estado revisado en la base de datos
    $sql_update = "UPDATE respuestas SET calificacion = ?, revisado = ? WHERE id = ?";
    $stmt_update = $conexion->prepare($sql_update);
    $stmt_update->bind_param("iii", $calificacion, $revisado, $id_respuesta);
    $stmt_update->execute();
    $stmt_update->close();
}

// Consultar las respuestas con información adicional
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
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">Calificar Respuestas de Foros</h2>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Materia</th>
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
                        <td><?php echo "Materia"; ?></td> <!-- Materia está estática, puedes ajustarlo si existe un campo -->
                        <td><?php echo htmlspecialchars($row['titulo_foro']); ?></td>
                        <td><?php echo htmlspecialchars($row['descripcion_foro']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($row['contenido_respuesta'])); ?></td>
                        <td><?php echo htmlspecialchars($row['nombre_estudiante']); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha_respuesta']); ?></td>
                        <td>
                            <form action="calificarForos.php" method="POST" class="d-flex align-items-center">
                                <input type="hidden" name="id_respuesta" value="<?php echo $row['id_respuesta']; ?>">
                                <input type="number" name="calificacion" class="form-control me-2" placeholder="Calificación" min="0" max="100" required>
                                <div class="form-check me-2">
                                    <input class="form-check-input" type="checkbox" name="revisado">
                                    <label class="form-check-label">Revisado</label>
                                </div>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No hay respuestas disponibles.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<footer class="text-center mt-4">
    <p>© 2024 Plataforma de Ingeniería en Sistemas</p>
</footer>

<script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conexion->close();
?>
