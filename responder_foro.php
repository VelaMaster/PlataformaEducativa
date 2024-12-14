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

// Obtener las rúbricas asociadas al foro
$sql_rubricas = "SELECT criterio, descripcion, puntos FROM rubricasforo WHERE id_foro = ?";
$stmt_rubricas = $conexion->prepare($sql_rubricas);
$stmt_rubricas->bind_param("i", $id_foro);
$stmt_rubricas->execute();
$resultado_rubricas = $stmt_rubricas->get_result();
$rubricas = $resultado_rubricas->fetch_all(MYSQLI_ASSOC);

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
    }
}

// Obtener respuestas del foro
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'ASC';
$orden = ($orden === 'DESC') ? 'DESC' : 'ASC';

$sql_respuestas = "SELECT respuestas.id, respuestas.id_usuario, respuestas.contenido, respuestas.fecha_creacion, respuestas.respuesta_padre, alumnos.nombre AS autor 
                   FROM respuestas
                   JOIN alumnos ON respuestas.id_usuario = alumnos.num_control
                   WHERE respuestas.id_tema = ?
                   ORDER BY respuestas.respuesta_padre ASC, respuestas.fecha_creacion $orden";
$stmt_respuestas = $conexion->prepare($sql_respuestas);
$stmt_respuestas->bind_param("i", $id_foro);
$stmt_respuestas->execute();
$resultado_respuestas = $stmt_respuestas->get_result();

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

            if ($respuesta['id_usuario'] == $num_control) {
                echo "<form method='POST' class='form-eliminar'>";
                echo "<input type='hidden' name='eliminar_respuesta' value='" . $respuesta['id'] . "'>";
                echo "<button type='submit' class='btn-eliminar'>Eliminar</button>";
                echo "</form>";
            }

            echo "<button class='btn-responder' onclick='mostrarFormularioRespuesta(" . $respuesta['id'] . ")'>Responder</button>";
            echo "<form id='form-respuesta-" . $respuesta['id'] . "' class='form-respuesta' style='display: none;' method='POST'>";
            echo "<textarea name='respuesta' placeholder='Escribe tu respuesta...' class='textarea-respuesta'></textarea>";
            echo "<input type='hidden' name='respuesta_padre' value='" . $respuesta['id'] . "'>";
            echo "<button type='submit' class='btn-publicar'>Publicar Respuesta</button>";
            echo "</form>";

            echo "<button class='btn-toggle' onclick='toggleSubRespuestas(" . $respuesta['id'] . ")'>Ver/Ocultar Respuestas</button>";
            echo "<div id='subrespuestas-" . $respuesta['id'] . "' style='display: none;'>";
            mostrarRespuestas($respuestas, $respuesta['id']);
            echo "</div>";

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
    <link rel="stylesheet" href="css/responderForo.css?v=<?php echo time(); ?>">
    <script>
        function toggleRespuestas() {
            const contenedor = document.getElementById('respuestas-contenedor');
            contenedor.style.display = contenedor.style.display === 'none' ? 'block' : 'none';
            const boton = document.getElementById('toggle-respuestas-boton');
            boton.textContent = contenedor.style.display === 'none' ? 'Ver Respuestas' : 'Ocultar Respuestas';
        }

        function mostrarFormularioRespuesta(idRespuesta) {
            const formulario = document.getElementById(`form-respuesta-${idRespuesta}`);
            formulario.style.display = formulario.style.display === 'none' ? 'block' : 'none';
        }

        function toggleSubRespuestas(idRespuesta) {
            const subRespuestas = document.getElementById(`subrespuestas-${idRespuesta}`);
            subRespuestas.style.display = subRespuestas.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <div class="navbar">
        <h1><?php echo htmlspecialchars($foro['nombre']); ?></h1>
    </div>

    <div class="titulo-seccion"><?php echo htmlspecialchars($foro['descripcion']); ?></div>

    <div class="rubricas">
        <h3>Rúbricas del Foro</h3>
        <table class="tabla-rubricas">
            <thead>
                <tr>
                    <th>Criterio</th>
                    <th>Descripción</th>
                    <th>Puntaje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rubricas as $rubrica): ?>
                <tr>
                    <td><?php echo htmlspecialchars($rubrica['criterio']); ?></td>
                    <td><?php echo htmlspecialchars($rubrica['descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($rubrica['puntos']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="form-options">
        <form method="GET" action="responder_foro.php">
            <input type="hidden" name="id_foro" value="<?php echo $id_foro; ?>">
            <select name="orden" onchange="this.form.submit()">
                <option value="ASC" <?php echo ($orden === 'ASC') ? 'selected' : ''; ?>>Más antiguas primero</option>
                <option value="DESC" <?php echo ($orden === 'DESC') ? 'selected' : ''; ?>>Más recientes primero</option>
            </select>
        </form>
        <h3>Respuestas</h3>
        <button id="toggle-respuestas-boton" class="btn-ver" onclick="toggleRespuestas()">Ver Respuestas</button>
    </div>

    <div id="respuestas-contenedor" class="respuestas-contenedor">
        <?php mostrarRespuestas($respuestas); ?>
    </div>

    <div class="titulo-seccion">Agregar una respuesta</div>
    <?php if (isset($error)) echo "<p style='color:red; text-align: center;'>$error</p>"; ?>
    <form method="POST" class="form-container">
        <textarea name="respuesta" placeholder="Escribe tu respuesta..." class="textarea-respuesta"></textarea>
        <button type="submit" class="btn-publicar">Publicar</button>
        <a href="forosAlumno.php" class="btn-regresar">Regresar</a>
    </form>
</body>
</html>
