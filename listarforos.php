<?php
session_start();
include('db.php');

// Verificar si el docente ha iniciado sesión y obtener su num_control
if (!isset($_SESSION['num_control'])) {
    die("Docente no autenticado.");
}

$docente_num_control = $_SESSION['num_control'];

// Consulta para obtener los foros a los que el docente tiene acceso
$query = "
    SELECT f.id AS foro_id, f.nombre AS foro_nombre, f.descripcion AS foro_desc, f.tipo_for, c.nombre_curso
    FROM foro_accesoDocentes fad
    JOIN foros f ON fad.id_foros = f.id
    JOIN cursos c ON f.id_curso = c.id
    WHERE fad.num_controlDocente = '$docente_num_control'
";

$result = mysqli_query($conexion, $query);
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

// Cerramos la conexión
mysqli_free_result($result);
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
                        <option value="General">General</option>
                        <option value="Privado">Privado</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Foro Cards generadas dinámicamente -->
        <?php foreach($foros as $foro): ?>
            <?php
            // Ajustar tipo_for: 'general' -> 'General', otra cosa -> 'Privado'
            $tipo_mostrar = ($foro['tipo_for'] === 'general') ? 'General' : 'Privado';
            $data_tipo = ($foro['tipo_for'] === 'general') ? 'general' : 'privado';
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
                    <button onclick="alert('Ver Foro')">Ver</button>
                    <button onclick="alert('Editar Foro')">Editar</button>
                    <button onclick="alert('Eliminar Foro')">Eliminar</button>
                </div>
            </div>
        <?php endforeach; ?>
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
    </script>
</body>
</html>
