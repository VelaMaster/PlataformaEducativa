<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi perfil</title>
    <link rel="stylesheet" href="css/miPerfilEditarAlumno.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<form>
<div class="profile-container">
    <a href="#" id="perfilDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="img/Logoito120.png" alt="Foto de perfil" class="profile-img">
    </a>
</div>
<br>
    <h4>Bienvenido, Diego Perez Barrios</h4>
    <br>
        <div class="mb-3">
          <label for="text" class="form-label">Nombre</label>
          <input type="number" class="form-control" id="txtEdad" readonly>
        </div>
        <div class="mb-3">
          <label for="text" class="form-label">Numero de control</label>
          <input type="number" class="form-control" id="txtEdad" readonly>
        </div>
        <div class="mb-3">
          <label for="text" class="form-label">Correo electronico</label>
          <input type="text" class="form-control" id="txtVerificar" readonly>
        </div>
        <br>
        <div class = "botones">
        <button type="button" class="btn btn-success" onclick='verificarEdad()'>Editar mis datos</button>
        </div>

</form>
</body>
</html>