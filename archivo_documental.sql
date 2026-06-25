-- phpMyAdmin SQL Dump
-- version 5.2.3deb1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 25-06-2026 a las 03:13:32
-- Versión del servidor: 8.4.10-0ubuntu0.26.04.1
-- Versión de PHP: 8.5.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `archivo_documental`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documents`
--

CREATE TABLE `documents` (
  `id` int UNSIGNED NOT NULL,
  `number` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_date` date NOT NULL,
  `sender` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('oficio','memorandum','carta') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_file_name` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` int UNSIGNED NOT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `documents`
--

INSERT INTO `documents` (`id`, `number`, `subject`, `document_date`, `sender`, `type`, `file_name`, `original_file_name`, `mime_type`, `file_size`, `created_by`, `created_at`, `updated_at`) VALUES
(15, 'ENC:Q3djP0sjIqYQdennk4oxs5VzqiwdhW93sYMPh2yrF40=', 'ENC:Yl/LbTvxmC6U7viqRQBz11UgIKH3lYilF03D7Zk6XpQmEg==', '2026-06-24', 'ENC:GkvpxdSYbvNkhQ+qOzjR/lfDlBOFEOESC/+MTfIDeX5iu0E=', 'oficio', '54f5270f7cb267982a3286bf570eaea3.enc', 'ENC:5NlkxnvlaXOWOjYDLewLJ1s1uxOIkZYNWXLcCXbppR0aRSofK2E=', 'application/pdf', 26304, 1, '2026-06-25 03:02:00', '2026-06-25 03:02:00'),
(16, 'ENC:tVq/NNpJKmP7xCnftTKbrKaTYs1oWKc0zi5v0MrRug4=', 'ENC:tU5KA8/IiRYwr50HDmTtwUicwufJ95lJgMLtlJjM04Xgw/3J8DxvuPE=', '2026-06-24', 'ENC:wyEs1jgAsDVoaiGGsFwKRkkCEmBTQzgVJuug2pgtJxKuk7o=', 'oficio', 'd49f3ef19ed95af223c7942e1eaf39e1.enc', 'ENC:eg+1WYHQWNcFjgrrUYRJ9aOqVXqVhUOjO4WvmH8BxXfy2NWrhg+G', 'application/pdf', 25900, 1, '2026-06-25 03:02:57', '2026-06-25 03:02:57'),
(17, 'ENC:wNY6IC6h6mM2oMH+tdgJui9iEebU8rqhZg9h3wWz/fA=', 'ENC:YSGwQLlWeX/KtxEerRZb4cLgtb8W6P1q6r56w/PFm28=', '2026-06-24', 'ENC:80HIuBFtlWYUoUNt4VCNSuD8GH4HWlYgz6EDvEoZ67hSztE=', 'oficio', '6acac289caa90720a651460676ac48f2.enc', 'ENC:38M/A7vd3XP3Bx0farh9p2G3bJWjBqOxjGu8mATm4XhA8Xvg', 'application/pdf', 24553, 1, '2026-06-25 03:03:24', '2026-06-25 03:03:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `system_logs`
--

CREATE TABLE `system_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `action` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `system_logs`
--

INSERT INTO `system_logs` (`id`, `user_id`, `action`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(204, 1, 'document_created', 'Expediente creado: S001', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '2026-06-25 03:02:00'),
(205, 1, 'document_created', 'Expediente creado: A001', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '2026-06-25 03:02:57'),
(206, 1, 'document_created', 'Expediente creado: J001', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '2026-06-25 03:03:24'),
(207, 1, 'document_viewed', 'PDF visualizado: S001', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '2026-06-25 03:03:26'),
(208, 1, 'logout', 'Cierre de sesion.', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '2026-06-25 03:05:23'),
(209, 1, 'login', 'Inicio de sesion correcto.', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '2026-06-25 03:05:25'),
(210, 1, 'document_viewed', 'PDF visualizado: S001', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '2026-06-25 03:08:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','operador') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'operador',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `active`, `created_at`) VALUES
(1, 'Alice', 'admin@c3r0d4y.com', '$2y$12$7hsplKp4PkoWUrvraA7XLesT3sN0tWcp9qqa5HU0qu4Fval2ntBiu', 'admin', 1, '2026-05-17 20:14:24'),
(4, 'Bob', 'operador@c3r0d4y.com', '$2y$12$86SjvYZGgnHi7hAubKdl6Om31yY4FMW7SWyWPwlsooJ.GBvrk0Ur2', 'operador', 1, '2026-05-20 02:05:21');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_documents_created_by` (`created_by`),
  ADD KEY `idx_documents_date` (`document_date`),
  ADD KEY `idx_documents_type` (`type`);

--
-- Indices de la tabla `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_logs_user` (`user_id`),
  ADD KEY `idx_logs_created_at` (`created_at`),
  ADD KEY `idx_logs_action` (`action`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=211;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `fk_documents_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `system_logs`
--
ALTER TABLE `system_logs`
  ADD CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
