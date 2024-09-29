<?php
// Iniciar la sesión
session_start();

// Datos de conexión a la base de datos
$servername = "localhost:85";
$db_username = "root"; // Usuario por defecto de XAMPP
$db_password = "";     // Contraseña por defecto en XAMPP suele estar vacía
$dbname = "plataformaweb";

// Crear conexión
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['username'];
    $contrasena = $_POST['password'];

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
            echo "Inicio de sesión exitoso";
            // Redirigir a otra página
            header("Location: dashboard.php"); // Asegúrate de crear este archivo
            exit();
        } else {
            echo "Contraseña incorrecta";
        }
    } else {
        echo "Usuario no encontrado";
    }

    $stmt->close();
}

$conn->close();
?>
