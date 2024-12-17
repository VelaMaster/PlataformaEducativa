<?php
session_start();
require 'db.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
$num_control = $_SESSION['usuario'];
$sql = " SELECT 
        tareas.id AS id_tarea, 
        tareas.id_curso, 
        tareas.titulo, 
        tareas.fecha_limite, 
        CASE 
            WHEN entregas.archivo_entrega IS NOT NULL THEN 'Entregado' 
            ELSE 'No Entregado' 
            END AS estado_entrega,
        CASE 
            WHEN entregas.calificacion IS NOT NULL THEN 'Calificada' 
            ELSE 'No calificada' 
            END AS estado_calificacion
    FROM tareas
    JOIN grupos ON tareas.id_curso = grupos.id_curso
    JOIN grupo_alumnos ON grupos.id = grupo_alumnos.id_grupo
    LEFT JOIN entregas ON tareas.id = entregas.id_tarea AND entregas.id_alumno = grupo_alumnos.num_control
    WHERE grupo_alumnos.num_control = ?
    AND tareas.fecha_limite >= CURDATE()
";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('s', $num_control);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tareas Asignadas</title>
    <link rel="stylesheet" href="css/gestionTareasAlumno.css?v=<?php echo time(); ?>">
</head>
<body>

<h2>Tareas Asignadas</h2>

<div class="table-container">
    <table>
    <tr>
    <th>Materia</th>
    <th>Título de la Tarea</th>
    <th>Fecha de Entrega</th>
    <th>Estado de Entrega</th>
    <th>Estado de Calificación</th>
    <th>Acciones</th>
</tr>
<?php
    if ($resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . obtenerNombreMateria($fila["id_curso"], $conexion) . "</td>";
            echo "<td>" . $fila["titulo"] . "</td>";
            echo "<td>" . $fila["fecha_limite"] . "</td>";
            echo "<td>" . ($fila["estado_entrega"] ?? 'No Entregado') . "</td>";
            echo "<td>" . ($fila["estado_calificacion"] ?? 'No calificada') . "</td>";
            
            

            echo "<td class='acciones'> <a href='verdetallesTarea.php?id=" . $fila["id_tarea"] . "'>Ver</a> </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No hay tareas asignadas</td></tr>";
    }
?>

    </table>
</div>

<div class="back-button-container">
    <a href="inicioAlumno.php" class="back-button">Regresar al inicio</a>
</div>
<footer class="text-center py-3">
    <p>© 2024 PE-ISC</p>
    </footer>

</body>
</html>

<?php
function obtenerNombreMateria($id_curso, $conexion) {
    $consulta = "SELECT nombre_curso FROM cursos WHERE id = ?";
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param('i', $id_curso);
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

