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

// Obtener mensajes de notificación si existen
$successMessage = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$errorMessage = isset($_SESSION['error']) ? $_SESSION['error'] : '';

// Limpiar mensajes de notificación
unset($_SESSION['success']);
unset($_SESSION['error']);
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
                    <input type="password" class="form-control" id="txtPassword" name="password" placeholder="Ingrese nueva contraseña">
                </div>
                <div class="mb-3 text-start">
                    <label for="txtConfirmPassword" class="form-label">Confirmar Nueva Contraseña</label>
                    <input type="password" class="form-control" id="txtConfirmPassword" name="confirm_password" placeholder="Confirme nueva contraseña">
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

    <!-- Modales de Notificación -->

    <!-- Modal de Éxito -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successModalLabel"><i class="bi bi-check-circle"></i> Éxito</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body"><?php echo htmlspecialchars($successMessage); ?></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Error -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="errorModalLabel"><i class="bi bi-exclamation-triangle"></i> Error</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body"><?php echo htmlspecialchars($errorMessage); ?></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php if (!empty($successMessage)): ?>
                new bootstrap.Modal(document.getElementById('successModal')).show();
            <?php endif; ?>

            <?php if (!empty($errorMessage)): ?>
                new bootstrap.Modal(document.getElementById('errorModal')).show();
            <?php endif; ?>

            document.getElementById('editProfileForm').addEventListener('submit', function (e) {
                var password = document.getElementById('txtPassword').value;
                var confirmPassword = document.getElementById('txtConfirmPassword').value;
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden.');
                }
            });
        });
    </script>
</body>
</html>
