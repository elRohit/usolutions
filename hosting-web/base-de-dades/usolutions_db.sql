-- phpMyAdmin SQL Dump
-- version 5.2.1deb1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 09-05-2025 a las 13:20:26
-- Versión del servidor: 10.11.11-MariaDB-0+deb12u1
-- Versión de PHP: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `usolutions_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `container_templates`
--

CREATE TABLE `container_templates` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `os_type` enum('debian','ubuntu','fedora','centos','alpine') NOT NULL,
  `version` varchar(50) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `container_templates`
--

INSERT INTO `container_templates` (`id`, `name`, `description`, `file_name`, `os_type`, `version`, `active`, `created_at`) VALUES
(1, 'Debian 12', 'Debian 12 (Bookworm) Minimal', 'local:vztmpl/debian-12-standard_12.7-1_amd64.tar.zst', 'debian', '12', 1, '2025-03-18 18:08:44'),
(2, 'Ubuntu 24.10', 'Ubuntu 24.10 LTS (Noble Numbat) Minimal', 'local:vztmpl/ubuntu-24.10-standard_24.10-1_amd64.tar.zst', 'ubuntu', '24.10', 1, '2025-03-18 18:08:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `count_administrative`
--

CREATE TABLE `count_administrative` (
  `id` int(11) NOT NULL,
  `service_type` varchar(50) NOT NULL,
  `count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `count_administrative`
--

