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

// Obtener ID de la tarea
$id_tarea = $_GET['id'];

// Función para obtener el nombre del curso
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

// Obtener los detalles de la tarea
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
        <title>Detalle de Tarea</title>
        <link rel="stylesheet" href="css/estiloTarea.css">
    </head>
    <body>
    <!-- Barra de navegación -->
    <header>
        <nav>
            <ul>
                <li><a href="inicioAlumno.php">Inicio</a></li>
                <li><a href="gestionTareasAlumno.php">Volver</a></li>
            </ul>
        </nav>
    </header>
    <main>
    <!-- Área de detalles de la tarea -->
    <h1>Detalle de la Tarea</h1>
    <div id="container">
        <section id="detalle-tarea">
            <h2>Tarea: <span id="titulo-tarea"><?php echo $tarea['titulo']; ?></span></h2>
            <p id="descripcion-tarea"><?php echo $tarea['descripcion']; ?></p>
            <p><strong>Materia:</strong> <?php echo $nombre_materia; ?></p>
            <p><strong>Fecha de creación:</strong> <?php echo $tarea['fecha_creacion']; ?></p>
            <p><strong>Fecha límite:</strong> <?php echo $tarea['fecha_limite']; ?></p>
            <?php if (!empty($tarea['archivo_tarea'])): ?>
                <p><strong>Archivo:</strong> <a href="<?php echo $tarea['archivo_tarea']; ?>" target="_blank">Descargar archivo</a></p>
            <?php endif; ?>
        </section>
    </div>

    <!-- Área para subir archivos -->
    <section id="subir-archivo">
        <h3>Subir tu archivo</h3>
        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="archivo" required>
            <button type="submit" id="enviar-btn">Enviar</button>
        </form>
    </section>

    <!-- Botón de regreso -->
    <button onclick="location.href='inicioAlumno.php'">Volver</button>

    </main>

    <footer>
        <p>© 2024 Plataforma de Educación</p>
    </footer>

    <script>
        document.querySelector('form').addEventListener('submit', function(event) {
            event.preventDefault();
            const mensaje = document.createElement('p');
            mensaje.textContent = 'Archivo subido';
            mensaje.style.color = 'green';
            document.getElementById('subir-archivo').appendChild(mensaje);

            const enviarBtn = document.getElementById('enviar-btn');
            enviarBtn.style.display = 'none';

            const eliminarBtn = document.createElement('button');
            eliminarBtn.textContent = 'Eliminar';
            eliminarBtn.id = 'eliminar-btn';

            eliminarBtn.addEventListener('click', function() {
                mensaje.remove();
                eliminarBtn.remove();
                enviarBtn.style.display = 'inline';
            });

            document.getElementById('subir-archivo').appendChild(eliminarBtn);
            event.target.submit();
        });
    </script>

    </body>
    </html>
    <?php
} else {
    echo "Tarea no encontrada";
}
$conexion->close();
?>
