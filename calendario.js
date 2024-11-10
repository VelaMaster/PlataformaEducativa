import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';

document.addEventListener('DOMContentLoaded', function() {
    let calendarEl = document.getElementById('calendar');
    let calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin],
        initialView: 'dayGridMonth',
        locale: 'es',
        events: 'obtener_tareas.php' // CALENDARIO DE PROFESOR  Cargar eventos desde el archivo PHP
    });
    calendar.render();
});
