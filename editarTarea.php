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
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        .form-container h2 {
            color: #ff9900;
            font-size: 24px;
            margin-bottom: 20px;
            border-bottom: 2px solid #ff9900;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 15px;
            padding: 12px;
            border-radius: 8px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .form-group label {
            font-weight: bold;
            color: #ff9900;
            margin-right: 10px;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .file-preview {
            display: flex;
            align-items: center;
            background-color: #f1f1f1;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
            justify-content: start;
        }
        .file-preview img {
            width: 36px;
            height: 36px;
            margin-right: 10px;
            border-radius: 4px;
            object-fit: cover;
        }
        .file-preview a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .file-preview a:hover {
            text-decoration: underline;
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
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
        .form-container button:hover {
            background-color: #e68a00;
        }
        .back-button {
            background-color: #555;
            color: #fff;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        .back-button:hover {
            background-color: #333;
        }
    </style>
</head>
<body>

<div class="form-container">
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

        <?php if (!empty($fila['archivo_tarea'])): ?>
            <div class="file-preview">
                <?php
                $file_path = htmlspecialchars($fila['archivo_tarea']);
                $file_extension = pathinfo($file_path, PATHINFO_EXTENSION);
                $image_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array(strtolower($file_extension), $image_extensions)) {
                    echo "<img src='$file_path' alt='Archivo'>";
                } else {
                    echo "<img src='file-icon.png' alt='Archivo'>";
                }
                ?>
                <a href="<?php echo $file_path; ?>" target="_blank"><?php echo basename($file_path); ?></a>
            </div>
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
