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
    $error_message = "Las contraseñas no coinciden.";
    echo "<script>
        alert('$error_message');
        window.history.back();
    </script>";
    exit();
}

// Validar que la contraseña sea segura
if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
    $error_message = "La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un carácter especial.";
    echo "<script>
        alert('$error_message');
        window.history.back();
    </script>";
    exit();
}

// Actualizar datos en la base de datos
$query = "UPDATE docentes SET correo = ?, contrasena = ? WHERE num_control = ?";
$stmt = mysqli_prepare($conexion, $query);
$hashed_password = password_hash($password, PASSWORD_BCRYPT); // Hashear la contraseña
mysqli_stmt_bind_param($stmt, 'sss', $correo, $hashed_password, $num_control);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
mysqli_close($conexion);

// Mostrar ventana emergente de éxito
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Éxito</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .success-popup {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        .success-popup h1 {
            font-size: 2rem;
            color: #28a745;
            margin-bottom: 10px;
        }
        .success-popup p {
            margin: 15px 0;
            font-size: 1.1rem;
            color: #555;
        }
        .success-popup button {
            background: #ff6f61;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 1rem;
            transition: background-color 0.3s ease, transform 0.3s ease;
            cursor: pointer;
        }
        .success-popup button:hover {
            background: #ff5a4c;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="success-popup">
        <h1>¡Éxito!</h1>
        <p>Los datos se han actualizado correctamente.</p>
        <button onclick="window.location.href='verPerfilDocente.php';">Aceptar</button>
    </div>
</body>
</html>
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
