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
    $query = "SELECT nombre, segundo_nombre, apellido_p, apellido_m, correo FROM docentes WHERE num_control = ?";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, 's', $num_control);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $nombre, $segundo_nombre, $apellido_p, $apellido_m, $correo);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
} catch (mysqli_sql_exception $e) {
    $errorMessage = "Error al obtener los datos del usuario.";
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
    <!-- Metadatos y enlaces de estilos -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar mi Perfil - Alumno</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Animaciones y iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <!-- Tus estilos personalizados -->
    <link rel="stylesheet" href="css/editarDatosAlumno.css?v=<?php echo time(); ?>">
</head>
<style>
    /* Estilo del botón "Confirmar" */
.btn-primary {
    width: 40%;
    margin: 0 auto;
    background-color: #FFA500; /* Naranja */
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.btn-primary:hover {
    background-color: #FF8C00; /* Naranja más oscuro */
}
.btn-primary:active,
.btn-primary:focus{
    background-color: #FF7F50 !important; /* Coral oscuro para el estado activo o enfocado */
    outline: none !important; /* Elimina el borde azul predeterminado */
    box-shadow: 0 0 5px rgba(255, 127, 80, 0.5) !important; /* Agrega un efecto sutil */
}
/* Estilo del botón "Cancelar" */
.btn-secondary {
    width: 40%;
    margin: 0 auto;
    background-color: #A9A9A9; /* Gris */
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.btn-gray:hover {
    background-color: #808080; /* Gris oscuro para hover */
}

</style>
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
            <form id="editProfileForm" action="procesarActualizarPerfilDocente.php" method="POST">
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
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='verPerfilDocente.php';">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal de Error -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header"> 
            <h5 class="modal-title" id="errorModalLabel">Error</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <?php echo htmlspecialchars($errorMessage); ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Aceptar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal para Errores de Validación -->
    <div class="modal fade" id="validationErrorModal" tabindex="-1" aria-labelledby="validationErrorModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header"> 
            <h5 class="modal-title" id="validationErrorModalLabel">Error de Validación</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body" id="validationErrorMessage">
            <!-- El mensaje será establecido por JavaScript -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Aceptar</button>
          </div>
        </div>
      </div>
    </div>
<!-- Modal de Éxito -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">¡Éxito!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <?= htmlspecialchars($successMessage) ?>
            </div>
            <div class="modal-footer">
                <button type="button" id="successModalButton" class="btn btn-primary">Aceptar</button>
            </div>
        </div>
    </div>
</div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Tu código JavaScript -->
    <script>
document.getElementById('editProfileForm').addEventListener('submit', function (e) {
    const password = document.getElementById('txtPassword').value;
    const confirmPassword = document.getElementById('txtConfirmPassword').value;

    const passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    if (!passwordRegex.test(password)) {
        e.preventDefault();
        document.getElementById('validationErrorMessage').textContent = "La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una letra minúscula, un número y un carácter especial.";
        var validationErrorModal = new bootstrap.Modal(document.getElementById('validationErrorModal'));
        validationErrorModal.show();
        return;
    }

    if (password !== confirmPassword) {
        e.preventDefault();
        document.getElementById('validationErrorMessage').textContent = "Las contraseñas no coinciden.";
        var validationErrorModal = new bootstrap.Modal(document.getElementById('validationErrorModal'));
        validationErrorModal.show();
        return;
    }
});
<?php if (!empty($successMessage)) { ?>
    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
    successModal.show();
    document.getElementById('successModalButton').addEventListener('click', function () {
        window.location.href = 'verPerfilDocente.php';
    });
<?php } ?>

<?php if (!empty($errorMessage)) { ?>
    var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
    errorModal.show();
<?php } ?>

    </script>
</body>
</html>