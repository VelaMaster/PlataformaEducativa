<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
$num_control = $_SESSION['usuario'];
$conexion = mysqli_connect("localhost", "root", "", "peis");
if (!$conexion) {
    die("Conexión fallida: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio Profesor</title>
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/iniciosesionalumno.css">
    <link rel="stylesheet" href="css/estilostarjetas.css">
    <style>
        /* Custom styling for a professional look */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .navbar {
            background-color: #ff9900;
        }
        .navbar-brand, .navbar-nav .nav-link {
            color: white;
            font-weight: bold;
        }
        .navbar .container-fluid {
            display: flex;
            justify-content: center;
        }
        .navbar-nav {
            display: flex;
            gap: 20px;
        }
        .navbar-nav .nav-link:hover {
            color: #f5f5f5;
        }
        .profile-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }
        .profile-container {
            position: absolute;
            top: 15px;
            right: 15px;
        }
        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            padding: 20px;
        }
        .card {
            width: 300px;
            height: 250px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }
        .card-content {
            background: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 16px;
        }
        .card-title {
            font-size: 1.2em;
            font-weight: bold;
        }
        .card-subtitle {
            font-size: 0.9em;
        }
        .view-more {
            background-color: #ff9900;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        .view-more:hover {
            background-color: #e58e00;
        }
        footer {
            background-color: #333;
            color: white;
            padding: 10px 0;
            text-align: center;
        }
        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 600px;
            max-height: 80%;
            overflow-y: auto;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        .modal-header {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px;
            background-color: #ff9900;
            color: white;
            font-weight: bold;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            text-align: center;
            position: relative;
        }
        .modal-header h5 {
            margin: 0;
            text-align: center;
        }
        .modal-close {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 1.5em;
            cursor: pointer;
            color: white;
            font-weight: bold;
        }
        .task-table {
            width: 100%;
            border-collapse: collapse;
        }
        .task-table th, .task-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .task-table th {
            background-color: #ff9900;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        .task-table td {
            background-color: #f9f9f9;
        }
        .task-table tr:hover td {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>

<!-- Centered Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand text-center" href="#">Plataforma educativa para Ingeniería en Sistemas</a>
        <div class="collapse navbar-collapse justify-content-center">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="inicioProfesor.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="calendarioProfesor.php">Calendario</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestionTareasProfesor.php">Gestionar tareas</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Profile Image and Dropdown -->
<div class="profile-container">
    <a href="#" id="perfilDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="img/Logoito120.png" alt="Foto de perfil" class="profile-img">
    </a>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="perfilDropdown">
        <li><a class="dropdown-item" href="verPerfilDocente.php">Ver perfil</a></li>
        <li><a class="dropdown-item" href="editarperfilDocente.php">Editar perfil</a></li>
    </ul>
</div>

<!-- Main Content Area -->
<main>
    <div class="card-container">
    <?php
    try {
        $consulta_materias = "
    SELECT 
        c.id_curso,
        c.nombre_curso AS nombre_materia,
        g.nombre_grupo,
        c.imagen_url,
        g.horario,
        g.aula
    FROM 
        cursos c
    JOIN 
        grupos g ON c.id_curso = g.id_curso
    WHERE 
        g.id_docente = '$num_control'
    ORDER BY 
        c.nombre_curso, g.nombre_grupo
";

        
        $resultado_materias = mysqli_query($conexion, $consulta_materias);
        if ($resultado_materias && mysqli_num_rows($resultado_materias) > 0) {
            while ($row = mysqli_fetch_assoc($resultado_materias)) {
                $imagen_url = $row['imagen_url'];
                $id_curso = $row['id_curso'];
                echo "<div class='card' style='background-image: url($imagen_url)'>";
                echo "<div class='card-content'>";
                echo "<h2 class='card-title'>" . $row['nombre_materia'] . "</h2>";
                echo "<p class='card-subtitle'>Grupo: " . $row['nombre_grupo'] . "</p>";
                echo "<p class='card-subtitle'>Horario: " . $row['horario'] . ' ' . $row['aula'] . "</p>";
                echo "<button class='view-more' onclick='showTasks($id_curso)'>Ver más</button>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p class='text-center'>No tienes materias asignadas actualmente.</p>";
        }
    } catch (mysqli_sql_exception $e) {
        echo "Error en la consulta: " . $e->getMessage();
    }

    mysqli_free_result($resultado_materias);
    mysqli_close($conexion);
    ?>
    </div>
</main>

<!-- Modal for tasks -->
<div id="tasksModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h5>Tareas Asignadas</h5>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <table class="task-table" id="tasksList">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Fecha Creada</th>
                    <th>Fecha de Entrega</th>
                </tr>
            </thead>
            <tbody>
                <!-- Task content will be loaded here dynamically -->
            </tbody>
        </table>
    </div>
</div>

<!-- Footer -->
<footer>
    <p>© 2024 PE-ISC</p>
</footer>

<script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
<script>
    function showTasks(courseId) {
        fetch(`getTasks.php?id_curso=${courseId}`)
            .then(response => response.json())
            .then(data => {
                const tasksList = document.getElementById("tasksList").querySelector("tbody");
                tasksList.innerHTML = "";

                if (data.length > 0) {
                    data.forEach(task => {
                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td>${task.titulo}</td>
                            <td>${task.descripcion}</td>
                            <td>${task.fecha_creacion || 'Sin fecha'}</td>
                            <td>${task.fecha_limite || 'Sin fecha'}</td>
                        `;
                        tasksList.appendChild(row);
                    });
                } else {
                    const row = document.createElement("tr");
                    row.innerHTML = "<td colspan='4'>No hay tareas asignadas.</td>";
                    tasksList.appendChild(row);
                }

                document.getElementById("tasksModal").style.display = "flex";
            });
    }

    function closeModal() {
        document.getElementById("tasksModal").style.display = "none";
    }
</script>
</body>
</html>
