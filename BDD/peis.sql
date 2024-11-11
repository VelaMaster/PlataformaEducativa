-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 04-11-2024 a las 19:38:15
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
  `nombre` varchar(100) NOT NULL,
  `segundo_nombre` varchar(100) DEFAULT NULL,
  `apellido_m` varchar(100) NOT NULL,
  `apellido_p` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`num_control`, `nombre`, `segundo_nombre`, `apellido_m`, `apellido_p`, `correo`, `contrasena`) VALUES
(21160750, 'Diego', NULL, 'Pérez', 'Barrios', '21160750@itoaxaca.edu.mx', 'facilita'),
(21160751, 'Ana', NULL, 'López', 'Martínez', '21160751@itoaxaca.edu.mx', 'contraseña456'),
(21160752, 'Luis', 'Fernando', 'Ramírez', 'Hernández', '21160752@itoaxaca.edu.mx', 'contraseña789'),
(21160753, 'Sofía', 'Isabel', 'Castillo', 'Reyes', '21160753@itoaxaca.edu.mx', 'contraseña101'),
(21160754, 'Carlos', 'Eduardo', 'Mendoza', 'Santos', '21160754@itoaxaca.edu.mx', 'contraseña202'),
(21160755, 'María', NULL, 'García', 'Vásquez', '21160755@itoaxaca.edu.mx', 'contraseña303'),
(21160756, 'Javier', 'Alejandro', 'Serrano', 'Flores', '21160756@itoaxaca.edu.mx', 'contraseña404'),
(21160757, 'Patricia', 'Noemí', 'Jiménez', 'Cruz', '21160757@itoaxaca.edu.mx', 'contraseña505'),
(21160758, 'Fernando', NULL, 'Ríos', 'Torres', '21160758@itoaxaca.edu.mx', 'contraseña606'),
(21160759, 'Valeria', 'Elena', 'Alvarado', 'Salinas', '21160759@itoaxaca.edu.mx', 'contraseña707'),
(21160760, 'Eduardo', NULL, 'Márquez', 'Ramírez', '21160760@itoaxaca.edu.mx', 'contraseña808'),
(21160761, 'Claudia', 'Elena', 'Bautista', 'Vargas', '21160761@itoaxaca.edu.mx', 'contraseña909'),
(21160762, 'Gabriel', NULL, 'Córdoba', 'Pérez', '21160762@itoaxaca.edu.mx', 'contraseña010'),
(21160763, 'Tania', NULL, 'Molina', 'Guzmán', '21160763@itoaxaca.edu.mx', 'contraseña111'),
(21160764, 'Ricardo', 'Javier', 'Ponce', 'López', '21160764@itoaxaca.edu.mx', 'contraseña212'),
(21160765, 'Karina', NULL, 'Salazar', 'Martínez', '21160765@itoaxaca.edu.mx', 'contraseña313'),
(21160766, 'Diego', 'Armando', 'Arce', 'Rivas', '21160766@itoaxaca.edu.mx', 'contraseña414'),
(21160767, 'Fernanda', NULL, 'Mendoza', 'Díaz', '21160767@itoaxaca.edu.mx', 'contraseña515'),
(21160768, 'Salvador', NULL, 'Lara', 'Hernández', '21160768@itoaxaca.edu.mx', 'contraseña616'),
(21160769, 'Brenda', 'Lucía', 'Figueroa', 'Guerrero', '21160769@itoaxaca.edu.mx', 'contraseña717'),
(21160770, 'Samuel', NULL, 'Navarro', 'Castillo', '21160770@itoaxaca.edu.mx', 'contraseña818'),
(21160771, 'Gina', 'Patricia', 'Sánchez', 'Vázquez', '21160771@itoaxaca.edu.mx', 'contraseña919'),
(21160772, 'Esteban', NULL, 'Aguirre', 'Hernández', '21160772@itoaxaca.edu.mx', 'contraseña020'),
(21160773, 'Diana', NULL, 'Bermúdez', 'Ramírez', '21160773@itoaxaca.edu.mx', 'contraseña121'),
(21160774, 'Miguel', NULL, 'Cano', 'Salas', '21160774@itoaxaca.edu.mx', 'contraseña222'),
(21160775, 'Lorena', NULL, 'Castañeda', 'Pérez', '21160775@itoaxaca.edu.mx', 'contraseña323'),
(21160776, 'Ulises', NULL, 'Soto', 'Cruz', '21160776@itoaxaca.edu.mx', 'contraseña424'),
(21160777, 'Ivette', NULL, 'Torres', 'Morales', '21160777@itoaxaca.edu.mx', 'contraseña525'),
(21160778, 'Alfredo', NULL, 'Saldívar', 'García', '21160778@itoaxaca.edu.mx', 'contraseña626'),
(21160779, 'Luz', NULL, 'Velasco', 'López', '21160779@itoaxaca.edu.mx', 'contraseña727'),
(21160780, 'Cecilia', 'Fernanda', 'Núñez', 'Rodríguez', '21160780@itoaxaca.edu.mx', 'contraseña828'),
(21160781, 'Arturo', NULL, 'Alonso', 'Díaz', '21160781@itoaxaca.edu.mx', 'contraseña929'),
(21160782, 'Rebeca', 'Ana', 'Vega', 'Martínez', '21160782@itoaxaca.edu.mx', 'contraseña030'),
(21160783, 'Pablo', NULL, 'Pérez', 'Barrios', '21160783@itoaxaca.edu.mx', 'contraseña131'),
(21160784, 'Fátima', 'Jimena', 'Ríos', 'Torres', '21160784@itoaxaca.edu.mx', 'contraseña232'),
(21160785, 'Gonzalo', 'Arturo', 'Salas', 'Cruz', '21160785@itoaxaca.edu.mx', 'contraseña333'),
(21160786, 'Nadia', NULL, 'Cervantes', 'García', '21160786@itoaxaca.edu.mx', 'contraseña434'),
(21160787, 'Sergio', NULL, 'Navarro', 'Mendoza', '21160787@itoaxaca.edu.mx', 'contraseña535'),
(21160788, 'Carla', 'Andrea', 'Ríos', 'Pérez', '21160788@itoaxaca.edu.mx', 'contraseña636'),
(21160789, 'Fernando', NULL, 'Morales', 'Ramírez', '21160789@itoaxaca.edu.mx', 'contraseña737'),
(21160790, 'Ángel', NULL, 'Hernández', 'Santos', '21160790@itoaxaca.edu.mx', 'contraseña838'),
(21160791, 'Valentino', 'Eduardo', 'Sánchez', 'Barrios', '21160791@itoaxaca.edu.mx', 'contraseña939'),
(21160792, 'Magdalena', NULL, 'Reyes', 'Vásquez', '21160792@itoaxaca.edu.mx', 'contraseña040'),
(21160793, 'Cristian', NULL, 'Mendoza', 'Castillo', '21160793@itoaxaca.edu.mx', 'contraseña141'),
(21160794, 'Nora', 'Sofía', 'Zavala', 'López', '21160794@itoaxaca.edu.mx', 'contraseña242'),
(21160795, 'Oscar', NULL, 'Cuéllar', 'Díaz', '21160795@itoaxaca.edu.mx', 'contraseña343'),
(21160796, 'Yolanda', 'María', 'Gómez', 'Núñez', '21160796@itoaxaca.edu.mx', 'contraseña444'),
(21160797, 'Ricardo', NULL, 'Serrano', 'Vargas', '21160797@itoaxaca.edu.mx', 'contraseña545'),
(21160798, 'Helena', NULL, 'Pérez', 'Alvarado', '21160798@itoaxaca.edu.mx', 'contraseña646'),
(21160799, 'Samuel', 'Alejandro', 'Cervantes', 'González', '21160799@itoaxaca.edu.mx', 'contraseña747'),
(21160800, 'Felipe', NULL, 'Ramírez', 'Salazar', '21160800@itoaxaca.edu.mx', 'contraseña848');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id_curso` int(11) NOT NULL,
  `nombre_curso` varchar(100) NOT NULL,
  `id_docente` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `imagen_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id_curso`, `nombre_curso`, `id_docente`, `fecha_creacion`, `imagen_url`) VALUES
(1, 'Programación Web', 10123456, '2024-10-30 02:19:05', 'https://picsum.photos/300/250?random=$1'),
(2, 'Bases de Datos', 10123457, '2024-10-30 02:20:05', 'https://picsum.photos/300/250?random=$2'),
(3, 'Estructuras de Datos', 10123458, '2024-10-30 02:21:05', 'https://picsum.photos/300/250?random=$3'),
(4, 'Redes de Computadoras', 10123459, '2024-10-30 02:22:05', 'https://picsum.photos/300/250?random=$4'),
(5, 'Sistemas Operativos', 10123460, '2024-10-30 02:23:05', 'https://picsum.photos/300/250?random=$5'),
(6, 'Desarrollo de Aplicaciones Móviles', 10123461, '2024-10-30 02:24:05', 'https://picsum.photos/300/250?random=$6'),
(7, 'Inteligencia Artificial', 10123462, '2024-10-30 02:25:05', 'https://picsum.photos/300/250?random=$7'),
(8, 'Programación en Java', 10123463, '2024-10-30 02:26:05', 'https://picsum.photos/300/250?random=$8'),
(9, 'Seguridad Informática', 10123464, '2024-10-30 02:27:05', 'https://picsum.photos/300/250?random=$9'),
(10, 'Análisis de Algoritmos', 10123465, '2024-10-30 02:28:05', 'https://picsum.photos/300/250?random=$10'),
(11, 'Arquitectura de Computadoras', 10123466, '2024-10-30 02:29:05', 'https://picsum.photos/300/250?random=$11'),
(12, 'Ingeniería de Software', 10123467, '2024-10-30 02:30:05', 'https://picsum.photos/300/250?random=$12'),
(13, 'Computación en la Nube', 10123468, '2024-10-30 02:31:05', 'https://picsum.photos/300/250?random=$13'),
(14, 'Programación en Python', 10123469, '2024-10-30 02:32:05', 'https://picsum.photos/300/250?random=$14'),
(15, 'Ética en la Computación', 10123470, '2024-10-30 02:33:05', 'https://picsum.photos/300/250?random=$15'),
(16, 'Estadistica', 21160750, '2024-11-01 05:20:47', 'https://picsum.photos/300/250?random=$16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docentes`
--

