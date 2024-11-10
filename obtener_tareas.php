<?php
// obtener_tareas.php

include 'db.php';
session_start();

if (!isset($_SESSION['usuario'])) {
    echo "No autorizado. Inicie sesión como docente.";
    exit();
}

$num_control = $_SESSION['usuario'];

// Consulta para obtener las tareas asignadas al docente actual
$sql = "SELECT t.titulo, t.fecha_limite, c.nombre_curso 
        FROM tareas AS t
        INNER JOIN cursos AS c ON t.id_curso = c.id_curso
        WHERE c.id_docente = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $num_control);
$stmt->execute();
$resultado = $stmt->get_result();

$tareas = [];
$colores = ["#FF5733", "#33C3FF", "#B833FF", "#FF33C3", "#33FF57", "#FFB833"]; // Lista de colores
$color_index = 0;

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $color = $colores[$color_index % count($colores)]; // Asignar color y rotar en la lista
        $tareas[] = [
            'title' => $fila['titulo'] . ' (' . $fila['nombre_curso'] . ')',
            'start' => $fila['fecha_limite'],
            'color' => $color
        ];
        $color_index++;
    }
}

$conexion->close();

// Devolver los datos como JSON
header('Content-Type: application/json');
echo json_encode($tareas);
