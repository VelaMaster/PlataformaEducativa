<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
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
    </style>
</head>
<body>
    <form action="validar.php" method="post">
        <h1>Iniciar Sesión</h1>
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
