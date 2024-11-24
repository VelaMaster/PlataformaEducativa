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

// Cambiar consulta para tabla de docentes
$query = "SELECT nombre, segundo_nombre, apellido_p, apellido_m, correo FROM docentes WHERE num_control = ?";
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
    <title>Editar mi perfil</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Animate.css para animaciones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="css/editarDatosprofesor.css?v=<?php echo time(); ?>">
</head>
<body>
    <!-- Botón de "Volver" -->
    <button id="logoutBtn" class="btn btn-danger animate__animated animate__fadeInDown" onclick="window.history.back();">
        <i class="bi bi-arrow-left"></i> Volver
    </button>

    <!-- Contenedor principal -->
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="profile-card text-center animate__animated animate__fadeInUp">
            <div class="text-center">
                <img src="img/perfil120.png" alt="Foto de perfil" class="profile-img mb-3">
            </div>
            <h4 class="welcome-text">Editar Perfil</h4>
            <h5 class="fw-bold text-muted"><?php echo htmlspecialchars($nombre . ' ' . $segundo_nombre . ' ' . $apellido_p . ' ' . $apellido_m); ?></h5>
            <form action="procesarActualizarPerfil.php" method="POST">
                <div class="mb-3 text-start">
                    <label for="txtNombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="txtNombre" name="nombre" value="<?php echo htmlspecialchars($nombre . ' ' . $segundo_nombre . ' ' . $apellido_p . ' ' . $apellido_m); ?>" readonly>
                </div>
                <div class="mb-3 text-start">
                    <label for="txtNumControl" class="form-label">Número de control</label>
                    <input type="text" class="form-control" id="txtNumControl" name="num_control" value="<?php echo htmlspecialchars($num_control); ?>" readonly>
                </div>
                <div class="mb-3 text-start">
                    <label for="txtCorreo" class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" id="txtCorreo" name="correo" value="<?php echo htmlspecialchars($correo); ?>">
                </div>
                <div class="mb-3 text-start">
                    <label for="txtPassword" class="form-label">Nueva contraseña</label>
                    <input type="password" class="form-control" id="txtPassword" name="password">
                </div>
                <div class="mb-3 text-start">
                    <label for="txtConfirmPassword" class="form-label">Confirmar nueva contraseña</label>
                    <input type="password" class="form-control" id="txtConfirmPassword" name="confirm_password">
                </div>
                <div class="d-grid gap-2">
    <button type="submit" class="btn-confirmar">
        <i class="bi bi-check-circle"></i> Confirmar
    </button>
    <button type="button" class="btn-cancelar" onclick="window.location.href='verPerfilDocente.php';">
        <i class="bi bi-x-circle"></i> Cancelar
    </button>
</div>

            </form>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
