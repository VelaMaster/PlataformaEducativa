<?php
header('Content-Type: application/json');
if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'No se proporcionó el ID de la tarea.']);
    exit;
}

$id_tarea = intval($_GET['id']);

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "peis";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Error de conexión a la base de datos.']);
    exit;
}

$stmt = $conn->prepare("SELECT titulo, descripcion, fecha_creacion, fecha_limite FROM tareas WHERE id_tarea = ?");
$stmt->bind_param("i", $id_tarea);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Tarea no encontrada.']);
    exit;
}

$task = $result->fetch_assoc();
echo json_encode($task);

$stmt->close();
$conn->close();
?>
