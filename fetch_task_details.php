<?php
header('Content-Type: application/json');
include 'db.php';
if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'No se proporcionó el ID de la tarea.']);
    exit;
}

$id_tarea = intval($_GET['id']);
$stmt = mysqli_prepare($conexion, "SELECT titulo, descripcion, fecha_creacion, fecha_limite FROM tareas WHERE id = ?");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id_tarea);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $task = mysqli_fetch_assoc($result);
        echo json_encode($task);
    } else {
        echo json_encode(['error' => 'Tarea no encontrada.']);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['error' => 'Error en la preparación de la consulta.']);
}
mysqli_close($conexion);
?>
