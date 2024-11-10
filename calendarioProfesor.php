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
    <!-- FullCalendar CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        /* Estilos generales */
        body {
            background-color: #fae8ce;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        main {
            flex: 1;
            padding-bottom: 20px;
            padding-top: 20px;
        }

        #calendar {
            min-height: 600px;
            margin-top: 20px;
        }

           /* Estilos para el título del calendario */
           .calendar-header {
            text-align: center;
            font-family: 'Georgia', serif;
            color: #333;
            margin-bottom: 10px;
        }

        .calendar-header h2 {
            font-size: 2.5em;
            font-style: italic;
            font-weight: 400;
            margin: 0;
            color: #888; /* color gris */
        }

        /* Estilos personalizados para FullCalendar */
        .fc .fc-toolbar-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.8em;
            font-weight: 400;
            color: #000;
            font-style: italic;
            text-transform: capitalize;
        }

        .fc .fc-day-header {
            font-family: 'Poppins', sans-serif;
            font-size: 1em;
            font-weight: 600;
            color: #333;
        }

        /* Pie de página */
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: relative;
            width: 100%;
        }
    </style>
</head>
<!-- Barra de navegación -->
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
                    <a class="nav-link" href="gestionTareasProfesor.php">Gestionar tareas</a>
                </li>
            </ul>
        </div>
    </div>
 </div>
</div>
<main>
    <div class="calendar-header">
        <h2>Calendario de Tareas Asignadas</h2>
    </div>
    <div id='calendar'></div>
</main>

<!-- Incluye el archivo JavaScript compilado por Webpack -->
<script src="dist/bundle.js"></script>

<footer>
    <p>© 2024 Plataforma de Educación</p>
</footer>
</body>
</html>
