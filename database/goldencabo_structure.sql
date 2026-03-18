-- Golden Cabo Transportation — Database Structure
-- Import this file in Hostinger phpMyAdmin

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `reservations` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_number` varchar(20) NOT NULL,
  `zone` tinyint(3) UNSIGNED NOT NULL,
  `zone_name` varchar(60) NOT NULL,
  `hotel` varchar(120) NOT NULL,
  `first_name` varchar(80) NOT NULL,
  `last_name` varchar(80) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(25) NOT NULL,
  `passengers` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `direction` enum('air','htl') NOT NULL DEFAULT 'air',
  `trip_type` enum('one','rnd') NOT NULL DEFAULT 'one',
  `arrival_flight` varchar(15) DEFAULT NULL,
  `departure_flight` varchar(15) DEFAULT NULL,
  `arrival_date` date NOT NULL,
  `arrival_time` time DEFAULT NULL,
  `price` varchar(20) NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'confirmed',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reservations_order_number_unique` (`order_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `contacts` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` varchar(80) NOT NULL,
  `last_name` varchar(80) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(25) DEFAULT NULL,
  `service` varchar(80) DEFAULT NULL,
  `message` text NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` VALUES
(1, '2024_01_01_000001_create_reservations_table', 1),
(2, '2024_01_01_000002_create_contacts_table', 1);

COMMIT;
