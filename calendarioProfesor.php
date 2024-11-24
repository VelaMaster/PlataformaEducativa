<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario del Docente</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .navbar {
            background-color: #ff8c42;
            padding: 15px;
            text-align: center;
            color: white;
        }

        #calendar {
            max-width: 1100px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 15px 0;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <div class="navbar">
        Plataforma Educativa para Ingeniería en Sistemas
    </div>

    <!-- Contenedor del calendario -->
    <div id="calendar"></div>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales/es.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            if (!calendarEl) {
                console.error("No se encontró el elemento con ID 'calendar'. Asegúrate de que exista en el HTML.");
                return;
            }

            // Inicializar el calendario
            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: ['dayGrid'], // Plugin para vista de cuadrícula
                initialView: 'dayGridMonth', // Vista inicial
                locale: 'es', // Idioma
                headerToolbar: {
                    left: 'prev,next today', // Controles de navegación
                    center: 'title', // Título
                    right: 'dayGridMonth,timeGridWeek,timeGridDay' // Opciones de vista
                },
                events: 'obtener_tareas.php', // Ruta para cargar los eventos
                eventClick: function(info) {
                    // Mostrar información del evento
                    alert(
                        "Tarea: " + info.event.title +
                        "\nDescripción: " + (info.event.extendedProps.description || "Sin descripción") +
                        "\nFecha: " + info.event.start.toISOString()
                    );
                },
                eventDidMount: function(info) {
                    // Tooltip para mostrar el título completo
                    info.el.setAttribute("title", info.event.title);
                }
            });

            calendar.render(); // Renderizar el calendario
        });
    </script>

    <!-- Pie de página -->
    <footer>
        <p>© 2024 Plataforma de Educación para Ingeniería en Sistemas</p>
    </footer>
</body>
</html>
