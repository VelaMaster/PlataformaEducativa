<?php
session_start();
if (!isset($_SESSION['usuario'])) {
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

// Procesamiento del formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $materia = $_POST['materia'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fechaEntrega = $_POST['fechaEntrega'];
    $archivoPath = null;

    // Subida de archivo
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == UPLOAD_ERR_OK) {
        $archivoNombre = $_FILES['archivo']['name'];
        $archivoTmp = $_FILES['archivo']['tmp_name'];
        $archivoPath = "uploads/" . basename($archivoNombre);

        if (!move_uploaded_file($archivoTmp, $archivoPath)) {
            echo "<script>alert('Error al subir el archivo.');</script>";
            $archivoPath = null;
        }
    }

    // Insertar tarea en la base de datos
    $sql = "INSERT INTO tareas (id_curso, titulo, descripcion, fecha_limite, archivo_tarea) VALUES ('$materia', '$titulo', '$descripcion', '$fechaEntrega', '$archivoPath')";
    if ($conexion->query($sql) === TRUE) {
        $tarea_id = $conexion->insert_id;

        // Verificar si existen datos de rúbrica
        if (isset($_POST['criterios']) && isset($_POST['puntosCubrir']) && isset($_POST['puntos'])) {
            $criterios = $_POST['criterios'];
            $puntosCubrir = $_POST['puntosCubrir'];
            $puntos = $_POST['puntos'];

            for ($i = 0; $i < count($criterios); $i++) {
                $criterio = $conexion->real_escape_string($criterios[$i]);
                $descripcionRubrica = $conexion->real_escape_string($puntosCubrir[$i]);
                $puntaje_maximo = (int)$puntos[$i];

                // Insertar cada criterio en la base de datos
                $sqlRubrica = "INSERT INTO rubricas (id_tarea, criterio, descripcion, puntos) VALUES ('$tarea_id', '$criterio', '$descripcionRubrica', '$puntaje_maximo')";
                if (!$conexion->query($sqlRubrica)) {
                    echo "Error al insertar rúbrica: " . $conexion->error . "<br>";
                }
            }
            echo "<script>alert('Tarea y rúbrica asignadas con éxito.'); window.location.href = 'gestionTareasProfesor.php';</script>";
        } else {
            echo "<script>alert('No se recibieron datos de rúbrica.');</script>";
        }
    } else {
        echo "<script>alert('Error al asignar la tarea: " . $conexion->error . "');</script>";
    }

    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tareas - Profesor</title>
    <link rel="stylesheet" href="css/estiloProfesor.css">
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
                $sql = "SELECT c.id_curso, c.nombre_curso FROM cursos c JOIN grupos g ON c.id_curso = g.id_curso WHERE g.id_docente = '".$_SESSION['usuario']."'";
                $resultado = $conexion->query($sql);
                if ($resultado->num_rows > 0) {
                    while ($row = $resultado->fetch_assoc()) {
                        echo "<option value='" . $row['id_curso'] . "'>" . $row['nombre_curso'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No tiene materias asignadas</option>";
                }
                ?>
            </select>

            <label for="titulo">Título de la Tarea:</label>
            <input type="text" id="titulo" name="titulo" required>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required></textarea>

            <label for="fechaEntrega">Fecha de Entrega:</label>
            <input type="date" id="fechaEntrega" name="fechaEntrega" required>

            <label for="archivo">Subir archivo:</label>
            <input type="file" id="archivo" name="archivo">

            <!-- Rubric Section -->
            <h2>Crear Rubrica Dinámica</h2>
            <table id="rubricaTable">
                <thead>
                    <tr>
                        <th>Criterio</th>
                        <th>Puntos a cubrir</th>
                        <th>Puntaje Máximo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="criterios[]" placeholder="Criterio" required></td>
                        <td><input type="text" name="puntosCubrir[]" placeholder="Descripción del criterio" required></td>
                        <td><input type="number" name="puntos[]" value="0" min="0" required></td>
                    </tr>
                </tbody>
            </table>

            <button type="button" onclick="agregarFila()">Agregar Criterio</button>
            <button type="button" onclick="quitarFila()">Quitar Criterio</button>

            <button type="submit">Asignar Tarea</button>
        </form>
    </section>
</main>

<footer>
    <p>© 2024 Plataforma de Educación</p>
</footer>

<script>
    function agregarFila() {
        const tableBody = document.querySelector("#rubricaTable tbody");
        const newRow = document.createElement("tr");
        newRow.innerHTML = `
            <td><input type="text" name="criterios[]" placeholder="Criterio" required></td>
            <td><input type="text" name="puntosCubrir[]" placeholder="Descripción del criterio" required></td>
            <td><input type="number" name="puntos[]" value="0" min="0" required></td>
        `;
        tableBody.appendChild(newRow);
    }

    function quitarFila() {
        const tableBody = document.querySelector("#rubricaTable tbody");
        if (tableBody.rows.length > 1) {
            tableBody.deleteRow(tableBody.rows.length - 1);
        } else {
            alert("Debe haber al menos una fila.");
        }
    }
</script>

</body>
</html>

<?php
$conexion->close();
?>
