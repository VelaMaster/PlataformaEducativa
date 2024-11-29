<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];
    $role = $_POST['role'];

    // Verifica si se han proporcionado todos los datos necesarios
    if (empty($usuario) || empty($contrasena) || empty($role)) {
        header("Location: index.php?error=empty_fields");
        exit();
    }
    $conexion = mysqli_connect("127.0.0.1:3306", "root", "", "peis");

    if (!$conexion) {
        die("Conexión fallida: " . mysqli_connect_error());
    }

    // Escapa las entradas para prevenir inyección SQL
    $usuario = mysqli_real_escape_string($conexion, $usuario);

    // Consulta según el rol
    if ($role == "Estudiante") {
        $consulta = "SELECT * FROM alumnos WHERE num_control = ?";
    } elseif ($role == "Docente") {
        $consulta = "SELECT * FROM docentes WHERE num_control = ?";
    } else {
        // Si el rol no es válido, redirigir con error
        header("Location: index.php?error=invalid_role");
        exit();
    }

    // Preparar la consulta
    $stmt = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($stmt, 's', $usuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        // Obtén la fila
        $row = mysqli_fetch_assoc($resultado);

        // Verifica si la contraseña está encriptada
        if (password_verify($contrasena, $row['contrasena'])) {
            // Contraseña encriptada válida
            $_SESSION['nombre'] = $row['nombre']; // Nombre del usuario
            $_SESSION['num_control'] = $row['num_control']; // Número de control
            $_SESSION['usuario'] = $usuario; // Usuario en sesión

            if ($role == "Estudiante") {
                header("Location: inicioAlumno.php");
            } elseif ($role == "Docente") {
                header("Location: inicioDocente.php");
            }
            exit();
        } elseif ($contrasena === $row['contrasena']) {
            $_SESSION['nombre'] = $row['nombre'];
            $_SESSION['num_control'] = $row['num_control'];
            $_SESSION['usuario'] = $usuario;
            $_SESSION['password_plain'] = true;
            if ($role == "Estudiante") {
                header("Location: inicioAlumno.php");
            } elseif ($role == "Docente") {
                header("Location: inicioProfesor.php");
            }
            exit();
        } else {
            // Contraseña incorrecta
            header("Location: index.php?error=auth");
            exit();
        }
    } else {
        // Usuario no encontrado
        header("Location: index.php?error=auth");
        exit();
    }

    // Liberar recursos y cerrar conexión
    mysqli_free_result($resultado);
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
} else {
    // Si se accede al archivo sin enviar datos por POST, redirigir al index
    header("Location: index.php");
    exit();
}
?>
