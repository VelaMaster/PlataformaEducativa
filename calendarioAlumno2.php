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
        'url' => 'verdetallesTarea.php?id=' . $row['id']
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
            background-color: #e7d6bf;
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .calendario-container {
            width: 90%;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            overflow: hidden;
        }
        .calendario-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #e88f13;
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 10px 20px;
        }
        .calendario-header h2 {
            margin: 0;
            font-size: 24px;
        }
        .calendario-header button {
            background-color: white;
            color: #e88f13;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
        }
        .calendario-header button:hover {
            background-color: #f9a94c;
            color: white;
        }
        #calendar {
            padding: 10px;
            background-color: #fdf1e6;
            border-radius: 5px;
        }
        .boton-regresar {
            display: inline-block;
            background-color: #e88f13;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .boton-regresar:hover {
            background-color: #f9a94c;
        }
    </style>
</head>
<body>
    <div class="calendario-container">
        <div class="calendario-header">
            <h2>Calendario de Tareas</h2>
        </div>
        <div id="calendar"></div>
    </div>
    <div style="text-align: center; margin-top: 20px;">
        <a href="inicioAlumno.php" class="boton-regresar">Regresar al Menú</a>
    </div>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                events: <?php echo json_encode($tareas); ?>,
                eventClick: function (info) {
                    window.location.href = info.event.url;
                }
            });
            calendar.render();
        });
    </script>
</body>
</html>