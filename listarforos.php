<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foros Asignados</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/listarForos.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Foros Asignados</h1>

        <!-- Filtros -->
        <div class="mb-4">
            <div class="row">
                <div class="col-md-6">
                    <label for="filtro-materia" class="form-label">Filtrar por Materia:</label>
                    <select id="filtro-materia" class="form-select" onchange="filtrarForos()">
                        <option value="">Todas las Materias</option>
                        <option value="Ingeniería de Software">Ingeniería de Software</option>
                        <option value="Redes de Computadoras">Redes de Computadoras</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="filtro-tipo" class="form-label">Filtrar por tipo de foro:</label>
                    <select id="filtro-tipo" class="form-select" onchange="filtrarForos()">
                        <option value="">Todos los tipos</option>
                        <option value="General">General</option>
                        <option value="Temático">Privado</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Foro Cards -->
        <div class="foro-card" data-materia="Ingeniería de Software" data-tipo="General" onclick="toggleButtons(this)">
            <h3>Materia: Ingeniería de Software</h3>
            <p><strong>Título del Foro:</strong> Planificación Ágil</p>
            <p><strong>Descripción:</strong> Foro para discutir estrategias y herramientas en metodologías ágiles.</p>
            <p class="tipo-foro">Tipo: General</p>
            <div class="foro-buttons">
                <button onclick="alert('Ver Foro')">Ver</button>
                <button onclick="alert('Editar Foro')">Editar</button>
                <button onclick="alert('Eliminar Foro')">Eliminar</button>
            </div>
        </div>

        <div class="foro-card" data-materia="Redes de Computadoras" data-tipo="Temático" onclick="toggleButtons(this)">
            <h3>Materia: Redes de Computadoras</h3>
            <p><strong>Título del Foro:</strong> Seguridad en Redes</p>
            <p><strong>Descripción:</strong> Discusión sobre los desafíos actuales en la seguridad de redes.</p>
            <p class="tipo-foro">Tipo: Privado</p>
            <div class="foro-buttons">
                <button onclick="alert('Ver Foro')">Ver</button>
                <button onclick="alert('Editar Foro')">Editar</button>
                <button onclick="alert('Eliminar Foro')">Eliminar</button>
            </div>
        </div>
    </div>

    <script>
        // Función para alternar botones
        function toggleButtons(card) {
            const allCards = document.querySelectorAll('.foro-card');
            allCards.forEach(c => {
                if (c !== card) {
                    c.classList.remove('active');
                }
            });
            card.classList.toggle('active');
        }

        // Función para filtrar foros
        function filtrarForos() {
            const filtroMateria = document.getElementById('filtro-materia').value.toLowerCase();
            const filtroTipo = document.getElementById('filtro-tipo').value.toLowerCase();
            const tarjetas = document.querySelectorAll('.foro-card');

            tarjetas.forEach(tarjeta => {
                const materia = tarjeta.getAttribute('data-materia').toLowerCase();
                const tipo = tarjeta.getAttribute('data-tipo').toLowerCase();

                if (
                    (filtroMateria === "" || materia === filtroMateria) &&
                    (filtroTipo === "" || tipo === filtroTipo)
                ) {
                    tarjeta.style.display = "block";
                } else {
                    tarjeta.style.display = "none";
                }
            });
        }
    </script>
</body>
</html>
