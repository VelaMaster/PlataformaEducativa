<?php
session_start();
require 'db.php';
if (isset($_SESSION['usuario'])) {
    $num_control = $_SESSION['usuario'];
} else {
    echo "<script>alert('Error: Usuario no autenticado.'); window.location.href = 'index.php';</script>";
    exit;
}
$sql = "
    SELECT c.id AS id_curso, c.nombre_curso 
    FROM cursos c 
    JOIN grupos g ON c.id = g.id_curso 
    WHERE g.id_docente = (
        SELECT d.id 
        FROM docentes d 
        WHERE d.num_control = ?
    )
";
$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, 's', $num_control);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Tareas - Profesor</title>
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/gestionTareasprofesor.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/iniciosesionalumno.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/barradeNavegacion.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/seleccionarArchivo.css?v=<?php echo time(); ?>">
    <style>
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-toggle {
            background-color: #ff5722;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #fff;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 5px;
            overflow: hidden;
            width: 220px;
        }

        .dropdown-menu a {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            color: #000;
            text-decoration: none;
            gap: 10px;
        }

        .dropdown-menu a:hover {
            background-color: #f1f1f1;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        .dropdown-menu svg {
            width: 24px;
            height: 24px;
        }

        .add-rubric-button, .remove-rubric-button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-weight: bold;
        }

        .add-rubric-button:hover, .remove-rubric-button:hover {
            background-color: #0056b3;
        }
    </style>
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
                        <a class="nav-link" href="calendarioDocente.php">Calendario</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestionTareasProfesor.php">Asignar tareas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="calificarTareas.php">Calificar tareas</a>
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
                if ($resultado && mysqli_num_rows($resultado) > 0) {
                    while ($row = mysqli_fetch_assoc($resultado)) {
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
    <input type="file" id="archivo" name="archivo">
</label>
            <div class="file-upload-preview" id="filePreview" style="display: none;">
                <img src="" alt="Previsualización de archivo" id="fileIcon" onclick="abrirModal(this.src)" ondblclick="window.open(this.src, '_blank')">
                <p id="fileName">Ningún archivo seleccionado</p>
            </div>


            <div class="button-container">
            <div class="dropdown">
    <button class="dropdown-toggle">+ Agregar o crear</button>
    <div class="dropdown-menu">
        <a href="https://drive.google.com" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" fill="#4285F4" viewBox="0 0 24 24">
                <path d="M12 2L2 12l5 8h10l5-8L12 2z"></path>
                <path d="M12 2L2 12h10l5-8z" fill="#0F9D58"></path>
                <path d="M17 12h-5l5 8h5l-5-8z" fill="#F4B400"></path>
            </svg>
            Google Drive
        </a>
        <a href="https://www.canva.com" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" fill="#00C4CC" viewBox="0 0 24 24">
                <path d="..."></path>
            </svg>
            Canva
                        </a>
                        <a href="https://docs.google.com/presentation" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="#D24726" viewBox="0 0 24 24">
                                <path d="M6 2C4.9 2 4 2.9 4 4v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6H6zm7 1.5L18.5 9H13V3.5z"></path>
                            </svg>
            Presentación
        </a>
        <a href="https://docs.google.com/document" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" fill="#2B579A" viewBox="0 0 24 24">
                <path d="M6 2C4.9 2 4 2.9 4 4v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6H6zm7 1.5L18.5 9H13V3.5z"></path>
            </svg>
            Documento
        </a>
    </div>
</div>


                <button type="button" id="addRubricaButton" class="add-rubric-button" onclick="mostrarRubrica()">Añadir Rúbrica</button>
                <button type="button" id="removeRubricaButton" class="remove-rubric-button" onclick="ocultarRubrica()" style="display: none;">Eliminar Rúbrica</button>
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

<script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="js/seleccionarArchivos.js"></script>
<script src="js/rubrica.js"></script>

<script>
    function mostrarRubrica() {
        document.getElementById('rubricaContainer').style.display = 'block';
        document.getElementById('addRubricaButton').style.display = 'none';
        document.getElementById('removeRubricaButton').style.display = 'inline-block';
    }

    function ocultarRubrica() {
        document.getElementById('rubricaContainer').style.display = 'none';
        document.getElementById('addRubricaButton').style.display = 'inline-block';
        document.getElementById('removeRubricaButton').style.display = 'none';
    }
</script>
</body>
</html>
<?php
mysqli_free_result($resultado);
mysqli_stmt_close($stmt);
?>
