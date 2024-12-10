<?php
header('Content-Type: application/json');
include 'db.php';
if (!$conexion) {
    echo json_encode([]);
    exit;
}
$sql = "
    SELECT 
        tareas.id AS id_tarea, 
        tareas.titulo, 
        tareas.fecha_limite, 
        tareas.id_curso, 
        cursos.nombre_curso 
    FROM tareas
    INNER JOIN cursos ON tareas.id_curso = cursos.id
";
$result = mysqli_query($conexion, $sql);
$events = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = [
            'id' => $row['id_tarea'],
            'title' => $row['titulo'],
            'start' => $row['fecha_limite'],
            'id_curso' => $row['id_curso'],
            'curso' => $row['nombre_curso']
        ];
    }
}
echo json_encode($events);
mysqli_close($conexion);
?>
