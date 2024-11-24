<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Verificar si la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger y sanitizar el mensaje desde el formulario
    $mensaje = htmlspecialchars(trim($_POST['mensaje']));

    // Validar que el mensaje no esté vacío
    if (empty($mensaje)) {
        header("Location: soporte.php?error=1");
        exit();
    }

    // Obtener datos del usuario desde la sesión
    $nombreUsuario = $_SESSION['nombre'] ?? 'Usuario no registrado';
    $numControl = $_SESSION['num_control'] ?? 'Sin número de control';

    // Incluir Composer autoload
    require 'vendor/autoload.php';
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP de Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';                   // Servidor SMTP de Gmail
        $mail->SMTPAuth   = true;                               // Activar autenticación SMTP
        $mail->Username   = 'trabajospruebas075@gmail.com';     // Tu correo de Gmail
        $mail->Password   = 'eihqhmtdcnadhygp';                // Contraseña de aplicación de Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;     // Habilitar encriptación TLS
        $mail->Port       = 587;                                // Puerto TCP para SMTP

        // Configuración del correo
        $mail->setFrom('trabajospruebas075@gmail.com', 'Plataforma Educativa'); // Remitente
        $mail->addAddress('pdiegovela@gmail.com');                           // Destinatario

        // Contenido del correo
        $mail->isHTML(false); // Usar formato de texto plano
        $mail->Subject = 'Nuevo mensaje de contacto de la plataforma';
        $mail->Body = "Has recibido un nuevo mensaje de contacto:\n\n" .
                     "Nombre del usuario: $nombreUsuario\n" .
                     "Número de control: $numControl\n\n" .
                     "Mensaje:\n$mensaje";

        // Enviar el correo
        $mail->send();
        header("Location: soporte.php?success=1");
        exit();
    } catch (Exception $e) {
        // Manejar errores de PHPMailer
        error_log("Error al enviar el correo: " . $mail->ErrorInfo);
        header("Location: soporte.php?error=2");
        exit();
    }
} else {
    // Redirigir si no es una solicitud POST
    header("Location: soporte.php");
    exit();
}
?>
