<?php
require_once 'TaskEditor.php';

// Configuración de la base de datos
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";

// Instanciar la clase TaskEditor
$editor = new TaskEditor($servidor, $usuario, $contraseña, $baseDatos);

// Obtener el ID de la tarea de la URL y sanitizarlo
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("ID de tarea inválido.");
}

// Manejar actualizaciones de la tarea y rúbricas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Actualizar la tarea
    if (isset($_POST['actualizar_tarea'])) {
        // Obtener y sanitizar los datos del formulario
        $titulo = trim($_POST['titulo']);
        $descripcion = trim($_POST['descripcion']);
        $fecha_limite = $_POST['fecha_limite'];

        // Manejar la subida de archivo si existe
        $archivo = null;
        if (isset($_FILES['archivo_tarea']) && $_FILES['archivo_tarea']['error'] != UPLOAD_ERR_NO_FILE) {
            $resultadoUpload = $editor->handleFileUpload($_FILES['archivo_tarea']);
            if (strpos($resultadoUpload, 'Error') === 0 || strpos($resultadoUpload, 'Tipo de archivo') === 0 || strpos($resultadoUpload, 'El archivo') === 0 || strpos($resultadoUpload, 'Error desconocido') === 0) {
                // Si el resultado comienza con "Error" o cualquier mensaje de error, mostrarlo
                echo "<p style='color:red;'>$resultadoUpload</p>";
            } else {
                // Subida exitosa, establecer el camino del archivo
                $archivo = $resultadoUpload;
            }
        }

        // Actualizar la tarea
        $actualizado = $editor->updateTask($id, $titulo, $descripcion, $fecha_limite, $archivo);
        if ($actualizado) {
            echo "<p style='color:green;'>Tarea actualizada correctamente.</p>";
        } else {
            echo "<p style='color:red;'>Error al actualizar la tarea.</p>";
        }
    }

    // Agregar una nueva rúbrica
    if (isset($_POST['accion_rubrica']) && $_POST['accion_rubrica'] === 'agregar') {
        $criterio = trim($_POST['criterio']);
        $descripcion_rubrica = trim($_POST['descripcion_rubrica']);
        $nuevoPuntos = intval($_POST['puntos']);

        if ($nuevoPuntos <= 0) {
            echo "<p style='color:red;'>Los puntos deben ser un número positivo.</p>";
        } else {
            $agregado = $editor->addRubric($id, $criterio, $descripcion_rubrica, $nuevoPuntos);
            if ($agregado) {
                echo "<p style='color:green;'>Rúbrica agregada correctamente.</p>";
            } else {
                echo "<p style='color:red;'>No puedes agregar esta rúbrica. La suma total de los puntos excede 100.</p>";
            }
        }
    }

    // Eliminar una rúbrica
    if (isset($_POST['accion_rubrica']) && $_POST['accion_rubrica'] === 'eliminar') {
        $id_rubrica = intval($_POST['id_rubrica']);
        $eliminado = $editor->deleteRubric($id_rubrica);
        if ($eliminado) {
            echo "<p style='color:green;'>Rúbrica eliminada correctamente.</p>";
        } else {
            echo "<p style='color:red;'>Error al eliminar la rúbrica.</p>";
        }
    }
}

// Obtener los datos de la tarea y las rúbricas
$task = $editor->getTask($id);
$rubrics = $editor->getRubrics($id);
$totalPuntos = $editor->getTotalRubricPoints($id);

// Cerrar la conexión
$editor->closeConnection();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarea</title>
    <link rel="stylesheet" href="css/editarTarea.css?v=<?php echo time(); ?>">
    <script>
        // Actualizar el total de puntos en tiempo real
        function actualizarTotalPuntos() {
            const puntosInputs = document.querySelectorAll('input[name="puntos[]"]');
            let totalPuntos = 0;

            // Calcular la suma total de puntos
            puntosInputs.forEach(input => {
                totalPuntos += parseFloat(input.value) || 0;
            });

            // Mostrar el total actualizado
            document.getElementById('totalPuntos').textContent = totalPuntos;

            // Verificar si la suma total excede 100
            if (totalPuntos > 100) {
                document.getElementById('errorPuntos').textContent = 'La suma total de los puntos no puede exceder 100.';
                return false;
            } else {
                document.getElementById('errorPuntos').textContent = '';
                return true;
            }
        }

        // Validar los puntos antes de agregar una nueva rúbrica
        function validarNuevaRubrica() {
            const nuevoPuntos = parseFloat(document.getElementById('nuevoPuntos').value) || 0;
            const totalPuntosActual = parseFloat(document.getElementById('totalPuntos').textContent) || 0;

            if ((totalPuntosActual + nuevoPuntos) > 100) {
                alert('No puedes agregar esta rúbrica. La suma total de los puntos excede 100.');
                return false;
            }
            return true;
        }

        // Escuchar cambios en los inputs de puntos
        document.addEventListener('input', function(event) {
            if (event.target.name === 'puntos[]') {
                actualizarTotalPuntos();
            }
        });

        // Validar al cargar la página
        window.addEventListener('load', actualizarTotalPuntos);
    </script>
