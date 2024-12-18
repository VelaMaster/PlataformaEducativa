<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calendario</title>
  <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/barradeNavegacion.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/calendarioEstilo.css?v=<?php echo time(); ?>">
</head>
<body>
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
                    <a class="nav-link"href="inicioProfesor.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="calendarioDocente.php">Calendario</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestionTareasProfesor.php">Asignar tareas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestionForosProfesor.php">Asignar foros</a> <!-- Nueva opción -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="calificarTareas.php">Calificar tareas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="calificarForos.php">Calificar foros</a>  
                </li>
            </ul>
        </div>
    </div>
 </div>
</div>

<div class="calendario-container">
  <!-- Cabecera del calendario -->
  <div class="calendario-header">
    <button id="prev">&lt;</button>
    <h2 id="monthYear">Noviembre 2024</h2>
    <button id="next">&gt;</button>
  </div>

  <!-- Cuerpo del calendario -->
  <div class="calendario-body" id="calendarioBody">
    <!-- Los días y eventos se llenarán dinámicamente -->
  </div>
</div>
<!-- Modal para eventos -->
<div class="modal-calendario" id="modalEvento">
  <div class="modal-contenido">
    <h3 id="nombreCurso">Nombre del Curso</h3>
    <p><strong>Nombre tarea:</strong> <span id="tituloEvento">Título del Evento</span></p>
    <p><strong>Descripción:</strong> <span id="descripcionEvento">Descripción del evento.</span></p>
    <p><strong>Fecha de Asignación:</strong> <span id="fechaAsignacionEvento"></span></p>
    <p><strong>Fecha Límite:</strong> <span id="fechaLimiteEvento"></span></p>
    <div class="modal-buttons">
      <button id="cerrarModal" class="btn btn-secondary">Cerrar</button>
      <button id="verDetalles" class="btn btn-primary">Ver detalles</button>
    </div>
  </div>
</div>


<!-- Modal para agregar tareas -->
<div class="modal-calendario" id="modalAgregar">
  <div class="modal-contenido">
    <h3>¿Agregar tarea?</h3>
    <p>Este día no tiene ninguna tarea asignada. ¿Deseas agregar una nueva tarea?</p>
    <button id="btnAgregarTarea" class="btn btn-primary">Agregar Tarea</button>
    <button id="cerrarAgregar" class="btn btn-secondary">Cerrar</button>
  </div>
</div>

<script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
<script>
async function fetchEventos() {
  try {
    const response = await fetch("fetch_events.php");
    eventos = await response.json();
    console.log(eventos); // Revisa en la consola los datos devueltos
    renderCalendar(currentDate);
  } catch (error) {
    console.error("Error al cargar los eventos:", error);
  }
}
const calendarioBody = document.getElementById("calendarioBody");
const monthYear = document.getElementById("monthYear");
const modalEvento = document.getElementById("modalEvento");
const modalAgregar = document.getElementById("modalAgregar");
const tituloEvento = document.getElementById("tituloEvento");
const descripcionEvento = document.getElementById("descripcionEvento");
const cerrarModal = document.getElementById("cerrarModal");
const cerrarAgregar = document.getElementById("cerrarAgregar");
const btnAgregarTarea = document.getElementById("btnAgregarTarea");

let currentDate = new Date();
let eventos = []; // Este array se llenará dinámicamente con fetch

// Función para cargar eventos dinámicamente desde fetch_events.php
async function fetchEventos() {
  try {
    const response = await fetch("fetch_events.php");
    eventos = await response.json();
    renderCalendar(currentDate);
  } catch (error) {
    console.error("Error al cargar los eventos:", error);
  }
}

