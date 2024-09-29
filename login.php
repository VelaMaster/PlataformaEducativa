<?php

session_start();
$servername = "localhost:85";
$db_username = "root";
$db_password = "";
$dbname = "plataformaweb";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['username'];
    $contrasena = $_POST['password'];

    $stmt = $conn->prepare("SELECT password FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hash_contrasena);
        $stmt->fetch();

        if (password_verify($contrasena, $hash_contrasena)) {
            $_SESSION['username'] = $usuario;
            echo "Inicio de sesión exitoso";
            header("Location: dashboard.php");
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
