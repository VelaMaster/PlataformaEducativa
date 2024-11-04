<?php
session_start();

// Ensure 'usuario' session variable is set
if (isset($_SESSION['usuario'])) {
    $num_control = $_SESSION['usuario'];
} else {
    // Redirect to login page if not authenticated
    echo "<script>alert('Error: Usuario no autenticado.'); window.location.href = 'index.php';</script>";
    exit;
}

// Database connection
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";

$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Query to fetch subjects assigned to the professor
$sql = "SELECT id_curso, nombre_curso FROM cursos WHERE id_docente = '$num_control'";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tareas - Profesor</title>
    <link rel="stylesheet" href="css/estiloProfesor.css">
    <style>
        /* Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        header nav {
            background-color: #ff9900;
            padding: 10px;
        }

        header nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            margin: 0;
            padding: 0;
        }

        header nav ul li {
            margin: 0 15px;
        }

        header nav ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }

        main {
            max-width: 800px;
            margin: 30px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h1 {
            color: #333;
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 20px;
            color: #ff9900;
            border-bottom: 2px solid #ff9900;
            padding-bottom: 8px;
            margin-top: 20px;
        }

        form {
            display: grid;
            gap: 10px;
            margin-top: 20px;
        }

        form label {
            font-weight: bold;
            color: #333;
        }

        form input, form select, form textarea, form button {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            outline: none;
        }

        form button {
            background-color: #ff9900;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #e68a00;
        }

        footer {
            text-align: center;
            padding: 15px;
            background-color: #333;
            color: #fff;
            margin-top: 20px;
            border-top: 3px solid #ff9900;
        }
    </style>
</head>
<body>

<header>
    <nav>
        <ul>
            <li><a href="inicioProfesor.php">Inicio</a></li>
            <li><a href="calendarioProfesor.php">Calendario</a></li>
            <li><a href="gestionTareasProfesor.php">Gestión de Tareas</a></li>
        </ul>
    </nav>
</header>

<main>
    <h1>Gestión de Tareas</h1>

    <section id="asignar-tarea">
        <h2>Asignar Nueva Tarea</h2>
        <form action="asignarTarea.php" method="POST">
            <label for="materia">Materia:</label>
            <select id="materia" name="materia">
                <?php
                // Display each subject assigned to the professor
                if ($resultado->num_rows > 0) {
                    while($row = $resultado->fetch_assoc()) {
                        echo "<option value='" . $row['id_curso'] . "'>" . $row['nombre_curso'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No tiene materias asignadas</option>";
                }
                ?>
            </select>

            <label for="titulo">Título de la Tarea:</label>
            <input type="text" id="titulo" name="titulo" required>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required></textarea>

            <label for="fechaEntrega">Fecha de Entrega:</label>
            <input type="date" id="fechaEntrega" name="fechaEntrega" required>

            <button type="submit">Asignar Tarea</button>
        </form>
    </section>

    <section id="mostrar-tareas">
        <form action="listarTareas.php" method="POST">
            <button type="submit">Mostrar Tareas Asignadas</button>
        </form>
    </section>
</main>

<footer>
    <p>© 2024 Plataforma de Educación</p>
</footer>

</body>
</html>

<?php
$conexion->close();
?>
