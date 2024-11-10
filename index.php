<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/estilosIndex.css">
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
            integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <title>Iniciar Sesion</title>
    </head>

    <body>
        <div class="container" id="container">
            <div class="form-contianer sing-up">
                 <form method="POST" action="validar.php"> <!-- Asegúrate de que el método y la acción sean correctos -->
                    <h1>Docente</h1>
                    <h1>Iniciar Sesión</h1>
                    <img src="img/Logoito120.png" alt="logo" class="logo img-fluid">
                    <br>
                    <input type="text" name="usuario" placeholder="Usuario" required> <!-- Añadir el atributo name -->
                    <input type="password" name="contrasena" placeholder="Contraseña" required> <!-- Cambiar a type="password" y añadir name -->
                    <input type="hidden" name="role" value="Docente"> <!-- Añadir campo oculto para rol -->
                    <br>
                    <button type="submit">Iniciar sesión</button>
                </form>
            </div>
            <div class="form-contianer sing-in">
            <form method="POST" action="validar.php"> <!-- Añadir método y acción -->
                    <h1>Estudiante</h1>
                    <h1>Iniciar Sesión</h1>
                    <img src="img/Logoito120.png" alt="logo" class="logo img-fluid">
                    <br>
                    <input type="text" name="usuario" placeholder="Usuario" required>
                    <input type="password" name="contrasena" placeholder="Contraseña" required>
                    <input type="hidden" name="role" value="Estudiante"> <!-- Añadir campo oculto para rol -->
                    <br>
                    <button type="submit">Iniciar sesión</button>
                </form>
            </div>

            <div class="toggle-container">
                <div class="toggle">
                    <div class="toggle-panel toggle-left">
                        <h1>Bienvenido! Docente</h1>
                        <p>Introdusca sus credenciales</p>
                        <button class="hidden" id="login">Soy estudiante</button>
                    </div>
                    <div class="toggle-panel toggle-right">
                        <h1>Hola!</h1>
                        <p>Inicie sesion para poder usar todas las funiconalidades</p>
                        <button class="hidden" id="register">Soy Profesor</button>
                    </div>
                </div>

            </div>

        </div>

        <script src="js/ScriptIndex.js"></script>
    </body>

</html>