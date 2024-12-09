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

// Obtener las respuestas del foro, incluyendo la calificación
$sql_respuestas = "SELECT respuestas.id, respuestas.contenido, respuestas.fecha_creacion, respuestas.calificacion, alumnos.nombre AS autor 
                   FROM respuestas
                   JOIN alumnos ON respuestas.id_usuario = alumnos.num_control
                   WHERE respuestas.id_tema = ?
                   ORDER BY respuestas.fecha_creacion ASC";

$stmt_respuestas = $conexion->prepare($sql_respuestas);
$stmt_respuestas->bind_param("i", $id_foro);
$stmt_respuestas->execute();
$resultado_respuestas = $stmt_respuestas->get_result();

// Procesar nueva respuesta si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respuesta'])) {
    $respuesta = $conexion->real_escape_string($_POST['respuesta']);

    if (!empty($respuesta)) {
        $sql_insertar = "INSERT INTO respuestas (id_tema, id_usuario, tipo_usuario, contenido, fecha_creacion) 
                         VALUES (?, ?, 'alumno', ?, NOW())";
        $stmt_insertar = $conexion->prepare($sql_insertar);
        $stmt_insertar->bind_param("iss", $id_foro, $num_control, $respuesta);
        $stmt_insertar->execute();
        header("Location: responder_foro.php?id_foro=$id_foro");
        exit();
    } else {
        $error = "La respuesta no puede estar vacía.";
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
            background-color: #e7d6bf;
            margin: 0;
            padding: 20px;
        }

        .foro-container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #FF7700;
            text-align: center;
        }

        p.descripcion {
            font-size: 1.1em;
            color: #555;
            text-align: justify;
        }

        .respuesta {
            margin-top: 15px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fdfdfd;
        }

        .respuesta .autor {
            font-weight: bold;
            color: #FF7700;
        }

        .respuesta .contenido {
            margin: 10px 0;
            font-size: 1em;
            color: #333;
        }

        .respuesta .calificacion {
            font-size: 0.9em;
            color: #006400;
            margin-top: 5px;
        }

        .respuesta .fecha {
            font-size: 0.85em;
            color: #666;
            text-align: right;
        }

        .form-container {
            margin-top: 30px;
        }

        .form-container textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }

        .form-container button {
            background-color: #FF7700;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-container button:hover {
            background-color: #FF5500;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="foro-container">
        <h2><?php echo htmlspecialchars($foro['nombre']); ?></h2>
        <p class="descripcion"><?php echo htmlspecialchars($foro['descripcion']); ?></p>

        <div class="respuestas">
            <h3>Respuestas</h3>
            <?php
            if ($resultado_respuestas->num_rows > 0) {
                while ($respuesta = $resultado_respuestas->fetch_assoc()) {
                    echo "<div class='respuesta'>";
                    echo "<p class='autor'>" . htmlspecialchars($respuesta['autor']) . "</p>";
                    echo "<p class='contenido'>" . htmlspecialchars($respuesta['contenido']) . "</p>";
                    if (!is_null($respuesta['calificacion'])) {
                        echo "<p class='calificacion'>Calificación: " . htmlspecialchars($respuesta['calificacion']) . "</p>";
                    }
                    echo "<p class='fecha'>" . htmlspecialchars($respuesta['fecha_creacion']) . "</p>";
                    // Botón para responder
                    echo "<a href='responder_comentario.php?id_respuesta=" . $respuesta['id'] . "'>Responder</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>No hay respuestas aún. ¡Sé el primero en comentar!</p>";
            }
            ?>
        </div>

        <div class="form-container">
            <h3>Agregar una respuesta</h3>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="POST">
                <textarea name="respuesta" placeholder="Escribe tu respuesta..."></textarea>
                <button type="submit">Publicar</button>
            </form>
        </div>
    </div>
</body>
</html>
