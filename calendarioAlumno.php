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

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        /* Barra de navegación */
        header nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: space-around;
            align-items: center;
            background-color: #ff7f27;
            padding: 10px 0;
            border-radius: 0 0 8px 8px;
        }
        header nav ul li a {
            text-decoration: none;
            color: white;
            font-weight: bold;
            padding: 5px 15px;
        }
        header nav ul li a:hover {
            background-color: #ffa64d;
            border-radius: 4px;
        }

        /* Contenedor del calendario */
        .calendario-container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            background-color: #ffe4c4;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        #calendar {
            background-color: #ffe4c4;
            border-radius: 8px;
            padding: 20px;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .modal-content h2 {
            color: #ff7f27;
        }

        .modal-content p {
            margin: 10px 0;
        }

        .modal-content button {
            background-color: #ff7f27;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin: 5px;
        }

        .modal-content button:hover {
            background-color: #ff9f43;
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

    <!-- Barra de navegación -->
    <header> 
        <nav>   
            <ul>
                <li><a href="inicioAlumno.php">Inicio</a></li>
                <li><a href="calendarioAlumno.php">Calendario</a></li>
                <li><a href="gestionTareasAlumno.php">Tareas</a></li>
            </ul>
        </nav>
    </header>

    <h1 style="text-align: center; margin: 20px 0;">Calendario de Tareas</h1>
    
    <!-- Contenedor del calendario -->
    <div class="calendario-container">
        <div id='calendar'></div>
    </div>

    <!-- Modal -->
    <div id="taskModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modalTitle">Título de la Tarea</h2>
            <p><strong>Nombre tarea:</strong> <span id="modalTaskName"></span></p>
            <p><strong>Descripción:</strong> <span id="modalDescription"></span></p>
            <p><strong>Fecha de Asignación:</strong> <span id="modalStartDate"></span></p>
            <button id="closeModalButton">Cerrar</button>
            <button id="detailsButton">Ver detalles</button>
        </div>
    </div>

    <!-- Inicialización de FullCalendar -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const modal = document.getElementById('taskModal');
            const closeModal = document.querySelector('.close');
            const closeModalButton = document.getElementById('closeModalButton');
            const modalTitle = document.getElementById('modalTitle');
            const modalTaskName = document.getElementById('modalTaskName');
            const modalDescription = document.getElementById('modalDescription');
            const modalStartDate = document.getElementById('modalStartDate');
            const detailsButton = document.getElementById('detailsButton');
            let currentTaskId = null;

            // Inicializamos FullCalendar
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: async function(fetchInfo, successCallback, failureCallback) {
    try {
        const response = await fetch('fetch_events2.php');
        if (!response.ok) {
            throw new Error('Error al cargar eventos');
        }
        const data = await response.json();
        successCallback(data);
    } catch (error) {
        console.error('Error:', error);
        failureCallback(error);
    }
},

                eventClick: function(info) {
                    info.jsEvent.preventDefault(); // Evita redirección inmediata

                    // Rellenar datos del modal
                    currentTaskId = info.event.id;
                    modalTitle.textContent = info.event.title;
                    modalTaskName.textContent = info.event.extendedProps.nombre_tarea || 'Sin nombre';
                    modalDescription.textContent = info.event.extendedProps.descripcion || 'Sin descripción';
                    modalStartDate.textContent = info.event.startStr || 'Sin fecha de asignación';
                    
                    // Mostrar el modal
                    modal.style.display = 'block';
                },
                locale: 'es',
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    day: 'Día'
                }
            });

            calendar.render();

            // Cerrar el modal
            closeModal.onclick = function() {
                modal.style.display = 'none';
            };

            closeModalButton.onclick = function() {
                modal.style.display = 'none';
            };

            window.onclick = function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            };

            // Redirigir a la página de detalles
            detailsButton.onclick = function() {
                if (currentTaskId) {
                    window.location.href = `tarea.php?id=${currentTaskId}`;
                }
            };
        });
    </script>
</body>
</html>
