<?php
session_start();
include('db.php');

// Verificar si el docente ha iniciado sesión y obtener su num_control
if (!isset($_SESSION['num_control'])) {
    die("Docente no autenticado.");
}

$docente_num_control = $_SESSION['num_control'];

// Preparar la consulta para obtener los foros a los que el docente tiene acceso
$query = "
    SELECT f.id AS foro_id, f.nombre AS foro_nombre, f.descripcion AS foro_desc, f.tipo_for, c.nombre_curso
    FROM foro_accesodocentes fad
    JOIN foros f ON fad.id_foros = f.id
    JOIN cursos c ON f.id_curso = c.id
    WHERE fad.num_controlDocente = ?
";

// Preparar la sentencia
if ($stmt = mysqli_prepare($conexion, $query)) {
    // Vincular parámetros
    mysqli_stmt_bind_param($stmt, "i", $docente_num_control);

    // Ejecutar la consulta
    mysqli_stmt_execute($stmt);

    // Obtener el resultado
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        die("Error en la consulta: " . mysqli_error($conexion));
    }

    // Se obtienen los foros en un arreglo
    $foros = array();
    $materiasUnicas = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $foros[] = $row;
        // Generar una lista de materias únicas
        if (!in_array($row['nombre_curso'], $materiasUnicas)) {
            $materiasUnicas[] = $row['nombre_curso'];
        }
    }

    // Cerramos la sentencia
    mysqli_stmt_close($stmt);
} else {
    die("Error en la preparación de la consulta: " . mysqli_error($conexion));
}

// Cerramos la conexión
mysqli_close($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foros Asignados</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/listarForos.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="barranavegacion">
        <div class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Plataforma educativa para Ingeniería en Sistemas</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" 
                        aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="inicioProfesor.php">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="calendarioDocente.php">Calendario</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="gestionTareasProfesor.php">Asignar tareas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="gestionForosProfesor.php">Asignar foros</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="calificarTareas.php">Calificar tareas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="calificarForos.php">Calificar foros</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<<<<<<< HEAD

=======
>>>>>>> 151ad236f90eeac917f44fffddad83de80bb9526
    <div class="container">
        <h1 class="text-center mb-4">Foros Asignados</h1>

        <!-- Filtros -->
        <div class="mb-4">
            <div class="row">
                <div class="col-md-6">
                    <label for="filtro-materia" class="form-label">Filtrar por Materia:</label>
                    <select id="filtro-materia" class="form-select" onchange="filtrarForos()">
                        <option value="">Todas las Materias</option>
                        <?php foreach ($materiasUnicas as $materia): ?>
                            <option value="<?php echo htmlspecialchars($materia, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($materia, ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="filtro-tipo" class="form-label">Filtrar por tipo de foro:</label>
                    <select id="filtro-tipo" class="form-select" onchange="filtrarForos()">
                        <option value="">Todos los tipos</option>
                        <option value="general">General</option>
                        <option value="privado">Privado</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Foro Cards generadas dinámicamente -->
        <?php foreach($foros as $foro): ?>
            <?php
            // Ajustar tipo_for: 'general' -> 'General', otra cosa -> 'Privado'
            $tipo_mostrar = ($foro['tipo_for'] === 'general') ? 'General' : 'Privado';
            $data_tipo = strtolower($foro['tipo_for']);
            ?>
            <div class="foro-card" 
                 data-materia="<?php echo htmlspecialchars($foro['nombre_curso'], ENT_QUOTES, 'UTF-8'); ?>" 
                 data-tipo="<?php echo htmlspecialchars($data_tipo, ENT_QUOTES, 'UTF-8'); ?>" 
                 onclick="toggleButtons(this)">
                <h3>Materia: <?php echo htmlspecialchars($foro['nombre_curso'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p><strong>Título del Foro:</strong> <?php echo htmlspecialchars($foro['foro_nombre'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Descripción:</strong> <?php echo htmlspecialchars($foro['foro_desc'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p class="tipo-foro">Tipo: <?php echo htmlspecialchars($tipo_mostrar, ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="foro-buttons">
                    <!-- Modificar para redirigir a las páginas correspondientes -->
<<<<<<< HEAD
                    <a href="verForo.php?id=<?php echo $foro['foro_id']; ?>" class="btn btn-primary">Ver Foro</a>
                    <a href="editarForo.php?id=<?php echo $foro['foro_id']; ?>" class="btn btn-warning">Editar Foro</a>
                    <button class="btn btn-danger" onclick="confirmarEliminacion(<?php echo $foro['foro_id']; ?>)">Eliminar Foro</button>
=======
                    <a href="verForo.php?id=<?php echo urlencode($foro['foro_id']); ?>" class="btn btn-primary">Ver Foro</a>
                    <a href="editarForo.php?id=<?php echo urlencode($foro['foro_id']); ?>" class="btn btn-warning">Editar Foro</a>
                    <a href="eliminarForo.php?id=<?php echo urlencode($foro['foro_id']); ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este foro?');">Eliminar Foro</a>
>>>>>>> 151ad236f90eeac917f44fffddad83de80bb9526
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Modal de confirmación -->
    <div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEliminarLabel">Confirmar eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar esta tarea?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmarEliminarBtn">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para alternar botones
        function toggleButtons(card) {
            const allCards = document.querySelectorAll('.foro-card');
            allCards.forEach(c => {
                if (c !== card) {
                    c.classList.remove('active');
                }
            });
            card.classList.toggle('active');
        }

        // Función para filtrar foros
        function filtrarForos() {
            const filtroMateria = document.getElementById('filtro-materia').value.toLowerCase();
            const filtroTipo = document.getElementById('filtro-tipo').value.toLowerCase();
            const tarjetas = document.querySelectorAll('.foro-card');

            tarjetas.forEach(tarjeta => {
                const materia = tarjeta.getAttribute('data-materia').toLowerCase();
                const tipo = tarjeta.getAttribute('data-tipo').toLowerCase();

                if (
                    (filtroMateria === "" || materia === filtroMateria) &&
                    (filtroTipo === "" || tipo === filtroTipo)
                ) {
                    tarjeta.style.display = "block";
                } else {
                    tarjeta.style.display = "none";
                }
            });
        }

<<<<<<< HEAD
        // Función para mostrar el modal de confirmación
        function confirmarEliminacion(foroId) {
            const confirmarBtn = document.getElementById('confirmarEliminarBtn');
            confirmarBtn.onclick = function() {
                window.location.href = "eliminarForo.php?id=" + foroId;
            };
            const modal = new bootstrap.Modal(document.getElementById('modalEliminar'));
            modal.show();
        }
=======
        // Nota: La función eliminarForo se maneja directamente en el onclick del botón eliminar
>>>>>>> 151ad236f90eeac917f44fffddad83de80bb9526
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
