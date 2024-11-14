<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario del Docente</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/iniciosesionalumno.css">
    <link rel="stylesheet" href="css/estilostarjetas.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css" rel="stylesheet">
    <script src="dist/bundle.js"></script>

    <style>
        /* Estilos adicionales */
        body {
            background-color: #f9f9f9;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            font-family: 'Arial', sans-serif;
        }

        /* Restauración de la barra de navegación anterior */
        .navbar {
            background-color: #ff8c42;
            padding: 10px 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            font-weight: bold;
            color: #fff !important;
            font-size: 1.2rem;
        }
        .navbar-nav .nav-link {
            color: #fff !important;
            margin-right: 15px;
            font-size: 1rem;
        }
        .navbar-nav .nav-link:hover {
            color: #f4f4f4 !important;
            background-color: #d07534;
            border-radius: 5px;
            padding: 5px 10px;
        }

        /* Título del calendario */
        .calendar-title {
            font-family: 'Georgia', serif;
            font-size: 2.5rem;
            color: #444;
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        /* Contenedor del calendario */
        #calendar {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            background-color: #fff;
        }
        .fc-toolbar-title {
            font-size: 1.8rem;
            color: #555;
            font-weight: 600;
        }
        
        /* Pie de página */
        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 15px 0;
            font-size: 0.9rem;
            width: 100%;
            margin-top: auto;
            position: relative;
            bottom: 0;
            left: 0;
        }
        footer p {
            margin: 0;
        }
    </style>
</head>

<body>
    <!-- Barra de navegación -->
<<<<<<< HEAD
    <div class="barranavegacion">
 <div class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Plataforma educativa para Ingenieria en Sistemas</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="inicioProfesor.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="calendarioProfesor.php">Calendario</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestionTareasProfesor.php">Tareas</a>
                </li>
            </ul>
        </div>
    </div>
 </div>
</div>
=======
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Plataforma Educativa para Ingeniería en Sistemas</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="inicioProfesor.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="calendarioProfesor.php">Calendario</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestionTareasProfesor.php">Gestionar Tareas</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
>>>>>>> refs/remotes/origin/main

    <!-- Contenido principal -->
    <main>
        <h2 class="calendar-title">Calendario de Tareas Asignadas</h2>
        <div id="calendar"></div>
    </main>

    <!-- Incluye el archivo JavaScript compilado por Webpack -->
    <script src="dist/bundle.js"></script>

    <!-- Pie de página -->
    <footer>
        <p>© 2024 Plataforma de Educación para Ingeniería en Sistemas</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
