-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.0.30 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para configurador_mapas
CREATE DATABASE IF NOT EXISTS `configurador_mapas` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `configurador_mapas`;

-- Volcando estructura para tabla configurador_mapas.events
CREATE TABLE IF NOT EXISTS `events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `uuid_event` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT (uuid()),
  `event_name` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `description` text,
  `name_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `event_place` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla configurador_mapas.events: ~19 rows (aproximadamente)
INSERT INTO `events` (`id`, `uuid_event`, `event_name`, `event_date`, `description`, `name_file`, `event_place`) VALUES
	(28, '5028f37a-ee49-11ef-bf9f-60189541604e', 'evento1', '2025-02-18', 'evento1', NULL, 'Aguascalientes'),
	(29, 'de73f8d6-ee49-11ef-bf9f-60189541604e', 'evento2', '0012-12-12', 'evento2', NULL, 'Baja California'),
	(31, '535f129b-ee4b-11ef-bf9f-60189541604e', 'prueba', '2222-08-10', 'prueba', NULL, 'Chihuahua'),
	(32, '9e24d4a0-eed4-11ef-b84b-60189541604e', 'prueba', '2025-02-19', 'prueba', NULL, 'Guerrero'),
	(34, '71714288-eed5-11ef-b84b-60189541604e', 'prueba', '0012-12-12', 'un pequeño evento ', NULL, 'Aguascalientes'),
	(35, '15eb0687-eed8-11ef-b84b-60189541604e', 'prueba', '2025-02-19', 'un pequeño evento ', NULL, 'Campeche'),
	(36, '1c50aaf4-eeda-11ef-b84b-60189541604e', 'abneas2', '2025-02-19', 'un pequeño evento 2', NULL, 'Baja California Sur'),
	(37, '8bc25d41-eeda-11ef-b84b-60189541604e', 'prueba', '2025-02-19', 'un pequeño evento ', NULL, 'Coahuila'),
	(38, 'bf5002a8-eeda-11ef-b84b-60189541604e', 'prueba', '2025-02-27', 'evento1', NULL, 'Colima'),
	(39, 'fb449d73-eeda-11ef-b84b-60189541604e', 'prueba', '2025-02-27', 'un pequeño evento 2', NULL, 'Coahuila'),
	(40, '38400d51-eedb-11ef-b84b-60189541604e', '1739981078_067c7cee8346b41446b7.png', '2025-02-19', 'un pequeño evento 2', NULL, 'Campeche'),
	(41, '5380635e-eedb-11ef-b84b-60189541604e', 'prueba1', '2025-02-19', 'un pequeño evento ', NULL, 'Aguascalientes'),
	(42, '7c20e821-eedb-11ef-b84b-60189541604e', 'evento1', '2025-02-19', 'evento1', NULL, 'Campeche'),
	(43, '9d4bc1e1-eedb-11ef-b84b-60189541604e', 'abneas', '2025-02-19', 'un pequeño evento ', NULL, 'Campeche'),
	(46, 'a9729ae1-ef0b-11ef-b84b-60189541604e', 'evento1', '2025-02-19', 'un pequeño evento ', '1740001883_60f1f1a4135542ad98a6.svg', 'Guerrero'),
	(47, 'ed8c04c1-ef0b-11ef-b84b-60189541604e', 'nuevo evento ', '2025-02-19', 'un pequeño evento ', '1740001998_6d8ffbb065fc41bd1c1b.svg', 'Aguascalientes'),
	(48, '6958227e-efbb-11ef-aa39-60189541604e', 'pruebadefinitiva', '2025-02-20', 'un evento', '1740077367_16e9b1a3eef5d2c61c06.svg', 'Guerrero'),
	(49, 'c9823420-f2d8-11ef-b478-60189541604e', 'prueba', '2025-02-24', 'prueba', '1740419837_438485c2d9218ab5ad8a.png', 'Aguascalientes'),
	(50, '66008559-f2d9-11ef-b478-60189541604e', 'prueba', '2025-02-24', 'un pequeño evento ', '1740420100_205ce281d2c060bfdbb2.svg', 'Aguascalientes');

