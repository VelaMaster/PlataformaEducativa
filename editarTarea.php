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

$id_tarea = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha_limite = $_POST['fecha_limite'];
    $archivoPath = null;

    // Check if a file is uploaded
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == UPLOAD_ERR_OK) {
        $archivoNombre = $_FILES['archivo']['name'];
        $archivoTmp = $_FILES['archivo']['tmp_name'];
        $archivoPath = "uploads/" . basename($archivoNombre);

        // Move file to the uploads directory
        if (move_uploaded_file($archivoTmp, $archivoPath)) {
            $archivoPath = $conexion->real_escape_string($archivoPath);
        } else {
            $archivoPath = null;
        }
    }

    // Update the task with or without a new file
    if ($archivoPath) {
        $sql = "UPDATE tareas SET titulo = ?, descripcion = ?, fecha_limite = ?, archivo_tarea = ? WHERE id_tarea = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssssi", $titulo, $descripcion, $fecha_limite, $archivoPath, $id_tarea);
    } else {
        $sql = "UPDATE tareas SET titulo = ?, descripcion = ?, fecha_limite = ? WHERE id_tarea = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssi", $titulo, $descripcion, $fecha_limite, $id_tarea);
    }

    if ($stmt->execute()) {
        header("Location: listarTareas.php");
        exit();
    } else {
        echo "Error al actualizar: " . $stmt->error;
    }
    $stmt->close();
}

$sql = "SELECT * FROM tareas WHERE id_tarea = $id_tarea";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    $fila = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Tarea</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        .form-container h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .form-container label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
            text-align: left;
        }
        .form-container input,
        .form-container textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .form-container button {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background-color: #ff9900;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .form-container button:hover {
            background-color: #e68a00;
        }
        .form-container .back-button {
            background-color: #555;
            margin-top: 10px;
        }
        .form-container .back-button:hover {
            background-color: #333;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Editar Tarea</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" value="<?php echo $fila['titulo']; ?>" required>

        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion" name="descripcion" rows="4" required><?php echo $fila['descripcion']; ?></textarea>

        <label for="fecha_limite">Fecha de Entrega:</label>
        <input type="date" id="fecha_limite" name="fecha_limite" value="<?php echo $fila['fecha_limite']; ?>" required>

        <label for="archivo">Archivo (opcional):</label>
        <input type="file" id="archivo" name="archivo">

        <?php if (!empty($fila['archivo_tarea'])): ?>
            <p>Archivo actual: <a href="<?php echo $fila['archivo_tarea']; ?>" target="_blank">Ver archivo</a></p>
        <?php endif; ?>

        <button type="submit">Actualizar Tarea</button>
    </form>
    <button class="back-button" onclick="window.location.href='listarTareas.php'">Regresar a Tareas Asignadas</button>
</div>

</body>
</html>

<?php
} else {
    echo "Tarea no encontrada";
}
$conexion->close();
?>
