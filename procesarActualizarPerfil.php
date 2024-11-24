<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    // Validar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        header("Location: editarPerfilAlumno.php?error=Las contraseñas no coinciden");
        exit();
    }
    // Validar la seguridad de la contraseña
    $password_regex = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
    if (!preg_match($password_regex, $password)) {
        header("Location: editarPerfilAlumno.php?error=Contraseña no cumple requisitos de seguridad");
        exit();
    }

    // Encriptar la contraseña
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Conexión a la base de datos
    $conexion = mysqli_connect("localhost", "root", "", "peis");
    if (!$conexion) {
        die("Conexión fallida: " . mysqli_connect_error());
    }

    // Obtener el número de control del alumno desde la sesión
    $num_control = $_SESSION['num_control'];

    // Actualizar la contraseña en la base de datos
    $consulta = "UPDATE alumnos SET correo = ?, contrasena = ? WHERE num_control = ?";
    $stmt = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($stmt, "ssi", $correo, $password_hash, $num_control);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: editarPerfilAlumno.php?success=Contraseña actualizada con éxito");
    } else {
        header("Location: editarPerfilAlumno.php?error=Error al actualizar la contraseña");
    }

    // Cerrar conexión
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
} else {
    header("Location: editarPerfilAlumno.php");
    exit();
}
?>
