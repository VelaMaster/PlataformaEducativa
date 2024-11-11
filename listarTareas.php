<<<<<<< HEAD
<?php
// listarTareas.php

// Database connection
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";

$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Query to fetch assigned tasks
$sql = "SELECT id_tarea, id_curso, titulo, fecha_limite FROM tareas";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tareas Asignadas</title>
    <style>
        /* Global Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9fbfd;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            color: #333;
            padding: 20px 0;
            font-size: 32px;
            font-weight: 700;
            margin: 0;
        }

        /* Table Container */
        .table-container {
            max-width: 80%;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
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
            padding: 16px;
            font-size: 18px;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #e6e6e6;
            font-size: 16px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #fef3e6;
        }

        .acciones a {
            margin: 0 10px;
            color: #ff9900;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        .acciones a:hover {
            color: #e68a00;
        }

        /* Back Button */
        .back-button-container {
            text-align: center;
            margin: 30px;
        }

        .back-button {
            background-color: #ff9900;
            color: #fff;
            padding: 14px 30px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
            font-size: 16px;
        }

        .back-button:hover {
            background-color: #e68a00;
        }

        /* Modal Overlay */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        /* Modal Content */
        .modal-content {
            background-color: #fff;
            padding: 25px;
            width: 360px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.25);
            text-align: center;
        }

        .modal-content p {
            margin-bottom: 20px;
            font-size: 18px;
            color: #333;
        }

        /* Modal Buttons */
        .modal-buttons {
            display: flex;
            justify-content: space-between;
        }

        .modal-button {
            padding: 12px 28px;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        .confirm-button {
            background-color: #ff9900;
            color: #fff;
        }

        .confirm-button:hover {
            background-color: #e68a00;
        }

        .cancel-button {
            background-color: #ccc;
            color: #333;
        }

        .cancel-button:hover {
            background-color: #aaa;
        }

        /* Footer */
        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 20px 0;
            font-size: 14px;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

        .footer-divider {
            height: 5px;
            background-color: #ff9900;
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
                        <a href='verTarea.php?id=" . $fila["id_tarea"] . "'>Ver</a> |
                        <a href='editarTarea.php?id=" . $fila["id_tarea"] . "'>Editar</a> |
                        <a href='#' onclick='confirmarEliminacion(" . $fila["id_tarea"] . ")'>Eliminar</a>
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
    <a href="gestionTareasProfesor.php" class="back-button">Regresar a Gestión de Tareas</a>
</div>

<!-- Modal HTML -->
<div id="modalEliminar" class="modal-overlay">
    <div class="modal-content">
        <p>¿Estás seguro de que deseas eliminar esta tarea?</p>
        <div class="modal-buttons">
            <button class="modal-button confirm-button" onclick="eliminarTarea()">Confirmar</button>
            <button class="modal-button cancel-button" onclick="cerrarModal()">Cancelar</button>
        </div>
    </div>
</div>

<footer>
    <div class="footer-divider"></div>
    <p>&copy; 2024 PE-ISC</p>
</footer>

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
// Function to get course name based on id_curso
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
=======
<?php
// listarTareas.php

// Database connection
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";

$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Query to fetch assigned tasks
$sql = "SELECT id_tarea, id_curso, titulo, fecha_limite FROM tareas";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tareas Asignadas</title>
    <style>
        /* Global Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9fbfd;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            color: #333;
            padding: 20px 0;
            font-size: 32px;
            font-weight: 700;
            margin: 0;
        }

        /* Table Container */
        .table-container {
            max-width: 80%;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
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
            padding: 16px;
            font-size: 18px;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #e6e6e6;
            font-size: 16px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #fef3e6;
        }

        .acciones a {
            margin: 0 10px;
            color: #ff9900;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        .acciones a:hover {
            color: #e68a00;
        }

        /* Back Button */
        .back-button-container {
            text-align: center;
            margin: 30px;
        }

        .back-button {
            background-color: #ff9900;
            color: #fff;
            padding: 14px 30px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
            font-size: 16px;
        }

        .back-button:hover {
            background-color: #e68a00;
        }

        /* Modal Overlay */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        /* Modal Content */
        .modal-content {
            background-color: #fff;
            padding: 25px;
            width: 360px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.25);
            text-align: center;
        }

        .modal-content p {
            margin-bottom: 20px;
            font-size: 18px;
            color: #333;
        }

        /* Modal Buttons */
        .modal-buttons {
            display: flex;
            justify-content: space-between;
        }

        .modal-button {
            padding: 12px 28px;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        .confirm-button {
            background-color: #ff9900;
            color: #fff;
        }

        .confirm-button:hover {
            background-color: #e68a00;
        }

        .cancel-button {
            background-color: #ccc;
            color: #333;
        }

        .cancel-button:hover {
            background-color: #aaa;
        }

        /* Footer */
        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 20px 0;
            font-size: 14px;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

        .footer-divider {
            height: 5px;
            background-color: #ff9900;
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
                        <a href='verTarea.php?id=" . $fila["id_tarea"] . "'>Ver</a> |
                        <a href='editarTarea.php?id=" . $fila["id_tarea"] . "'>Editar</a> |
                        <a href='#' onclick='confirmarEliminacion(" . $fila["id_tarea"] . ")'>Eliminar</a>
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
    <a href="gestionTareasProfesor.php" class="back-button">Regresar a Gestión de Tareas</a>
</div>

<!-- Modal HTML -->
<div id="modalEliminar" class="modal-overlay">
    <div class="modal-content">
        <p>¿Estás seguro de que deseas eliminar esta tarea?</p>
        <div class="modal-buttons">
            <button class="modal-button confirm-button" onclick="eliminarTarea()">Confirmar</button>
            <button class="modal-button cancel-button" onclick="cerrarModal()">Cancelar</button>
        </div>
    </div>
</div>

<footer>
    <div class="footer-divider"></div>
    <p>&copy; 2024 PE-ISC</p>
</footer>

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
// Function to get course name based on id_curso
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
>>>>>>> a2a1b46207def8b4e85769ff9f52bde6c6ef020e
