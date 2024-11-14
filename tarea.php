<?php
// tarea.php

// Conexión a la base de datos
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDatos = "peis";

$conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener ID de la tarea y asegurarse de que es un número entero
$id_tarea = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Verificar si el ID de la tarea es válido
if ($id_tarea > 0) {
    // Obtener el ID del alumno desde la sesión
    session_start();
    $id_alumno = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 0;

    if ($id_alumno > 0) {
        // Consulta para verificar si el alumno tiene acceso a esta tarea
        $sql = "SELECT * FROM tareas
                JOIN grupo_alumnos ON grupo_alumnos.id_grupo = tareas.id_curso
                WHERE tareas.id_tarea = $id_tarea
                AND grupo_alumnos.num_control = $id_alumno";

        $resultado = $conexion->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
            $tarea = $resultado->fetch_assoc();

            // Verificar si el alumno ya entregó la tarea
            $sqlEntrega = "SELECT * FROM entregas WHERE id_tarea = $id_tarea AND id_alumno = $id_alumno";
            $resultadoEntrega = $conexion->query($sqlEntrega);
            $entregado = $resultadoEntrega && $resultadoEntrega->num_rows > 0;

            // Obtener el nombre de la materia
            function obtenerNombreMateria($id_curso, $conexion) {
                $consulta = "SELECT nombre_curso FROM cursos WHERE id_curso = $id_curso";
                $resultado = $conexion->query($consulta);
                if ($resultado && $resultado->num_rows > 0) {
                    $fila = $resultado->fetch_assoc();
                    return $fila['nombre_curso'];
                } else {
                    return "Desconocido";
                }
            }

            $nombre_materia = obtenerNombreMateria($tarea['id_curso'], $conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Tarea</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
         * {
                    box-sizing: border-box;
                    margin: 0;
                    padding: 0;
                }
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f8f9fa;
                    color: #333;
                    line-height: 1.6;
                    padding: 20px;
                }
                .navbar, .footer {
                    background-color: #333;
                    color: #fff;
                    text-align: center;
                    padding: 10px 0;
                }
                .navbar a, .footer {
                    color: #fff;
                    text-decoration: none;
                    margin: 0 20px;
                    font-weight: bold;
                }
                footer {
                  background-color: #333;
                  color: white;
                  text-align: center;
                  padding: 10px 0;
                 position: fixed;
                  bottom: 0;
                  left: 0;
                  width: 100%;
                  z-index: 10; /* Asegura que el pie de página esté sobre otros elementos */
                }

                .footer { 
                  position: relative;
                  padding-top: 10px;
                }
                .container {
                    max-width: 900px;
                    margin: 20px auto;
                    background: #fff;
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    padding: 30px;
                }
                h1 {
                    text-align: center;
                    color: #ff6600;
                    margin-bottom: 20px;
                }
                .line {
                    border-bottom: 2px solid #ff6600;
                    margin-bottom: 20px;
                }
                .detail-item {
                    display: flex;
                    justify-content: space-between;
                    padding: 10px 0;
                    border-bottom: 1px solid #ddd;
                }
                .detail-item:last-child {
                    border-bottom: none;
                }
                .detail-label {
                    font-weight: bold;
                }
                .upload-section {
    background: #ffffff;
    padding: 25px;
    border-radius: 10px;
    border: 1px solid #e0e0e0;
    margin-top: 25px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    max-width: 900px;
    margin-left: auto;
    margin-right: auto;
}

.upload-section h3 {
    margin-bottom: 20px;
    color: #333;
    font-size: 20px;
    font-weight: 600;
}

.upload-section input[type="file"] {
    display: block;
    margin: 15px auto;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    width: 90%;
    font-size: 15px;
    color: #555;
    transition: border-color 0.3s;
}

.upload-section input[type="file"]:hover, .upload-section input[type="file"]:focus {
    border-color: #ff6600;
    outline: none;
}

.upload-section button {
    background-color: #ff6600;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 12px 25px;
    font-size: 17px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
}

.upload-section button:hover {
    background-color: #e65c00;
    transform: scale(1.05);
}

.upload-section button:active {
    background-color: #cc5200;
    transform: scale(1.02);
}

                .eliminar-btn {
                    background-color: #dc3545;
                    color: #fff;
                    border: none;
                    border-radius: 5px;
                    padding: 10px 20px;
                    font-size: 16px;
                    cursor: pointer;
                    margin-top: 10px;
                    transition: background-color 0.3s;
                }
                .eliminar-btn:hover {
                    background-color: #c82333;
                }
                .back-button {
                    display: block;
                    width: 100%;
                    text-align: center;
                    background-color: #ff6600;
                    color: #fff;
                    padding: 10px 0;
                    border-radius: 5px;
                    text-decoration: none;
                    font-weight: bold;
                    margin-top: 20px;
                    transition: background-color 0.3s;
                }
                .back-button:hover {
                    background-color: #e65c00;
                }
                .preview {
                    margin-top: 15px;
                    text-align: center;
                }
                .preview img {
                    max-width: 100px;
                    border-radius: 5px;
                    margin-top: 10px;
                }

                .modal {
        display: none; /* Oculto por defecto */
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Fondo semi-transparente */
    }
    .modal-contenido {
        background-color: #fff;
        margin: 10% auto; /* Centrado vertical y horizontal */
        padding: 20px;
        border-radius: 8px;
        width: 80%;
        max-width: 400px;
        text-align: center;
    }
    .modal h2 {
        color: #ff6600;
        margin-bottom: 20px;
    }
    .btn-confirmar, .btn-cancelar {
        padding: 10px 20px;
        margin: 10px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
    }
    .btn-confirmar {
        background-color: #dc3545;
        color: #fff;
    }
    .btn-cancelar {
        background-color: #6c757d;
        color: #fff;
    }
    /* From Uiverse.io by Na3ar-17 */ 
.card {
  width: 200px;
  /* background-color: rgba(36, 40, 50, 1);
background-image: linear-gradient(135deg, rgba(36, 40, 50, 1) 0%, rgba(36, 40, 50, 1) 40%, rgba(37, 28, 40, 1) 100%); */

  background-color: rgba(36, 40, 50, 1);
  background-image: linear-gradient(
    139deg,
    rgba(36, 40, 50, 1) 0%,
    rgba(36, 40, 50, 1) 0%,
    rgba(37, 28, 40, 1) 100%
  );

  border-radius: 10px;
  padding: 15px 0px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.card .separator {
  border-top: 1.5px solid #e65c00;
}

.card .list {
  list-style-type: none;
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 0px 10px;
}

.card .list .element {
  display: flex;
  align-items: center;
  color: #e65c00;
  gap: 10px;
  transition: all 0.3s ease-out;
  padding: 4px 7px;
  border-radius: 6px;
  cursor: pointer;
}

.card .list .element svg {
  width: 19px;
  height: 19px;
  transition: all 0.3s ease-out;
}

.card .list .element .label {
  font-weight: 600;
}

.card .list .element:hover {
  background-color: #e65c00;
  color: #ffffff;
  transform: translate(1px, -1px);
}
.card .list .delete:hover {
  background-color: #8e2a2a;
}

.card .list .element:active {
  transform: scale(0.99);
}

.card .list:not(:last-child) .element:hover svg {
  stroke: #ffffff;
}

.card .list:last-child svg {
  stroke: #e65c00;
}
.card .list:last-child .element {
  color: #e65c00;
}

.card .list:last-child .element:hover {
  background-color: rgba(56, 45, 71, 0.836);
}

    </style>
</head>
<body>
    
    <div class="container">
        <h1>Detalles de la Tarea</h1>
        <div class="line"></div>
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
            <span><?php echo htmlspecialchars($tarea['descripcion']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Fecha de Creación:</span>
            <span><?php echo htmlspecialchars($tarea['fecha_creacion']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Fecha de Entrega:</span>
            <span><?php echo htmlspecialchars($tarea['fecha_limite']); ?></span>
        </div>

      
        <div class="card">
  <ul class="list">
    <li class="element">
      <a href="https://drive.google.com" target="_blank" style="text-decoration: none; color: inherit; display: flex; align-items: center;">
        <!-- Ícono de Google Drive -->
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="25"
          height="25"
          viewBox="0 0 24 24"
          fill="none"
          stroke="#7e8590"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
          class="lucide lucide-google-drive"
          style="margin-right: 8px;"
        >
          <path d="M12 2L2 12l5 8h10l5-8L12 2z" fill="#4285F4"></path>
          <path d="M12 2L2 12h10l5-8z" fill="#0F9D58"></path>
          <path d="M17 12h-5l5 8h5l-5-8z" fill="#F4B400"></path>
        </svg>
        <p class="label">Google Drive</p>
      </a>
    </li>
   
  

    <li class="element">
  <a href="https://www.canva.com" target="_blank" style="text-decoration: none; color: inherit; display: flex; align-items: center;">
    <!-- Ícono de Canva -->
    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 24 24"
      width="24"
      height="24"
      fill="#00C4CC"  
    >
      <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm3.29 16.36c-.703.563-1.446.996-2.173 1.12-.583.1-1.14-.046-1.603-.397-.247-.19-.515-.375-.797-.562-.098-.066-.198-.123-.297-.19-.4-.255-.857-.417-1.33-.47-.517-.06-1.034.034-1.548.15-.273.064-.547.146-.82.236a9.56 9.56 0 01-.297.085c-.272.084-.494.002-.683-.17-.092-.085-.152-.193-.212-.3-.063-.115-.123-.23-.174-.347-.157-.364-.245-.732-.353-1.106-.223-.763-.47-1.517-.722-2.272-.156-.484-.3-.968-.467-1.447-.063-.184-.13-.37-.207-.556-.055-.137-.116-.27-.186-.4-.047-.086-.106-.17-.17-.25-.103-.124-.2-.112-.302-.01-.047.048-.087.102-.127.156-.287.385-.563.77-.88 1.14-.283.333-.62.618-1.04.77-.23.08-.465.144-.693.227a.83.83 0 01-.97-.234c-.35-.417-.445-.916-.44-1.42.008-.767.326-1.48.836-2.058.588-.66 1.3-1.148 2.13-1.455a6.24 6.24 0 012.222-.41c.817.006 1.606.187 2.368.482.472.177.93.394 1.378.626.425.22.85.447 1.266.688.358.206.732.345 1.138.42.395.073.787.063 1.173-.012.275-.054.545-.146.805-.263.246-.11.478-.266.7-.43.36-.268.61-.614.85-.96.088-.13.174-.26.262-.39.02-.03.056-.062.086-.063.06-.002.1.034.14.07.187.18.37.368.54.562.22.25.402.53.553.826.34.666.527 1.376.638 2.108.08.532.046 1.054-.164 1.565-.158.386-.372.746-.683 1.015-.3.26-.664.42-1.028.53a3.568 3.568 0 01-1.27.087c-.542-.04-.98-.242-1.398-.566z"/>
    </svg>
    <p class="label">‎ ‎   Canva</p>
  </a>
</li>

<li class="element">
  <a href="https://docs.google.com/presentation/u/1/" target="_blank" style="display: flex; align-items: center; text-decoration: none;">
    <!-- Ícono de PowerPoint -->
    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 24 24"
      width="24"
      height="24"
      fill="#D24726" 
    >
      <path d="M6 2C4.9 2 4 2.9 4 4v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6H6zm7 1.5L18.5 9H13V3.5zM11 12h1.5v1.5H11V12zm0 2h1.5v1.5H11V14zm0 2h1.5v1.5H11V16z"/>
    </svg>
    <span style="margin-left: 10px; color: #D24726;">Presentación</span>
  </a>
</li>


    
    <li class="element">
  <a href="https://docs.google.com/document/u/1/" target="_blank" style="display: flex; align-items: center; text-decoration: none;">
    <!-- Ícono de archivo de Word -->
    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 24 24"
      width="24"
      height="24"
      fill="#2B579A" 
    >
      <path d="M6 2C4.9 2 4 2.9 4 4v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6H6zm7 1.5L18.5 9H13V3.5zM8.5 13h1.2l1.1 3.7h.1l1.2-3.7h1.1l1.2 3.7h.1l1.1-3.7h1.2L14.8 19h-1.1l-1.2-3.5h-.1L11.3 19H10l-1.5-6zm-2.5-.5h1V14h-1v-1.5zm0 2.5h1v1h-1v-1z"/>
    </svg>
    <span style="margin-left: 10px; color: #e65c00;">Documento</span>
  </a>
</li>

   <!-- aqui va el otro botom yo o poñgo -->
  
    
  </ul>
</div>


<?php if ($entregado): ?>
    <?php 
    // Obtener los datos de la entrega
    $entrega = $resultadoEntrega->fetch_assoc();
    $nombre_archivo = str_replace("uploads/", "", $entrega['archivo_entrega']);
    $ruta_archivo = 'uploads/' . $nombre_archivo;
    ?>
    <div class="detail-item">
        <span class="detail-label">Archivo Entregado:</span>
        <!-- Nombre del archivo que puede ser clickeado para ver o descargar -->
        <a href="download.php?file=<?php echo urlencode($nombre_archivo); ?>" target="_blank">
            <?php echo htmlspecialchars($nombre_archivo); ?>
        </a>
    </div>

    <!-- Previsualización del archivo -->
    <div class="preview-container" style="display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: #f9f9f9; padding: 15px; border: 1px solid #e0e0e0; border-radius: 10px; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.15); max-width: 200px; margin: 20px auto;">
    <h4 style="margin: 0 0 10px; font-size: 16px; font-weight: bold; color: #333; text-align: center;">Vista previa</h4>
    
    <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $nombre_archivo)): ?>
        <!-- Imagen centrada con bordes redondeados -->
        <img src="<?php echo $ruta_archivo; ?>" alt="Previsualización de Imagen" style="width: 180px; height: 180px; object-fit: cover; border-radius: 8px;">
    <?php elseif (preg_match('/\.pdf$/i', $nombre_archivo)): ?>
        <!-- Previsualización de PDF centrada sin barras de herramientas -->
        <embed src="<?php echo $ruta_archivo; ?>#toolbar=0&navpanes=0&scrollbar=0" type="application/pdf" width="180" height="180" style="border-radius: 8px; border: none;">
    <?php else: ?>
        <p style="font-size: 13px; color: #888; text-align: center;">Vista previa no disponible</p>
    <?php endif; ?>
</div>



    <!-- Botón para abrir la ventana modal de confirmación -->
    <div style="display: flex; justify-content: center; margin-top: 15px;">
    <button type="button" class="eliminar-btn" onclick="mostrarModal()" style="padding: 10px 20px; background-color: #ff4c4c; color: white; border: none; border-radius: 5px; cursor: pointer;">
        Eliminar Tarea
    </button>
</div>
<?php else: ?>
 <div class="upload-section">
    <h3>Subir tu archivo</h3>
    <form id="uploadForm" action="upload.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_tarea" value="<?php echo $id_tarea; ?>">
        <input type="file" name="archivo" required>
        <button type="submit">Enviar</button>
    </form>
</div>


<?php endif; ?>


<!-- Código de la ventana modal -->
<div id="modalConfirmacion" class="modal">
    <div class="modal-contenido">
        <h2>Confirmar Eliminación</h2>
        <p>¿Estás seguro de que deseas eliminar esta tarea?</p>
        <form id="eliminarForm" action="eliminarTareaAlumno.php" method="POST">
            <input type="hidden" name="id_tarea" value="<?php echo $id_tarea; ?>">
            <button type="submit" class="btn-confirmar">Sí, eliminar</button>
            <button type="button" class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
        </form>
    </div>
</div>

        <a href="gestionTareasAlumno.php" class="back-button">Regresar a Tareas Asignadas</a>
    </div>
    

    <script>
    // Función para mostrar el modal
    function mostrarModal() {
        document.getElementById("modalConfirmacion").style.display = "block";
    }

    // Función para cerrar el modal
    function cerrarModal() {
        document.getElementById("modalConfirmacion").style.display = "none";
    }
</script>

  <!-- Pie de página --> 
    <footer class="text-center py-3">
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