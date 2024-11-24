<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$num_control = $_SESSION['usuario'];

// Conectar a la base de datos
$conexion = mysqli_connect("localhost", "root", "", "peis");

if (!$conexion) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Preparar la consulta para evitar inyecciones SQL
$query = "SELECT nombre, segundo_nombre, apellido_p, apellido_m, correo FROM alumnos WHERE num_control = ?";
$stmt = mysqli_prepare($conexion, $query);
mysqli_stmt_bind_param($stmt, 's', $num_control);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $nombre, $segundo_nombre, $apellido_p, $apellido_m, $correo);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
mysqli_close($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Alumno</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Animate.css para animaciones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="css/miPerfilEditarAlumno.css?v=<?php echo time(); ?>">
</head>
<body>
    <!-- Botón de "Salir" -->
    <button id="logoutBtn" class="btn btn-danger animate__animated animate__fadeInDown" onclick="window.location.href='inicioAlumno.php'">
        <i class="bi bi-box-arrow-left"></i> Salir
    </button>

    <!-- Contenedor principal -->
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="profile-card text-center animate__animated animate__fadeInUp">
            <div class="text-center">
                <img src="img/perfil120.png" alt="Foto de perfil" class="profile-img mb-3 animate__animated animate__pulse animate__infinite">
            </div>
            <h4 class="welcome-text">Bienvenido</h4>
            <h5 class="fw-bold text-muted"><?php echo htmlspecialchars($nombre . ' ' . $segundo_nombre . ' ' . $apellido_p . ' ' . $apellido_m); ?></h5>
            <form>
                <div class="mb-3 text-start">
                    <label for="txtNombre" class="form-label">Nombre Completo</label>
                    <input type="text" class="form-control" id="txtNombre" value="<?php echo htmlspecialchars($nombre . ' ' . $segundo_nombre . ' ' . $apellido_p . ' ' . $apellido_m); ?>" readonly>
                </div>
                <div class="mb-3 text-start">
                    <label for="txtNumControl" class="form-label">Número de Control</label>
                    <input type="text" class="form-control" id="txtNumControl" value="<?php echo htmlspecialchars($num_control); ?>" readonly>
                </div>
                <div class="mb-3 text-start">
                    <label for="txtCorreo" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="txtCorreo" value="<?php echo htmlspecialchars($correo); ?>" readonly>
                </div>
                <div class="d-grid">
                    <button type="button" class="btn btn-warning" onclick="window.location.href='editarperfilAlumno.php'">
                        <i class="bi bi-pencil-square"></i> Editar mis Datos
                    </button>
                </div>
            </form>
        </div>
    </div>
<<<<<<< HEAD
    <br>
    <h4>Bienvenido, <?php echo $nombre . ' ' . $segundo_nombre . ' ' . $apellido_p . ' ' . $apellido_m; ?></h4>
    <br>
    <div class="mb-3">
        <label for="txtNombre" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="txtNombre" value="<?php echo $nombre . ' ' . $segundo_nombre . ' ' . $apellido_p . ' ' . $apellido_m; ?>" readonly>
    </div>
    <div class="mb-3">
        <label for="txtNumControl" class="form-label">Número de control</label>
        <input type="text" class="form-control" id="txtNumControl" value="<?php echo $num_control; ?>" readonly>
    </div>
    <div class="mb-3">
        <label for="txtCorreo" class="form-label">Correo electrónico</label>
        <input type="email" class="form-control" id="txtCorreo" value="<?php echo $correo; ?>" readonly>
    </div>
    <br>
    <div class="botones">
        <button type="button" class="btn btn-success" onclick='window.location.href = "editarperfilAlumno.php"'>Editar mis datos</button>
    </div>
</form>
=======

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
>>>>>>> f5f1defea1ff424cfed960e68aa29dc87d5a2a19
</body>
</html>
