<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: loginAlumno.php');
    exit();
}
<<<<<<< HEAD
$id_alumno = $_SESSION['id'];
if (!isset($_GET['id'])) {
    die('No se ha proporcionado el ID de la tarea.');
}
$id_tarea = intval($_GET['id']); 
=======
>>>>>>> 4d02e976d899d99eb2811993568531d4dff63e1b
$host = 'localhost';
$db   = 'peis';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}

// Obtener el ID del alumno desde la sesión
$usuario = $_SESSION['usuario'];
$stmt = $pdo->prepare('SELECT id FROM alumnos WHERE num_control= ?');
$stmt->execute([$usuario]);
$alumno = $stmt->fetch();

if ($alumno) {
    $id_alumno = $alumno['id'];
} else {
    die('Error: No se encontró el ID del alumno 112.');
}

// Verificar parámetro GET
if (!isset($_GET['id'])) {
    die('No se ha proporcionado el ID de la tarea.');
}
$id_tarea = intval($_GET['id']);

// Resto del código para subir archivos y manejar entregas...

$stmt = $pdo->prepare('SELECT tareas.*, cursos.nombre_curso FROM tareas JOIN cursos ON tareas.id_curso = cursos.id WHERE tareas.id = ?');
$stmt->execute([$id_tarea]);
$tarea = $stmt->fetch();

