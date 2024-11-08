<?php
// Conexión a la base de datos
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";

$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Consulta para obtener las tareas asignadas a todos los estudiantes
$sql = "SELECT DISTINCT tareas.id_tarea, tareas.id_curso, tareas.titulo, tareas.fecha_limite 
        FROM tareas
        JOIN grupo_alumnos ON tareas.id_curso = grupo_alumnos.id_grupo";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tareas Asignadas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
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

        th {
            background-color: #ff9900;
            color: #fff;
            font-weight: bold;
            text-align: left;
            padding: 12px;
            font-size: 16px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            font-size: 15px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
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
    </style>
</head>
<body>

<h2>Tareas Asignadas</h2>

<div class="table-container">
    <table>
        <tr>
            <th>Materia</th>
            <th>Título de la Tarea</th>
            <th>Fecha de Entrega</th>
            <th>Acciones</th>
        </tr>
        <?php
        if ($resultado->num_rows > 0) {
            while($fila = $resultado->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . obtenerNombreMateria($fila["id_curso"], $conexion) . "</td>";
                echo "<td>" . $fila["titulo"] . "</td>";
                echo "<td>" . $fila["fecha_limite"] . "</td>";
                echo "<td class='acciones'>
                        <a href='tarea.php?id=" . $fila["id_tarea"] . "'>Ver</a> 
                      
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No hay tareas asignadas</td></tr>";
        }
        $conexion->close();
        ?>
    </table>
</div>

<div class="back-button-container">
    <a href="inicioAlumno.php" class="back-button">Regresar al inicio</a>
</div>

<script>
    let idTareaEliminar = null;

    function confirmarEliminacion(idTarea) {
        idTareaEliminar = idTarea;
        document.getElementById('modalEliminar').style.display = 'flex';
    }

    function cerrarModal() {
        document.getElementById('modalEliminar').style.display = 'none';
        idTareaEliminar = null;
    }

    function eliminarTarea() {
        if (idTareaEliminar) {
            window.location.href = 'eliminarTarea.php?id=' + idTareaEliminar;
        }
    }
</script>

</body>
</html>

<?php
// Función para obtener el nombre de la materia basado en el id_curso
function obtenerNombreMateria($id_curso, $conexion) {
    $consulta = "SELECT nombre_curso FROM cursos WHERE id_curso = $id_curso";
    $resultado = $conexion->query($consulta);
    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        return $fila['nombre_curso'];
    } else {
        return "Desconocido";
    }
}
?>
