<?php
// Incluir archivo de conexión
include 'db.php';

// Consulta para obtener las materias
$sql = "SELECT id_curso, nombre_curso FROM cursos";
$result = mysqli_query($conexion, $sql);

// Verificar si la consulta devuelve resultados y crear las opciones del select
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='" . htmlspecialchars($row['id_curso'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($row['nombre_curso'], ENT_QUOTES, 'UTF-8') . "</option>";
    }
} else {
    // Mensaje si no hay resultados o error en la consulta
    echo "<option value=''>No hay materias disponibles</option>";
}

// Liberar el resultado y cerrar la conexión
mysqli_free_result($result);
mysqli_close($conexion);
?>
