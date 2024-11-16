<?php
session_start();
if (isset($_SESSION['usuario'])) {
    $num_control = $_SESSION['usuario'];
} else {
    echo "<script>alert('Error: Usuario no autenticado.'); window.location.href = 'index.php';</script>";
    exit;
}
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";
$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
$sql = "
    SELECT c.id_curso, c.nombre_curso 
    FROM cursos c 
    JOIN grupos g ON c.id_curso = g.id_curso 
    WHERE g.id_docente = '$num_control'
";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Tareas - Profesor</title>
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/gestionTareasprofesor.css">
    <link rel="stylesheet" href="css/iniciosesionalumno.css">
    <link rel="stylesheet" href="css/barradeNavegacion.css">
    <link rel="stylesheet" href="css/seleccionarArchivo.css">
</head>
<body>
<div class="barranavegacion">
    <div class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Plataforma educativa para Ingeniería en Sistemas</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="inicioProfesor.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="calendarioProfesor.php">Calendario</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestionTareasProfesor.php">Tareas</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<main>
    <h1>Gestión de Tareas</h1>

    <section id="asignar-tarea">
        <h2>Asignar Nueva Tarea</h2>
        <form action="asignarTarea.php" method="POST" enctype="multipart/form-data" onsubmit="return validarFecha() && validarTotalPuntos();">
            <label for="materia">Materia:</label>
            <select id="materia" name="materia" required>
                <?php
                if ($resultado->num_rows > 0) {
                    while($row = $resultado->fetch_assoc()) {
                        echo "<option value='" . $row['id_curso'] . "'>" . $row['nombre_curso'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No tiene materias asignadas</option>";
                }
                ?>
            </select>

            <label for="titulo">Título de la Tarea:</label>
            <input type="text" id="titulo" name="titulo" required placeholder="Ingrese el título de la tarea">

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required placeholder="Describa los detalles de la tarea"></textarea>

            <label for="fechaEntrega">Fecha de Entrega:</label>
            <input type="date" id="fechaEntrega" name="fechaEntrega" required>

            <label class="custom-file-upload">
                Seleccionar archivo
                <input type="file" id="archivo" name="archivo" onchange="previewFile()">
            </label>
            <div class="file-upload-preview" id="filePreview" style="display: none;">
                <img src="" alt="Previsualización de archivo" id="fileIcon" onclick="abrirModal(this.src)" ondblclick="window.open(this.src, '_blank')">
                <p id="fileName">Ningún archivo seleccionado</p>
            </div>

            <div class="button-container">
            <button type="button" class="add-rubric-button" onclick="mostrarRubrica()">Añadir Rúbrica</button>
            </div>

            <div id="rubricaContainer" style="display: none;">
    <h3>Rúbrica de Evaluación</h3>
    <table id="rubricaTable">
        <thead>
            <tr>
                <th>Criterio</th>
                <th>Descripción</th>
                <th>Puntos</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <div class="button-container">
        <button type="button" class="add-row-button" onclick="agregarFilaRubrica()">Añadir Fila</button>
    </div>

    <p>Total de Puntos Asignados: <span id="totalPuntos">0</span>/100</p>
</div>

            <div class="button-container">
                <button type="submit" class="assign-button">Asignar Tarea</button>
                <a href="listarTareas.php" class="show-tasks-button">Mostrar Tareas Asignadas</a>
            </div>
        </form>
    </section>
</main>

<footer>
    <p>© 2024 PE-ISC</p>
</footer>

<div id="previewModal" class="modal" onclick="cerrarModal()">
    <span class="close" onclick="cerrarModal()">&times;</span>
    <img class="modal-content" id="modalImage" style="display: none;">
    <div id="modalText" style="display: none; white-space: pre-wrap; padding: 20px; background-color: #fff; border-radius: 25px;"></div>
    <iframe id="modalIframe" style="display: none; width: 100%; height: 80vh; border: none;"></iframe>
    <p id="unsupportedText" style="display: none; padding: 80px; text-align: center;">Este tipo de archivo no es compatible con la previsualización.</p>
</div>
<script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="js/seleccionarArchivos.js"></script>
<script src="js/rubrica.js"></script>

<script>

    document.addEventListener('DOMContentLoaded', function() {
        var fechaHoy = new Date().toISOString().split('T')[0];
        document.getElementById('fechaEntrega').setAttribute('min', fechaHoy);
    });
    function validarFecha() {
        var fechaSeleccionada = document.getElementById('fechaEntrega').value;
        var fechaHoy = new Date().toISOString().split('T')[0];
        if (fechaSeleccionada < fechaHoy) {
            alert('La fecha de entrega no puede ser una fecha pasada.');
            return false;
        }
        return true;
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</body>
</html>

<?php
$conexion->close();
?>
