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
        // Validación de puntos en la rúbrica (asegurarse que no exceda 100)
        if (isset($_POST['rubrica_criterio'])) {
            $criterios = $_POST['rubrica_criterio'];
            $descripciones = $_POST['rubrica_descripcion'];
            $puntos = $_POST['rubrica_puntos'];

            // Verificar que el total de puntos no exceda 100
            $total_puntos = array_sum($puntos);
            if ($total_puntos != 100) {
                die("<script>alert('El total de puntos en la rúbrica debe ser exactamente 100.'); window.history.back();</script>");
            }

            // Primero eliminamos las rúbricas existentes para este foro
            $sql_delete = "DELETE FROM rubricasforo WHERE id_foro = ?";
            $stmt_delete = $conexion->prepare($sql_delete);
            $stmt_delete->bind_param("i", $id_foro);
            $stmt_delete->execute();

            // Luego insertamos las nuevas rúbricas
            $sql_insert = "INSERT INTO rubricasforo (id_foro, criterio, descripcion, puntos) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conexion->prepare($sql_insert);

            foreach ($criterios as $index => $criterio) {
                $descripcion = $descripciones[$index];
                $punto = $puntos[$index];
                $stmt_insert->bind_param("issi", $id_foro, $criterio, $descripcion, $punto);
                $stmt_insert->execute();
            }
        }
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

    // Consultar las rúbricas asociadas al foro
    $sql_rubricas = "SELECT * FROM rubricasforo WHERE id_foro = $id_foro";
    $resultado_rubricas = $conexion->query($sql_rubricas);
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #ff9900;
            color: white;
        }
        td {
            background-color: #f9f9f9;
        }
        .add-btn, .remove-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .remove-btn {
            background-color: #dc3545;
        }
        .add-btn:hover, .remove-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Foro</h2>
        <form method="POST" onsubmit="return validarPuntos()">
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

            <h3>Rúbricas Asociadas</h3>
            <table id="rubricaTable">
                <thead>
                    <tr>
                        <th>Criterio</th>
                        <th>Descripción</th>
                        <th>Puntos</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($resultado_rubricas->num_rows > 0) {
                        while ($rubrica = $resultado_rubricas->fetch_assoc()) {
                            echo "<tr>
                                <td><input type='text' name='rubrica_criterio[]' class='form-control' value='" . htmlspecialchars($rubrica['criterio']) . "' required></td>
                                <td><input type='text' name='rubrica_descripcion[]' class='form-control' value='" . htmlspecialchars($rubrica['descripcion']) . "' required></td>
                                <td><input type='number' name='rubrica_puntos[]' class='form-control' value='" . htmlspecialchars($rubrica['puntos']) . "' min='1' max='100' required></td>
                                <td><button type='button' class='remove-btn' onclick='eliminarFila(this)'>Eliminar</button></td>
                            </tr>";
                        }
                    } else {
                        echo "<tr>
                            <td><input type='text' name='rubrica_criterio[]' class='form-control' required></td>
                            <td><input type='text' name='rubrica_descripcion[]' class='form-control' required></td>
                            <td><input type='number' name='rubrica_puntos[]' class='form-control' min='1' max='100' required></td>
                            <td><button type='button' class='remove-btn' onclick='eliminarFila(this)'>Eliminar</button></td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>

            <button type="button" class="add-btn" onclick="agregarFila()">Añadir Fila</button>
            <button type="submit" name="actualizar_foro" class="btn">Actualizar Foro</button>
        </form>
    </div>

    <script>
        // Función para agregar una nueva fila a la tabla de rúbricas
        function agregarFila() {
            const tableBody = document.querySelector('#rubricaTable tbody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type='text' name='rubrica_criterio[]' class='form-control' required></td>
                <td><input type='text' name='rubrica_descripcion[]' class='form-control' required></td>
                <td><input type='number' name='rubrica_puntos[]' class='form-control' min='1' max='100' required></td>
                <td><button type='button' class='remove-btn' onclick='eliminarFila(this)'>Eliminar</button></td>
            `;
            tableBody.appendChild(row);
        }

        // Función para eliminar una fila de la tabla de rúbricas
        function eliminarFila(button) {
            const row = button.parentElement.parentElement;
            row.remove();
        }

        // Validación de puntos (asegurarse de que el total sea exactamente 100)
        function validarPuntos() {
            const puntosInputs = document.querySelectorAll('[name="rubrica_puntos[]"]');
            let totalPuntos = 0;
            puntosInputs.forEach(input => {
                totalPuntos += parseInt(input.value) || 0;
            });

            if (totalPuntos !== 100) {
                alert('El total de los puntos de la rúbrica debe ser exactamente 100.');
                return false;  // Evita el envío del formulario
            }
            return true;  // Permite el envío del formulario
        }
    </script>
</body>
</html>

<?php
} else {
    echo "Foro no encontrado.";
}
$conexion->close();
?>
