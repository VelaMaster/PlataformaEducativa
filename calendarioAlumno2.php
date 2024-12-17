<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Tareas</title>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/main.min.css" rel="stylesheet" />
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.4/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.4/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.4/index.global.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0d7af;
            margin: 0;
            padding: 0;
        }
        #calendar {
            max-width: 1100px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffe4c4;
            border-radius: 10px;
        }
    </style>
</head>
<body>

    <h1 style="text-align: center; margin: 20px 0;">Calendario de Tareas</h1>

    <!-- Contenedor del calendario -->
    <div id="calendar"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                events: 'fetch_events.php', // Cargar eventos desde el backend PHP
                eventClick: function(info) {
                    alert(`Tarea: ${info.event.title}\nDescripción: ${info.event.extendedProps.descripcion}`);
                },
                locale: 'es', // Configurar el idioma a español
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                }
            });

            calendar.render();
        });
    </script>
</body>
</html>
