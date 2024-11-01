<?php
$conexion = mysqli_connect("localhost", "root", "", "peis");
if (!$conexion) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Obtener el número de control del estudiante de la sesión
$num_control = $_SESSION['usuario'];

// Consulta para obtener las materias del estudiante
$consulta_materias = "
    SELECT c.nombre AS nombre_materia, d.nombre AS nombre_profesor 
    FROM cursos c 
    JOIN grupos g ON c.id_grupo = g.id_grupo 
    JOIN alumnos a ON g.id_grupo = a.id_grupo 
    JOIN docentes d ON c.id_docente = d.id_docente 
    WHERE a.num_control = '$num_control'
";

$resultado_materias = mysqli_query($conexion, $consulta_materias);

if ($resultado_materias) {
    while ($row = mysqli_fetch_assoc($resultado_materias)) {
        echo "Materia: " . $row['nombre_materia'] . " - Profesor: " . $row['nombre_profesor'] . "<br>";
    }
} else {
    echo "Error en la consulta: " . mysqli_error($conexion);
}

mysqli_close($conexion);
