<?php
// editarForo.php

$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";

$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$id_foro = $_GET['id'];

// Actualizar foro principal
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['actualizar_foro'])) {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $tipo_foro = $_POST['tipo_foro'];

    // Actualizar foro en la base de datos
    $sql = "UPDATE foros SET nombre = ?, descripcion = ?, tipo_for = ? WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssi", $titulo, $descripcion, $tipo_foro, $id_foro);

    if ($stmt->execute()) {
        header("Location: listarForos.php");
        exit();
    } else {
        echo "Error al actualizar: " . $stmt->error;
    }
    $stmt->close();
}

// Consultar datos del foro
$sql = "SELECT * FROM foros WHERE id = $id_foro";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    $foro = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Foro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
        }
        .container {
            width: 100%;
            max-width: 800px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        h2 {
            color: #ff9900;
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }
        .form-group input, .form-group textarea {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn {
            background-color: #ff9900;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #e68a00;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Foro</h2>
        <form method="POST">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($foro['nombre']); ?>" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" required><?php echo htmlspecialchars($foro['descripcion']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="tipo_foro">Tipo de Foro:</label>
                <input type="text" id="tipo_foro" name="tipo_foro" value="<?php echo htmlspecialchars($foro['tipo_for']); ?>" required>
            </div>
            <button type="submit" name="actualizar_foro" class="btn">Actualizar Foro</button>
        </form>
    </div>
</body>
</html>

<?php
} else {
    echo "Foro no encontrado.";
}
$conexion->close();
?>