-- Volcando estructura para tabla configurador_mapas.groups
CREATE TABLE IF NOT EXISTS `groups` (
  `id` mediumint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla configurador_mapas.groups: ~0 rows (aproximadamente)
INSERT INTO `groups` (`id`, `name`, `description`) VALUES
	(1, 'admin', 'This is a test description');

-- Volcando estructura para tabla configurador_mapas.login_attempts
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` mediumint unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `login` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `time` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla configurador_mapas.login_attempts: ~0 rows (aproximadamente)

-- Volcando estructura para tabla configurador_mapas.maps
CREATE TABLE IF NOT EXISTS `maps` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int DEFAULT NULL,
  `upload_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `maps_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla configurador_mapas.maps: ~0 rows (aproximadamente)

-- Volcando estructura para tabla configurador_mapas.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla configurador_mapas.migrations: ~0 rows (aproximadamente)
INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
	(1, '20181211100537', 'IonAuth\\Database\\Migrations\\Migration_Install_ion_auth', 'default', 'IonAuth', 1738088181, 1);

-- Volcando estructura para tabla configurador_mapas.stands
CREATE TABLE IF NOT EXISTS `stands` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `x` float NOT NULL,
  `y` float NOT NULL,
  `width` float DEFAULT NULL,
  `height` float DEFAULT NULL,
  `radius` float DEFAULT NULL,
  `stroke_width` float DEFAULT NULL,
  `id_evento` int NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `numero` varchar(255) DEFAULT NULL,
  `estatus` int DEFAULT NULL,
  `contacto` varchar(255) DEFAULT NULL,
  `map_id` int DEFAULT NULL,
  `stand_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` enum('available','occupied','reserved') DEFAULT 'available',
  `description` text,
  PRIMARY KEY (`id`),
  KEY `map_id` (`map_id`),
  CONSTRAINT `stands_ibfk_1` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla configurador_mapas.stands: ~0 rows (aproximadamente)

-- Volcando estructura para tabla configurador_mapas.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` mediumint unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(254) COLLATE utf8mb4_general_ci NOT NULL,
  `activation_selector` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activation_code` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `forgotten_password_selector` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `forgotten_password_code` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `forgotten_password_time` int unsigned DEFAULT NULL,
  `remember_selector` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `remember_code` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_on` int unsigned NOT NULL,
  `last_login` int unsigned DEFAULT NULL,
  `active` tinyint unsigned DEFAULT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `company` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `activation_selector` (`activation_selector`),
  UNIQUE KEY `forgotten_password_selector` (`forgotten_password_selector`),
  UNIQUE KEY `remember_selector` (`remember_selector`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla configurador_mapas.users: ~1 rows (aproximadamente)
INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `email`, `activation_selector`, `activation_code`, `forgotten_password_selector`, `forgotten_password_code`, `forgotten_password_time`, `remember_selector`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`) VALUES
	(2, '127.0.0.1', 'benedmunds', '$2y$12$gaXtENAlIPxXQS8iyJpRKOyd5.WtxBlBuLDNuUmPA5tb36d5D.xbO', 'ben.edmunds@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1738189095, 1741358447, 1, 'Ben', 'Edmunds', NULL, NULL);

-- Volcando estructura para tabla configurador_mapas.users_groups
CREATE TABLE IF NOT EXISTS `users_groups` (
  `id` mediumint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint unsigned NOT NULL,
  `group_id` mediumint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `users_groups_user_id_foreign` (`user_id`),
  KEY `users_groups_group_id_foreign` (`group_id`),
  CONSTRAINT `users_groups_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `users_groups_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla configurador_mapas.users_groups: ~0 rows (aproximadamente)
INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
	(1, 2, 1);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
