<?php
session_start();
header('Content-Type: application/json');

// Verificar si el docente está autenticado
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

$num_control_docente = $_SESSION['usuario']; // Número de control del docente

include 'db.php';
if (!$conexion) {
    echo json_encode(['error' => 'Error en la conexión a la base de datos']);
    exit;
}

// Consulta SQL para obtener solo las tareas del docente autenticado
$sql = "
    SELECT 
        t.id AS id_tarea, 
        t.titulo, 
        t.fecha_creacion AS start, 
        t.fecha_limite AS end, 
        t.id_curso, 
        c.nombre_curso 
    FROM tareas t
    INNER JOIN cursos c ON t.id_curso = c.id
    INNER JOIN grupos g ON g.id_curso = c.id
    WHERE g.id_docente = (
        SELECT id FROM docentes WHERE num_control = ?
    )
";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Error en la consulta SQL: ' . $conexion->error]);
    exit;
}

$stmt->bind_param("i", $num_control_docente);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = [
            'id' => $row['id_tarea'],
            'title' => $row['titulo'],
            'start' => $row['start'],
            'end' => $row['end'],
            'id_curso' => $row['id_curso'],
            'curso' => $row['nombre_curso']
        ];
    }
}

echo json_encode($events);
$stmt->close();
mysqli_close($conexion);
?>
