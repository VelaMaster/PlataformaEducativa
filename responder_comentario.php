<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$num_control = $_SESSION['usuario'];
$conexion = new mysqli("localhost", "root", "", "peis");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Validar los parámetros id_respuesta y id_tema
$id_respuesta = isset($_GET['id_respuesta']) ? intval($_GET['id_respuesta']) : 0;
$id_tema = isset($_GET['id_tema']) ? intval($_GET['id_tema']) : 0;

// Validar que ambos parámetros sean válidos
if ($id_respuesta <= 0 || $id_tema <= 0) {
    die("ID del tema o respuesta no especificado o inválido.");
}

// Obtener la información del comentario original
$sql_respuesta = "SELECT respuestas.contenido, respuestas.fecha_creacion, alumnos.nombre AS autor
                  FROM respuestas
                  JOIN alumnos ON respuestas.id_usuario = alumnos.num_control
                  WHERE respuestas.id = ?";
$stmt_respuesta = $conexion->prepare($sql_respuesta);
$stmt_respuesta->bind_param("i", $id_respuesta);
$stmt_respuesta->execute();
$resultado_respuesta = $stmt_respuesta->get_result();

if ($resultado_respuesta->num_rows === 0) {
    die("La respuesta original no existe.");
}

$comentario = $resultado_respuesta->fetch_assoc();

// Procesar la nueva respuesta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respuesta'])) {
    $respuesta = $conexion->real_escape_string($_POST['respuesta']);

    if (!empty($respuesta)) {
        $sql_insertar = "INSERT INTO respuestas (id_tema, id_usuario, tipo_usuario, contenido, fecha_creacion, id_respuesta_padre) 
                         VALUES (?, ?, 'alumno', ?, NOW(), ?)";
        $stmt_insertar = $conexion->prepare($sql_insertar);
        $stmt_insertar->bind_param("issi", $id_tema, $num_control, $respuesta, $id_respuesta);
        $stmt_insertar->execute();

        // Redirigir al foro después de guardar la respuesta
        header("Location: responder_foro.php?id_foro=$id_tema");
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
    <title>Responder Comentario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e7d6bf;
            margin: 0;
            padding: 20px;
        }

        .comentario-container {
            max-width: 700px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .comentario-container h2 {
            color: #FF7700;
            text-align: center;
        }

        .respuesta-principal {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fdfdfd;
        }

        .respuesta-principal .autor {
            font-weight: bold;
            color: #FF7700;
        }

        .respuesta-principal .contenido {
            margin: 10px 0;
            font-size: 1em;
            color: #333;
        }

        .respuesta-principal .fecha {
            font-size: 0.85em;
            color: #666;
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
    <div class="comentario-container">
        <h2>Responder Comentario</h2>

        <div class="respuesta-principal">
            <p class="autor"><?php echo htmlspecialchars($comentario['autor']); ?></p>
            <p class="contenido"><?php echo htmlspecialchars($comentario['contenido']); ?></p>
            <p class="fecha"><?php echo htmlspecialchars($comentario['fecha_creacion']); ?></p>
        </div>

        <div class="form-container">
            <h3>Escribe tu respuesta</h3>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="POST" action="responder_comentario.php?id_respuesta=<?php echo $id_respuesta; ?>&id_tema=<?php echo $id_tema; ?>">
                <textarea name="respuesta" placeholder="Escribe tu respuesta..."></textarea>
                <button type="submit">Publicar</button>
            </form>
        </div>
    </div>
</body>
</html>
