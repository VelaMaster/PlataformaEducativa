<?php 
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";

// Crear conexión utilizando mysqli con manejo de errores mejorado
$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Establecer conjunto de caracteres a UTF-8 para evitar problemas de codificación
$conexion->set_charset("utf8");

// Obtener 'id_tarea' de los parámetros GET y asegurarse de que sea un entero válido
$id_tarea = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_tarea > 0) {
    session_start();
    
    // Asegurarse de que 'usuario' está definido en la sesión y es un entero
    $id_alumno = isset($_SESSION['usuario']) ? (int)$_SESSION['usuario'] : 0;

    if ($id_alumno > 0) {
        // Preparar la consulta SQL para obtener la tarea específica del alumno
        $sql = "SELECT tareas.*, grupo_alumnos.num_control
                FROM tareas
                JOIN grupos ON grupos.id_curso = tareas.id_curso
                JOIN grupo_alumnos ON grupo_alumnos.id_grupo = grupos.id
                WHERE tareas.id = ? AND grupo_alumnos.num_control = ?";
        $stmt = $conexion->prepare($sql);
        if ($stmt === false) {
            die("Error al preparar la consulta: " . $conexion->error);
        }

        // Vincular parámetros con tipos 'ii' (entero, entero)
        $stmt->bind_param("ii", $id_tarea, $id_alumno);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows > 0) {
            $tarea = $resultado->fetch_assoc();

            // Consulta para verificar si ya se entregó la tarea
            $sqlEntrega = "SELECT archivo_entrega FROM entregas WHERE id_tarea = ? AND id_alumno = ?";
            $stmtEntrega = $conexion->prepare($sqlEntrega);
            if ($stmtEntrega === false) {
                die("Error al preparar la consulta de entrega: " . $conexion->error);
            }
            $stmtEntrega->bind_param("ii", $id_tarea, $id_alumno);
            $stmtEntrega->execute();
            $resultadoEntrega = $stmtEntrega->get_result();
            $entregado = $resultadoEntrega && $resultadoEntrega->num_rows > 0;

            // Función para obtener el nombre del curso
            function obtenerNombreMateria($id_curso, $conexion) {
                $consulta = "SELECT nombre_curso FROM cursos WHERE id = ?";
                $stmtMateria = $conexion->prepare($consulta);
                if ($stmtMateria === false) {
                    return "Desconocido";
                }
                $stmtMateria->bind_param("i", $id_curso);
                $stmtMateria->execute();
                $resultado = $stmtMateria->get_result();
                if ($resultado && $resultado->num_rows > 0) {
                    $fila = $resultado->fetch_assoc();
                    return $fila['nombre_curso'];
                } else {
                    return "Desconocido";
                }
            }

            $nombre_materia = obtenerNombreMateria($tarea['id_curso'], $conexion);

            // Consultar la rúbrica asociada
            $sqlRubrica = "SELECT * FROM rubricas WHERE id_tarea = ?";
            $stmtRubrica = $conexion->prepare($sqlRubrica);
            if ($stmtRubrica === false) {
                die("Error al preparar la consulta de rúbrica: " . $conexion->error);
            }
            $stmtRubrica->bind_param("i", $id_tarea);
            $stmtRubrica->execute();
            $resultadoRubrica = $stmtRubrica->get_result();
            $rubrica = [];
            if ($resultadoRubrica && $resultadoRubrica->num_rows > 0) {
                while ($fila = $resultadoRubrica->fetch_assoc()) {
                    $rubrica[] = $fila;
                }
            }

            // Consulta para obtener la calificación y retroalimentación
            $sqlCalificacion = "SELECT calificacion, retroalimentacion
                                FROM entregas 
                                WHERE id_tarea = ? AND id_alumno = ?";
            $stmtCalificacion = $conexion->prepare($sqlCalificacion);
            if ($stmtCalificacion === false) {
                die("Error al preparar la consulta de calificación: " . $conexion->error);
            }
            $stmtCalificacion->bind_param("ii", $id_tarea, $id_alumno);
            $stmtCalificacion->execute();
            $resultadoCalificacion = $stmtCalificacion->get_result();

            // Inicializar variables
            $calificacion = null;
            $retroalimentacion = null;

            if ($resultadoCalificacion && $resultadoCalificacion->num_rows > 0) {
                $filaCalificacion = $resultadoCalificacion->fetch_assoc();
                $calificacion = $filaCalificacion['calificacion'];
                $retroalimentacion = $filaCalificacion['retroalimentacion'];
            } else {
                // No se encontraron datos
                $calificacion = null;
                $retroalimentacion = null;
            }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Tarea</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/estiloTarea.css">
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css">
</head>
<style>
    body{
        background-color: #e7d6bf;
        font-family: Arial, sans-serif;
    }
    .container {
        max-width: 800px;
        margin: 50px auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.15);
    }
    .detail-item {
        margin-bottom: 15px;
        display: flex;
    }
    .detail-label {
        font-weight: bold;
        width: 200px;
    }
    .download-button {
        display: inline-block;
        padding: 6px 12px;
        background-color: #007bff;
        color: #fff;
        text-decoration: none;
        border-radius: 4px;
    }
    .preview-container img, .preview-container embed {
        max-width: 100%;
        height: auto;
    }
    .eliminar-btn {
        padding: 8px 16px;
        background-color: #dc3545;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    .menu-opciones .element {
        margin: 10px 0;
    }
    .back-button {
        display: inline-block;
        margin: 20px auto;
        padding: 10px 20px;
        background-color: #6c757d;
        color: #fff;
        text-decoration: none;
        border-radius: 4px;
    }
    .btn-primary {
        /* Asegurarse de que el botón "Ver más" sea visible */
        padding: 8px 16px;
    }
</style>
<body>
    
    <div class="container">
        <h1>Detalles de la Tarea</h1>
        <div class="line" style="height:2px; background-color:#ccc; margin: 20px 0;"></div>
        <div class="detail-item">
            <span class="detail-label">Materia:</span>
            <span><?php echo htmlspecialchars($nombre_materia); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Título:</span>
            <span><?php echo htmlspecialchars($tarea['titulo']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Descripción:</span>
            <span><?php echo nl2br(htmlspecialchars($tarea['descripcion'])); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Fecha de Creación:</span>
            <span><?php echo htmlspecialchars($tarea['fecha_creacion']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Fecha de Entrega:</span>
            <span><?php echo htmlspecialchars($tarea['fecha_limite']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Archivo Adjunto:</span>
            <?php if (!empty($tarea['archivo_tarea'])): ?>
                <a href="download.php?file=<?php echo urlencode($tarea['archivo_tarea']); ?>" target="_blank" class="download-button">
                    <i class="fas fa-download"></i> Ver Archivo
                </a>
            <?php else: ?>
                <span>No hay archivo disponible</span>
            <?php endif; ?>
        </div>

        <?php if ($calificacion !== null): ?>
            <div class="detail-item">
                <span class="detail-label">Calificación:</span>
                <span><?php echo htmlspecialchars($calificacion); ?> / 100</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Retroalimentación:</span>
                <span><?php echo nl2br(htmlspecialchars($retroalimentacion)); ?></span>
            </div>
        <?php else: ?>
            <div class="detail-item">
                <span class="detail-label">Calificación:</span>
                <span>Aún no calificada</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Retroalimentación:</span>
                <span>Sin retroalimentación</span>
            </div>
        <?php endif; ?>

        <?php if ($entregado): ?>
            <?php 
            // Obtener los datos de la entrega
            $entrega = $resultadoEntrega->fetch_assoc();
            $nombre_archivo = basename($entrega['archivo_entrega']); // Usa basename para obtener solo el nombre del archivo
            $ruta_archivo = 'uploads/' . $nombre_archivo;
            ?>
            <div class="detail-item">
                <span class="detail-label">Archivo Entregado:</span>
                <a href="<?php echo htmlspecialchars($ruta_archivo); ?>" target="_blank" class="download-button">
                    <i class="fas fa-download"></i> <?php echo htmlspecialchars($nombre_archivo); ?>
                </a>
            </div>
        
            <!-- Previsualización del archivo -->
            <div class="preview-container" onclick="abrirModal('<?php echo htmlspecialchars($ruta_archivo); ?>', '<?php echo htmlspecialchars($nombre_archivo); ?>')" style="display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: #f9f9f9; padding: 15px; border: 1px solid #e0e0e0; border-radius: 10px; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.15); max-width: 200px; margin: 20px auto; cursor: pointer;">
                <h4 style="margin: 0 0 10px; font-size: 16px; font-weight: bold; color: #333; text-align: center;">Vista previa</h4>

                <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $nombre_archivo)): ?>
                    <img src="<?php echo htmlspecialchars($ruta_archivo); ?>" alt="Previsualización de Imagen" style="width: 180px; height: 180px; object-fit: cover; border-radius: 8px;">
                <?php elseif (preg_match('/\.pdf$/i', $nombre_archivo)): ?>
                    <embed src="<?php echo htmlspecialchars($ruta_archivo); ?>#toolbar=0&navpanes=0&scrollbar=0" type="application/pdf" width="180" height="180" style="border-radius: 8px; border: none;">
                <?php else: ?>
                    <p style="font-size: 13px; color: #888; text-align: center;">Vista previa no disponible</p>
                <?php endif; ?>
            </div>

            <!-- Botón para eliminar la tarea -->
            <div style="display: flex; justify-content: center; margin-top: 15px;">
                <form id="eliminarForm" action="eliminarTareaAlumno.php" method="POST" onsubmit="return confirmarEliminacion();">
                    <input type="hidden" name="id_tarea" value="<?php echo htmlspecialchars($id_tarea); ?>">
                    <button type="submit" class="eliminar-btn">Eliminar Tarea</button>
                </form>
            </div>
        
            <script>
                function confirmarEliminacion() {
                    return confirm("¿Estás seguro de que deseas eliminar esta tarea? Esta acción no se puede deshacer.");
                }

                function abrirModal(ruta, nombre) {
                    // Implementa la función para abrir una ventana modal con la previsualización
                    // Puedes usar Bootstrap modals o cualquier otra librería/modal personalizada
                    // Por simplicidad, aquí abrimos una nueva ventana
                    window.open(ruta, '_blank');
                }
            </script>

            <!-- Código de la ventana modal (opcional) -->
            <div id="modalConfirmacion" class="modal" style="display:none;">
                <div class="modal-contenido">
                    <h2>Confirmar Eliminación</h2>
                    <p>¿Estás seguro de que deseas eliminar esta tarea?</p>
                    <form id="eliminarForm" action="eliminarTareaAlumno.php" method="POST">
                        <input type="hidden" name="id_tarea" value="<?php echo htmlspecialchars($id_tarea); ?>">
                        <button type="submit" class="btn-confirmar">Sí, eliminar</button>
                        <button type="button" class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Código para las rúbricas -->
        <?php if (isset($rubrica) && count($rubrica) > 0): ?>
            <h3 style="text-align: center; margin-top: 30px;">Rúbricas</h3>
            <div style="display: flex; justify-content: center; align-items: center; flex-direction: column; text-align: center;">
                <table class="table table-bordered" style="margin-top: 20px; width: 100%; max-width: 800px;">
                    <thead class="thead-light">
                        <tr>
                            <th>Criterio</th>
                            <th>Descripción</th>
                            <th>Puntos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rubrica as $criterio): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($criterio['criterio']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($criterio['descripcion'])); ?></td>
                                <td><?php echo htmlspecialchars($criterio['puntos']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="text-align: center; margin-top: 20px;">No hay criterios definidos para esta rúbrica.</p>
        <?php endif; ?>

        <!-- Contenedor de opciones para agregar o crear -->
        <div class="container mt-5">
            <div class="card" style="position: relative;">
                <button onclick="toggleMenu(event)" class="btn btn-secondary">+ Agregar o crear</button>
                <div id="menuOpciones" class="menu-opciones" style="display: none; position: absolute; top: 100%; left: 0; z-index: 1000;">
                    <ul class="list-unstyled p-2 bg-light border">
                        <li class="element mb-2">
                            <a href="https://drive.google.com" target="_blank" class="d-flex align-items-center text-dark text-decoration-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="#7e8590" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-google-drive me-2">
                                    <path d="M12 2L2 12l5 8h10l5-8L12 2z" fill="#4285F4"></path>
                                    <path d="M12 2L2 12h10l5-8z" fill="#0F9D58"></path>
                                    <path d="M17 12h-5l5 8h5l-5-8z" fill="#F4B400"></path>
                                </svg>
                                <p class="mb-0">Google Drive</p>
                            </a>
                        </li>
                        <li class="element mb-2">
                            <a href="https://www.canva.com" target="_blank" class="d-flex align-items-center text-dark text-decoration-none">
                                <!-- Ícono de Canva -->
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="#00C4CC" class="me-2">
                                    <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm3.29 16.36c-.703.563-1.446.996-2.173 1.12-.583.1-1.14-.046-1.603-.397-.247-.19-.515-.375-.797-.562-.098-.066-.198-.123-.297-.19-.4-.255-.857-.417-1.33-.47-.517-.06-1.034.034-1.548.15-.273.064-.547.146-.82.236a9.56 9.56 0 01-.297.085c-.272.084-.494.002-.683-.17-.092-.085-.152-.193-.212-.3-.063-.115-.123-.23-.174-.347-.157-.364-.245-.732-.353-1.106-.223-.763-.47-1.517-.722-2.272-.156-.484-.3-.968-.467-1.447-.063-.184-.13-.37-.207-.556-.055-.137-.116-.27-.186-.4-.047-.086-.106-.17-.17-.25-.103-.124-.2-.112-.302-.01-.047.048-.087.102-.127.156-.287.385-.563.77-.88 1.14-.283.333-.62.618-1.04.77-.23.08-.465.144-.693.227a.83.83 0 01-.97-.234c-.35-.417-.445-.916-.44-1.42.008-.767.326-1.48.836-2.058.588-.66 1.3-1.148 2.13-1.455a6.24 6.24 0 012.222-.41c.817.006 1.606.187 2.368.482.472.177.93.394 1.378.626.425.22.85.447 1.266.688.358.206.732.345 1.138.42.395.073.787.063 1.173-.012.275-.054.545-.146.805-.263.246-.11.478-.266.7-.43.36-.268.61-.614.85-.96.088-.13.174-.26.262-.39.02-.03.056-.062.086-.063.06-.002.1.034.14.07.187.18.37.368.54.562.22.25.402.53.553.826.34.666.527 1.376.638 2.108.08.532.0461.054-.164 1.565-.158.386-.372.746-.683 1.015-.3.26-.664.42-1.028.53a3.568 3.568 0 01-1.27.087c-.542-.04-.98-.242-1.398-.566z"/>
                                </svg>
                                <p class="mb-0">Canva</p>
                            </a>
                        </li>
                        <li class="element mb-2">
                            <a href="https://docs.google.com/presentation/u/1/" target="_blank" class="d-flex align-items-center text-dark text-decoration-none">
                                <!-- Ícono de PowerPoint -->
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="#D24726" class="me-2">
                                    <path d="M6 2C4.9 2 4 2.9 4 4v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6H6zm7 1.5L18.5 9H13V3.5zM8.5 13h1.2l1.1 3.7h.1l1.2-3.7h1.1l1.2 3.7h.1l1.1-3.7h1.2L14.8 19h-1.1l-1.2-3.5h-.1L11.3 19H10l-1.5-6zm-2.5-.5h1V14h-1v-1.5zm0 2.5h1v1h-1v-1z"/>
                                </svg>
                                <span style="color: #D24726;">Presentación</span>
                            </a>
                        </li>
                        <li class="element mb-2">
                            <a href="https://docs.google.com/document/u/1/" target="_blank" class="d-flex align-items-center text-dark text-decoration-none">
                                <!-- Ícono de archivo de Word -->
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="#2B579A" class="me-2">
                                    <path d="M6 2C4.9 2 4 2.9 4 4v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6H6zm7 1.5L18.5 9H13V3.5zM8.5 13h1V14h-1v-1.5zm0 2h1v1h-1v-1z"/>
                                </svg>
                                <span style="color: #e65c00;">Documento</span>
                            </a>
                        </li>
                        <li class="element mb-2">
                            <!-- Formulario de subida de archivos -->
                            <form id="uploadForm" action="upload.php" method="POST" enctype="multipart/form-data" class="d-flex flex-column align-items-center">
                                <!-- Input oculto para el id_tarea -->
                                <input type="hidden" name="id_tarea" value="<?php echo htmlspecialchars($id_tarea); ?>">

                                <!-- SVG Ícono y Texto que disparan el input de archivo -->
                                <label for="file-upload" class="d-flex align-items-center cursor-pointer mb-2">
                                    <svg class="lucide lucide-users-round me-2" stroke-linejoin="round" stroke-linecap="round" stroke-width="2" stroke="#7e8590" fill="none" viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M18 21a8 8 0 0 0-16 0"></path>
                                        <circle r="5" cy="8" cx="10"></circle>
                                        <path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3"></path>
                                    </svg>
                                    <p class="mb-0">Seleccionar Archivo</p>
                                </label>

                                <!-- Input de archivo oculto -->
                                <input 
                                    type="file" 
                                    id="file-upload" 
                                    name="archivo" 
                                    required 
                                    class="d-none"
                                >

                                <!-- Botón de enviar -->
                                <button type="submit" class="btn btn-secondary">Enviar</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón para regresar a Tareas Asignadas -->
    <a href="gestionTareasAlumno.php" class="back-button">Regresar a Tareas Asignadas</a>

    <!-- Script para toggle del menú de opciones -->
    <script>
        function toggleMenu(event) {
            event.stopPropagation(); // Evitar que el clic se propague a otros elementos
            const menu = document.getElementById("menuOpciones");
            menu.style.display = menu.style.display === "none" ? "block" : "none";
        }

        // Cerrar el menú si se hace clic fuera de él
        document.addEventListener("click", function(event) {
            const menu = document.getElementById("menuOpciones");
            if (!menu.contains(event.target) && event.target.id !== "menuOpciones") {
                menu.style.display = "none";
            }
        });

        function abrirModal(ruta, nombre) {
            // Implementa la función para abrir una ventana modal con la previsualización
            // Puedes usar Bootstrap modals o cualquier otra librería/modal personalizada
            // Por simplicidad, aquí abrimos una nueva ventana
            window.open(ruta, '_blank');
        }
    </script>

    <!-- Pie de página -->
    <footer class="text-center py-3 mt-5">
        <p>© 2024 PE-ISC</p>
    </footer>
</body>
</html>
<?php
        } else {
            echo "Tarea no encontrada o no tienes acceso a esta tarea.";
        }
    } else {
        echo "No estás autenticado como alumno.";
    }
} else {
    echo "ID de tarea inválido.";
}

$conexion->close();
?>
