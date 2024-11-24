<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("HTTP/1.1 403 Forbidden");
    echo json_encode(["error" => "Acceso no autorizado"]);
    exit();
}

$num_control = $_SESSION['usuario']; // Número de control del estudiante

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "peis");
if ($conexion->connect_error) {
    echo json_encode([]); // Retornar un JSON vacío si hay un error
    exit();
}
$conexion->set_charset("utf8");

// Consulta para obtener las tareas asignadas al alumno actual
$sql = "SELECT tareas.id_tarea, tareas.titulo, tareas.fecha_limite, cursos.nombre_curso
        FROM tareas
        JOIN grupo_alumnos ON tareas.id_curso = grupo_alumnos.id_grupo
        JOIN cursos ON tareas.id_curso = cursos.id_curso
        WHERE grupo_alumnos.num_control = ?
        AND tareas.fecha_limite >= CURDATE()";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $num_control);
$stmt->execute();
$resultado = $stmt->get_result();

$events = [];
while ($fila = $resultado->fetch_assoc()) {
    $events[] = [
        'id' => $fila['id_tarea'],            // ID de la tarea
        'title' => $fila['titulo'],           // Título de la tarea
        'start' => $fila['fecha_limite'],     // Fecha de entrega
        'curso' => $fila['nombre_curso'],     // Nombre del curso
    ];
}

// Retornar los eventos como JSON
echo json_encode($events);

$stmt->close();
$conexion->close();
?>
