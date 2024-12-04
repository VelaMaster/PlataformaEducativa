<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Obtener el número de control del usuario desde la sesión
$num_control = $_SESSION['usuario'];

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "peis");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener el ID del foro desde la URL
if (isset($_GET['id_foro'])) {
    $id_tema = $_GET['id_foro'];

    // Consulta para obtener los datos del foro
    $sql = "SELECT * FROM foros WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_tema);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $foro = $resultado->fetch_assoc();
    } else {
        die("Foro no encontrado.");
    }
} else {
    die("ID del foro no proporcionado.");
}

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tema = $_POST['id_tema'];       // ID del foro
    $id_usuario = $_POST['id_usuario']; // ID del usuario (número de control)
    $contenido = trim($_POST['contenido']); // Contenido de la respuesta

    // Validar que la respuesta no esté vacía
    if (empty($contenido)) {
        echo "<script>
                alert('Debes contestar el foro antes de enviarlo.');
                window.history.back();
              </script>";
        exit();
    }

    // Insertar la respuesta en la tabla respuestas
    $sql = "INSERT INTO respuestas (id_tema, id_usuario, contenido, fecha_creacion) VALUES (?, ?, ?, NOW())";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iis", $id_tema, $id_usuario, $contenido);

    if ($stmt->execute()) {
        echo "<script>
                alert('¡Respuesta entregada con éxito!');
                window.location.href = 'forosAlumno.php';
              </script>";
        exit();
    } else {
        echo "Error al guardar la respuesta: " . $conexion->error;
    }

    $stmt->close();
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Responder Foro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #FFE4B5; /* Fondo naranja claro */
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            font-weight: bold;
            margin-top: 10px;
        }
        textarea {
            width: 100%;
            height: 150px;
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            display: block;
            width: 100%;
            margin-top: 20px;
            padding: 10px;
            background-color: #FF9900; /* Botón anaranjado */
            color: white;
            border: none;
            border-radius: 20px; /* Bordes redondeados */
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); /* Sombra */
            transition: all 0.3s ease;
        }
        button:hover {
            background-color: #FF7700; /* Más oscuro al pasar el cursor */
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.3); /* Sombra más intensa */
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Responder Foro</h2>
    <p><strong>Foro:</strong> <?php echo htmlspecialchars($foro['nombre']); ?></p>
    <p><strong>Descripción:</strong> <?php echo htmlspecialchars($foro['descripcion']); ?></p>

    <form action="" method="POST">
        <input type="hidden" name="id_tema" value="<?php echo $foro['id']; ?>">
        <input type="hidden" name="id_usuario" value="<?php echo $num_control; ?>">
        
        <label for="contenido">Tu Respuesta:</label>
        <textarea id="contenido" name="contenido" placeholder="Escribe tu respuesta aquí..." required></textarea>

        <button type="submit">Enviar Respuesta</button>
    </form>
</div>

</body>
</html>