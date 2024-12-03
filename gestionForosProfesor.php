<?php
session_start();
if (isset($_SESSION['usuario'])) {
    $num_control = $_SESSION['usuario'];
} else {
    echo "<script>alert('Error: Usuario no autenticado.'); window.location.href = 'index.php';</script>";
    exit;
}
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";
$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
$sql = "
    SELECT f.id, f.nombre, f.descripcion, f.tipo_for
    FROM foros f
    JOIN cursos c ON f.id_curso = c.id
    JOIN grupos g ON c.id = g.id_curso
    WHERE g.id_docente = '$num_control'
";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Foros - Profesor</title>
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/gestionForosProfesor.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/iniciosesionalumno.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/barradeNavegacion.css?v=<?php echo time(); ?>">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fb;
            margin: 0;
            padding: 0;
        }
        header, footer {
            background-color: #343a40;
            color: #fff;
            padding: 10px 0;
        }
        h1, h2 {
            font-size: 1.75rem;
            font-weight: 600;
            color: #333;
        }
        h3 {
            color: #555;
            font-weight: 500;
        }
        .navbar {
            background-color: #007bff;
        }
        .navbar a {
            color: white;
            font-weight: 600;
        }
        .navbar a:hover {
            color: #f1f1f1;
        }
        .navbar-toggler-icon {
            background-color: white;
        }
        .card {
            border: none;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background-color: #007bff;
            color: white;
            font-size: 1.25rem;
            padding: 15px;
        }
        .card-body {
            padding: 20px;
        }
        .card-body p {
            color: #666;
            font-size: 1rem;
            line-height: 1.6;
        }
        .card-footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 15px;
        }
        .form-container {
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .form-container label {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
        }
        .form-container input, .form-container select, .form-container textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1rem;
            color: #333;
        }
        .form-container input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-container input[type="submit"]:hover {
            background-color: #218838;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        footer p {
            text-align: center;
            font-size: 1rem;
        }
    </style>
</head>
<body>
<header>
    <div class="container">
        <h1>Plataforma educativa para Ingeniería en Sistemas</h1>
    </div>
</header>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Inicio</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="calendarioDocente.php">Calendario</a></li>
                <li class="nav-item"><a class="nav-link" href="gestionTareasProfesor.php">Asignar tareas</a></li>
                <li class="nav-item"><a class="nav-link" href="calificarTareas.php">Calificar tareas</a></li>
            </ul>
        </div>
    </div>
</nav>

<main class="container mt-4">
    <section id="asignar-foro">
        <h2>Asignar Nuevo Foro</h2>
        <div class="form-container">
            <form action="asignarForo.php" method="POST">
                <label for="materia">Materia:</label>
                <select id="materia" name="materia" required>
                    <?php
                    if ($resultado->num_rows > 0) {
                        while ($row = $resultado->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['nombre']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>No tiene foros asignados</option>";
                    }
                    ?>
                </select>

                <label for="titulo">Título del Foro:</label>
                <input type="text" id="titulo" name="titulo" required placeholder="Ingrese el título del foro">

                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" required placeholder="Describa los detalles del foro"></textarea>

                <label for="tipo_for">Tipo de Foro:</label>
                <select id="tipo_for" name="tipo_for" required>
                    <option value="general">Foro General</option>
                    <option value="tematico">Foro Temático</option>
                </select>

                <input type="submit" value="Asignar Foro">
            </form>
        </div>
    </section>

    <section id="foros-asignados">
        <h2>Foros Asignados</h2>
        <div class="form-container">
            <form method="POST" action="listarforos.php">
                <input type="submit" value="Mostrar Foros Asignados" class="btn btn-primary">
            </form>
        </div>
    </section>
</main>

<footer>
    <p>© 2024 PE-ISC</p>
</footer>

<script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conexion->close();
?>
