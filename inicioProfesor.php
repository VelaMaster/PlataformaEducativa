<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
$passwordPlain = isset($_SESSION['password_plain']) ? $_SESSION['password_plain'] : false;
unset($_SESSION['password_plain']); // Limpiar después de mostrar el modal

$num_control = $_SESSION['usuario'];
$conexion = mysqli_connect("localhost", "root", "", "peis");

if (!$conexion) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Obtener el 'id' del docente
$query_docente_id = "SELECT id FROM docentes WHERE num_control = '$num_control'";
$result_docente_id = mysqli_query($conexion, $query_docente_id);
if ($result_docente_id && mysqli_num_rows($result_docente_id) > 0) {
    $row_docente = mysqli_fetch_assoc($result_docente_id);
    $id_docente = $row_docente['id'];
} else {
    echo "Docente no encontrado.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio Profesor</title>
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/iniciosesionalumno.css">
    <link rel="stylesheet" href="css/estilostarjetas.css">
    <link rel="stylesheet" href="css/barradeNavegacion.css">
    <link rel="stylesheet" href="css/inicioProfesor.css?v=<?php echo time(); ?>">
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
                    <a class="nav-link"href="inicioProfesor.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="calendarioDocente.php">Calendario</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestionTareasProfesor.php">Asignar tareas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestionForosProfesor.php">Asignar foros</a> <!-- Nueva opción -->
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

<?php if ($passwordPlain): ?>
    <div class="modal fade" id="passwordWarningModal" tabindex="-1" aria-labelledby="passwordWarningLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordWarningLabel">¡Atención!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    Tu contraseña no está encriptada. Por tu seguridad, te recomendamos cambiarla lo antes posible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-orange" onclick="window.location.href='editarperfilDocente.php';">Cambiar Contraseña</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="profile-container">
    <a href="#" id="perfilDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="img/perfil120.png" alt="Foto de perfil" class="profile-img">
    </a>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="perfilDropdown">
        <li><a class="dropdown-item" href="verPerfilDocente.php">Ver perfil</a></li>
        <li><a class="dropdown-item" href="soporte.php">Ayuda y soporte</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="logout.php">Salir</a></li>
    </ul>
</div>

<main>
        <div class="card-container">
        <?php
        try {
            $consulta_materias = "
            SELECT 
                c.id AS id_curso,
                c.nombre_curso AS nombre_materia,
                g.nombre_grupo,
                c.imagen_url,
                g.horario,
                g.aula
            FROM 
                cursos c
            JOIN 
                grupos g ON c.id = g.id_curso
            WHERE 
                g.id_docente = '$id_docente'
            ORDER BY 
                c.nombre_curso, g.nombre_grupo
            ";

            $resultado_materias = mysqli_query($conexion, $consulta_materias);
            if ($resultado_materias && mysqli_num_rows($resultado_materias) > 0) {
                while ($row = mysqli_fetch_assoc($resultado_materias)) {
                    $imagen_url = $row['imagen_url'];
                    $id_curso = $row['id_curso'];
                    echo "<div class='card' style='background-image: url($imagen_url)'>";
                    echo "<div class='card-content'>";
                    echo "<h2 class='card-title'>" . $row['nombre_materia'] . "</h2>";
                    echo "<p class='card-subtitle'>Grupo: " . $row['nombre_grupo'] . "</p>";
                    echo "<p class='card-subtitle'>Horario: " . $row['horario'] . ' ' . $row['aula'] . "</p>";
                    echo "<button class='view-more' onclick=\"window.location.href='vermasProfesor.php?id_curso=$id_curso'\">Ver más</button>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p class='text-center'>No tienes materias asignadas actualmente.</p>";
            }
        } catch (mysqli_sql_exception $e) {
            echo "Error en la consulta: " . $e->getMessage();
        }

        mysqli_free_result($resultado_materias);
        mysqli_close($conexion);
        ?>
        </div>
    </main>

<footer>
    <p>© 2024 PE-ISC</p>
</footer>

<script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
<script>
    <?php if ($passwordPlain): ?>
        var passwordWarningModal = new bootstrap.Modal(document.getElementById('passwordWarningModal'));
        passwordWarningModal.show();
    <?php endif; ?>
</script>
</body>
</html>
