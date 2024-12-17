<?php
// Conexión a la base de datos
$conexion = mysqli_connect("localhost", "root", "", "peis");

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Consulta SQL
$sql = "SELECT 
            respuestas.id AS id_respuesta,
            foros.nombre AS titulo_foro,
            foros.descripcion AS descripcion_foro,
            CONCAT(alumnos.nombre, ' ', alumnos.apellido_p, ' ', alumnos.apellido_m) AS nombre_estudiante,
            respuestas.fecha_creacion AS fecha_respuesta,
            respuestas.calificacion,
            respuestas.revisado
        FROM respuestas
        INNER JOIN foros ON respuestas.id_tema = foros.id
        INNER JOIN alumnos ON respuestas.id_usuario = alumnos.id
        ORDER BY respuestas.fecha_creacion DESC";

$resultado = $conexion->query($sql);

// Verificar si hay resultados
if ($resultado->num_rows > 0):
?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Listado de Respuestas</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container mt-5">
            <h2 class="mb-4">Listado de Respuestas</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Título del Foro</th>
                        <th>Descripción</th>
                        <th>Estudiante</th>
                        <th>Fecha de Respuesta</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['titulo_foro']); ?></td>
                            <td><?php echo htmlspecialchars($row['descripcion_foro']); ?></td>
                            <td><?php echo htmlspecialchars($row['nombre_estudiante']); ?></td>
                            <td><?php echo htmlspecialchars($row['fecha_respuesta']); ?></td>
                            <td>
                                <form action="calificarForos.php" method="POST" class="d-flex align-items-center">
                                    <input type="hidden" name="id_respuesta" value="<?php echo $row['id_respuesta']; ?>">
                                    <input type="number" name="calificacion" class="form-control me-2" placeholder="Calificación" min="0" max="100" required>
                                    <div class="form-check me-2">
                                        <input class="form-check-input" type="checkbox" name="revisado">
                                        <label class="form-check-label">Revisado</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </body>
    </html>
<?php
else:
    echo "<p>No se encontraron respuestas.</p>";
endif;

// Cerrar conexión
$conexion->close();
?>
