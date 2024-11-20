-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-11-2024 a las 02:45:58
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `peis`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `num_control` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `segundo_nombre` varchar(50) DEFAULT NULL,
  `apellido_p` varchar(50) NOT NULL,
  `apellido_m` varchar(50) DEFAULT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`num_control`, `nombre`, `segundo_nombre`, `apellido_p`, `apellido_m`, `correo`, `contrasena`) VALUES
(1, 'Juan', 'Carlos', 'Perez', 'Lopez', 'juan.perez@example.com', '123'),
(2, 'Ana', NULL, 'Martinez', 'Garcia', 'ana.martinez@example.com', 'password123'),
(3, 'Luis', 'Miguel', 'Sanchez', NULL, 'luis.sanchez@example.com', 'password123'),
(4, 'Maria', NULL, 'Gomez', 'Fernandez', 'maria.gomez@example.com', 'password123'),
(5, 'Pedro', 'Alonso', 'Ramirez', 'Martinez', 'pedro.ramirez@example.com', 'password123'),
(6, 'Carmen', 'Elena', 'Lopez', NULL, 'carmen.lopez@example.com', 'password123'),
(7, 'Miguel', NULL, 'Hernandez', 'Castillo', 'miguel.hernandez@example.com', 'password123'),
(8, 'Lucia', 'Beatriz', 'Garcia', NULL, 'lucia.garcia@example.com', 'password123'),
(9, 'Carlos', NULL, 'Martinez', 'Ruiz', 'carlos.martinez@example.com', 'password123'),
(10, 'Sara', 'Isabel', 'Torres', 'Sanchez', 'sara.torres@example.com', 'password123');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id_curso` int(11) NOT NULL,
  `nombre_curso` varchar(100) NOT NULL,
  `fecha_creacion` date DEFAULT NULL,
  `imagen_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id_curso`, `nombre_curso`, `fecha_creacion`, `imagen_url`) VALUES
(1, 'Matemáticas Básicas', '2024-01-01', NULL),
(2, 'Introducción a la Programación', '2024-02-01', NULL),
(3, 'Historia Universal', '2024-03-01', NULL),
(4, 'Física Aplicada', '2024-04-01', NULL),
(5, 'Química General', '2024-05-01', NULL),
(6, 'Biología', '2024-06-01', NULL),
(7, 'Literatura', '2024-07-01', NULL),
(8, 'Geografía', '2024-08-01', NULL),
(9, 'Arte y Cultura', '2024-09-01', NULL),
(10, 'Desarrollo Web', '2024-10-01', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docentes`
--

