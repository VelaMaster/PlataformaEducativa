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

    // Realiza la consulta según el rol
    if ($role == "Estudiante") {
        $consulta = "SELECT * FROM alumnos WHERE num_control = '$usuario' AND contrasena = '$contrasena'";
    } elseif ($role == "Docente") {
        $consulta = "SELECT * FROM docentes WHERE num_control = '$usuario' AND contrasena = '$contrasena'";
    }

    // Depuración: imprime la consulta
    error_log("Consulta ejecutada: " . $consulta); // Puedes revisar el log de errores de PHP para ver esto

    $resultado = mysqli_query($conexion, $consulta);
    if (!$resultado) {
        die("Error en la consulta: " . mysqli_error($conexion));
    }

    $filas = mysqli_num_rows($resultado);

    // Depuración: imprime el número de filas encontradas
    error_log("Número de filas encontradas: " . $filas); // Revisa el log para esto

    if ($filas > 0) {
        $_SESSION['usuario'] = $usuario; // Guardar sesión
        if ($role == "Estudiante") {
            header("location:inicioAlumno.php");
        } elseif ($role == "Docente") {
            header("location:inicioProfesor.php");
        }
    } else {
        header("location:index.php?error=auth"); // Redirigir en caso de fallo
    }

    mysqli_free_result($resultado);
    mysqli_close($conexion);
}
?>
