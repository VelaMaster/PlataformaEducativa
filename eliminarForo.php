<?php
// eliminarForo.php
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";

// Crear conexión
$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Verificar si el parámetro 'id' está presente en la URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];  // Obtener el ID del foro desde la URL

    // Preparar la consulta SQL para eliminar las entradas relacionadas en foro_accesodocentes
    $sql = "DELETE FROM foro_accesodocentes WHERE id_foros = ?";

    // Preparar la consulta
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('i', $id);  // Vincular el parámetro de tipo entero

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Redirigir de nuevo a listarForos.php después de eliminar
        header("Location: listarForos.php");
        exit(); // Asegurarse de que no se ejecute código adicional
    } else {
        echo "Error al eliminar el foro: " . $stmt->error;
    }

    // Cerrar la sentencia
    $stmt->close();
} else {
    echo "No se ha especificado un ID de foro válido.";
}

// Cerrar la conexión
$conexion->close();
?>