CREATE TABLE `docentes` (
  `num_control` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `segundo_nombre` varchar(50) DEFAULT NULL,
  `apellido_p` varchar(50) NOT NULL,
  `apellido_m` varchar(50) DEFAULT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `docentes`
--

INSERT INTO `docentes` (`num_control`, `nombre`, `segundo_nombre`, `apellido_p`, `apellido_m`, `correo`, `contrasena`) VALUES
(1, 'Raul', NULL, 'Salinas', 'Diaz', 'raul.salinas@example.com', 'docpassword123'),
(2, 'Carla', 'Maria', 'Gonzalez', NULL, 'carla.gonzalez@example.com', 'docpassword123'),
(3, 'Fernando', NULL, 'Perez', 'Martinez', 'fernando.perez@example.com', 'docpassword123'),
(4, 'Laura', 'Patricia', 'Gutierrez', NULL, 'laura.gutierrez@example.com', 'docpassword123'),
(5, 'Pablo', NULL, 'Alvarez', 'Sanchez', 'pablo.alvarez@example.com', 'docpassword123'),
(6, 'Sonia', 'Luisa', 'Rios', NULL, 'sonia.rios@example.com', 'docpassword123'),
(7, 'Gabriel', NULL, 'Castro', 'Jimenez', 'gabriel.castro@example.com', 'docpassword123'),
(8, 'Isabel', NULL, 'Herrera', 'Nava', 'isabel.herrera@example.com', 'docpassword123'),
(9, 'Miguel', 'Angel', 'Mendez', NULL, 'miguel.mendez@example.com', 'docpassword123'),
(10, 'Patricia', NULL, 'Lopez', 'Cruz', 'patricia.lopez@example.com', 'docpassword123');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entregas`
--

CREATE TABLE `entregas` (
  `id_entrega` int(11) NOT NULL,
  `id_tarea` int(11) DEFAULT NULL,
  `id_alumno` int(11) DEFAULT NULL,
  `archivo_entrega` varchar(255) DEFAULT NULL,
  `fecha_entrega` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `entregas`
--

INSERT INTO `entregas` (`id_entrega`, `id_tarea`, `id_alumno`, `archivo_entrega`, `fecha_entrega`) VALUES
(2, 2, 2, 'entrega2.pdf', '2024-11-02'),
(3, 3, 3, 'entrega3.pdf', '2024-11-03'),
(4, 4, 4, 'entrega4.pdf', '2024-11-04'),
(5, 5, 5, 'entrega5.pdf', '2024-11-05'),
(6, 6, 6, 'entrega6.pdf', '2024-11-06'),
(7, 7, 7, 'entrega7.pdf', '2024-11-07'),
(8, 8, 8, 'entrega8.pdf', '2024-11-08'),
(9, 9, 9, 'entrega9.pdf', '2024-11-09'),
(10, 10, 10, 'entrega10.pdf', '2024-11-10'),
(23, 2, 1, 'uploads/Evidencia_20161166.pdf', '2024-11-14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `id_grupo` int(11) NOT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `id_docente` int(11) DEFAULT NULL,
  `nombre_grupo` varchar(100) DEFAULT NULL,
  `horario` varchar(100) DEFAULT NULL,
  `aula` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupos`
--

INSERT INTO `grupos` (`id_grupo`, `id_curso`, `id_docente`, `nombre_grupo`, `horario`, `aula`) VALUES
(1, 1, 1, 'Grupo A', 'Lunes 8:00 - 10:00', 'Aula 101'),
(2, 2, 2, 'Grupo B', 'Martes 10:00 - 12:00', 'Aula 102'),
(3, 3, 3, 'Grupo C', 'Miércoles 8:00 - 10:00', 'Aula 103'),
(4, 4, 4, 'Grupo D', 'Jueves 10:00 - 12:00', 'Aula 104'),
(5, 5, 5, 'Grupo E', 'Viernes 8:00 - 10:00', 'Aula 105'),
(6, 6, 6, 'Grupo F', 'Lunes 10:00 - 12:00', 'Aula 106'),
(7, 7, 7, 'Grupo G', 'Martes 8:00 - 10:00', 'Aula 107'),
(8, 8, 8, 'Grupo H', 'Miércoles 10:00 - 12:00', 'Aula 108'),
(9, 9, 9, 'Grupo I', 'Jueves 8:00 - 10:00', 'Aula 109'),
(10, 10, 10, 'Grupo J', 'Viernes 10:00 - 12:00', 'Aula 110');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo_alumnos`
--

CREATE TABLE `grupo_alumnos` (
  `id_grupo` int(11) NOT NULL,
  `num_control` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupo_alumnos`
--

INSERT INTO `grupo_alumnos` (`id_grupo`, `num_control`) VALUES
(1, 1),
(2, 1),
(2, 2),
(3, 1),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rubricas`
--

CREATE TABLE `rubricas` (
  `id_rubrica` int(11) NOT NULL,
  `id_tarea` int(11) DEFAULT NULL,
  `criterio` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `puntos` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rubricas`
--

INSERT INTO `rubricas` (`id_rubrica`, `id_tarea`, `criterio`, `descripcion`, `puntos`) VALUES
(1, 1, 'Claridad', 'Claridad en la entrega', 10),
(2, 2, 'Originalidad', 'Originalidad en el contenido', 10),
(3, 3, 'Presentación', 'Buena presentación', 10),
(4, 4, 'Calidad', 'Alta calidad en el trabajo', 10),
(5, 5, 'Creatividad', 'Uso creativo de recursos', 10),
(6, 6, 'Puntualidad', 'Entrega a tiempo', 10),
(7, 7, 'Complejidad', 'Trabajo bien desarrollado', 10),
(8, 8, 'Investigación', 'Investigación completa', 10),
(9, 9, 'Profundidad', 'Análisis profundo', 10),
(10, 10, 'Organización', 'Buena organización', 10),
(11, 13, 'Presentación ', 'Buen diseño ', 30),
(12, 13, 'Ortografía ', 'Sin mala ortografía      ', 70);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas`
--

CREATE TABLE `tareas` (
  `id_tarea` int(11) NOT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `archivo_tarea` varchar(255) DEFAULT NULL,
  `fecha_creacion` date DEFAULT NULL,
  `fecha_limite` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tareas`
--

INSERT INTO `tareas` (`id_tarea`, `id_curso`, `titulo`, `descripcion`, `archivo_tarea`, `fecha_creacion`, `fecha_limite`) VALUES
(1, 1, 'Tarea 1', 'Descripción de la tarea 1', 'tarea1.pdf', '2024-10-01', '2024-11-01'),
(2, 2, 'Tarea 2', 'Descripción de la tarea 2', 'tarea2.pdf', '2024-10-02', '2024-11-02'),
(3, 3, 'Tarea 3', 'Descripción de la tarea 3', 'tarea3.pdf', '2024-10-03', '2024-11-03'),
(4, 4, 'Tarea 4', 'Descripción de la tarea 4', 'tarea4.pdf', '2024-10-04', '2024-11-04'),
(5, 5, 'Tarea 5', 'Descripción de la tarea 5', 'tarea5.pdf', '2024-10-05', '2024-11-05'),
(6, 6, 'Tarea 6', 'Descripción de la tarea 6', 'tarea6.pdf', '2024-10-06', '2024-11-06'),
(7, 7, 'Tarea 7', 'Descripción de la tarea 7', 'tarea7.pdf', '2024-10-07', '2024-11-07'),
(8, 8, 'Tarea 8', 'Descripción de la tarea 8', 'tarea8.pdf', '2024-10-08', '2024-11-08'),
(9, 9, 'Tarea 9', 'Descripción de la tarea 9', 'tarea9.pdf', '2024-10-09', '2024-11-09'),
(10, 10, 'Tarea 10', 'Descripción de la tarea 10', 'tarea10.pdf', '2024-10-10', '2024-11-10'),
(11, 1, 'tarea actual', 'para el 15', NULL, '2024-11-13', '2024-11-30'),
(12, 2, 'tarea actual v2', 'v2', NULL, '2024-11-13', '2024-11-30'),
(13, 1, 'prueba 10 ', 'pues lo que sea ', 'uploads/WhatsApp Image 2024-11-19 at 11.53.18.jpeg', '2024-11-19', '2024-11-30');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`num_control`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id_curso`);

--
-- Indices de la tabla `docentes`
--
ALTER TABLE `docentes`
  ADD PRIMARY KEY (`num_control`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `entregas`
--
ALTER TABLE `entregas`
  ADD PRIMARY KEY (`id_entrega`),
  ADD KEY `id_tarea` (`id_tarea`),
  ADD KEY `id_alumno` (`id_alumno`);

--
-- Indices de la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`id_grupo`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `id_docente` (`id_docente`);

--
-- Indices de la tabla `grupo_alumnos`
--
ALTER TABLE `grupo_alumnos`
  ADD PRIMARY KEY (`id_grupo`,`num_control`),
  ADD KEY `num_control` (`num_control`);

--
-- Indices de la tabla `rubricas`
--
ALTER TABLE `rubricas`
  ADD PRIMARY KEY (`id_rubrica`),
  ADD KEY `id_tarea` (`id_tarea`);

--
-- Indices de la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD PRIMARY KEY (`id_tarea`),
  ADD KEY `id_curso` (`id_curso`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `entregas`
--
ALTER TABLE `entregas`
  MODIFY `id_entrega` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id_grupo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `rubricas`
--
ALTER TABLE `rubricas`
  MODIFY `id_rubrica` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `tareas`
--
ALTER TABLE `tareas`
  MODIFY `id_tarea` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `entregas`
--
ALTER TABLE `entregas`
  ADD CONSTRAINT `entregas_ibfk_1` FOREIGN KEY (`id_tarea`) REFERENCES `tareas` (`id_tarea`),
  ADD CONSTRAINT `entregas_ibfk_2` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`num_control`);

--
-- Filtros para la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD CONSTRAINT `grupos_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`),
  ADD CONSTRAINT `grupos_ibfk_2` FOREIGN KEY (`id_docente`) REFERENCES `docentes` (`num_control`);

--
-- Filtros para la tabla `grupo_alumnos`
--
ALTER TABLE `grupo_alumnos`
  ADD CONSTRAINT `grupo_alumnos_ibfk_1` FOREIGN KEY (`id_grupo`) REFERENCES `grupos` (`id_grupo`),
  ADD CONSTRAINT `grupo_alumnos_ibfk_2` FOREIGN KEY (`num_control`) REFERENCES `alumnos` (`num_control`);

--
-- Filtros para la tabla `rubricas`
--
ALTER TABLE `rubricas`
  ADD CONSTRAINT `rubricas_ibfk_1` FOREIGN KEY (`id_tarea`) REFERENCES `tareas` (`id_tarea`);

--
-- Filtros para la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD CONSTRAINT `tareas_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
