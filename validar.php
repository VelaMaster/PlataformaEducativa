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
    if ($role == "Alumno") {
        $consulta = "SELECT * FROM alumnos WHERE num_control = '$usuario' AND contrasena = '$contrasena'";
    } elseif ($role == "Docente") {
        $consulta = "SELECT * FROM docentes WHERE num_control = '$usuario' AND contrasena = '$contrasena'";
    }

    $resultado = mysqli_query($conexion, $consulta);
    if (!$resultado) {
        die("Error en la consulta: " . mysqli_error($conexion));
    }

    $filas = mysqli_num_rows($resultado);

    if ($filas > 0) {
        $fila = mysqli_fetch_assoc($resultado);
        $_SESSION['num_control'] = $fila['num_control']; // Guardar el número de control en la sesión
        $_SESSION['role'] = $role; // Guardar el rol en la sesión

        if ($role == "Alumno") {
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
