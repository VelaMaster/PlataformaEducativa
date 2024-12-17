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
// Determinar el filtro y la búsqueda
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'privados';
$busqueda = isset($_GET['busqueda']) ? $conexion->real_escape_string($_GET['busqueda']) : '';

// Consulta para obtener los foros según el filtro y búsqueda
if ($filtro === 'publicos') {
    $sql = "SELECT id AS id_foro, nombre AS nombre_foro, descripcion AS descripcion_foro
            FROM foros
            WHERE tipo_for = 'general' AND nombre LIKE '%$busqueda%'";
} else {
    $sql = "SELECT foros.id AS id_foro, foros.nombre AS nombre_foro, foros.descripcion AS descripcion_foro
            FROM foros
            JOIN foro_accesoalumnos ON foros.id = foro_accesoalumnos.id_foros
            WHERE foro_accesoalumnos.num_controlAlumno = '$num_control' AND foros.nombre LIKE '%$busqueda%'";
}

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Foros</title>
    <link rel="stylesheet" href="css/verForosAlumno.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="content">
        <h2>Foros</h2>

        <!-- Barra de búsqueda -->
        <div class="search-bar">
            <form method="get" action="">
                <input type="text" name="busqueda" placeholder="Buscar foros..." value="<?= htmlspecialchars($busqueda) ?>">
                <button type="submit">Buscar</button>
                <input type="hidden" name="filtro" value="<?= htmlspecialchars($filtro) ?>">
            </form>
        </div>

        <!-- Filtro de foros -->
        <form method="get" action="">
            <label for="filtro">Selecciona tipo de foros:</label>
            <select name="filtro" id="filtro" onchange="this.form.submit()">
                <option value="privados" <?= $filtro === 'privados' ? 'selected' : '' ?>>Privados</option>
                <option value="publicos" <?= $filtro === 'publicos' ? 'selected' : '' ?>>Generales</option>
            </select>
            <input type="hidden" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>">
        </form>

        <!-- Tabla de resultados -->
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
                    echo "<tr><td colspan='3'>No se encontraron foros.</td></tr>";
                }
                $conexion->close();
                ?>
            </table>
        </div>

        <!-- Botón regresar -->
        <div class="back-button-container">
            <a href="inicioAlumno.php" class="back-button">Regresar al inicio</a>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 PE-ISC</p>
    </footer>
</body>
</html>