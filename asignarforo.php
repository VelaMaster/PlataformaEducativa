<?php
session_start();

// Verificación de que el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    echo "<script>alert('Error: Usuario no autenticado.'); window.location.href = 'index.php';</script>";
    exit;
}

$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";

// Conexión a la base de datos
$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Recuperamos el ID del docente de la sesión
$num_control = $_SESSION['usuario'];

// Comprobamos si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitización de datos del formulario
    $materia = htmlspecialchars($_POST['materia']);
    $titulo = htmlspecialchars($_POST['titulo']);
    $descripcion = htmlspecialchars($_POST['descripcion']);
    $tipo_for = htmlspecialchars($_POST['tipo_for']);

    // Validar que los campos no estén vacíos
    if (empty($materia) || empty($titulo) || empty($descripcion) || empty($tipo_for)) {
        echo "<script>alert('Todos los campos son obligatorios.'); window.location.href = 'asignarForo.php';</script>";
        exit;
    }

    // Consulta preparada para evitar inyección SQL
    $stmt = $conexion->prepare("INSERT INTO foros (id_curso, nombre, descripcion, tipo_for) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $materia, $titulo, $descripcion, $tipo_for);  // 'ssss' indica que los parámetros son cadenas

    // Ejecutamos la consulta
    if ($stmt->execute()) {
        // Si la inserción es exitosa
        echo "<script>alert('Foro asignado exitosamente.'); window.location.href = 'gestionForosProfesor.php';</script>";
    } else {
        // Si hay un error en la consulta
        echo "<script>alert('Error al asignar el foro. Intenta nuevamente.'); window.location.href = 'asignarForo.php';</script>";
    }

    // Cerramos la declaración preparada
    $stmt->close();
}

// Consulta para obtener las materias disponibles para el docente
$sql = "
    SELECT f.id, f.nombre, f.descripcion, f.tipo_for
    FROM foros f
    JOIN cursos c ON f.id_curso = c.id
    JOIN grupos g ON c.id = g.id_curso
    WHERE g.id_docente = '$num_control'
";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignar Foro</title>
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/gestionForosProfesor.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/iniciosesionalumno.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/barradeNavegacion.css?v=<?php echo time(); ?>">
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
</main>

<footer>
    <p>© 2024 PE-ISC</p>
</footer>

<script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Cerramos la conexión a la base de datos
$conexion->close();
?>
