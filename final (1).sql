-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-03-2021 a las 18:18:54
-- Versión del servidor: 10.4.14-MariaDB
-- Versión de PHP: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `final`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instrumentos`
--

CREATE TABLE `instrumentos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `instrumentos`
--

INSERT INTO `instrumentos` (`id`, `nombre`) VALUES
(1, 'oboe'),
(2, 'clarinete'),
(3, 'flauta'),
(4, 'saxo alto'),
(5, 'saxo tenor'),
(6, 'saxo baritono'),
(7, 'tuba'),
(8, 'trombón'),
(9, 'trompa'),
(10, 'bombardino'),
(11, 'trompeta'),
(12, 'fliscorno'),
(13, 'fagot'),
(14, 'violonchelo'),
(15, 'percusión'),
(16, 'corno ingles');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `obras`
--

CREATE TABLE `obras` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `autor` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `obras`
--

INSERT INTO `obras` (`id`, `nombre`, `autor`) VALUES
(1, 'Ferling', 'señor ferling'),
(2, 'Prueba', 'Anónimo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partituras`
--

CREATE TABLE `partituras` (
  `id` int(11) NOT NULL,
  `archivo` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_obra` int(11) NOT NULL,
  `id_instrumento` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `partituras`
--

INSERT INTO `partituras` (`id`, `archivo`, `nombre`, `id_obra`, `id_instrumento`) VALUES
(2, 'http://localhost/backendfinal/partituras/Ferling.pdf', 'Ferling.pdf', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(4) NOT NULL,
  `nombreRol` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombreRol`) VALUES
(1, 'registrado'),
(14, 'administrador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `idRol` int(4) NOT NULL DEFAULT 1,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `idRol`, `nombre`, `apellidos`, `email`, `password`) VALUES
(1, 14, 'Pablo', 'Flores', 'pabloflores11@hotmail.com', '$2y$10$IQRBWs4zJrpvCNHfdVzQmuGMt8g3i6SbxCmYFI/PSpWcac.cm4SKG'),
(3, 1, 'Javier', 'Flores', 'javier@hotmail.com', '$2y$10$TZcj9UXrcelqKlH4CGkiMuKeG8NBSA3mjfBxNN8o/2SDOfVDn0kKi');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `videoteca`
--

CREATE TABLE `videoteca` (
  `id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `autor` varchar(100) NOT NULL,
  `enlace` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `videoteca`
--

INSERT INTO `videoteca` (`id`, `titulo`, `autor`, `enlace`) VALUES
(3, 'El colibrí', 'Anónimo', 'https://www.youtube.com/embed/veO2ooRTsKU'),
(4, 'Manual del perfecte inoportú', 'Joaquim Cano', 'https://www.youtube.com/embed/br2h74Ymcq0'),
(5, 'Lluna Mediterránea', 'Teodoro Aparicio', 'https://www.youtube.com/embed/eSJKm8WrU68'),
(6, 'Libertadores', 'Óscar Navarro', 'https://www.youtube.com/embed/dTbSU5362mk');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `instrumentos`
--
ALTER TABLE `instrumentos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `obras`
--
ALTER TABLE `obras`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `partituras`
--
ALTER TABLE `partituras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_obra` (`id_obra`),
  ADD KEY `id_instrumento` (`id_instrumento`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `relacionRoles` (`idRol`);

--
-- Indices de la tabla `videoteca`
--
ALTER TABLE `videoteca`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `instrumentos`
--
ALTER TABLE `instrumentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `obras`
--
ALTER TABLE `obras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `partituras`
--
ALTER TABLE `partituras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `videoteca`
--
ALTER TABLE `videoteca`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `partituras`
--
ALTER TABLE `partituras`
  ADD CONSTRAINT `id_instrumento` FOREIGN KEY (`id_instrumento`) REFERENCES `instrumentos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_obra` FOREIGN KEY (`id_obra`) REFERENCES `obras` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `relacionRoles` FOREIGN KEY (`idRol`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
