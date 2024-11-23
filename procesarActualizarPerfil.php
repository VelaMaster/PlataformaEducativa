<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Verificar si se accedió mediante POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}
$conexion = mysqli_connect("localhost", "root", "", "peis");

if (!$conexion) {
    $_SESSION['error'] = "Error de conexión a la base de datos.";
    header("Location: editarPerfilAlumno.php");
    exit();
}
$num_control = $_SESSION['usuario'];
$correo = $_POST['correo'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Validar que las contraseñas coincidan
if ($password !== $confirm_password) {
    $_SESSION['error'] = "Las contraseñas no coinciden.";
    header("Location: editarPerfilAlumno.php");
    exit();
}
if (!empty($password) && !preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
    $_SESSION['error'] = "La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un carácter especial.";
    header("Location: editarPerfilAlumno.php");
    exit();
}

// Preparar la consulta para evitar inyecciones SQL
if (!empty($password)) {
    $query = "UPDATE docentes SET correo = ?, contrasena = ? WHERE num_control = ?";
    $stmt = mysqli_prepare($conexion, $query);
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    mysqli_stmt_bind_param($stmt, 'sss', $correo, $hashed_password, $num_control);
} else {
    $query = "UPDATE docentes SET correo = ? WHERE num_control = ?";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $correo, $num_control);
}

// Ejecutar la consulta y manejar errores
if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = "Tu perfil ha sido actualizado correctamente.";
} else {
    $_SESSION['error'] = "Hubo un error al actualizar tu perfil. Por favor, intenta nuevamente.";
}

mysqli_stmt_close($stmt);
mysqli_close($conexion);

// Redirigir de vuelta a la página de edición
header("Location: editarPerfilAlumno.php");
exit();
?>
