<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
            position: relative;
            font-family: Arial, sans-serif;
            height: 100vh;
            background-image: url('/PlataformaEducativa/img/fondologin2.jpeg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }
        .role-selection {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        .role-selection label {
            background-color: sandybrown;
            color: white;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            flex: 1;
            margin: 0 5px;
            text-align: center;
        }
        .role-selection input[type="radio"] {
            display: none;
        }
        .role-selection input[type="radio"]:checked + label {
            background-color: rosybrown;
        }
        .modal {
            display: none;
            position:fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            border-radius: 5px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .logo{
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 100px;
        margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <form action="validar.php" method="post">
        <h1>Iniciar Sesión</h1>
        <img src = "/PlataformaEducativa/img/Logoito150.png" alt="logo" class="logo img-fluid">
        <div class="role-selection">
            <input type="radio" id="estudiante" name="role" value="Estudiante" required>
            <label for="estudiante">Estudiante</label>

            <input type="radio" id="docente" name="role" value="Docente" required>
            <label for="docente">Docente</label>
        </div>
        <p>
            Usuario: <input type="text" placeholder="Ingrese su usuario" name="usuario" required>
        </p>
        <p>
            Contraseña: <input type="password" placeholder="Ingrese su contraseña" name="contrasena" required>
        </p>
        <input type="submit" value="Ingresar">
    </form>
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Error al iniciar sesion</h2>
            <p>Datos incorrectos intente nuevamente.</p>
        </div>
    </div>

    <script>
        function closeModal() {
            document.getElementById('errorModal').style.display = 'none';
        }
        <?php if (isset($_GET['error']) && $_GET['error'] == 'auth'): ?>
            document.getElementById('errorModal').style.display = 'block';
        <?php endif; ?>
    </script>
</body>
</html>
