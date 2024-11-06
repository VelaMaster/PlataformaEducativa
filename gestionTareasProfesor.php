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
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
            border-radius: 6px;
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
            border-radius: 6px;
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
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 8px;
            background-color: #f5f5f5;
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .file-upload-preview img {
            width: 40px;
            height: 40px;
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
