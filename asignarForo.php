<?php
session_start();
include 'db.php';
if (!isset($_SESSION['usuario'])) {
    echo "<script>alert('Error: Usuario no autenticado.'); window.location.href = 'index.php';</script>";
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ahora materia es el id del curso directamente
    $id_curso = (int) $_POST['materia'];
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $tipo_for = trim($_POST['tipo_for']);

    if (empty($id_curso) || empty($titulo) || empty($descripcion) || empty($tipo_for)) {
        echo "<script>alert('Todos los campos son obligatorios.'); window.history.back();</script>";
        exit;
    }

    // Insertar el foro con el id_curso directo
    $sql_insert_foro = "INSERT INTO foros (nombre, descripcion, tipo_for, id_curso) VALUES (?, ?, ?, ?)";
    $stmt_foro = $conexion->prepare($sql_insert_foro);
    if (!$stmt_foro) {
        die("Error al preparar la inserción del foro: " . $conexion->error);
    }
    $stmt_foro->bind_param("sssi", $titulo, $descripcion, $tipo_for, $id_curso);

    if (!$stmt_foro->execute()) {
        $stmt_foro->close();
        die("<script>alert('Error al insertar el foro: " . addslashes($stmt_foro->error) . "'); window.history.back();</script>");
    }
    $id_foro_nuevo = $stmt_foro->insert_id;
    $stmt_foro->close();

    // Insertar la información del docente que creó el foro en foro_accesoDocentes
    $num_control_docente = (int)$_SESSION['usuario'];
    $sql_insert_acceso_docente = "INSERT INTO foro_accesoDocentes (id_foros, num_controlDocente) VALUES (?, ?)";
    $stmt_acceso_docente = $conexion->prepare($sql_insert_acceso_docente);
    if (!$stmt_acceso_docente) {
        die("Error al preparar la inserción de acceso docente: " . $conexion->error);
    }
    $stmt_acceso_docente->bind_param("ii", $id_foro_nuevo, $num_control_docente);
    if (!$stmt_acceso_docente->execute()) {
        $stmt_acceso_docente->close();
        die("<script>alert('Error al asignar acceso a docente: " . addslashes($stmt_acceso_docente->error) . "'); window.history.back();</script>");
    }
    $stmt_acceso_docente->close();
    
    // Insertar rúbrica si existe
    if (!empty($_POST['rubrica_criterio']) && is_array($_POST['rubrica_criterio'])) {
        $rubrica_criterios = $_POST['rubrica_criterio'];
        $rubrica_descripciones = $_POST['rubrica_descripcion'];
        $rubrica_puntos = $_POST['rubrica_puntos'];
        if (count($rubrica_criterios) === count($rubrica_descripciones) && count($rubrica_criterios) === count($rubrica_puntos)) {
            $sql_insert_rubrica = "INSERT INTO rubricasForo (id_foro, criterio, descripcion, puntos) VALUES (?, ?, ?, ?)";
            $stmt_rubrica = $conexion->prepare($sql_insert_rubrica);
            if (!$stmt_rubrica) {
                die("Error al preparar la inserción de la rúbrica: " . $conexion->error);
            }
            foreach ($rubrica_criterios as $index => $criterio) {
                $criterio_clean = trim($criterio);
                $desc_clean = trim($rubrica_descripciones[$index]);
                $puntos = (int) $rubrica_puntos[$index];

                if (empty($criterio_clean) || empty($desc_clean) || $puntos <= 0) {
                    continue;
                }

                $stmt_rubrica->bind_param("issi", $id_foro_nuevo, $criterio_clean, $desc_clean, $puntos);
                if (!$stmt_rubrica->execute()) {
                    $stmt_rubrica->close();
                    die("<script>alert('Error al insertar la rúbrica: " . addslashes($stmt_rubrica->error) . "'); window.history.back();</script>");
                }
            }
            $stmt_rubrica->close();
        }
    }

    // Asignar acceso a alumnos si el foro es privado
    if ($tipo_for === 'privado') {
        $sql_alumnos = "
            SELECT ga.num_control
            FROM grupo_alumnos ga
            JOIN grupos g ON ga.id_grupo = g.id
            WHERE g.id_curso = ?
        ";
        $stmt_alumnos = $conexion->prepare($sql_alumnos);
        if (!$stmt_alumnos) {
            die("Error al preparar la consulta de alumnos: " . $conexion->error);
        }
        $stmt_alumnos->bind_param("i", $id_curso);
        $stmt_alumnos->execute();
        $res_alumnos = $stmt_alumnos->get_result();

        $alumnos = [];
        while ($row = $res_alumnos->fetch_assoc()) {
            $alumnos[] = (int) $row['num_control'];
        }
        $stmt_alumnos->close();

        if (!empty($alumnos)) {
            $sql_insert_acceso = "INSERT INTO foro_accesoAlumnos (id_foros, num_controlAlumno) VALUES (?, ?)";
            $stmt_acceso = $conexion->prepare($sql_insert_acceso);
            if (!$stmt_acceso) {
                die("Error al preparar la inserción de acceso al foro: " . $conexion->error);
            }

            foreach ($alumnos as $num_controlAlumno) {
                $stmt_acceso->bind_param("ii", $id_foro_nuevo, $num_controlAlumno);
                if (!$stmt_acceso->execute()) {
                    $stmt_acceso->close();
                    die("<script>alert('Error al asignar acceso a alumnos: " . addslashes($stmt_acceso->error) . "'); window.history.back();</script>");
                }
            }
            $stmt_acceso->close();
        }
    }

    echo "<script>alert('Foro asignado exitosamente.'); window.location.href = 'gestionForosProfesor.php';</script>";
    exit;
}
?>
