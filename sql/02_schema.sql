-- =====================================================================
-- Aerosol Project - 02_schema.sql
-- Table definitions for the Next.js app. Run after 01_create_database.sql.
--
--   mysql -u root -p aerosol < 02_schema.sql
-- =====================================================================

USE `aerosol`;

-- ---------------------------------------------------------------------
-- users: application accounts (member = normal dashboard user,
-- admin = can also reach /dashboard/settings and /dashboard/users).
-- Passwords are always bcrypt hashes (see lib/auth.ts / lib/password.ts),
-- never plaintext or md5.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`   VARCHAR(255) NOT NULL,
  `email`      VARCHAR(255) NOT NULL,
  `level`      ENUM('member', 'admin') NOT NULL DEFAULT 'member',
  `password`   VARCHAR(255) NOT NULL COMMENT 'bcrypt hash',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_username` (`username`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- settings: simple key/value store for site configuration
-- (site_name, site_description, site_logo, maintenance_mode, ...).
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key`   VARCHAR(100) NOT NULL,
  `setting_value` TEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_settings_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- sensor: time-series readings pushed by the ESP32 / simulator via
-- POST /api/sensor-data. An index on create_at is added because every
-- dashboard query is "ORDER BY create_at/id DESC LIMIT 100".
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sensor` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `temperature` FLOAT NOT NULL DEFAULT 0,
  `humidity`    FLOAT NOT NULL DEFAULT 0,
  `tds_value`   FLOAT NOT NULL DEFAULT 0,
  `ec_value`    FLOAT NOT NULL DEFAULT 0,
  `ph_value`    FLOAT NOT NULL DEFAULT 0,
  `create_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sensor_create_at` (`create_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
