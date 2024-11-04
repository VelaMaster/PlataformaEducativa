<?php
// verTarea.php

// Database connection
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";

$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Get the task ID
$id_tarea = $_GET['id'];

// Function to get the course name
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

// Query task details
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
                background-color: #fff;
                max-width: 500px;
                padding: 20px;
                margin: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }
            .card h2 {
                color: #333;
                font-size: 24px;
                margin-bottom: 20px;
                border-bottom: 2px solid #ff9900;
                padding-bottom: 10px;
            }
            .detail {
                margin-bottom: 15px;
            }
            .detail label {
                font-weight: bold;
                color: #ff9900;
                display: inline-block;
                width: 150px;
            }
            .detail p {
                display: inline;
                color: #333;
                font-size: 16px;
                margin: 0;
            }
            .back-button-container {
                text-align: center;
                margin-top: 20px;
            }
            .back-button {
                background-color: #ff9900;
                color: #fff;
                padding: 10px 20px;
                border: none;
                border-radius: 4px;
                font-weight: bold;
                cursor: pointer;
                text-decoration: none;
                transition: background-color 0.3s;
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
                <p><?php echo $nombre_materia; ?></p>
            </div>
            <div class="detail">
                <label>Título:</label>
                <p><?php echo $tarea['titulo']; ?></p>
            </div>
            <div class="detail">
                <label>Descripción:</label>
                <p><?php echo $tarea['descripcion']; ?></p>
            </div>
            <div class="detail">
                <label>Fecha de Creación:</label>
                <p><?php echo $tarea['fecha_creacion']; ?></p>
            </div>
            <div class="detail">
                <label>Fecha de Entrega:</label>
                <p><?php echo $tarea['fecha_limite']; ?></p>
            </div>
            <?php if (!empty($tarea['archivo_tarea'])): ?>
                <div class="detail">
                    <label>Archivo:</label>
                    <p><a href="<?php echo $tarea['archivo_tarea']; ?>" target="_blank">Descargar archivo</a></p>
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
