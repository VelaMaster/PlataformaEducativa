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

// Configurar el conjunto de caracteres a UTF-8
mysqli_set_charset($conexion, "utf8");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio Estudiante</title>
    <link rel="stylesheet" href="css/iniciosesionalumno.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilostarjetas.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/barradeNavegacion.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/inicioProfesor.css?v=<?php echo time(); ?>">
</head>
<style>
    body{
        background-color: #e7d6bf;
    }
</style>
<body>
    <!-- Barra de navegación -->
    <div class="barranavegacion">
        <div class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Plataforma educativa para Ingeniería en Sistemas</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link active" href="inicioAlumno.php">Inicio</a></li>
                        <li class="nav-item"><a class="nav-link" href="calendarioAlumno2.php">Calendario</a></li>
                        <li class="nav-item"><a class="nav-link" href="gestionTareasAlumno.php">Tareas</a></li>
                        <li class="nav-item"><a class="nav-link" href="forosAlumno.php">Foros</a></li>
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
                    <button type="button" class="btn btn-orange" onclick="window.location.href='editarperfilAlumno.php';">Cambiar Contraseña</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

    <!-- Imagen de perfil -->
    <div class="profile-container">
        <a href="#" id="perfilDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="img/perfil120.png" alt="Foto de perfil" class="profile-img">
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="perfilDropdown">
            <li><a class="dropdown-item" href="verPerfilAlumno.php">Ver perfil</a></li>
            <li><a class="dropdown-item" href="soporteAlumno.php">Ayuda y soporte</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="logout.php">Salir</a></li>
        </ul>
    </div>

    <!-- Contenedor de tarjetas -->
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
                JOIN 
                    grupo_alumnos ga ON g.id = ga.id_grupo
                WHERE 
                    ga.num_control = ?
                ORDER BY 
                    c.nombre_curso, g.nombre_grupo;
            ";
            
            // Preparar la consulta
            $stmt = mysqli_prepare($conexion, $consulta_materias);
            if ($stmt === false) {
                throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
            }

            // Vincular parámetros (usar 'i' para entero)
            mysqli_stmt_bind_param($stmt, 'i', $num_control);

            // Ejecutar la consulta
            mysqli_stmt_execute($stmt);

            // Obtener el resultado
            $resultado_materias = mysqli_stmt_get_result($stmt);

            if ($resultado_materias && mysqli_num_rows($resultado_materias) > 0) {
                while ($row = mysqli_fetch_assoc($resultado_materias)) {
                    $imagen_url = $row['imagen_url'];
                    $id_curso = $row['id_curso'];
                    echo "<div class='card' style='background-image: url($imagen_url)'>";
                    echo "<div class='card-content'>";
                    echo "<h2 class='card-title'>" . htmlspecialchars($row['nombre_materia'], ENT_QUOTES, 'UTF-8') . "</h2>";
                    echo "<p class='card-subtitle'>Grupo: " . htmlspecialchars($row['nombre_grupo'], ENT_QUOTES, 'UTF-8') . "</p>";
                    echo "<p class='card-subtitle'>Horario: " . htmlspecialchars($row['horario'], ENT_QUOTES, 'UTF-8') . " en " . htmlspecialchars($row['aula'], ENT_QUOTES, 'UTF-8') . "</p>";
                    echo "<button class='view-more' onclick=\"window.location.href='gestionTareasAlumno2.php?id_curso=$id_curso'\">Ver más</button>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>No se encontraron materias para este estudiante.</p>";
            }

            // Cerrar la sentencia
            mysqli_stmt_close($stmt);
        } catch (mysqli_sql_exception $e) {
            echo "<p>Error en la consulta: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
        } catch (Exception $e) {
            echo "<p>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
        }

        // Cerrar la conexión
        mysqli_close($conexion);
        ?>
    </div>

    <footer>
        <p>&copy; 2024 PE-ISC</p>
    </footer>

    <script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
