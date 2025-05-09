-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-05-2025 a las 17:14:35
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
-- Base de datos: `ddb250465`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `nombre`) VALUES
(4, 'Gestionar usuarios'),
(3, 'Modificar fichajes'),
(1, 'Ver fichajes propios'),
(2, 'Ver todos los fichajes');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`) VALUES
(1, 'Administrador'),
(3, 'Empleado'),
(2, 'Recursos Humanos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_permisos`
--

CREATE TABLE `rol_permisos` (
  `rol_id` int(11) NOT NULL,
  `permiso_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `rol_permisos`
--

INSERT INTO `rol_permisos` (`rol_id`, `permiso_id`) VALUES
(1, 2),
(1, 3),
(1, 4),
(2, 2),
(3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones_fichaje`
--

CREATE TABLE `sesiones_fichaje` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_entrada` datetime NOT NULL,
  `fecha_salida` datetime DEFAULT NULL,
  `tiempo_extra` time GENERATED ALWAYS AS (case when `fecha_salida` is not null then sec_to_time(time_to_sec(timediff(`fecha_salida`,`fecha_entrada`)) - 28800) else NULL end) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `sesiones_fichaje`
--

INSERT INTO `sesiones_fichaje` (`id`, `usuario_id`, `fecha_entrada`, `fecha_salida`) VALUES
(50, 1, '2025-05-01 06:52:44', '2025-05-01 15:55:44'),
(58, 2, '2025-04-16 21:03:43', '2025-04-17 07:53:43'),
(60, 1, '2025-04-18 02:17:11', '2025-04-18 07:52:11'),
(63, 3, '2025-04-19 02:32:00', '2025-04-19 12:22:00'),
(65, 4, '2025-04-15 03:57:12', '2025-04-15 07:56:12'),
(69, 4, '2025-04-24 11:55:14', '2025-04-24 22:27:14'),
(70, 3, '2025-04-24 14:55:03', '2025-04-24 17:54:03'),
(71, 4, '2025-04-10 09:35:32', '2025-04-10 15:53:32'),
(74, 3, '2025-04-10 21:40:58', '2025-04-11 00:23:58'),
(77, 1, '2025-04-21 06:35:40', '2025-04-21 12:46:40'),
(81, 1, '2025-04-29 10:19:08', '2025-04-29 16:40:08'),
(83, 4, '2025-05-06 07:21:56', '2025-05-06 13:41:56'),
(84, 3, '2025-04-19 08:28:35', '2025-04-19 18:37:35'),
(86, 2, '2025-05-04 03:36:09', '2025-05-04 12:49:09'),
(89, 1, '2025-05-03 17:36:41', '2025-05-04 02:41:41'),
(90, 1, '2025-04-08 17:14:08', '2025-04-08 23:58:08'),
(91, 2, '2025-04-13 13:54:21', '2025-04-13 18:19:21'),
(93, 1, '2025-04-07 22:27:31', '2025-04-08 08:41:31'),
(97, 4, '2025-04-14 20:48:56', '2025-04-15 06:03:56'),
(98, 1, '2025-05-05 09:31:00', '2025-05-05 15:39:00'),
(99, 2, '2025-04-06 08:15:00', '2025-04-06 18:42:00'),
(100, 1, '2025-04-28 08:46:00', '2025-04-28 13:21:00'),
(101, 3, '2025-05-07 10:42:00', '2025-05-07 15:44:00'),
(102, 2, '2025-04-21 10:05:00', '2025-04-21 18:15:00'),
(103, 4, '2025-04-11 08:38:00', '2025-04-11 13:47:00'),
(104, 2, '2025-04-08 06:49:00', '2025-04-08 16:27:00'),
(105, 1, '2025-05-03 07:38:00', '2025-05-03 18:43:00'),
(106, 3, '2025-04-10 07:59:00', '2025-04-10 17:43:00'),
(107, 1, '2025-04-27 06:29:00', '2025-04-27 14:23:00'),
(108, 1, '2025-04-07 09:03:00', '2025-04-07 17:03:00'),
(109, 2, '2025-04-20 10:39:00', '2025-04-20 16:12:00'),
(110, 1, '2025-04-07 08:58:00', '2025-04-07 18:26:00'),
(111, 4, '2025-04-29 06:22:00', '2025-04-29 14:14:00'),
(112, 4, '2025-04-27 06:47:00', '2025-04-27 12:33:00'),
(113, 4, '2025-04-15 10:40:00', '2025-04-15 14:53:00'),
(114, 1, '2025-04-18 10:29:00', '2025-04-18 18:27:00'),
(115, 4, '2025-04-12 07:50:00', '2025-04-12 15:44:00'),
(116, 4, '2025-04-22 07:32:00', '2025-04-22 18:16:00'),
(117, 4, '2025-05-01 08:17:00', '2025-05-01 19:39:00'),
(118, 1, '2025-04-28 08:00:00', '2025-04-28 15:52:00'),
(119, 2, '2025-04-22 07:35:00', '2025-04-22 18:50:00'),
(120, 3, '2025-04-22 09:02:00', '2025-04-22 19:01:00'),
(121, 3, '2025-04-20 08:51:00', '2025-04-20 13:32:00'),
(122, 4, '2025-04-24 08:04:00', '2025-04-24 16:10:00'),
(123, 4, '2025-04-15 09:10:00', '2025-04-15 15:09:00'),
(124, 2, '2025-04-13 07:29:00', '2025-04-13 15:33:00'),
(125, 2, '2025-04-25 07:17:00', '2025-04-25 15:20:00'),
(126, 2, '2025-04-12 06:55:00', '2025-04-12 17:16:00'),
(127, 1, '2025-04-21 08:54:00', '2025-04-21 16:53:00'),
(128, 1, '2025-04-21 10:52:00', '2025-04-21 18:49:00'),
(129, 4, '2025-04-28 07:57:00', '2025-04-28 16:05:00'),
(130, 3, '2025-04-12 06:07:00', '2025-04-12 11:16:00'),
(131, 1, '2025-04-22 06:30:00', '2025-04-22 14:39:00'),
(132, 1, '2025-04-07 07:06:00', '2025-04-07 13:20:00'),
(133, 1, '2025-04-18 07:01:00', '2025-04-18 15:10:00'),
(134, 4, '2025-04-22 08:06:00', '2025-04-22 18:30:00'),
(135, 4, '2025-04-16 10:34:00', '2025-04-16 14:53:00'),
(136, 4, '2025-04-08 07:45:00', '2025-04-08 15:48:00'),
(138, 1, '2025-05-03 07:47:00', '2025-05-03 13:09:00'),
(139, 3, '2025-04-27 07:12:00', '2025-04-27 11:43:00'),
(140, 3, '2025-04-15 09:36:00', '2025-04-15 17:28:00'),
(141, 3, '2025-04-22 06:00:00', '2025-04-22 13:57:00'),
(142, 4, '2025-05-02 06:55:00', '2025-05-02 13:35:00'),
(143, 3, '2025-04-14 08:17:00', '2025-04-14 16:15:00'),
(144, 1, '2025-05-07 08:46:00', '2025-05-07 13:16:00'),
(145, 3, '2025-05-06 07:51:00', '2025-05-06 13:24:00'),
(146, 1, '2025-04-27 09:13:00', '2025-04-27 13:31:00'),
(147, 3, '2025-05-03 10:09:00', '2025-05-03 20:30:00'),
(155, 4, '2025-05-09 17:01:05', '2025-05-09 17:01:12'),
(156, 4, '2025-05-09 17:01:55', '2025-05-09 17:02:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarjetas`
--

CREATE TABLE `tarjetas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `rfid_code` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `tarjetas`
--

INSERT INTO `tarjetas` (`id`, `usuario_id`, `rfid_code`) VALUES
(8, 4, '937681082740');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `rol_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `password`, `rol_id`) VALUES
(1, 'Pedro', 'Gómez', 'pedro.gomez@email.com', '$2y$10$2ir6BaJNQHQ.VRLG8Rmiku1O2AdXi16.Fhh8CK/OQwZrYqdecYH5e', 3),
(2, 'Manuel', 'López', 'manuel.lopez@email.com', '$2y$10$2ir6BaJNQHQ.VRLG8Rmiku1O2AdXi16.Fhh8CK/OQwZrYqdecYH5e', 3),
(3, 'Laura', 'Martínez', 'laura.martinez@email.com', '$2y$10$2ir6BaJNQHQ.VRLG8Rmiku1O2AdXi16.Fhh8CK/OQwZrYqdecYH5e', 2),
(4, 'Carlos', 'Fernández', 'carlos.fernandez@email.com', '$2y$10$2ir6BaJNQHQ.VRLG8Rmiku1O2AdXi16.Fhh8CK/OQwZrYqdecYH5e', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD PRIMARY KEY (`rol_id`,`permiso_id`),
  ADD KEY `fk_permiso` (`permiso_id`);

--
-- Indices de la tabla `sesiones_fichaje`
--
ALTER TABLE `sesiones_fichaje`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sesion_usuario` (`usuario_id`);

--
-- Indices de la tabla `tarjetas`
--
ALTER TABLE `tarjetas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rfid_code` (`rfid_code`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_usuario_rol` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `sesiones_fichaje`
--
ALTER TABLE `sesiones_fichaje`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT de la tabla `tarjetas`
--
ALTER TABLE `tarjetas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD CONSTRAINT `fk_permiso` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sesiones_fichaje`
--
ALTER TABLE `sesiones_fichaje`
  ADD CONSTRAINT `fk_sesion_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tarjetas`
--
ALTER TABLE `tarjetas`
  ADD CONSTRAINT `fk_tarjeta_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
