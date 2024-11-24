<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Las contraseñas no coinciden.";
        header("Location: editarPerfilDocente.php");
        exit();
    }

    $password_regex = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
    if (!preg_match($password_regex, $password)) {
        $_SESSION['error'] = "La contraseña no cumple con los requisitos de seguridad.";
        header("Location: editarPerfilDocente.php");
        exit();
    }

    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    $conexion = mysqli_connect("localhost", "root", "", "peis");
    if (!$conexion) {
        $_SESSION['error'] = "Error de conexión con la base de datos.";
        header("Location: editarPerfilDocente.php");
        exit();
    }

    $num_control = $_SESSION['usuario'];

    $consulta = "UPDATE docentes SET correo = ?, contrasena = ? WHERE num_control = ?";
    $stmt = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($stmt, "ssi", $correo, $password_hash, $num_control);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Datos actualizados con éxito.";
    } else {
        $_SESSION['error'] = "Error al actualizar los datos.";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
    header("Location: editarperfilDocente.php");
    exit();
}