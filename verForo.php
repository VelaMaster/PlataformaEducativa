<?php
// verForo.php

// Conexión a la base de datos
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";

$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener el ID del foro
$id = $_GET['id'];

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

// Consultar los detalles del foro
$sql = "SELECT * FROM foros WHERE id = $id";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    $foro = $resultado->fetch_assoc();
    $nombre_materia = obtenerNombreMateria($foro['id_curso'], $conexion);
    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Detalles del Foro</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f6f9;
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                height: 100vh;
                margin: 0;
            }
            .card {
                background-color: #ffffff;
                max-width: 800px;
                width: 100%;
                padding: 30px;
                border-radius: 12px;
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
                text-align: center;
                margin-bottom: 20px;
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
            <h2>Detalles del Foro</h2>
            <div class="detail">
                <label>Materia:</label>
                <p><?php echo htmlspecialchars($nombre_materia); ?></p>
            </div>
            <div class="detail">
                <label>Título:</label>
                <p><?php echo htmlspecialchars($foro['nombre']); ?></p>
            </div>
            <div class="detail">
                <label>Descripción:</label>
                <p><?php echo htmlspecialchars($foro['descripcion']); ?></p>
            </div>
            <div class="detail">
                <label>Tipo de Foro:</label>
                <p><?php echo htmlspecialchars($foro['tipo_for']); ?></p>
            </div>
        </div>

        <div class="back-button-container">
            <a href="listarForos.php" class="back-button">Regresar a Foros Asignados</a>
        </div>
    </body>
    </html>

    <?php
} else {
    echo "Foro no encontrado";
}
$conexion->close();
?>
