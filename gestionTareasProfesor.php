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
$sql = "SELECT id_curso, nombre_curso FROM cursos WHERE id_docente = '$num_control'";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tareas - Profesor</title>
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/iniciosesionalumno.css">
    <link rel="stylesheet" href="css/estilostarjetas.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/gestiontareas.css"> <!--ESTILO DE FORMULARIO DE TAREAS -->
</head>
<body>


<!-- Barra de navegación -->
<div class="barranavegacion">
 <div class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Plataforma educativa para Ingenieria en Sistemas</a>
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
                    <a class="nav-link" href="gestionTareasProfesor.php">Gestionar tareas</a>
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
        <form action="asignarTarea.php" method="POST" enctype="multipart/form-data">
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

            <label for="archivo">Subir archivo (opcional):</label>
            <input type="file" id="archivo" name="archivo" onchange="previewFile()">

            <div class="file-upload-preview" id="filePreview">
                <img src="file-icon.png" alt="Archivo" id="fileIcon">
                <p id="fileName">Ningún archivo seleccionado</p>
            </div>

            <button type="submit">Asignar Tarea</button>
        </form>
    </section>

    <section id="mostrar-tareas">
        <a href="listarTareas.php" class="show-tasks-button">Mostrar Tareas Asignadas</a>
    </section>
</main>

<footer>
    <p>© 2024 PE-ISC</p>
</footer>

<script>
    function previewFile() {
        const fileInput = document.getElementById('archivo');
        const filePreview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');
        const fileIcon = document.getElementById('fileIcon');

        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            fileName.textContent = file.name;
            filePreview.style.display = 'flex';

            if (file.type.startsWith('image/')) {
                fileIcon.src = URL.createObjectURL(file);
            } else {
                fileIcon.src = 'file-icon.png'; // Fallback icon for non-image files
            }
        } else {
            filePreview.style.display = 'none';
        }
    }
</script>

</body>
</html>

<?php
$conexion->close();
?>
