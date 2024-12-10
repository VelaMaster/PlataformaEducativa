<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$passwordPlain = isset($_SESSION['password_plain']) ? $_SESSION['password_plain'] : false;
unset($_SESSION['password_plain']);

$num_control = $_SESSION['usuario'];
$conexion = mysqli_connect("127.0.0.1:3306", "root", "", "peis");

if (!$conexion) {
    die("Conexión fallida: " . mysqli_connect_error());
}
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
                        <li class="nav-item"><a class="nav-link" href="calendarioAlumno.php">Calendario</a></li>
                        <li class="nav-item"><a class="nav-link" href="gestionTareasAlumno.php">Tareas</a></li>
                        <li class="nav-item"><a class="nav-link" href="forosAlumno.php">Foros</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

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
            $stmt = mysqli_prepare($conexion, $consulta_materias);
            mysqli_stmt_bind_param($stmt, 's', $num_control);
            mysqli_stmt_execute($stmt);
            $resultado_materias = mysqli_stmt_get_result($stmt);

            if ($resultado_materias && mysqli_num_rows($resultado_materias) > 0) {
                while ($row = mysqli_fetch_assoc($resultado_materias)) {
                    $imagen_url = $row['imagen_url'];
                    echo "<div class='card'>";
                    echo "<img src='$imagen_url' alt='Imagen de la materia'>";
                    echo "<div class='card-content'>";
                    echo "<h2 class='card-title'>" . $row['nombre_materia'] . "</h2>";
                    echo "<p class='card-subtitle'>Grupo: " . $row['nombre_grupo'] . "</p>";
                    echo "<p class='card-subtitle'>Horario: " . $row['horario'] . " en " . $row['aula'] . "</p>";
                    echo "<a href='gestionTareasAlumno2.php?id_curso=" . $row['id_curso'] . "' class='btn-ver-mas'>Ver más</a>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>No se encontraron materias para este estudiante.</p>";
            }
        } catch (mysqli_sql_exception $e) {
            echo "<p>Error en la consulta: " . $e->getMessage() . "</p>";
        }

        mysqli_close($conexion);
        ?>
    </div>

    <footer>
        <p>&copy; 2024 PE-ISC</p>
    </footer>

    <script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
