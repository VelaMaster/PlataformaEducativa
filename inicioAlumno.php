<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
$num_control = $_SESSION['usuario'];
$conexion = mysqli_connect("localhost", "root", "", "peis");
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
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/iniciosesionalumno.css">
    <link rel="stylesheet" href="css/estilostarjetas.css">
    <style>
        /* Estilo de la imagen circular para el perfil */
        .profile-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Ajustes de la posición de la foto de perfil */
        .profile-container {
            position: absolute;
            top: 15px; /* Ajusta según la altura que prefieras */
            right: 15px;
        }
    </style>
</head>
<body>
<!-- Barra de navegación -->
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
                    <a class="nav-link active" aria-current="page" href="inicioAlumno.html">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="calendario.html">Calendario</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="clases.html">Clases</a>
                </li>
            </ul>
        </div>
    </div>
 </div>
</div>

<!-- Contenedor de la imagen de perfil fuera de la barra de navegación -->
<div class="profile-container">
    <a href="#" id="perfilDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="img/Logoito120.png" alt="Foto de perfil" class="profile-img">
    </a>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="perfilDropdown">
        <li><a class="dropdown-item" href="verPerfil.html">Ver perfil</a></li>
        <li><a class="dropdown-item" href="editarPerfil.html">Editar perfil</a></li>
    </ul>
</div>
<div class="card-container">
<?php
try {
    $consulta_materias = "
        SELECT c.nombre_curso AS nombre_materia, CONCAT(d.nombre,' ',d.apellido_p,' ', d.apellido_m) AS nombre_profesor, c.descripcion
        FROM cursos c
        JOIN grupos g ON c.id_curso = g.id_curso
        JOIN grupo_alumnos ga ON g.id_grupo = ga.id_grupo
        JOIN docentes d ON c.id_docente = d.num_control
        WHERE ga.num_control = '$num_control'
    ";
    $resultado_materias = mysqli_query($conexion, $consulta_materias);
    if ($resultado_materias && mysqli_num_rows($resultado_materias) > 0) {
        echo "<div class = 'card-container'>";
        while ($row = mysqli_fetch_assoc($resultado_materias)) {
            echo "<div class='card'>";
            echo "<div class='card-content'>";
            echo "<h2 class='card-title'>" . $row['nombre_materia'] . "</h2>";
            echo "<p class='card-subtitle'>Profesor: " . $row['nombre_profesor'] . "</p>";
            echo "<p class='card-description'>" . $row['descripcion'] . "</p>";
            echo "<button class='view-more'>Ver más</button>";
            echo "</div>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "No se encontraron materias para este estudiante.";
    }
} catch (mysqli_sql_exception $e) {
    echo "Error en la consulta: " . $e->getMessage();
}
mysqli_free_result($resultado_materias);
mysqli_close($conexion);
?>
</div>
<!-- Pie de página -->
<footer class="text-center py-3">
    <p>© 2024 PE-ISC</p>
</footer>

<script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
