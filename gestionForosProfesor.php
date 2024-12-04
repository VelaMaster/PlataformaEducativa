<?php
session_start();

// Verificar si el usuario está autenticado
if (isset($_SESSION['usuario'])) {
    $num_control = $_SESSION['usuario'];  // Número de control del docente
} else {
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

// Consulta para obtener los foros accesibles por el docente
$sql = "
    SELECT f.id, f.nombre, f.descripcion, f.tipo_for
    FROM foros f
    JOIN foro_acceso_docentes fad ON f.id = fad.id_foros
    WHERE fad.num_control_docente = ?
";

// Preparar la consulta para evitar inyección SQL
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("Error al preparar la consulta: " . $conexion->error);
}

$stmt->bind_param("i", $num_control);  // Asumiendo que $num_control es un número entero
$stmt->execute();
$resultado = $stmt->get_result();

// Cerrar la conexión con la base de datos
$stmt->close();

// Cerrar la conexión con la base de datos
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Foros - Profesor</title>
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/gestionForosProfesor.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/iniciosesionalumno.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/barradeNavegacion.css?v=<?php echo time(); ?>">
    <style>
        /* Tu estilo CSS aquí */
    </style>
</head>
<body>
<header>
    <div class="container">
        <h1>Plataforma educativa para Ingeniería en Sistemas</h1>
    </div>
</header>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Inicio</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="calendarioDocente.php">Calendario</a></li>
                <li class="nav-item"><a class="nav-link" href="gestionTareasProfesor.php">Asignar tareas</a></li>
                <li class="nav-item"><a class="nav-link" href="calificarTareas.php">Calificar tareas</a></li>
            </ul>
        </div>
    </div>
</nav>

<main class="container mt-4">
    <section id="asignar-foro">
        <h2>Asignar Nuevo Foro</h2>
        <div class="form-container">
            <form action="asignarForo.php" method="POST">
                <label for="materia">Materia:</label>
                <select id="materia" name="materia" required>
                    <?php
                    if ($resultado->num_rows > 0) {
                        while ($row = $resultado->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['nombre']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>No tiene foros asignados</option>";
                    }
                    ?>
                </select>

                <label for="titulo">Título del Foro:</label>
                <input type="text" id="titulo" name="titulo" required placeholder="Ingrese el título del foro">

                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" required placeholder="Describa los detalles del foro"></textarea>

                <label for="tipo_for">Tipo de Foro:</label>
                <select id="tipo_for" name="tipo_for" required>
                    <option value="general">Foro General</option>
                    <option value="tematico">Foro Temático</option>
                </select>

                <input type="submit" value="Asignar Foro">
            </form>
        </div>
    </section>

    <section id="foros-asignados">
        <h2>Foros Asignados</h2>
        <div class="form-container">
            <form method="POST" action="listarforos.php">
                <input type="submit" value="Mostrar Foros Asignados" class="btn btn-primary">
            </form>
        </div>
    </section>
</main>

<footer>
    <p>© 2024 PE-ISC</p>
</footer>

<script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>