</head>
<body>
    <div class="container">
        <h2>Editar Tarea</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($task['titulo']); ?>" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" required><?php echo htmlspecialchars($task['descripcion']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="fecha_limite">Fecha de Entrega:</label>
                <input type="date" id="fecha_limite" name="fecha_limite" value="<?php echo htmlspecialchars($task['fecha_limite']); ?>" required>
            </div>
            <div class="form-group">
                <label for="archivo_tarea">Archivo:</label>
                <input type="file" id="archivo_tarea" name="archivo_tarea">
            </div>
            <div class="button-container">
                <button type="submit" name="actualizar_tarea" class="btn">Actualizar Tarea</button>
                <a href="listarTareas.php" class="btn">Regresar a Tareas</a>
            </div>
        </form>
    </div>

    <!-- Vista previa del archivo -->
    <div class="file-preview" id="file-preview">
        <?php if (!empty($task['archivo_tarea'])): ?>
            <?php $extension = pathinfo($task['archivo_tarea'], PATHINFO_EXTENSION); ?>
            <?php if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])): ?>
                <img src="<?php echo htmlspecialchars($task['archivo_tarea']); ?>" id="preview-image" alt="Vista previa del archivo">
            <?php elseif (strtolower($extension) === 'pdf'): ?>
                <iframe src="<?php echo htmlspecialchars($task['archivo_tarea']); ?>" id="preview-pdf"></iframe>
            <?php else: ?>
                <p>Vista previa no disponible para este tipo de archivo.</p>
            <?php endif; ?>
        <?php else: ?>
            <p>No hay archivo asignado actualmente.</p>
        <?php endif; ?>
    </div>

    <div class="container">
        <h3>Rúbricas Asociadas</h3>
        <table>
            <thead>
                <tr>
                    <th>Criterio</th>
                    <th>Descripción</th>
                    <th>Puntos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($rubrics)): ?>
                    <?php foreach ($rubrics as $rubrica): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($rubrica['criterio']); ?></td>
                            <td><?php echo htmlspecialchars($rubrica['descripcion']); ?></td>
                            <td><input type="number" name="puntos[]" value="<?php echo htmlspecialchars($rubrica['puntos']); ?>" readonly></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta rúbrica?');">
                                    <input type="hidden" name="id_rubrica" value="<?php echo $rubrica['id']; ?>">
                                    <input type="hidden" name="accion_rubrica" value="eliminar">
                                    <button type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No hay rúbricas asignadas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <p>Total de puntos actuales: <span id="totalPuntos"><?php echo htmlspecialchars($totalPuntos); ?></span>/100</p>
        <p id="errorPuntos" style="color: red;"></p>

        <form method="POST" onsubmit="return validarNuevaRubrica()">
            <input type="hidden" name="accion_rubrica" value="agregar">
            <div>
                <label for="criterio">Criterio:</label>
                <input type="text" id="criterio" name="criterio" required>
            </div>
            <div>
                <label for="descripcion_rubrica">Descripción:</label>
                <input type="text" id="descripcion_rubrica" name="descripcion_rubrica" required>
            </div>
            <div>
                <label for="nuevoPuntos">Puntos:</label>
                <input type="number" id="nuevoPuntos" name="puntos" min="1" required>
            </div>
            <button type="submit">Agregar Rúbrica</button>
        </form>
    </div>

    <!-- JavaScript para la vista previa -->
    <script>
        document.getElementById('archivo_tarea').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const previewContainer = document.getElementById('file-preview');
            previewContainer.innerHTML = ""; // Limpia la vista previa anterior

            if (file) {
                const fileReader = new FileReader();
                const fileType = file.type;

                fileReader.onload = function(e) {
                    if (fileType.includes("image")) {
                        const img = document.createElement("img");
                        img.src = e.target.result;
                        img.style.borderRadius = "8px";
                        img.style.maxWidth = "100%";
                        previewContainer.appendChild(img);
                    } else if (fileType === "application/pdf") {
                        const iframe = document.createElement("iframe");
                        iframe.src = e.target.result;
                        iframe.style.width = "100%";
                        iframe.style.height = "500px";
                        previewContainer.appendChild(iframe);
                    } else {
                        previewContainer.innerHTML = "<p>Vista previa no disponible. Archivo: " + file.name + "</p>";
                    }
                };

                fileReader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
