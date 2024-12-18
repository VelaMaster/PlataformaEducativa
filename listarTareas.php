<?php
session_start(); 
include('db.php');

if (!isset($_SESSION['num_control'])) {
    header('Location: login.php');
    exit();
}
$docente_num_control = $_SESSION['num_control'];
$query_docente = "SELECT id FROM docentes WHERE num_control = '$docente_num_control'";
$result_docente = mysqli_query($conexion, $query_docente);
$docente_data = mysqli_fetch_assoc($result_docente);
$docente_id = $docente_data['id'];

$query_cursos = "SELECT DISTINCT c.id, c.nombre_curso
                 FROM cursos c
                 INNER JOIN grupos g ON c.id = g.id_curso
                 WHERE g.id_docente = '$docente_id'";
$result_cursos = mysqli_query($conexion, $query_cursos);

$id_curso = isset($_GET['id_curso']) ? intval($_GET['id_curso']) : 0;
$sql = "SELECT t.id, t.titulo, t.fecha_limite, c.nombre_curso
        FROM tareas t
        INNER JOIN cursos c ON t.id_curso = c.id
        INNER JOIN grupos g ON c.id = g.id_curso
        WHERE g.id_docente = '$docente_id'";

if ($id_curso > 0) {
    $sql .= " AND c.id = '$id_curso'";
}

$resultado = mysqli_query($conexion, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    
    <title>Tareas Asignadas</title>
    <link rel="stylesheet" href="css/listarTareas.css?v=<?php echo time(); ?>">
</head>



<body>
    <!-- Barra de navegación -->
    <div class="barranavegacion">
        <span>Plataforma educativa para Ingeniería en Sistemas</span>
        <a href="inicioProfesor.php">Inicio</a>
        <a href="calendarioDocente.php">Calendario</a>
        <a href="gestionTareasProfesor.php">Asignar tareas</a>
        <a href="gestionForosProfesor.php">Asignar foros</a>
        <a href="calificarTareas.php">Calificar tareas</a>
        <a href="calificarForos.php">Calificar foros</a>
    </div>

    <div class="container">
    <h2 class="my-4 text-center">Tareas Asignadas</h2>
  

    <!-- Formulario de Filtrado -->
    <form method="GET" action="listarTareas.php" class="row g-3 filtro-container">
        <div class="col-md-8">
            <label for="id_curso" class="form-label">Filtrar por curso:</label>
            <select name="id_curso" id="id_curso" class="form-select">
                <option value="0">Todos</option>
                <?php
                if ($result_cursos && mysqli_num_rows($result_cursos) > 0) {
                    while($curso = mysqli_fetch_assoc($result_cursos)) {
                        $selected = ($id_curso == $curso['id']) ? 'selected' : '';
                        echo "<option value='" . $curso['id'] . "' $selected>" . htmlspecialchars($curso['nombre_curso']) . "</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
        </div>
    </form>

    <div class="table-container">
        <table>
            <tr>
                <th>Materia</th>
                <th>Título de la Tarea</th>
                <th>Fecha de Entrega</th>
                <th>Acciones</th>
            </tr>
            <?php
            if ($resultado && mysqli_num_rows($resultado) > 0) {
                while($fila = mysqli_fetch_assoc($resultado)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($fila["nombre_curso"]) . "</td>";
                    echo "<td>" . htmlspecialchars($fila["titulo"]) . "</td>";
                    echo "<td>" . htmlspecialchars($fila["fecha_limite"]) . "</td>";
                    echo "<td class='acciones'>
                            <a href='verTarea.php?id=" . $fila["id"] . "'>Ver</a> |
                            <a href='editarTarea.php?id=" . $fila["id"] . "'>Editar</a> |
                            <a href='#' onclick='confirmarEliminacion(" . $fila["id"] . ")'>Eliminar</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No hay tareas asignadas</td></tr>";
            }

            mysqli_close($conexion);
            ?>
        </table>
    </div>

    <!-- Botón para regresar -->
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
