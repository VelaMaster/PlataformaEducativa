<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$num_control = $_SESSION['usuario'];

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "peis");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener tareas del curso del alumno
$query = "SELECT id, titulo, descripcion, fecha_limite 
          FROM tareas 
          WHERE id_curso IN (
              SELECT id_curso FROM grupo_alumnos WHERE num_control = ?
          )";
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $num_control);
$stmt->execute();
$result = $stmt->get_result();

$tareas = [];
while ($row = $result->fetch_assoc()) {
    $tareas[] = [
        'id' => $row['id'],
        'title' => $row['titulo'],
        'description' => $row['descripcion'],
        'start' => $row['fecha_limite'],
        'url' => 'verdetallesTarea.php?id=' . $row['id'] // URL dinámica
    ];
}

$stmt->close();
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Tareas</title>
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }
        #calendar {
            max-width: 900px;
            margin: 0 auto;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <h1 style="text-align:center;">Calendario de Tareas</h1>
    <div id="calendar"></div>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'es',
    events: <?php echo json_encode($tareas); ?>, // Cargar tareas como eventos
    eventClick: function (info) {
        // Redirigir a la URL de la tarea
        window.location.href = info.event.url;
    }
});


            calendar.render();
        });
    </script>
    <div style="text-align: center; margin-top: 20px;"><a href="inicioAlumno.php" class="boton-regresar">
        Regresar al Menú
    </a>
</div>

</body>
</html>
