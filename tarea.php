<?php
// tarea.php

// Conexión a la base de datos
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";

$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener ID de la tarea y asegurarse de que es un número entero
$id_tarea = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Verificar si el ID de la tarea es válido
if ($id_tarea > 0) {
    // Obtener el ID del alumno desde la sesión
    session_start();
    $id_alumno = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 0;

    if ($id_alumno > 0) {
        // Consulta para verificar si el alumno tiene acceso a esta tarea
        $sql = "SELECT * FROM tareas
                JOIN grupo_alumnos ON grupo_alumnos.id_grupo = tareas.id_curso
                WHERE tareas.id_tarea = $id_tarea
                AND grupo_alumnos.num_control = $id_alumno";

        $resultado = $conexion->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
            $tarea = $resultado->fetch_assoc();

            // Verificar si el alumno ya entregó la tarea
            $sqlEntrega = "SELECT * FROM entregas WHERE id_tarea = $id_tarea AND id_alumno = $id_alumno";
            $resultadoEntrega = $conexion->query($sqlEntrega);
            $entregado = $resultadoEntrega && $resultadoEntrega->num_rows > 0;

            // Obtener el nombre de la materia
            function obtenerNombreMateria($id_curso, $conexion) {
                $consulta = "SELECT nombre_curso FROM cursos WHERE id_curso = $id_curso";
                $resultado = $conexion->query($consulta);
                if ($resultado && $resultado->num_rows > 0) {
                    $fila = $resultado->fetch_assoc();
                    return $fila['nombre_curso'];
                } else {
                    return "Desconocido";
                }
            }

            $nombre_materia = obtenerNombreMateria($tarea['id_curso'], $conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Tarea</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
         * {
                    box-sizing: border-box;
                    margin: 0;
                    padding: 0;
                }
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f8f9fa;
                    color: #333;
                    line-height: 1.6;
                    padding: 20px;
                }
                .navbar, .footer {
                    background-color: #333;
                    color: #fff;
                    text-align: center;
                    padding: 10px 0;
                }
                .navbar a, .footer {
                    color: #fff;
                    text-decoration: none;
                    margin: 0 20px;
                    font-weight: bold;
                }
                .footer {
                    position: relative;
                    padding-top: 10px;
                }
                .footer::after {
                    content: '';
                    display: block;
                    width: 100px;
                    height: 4px;
                    background-color: #ff6600;
                    margin: 5px auto 0;
                }
                .container {
                    max-width: 900px;
                    margin: 20px auto;
                    background: #fff;
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    padding: 30px;
                }
                h1 {
                    text-align: center;
                    color: #ff6600;
                    margin-bottom: 20px;
                }
                .line {
                    border-bottom: 2px solid #ff6600;
                    margin-bottom: 20px;
                }
                .detail-item {
                    display: flex;
                    justify-content: space-between;
                    padding: 10px 0;
                    border-bottom: 1px solid #ddd;
                }
                .detail-item:last-child {
                    border-bottom: none;
                }
                .detail-label {
                    font-weight: bold;
                }
                .upload-section {
                    background: #f9f9f9;
                    padding: 20px;
                    border-radius: 8px;
                    border: 1px solid #ddd;
                    margin-top: 20px;
                    text-align: center;
                }
                .upload-section h3 {
                    margin-bottom: 15px;
                    color: #6c757d;
                }
                .upload-section input[type="file"] {
                    display: block;
                    margin: 10px auto;
                    padding: 8px;
                    border: 1px solid #ccc;
                    border-radius: 4px;
                    width: 80%;
                }
                .upload-section button {
                    background-color: #ff6600;
                    color: #fff;
                    border: none;
                    border-radius: 5px;
                    padding: 10px 20px;
                    font-size: 16px;
                    cursor: pointer;
                    transition: background-color 0.3s;
                }
                .upload-section button:hover {
                    background-color: #e65c00;
                }
                .eliminar-btn {
                    background-color: #dc3545;
                    color: #fff;
                    border: none;
                    border-radius: 5px;
                    padding: 10px 20px;
                    font-size: 16px;
                    cursor: pointer;
                    margin-top: 10px;
                    transition: background-color 0.3s;
                }
                .eliminar-btn:hover {
                    background-color: #c82333;
                }
                .back-button {
                    display: block;
                    width: 100%;
                    text-align: center;
                    background-color: #ff6600;
                    color: #fff;
                    padding: 10px 0;
                    border-radius: 5px;
                    text-decoration: none;
                    font-weight: bold;
                    margin-top: 20px;
                    transition: background-color 0.3s;
                }
                .back-button:hover {
                    background-color: #e65c00;
                }
                .preview {
                    margin-top: 15px;
                    text-align: center;
                }
                .preview img {
                    max-width: 100px;
                    border-radius: 5px;
                    margin-top: 10px;
                }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="inicioAlumno.php">Inicio</a>
        <a href="gestionTareasAlumno.php">Volver</a>
    </div>
    <div class="container">
        <h1>Detalles de la Tarea</h1>
        <div class="line"></div>
        <div class="detail-item">
            <span class="detail-label">Materia:</span>
            <span><?php echo htmlspecialchars($nombre_materia); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Título:</span>
            <span><?php echo htmlspecialchars($tarea['titulo']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Descripción:</span>
            <span><?php echo htmlspecialchars($tarea['descripcion']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Fecha de Creación:</span>
            <span><?php echo htmlspecialchars($tarea['fecha_creacion']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Fecha de Entrega:</span>
            <span><?php echo htmlspecialchars($tarea['fecha_limite']); ?></span>
        </div>

        <?php if ($entregado): ?>
            <?php 
            // Obtener los datos de la entrega
            $entrega = $resultadoEntrega->fetch_assoc();
            $nombre_archivo = $entrega['archivo_entrega']; // Nombre del archivo subido
            ?>
            <div class="detail-item">
                <span class="detail-label">Archivo Entregado:</span>
                <!-- Nombre del archivo que puede ser clickeado para ver o descargar -->
                <a href="download.php?file=<?php echo urlencode($nombre_archivo); ?>" target="_blank">
                    <?php echo htmlspecialchars($nombre_archivo); ?>
                </a>
            </div>
            <form action="eliminarTareaAlumno.php" method="POST">
                <input type="hidden" name="id_tarea" value="<?php echo $id_tarea; ?>">
                <button type="submit" class="eliminar-btn">Eliminar Tarea</button>
            </form>
        <?php else: ?>
            <div class="upload-section">
                <h3>Subir tu archivo</h3>
                <form id="uploadForm" action="upload.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_tarea" value="<?php echo $id_tarea; ?>">
                    <input type="file" name="archivo" required>
                    <button type="submit">Enviar</button>
                </form>
            </div>
        <?php endif; ?>
        <a href="gestionTareasAlumno.php" class="back-button">Regresar a Tareas Asignadas</a>
    </div>
    <div class="footer">
        © 2024 PE-ISC
    </div>
</body>
</html>
<?php
        } else {
            echo "Tarea no encontrada o no tienes acceso a esta tarea.";
        }
    } else {
        echo "No estás autenticado como alumno.";
    }
} else {
    echo "ID de tarea inválido.";
}

$conexion->close();
?>
