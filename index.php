<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/estilosIndex.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
        // Función para obtener el valor de un parámetro en la URL
        function getParameterByName(name) {
            name = name.replace(/[\[\]]/g, '\\$&');
            var url = window.location.href;
            var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)');
            var results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, ' '));
        }
        window.onload = function() {
            var error = getParameterByName('error');
            if (error === 'auth') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos vacíos',
                    text: 'Por favor, complete todos los campos.',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    // Eliminar el parámetro 'error' de la URL
                    window.history.replaceState(null, null, window.location.pathname);
                });
            }
        };
    </script>
    <script>
    window.onload = function() {
        var error = getParameterByName('error');
        if (error === 'auth') {
            Swal.fire({
                icon: 'error',
                title: 'Datos incorrectos',
                text: 'El usuario o la contraseña son incorrectos.',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#FF9800', // Color naranja para el botón
                background: '#f0f0f0', // Color del fondo de la alerta
                backdrop: 'rgba(0, 0, 0, 0.8)', // Fondo tenue para evitar que el formulario se desplace
                allowOutsideClick: false, // Evita que la alerta se cierre al hacer clic fuera
                allowEscapeKey: false, // Evita que la alerta se cierre con la tecla Escape
                focusConfirm: false // Evita que el botón de confirmación reciba el foco automáticamente
            }).then(() => {
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        
        }
    };
</script>

    </body>

</html>