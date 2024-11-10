<?php
// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conexión a la base de datos
    $servidor = "localhost";
    $usuario = "root";
    $contraseña = "";
    $baseDatos = "peis";
    
    $conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);
    
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }
    
    // Obtener datos del formulario
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

        if (move_uploaded_file($archivoTmp, $archivoPath)) {
            $archivoPath = $conexion->real_escape_string($archivoPath);
        } else {
            $archivoPath = null;
            echo "<script>alert('Error al subir el archivo.');</script>";
        }
    }

    // Insertar tarea en la base de datos
    $sql = "INSERT INTO tareas (id_curso, titulo, descripcion, fecha_limite, archivo_tarea) VALUES ('$materia', '$titulo', '$descripcion', '$fechaEntrega', '$archivoPath')";
    
    if ($conexion->query($sql) === TRUE) {
        $tarea_id = $conexion->insert_id; // Obtener el ID de la tarea recién insertada

        // Verificar si hay datos de rúbrica y mostrarlos
        if (!empty($_POST['criterios']) && !empty($_POST['puntosCubrir']) && !empty($_POST['puntos'])) {
            echo "<pre>";
            print_r($_POST['criterios']);
            print_r($_POST['puntosCubrir']);
            print_r($_POST['puntos']);
            echo "</pre>";

            for ($i = 0; $i < count($_POST['criterios']); $i++) {
                $criterio = $conexion->real_escape_string($_POST['criterios'][$i]);
                $descripcion = $conexion->real_escape_string($_POST['puntosCubrir'][$i]);
                $puntaje_maximo = (int)$_POST['puntos'][$i];

                // Insertar cada criterio en la base de datos
                $sqlRubrica = "INSERT INTO rubrica (id_tarea, criterio, descripcion, puntaje_maximo) VALUES ('$tarea_id', '$criterio', '$descripcion', '$puntaje_maximo')";
                
                if ($conexion->query($sqlRubrica) === TRUE) {
                    echo "Rúbrica insertada exitosamente para el criterio: $criterio<br>";
                } else {
                    echo "Error al insertar rúbrica: " . $conexion->error . "<br>";
                }
            }
        } else {
            echo "No se recibieron datos de rúbrica.<br>";
        }

        echo "<script>alert('Tarea y rúbrica asignadas con éxito.'); window.location.href = 'gestionTareasProfesor.php';</script>";
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
    <style>
        /* Estilos CSS */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        header nav {
            background-color: #ff9900;
            padding: 10px;
        }

        header nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            margin: 0;
            padding: 0;
        }

        header nav ul li {
            margin: 0 15px;
        }

        header nav ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }

        main {
            max-width: 800px;
            margin: 30px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h1 {
            color: #333;
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 20px;
            color: #ff9900;
            border-bottom: 2px solid #ff9900;
            padding-bottom: 8px;
            margin-top: 20px;
        }

        form {
            display: grid;
            gap: 10px;
            margin-top: 20px;
        }

        form label {
            font-weight: bold;
            color: #333;
        }

        form input, form select, form textarea {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            outline: none;
        }

        form button {
            background-color: #ff9900;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #e68a00;
        }

        /* Modal styling */
        #successModal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }

        #successModal button {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #ff9900;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        #successModal button:hover {
            background-color: #e68a00;
        }

        /* Button styling for "Mostrar Tareas Asignadas" */
        .show-tasks-button {
            display: block;
            background-color: #ff9900;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 4px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
            margin-top: 20px;
        }

        .show-tasks-button:hover {
            background-color: #e68a00;
        }

        footer {
            text-align: center;
            padding: 15px;
            background-color: #333;
            color: #fff;
            margin-top: 20px;
            border-top: 3px solid #ff9900;
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
        <form id="asignar-tarea" action="asignarTarea.php" method="POST" enctype="multipart/form-data">
            <label for="materia">Materia:</label>
            <select id="materia" name="materia">
                <?php include 'obtenerMaterias.php'; ?>
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
                        <td><input type="text" name="criterios[]" placeholder="Criterio"></td>
                        <td><input type="text" name="puntosCubrir[]" placeholder="Descripción del criterio"></td>
                        <td><input type="number" name="puntos[]" value="0" min="0"></td>
                    </tr>
                </tbody>
            </table>

            <button type="button" onclick="agregarFila()">Agregar Criterio</button>
            <button type="button" onclick="quitarFila()">Quitar Criterio</button>

            <button type="submit">Asignar Tarea</button>
        </form>
    </section>

    <a href="listarTareas.php" class="show-tasks-button">Mostrar Tareas Asignadas</a>
</main>

<footer>
    <p>© 2024 Plataforma de Educación</p>
</footer>

<script>
    function agregarFila() {
        const tableBody = document.querySelector("#rubricaTable tbody");
        const newRow = document.createElement("tr");
        newRow.innerHTML = `
            <td><input type="text" name="criterios[]" placeholder="Criterio"></td>
            <td><input type="text" name="puntosCubrir[]" placeholder="Descripción del criterio"></td>
            <td><input type="number" name="puntos[]" value="0" min="0"></td>
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
