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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio Estudiante</title>
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/estilostarjetas.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/iniciosesionalumno.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/barradeNavegacion.css?v=<?php echo time(); ?>">
    <style>
        /* Estilo de la imagen circular para el perfil */
        .profile-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }        /* Ajustes de la posición de la foto de perfil */
        .profile-container {
            position: absolute;
            top: 15px; /* Ajusta según la altura que prefieras */
            right: 15px;
        }
        .card {
    width: 320px;
    height: 270px;
    border-radius: 10px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column; /* Organiza el contenido en columna */
    justify-content: flex-end; /* Empuja el contenido hacia la parte inferior */
    overflow: hidden;
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}
.card:hover {
    transform: scale(1.05); /* Agranda la tarjeta un 5% */
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Incrementa la sombra */
}
.card img {
    width: 100%;
    height: auto; /* Asegura que la imagen mantenga su proporción */
    flex: 1; /* La imagen toma el espacio disponible en el medio */
}
.card-content {
    background-color: rgb(102, 102, 102);
    color: white;
    padding: 16px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
}
.btn-ver-mas {
    display: inline-block;
    padding: 4px 10px;
    background-color: #FFA500;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    text-align: center !important;
    font-size: 16px;
    transition: background-color 0.3s;
}
.btn-ver-mas:hover {
    background-color: #FF8C00;
}
.btn-orange {
    background-color: #FFA500; /* Color naranja */
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.btn-orange:hover {
    background-color: #FF8C00; /* Naranja más oscuro para hover */
}
/* Estilo del enlace al pasar el ratón */
.dropdown-item:hover {
    background-color: #F1AA3D;
    color: white;
    border-radius: 10px;
    text-decoration: none;
    text-align: center;
    transform: scale(1.1); /* Aumenta el tamaño */
    transition: transform 0.2s ease-in-out, background-color 0.2s ease-in-out;
}
/* Estilo del enlace al estar activo o enfocado */
.dropdown-item:active,
.dropdown-item:focus {
    background-color: #FFA500; /* Naranja */
    color: white;
    text-decoration: none;
    transform: scale(1); /* Mantiene el tamaño normal */
}

/* Elimina el borde azul predeterminado */
.dropdown-item:focus {
    outline: none;
}
.dropdown-menu{
    text-align: center;
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
                    <a class="nav-link active" aria-current="page" href="inicioAlumno.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="calendarioAlumno.php">Calendario</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestionTareasAlumno.php">Tareas</a>
                </li>
            </ul>
        </div>
    </div>
 </div>
</div>
<!-- Modal de advertencia de seguridad -->
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
<!-- Contenedor de la imagen de perfil fuera de la barra de navegación -->
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

<div class="card-container">
<?php
try {
    $consulta_materias = "
    SELECT c.nombre_curso AS nombre_materia, 
           CONCAT(d.nombre, ' ', d.apellido_p, ' ', d.apellido_m) AS nombre_profesor, 
           c.imagen_url,
           g.horario,
           g.aula,
           g.nombre_grupo AS grupo,
           g.id_curso
    FROM cursos c
    JOIN grupos g ON c.id_curso = g.id_curso
    JOIN grupo_alumnos ga ON g.id_grupo = ga.id_grupo
    JOIN docentes d ON g.id_docente = d.num_control
    WHERE ga.num_control = ?
";
$stmt = mysqli_prepare($conexion, $consulta_materias);
mysqli_stmt_bind_param($stmt, 's', $num_control);
mysqli_stmt_execute($stmt);
$resultado_materias = mysqli_stmt_get_result($stmt);


    if ($resultado_materias && mysqli_num_rows($resultado_materias) > 0) {
        echo "<div class='card-container'>";
        while ($row = mysqli_fetch_assoc($resultado_materias)) {
            $imagen_url = $row['imagen_url']; // Recupera la URL de la imagen
            echo "<div class='card' style='background-image: url($imagen_url)'>"; // Aplica la imagen como fondo
            echo "<div class='card-content'>";
            echo "<h2 class='card-title'>" . $row['nombre_materia'] . "</h2>";
            echo "<p class='card-subtitle'>Profesor: " . $row['nombre_profesor'] . "</p>";
            echo "<p class='card-subtitle'>" . $row['grupo'] . "</p>"; // Muestra el grupo
            echo "<p class='card-subtitle'>Horario: " . $row['horario'] .' '. $row['aula'] . "</p>"; // Muestra el horario
            echo "<a href='gestionTareasAlumno2.php?id_curso=" . $row['id_curso'] . "' class='btn-ver-mas'>Ver más</a>";
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
<footer class="text-center py-3">
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