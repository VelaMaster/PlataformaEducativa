-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 22-11-2024 a las 05:24:55
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
CREATE DATABASE IF NOT EXISTS `peis` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `peis`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

DROP TABLE IF EXISTS `alumnos`;
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
(21160712, 'Diego', 'Perez', 'Barrios', 'Perez', 'diego.perez@ejemplo.com', 'contrasena_segura123'),
(21160750, 'Diego', NULL, 'Perez', 'Barrios', 'pdiegovela@gmail.com', 'facilita'),
(21160751, 'Ana', 'Sofia', 'Garcia', 'Lopez', 'ana.garcia@ejemplo.com', 'ana12345'),
(21160752, 'Luis', 'Fernando', 'Ramirez', 'Sanchez', 'luis.ramirez@ejemplo.com', 'luis_password'),
(21160753, 'Maria', 'Jose', 'Martinez', 'Ortega', 'maria.martinez@ejemplo.com', 'maria_2024'),
(21160754, 'Carlos', 'Eduardo', 'Hernandez', 'Rodriguez', 'carlos.hernandez@ejemplo.com', 'car12345');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

DROP TABLE IF EXISTS `cursos`;
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
(1, 'Introducción a la Programación', '2024-01-10', 'https://picsum.photos/300/250?random=1\n'),
(2, 'Matemáticas Discretas', '2024-02-15', 'https://picsum.photos/300/250?random=2\n'),
(3, 'Bases de Datos', '2024-03-05', 'https://picsum.photos/300/250?random=3\n'),
(4, 'Sistemas Operativos', '2024-04-20', 'https://picsum.photos/300/250?random=4\n');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docentes`
--

DROP TABLE IF EXISTS `docentes`;
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
(21160750, 'Diego', 'Perez', 'Barrios', 'Perez', 'diego.perez.docente@ejemplo.com', 'facilita'),
(21160760, 'Laura', 'Fernanda', 'Cruz', 'Martinez', 'laura.cruz@ejemplo.com', 'laura_pass'),
(21160761, 'Ricardo', 'Javier', 'Flores', 'Soto', 'ricardo.flores@ejemplo.com', 'ricardo_password'),
(21160762, 'Paula', 'Andrea', 'Mendez', 'Paredes', 'paula.mendez@ejemplo.com', 'paula1234');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entregas`
--

DROP TABLE IF EXISTS `entregas`;
CREATE TABLE `entregas` (
  `id_entrega` int(11) NOT NULL,
  `id_tarea` int(11) DEFAULT NULL,
  `id_alumno` int(11) DEFAULT NULL,
  `archivo_entrega` varchar(255) DEFAULT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `calificacion` int(11) DEFAULT NULL,
  `retroalimentacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `entregas`
--

INSERT INTO `entregas` (`id_entrega`, `id_tarea`, `id_alumno`, `archivo_entrega`, `fecha_entrega`, `calificacion`, `retroalimentacion`) VALUES
(4, 16, 21160750, 'uploads/Captura desde 2024-11-18 11-45-14.png', '2024-11-18', 1, 'Buen trabajo'),
(5, 16, 21160751, 'uploads/Captura desde 2024-11-21 12-14-45.png', '2024-11-21', 70, 'Buen trabajo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

DROP TABLE IF EXISTS `grupos`;
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
(1, 1, 21160750, 'Grupo A', 'Lunes y Miércoles 10:00-12:00', 'Aula 101'),
(2, 2, 21160750, 'Grupo B', 'Martes y Jueves 08:00-10:00', 'Aula 102'),
(3, 3, 21160761, 'Grupo C', 'Lunes y Miércoles 14:00-16:00', 'Aula 201'),
(4, 4, 21160762, 'Grupo D', 'Martes y Jueves 12:00-14:00', 'Aula 202');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo_alumnos`
--

DROP TABLE IF EXISTS `grupo_alumnos`;
CREATE TABLE `grupo_alumnos` (
  `id_grupo` int(11) NOT NULL,
  `num_control` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupo_alumnos`
--

INSERT INTO `grupo_alumnos` (`id_grupo`, `num_control`) VALUES
(1, 21160750),
(1, 21160751),
(2, 21160752),
(3, 21160753),
(4, 21160754);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rubricas`
--

DROP TABLE IF EXISTS `rubricas`;
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
(18, 15, 'Prueba 12', 'Descripcion', 90),
(19, 15, 'Ortografia', 'luis', 10),
(20, 16, 'we', 'we', 23),
(21, 16, '34', '56', 77);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas`
--

DROP TABLE IF EXISTS `tareas`;
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
(15, 1, 'Prueba', 'Prueba12', 'uploads/11-Texto del artículo-9-1-10-20181218.pdf', '2024-11-15', '2024-11-17'),
(16, 1, 'Prueba mensaje', 'mensaje', 'uploads/Captura desde 2024-11-15 19-02-10.png', '2024-11-15', '2024-11-23'),
(17, 1, 'Resumen del libro de Programacion', 'Realizar un resumen de la página 80 - 85', 'uploads/huevos a la mexicana ingles.pdf', '2024-11-21', '2024-11-23'),
(18, 2, 'Matrices', 'Realizar matrices en base al teorema fundamental del calculo', '', '2024-11-21', '2024-11-24');

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
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `entregas`
--
ALTER TABLE `entregas`
  MODIFY `id_entrega` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id_grupo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `rubricas`
--
ALTER TABLE `rubricas`
  MODIFY `id_rubrica` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `tareas`
--
ALTER TABLE `tareas`
  MODIFY `id_tarea` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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


--
-- Metadatos
--
USE `phpmyadmin`;

--
-- Metadatos para la tabla alumnos
--

--
-- Volcado de datos para la tabla `pma__table_uiprefs`
--

INSERT INTO `pma__table_uiprefs` (`username`, `db_name`, `table_name`, `prefs`, `last_update`) VALUES
('root', 'peis', 'alumnos', '{\"sorted_col\":\"`alumnos`.`num_control` ASC\"}', '2024-11-04 01:21:33');

--
-- Metadatos para la tabla cursos
--

--
-- Volcado de datos para la tabla `pma__table_uiprefs`
--

INSERT INTO `pma__table_uiprefs` (`username`, `db_name`, `table_name`, `prefs`, `last_update`) VALUES
('root', 'peis', 'cursos', '{\"CREATE_TIME\":\"2024-11-03 17:53:09\"}', '2024-11-04 15:51:57');

--
-- Metadatos para la tabla docentes
--

--
-- Volcado de datos para la tabla `pma__table_uiprefs`
--

INSERT INTO `pma__table_uiprefs` (`username`, `db_name`, `table_name`, `prefs`, `last_update`) VALUES
('root', 'peis', 'docentes', '{\"CREATE_TIME\":\"2024-10-24 00:25:14\"}', '2024-11-11 02:16:31');

--
-- Metadatos para la tabla entregas
--

--
-- Metadatos para la tabla grupos
--

--
-- Metadatos para la tabla grupo_alumnos
--

--
-- Metadatos para la tabla rubricas
--

--
-- Metadatos para la tabla tareas
--

--
-- Metadatos para la base de datos peis
--
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