if (!$tarea) {
    die('Tarea no encontrada.');
}
$stmt = $pdo->prepare('SELECT * FROM rubricas WHERE id_tarea = ?');
$stmt->execute([$id_tarea]);
$rubricas = $stmt->fetchAll();
$stmt = $pdo->prepare('SELECT * FROM entregas WHERE id_tarea = ? AND id_alumno = ?');
$stmt->execute([$id_tarea, $id_alumno]);
$entrega = $stmt->fetch();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivoTarea'])) {
    if ($entrega) {
        $error = 'Ya has subido una entrega para esta tarea.';
    } else {
        $file = $_FILES['archivoTarea'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'docx', 'pptx'];
            $file_name = $file['name'];
            $file_tmp = $file['tmp_name'];
            $file_size = $file['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($file_ext, $allowed_extensions)) {
                $error = 'Tipo de archivo no permitido.';
            } elseif ($file_size > 10 * 1024 * 1024) { // 10MB
                $error = 'El archivo excede el tamaño máximo permitido.';
            } else {
                $upload_dir = 'uploads/entregas/' . $id_tarea . '/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                    chmod($upload_dir, 0777); // Fuerza los permisos a 0777
                }
                
                $new_file_name = 'entrega_alumno_' . $id_alumno . '_' . time() . '.' . $file_ext;
                $destination = $upload_dir . $new_file_name;

                if (move_uploaded_file($file_tmp, $destination)) {
                    // Insertar en la base de datos
                    $stmt = $pdo->prepare('INSERT INTO entregas (id_tarea, id_alumno, archivo_entrega, fecha_entrega) VALUES (?, ?, ?, ?)');
                    $fecha_entrega = date('Y-m-d');
                    $stmt->execute([$id_tarea, $id_alumno, $destination, $fecha_entrega]);
                    $entrega = [
                        'id' => $pdo->lastInsertId(),
                        'id_tarea' => $id_tarea,
                        'id_alumno' => $id_alumno,
                        'archivo_entrega' => $destination,
                        'fecha_entrega' => $fecha_entrega,
                        'calificacion' => null,
                        'retroalimentacion' => null
                    ];

                    $success = 'Archivo subido exitosamente.';
                } else {
                    $error = 'Error al mover el archivo.';
                }
            }
        } else {
            $error = 'Error al subir el archivo.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Tarea</title>
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/barradeNavegacion.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/vermasProfesor.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/verdetallesTarea.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Barra de Navegación -->
    <div class="barranavegacion">
        <div class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Plataforma educativa para Ingeniería en Sistemas</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" 
                    aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
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
                        <li class="nav-item">
                            <a class="nav-link" href="forosAlumno.php">Foros</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Cerrar sesión</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor Principal -->
    <div class="container mt-5">
        <h1 class="text-center mb-4">Ver Tarea</h1>
        
        <!-- Mostrar mensajes de error o éxito -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <!-- Card de Detalles de la Tarea -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h4 class="mb-0">Detalles de la Tarea</h4>
            </div>
            <div class="card-body">
                <!-- Filas para cada detalle -->
                <div class="row mb-3">
                    <div class="col-md-3 detail-label">Materia:</div>
                    <div class="col-md-9"><?php echo htmlspecialchars($tarea['nombre_curso']); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 detail-label">Título:</div>
                    <div class="col-md-9"><?php echo htmlspecialchars($tarea['titulo']); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 detail-label">Descripción:</div>
                    <div class="col-md-9"><?php echo nl2br(htmlspecialchars($tarea['descripcion'])); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 detail-label">Fecha Asignada:</div>
                    <div class="col-md-9"><?php echo date('d \d\e F \d\e Y', strtotime($tarea['fecha_creacion'])); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 detail-label">Fecha de Entrega:</div>
                    <div class="col-md-9"><?php echo date('d \d\e F \d\e Y', strtotime($tarea['fecha_limite'])); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 detail-label">Archivo Adjunto:</div>
                    <div class="col-md-9">
                        <a href="<?php echo htmlspecialchars($tarea['archivo_tarea']); ?>" class="download-link" target="_blank">
                            <i class="fas fa-download"></i> Descargar Documento de la Tarea
                        </a>
                        <div class="mt-3">
                            <?php
                                $file_ext = strtolower(pathinfo($tarea['archivo_tarea'], PATHINFO_EXTENSION));
                                if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                                    echo '<img src="' . htmlspecialchars($tarea['archivo_tarea']) . '" alt="Vista Previa de la Imagen" class="preview-img img-thumbnail">';
                                } elseif ($file_ext === 'pdf') {
                                    echo '<embed src="' . htmlspecialchars($tarea['archivo_tarea']) . '" type="application/pdf" class="preview-pdf" width="100%" height="400px">';
                                } else {
                                    echo '<p>Vista previa no disponible</p>';
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 detail-label">Calificación:</div>
                    <div class="col-md-9">
                        <?php
                            if ($entrega && $entrega['calificacion'] !== null) {
                                echo htmlspecialchars($entrega['calificacion']) . ' / 100';
                            } else {
                                echo 'Aún no calificado';
                            }
                        ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 detail-label">Retroalimentación:</div>
                    <div class="col-md-9">
                        <?php
                            if ($entrega && !empty($entrega['retroalimentacion'])) {
                                echo htmlspecialchars($entrega['retroalimentacion']);
                            } else {
                                echo 'Aún no hay retroalimentación.';
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h4 class="mb-0">Rúbrica</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered rubrica-table">
                        <thead class="table-light">
                            <tr>
                                <th>Criterio</th>
                                <th>Descripción</th>
                                <th>Puntos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rubricas as $rubrica): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($rubrica['criterio']); ?></td>
                                    <td><?php echo nl2br(htmlspecialchars($rubrica['descripcion'])); ?></td>
                                    <td><?php echo htmlspecialchars($rubrica['puntos']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Div para Previsualizar el Archivo Subido por el Alumno -->
        <?php if ($entrega && file_exists($entrega['archivo_entrega'])): ?>
            <div id="uploadedFilePreview">
                <h5>Previsualización de Archivo Subido:</h5>
                <div id="previewContent">
                    <?php
                        $file_ext_entrega = strtolower(pathinfo($entrega['archivo_entrega'], PATHINFO_EXTENSION));
                        if (in_array($file_ext_entrega, ['jpg', 'jpeg', 'png', 'gif'])) {
                            echo '<img src="' . htmlspecialchars($entrega['archivo_entrega']) . '" alt="Vista Previa de la Imagen" class="img-thumbnail">';
                        } elseif ($file_ext_entrega === 'pdf') {
                            echo '<embed src="' . htmlspecialchars($entrega['archivo_entrega']) . '" type="application/pdf" class="preview-pdf" width="100%" height="400px">';
                        } else {
                            echo '<p>Archivo subido: ' . htmlspecialchars($entrega['archivo_entrega']) . '</p>';
                        }
                    ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="d-flex justify-content-end mb-4">
            <button type="button" class="btn btn-agregar-tarea" data-bs-toggle="modal" data-bs-target="#agregarTareaModal">
                <i class="fas fa-plus"></i> Agregar o Crear una Tarea
            </button>
        </div>
    </div>

    <!-- Modal para Agregar o Crear una Tarea -->
    <div class="modal fade" id="agregarTareaModal" tabindex="-1" aria-labelledby="agregarTareaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg"> <!-- Modal más grande para acomodar las opciones -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="agregarTareaModalLabel">Agregar o Crear una Tarea</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Opciones de Creación de Tarea -->
                    <div class="mb-4">
                        <h6>Selecciona una opción para agregar o crear una tarea:</h6>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="https://www.canva.com" target="_blank" class="btn btn-outline-primary flex-fill" title="Crear con Canva">
                                <i class="fas fa-paint-brush me-2"></i> Canva
                            </a>
                            <a href="https://docs.google.com/document/" target="_blank" class="btn btn-outline-secondary flex-fill" title="Crear con Google Documentos">
                                <i class="fas fa-file-word me-2"></i> Google Documentos
                            </a>
                            <a href="https://docs.google.com/spreadsheets/" target="_blank" class="btn btn-outline-success flex-fill" title="Crear con Hojas de Google">
                                <i class="fas fa-file-excel me-2"></i> Hojas de Google
                            </a>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h6>O bien, sube un archivo desde tu dispositivo:</h6>
                        <form id="formAgregarTarea" enctype="multipart/form-data" method="POST" action="">
                            <div class="mb-3">
                                <label for="archivoTarea" class="form-label">Seleccionar Archivo</label>
                                <input class="form-control" type="file" id="archivoTarea" name="archivoTarea" accept=".jpg, .jpeg, .png, .gif, .pdf, .docx, .pptx" required>
                            </div>
                            <!-- Botones de acción dentro del formulario -->
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Agregar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
