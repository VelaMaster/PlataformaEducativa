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
$id = $_GET['id'];

// Función para obtener el nombre de la materia
function obtenerNombreMateria($id, $conexion) {
    $consulta = "SELECT nombre_curso FROM cursos WHERE id= $id";
    $resultado = $conexion->query($consulta);
    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        return $fila['nombre_curso'];
    } else {
        return "Desconocido";
    }
}

// Consultar los detalles de la tarea
$sql = "SELECT * FROM tareas WHERE id = $id";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    $tarea = $resultado->fetch_assoc();
    $nombre_materia = obtenerNombreMateria($tarea['id'], $conexion);

    // Consultar las rúbricas asociadas a la tarea (corrigiendo la consulta SQL)
    $sql_rubricas = "SELECT * FROM rubricas WHERE id_tarea = $id"; // Usar 'id_tarea' en lugar de 'id'
    $resultado_rubricas = $conexion->query($sql_rubricas);
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
            .table-container {
                max-width: 800px;
                width: 100%;
                margin-top: 20px;
                text-align: left;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            table th, table td {
                border: 1px solid #ddd;
                padding: 10px;
                text-align: left;
            }
            table th {
                background-color: #ff9900;
                color: white;
                font-weight: bold;
                text-align: center;
            }
            table td {
                background-color: #ffffff;
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
        </div>

        <!-- Tabla de rúbricas -->
        <div class="table-container">
            <h3>Rúbrica Asignada</h3>
            <table>
                <thead>
                    <tr>
                        <th>Criterio</th>
                        <th>Descripción</th>
                        <th>Puntos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado_rubricas->num_rows > 0): ?>
                        <?php while ($rubrica = $resultado_rubricas->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($rubrica['criterio']); ?></td>
                                <td><?php echo htmlspecialchars($rubrica['descripcion']); ?></td>
                                <td><?php echo htmlspecialchars($rubrica['puntos']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center;">No hay rúbricas asignadas a esta tarea.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="back-button-container">
            <a href="listarTareas.php" class="back-button">Regresar a Tareas Asignadas</a>
        </div>
    </body>
    </html>

    <?php
} else {
    echo "Tarea no encontrada";
}
$conexion->close();
?>
