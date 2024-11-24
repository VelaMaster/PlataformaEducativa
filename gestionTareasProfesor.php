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
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/gestionTareasprofesor.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/iniciosesionalumno.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/barradeNavegacion.css?v=<?php echo time(); ?>">
<<<<<<< HEAD
    <style>
        .button-container {
            display: flex;
            gap: 10px;
            align-items: center;
        }

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

        .add-rubric-button, .delete-rubric-button {
            background-color: #f8f9fa;
            color: #000;
            border: 1px solid #ccc;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
=======
    <link rel="stylesheet" href="css/seleccionarArchivo.css?v=<?php echo time(); ?>">
>>>>>>> f5f1defea1ff424cfed960e68aa29dc87d5a2a19
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
<<<<<<< HEAD

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
                                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm3.29 16.36c-.703.563-1.446.996-2.173 1.12-.583.1-1.14-.046-1.603-.397-.247-.19-.515-.375-.797-.562-.098-.066-.198-.123-.297-.19-.4-.255-.857-.417-1.33-.47-.517-.06-1.034.034-1.548.15-.273.064-.547.146-.82.236a9.56 9.56 0 01-.297.085c-.272.084-.494.002-.683-.17-.092-.085-.152-.193-.212-.3-.063-.115-.123-.23-.174-.347-.157-.364-.245-.732-.353-1.106-.223-.763-.47-1.517-.722-2.272-.156-.484-.3-.968-.467-1.447-.063-.184-.13-.37-.207-.556-.055-.137-.116-.27-.186-.4-.047-.086-.106-.17-.17-.25-.103-.124-.2-.112-.302-.01-.047.048-.087.102-.127.156-.287.385-.563.77-.88 1.14-.283.333-.62.618-1.04.77-.23.08-.465.144-.693.227a.83.83 0 01-.97-.234c-.35-.417-.445-.916-.44-1.42.008-.767.326-1.48.836-2.058.588-.66 1.3-1.148 2.13-1.455a6.24 6.24 0 012.222-.41c.817.006 1.606.187 2.368.482.472.177.93.394 1.378.626.425.22.85.447 1.266.688.358.206.732.345 1.138.42.395.073.787.063 1.173-.012.275-.054.545-.146.805-.263.246-.11.478-.266.7-.43.36-.268.61-.614.85-.96.088-.13.174-.26.262-.39.02-.03.056-.062.086-.063.06-.002.1.034.14.07.187.18.37.368.54.562.22.25.402.53.553.826.34.666.527 1.376.638 2.108.08.532.046 1.054-.164 1.565-.158.386-.372.746-.683 1.015-.3.26-.664.42-1.028.53a3.568 3.568 0 01-1.27.087c-.542-.04-.98-.242-1.398-.566z"></path>
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
                <button type="button" class="add-rubric-button" onclick="mostrarRubrica()">Añadir Rúbrica</button>
                <button type="button" class="delete-rubric-button" id="deleteRubricButton" style="display: none;" onclick="eliminarRubrica()">Eliminar Rúbrica</button>
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
                <p>Total de Puntos Asignados: <span id="totalPuntos">100</span>/100</p>
=======
            <div class="file-upload-preview" id="filePreview" style="display: none;">
                <img src="" alt="Previsualización de archivo" id="fileIcon" onclick="abrirModal(this.src)" ondblclick="window.open(this.src, '_blank')">
                <p id="fileName">Ningún archivo seleccionado</p>
            </div>

            <div class="button-container">
            <button type="button" class="add-rubric-button" onclick="mostrarRubrica()">Añadir Rúbrica</button>
>>>>>>> f5f1defea1ff424cfed960e68aa29dc87d5a2a19
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

<<<<<<< HEAD
<script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var fechaHoy = new Date().toISOString().split('T')[0];
        document.getElementById('fechaEntrega').setAttribute('min', fechaHoy);
    });
=======
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
>>>>>>> f5f1defea1ff424cfed960e68aa29dc87d5a2a19

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
<<<<<<< HEAD

    function mostrarRubrica() {
        document.getElementById('rubricaContainer').style.display = 'block';
        document.getElementById('deleteRubricButton').style.display = 'inline-block';
    }

    function eliminarRubrica() {
        document.getElementById('rubricaContainer').style.display = 'none';
        document.getElementById('deleteRubricButton').style.display = 'none';
        document.querySelector('#rubricaTable tbody').innerHTML = '';
        document.getElementById('totalPuntos').textContent = '100';
    }

    function agregarFilaRubrica() {
        const tabla = document.querySelector('#rubricaTable tbody');
        const fila = document.createElement('tr');

        fila.innerHTML = `
            <td><input type="text" name="criterio[]" placeholder="Criterio" required></td>
            <td><input type="text" name="descripcion[]" placeholder="Descripción" required></td>
            <td><input type="number" name="puntos[]" readonly></td>
            <td><button type="button" onclick="eliminarFila(this)">Eliminar</button></td>
        `;

        tabla.appendChild(fila);
        actualizarTotalPuntos();
    }

    function eliminarFila(boton) {
        boton.closest('tr').remove();
        actualizarTotalPuntos();
    }

    function actualizarTotalPuntos() {
        const filas = document.querySelectorAll('#rubricaTable tbody tr');
        const totalFilas = filas.length;
        const puntosPorFila = totalFilas > 0 ? (100 / totalFilas).toFixed(2) : 0;

        filas.forEach((fila) => {
            const puntosInput = fila.querySelector('input[name="puntos[]"]');
            puntosInput.value = puntosPorFila;
        });

        document.getElementById('totalPuntos').textContent = "100";
    }
</script>
=======
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


>>>>>>> f5f1defea1ff424cfed960e68aa29dc87d5a2a19
</body>
</html>
<?php
$conexion->close();
?>
