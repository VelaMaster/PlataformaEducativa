<?php
session_start();
include 'db.php';
if (!isset($_SESSION['usuario'])) {
    echo "<script>alert('Error: Usuario no autenticado.'); window.location.href = 'index.php';</script>";
    exit;
}
$num_control = (int) $_SESSION['usuario'];
$sql = "
    SELECT f.id, f.nombre, f.descripcion, f.tipo_for
    FROM foros f
    JOIN foro_accesoDocentes fad ON f.id = fad.id_foros
    WHERE fad.num_controlDocente = ?
";
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("Error al preparar la consulta: " . $conexion->error);
}
$stmt->bind_param("i", $num_control);

if (!$stmt->execute()) {
    die("Error al ejecutar la consulta: " . $stmt->error);
}
$resultado = $stmt->get_result();

$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Foros - Profesor</title>
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/gestionForosProfesor.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/barradeNavegacion.css?v=<?php echo time(); ?>">
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

<main class="container">
    <section id="asignar-foro">
        <h2>Asignar Nuevo Foro</h2>
        <form action="asignarForo.php" method="POST" class="mb-4">
            <div class="mb-3">
                <label for="materia" class="form-label">Materia:</label>
                <select id="materia" name="materia" class="form-select" required>
                    <?php
                    if ($resultado->num_rows > 0) {
                        while ($row = $resultado->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nombre']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>No tiene foros asignados</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="titulo" class="form-label">Título del Foro:</label>
                <input type="text" id="titulo" name="titulo" class="form-control" required placeholder="Ingrese el título del foro">
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción:</label>
                <textarea id="descripcion" name="descripcion" class="form-control" required placeholder="Describa los detalles del foro"></textarea>
            </div>
            <div class="mb-3">
                <label for="tipo_for" class="form-label">Tipo de Foro:</label>
                <select id="tipo_for" name="tipo_for" class="form-select" required onchange="toggleAccesoAlumnos(this.value)">
                    <option value="general">Foro General</option>
                    <option value="tematico">Foro Privado</option>
                </select>
            </div>

            <button type="button" id="addRubricaButton" class="btn btn-secondary me-2" onclick="mostrarRubrica()">Añadir Rúbrica</button>
            <button type="button" id="removeRubricaButton" class="btn btn-danger" onclick="ocultarRubrica()" style="display: none;">Eliminar Rúbrica</button>

            <div id="rubricaContainer" style="display: none;" class="mt-3">
                <h3>Rúbrica de Evaluación</h3>
                <table id="rubricaTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Criterio</th>
                            <th>Descripción</th>
                            <th>Puntos</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Filas de rúbrica añadidas dinámicamente -->
                    </tbody>
                </table>
                <div class="button-container mb-3">
                    <button type="button" class="btn btn-primary" onclick="agregarFilaRubrica()">Añadir Fila</button>
                </div>
                <p>Total de Puntos Asignados: <span id="totalPuntos">0</span>/100</p>
            </div>

            <button type="submit" class="btn btn-primary">Asignar Foro</button>
        </form>
    </section>
    <section id="foros-asignados">
        <h2>Foros Asignados</h2>
        <form method="POST" action="listarforos.php">
            <button type="submit" class="btn btn-secondary">Mostrar Foros Asignados</button>
        </form>
    </section>
</main>
<script src="js/rubrica.js"></script>
<script>
    function mostrarRubrica() {
        document.getElementById('rubricaContainer').style.display = 'block';
        document.getElementById('addRubricaButton').style.display = 'none';
        document.getElementById('removeRubricaButton').style.display = 'inline-block';
    }

    function ocultarRubrica() {
        document.getElementById('rubricaContainer').style.display = 'none';
        document.getElementById('addRubricaButton').style.display = 'inline-block';
        document.getElementById('removeRubricaButton').style.display = 'none';
    }

    function agregarFilaRubrica() {
        const tableBody = document.querySelector('#rubricaTable tbody');
        const row = document.createElement('tr');

        row.innerHTML = `
            <td><input type="text" name="rubrica_criterio[]" class="form-control" required></td>
            <td><input type="text" name="rubrica_descripcion[]" class="form-control" required></td>
            <td><input type="number" name="rubrica_puntos[]" class="form-control puntos-input" min="1" max="100" required></td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarFilaRubrica(this)">Eliminar</button></td>
        `;

        tableBody.appendChild(row);
        actualizarTotalPuntos();
    }

    function eliminarFilaRubrica(button) {
        const row = button.parentElement.parentElement;
        row.remove();
        actualizarTotalPuntos();
    }

    function actualizarTotalPuntos() {
        const puntosInputs = document.querySelectorAll('.puntos-input');
        let total = 0;
        puntosInputs.forEach(input => {
            total += parseInt(input.value) || 0;
        });
        document.getElementById('totalPuntos').innerText = total;
    }
    document.querySelector('form').addEventListener('submit', function(e) {
        const total = parseInt(document.getElementById('totalPuntos').innerText) || 0;
        if (total > 100) {
            e.preventDefault();
            alert('El total de puntos asignados en la rúbrica no puede exceder 100.');
        }
    });
    function toggleAccesoAlumnos(tipo) {
    }
</script>
<script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
<footer class="text-center mt-5">
    <p>© 2024 PE-ISC</p>
</footer>
</body>
</html>
