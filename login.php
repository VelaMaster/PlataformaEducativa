<?php
// Iniciar la sesión
session_start();

// Datos de conexión a la base de datos
$servername = "localhost";
$db_username = "root"; // Usuario por defecto de XAMPP
$db_password = "";     // Contraseña por defecto en XAMPP suele estar vacía
$dbname = "plataforma";

// Crear conexión
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y sanitizar los datos del formulario
    $usuario = htmlspecialchars($_POST['username']);
    $contrasena = htmlspecialchars($_POST['password']);

    // Preparar y ejecutar la consulta
    $stmt = $conn->prepare("SELECT password FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hash_contrasena);
        $stmt->fetch();

        // Verificar la contraseña
        if (password_verify($contrasena, $hash_contrasena)) {
            // Establecer variables de sesión
            $_SESSION['username'] = $usuario;
            // Redirigir a la página de dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // Contraseña incorrecta
            echo "<script>alert('Contraseña incorrecta'); window.location.href='iniciarSesion.html';</script>";
        }
    } else {
        // Usuario no encontrado
        echo "<script>alert('Usuario no encontrado'); window.location.href='iniciarSesion.html';</script>";
    }

    $stmt->close();
}

$conn->close();
?>
