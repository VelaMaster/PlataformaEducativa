<?php
// eliminarTarea.php
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";

$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Validar si se recibe el ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Error: No se especificó ninguna tarea.'); window.location.href = 'listarTareas.php';</script>";
    exit();
}

$id = intval($_GET['id']);

// Verificar si la tarea tiene entregas asociadas
$query_verificar = "SELECT COUNT(*) as total FROM entregas WHERE id_tarea = ?";
$stmt_verificar = $conexion->prepare($query_verificar);
if (!$stmt_verificar) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

$stmt_verificar->bind_param("i", $id);
$stmt_verificar->execute();
$result = $stmt_verificar->get_result();
$row = $result->fetch_assoc();

$tieneEntregas = $row['total'] > 0;

if (!$tieneEntregas) {
    // Eliminar la tarea si no hay entregas
    $query_eliminar = "DELETE FROM tareas WHERE id = ?";
    $stmt_eliminar = $conexion->prepare($query_eliminar);
    
    if (!$stmt_eliminar) {
        die("Error en la preparación de la consulta de eliminación: " . $conexion->error);
    }

    $stmt_eliminar->bind_param("i", $id);

    if ($stmt_eliminar->execute()) {
        header("Location: listarTareas.php?msg=Tarea eliminada correctamente");
        exit();
    } else {
        header("Location: listarTareas.php?msg=Error al eliminar la tarea");
        exit();
    }
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Tarea</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <!-- Ventana Modal -->
    <div class="modal fade show d-block" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="errorModalLabel">No se puede eliminar la tarea</h5>
                </div>
                <div class="modal-body">
                    <p>La tarea no puede ser eliminada porque tiene entregas asociadas. Por favor, verifica las entregas antes de intentar eliminar la tarea.</p>
                </div>
                <div class="modal-footer">
                    <a href="listarTareas.php" class="btn btn-primary">Volver a la lista de tareas</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
