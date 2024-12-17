<?php
// editarTarea.php

$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";

$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$id = $_GET['id'];  // Se obtiene el ID de la tarea desde la URL.

// Actualizar tarea principal
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['actualizar_tarea'])) {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha_limite = $_POST['fecha_limite'];
    $archivoPath = null;

    // Verificar si se subió un archivo
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == UPLOAD_ERR_OK) {
        $archivoNombre = $_FILES['archivo']['name'];
        $archivoTmp = $_FILES['archivo']['tmp_name'];
        $archivoPath = "uploads/" . basename($archivoNombre);

        if (move_uploaded_file($archivoTmp, $archivoPath)) {
            $archivoPath = $conexion->real_escape_string($archivoPath);
        } else {
            $archivoPath = null;
        }
    }

    // Actualización de la tarea
    if ($archivoPath) {
        $sql = "UPDATE tareas SET titulo = ?, descripcion = ?, fecha_limite = ?, archivo_tarea = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssssi", $titulo, $descripcion, $fecha_limite, $archivoPath, $id);
    } else {
        $sql = "UPDATE tareas SET titulo = ?, descripcion = ?, fecha_limite = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssi", $titulo, $descripcion, $fecha_limite, $id);
    }

    if ($stmt->execute()) {
        header("Location: listarTareas.php");
        exit();
    } else {
        echo "Error al actualizar: " . $stmt->error;
    }
    $stmt->close();
}

// Manejar rúbricas: agregar/eliminar
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion_rubrica'])) {
    $accion = $_POST['accion_rubrica'];

    // Acción de agregar una nueva rúbrica
    if ($accion === 'agregar') {
        $criterio = $_POST['criterio'];
        $descripcion = $_POST['descripcion_rubrica'];

        // Calcular los puntos por rúbrica
        $sql = "SELECT COUNT(*) AS total FROM rubricas WHERE id_tarea = $id"; // Se usa id_tarea
        $resultado = $conexion->query($sql);
        if ($resultado) {
            $total_rubricas = $resultado->fetch_assoc()['total'] + 1;
            $puntos = round(100 / $total_rubricas, 2);

            // Insertar nueva rúbrica
            $sql_insertar = "INSERT INTO rubricas (id_tarea, criterio, descripcion, puntos) VALUES (?, ?, ?, ?)";
            $stmt_insertar = $conexion->prepare($sql_insertar);
            $stmt_insertar->bind_param("issi", $id, $criterio, $descripcion, $puntos);
            $stmt_insertar->execute();
            $stmt_insertar->close();
        } else {
            echo "Error al consultar el total de rúbricas: " . $conexion->error;
        }
    } elseif ($accion === 'eliminar') {
        // Acción de eliminar una rúbrica
        $id_rubrica = $_POST['id_rubrica'];
        $sql_eliminar = "DELETE FROM rubricas WHERE id = ?";  // Usar 'id' en lugar de 'id_rubrica'
        $stmt_eliminar = $conexion->prepare($sql_eliminar);
        $stmt_eliminar->bind_param("i", $id_rubrica);
        $stmt_eliminar->execute();

        // Recalcular los puntos restantes
        $sql = "SELECT COUNT(*) AS total FROM rubricas WHERE id_tarea = $id"; // Se usa id_tarea
        $resultado = $conexion->query($sql);
        $total_rubricas = $resultado->fetch_assoc()['total'];
        if ($total_rubricas > 0) {
            $puntos = round(100 / $total_rubricas, 2);
            $sql_actualizar = "UPDATE rubricas SET puntos = ? WHERE id_tarea = ?";
            $stmt_actualizar = $conexion->prepare($sql_actualizar);
            $stmt_actualizar->bind_param("ii", $puntos, $id);
            $stmt_actualizar->execute();
        }
    }
}

// Consultar datos de la tarea y sus rúbricas
$sql = "SELECT * FROM tareas WHERE id = $id";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    $fila = $resultado->fetch_assoc();
    $sql_rubricas = "SELECT * FROM rubricas WHERE id_tarea = $id"; // Se usa id_tarea
    $resultado_rubricas = $conexion->query($sql_rubricas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarea</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
        }
        .container {
            width: 100%;
            max-width: 800px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        h2, h3 {
            color: #ff9900;
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }
        .form-group input, .form-group textarea {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn {
            background-color: #ff9900;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #e68a00;
        }
        .btn-red {
            background-color: #ff4d4d;
            padding: 8px 15px;
            font-size: 14px;
            font-weight: bold;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-red:hover {
            background-color: #e60000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #ff9900;
            color: white;
        }
        table td {
            background-color: #fff;
        }
        .actions {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        input.error {
            border-color: red;
        }
        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }
        .button-container {
            display: flex;
            justify-content: space-between;
        }
    </style>
    <script>
        function validarCampos() {
            const criterio = document.getElementById('criterio');
            const descripcion = document.getElementById('descripcion_rubrica');
            const errorCriterio = document.getElementById('error-criterio');
            const errorDescripcion = document.getElementById('error-descripcion');

            let valido = true;

            if (/\d/.test(criterio.value)) {
                errorCriterio.textContent = "El criterio no puede contener números.";
                valido = false;
            } else {
                errorCriterio.textContent = "";
            }

            if (/\d/.test(descripcion.value)) {
                errorDescripcion.textContent = "La descripción no puede contener números.";
                valido = false;
            } else {
                errorDescripcion.textContent = "";
            }

            return valido;
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Editar Tarea</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo $fila['titulo']; ?>" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" required><?php echo $fila['descripcion']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="fecha_limite">Fecha de Entrega:</label>
                <input type="date" id="fecha_limite" name="fecha_limite" value="<?php echo $fila['fecha_limite']; ?>" required>
            </div>
            <div class="form-group">
                <label for="archivo">Archivo (opcional):</label>
                <input type="file" id="archivo" name="archivo">
            </div>
            <div class="button-container">
                <button type="submit" name="actualizar_tarea" class="btn">Actualizar Tarea</button>
                <a href="listarTareas.php" class="btn">Regresar a Tareas</a>
            </div>
        </form>
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
                <?php if ($resultado_rubricas->num_rows > 0): ?>
                    <?php while ($rubrica = $resultado_rubricas->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($rubrica['criterio']); ?></td>
                            <td><?php echo htmlspecialchars($rubrica['descripcion']); ?></td>
                            <td><?php echo htmlspecialchars($rubrica['puntos']); ?></td>
                            <td>
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="id_rubrica" value="<?php echo $rubrica['id']; ?>"> <!-- Usamos 'id' en lugar de 'id_rubrica' -->
                                    <button type="submit" name="accion_rubrica" value="eliminar" class="btn-red">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No hay rúbricas asignadas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <form method="POST" onsubmit="return validarCampos()" class="actions">
            <input type="hidden" name="accion_rubrica" value="agregar">
            <div>
                <input type="text" id="criterio" name="criterio" placeholder="Criterio" required>
                <span id="error-criterio" class="error-message"></span>
            </div>
            <div>
                <input type="text" id="descripcion_rubrica" name="descripcion_rubrica" placeholder="Descripción" required>
                <span id="error-descripcion" class="error-message"></span>
            </div>
            <button type="submit" class="btn">Agregar Rúbrica</button>
        </form>
    </div>
</body>
</html>

<?php
} else {
    echo "Tarea no encontrada.";
}
$conexion->close();
?>
