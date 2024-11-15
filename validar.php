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

    // Conexión a la base de datos
    $conexion = mysqli_connect("localhost", "root", "", "peis");

    if (!$conexion) {
        die("Conexión fallida: " . mysqli_connect_error());
    }

    // Escapa las entradas para prevenir inyección SQL
    $usuario = mysqli_real_escape_string($conexion, $usuario);
    $contrasena = mysqli_real_escape_string($conexion, $contrasena);

    // Realiza la consulta según el rol
    if ($role == "Estudiante") {
        $consulta = "SELECT * FROM alumnos WHERE num_control = '$usuario' AND contrasena = '$contrasena'";
    } elseif ($role == "Docente") {
        $consulta = "SELECT * FROM docentes WHERE num_control = '$usuario' AND contrasena = '$contrasena'";
    } else {
        // Si el rol no es válido, redirigir con error
        header("Location: index.php?error=invalid_role");
        exit();
    }

    // Depuración: imprime la consulta en el log de errores de PHP
    error_log("Consulta ejecutada: " . $consulta);

    $resultado = mysqli_query($conexion, $consulta);
    if (!$resultado) {
        die("Error en la consulta: " . mysqli_error($conexion));
    }

    $filas = mysqli_num_rows($resultado);

    // Depuración: imprime el número de filas encontradas
    error_log("Número de filas encontradas: " . $filas);

    if ($filas > 0) {
        $_SESSION['usuario'] = $usuario; // Guardar sesión
        if ($role == "Estudiante") {
            header("Location: inicioAlumno.php");
        } elseif ($role == "Docente") {
            header("Location: inicioProfesor.php");
        }
    } else {
        header("Location: index.php?error=auth"); // Redirigir en caso de fallo
    }

    mysqli_free_result($resultado);
    mysqli_close($conexion);
} else {
    // Si se accede al archivo sin enviar datos por POST, redirigir al index
    header("Location: index.php");
    exit();
}
?>
