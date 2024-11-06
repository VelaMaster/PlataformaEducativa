<?php
// verTarea.php

// Conexión a la base de datos
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";

$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener el ID de la tarea
$id_tarea = $_GET['id'];

// Función para obtener el nombre de la materia
function obtenerNombreMateria($id_curso, $conexion) {
    $consulta = "SELECT nombre_curso FROM cursos WHERE id_curso = $id_curso";
    $resultado = $conexion->query($consulta);
    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        return $fila['nombre_curso'];
    } else {
        return "Desconocido";
    }
}

// Consultar los detalles de la tarea
$sql = "SELECT * FROM tareas WHERE id_tarea = $id_tarea";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    $tarea = $resultado->fetch_assoc();
    $nombre_materia = obtenerNombreMateria($tarea['id_curso'], $conexion);
    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Detalles de la Tarea</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f6f9;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .card {
                background-color: #ffffff;
                max-width: 600px;
                width: 100%;
                padding: 30px;
                border-radius: 12px;
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
                text-align: center;
            }
            .card h2 {
                color: #ff9900;
                font-size: 28px;
                margin-bottom: 20px;
                border-bottom: 2px solid #ff9900;
                padding-bottom: 10px;
            }
            .detail {
                margin-bottom: 15px;
                padding: 12px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                display: flex;
                justify-content: space-between;
                align-items: center;
                background-color: #f9f9f9;
            }
            .detail label {
                font-weight: bold;
                color: #ff9900;
                margin-right: 10px;
            }
            .detail p {
                color: #333;
                margin: 0;
                font-size: 16px;
                text-align: left;
                padding: 8px;
                background-color: #ffffff;
                border-radius: 5px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
            }
            .file-preview {
                display: flex;
                align-items: center;
                background-color: #f1f1f1;
                border: 1px solid #ddd;
                padding: 10px;
                border-radius: 8px;
                margin-top: 15px;
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
            .back-button-container {
                text-align: center;
                margin-top: 25px;
            }
            .back-button {
                background-color: #ff9900;
                color: #fff;
                padding: 12px 24px;
                border: none;
                border-radius: 6px;
                font-weight: bold;
                font-size: 16px;
                cursor: pointer;
                text-decoration: none;
                transition: background-color 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            }
            .back-button:hover {
                background-color: #e68a00;
            }
        </style>
    </head>
    <body>
        <div class="card">
            <h2>Detalles de la Tarea</h2>
            <div class="detail">
                <label>Materia:</label>
                <p><?php echo htmlspecialchars($nombre_materia); ?></p>
            </div>
            <div class="detail">
                <label>Título:</label>
                <p><?php echo htmlspecialchars($tarea['titulo']); ?></p>
            </div>
            <div class="detail">
                <label>Descripción:</label>
                <p><?php echo htmlspecialchars($tarea['descripcion']); ?></p>
            </div>
            <div class="detail">
                <label>Fecha de Creación:</label>
                <p><?php echo htmlspecialchars($tarea['fecha_creacion']); ?></p>
            </div>
            <div class="detail">
                <label>Fecha de Entrega:</label>
                <p><?php echo htmlspecialchars($tarea['fecha_limite']); ?></p>
            </div>
            <?php if (!empty($tarea['archivo_tarea'])): ?>
                <div class="file-preview">
                    <?php
                    // Check if the file is an image
                    $file_path = htmlspecialchars($tarea['archivo_tarea']);
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
            <div class="back-button-container">
                <a href="listarTareas.php" class="back-button">Regresar a Tareas Asignadas</a>
            </div>
        </div>
    </body>
    </html>

    <?php
} else {
    echo "Tarea no encontrada";
}
$conexion->close();
?>