CREATE TABLE `docentes` (
  `num_control` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `segundo_nombre` varchar(100) DEFAULT NULL,
  `apellido_p` varchar(100) NOT NULL,
  `apellido_m` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `docentes`
--

INSERT INTO `docentes` (`num_control`, `nombre`, `segundo_nombre`, `apellido_p`, `apellido_m`, `correo`, `contrasena`) VALUES
(10123456, 'Laura', 'María', 'Gutiérrez', 'Sánchez', 'laura.gutierrez@itoaxaca.edu.mx', 'docente123'),
(10123457, 'Javier', NULL, 'López', 'Ramírez', 'javier.lopez@itoaxaca.edu.mx', 'docente456'),
(10123458, 'Carmen', 'Elena', 'Martínez', 'Hernández', 'carmen.martinez@itoaxaca.edu.mx', 'docente789'),
(10123459, 'Fernando', NULL, 'Pérez', 'González', 'fernando.perez@itoaxaca.edu.mx', 'docente101'),
(10123460, 'Sofía', 'Isabel', 'Morales', 'Reyes', 'sofia.morales@itoaxaca.edu.mx', 'docente202'),
(10123461, 'Diego', NULL, 'Vázquez', 'Serrano', 'diego.vazquez@itoaxaca.edu.mx', 'docente303'),
(10123462, 'Patricia', NULL, 'Salinas', 'Cruz', 'patricia.salinas@itoaxaca.edu.mx', 'docente404'),
(10123463, 'Gabriel', 'Alejandro', 'Jiménez', 'Torres', 'gabriel.jimenez@itoaxaca.edu.mx', 'docente505'),
(10123464, 'Verónica', NULL, 'Mendoza', 'García', 'veronica.mendoza@itoaxaca.edu.mx', 'docente606'),
(10123465, 'Luis', NULL, 'Hernández', 'Vásquez', 'luis.hernandez@itoaxaca.edu.mx', 'docente707'),
(10123466, 'María', NULL, 'Alvarado', 'López', 'maria.alvarado@itoaxaca.edu.mx', 'docente808'),
(10123467, 'Ricardo', 'Javier', 'Soto', 'Molina', 'ricardo.soto@itoaxaca.edu.mx', 'docente909'),
(10123468, 'Claudia', NULL, 'Navarro', 'González', 'claudia.navarro@itoaxaca.edu.mx', 'docente010'),
(10123469, 'Samuel', NULL, 'Cruz', 'Cano', 'samuel.cruz@itoaxaca.edu.mx', 'docente121'),
(10123470, 'Nadia', 'Estefanía', 'Salas', 'Ríos', 'nadia.salas@itoaxaca.edu.mx', 'docente232'),
(21160750, 'Diego', NULL, 'Perez', 'Barrios', '21160750@itoaxaca.edu.mx', 'facilita');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entregas`
--

CREATE TABLE `entregas` (
  `id_entrega` int(11) NOT NULL,
  `id_tarea` int(11) DEFAULT NULL,
  `id_alumno` int(11) DEFAULT NULL,
  `archivo_entrega` varchar(255) DEFAULT NULL,
  `fecha_entrega` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `id_grupo` int(11) NOT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `nombre_grupo` varchar(100) NOT NULL,
  `horario` varchar(20) DEFAULT NULL,
  `aula` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupos`
--

INSERT INTO `grupos` (`id_grupo`, `id_curso`, `nombre_grupo`, `horario`, `aula`) VALUES
(1, 1, '7SA', '07:00-08:00', 'i2'),
(2, 2, '6DB', '08:00-09:00', 'i4'),
(3, 3, '5ED', '13:00-14:00', 'i5'),
(4, 4, '8RC', '16:00-17:00', 'a7'),
(5, 5, '9SO', '10:00-11:00', 'a4'),
(6, 6, '10AM', '12:00-13:00', 'a3'),
(7, 7, '11IA', '14:00-15:00', 'a2'),
(8, 8, '12JP', '09:00-10:00', 'i14'),
(9, 9, '13SI', '18:00-19:00', 'i13'),
(10, 10, '14AA', '14:00-15:00', 'i12'),
(11, 16, '5SA', '07:00-08:00', 'i11'),
(12, 16, '5SB', '11:00-12:00', 'i10');

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
(1, 21160750),
(1, 21160751),
(1, 21160752),
(1, 21160753),
(1, 21160754),
(2, 21160750),
(2, 21160755),
(2, 21160756),
(2, 21160757),
(2, 21160758),
(2, 21160759),
(3, 21160760),
(3, 21160761),
(3, 21160762),
(3, 21160763),
(3, 21160764),
(4, 21160765),
(4, 21160766),
(4, 21160767),
(4, 21160768),
(4, 21160769),
(5, 21160770),
(5, 21160771),
(5, 21160772),
(5, 21160773),
(5, 21160774),
(6, 21160750),
(6, 21160775),
(6, 21160776),
(6, 21160777),
(6, 21160778),
(6, 21160779),
(7, 21160780),
(7, 21160781),
(7, 21160782),
(7, 21160783),
(7, 21160784),
(8, 21160785),
(8, 21160786),
(8, 21160787),
(8, 21160788),
(8, 21160789),
(9, 21160790),
(9, 21160791),
(9, 21160792),
(9, 21160793),
(9, 21160794),
(10, 21160795),
(10, 21160796),
(10, 21160797),
(10, 21160798),
(10, 21160799);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas`
--

CREATE TABLE `tareas` (
  `id_tarea` int(11) NOT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `archivo_tarea` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_limite` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tareas`
--

INSERT INTO `tareas` (`id_tarea`, `id_curso`, `titulo`, `descripcion`, `archivo_tarea`, `fecha_creacion`, `fecha_limite`) VALUES
(1, 16, 'Primer reporte', 'Realizar un reporte de 25 paginas', NULL, '2024-11-04 04:02:58', '2024-11-05');

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
  ADD PRIMARY KEY (`id_curso`),
  ADD KEY `id_docente` (`id_docente`);

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
  ADD KEY `id_curso` (`id_curso`);

--
-- Indices de la tabla `grupo_alumnos`
--
ALTER TABLE `grupo_alumnos`
  ADD PRIMARY KEY (`id_grupo`,`num_control`),
  ADD KEY `num_control` (`num_control`);

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
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `entregas`
--
ALTER TABLE `entregas`
  MODIFY `id_entrega` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id_grupo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `tareas`
--
ALTER TABLE `tareas`
  MODIFY `id_tarea` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD CONSTRAINT `cursos_ibfk_1` FOREIGN KEY (`id_docente`) REFERENCES `docentes` (`num_control`);

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
  ADD CONSTRAINT `grupos_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`);

--
-- Filtros para la tabla `grupo_alumnos`
--
ALTER TABLE `grupo_alumnos`
  ADD CONSTRAINT `grupo_alumnos_ibfk_1` FOREIGN KEY (`id_grupo`) REFERENCES `grupos` (`id_grupo`),
  ADD CONSTRAINT `grupo_alumnos_ibfk_2` FOREIGN KEY (`num_control`) REFERENCES `alumnos` (`num_control`);

--
-- Filtros para la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD CONSTRAINT `tareas_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
