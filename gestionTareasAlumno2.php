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

// Validar y sanitizar id_curso
$id_curso = isset($_GET['id_curso']) ? intval($_GET['id_curso']) : 0;
if ($id_curso <= 0) {
    echo "ID de curso inválido.";
    exit;
}

// Consulta preparada para obtener tareas
$stmt = $conexion->prepare("SELECT tareas.id_tarea, tareas.id_curso, tareas.titulo, tareas.fecha_limite, 
                                   CASE 
                                       WHEN tareas.fecha_limite < CURDATE() THEN 'Vencida' 
                                       ELSE 'En plazo' 
                                   END AS estado_fecha,
                                   CASE 
                                       WHEN entregas.archivo_entrega IS NOT NULL THEN 'Entregado' 
                                       ELSE 'No entregado' 
                                   END AS estado_entrega
                            FROM tareas
                            JOIN grupo_alumnos ON tareas.id_curso = grupo_alumnos.id_grupo
                            LEFT JOIN entregas ON tareas.id_tarea = entregas.id_tarea AND entregas.id_alumno = grupo_alumnos.num_control
                            WHERE grupo_alumnos.num_control = ? AND tareas.id_curso = ?");
$stmt->bind_param("si", $num_control, $id_curso);
$stmt->execute();
$resultado = $stmt->get_result();

// Función para obtener el nombre de la materia
function obtenerNombreMateria($id_curso, $conexion) {
    $stmt = $conexion->prepare("SELECT nombre_curso FROM cursos WHERE id_curso = ?");
    $stmt->bind_param("i", $id_curso);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        return $fila['nombre_curso'];
    } else {
        return "Desconocido";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tareas Asignadas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e7d6bf;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            color: #333;
            padding: 20px 0;
            font-size: 24px;
        }

        .table-container {
            max-width: 90%;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #ff9900;
            color: #fff;
            font-weight: bold;
            font-size: 16px;
        }

        td {
            border-bottom: 1px solid #ddd;
            font-size: 15px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .estado-vencida {
            color: red;
            font-weight: bold;
        }

        .estado-en-plazo {
            color: green;
            font-weight: bold;
        }

        .acciones a {
            margin: 0 5px;
            color: #ff9900;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        .acciones a:hover {
            color: #e68a00;
        }

        .back-button-container {
            text-align: center;
            margin: 20px;
        }

        .back-button {
            background-color: #ff9900;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #e68a00;
        }

        @media (max-width: 768px) {
            table, th, td {
                font-size: 14px;
            }

            th, td {
                padding: 8px;
            }

            .back-button {
                padding: 8px 16px;
                font-size: 14px;
            }

            h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

<h2>Tareas Asignadas</h2>

<div class="table-container">
    <table>
        <tr>
            <th>Materia</th>
            <th>Título</th>
            <th>Fecha de Entrega</th>
            <th>Entrega</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        <?php
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . obtenerNombreMateria($fila["id_curso"], $conexion) . "</td>";
                echo "<td>" . $fila["titulo"] . "</td>";
                echo "<td>" . $fila["fecha_limite"] . "</td>";
                echo "<td>" . $fila["estado_entrega"] . "</td>";
                echo "<td class='" . ($fila["estado_fecha"] === "Vencida" ? "estado-vencida" : "estado-en-plazo") . "'>" . $fila["estado_fecha"] . "</td>";
                echo "<td class='acciones'><a href='tarea.php?id=" . $fila["id_tarea"] . "'>Ver</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No hay tareas asignadas</td></tr>";
        }
        $conexion->close();
        ?>
    </table>
</div>

<div class="back-button-container">
    <a href="inicioAlumno.php" class="back-button">Regresar al inicio</a>
</div>

</body>
</html>
<?