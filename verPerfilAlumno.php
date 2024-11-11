<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
$num_control = $_SESSION['usuario'];
$conexion = mysqli_connect("localhost", "root", "", "peis");
if (!$conexion) {
    die("Conexión fallida: " . mysqli_connect_error());
}
$query = "SELECT nombre, segundo_nombre, apellido_p, apellido_m, correo FROM alumnos WHERE num_control = ?";
$stmt = mysqli_prepare($conexion, $query);
mysqli_stmt_bind_param($stmt, 's', $num_control);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $nombre, $segundo_nombre, $apellido_p, $apellido_m, $correo);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi perfil</title>
    <link rel="stylesheet" href="css/miPerfilEditarAlumno.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<form>
    <div class="profile-container">
        <a href="#" id="perfilDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="img/perfil120.png" alt="Foto de perfil" class="profile-img">
        </a>
    </div>
    <br>
    <h4>Bienvenido, <?php echo $nombre . ' ' . $segundo_nombre . ' ' . $apellido_m . ' ' . $apellido_p; ?></h4>
    <br>
    <div class="mb-3">
        <label for="txtNombre" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="txtNombre" value="<?php echo $nombre . ' ' . $segundo_nombre . ' ' . $apellido_m . ' ' . $apellido_p; ?>" readonly>
    </div>
    <div class="mb-3">
        <label for="txtNumControl" class="form-label">Número de control</label>
        <input type="text" class="form-control" id="txtNumControl" value="<?php echo $num_control; ?>" readonly>
    </div>
    <div class="mb-3">
        <label for="txtCorreo" class="form-label">Correo electrónico</label>
        <input type="email" class="form-control" id="txtCorreo" value="<?php echo $correo; ?>" readonly>
    </div>
    <br>
    <div class="botones">
        <button type="button" class="btn btn-success" onclick='window.location.href = "editarperfilAlumno.php"'>Editar mis datos</button>
    </div>
</form>
</body>
</html>
