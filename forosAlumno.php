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

// Consulta para obtener los foros asignados al alumno
$sql = "SELECT foros.id AS id_foro, foros.nombre AS nombre_foro, foros.descripcion AS descripcion_foro
        FROM foros
        JOIN foro_accesoalumnos ON foros.id = foro_accesoalumnos.id_foros
        WHERE foro_accesoalumnos.num_controlAlumno = '$num_control'";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Foros Asignados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #FFE4B5; /* Fondo naranja claro */
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .content {
            flex: 1;
        }

        h2 {
            text-align: center;
            color: #333;
            padding: 20px 0;
            font-size: 24px;
        }

        .table-container {
            max-width: 90%;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #FF9900; /* Encabezado naranja */
            color: #fff;
            font-weight: bold;
            text-align: left;
            padding: 12px;
            font-size: 16px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            font-size: 15px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .acciones a {
            display: inline-block;
            padding: 5px 10px;
            color: #FF7700; /* Texto naranja */
            text-decoration: none;
            font-weight: bold;
            border: 2px solid #FF7700;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .acciones a:hover {
            background-color: #FF7700;
            color: #fff;
        }

        .back-button-container {
            text-align: center;
            margin: 20px;
        }

        .back-button {
            background-color: #FF9900; /* Botón anaranjado */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 20px; /* Bordes redondeados */
            font-weight: bold;
            text-decoration: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); /* Sombra */
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background-color: #FF7700; /* Más oscuro al pasar el cursor */
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.3); /* Sombra más intensa */
        }

        footer {
            background-color: #000; /* Pie de página negro */
            color: white;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="content">
        <h2>Foros Asignados</h2>

        <div class="table-container">
            <table>
                <tr>
                    <th>Nombre del Foro</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
                <?php
                if ($resultado->num_rows > 0) {
                    while ($fila = $resultado->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($fila["nombre_foro"]) . "</td>";
                        echo "<td>" . htmlspecialchars($fila["descripcion_foro"]) . "</td>";
                        echo "<td class='acciones'><a href='responder_foro.php?id_foro=" . $fila["id_foro"] . "'>Abrir Foro</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No tienes foros asignados.</td></tr>";
                }
                $conexion->close();
                ?>
            </table>
        </div>

        <div class="back-button-container">
            <a href="inicioAlumno.php" class="back-button">Regresar al inicio</a>
        </div>
    </div>

    <footer>
        <p>© 2024 PE-ISC</p>
    </footer>
</body>
</html>