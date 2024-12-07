<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    echo "<script>alert('Error: Usuario no autenticado.'); window.location.href = 'index.php';</script>";
    exit;
}

// Configuración de la base de datos
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";

// Establecer conexión con la base de datos
$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);

// Verificar si hay error en la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Procesar calificación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_respuesta'])) {
    $id_respuesta = $_POST['id_respuesta'];
    $calificacion = $_POST['calificacion'];
    $revisado = isset($_POST['revisado']) ? 1 : 0;

    $sql_update = "UPDATE respuestas SET calificacion = ?, revisado = ? WHERE id = ?";
    $stmt_update = $conexion->prepare($sql_update);
    $stmt_update->bind_param("iii", $calificacion, $revisado, $id_respuesta);
    $stmt_update->execute();
    $stmt_update->close();

    echo "<script>alert('Calificación actualizada con éxito.'); window.location.href = 'calificarForos.php';</script>";
    exit;
}

// Consultar las respuestas con los nombres de temas y usuarios
$sql = "
    SELECT 
        respuestas.id AS id_respuesta,
        temas.titulo AS nombre_tema,
        CASE
            WHEN respuestas.tipo_usuario = 'alumno' THEN CONCAT(alumnos.nombre, ' ', alumnos.apellido_p, ' ', alumnos.apellido_m)
            WHEN respuestas.tipo_usuario = 'docente' THEN CONCAT(docentes.nombre, ' ', docentes.apellido_p, ' ', docentes.apellido_m)
            ELSE 'Usuario no identificado'
        END AS nombre_usuario,
        respuestas.contenido,
        respuestas.fecha_creacion
    FROM respuestas
    JOIN temas ON respuestas.id_tema = temas.id
    LEFT JOIN alumnos ON respuestas.id_usuario = alumnos.id AND respuestas.tipo_usuario = 'alumno'
    LEFT JOIN docentes ON respuestas.id_usuario = docentes.id AND respuestas.tipo_usuario = 'docente'
";
$resultado = $conexion->query($sql);

// Cerrar conexión
$conexion->close();
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
        header {
            background-color: #343a40;
            color: white;
            padding: 10px 0;
            text-align: center;
        }
        nav.navbar {
            margin-bottom: 20px;
            background-color: #343a40;
        }
        nav .navbar-nav .nav-link {
            color: white !important;
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
                <th>Tema</th>
                <th>Usuario</th>
                <th>Contenido</th>
                <th>Fecha Creación</th>
                <th>Calificar</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado->num_rows > 0): ?>
                <?php while ($row = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nombre_tema']); ?></td>
                        <td><?php echo htmlspecialchars($row['nombre_usuario']); ?></td>
                        <td><?php echo htmlspecialchars($row['contenido']); ?></td>
                        <td><?php echo $row['fecha_creacion']; ?></td>
                        <td>
                            <form action="calificarForos.php" method="POST" class="d-flex align-items-center">
                                <input type="hidden" name="id_respuesta" value="<?php echo $row['id_respuesta']; ?>">
                                <input type="number" name="calificacion" class="form-control me-2" placeholder="Calificación" required>
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
                    <td colspan="5" class="text-center">No hay respuestas disponibles.</td>
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
