<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
$passwordPlain = isset($_SESSION['password_plain']) ? $_SESSION['password_plain'] : false;
unset($_SESSION['password_plain']); // Limpiar después de mostrar el modal

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
    <link rel="stylesheet" href="css/barraNavegacion.css">
    <link rel="stylesheet" href="css/barradeNavegacion.css">
    <style>
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
        .card {
            width: 320px;
            height: 270px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            overflow: hidden;
        }
        .card {
    width: 320px;
    height: 270px;
    border-radius: 10px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column; /* Organiza el contenido en columna */
    justify-content: flex-end; /* Empuja el contenido hacia la parte inferior */
    overflow: hidden;
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}
.card:hover {
    transform: scale(1.05); /* Agranda la tarjeta un 5% */
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Incrementa la sombra */
}
        .card-content {
            background-color: rgb(102, 102, 102);
            color: white;
            padding: 16px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
        }
        .btn-orange {
    background-color: #FFA500; /* Color naranja */
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.btn-orange:hover {
    background-color: #FF8C00; /* Naranja más oscuro para hover */
}
.dropdown-item:hover {
    background-color: #F1AA3D;
    color: white;
    border-radius: 10px;
    text-decoration: none;
    text-align: center;
    transform: scale(1.1); /* Aumenta el tamaño */
    transition: transform 0.2s ease-in-out, background-color 0.2s ease-in-out;
}
/* Estilo del enlace al estar activo o enfocado */
.dropdown-item:active,
.dropdown-item:focus {
    background-color: #FFA500; /* Naranja */
    color: white;
    text-decoration: none;
    transform: scale(1); /* Mantiene el tamaño normal */
}

/* Elimina el borde azul predeterminado */
.dropdown-item:focus {
    outline: none;
}
.dropdown-menu{
    text-align: center;
}
    </style>
</head>
<body>

<div class="barranavegacion">
 <div class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Plataforma educativa para Ingenieria en Sistemas</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
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
                    <a class="nav-link" href="calificarTareas.php">Calificar tareas</a>
                </li>
            </ul>
        </div>
    </div>
 </div>
</div>
<!-- Modal de advertencia de seguridad -->
<?php if ($passwordPlain): ?>
    <div class="modal fade" id="passwordWarningModal" tabindex="-1" aria-labelledby="passwordWarningLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordWarningLabel">¡Atención!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    Tu contraseña no está encriptada. Por tu seguridad, te recomendamos cambiarla lo antes posible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-orange" onclick="window.location.href='editarperfilAlumno.php';">Cambiar Contraseña</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="profile-container">
    <a href="#" id="perfilDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="img/perfil120.png" alt="Foto de perfil" class="profile-img">
    </a>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="perfilDropdown">
        <li><a class="dropdown-item" href="verPerfilDocente.php">Ver perfil</a></li>
        <li><a class="dropdown-item" href="soporte.php">Ayuda y soporte</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="logout.php">Salir</a></li>
    </ul>
</div>

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
<script>
    <?php if ($passwordPlain): ?>
        var passwordWarningModal = new bootstrap.Modal(document.getElementById('passwordWarningModal'));
        passwordWarningModal.show();
    <?php endif; ?>
</script>
</body>
</body>
</html>
