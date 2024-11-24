<?php
header('Content-Type: application/json');

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "peis";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

// Consulta para obtener las tareas junto con el nombre del curso
$sql = "SELECT tareas.id_tarea, tareas.titulo, tareas.fecha_limite, tareas.id_curso, cursos.nombre_curso 
        FROM tareas
        INNER JOIN cursos ON tareas.id_curso = cursos.id_curso";

$result = $conn->query($sql);

$events = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = [
            'id' => $row['id_tarea'],
            'title' => $row['titulo'],
            'start' => $row['fecha_limite'],
            'id_curso' => $row['id_curso'], // Usar id_curso para asociar colores
            'curso' => $row['nombre_curso']  // Nombre del curso
        ];
    }
}
echo json_encode($events);
$conn->close();
?>
