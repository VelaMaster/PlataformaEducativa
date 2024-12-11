<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$num_control = $_SESSION['usuario'];

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "peis");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Verificar si se ha proporcionado el id_foro
if (!isset($_GET['id_foro'])) {
    echo "ID del foro no especificado.";
    exit();
}

$id_foro = intval($_GET['id_foro']);

// Obtener la información del foro
$sql_foro = "SELECT nombre, descripcion FROM foros WHERE id = ?";
$stmt_foro = $conexion->prepare($sql_foro);
$stmt_foro->bind_param("i", $id_foro);
$stmt_foro->execute();
$resultado_foro = $stmt_foro->get_result();

if ($resultado_foro->num_rows === 0) {
    echo "El foro no existe.";
    exit();
}

$foro = $resultado_foro->fetch_assoc();

// Procesar nueva respuesta si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['respuesta'])) {
        $respuesta = $conexion->real_escape_string($_POST['respuesta']);
        $respuesta_padre = isset($_POST['respuesta_padre']) ? intval($_POST['respuesta_padre']) : NULL;

        if (!empty($respuesta)) {
            $sql_insertar = "INSERT INTO respuestas (id_tema, id_usuario, tipo_usuario, contenido, fecha_creacion, respuesta_padre) 
                             VALUES (?, ?, 'alumno', ?, NOW(), ?)";
            $stmt_insertar = $conexion->prepare($sql_insertar);
            $stmt_insertar->bind_param("issi", $id_foro, $num_control, $respuesta, $respuesta_padre);
            $stmt_insertar->execute();
            header("Location: responder_foro.php?id_foro=$id_foro");
            exit();
        } else {
            $error = "La respuesta no puede estar vacía.";
        }
    } elseif (isset($_POST['eliminar_respuesta'])) {
        $id_respuesta = intval($_POST['eliminar_respuesta']);
        $sql_eliminar = "DELETE FROM respuestas WHERE id = ?";
        $stmt_eliminar = $conexion->prepare($sql_eliminar);
        $stmt_eliminar->bind_param("i", $id_respuesta);
        $stmt_eliminar->execute();
        header("Location: responder_foro.php?id_foro=$id_foro");
        exit();
    }
}

// Obtener el orden seleccionado o usar un valor predeterminado
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'ASC';

// Validar el valor del orden
$orden = ($orden === 'DESC') ? 'DESC' : 'ASC';

// Obtener las respuestas del foro en formato jerárquico
$sql_respuestas = "SELECT respuestas.id, respuestas.id_usuario, respuestas.contenido, respuestas.fecha_creacion, respuestas.respuesta_padre, alumnos.nombre AS autor 
                   FROM respuestas
                   JOIN alumnos ON respuestas.id_usuario = alumnos.num_control
                   WHERE respuestas.id_tema = ?
                   ORDER BY respuestas.respuesta_padre ASC, respuestas.fecha_creacion $orden";
$stmt_respuestas = $conexion->prepare($sql_respuestas);
$stmt_respuestas->bind_param("i", $id_foro);
$stmt_respuestas->execute();
$resultado_respuestas = $stmt_respuestas->get_result();
// Organizar respuestas en un array jerárquico
$respuestas = [];
while ($fila = $resultado_respuestas->fetch_assoc()) {
    $respuestas[$fila['respuesta_padre']][] = $fila;
}

function mostrarRespuestas($respuestas, $respuesta_padre = NULL) {
    global $num_control;

    if (isset($respuestas[$respuesta_padre])) {
        foreach ($respuestas[$respuesta_padre] as $respuesta) {
            echo "<div class='respuesta'>";
            echo "<p><strong>" . htmlspecialchars($respuesta['autor']) . "</strong> - " . htmlspecialchars($respuesta['fecha_creacion']) . "</p>";
            echo "<p>" . htmlspecialchars($respuesta['contenido']) . "</p>";

            // Formulario para eliminar
            if ($respuesta['id_usuario'] == $num_control) {
                echo "<form method='POST' class='form-eliminar'>";
                echo "<input type='hidden' name='eliminar_respuesta' value='" . $respuesta['id'] . "'>";
                echo "<button type='submit'>Eliminar</button>";
                echo "</form>";
            }

            // Formulario para responder
            echo "<button onclick='mostrarFormularioRespuesta(" . $respuesta['id'] . ")'>Responder</button>";
            echo "<form id='form-respuesta-" . $respuesta['id'] . "' class='form-respuesta' style='display: none;' method='POST'>";
            echo "<textarea name='respuesta' placeholder='Escribe tu respuesta...'></textarea>";
            echo "<input type='hidden' name='respuesta_padre' value='" . $respuesta['id'] . "'>";
            echo "<button type='submit'>Publicar Respuesta</button>";
            echo "</form>";

            // Mostrar respuestas hijas
            mostrarRespuestas($respuestas, $respuesta['id']);
            echo "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($foro['nombre']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }
        h2 {
            color: #333;
        }
        .respuesta {
            margin-left: 20px;
            border-left: 2px solid #ccc;
            padding-left: 10px;
            margin-top: 10px;
        }
        .form-eliminar {
            display: inline;
        }
        textarea {
            width: 100%;
            height: 60px;
            margin-bottom: 10px;
        }
        button {
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .form-respuesta {
            margin-top: 10px;
        }
    </style>
    <script>
        function mostrarFormularioRespuesta(idRespuesta) {
            const formulario = document.getElementById(`form-respuesta-${idRespuesta}`);
            formulario.style.display = formulario.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <h2><?php echo htmlspecialchars($foro['nombre']); ?></h2>
    <p><?php echo htmlspecialchars($foro['descripcion']); ?></p>

    <h3>Ordenar respuestas</h3>
<form method="GET" action="responder_foro.php">
    <input type="hidden" name="id_foro" value="<?php echo $id_foro; ?>">
    <select name="orden" onchange="this.form.submit()">
        <option value="ASC" <?php echo ($orden === 'ASC') ? 'selected' : ''; ?>>Más antiguas primero</option>
        <option value="DESC" <?php echo ($orden === 'DESC') ? 'selected' : ''; ?>>Más recientes primero</option>
    </select>
</form>

    <h3>Respuestas</h3>
    <div>
        <?php mostrarRespuestas($respuestas); ?>
    </div>

    <h3>Agregar una respuesta</h3>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <textarea name="respuesta" placeholder="Escribe tu respuesta..."></textarea>
        <button type="submit">Publicar</button>
        <a href="forosAlumno.php">Regresar</a>
    </form>

    
</body>
</html>
