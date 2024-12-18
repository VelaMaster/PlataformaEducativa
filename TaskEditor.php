<?php
class TaskEditor {
    private $conexion;

    public function __construct($servidor, $usuario, $contraseña, $baseDatos) {
        $this->conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);
        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
    }

    public function getTask($id) {
        $stmt = $this->conexion->prepare("SELECT * FROM tareas WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $task = $resultado->fetch_assoc();
        $stmt->close();
        return $task;
    }

    public function getRubrics($task_id) {
        $stmt = $this->conexion->prepare("SELECT * FROM rubricas WHERE id_tarea = ?");
        $stmt->bind_param("i", $task_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $rubrics = [];
        while ($rubrica = $resultado->fetch_assoc()) {
            $rubrics[] = $rubrica;
        }
        $stmt->close();
        return $rubrics;
    }

    public function getTotalRubricPoints($task_id) {
        $stmt = $this->conexion->prepare("SELECT SUM(puntos) as total FROM rubricas WHERE id_tarea = ?");
        $stmt->bind_param("i", $task_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $total = 0;
        if ($fila = $resultado->fetch_assoc()) {
            $total = $fila['total'];
        }
        $stmt->close();
        return $total;
    }

    public function updateTask($id, $titulo, $descripcion, $fecha_limite, $archivo = null) {
        if ($archivo) {
            $stmt = $this->conexion->prepare("UPDATE tareas SET titulo = ?, descripcion = ?, fecha_limite = ?, archivo_tarea = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $titulo, $descripcion, $fecha_limite, $archivo, $id);
        } else {
            $stmt = $this->conexion->prepare("UPDATE tareas SET titulo = ?, descripcion = ?, fecha_limite = ? WHERE id = ?");
            $stmt->bind_param("sssi", $titulo, $descripcion, $fecha_limite, $id);
        }
        $resultado = $stmt->execute();
        if (!$resultado) {
            // Log the error or handle it accordingly
            error_log("Error al actualizar la tarea: " . $stmt->error);
        }
        $stmt->close();
        return $resultado;
    }

    public function addRubric($task_id, $criterio, $descripcion, $puntos) {
        // Validar que la suma total no exceda 100 después de agregar la nueva rúbrica
        $totalActual = $this->getTotalRubricPoints($task_id);
        if (($totalActual + $puntos) > 100) {
            return false; // No permitir que el total exceda 100
        }

        $stmt = $this->conexion->prepare("INSERT INTO rubricas (id_tarea, criterio, descripcion, puntos, cumple, no_cumple, observaciones) VALUES (?, ?, ?, ?, 0, 0, '')");
        $stmt->bind_param("issi", $task_id, $criterio, $descripcion, $puntos);
        $resultado = $stmt->execute();
        if (!$resultado) {
            // Log the error or handle it accordingly
            error_log("Error al agregar la rúbrica: " . $stmt->error);
        }
        $stmt->close();
        return $resultado;
    }

    public function deleteRubric($rubric_id) {
        $stmt = $this->conexion->prepare("DELETE FROM rubricas WHERE id = ?");
        $stmt->bind_param("i", $rubric_id);
        $resultado = $stmt->execute();
        if (!$resultado) {
            // Log the error or handle it accordingly
            error_log("Error al eliminar la rúbrica: " . $stmt->error);
        }
        $stmt->close();
        return $resultado;
    }

    // Manejar la subida de archivos y retornar el camino o un mensaje de error
    public function handleFileUpload($file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return $this->fileUploadErrorMessage($file['error']);
        }

        // Validar el tipo de archivo si es necesario
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        if (!in_array($file['type'], $tiposPermitidos)) {
            return "Tipo de archivo no permitido.";
        }

        // Validar el tamaño del archivo si es necesario
        $maxSize = 10 * 1024 * 1024; // 10 MB
        if ($file['size'] > $maxSize) {
            return "El archivo excede el tamaño máximo permitido de 10 MB.";
        }

        // Generar un nombre único para evitar conflictos
        $nombreArchivo = uniqid() . "_" . basename($file['name']);
        $rutaDestino = 'uploads/' . $nombreArchivo;

        if (move_uploaded_file($file['tmp_name'], $rutaDestino)) {
            return $rutaDestino;
        } else {
            return "Error al mover el archivo subido.";
        }
    }

    // Convertir el código de error en un mensaje legible
    private function fileUploadErrorMessage($error_code) {
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
                return "El archivo excede el tamaño máximo permitido por la configuración del servidor.";
            case UPLOAD_ERR_FORM_SIZE:
                return "El archivo excede el tamaño máximo permitido por el formulario.";
            case UPLOAD_ERR_PARTIAL:
                return "El archivo se subió parcialmente.";
            case UPLOAD_ERR_NO_FILE:
                return "No se subió ningún archivo.";
            case UPLOAD_ERR_NO_TMP_DIR:
                return "Falta la carpeta temporal.";
            case UPLOAD_ERR_CANT_WRITE:
                return "No se pudo escribir el archivo en el disco.";
            case UPLOAD_ERR_EXTENSION:
                return "Una extensión de PHP detuvo la subida del archivo.";
            default:
                return "Error desconocido al subir el archivo.";
        }
    }

    public function closeConnection() {
        $this->conexion->close();
    }
}
?>
