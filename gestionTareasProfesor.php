<<<<<<< HEAD
<?php
session_start();
if (isset($_SESSION['usuario'])) {
    $num_control = $_SESSION['usuario'];
} else {
    echo "<script>alert('Error: Usuario no autenticado.'); window.location.href = 'index.php';</script>";
    exit;
}

// Conexión a la base de datos
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";

$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener materias del profesor
$sql = "SELECT id_curso, nombre_curso FROM cursos WHERE id_docente = '$num_control'";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tareas - Profesor</title>
    <link rel="stylesheet" href="css/estiloProfesor.css">
    <style>
        /* Estilos incluidos */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9fbfd;
            color: #333;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header nav {
            background-color: #ff9900;
            padding: 15px 0;
        }

        header nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            margin: 0;
            padding: 0;
        }

        header nav ul li {
            margin: 0 20px;
        }

        header nav ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        header nav ul li a:hover {
            color: #ffd966;
        }

        main {
            max-width: 1000px;
            margin: 30px auto;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        h1 {
            color: #333;
            font-size: 28px;
            text-align: center;
            font-weight: 700;
            margin-bottom: 25px;
        }

        h2 {
            font-size: 22px;
            color: #ff9900;
            border-bottom: 2px solid #ff9900;
            padding-bottom: 8px;
            font-weight: 600;
            margin-top: 0;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        form label {
            font-weight: 600;
            color: #333;
        }

        form input, form select, form textarea {
            padding: 15px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        form input:focus, form select:focus, form textarea:focus {
            border-color: #ff9900;
        }

        .button-container, .button-container-rubric {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .assign-button, .show-tasks-button, .rubric-button, .add-rubric-button, .remove-rubric-button {
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            border: none;
            min-width: 150px;
            text-align: center;
        }

        .assign-button, .rubric-button, .add-rubric-button {
            background-color: #ff9900;
            color: white;
            border: 2px solid #ff8303;
        }

        .assign-button:hover, .rubric-button:hover, .add-rubric-button:hover {
            background-color: #ff8303;
            color: #ffffff;
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .show-tasks-button, .remove-rubric-button {
            background-color: #333;
            color: white;
            border: 2px solid #444;
        }

        .show-tasks-button:hover, .remove-rubric-button:hover {
            background-color: #444;
            color: #ffffff;
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #ff9900;
            color: white;
        }

        .file-upload-preview {
            display: none;
            border: 2px dashed #ff9900;
            padding: 15px;
            border-radius: 8px;
            background-color: #fff4e6;
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            justify-content: center;
        }

        .file-upload-preview img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .file-upload-preview p {
            margin: 0;
            color: #555;
            font-size: 16px;
            font-weight: 500;
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: #333;
            color: #fff;
            font-size: 14px;
            border-top: 4px solid #ff9900;
        }

        #rubrica-dinamica {
            display: none;
        }

        /* Estilos del modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 400px;
        }

        .modal-content p {
            font-size: 16px;
            color: #333;
            margin-bottom: 20px;
        }

        .modal-content button {
            background-color: #ff9900;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<header>
    <nav>
        <ul>
            <li><a href="inicioProfesor.php">Inicio</a></li>
            <li><a href="calendarioProfesor.php">Calendario</a></li>
            <li><a href="gestionTareasProfesor.php">Gestión de Tareas</a></li>
        </ul>
    </nav>
</header>

<main>
    <h1>Gestión de Tareas</h1>

    <section id="asignar-tarea">
        <h2>Asignar Nueva Tarea</h2>
        <form action="asignarTarea.php" method="POST" enctype="multipart/form-data" onsubmit="return validarFecha();">
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

            <div class="button-container">
                <button type="button" class="add-rubric-button" onclick="mostrarRubrica()">Añadir Rubrica</button>
            </div>

            <div class="button-container">
                <button type="submit" class="assign-button">Asignar Tarea</button>
                <a href="listarTareas.php" class="show-tasks-button">Mostrar Tareas Asignadas</a>
            </div>
        </form>
    </section>

    <section id="rubrica-dinamica">
        <h2>Crear Rubrica Dinámica</h2>
        <table id="rubricaTable">
            <thead>
                <tr>
                    <th>Criterios</th>
                    <th>Puntos a cubrir</th>
                    <th>Puntos</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" placeholder="Documento en Pdf"></td>
                    <td><input type="text" placeholder="Descripción del criterio"></td>
                    <td><input type="number" class="puntos" value="0" min="0" oninput="calcularTotal()"></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"><strong>TOTAL</strong></td>
                    <td id="totalPuntos">0</td>
                </tr>
            </tfoot>
        </table>

        <div class="button-container-rubric">
            <button class="rubric-button" onclick="agregarFila()">Agregar Fila</button>
            <button class="rubric-button" onclick="quitarFila()">Quitar Fila</button>
            <button class="remove-rubric-button" onclick="ocultarRubrica()">Eliminar Rubrica</button>
        </div>
    </section>
</main>

<footer>
    <p>© 2024 PE-ISC</p>
</footer>

<!-- Modal -->
<div id="modal" class="modal">
    <div class="modal-content">
        <p id="modalMessage"></p>
        <button onclick="cerrarModal()">Aceptar</button>
    </div>
</div>

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
                fileIcon.src = 'file-icon.png';
            }
        } else {
            filePreview.style.display = 'none';
        }
    }

    function mostrarRubrica() {
        document.getElementById("rubrica-dinamica").style.display = "block";
        document.querySelector(".add-rubric-button").style.display = "none";
    }

    function ocultarRubrica() {
        document.getElementById("rubrica-dinamica").style.display = "none";
        document.querySelector(".add-rubric-button").style.display = "inline-block";
    }

    function agregarFila() {
        const tableBody = document.querySelector("#rubricaTable tbody");
        const newRow = document.createElement("tr");

        newRow.innerHTML = `
            <td><input type="text" placeholder="Criterio"></td>
            <td><input type="text" placeholder="Descripción del criterio"></td>
            <td><input type="number" class="puntos" value="0" min="0" oninput="calcularTotal()"></td>
        `;
        tableBody.appendChild(newRow);
    }

    function quitarFila() {
        const tableBody = document.querySelector("#rubricaTable tbody");
        if (tableBody.rows.length > 1) {
            tableBody.deleteRow(tableBody.rows.length - 1);
        } else {
            mostrarModal("Debe haber al menos una fila.");
        }
    }

    function calcularTotal() {
        const puntosInputs = document.querySelectorAll(".puntos");
        let total = 0;

        puntosInputs.forEach(input => {
            total += parseFloat(input.value) || 0;
        });

        if (total > 100) {
            mostrarModal("El total de puntos no puede exceder los 100.");
            total = 100;
        }

        document.getElementById("totalPuntos").textContent = total;
    }

    function validarFecha() {
        const fechaEntrega = document.getElementById("fechaEntrega").value;
        const fechaActual = new Date().toISOString().split("T")[0];

        if (fechaEntrega < fechaActual) {
            mostrarModal("La fecha de entrega no puede ser en el pasado.");
            return false;
        }
        return true;
    }

    function mostrarModal(mensaje) {
        document.getElementById("modalMessage").textContent = mensaje;
        document.getElementById("modal").style.display = "flex";
    }

    function cerrarModal() {
        document.getElementById("modal").style.display = "none";
    }
</script>

</body>
</html>

<?php
$conexion->close();
?>
=======
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
    <link rel="stylesheet" href="css/estiloProfesor.css">
    <style>
        /* Global Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9fbfd;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Header Styles */
        header nav {
            background-color: #ff9900;
            padding: 15px 0;
        }

        header nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            margin: 0;
            padding: 0;
        }

        header nav ul li {
            margin: 0 20px;
        }

        header nav ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        header nav ul li a:hover {
            color: #ffd966;
        }

        /* Main Container */
        main {
            max-width: 900px;
            margin: 40px auto;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            padding: 30px 40px;
        }

        h1 {
            color: #333;
            font-size: 28px;
            text-align: center;
            margin-bottom: 25px;
            font-weight: 700;
        }

        h2 {
            font-size: 22px;
            color: #ff9900;
            border-bottom: 2px solid #ff9900;
            padding-bottom: 8px;
            margin-top: 0;
            font-weight: 600;
        }

        /* Form Styles */
        form {
            display: grid;
            gap: 15px;
            margin-top: 20px;
        }

        form label {
            font-weight: 600;
            color: #333;
        }

        form input, form select, form textarea {
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        form input:focus, form select:focus, form textarea:focus {
            border-color: #ff9900;
        }

        form button {
            background-color: #ff9900;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form button:hover {
            background-color: #e68a00;
        }

        /* File Upload Preview Styles */
        .file-upload-preview {
            display: none;
            border: 2px dashed #ff9900;
            padding: 15px;
            border-radius: 10px;
            background-color: #fff4e6;
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .file-upload-preview img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .file-upload-preview p {
            margin: 0;
            color: #555;
            font-size: 16px;
            font-weight: 500;
        }

        /* Secondary Button for Showing Tasks */
        .show-tasks-button {
            display: block;
            background-color: #333;
            color: white;
            padding: 12px;
            text-align: center;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
            margin-top: 25px;
        }

        .show-tasks-button:hover {
            background-color: #555;
        }

        /* Footer Styles */
        footer {
            text-align: center;
            padding: 20px;
            background-color: #333;
            color: #fff;
            margin-top: 40px;
            font-size: 14px;
            border-top: 4px solid #ff9900;
        }
    </style>
</head>
<body>

<header>
    <nav>
        <ul>
            <li><a href="inicioProfesor.php">Inicio</a></li>
            <li><a href="calendarioProfesor.php">Calendario</a></li>
            <li><a href="gestionTareasProfesor.php">Gestión de Tareas</a></li>
        </ul>
    </nav>
</header>

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
>>>>>>> 99456494b6ca6fceab23f5e875cd1448b1e3ae7c
