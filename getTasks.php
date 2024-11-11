<?php
header('Content-Type: application/json');

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "peis";
$conexion = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conexion->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Retrieve and validate course ID
$id_curso = isset($_GET['id_curso']) ? intval($_GET['id_curso']) : 0;
if ($id_curso <= 0) {
    echo json_encode(["error" => "Invalid course ID"]);
    exit;
}

// Prepare the SQL statement to prevent SQL injection
$query = "SELECT titulo, descripcion, fecha_creacion, fecha_limite FROM tareas WHERE id_curso = ?";
$stmt = $conexion->prepare($query);
if (!$stmt) {
    echo json_encode(["error" => "Failed to prepare statement"]);
    $conexion->close();
    exit;
}

// Bind parameters and execute the statement
$stmt->bind_param("i", $id_curso);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the tasks and store them in an array
$tareas = [];
while ($row = $result->fetch_assoc()) {
    $tareas[] = [
        'titulo' => $row['titulo'],
        'descripcion' => $row['descripcion'],
        'fecha_creacion' => $row['fecha_creacion'] ?: 'Sin fecha',
        'fecha_limite' => $row['fecha_limite'] ?: 'Sin fecha'
    ];
}

// Output the tasks as a JSON array
echo json_encode($tareas);

// Clean up
$stmt->close();
$conexion->close();
?>
