<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
$nombreUsuario = $_SESSION['nombre'] ?? 'Alumno no registrado';
$numControl = $_SESSION['num_control'] ?? 'Sin número de control';

// Obtener parámetros de la URL
$success = isset($_GET['success']) ? $_GET['success'] : null;
$error = isset($_GET['error']) ? $_GET['error'] : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda y Soporte</title>
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/barradeNavegacion.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/soporte.css?v=<?php echo time(); ?>">

    <style>
        .section {
            display: none;
            margin-top: 20px;
        }
        .active-section {
            display: block;
        }
    </style>
</head>
<body>
    <!-- Barra de Navegación -->
    <div class="barranavegacion">
        <div class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Plataforma Educativa para Ingeniería en Sistemas</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" 
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="inicioAlumno.php">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="calendarioAlumno.php">Calendario</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="gestionTareasAlumno.php">Tareas</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="container mt-5">
        <h2 class="mb-4">Ayuda y Soporte</h2>
        <div class="d-flex mb-4">
            <button id="btn-faq" class="btn btn-primary me-2">FAQ</button>
            <button id="btn-contacto" class="btn btn-secondary">Contáctanos</button>
        </div>

        <!-- Sección FAQ -->
        <div id="section-faq" class="section active-section">
            <h3>Preguntas Frecuentes</h3>
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            ¿Cómo puedo ver mis tareas asignadas?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" 
                         aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Para ver tus tareas asignadas, ve a la sección "Tareas" en el menú principal. Allí encontrarás una lista de todas las tareas con sus respectivas fechas de entrega y detalles.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            ¿Cómo puedo enviar una tarea?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" 
                         aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Para enviar una tarea, dirígete a la sección "Tareas", selecciona la tarea correspondiente y haz clic en "Enviar". Completa el formulario de envío y adjunta los archivos necesarios.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            ¿Cómo puedo recuperar mi contraseña?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" 
                         aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Si has olvidado tu contraseña, dirígete a la página de inicio de sesión y haz clic en "¿Olvidaste tu contraseña?". Sigue las instrucciones para restablecerla.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección Contáctanos -->
        <div id="section-contacto" class="section">
            <h3>Contáctanos</h3>
            <form id="contacto-form" action="enviar_contacto.php" method="POST">
                <div class="mb-3">
                    <label for="mensaje" class="form-label">Descripción del problema</label>
                    <textarea class="form-control" id="mensaje" name="mensaje" rows="5" required></textarea>
                </div>
                <!-- Cambiar el botón de submit para que abra el modal -->
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmarEnvioModal">
                    Enviar
                </button>
            </form>
            <div class="mt-4 text-center">
                <a href="https://www.facebook.com/profile.php?id=100064684089409" target="_blank" class="icon-link facebook me-3">
                    <i class="bi bi-facebook"></i>
                </a>
                <a href="https://github.com/VelaMaster/PlataformaEducativa" target="_blank" class="icon-link github">
                    <i class="bi bi-github"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div class="modal fade" id="confirmarEnvioModal" tabindex="-1" aria-labelledby="confirmarEnvioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Envío</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas enviar este mensaje?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="confirmar-envio-btn">Enviar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Envío Exitoso -->
    <div class="modal fade" id="mensajeEnviadoModal" tabindex="-1" aria-labelledby="mensajeEnviadoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <p>¡Tu mensaje ha sido enviado exitosamente!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Error en el Envío -->
    <div class="modal fade" id="errorEnvioModal" tabindex="-1" aria-labelledby="errorEnvioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <p>Hubo un error al enviar tu mensaje. Por favor, intenta nuevamente.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
    <script>
        // Alternar entre las secciones FAQ y Contáctanos
        document.getElementById('btn-faq').addEventListener('click', function() {
            document.getElementById('section-faq').classList.add('active-section');
            document.getElementById('section-contacto').classList.remove('active-section');
            this.classList.add('btn-primary');
            this.classList.remove('btn-secondary');
            document.getElementById('btn-contacto').classList.remove('btn-primary');
            document.getElementById('btn-contacto').classList.add('btn-secondary');
        });

        document.getElementById('btn-contacto').addEventListener('click', function() {
            document.getElementById('section-contacto').classList.add('active-section');
            document.getElementById('section-faq').classList.remove('active-section');
            this.classList.add('btn-primary');
            this.classList.remove('btn-secondary');
            document.getElementById('btn-faq').classList.remove('btn-primary');
            document.getElementById('btn-faq').classList.add('btn-secondary');
        });

        // Manejar el envío del formulario desde el modal de confirmación
        document.getElementById('confirmar-envio-btn').addEventListener('click', function() {
            // Cerrar el modal de confirmación
            var confirmarModal = bootstrap.Modal.getInstance(document.getElementById('confirmarEnvioModal'));
            confirmarModal.hide();

            // Enviar el formulario
            document.getElementById('contacto-form').submit();
        });

        // Función para obtener parámetros de la URL
        function getQueryParam(param) {
            let urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(param);
        }

        // Mostrar el modal correspondiente según el resultado del envío
        window.addEventListener('DOMContentLoaded', (event) => {
            const success = getQueryParam('success');
            const error = getQueryParam('error');

            if (success == '1') {
                var mensajeEnviadoModal = new bootstrap.Modal(document.getElementById('mensajeEnviadoModal'));
                mensajeEnviadoModal.show();

                // Cerrar el modal después de 3 segundos
                setTimeout(() => {
                    mensajeEnviadoModal.hide();
                }, 3000);
            }

            if (error == '1' || error == '2') {
                var errorEnvioModal = new bootstrap.Modal(document.getElementById('errorEnvioModal'));
                errorEnvioModal.show();

                // Cerrar el modal después de 3 segundos
                setTimeout(() => {
                    errorEnvioModal.hide();
                }, 3000);
            }
        });
    </script>
</body>
</html>
