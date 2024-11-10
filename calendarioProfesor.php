<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario del Docente</title>
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/iniciosesionalumno.css">
    <link rel="stylesheet" href="css/estilostarjetas.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css" rel="stylesheet">
    <style>
        /* Estilos adicionales */
        .calendar-title {
            font-family: Georgia, serif;
            font-size: 2.5rem;
            font-style: italic;
            color: #777;
            text-align: center;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .fc-toolbar-title {
            font-family: Arial, sans-serif;
            color: #333;
            font-size: 1.5rem;
            font-style: italic;
        }
    </style>
</head>
<body>
<div class="barranavegacion">
    <div class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Plataforma educativa para Ingeniería en Sistemas</a>
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
                        <a class="nav-link" href="gestionTareasProfesor.php">Gestionar tareas</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<main>
    <h2 class="calendar-title">Calendario de Tareas Asignadas</h2>
    <div id='calendar'></div>
</main>

<script src="dist/bundle.js"></script>

<footer>
    <p>© 2024 Plataforma de Educación</p>
</footer>
</body>
</html>
        