// Función para renderizar el calendario
function renderCalendar(date) {
  calendarioBody.innerHTML = ""; // Limpiar el contenido del calendario
  const year = date.getFullYear();
  const month = date.getMonth();
  const firstDay = new Date(year, month, 1).getDay();
  const lastDate = new Date(year, month + 1, 0).getDate();
  const daysOfWeek = ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"];

  // Renderizar encabezados de días de la semana
  daysOfWeek.forEach(day => {
    const dayHeader = document.createElement("div");
    dayHeader.classList.add("calendario-dia", "calendario-dia-header");
    dayHeader.textContent = day;
    calendarioBody.appendChild(dayHeader);
  });

  // Añadir días vacíos antes del primer día del mes
  for (let i = 0; i < firstDay; i++) {
    const emptyDay = document.createElement("div");
    emptyDay.classList.add("calendario-dia");
    calendarioBody.appendChild(emptyDay);
  }

// Renderizar los días del mes
for (let day = 1; day <= lastDate; day++) {
  const dayElement = document.createElement("div");
  dayElement.classList.add("calendario-dia");

  const dateStr = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
  const eventosDelDia = eventos.filter(e => e.start === dateStr);

  // Agregar fecha encima de las tareas
  const dateHeader = document.createElement("div");
  dateHeader.classList.add("calendario-dia-header");
  dateHeader.textContent = day;
  dayElement.appendChild(dateHeader);

  // Agregar marcador de tareas si existen
  eventosDelDia.forEach(evento => {
    const eventElement = document.createElement("div");
    eventElement.classList.add("evento", `materia-${evento.id_curso}`);
    eventElement.textContent = evento.title;
    dayElement.appendChild(eventElement);

// Mostrar modal de detalles al hacer clic en el evento
eventElement.addEventListener("click", async (e) => {
  e.stopPropagation(); // Evitar que el clic en la tarea dispare el clic del día
  try {
    const response = await fetch(`fetch_task_details.php?id=${evento.id}`);
    const taskDetails = await response.json();

    if (taskDetails.error) {
      alert("Error al cargar los detalles de la tarea.");
      return;
    }

    // Actualizar el contenido del modal
    document.getElementById("nombreCurso").textContent = evento.curso; // Mostrar el nombre del curso
    document.getElementById("tituloEvento").textContent = taskDetails.titulo; // Título de la tarea
    document.getElementById("descripcionEvento").textContent = taskDetails.descripcion; // Descripción de la tarea
    document.getElementById("fechaAsignacionEvento").textContent = taskDetails.fecha_creacion; // Fecha de creación
    document.getElementById("fechaLimiteEvento").textContent = taskDetails.fecha_limite; // Fecha límite

    // Configurar el botón "Ver detalles"
    const verDetallesButton = document.getElementById("verDetalles");
    verDetallesButton.onclick = () => {
      window.open(`verTarea.php?id=${evento.id}`, "_blank");
    };

    // Mostrar el modal
    modalEvento.classList.add("active");
  } catch (error) {
    console.error("Error al obtener los detalles de la tarea:", error);
    alert("Ocurrió un error al obtener los detalles de la tarea.");
  }
});


  });

  // Mostrar modal de agregar tarea al hacer clic en el día
  dayElement.addEventListener("click", () => {
    btnAgregarTarea.setAttribute("data-date", dateStr); // Guardar la fecha seleccionada
    modalAgregar.classList.add("active");
  });

  calendarioBody.appendChild(dayElement);
}


  // Actualizar encabezado de mes y año
  monthYear.textContent = `${date.toLocaleString("es-ES", { month: "long" })} ${year}`;
}

// Cambiar al mes anterior
document.getElementById("prev").addEventListener("click", () => {
  currentDate.setMonth(currentDate.getMonth() - 1);
  renderCalendar(currentDate);
});

// Cambiar al mes siguiente
document.getElementById("next").addEventListener("click", () => {
  currentDate.setMonth(currentDate.getMonth() + 1);
  renderCalendar(currentDate);
});

// Cerrar el modal de detalles
cerrarModal.addEventListener("click", () => {
  modalEvento.classList.remove("active");
});

// Cerrar el modal de agregar tarea
cerrarAgregar.addEventListener("click", () => {
  modalAgregar.classList.remove("active");
});

// Redirigir al formulario para agregar tarea
btnAgregarTarea.addEventListener("click", () => {
  const selectedDate = btnAgregarTarea.getAttribute("data-date");
  window.location.href = `gestionTareasProfesor.php?date=${selectedDate}`;
});

// Cargar eventos y renderizar el calendario
fetchEventos();

</script>


</body>
</html>