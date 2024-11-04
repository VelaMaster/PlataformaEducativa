<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Tareas</title>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/main.min.css" rel="stylesheet" />

    <!-- FullCalendar JS desde otra CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.4/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.4/index.global.min.js"></script>

    <!-- Mantén tu archivo CSS personalizado -->
    <link rel="stylesheet" href="css/estiloCalendario.css">
</head>
<body>

    <header> 
        <nav>   
            <ul>
                <li><a href="inicioAlumno.php">Inicio</a></li>
                <li><a href="calendarioAlumno.php">Calendario</a></li>
                <li><a href="">Tareas</a></li>
            </ul>
        </nav>
    </header>

    <h1>Calendario de Tareas</h1>
    
    <div id='calendar'></div> <!-- Aquí va el calendario -->

    <!-- Inicialización de FullCalendar -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            // Inicializamos FullCalendar usando las nuevas referencias
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',  // Vista mensual por defecto
                headerToolbar: {              // Barra de navegación
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: [  // Eventos de prueba
                    {
                        title: 'Tarea 1',
                        start: '2024-10-01',
                        url: 'tarea.html?id=1',
                        backgroundColor: 'red'
                    },
                    {
                        title: 'Tarea 2',
                        start: '2024-10-05',
                        url: 'tarea.html?id=2',
                        backgroundColor: 'blue'
                    }
                ]
            });

            calendar.render();  // Renderiza el calendario
        });
    </script>

</body>
</html>
