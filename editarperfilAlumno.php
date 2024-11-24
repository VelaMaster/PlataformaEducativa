<?php
session_start();
// Configuración para registrar errores en un archivo de log
ini_set('log_errors', 1);
ini_set('error_log', '/ruta/a/tu/archivo_de_error.log'); // Reemplaza con una ruta válida
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
$successMessage = '';
$errorMessage = '';

try {
    $num_control = $_SESSION['usuario'];
    $conexion = mysqli_connect("localhost", "root", "", "peis");

    // Preparar la consulta para obtener datos del usuario
    $query = "SELECT nombre, segundo_nombre, apellido_p, apellido_m, correo FROM alumnos WHERE num_control = ?";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, 's', $num_control);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $nombre, $segundo_nombre, $apellido_p, $apellido_m, $correo);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
} catch (mysqli_sql_exception $e) {
    $errorMessage = "Error al obtener los datos del usuario.";
    // Registrar el error en el log
    error_log($e->getMessage());
}

// Obtener mensajes de notificación
if (isset($_SESSION['success'])) {
    $successMessage = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    $errorMessage = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar mi Perfil - Alumno</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/editarDatosAlumno.css?v=<?php echo time(); ?>">
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
            <form id="editProfileForm" action="procesarActualizarPerfil.php" method="POST">
                <div class="mb-3 text-start">
                    <label for="txtCorreo" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="txtCorreo" name="correo" value="<?php echo htmlspecialchars($correo); ?>" required>
                </div>
                <div class="mb-3 text-start">
                    <label for="txtPassword" class="form-label">Nueva Contraseña</label>
                    <input type="password" class="form-control" id="txtPassword" name="password" placeholder="Ingrese nueva contraseña" required>
                </div>
                <div class="mb-3 text-start">
                    <label for="txtConfirmPassword" class="form-label">Confirmar Nueva Contraseña</label>
                    <input type="password" class="form-control" id="txtConfirmPassword" name="confirm_password" placeholder="Confirme nueva contraseña" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Confirmar
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='verPerfilAlumno.php';">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('editProfileForm').addEventListener('submit', function (e) {
            const password = document.getElementById('txtPassword').value;
            const confirmPassword = document.getElementById('txtConfirmPassword').value;

            // Expresión regular para validar la contraseña
            const passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

            // Validar si cumple con los requisitos
            if (!passwordRegex.test(password)) {
                e.preventDefault(); // Detener el envío del formulario
                alert("La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una letra minúscula, un número y un carácter especial.");
                return;
            }

            // Validar si las contraseñas coinciden
            if (password !== confirmPassword) {
                e.preventDefault(); // Detener el envío del formulario
                alert("Las contraseñas no coinciden.");
                return;
            }
        });
    </script>
</body>
</html>