INSERT INTO `count_administrative` (`id`, `service_type`, `count`, `created_at`, `updated_at`) VALUES
(1, 'wordpress', 8, '2025-03-28 14:27:36', '2025-05-09 12:08:54'),
(2, 'prestashop', 3, '2025-03-28 14:27:36', '2025-05-09 09:04:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `issue_date` datetime NOT NULL,
  `due_date` datetime NOT NULL,
  `paid_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('credit_card','paypal','bank_account') NOT NULL,
  `last_four` varchar(4) DEFAULT NULL,
  `expiry_month` varchar(2) DEFAULT NULL,
  `expiry_year` varchar(4) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proxmox_config`
--

CREATE TABLE `proxmox_config` (
  `id` int(11) NOT NULL,
  `api_url` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `node_name` varchar(100) NOT NULL,
  `storage` varchar(100) NOT NULL DEFAULT 'local',
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proxmox_config`
--

INSERT INTO `proxmox_config` (`id`, `api_url`, `username`, `password`, `node_name`, `storage`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'https://100.72.76.70:8006/api2/json', 'system@pam!systemapi', '242bc272-3359-45b5-93cd-76ae589d1c9a', 'node1', 'local', 1, '2025-03-18 18:08:44', '2025-03-20 16:38:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `server_configurations`
--

CREATE TABLE `server_configurations` (
  `id` int(11) NOT NULL,
  `server_id` int(11) NOT NULL,
  `config_key` varchar(100) NOT NULL,
  `config_value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `server_containers`
--

CREATE TABLE `server_containers` (
  `id` int(11) NOT NULL,
  `server_id` int(11) NOT NULL,
  `vmid` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `root_password` varchar(255) NOT NULL,
  `admin_username` varchar(50) DEFAULT NULL,
  `admin_password` varchar(255) DEFAULT NULL,
  `node_name` varchar(100) NOT NULL,
  `status` enum('pending','creating','running','stopped','failed','deleted') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `server_plans`
--

CREATE TABLE `server_plans` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `cpu_cores` int(11) NOT NULL,
  `ram_gb` int(11) NOT NULL,
  `storage_gb` int(11) NOT NULL,
  `bandwidth_tb` decimal(5,2) NOT NULL,
  `price_multiplier` decimal(5,2) NOT NULL DEFAULT 1.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `server_plans`
--

INSERT INTO `server_plans` (`id`, `name`, `cpu_cores`, `ram_gb`, `storage_gb`, `bandwidth_tb`, `price_multiplier`, `is_active`, `created_at`) VALUES
(1, 'Starter', 1, 1, 25, 1.00, 1.00, 1, '2025-03-27 17:38:08'),
(2, 'Basic', 2, 2, 50, 2.00, 1.50, 1, '2025-03-27 17:38:08'),
(3, 'Professional', 4, 4, 100, 3.00, 2.25, 1, '2025-03-27 17:38:08'),
(4, 'Business', 6, 8, 200, 5.00, 3.50, 1, '2025-03-27 17:38:08');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `server_view`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `server_view` (
`server_id` int(11)
,`user_id` int(11)
,`service_id` int(11)
,`plan_id` int(11)
,`server_name` varchar(100)
,`hostname` varchar(100)
,`server_status` enum('pending','provisioning','active','suspended','cancelled')
,`purchase_date` datetime
,`expiry_date` datetime
,`auto_renew` tinyint(1)
,`billing_cycle` enum('monthly','quarterly','annually')
,`notes` text
,`service_name` varchar(100)
,`service_description` text
,`service_type` enum('managed','unmanaged')
,`managed_app` enum('wordpress','prestashop','nextcloud')
,`plan_name` varchar(100)
,`cpu_cores` int(11)
,`ram_gb` int(11)
,`storage_gb` int(11)
,`bandwidth_tb` decimal(5,2)
,`container_id` int(11)
,`vmid` int(11)
,`ip_address` varchar(45)
,`container_status` enum('pending','creating','running','stopped','failed','deleted')
,`admin_username` varchar(50)
,`template_name` varchar(100)
,`os_type` enum('debian','ubuntu','fedora','centos','alpine')
,`os_version` varchar(50)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('managed','unmanaged') NOT NULL,
  `managed_app` enum('wordpress','prestashop','nextcloud') DEFAULT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `setup_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `type`, `managed_app`, `base_price`, `setup_fee`, `is_active`, `created_at`) VALUES
(1, 'WordPress Managed Hosting', 'Fully managed WordPress hosting with automatic updates and backups', 'managed', 'wordpress', 29.99, 9.99, 1, '2025-03-27 17:38:01'),
(2, 'PrestaShop Managed Hosting', 'Fully managed PrestaShop hosting for your e-commerce needs', 'managed', 'prestashop', 34.99, 9.99, 1, '2025-03-27 17:38:01'),
(4, 'Unmanaged Debian Server', 'Unmanaged Debian server with root access', 'unmanaged', NULL, 14.99, 4.99, 1, '2025-03-27 17:38:01'),
(5, 'Unmanaged Ubuntu Server', 'Unmanaged Ubuntu server with root access', 'unmanaged', NULL, 14.99, 4.99, 1, '2025-03-27 17:38:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `status` enum('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
  `priority` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `closed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ticket_messages`
--

CREATE TABLE `ticket_messages` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone`, `password`, `status`, `balance`, `created_at`, `last_login`, `profile_image`) VALUES
(12, 'Ismael', 'Flor', 'i.flor@sapalomera.cat', NULL, '$2y$10$SVfmSKp5FXV6LQlKVqlrdutgsAWw6QqDWt6hU4VWs4m4nOQ5dDMtG', 'active', 0.00, '2025-03-18 19:42:42', '2025-03-20 16:43:20', NULL),
(13, 'Rohit', 'Kumar', 'r.kumar@sapalomera.cat', NULL, '$2y$10$ewfmoKYR.05jh2IDLT2YzuHchViovpg5l5NqZ2Qwizv6721kPDZdG', 'active', 0.00, '2025-03-28 14:35:10', NULL, NULL),
(14, 'ale', 'prp', 'a.perez@sapalomera.cat', NULL, '$2y$10$cq.o1in2DKcZvZs44u/hAOZIQXYvXNWlmMEFAs7hr6RilUM1dLP6G', 'active', 0.00, '2025-04-11 15:16:21', NULL, NULL),
(15, 'benito', 'camela', 'bcamela@sapalomera.cat', NULL, '$2y$10$W5Ym8RcyxaEB6lfqQR3ZVe0nKsJpLDNsSnywSZ5u1pD6ZSwOF7i0e', 'active', 0.00, '2025-04-30 13:51:29', NULL, NULL),
(17, 'Rocket', 'Ruiz', 'speedrkk@gmail.com', NULL, '$2y$10$uS2m92RojJaHMfUgsLg0TOky1AfyrElz579v2WxE630PN9R7c3Q2y', 'active', 0.00, '2025-05-08 16:49:50', NULL, NULL),
(18, 'benito', 'prp', 'r.kumar@sapalomera.com', NULL, '$2y$10$iLzX4M2njOK3lL2CKi.iPOe9yJxqn7pvxokmGd0uvbhjEyWSSvA7C', 'active', 0.00, '2025-05-08 17:30:44', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_servers`
--

CREATE TABLE `user_servers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `server_name` varchar(100) NOT NULL,
  `hostname` varchar(100) NOT NULL,
  `status` enum('pending','provisioning','active','suspended','cancelled') NOT NULL DEFAULT 'pending',
  `purchase_date` datetime NOT NULL,
  `expiry_date` datetime NOT NULL,
  `auto_renew` tinyint(1) NOT NULL DEFAULT 1,
  `billing_cycle` enum('monthly','quarterly','annually') NOT NULL DEFAULT 'monthly',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user_servers`
--

INSERT INTO `user_servers` (`id`, `user_id`, `service_id`, `plan_id`, `server_name`, `hostname`, `status`, `purchase_date`, `expiry_date`, `auto_renew`, `billing_cycle`, `notes`, `created_at`, `updated_at`) VALUES
(17, 13, 1, 2, 'wordpress-1', '192.168.5.48', 'active', '2025-05-06 18:08:11', '2025-06-06 18:08:11', 1, 'monthly', 'Created via Terraform', '2025-05-06 18:08:11', NULL),
(18, 13, 1, 2, 'wordpress-2', '192.168.5.49', 'active', '2025-05-06 18:14:51', '2025-06-06 18:14:51', 1, 'monthly', 'Created via Terraform', '2025-05-06 18:14:51', NULL),
(19, 13, 4, 2, 'tuntun3', '192.168.5.50', 'active', '2025-05-06 19:08:13', '2025-06-06 19:08:13', 1, 'monthly', 'Created via Terraform', '2025-05-06 19:08:13', NULL),
(20, 13, 2, 2, 'prestashop-1', '192.168.5.22', 'active', '2025-05-07 13:40:07', '2025-06-07 13:40:07', 1, 'monthly', 'Created via Terraform', '2025-05-07 13:40:07', NULL),
(21, 13, 2, 1, 'prestashop-2', '192.168.5.20', 'active', '2025-05-07 15:19:30', '2025-06-07 15:19:30', 1, 'monthly', 'Created via Terraform', '2025-05-07 15:19:30', NULL),
(23, 13, 1, 3, 'wordpress-3', '192.168.5.21', 'active', '2025-05-07 18:06:58', '2025-06-07 18:06:58', 1, 'monthly', 'Created via Terraform', '2025-05-07 18:06:58', NULL),
(25, 17, 1, 4, 'wordpress-5', '192.168.5.26', 'active', '2025-05-08 16:52:50', '2025-06-08 16:52:50', 1, 'monthly', 'Created via Terraform', '2025-05-08 16:52:50', NULL),
(26, 18, 1, 3, 'wordpress-6', '192.168.5.27', 'active', '2025-05-08 17:33:50', '2025-06-08 17:33:50', 1, 'monthly', 'Created via Terraform', '2025-05-08 17:33:50', NULL),
(27, 12, 1, 2, 'wordpress-7', '192.168.5.25', 'active', '2025-05-09 08:55:54', '2025-06-09 08:55:54', 1, 'monthly', 'Created via Terraform', '2025-05-09 08:55:54', NULL),
(28, 12, 2, 2, 'prestashop-3', '192.168.5.28', 'active', '2025-05-09 09:07:44', '2025-06-09 09:07:44', 1, 'monthly', 'Created via Terraform', '2025-05-09 09:07:44', NULL),
(29, 12, 4, 2, 'debian-isma', '192.168.5.29', 'active', '2025-05-09 09:12:50', '2025-06-09 09:12:50', 1, 'monthly', 'Created via Terraform', '2025-05-09 09:12:50', '2025-05-09 10:57:59'),
(30, 12, 1, 3, 'wordpress-8', '192.168.5.30', 'active', '2025-05-09 12:11:09', '2025-06-09 12:11:09', 1, 'monthly', 'Created via Terraform', '2025-05-09 10:11:09', '2025-05-09 10:17:14'),
(3130, 12, 4, 2, 'hola', '192.168.5.32', 'active', '2025-05-09 11:07:31', '2025-06-09 11:07:31', 1, 'monthly', 'Created via Terraform', '2025-05-09 11:07:31', NULL);

-- --------------------------------------------------------

--
-- Estructura para la vista `server_view`
--
DROP TABLE IF EXISTS `server_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`usolutions_admin`@`localhost` SQL SECURITY DEFINER VIEW `server_view`  AS SELECT `us`.`id` AS `server_id`, `us`.`user_id` AS `user_id`, `us`.`service_id` AS `service_id`, `us`.`plan_id` AS `plan_id`, `us`.`server_name` AS `server_name`, `us`.`hostname` AS `hostname`, `us`.`status` AS `server_status`, `us`.`purchase_date` AS `purchase_date`, `us`.`expiry_date` AS `expiry_date`, `us`.`auto_renew` AS `auto_renew`, `us`.`billing_cycle` AS `billing_cycle`, `us`.`notes` AS `notes`, `s`.`name` AS `service_name`, `s`.`description` AS `service_description`, `s`.`type` AS `service_type`, `s`.`managed_app` AS `managed_app`, `sp`.`name` AS `plan_name`, `sp`.`cpu_cores` AS `cpu_cores`, `sp`.`ram_gb` AS `ram_gb`, `sp`.`storage_gb` AS `storage_gb`, `sp`.`bandwidth_tb` AS `bandwidth_tb`, `sc`.`id` AS `container_id`, `sc`.`vmid` AS `vmid`, `sc`.`ip_address` AS `ip_address`, `sc`.`status` AS `container_status`, `sc`.`admin_username` AS `admin_username`, `ct`.`name` AS `template_name`, `ct`.`os_type` AS `os_type`, `ct`.`version` AS `os_version` FROM ((((`user_servers` `us` join `services` `s` on(`us`.`service_id` = `s`.`id`)) join `server_plans` `sp` on(`us`.`plan_id` = `sp`.`id`)) left join `server_containers` `sc` on(`us`.`id` = `sc`.`server_id`)) left join `container_templates` `ct` on(`sc`.`template_id` = `ct`.`id`)) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `container_templates`
--
ALTER TABLE `container_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `count_administrative`
--
ALTER TABLE `count_administrative`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `service_type` (`service_type`);

--
-- Indices de la tabla `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `proxmox_config`
--
ALTER TABLE `proxmox_config`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `server_configurations`
--
ALTER TABLE `server_configurations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `server_config_unique` (`server_id`,`config_key`);

--
-- Indices de la tabla `server_containers`
--
ALTER TABLE `server_containers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `server_id` (`server_id`),
  ADD KEY `template_id` (`template_id`);

--
-- Indices de la tabla `server_plans`
--
ALTER TABLE `server_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `ticket_messages`
--
ALTER TABLE `ticket_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `user_servers`
--
ALTER TABLE `user_servers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `plan_id` (`plan_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `container_templates`
--
ALTER TABLE `container_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `count_administrative`
--
ALTER TABLE `count_administrative`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `proxmox_config`
--
ALTER TABLE `proxmox_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `server_configurations`
--
ALTER TABLE `server_configurations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `server_containers`
--
ALTER TABLE `server_containers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `server_plans`
--
ALTER TABLE `server_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `ticket_messages`
--
ALTER TABLE `ticket_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `user_servers`
--
ALTER TABLE `user_servers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3131;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD CONSTRAINT `payment_methods_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `server_configurations`
--
ALTER TABLE `server_configurations`
  ADD CONSTRAINT `server_configurations_ibfk_1` FOREIGN KEY (`server_id`) REFERENCES `user_servers` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `server_containers`
--
ALTER TABLE `server_containers`
  ADD CONSTRAINT `server_containers_ibfk_1` FOREIGN KEY (`server_id`) REFERENCES `user_servers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `server_containers_ibfk_2` FOREIGN KEY (`template_id`) REFERENCES `container_templates` (`id`);

--
-- Filtros para la tabla `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `support_tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ticket_messages`
--
ALTER TABLE `ticket_messages`
  ADD CONSTRAINT `ticket_messages_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_messages_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `user_servers`
--
ALTER TABLE `user_servers`
  ADD CONSTRAINT `user_servers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_servers_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
  ADD CONSTRAINT `user_servers_ibfk_3` FOREIGN KEY (`plan_id`) REFERENCES `server_plans` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
