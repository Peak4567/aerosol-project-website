-- =====================================================================
-- Aerosol Project - 01_create_database.sql
-- Creates the database and a dedicated application user with just
-- enough privileges to run the Next.js app.
--
-- Run this file FIRST, as a MySQL user that can create databases/users
-- (e.g. root):
--
--   mysql -u root -p < 01_create_database.sql
--
-- Then run 02_schema.sql and 03_seed.sql (in that order) against the
-- `aerosol` database, or simply:
--
--   mysql -u root -p aerosol < 02_schema.sql
--   mysql -u root -p aerosol < 03_seed.sql
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `aerosol`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Dedicated least-privilege application user.
-- Change 'aerosol1234' before running this in anything but a local/dev
-- environment, and keep it in sync with DB_PASSWORD in your .env file.
CREATE USER IF NOT EXISTS 'aerosol'@'localhost' IDENTIFIED BY 'aerosol1234';

GRANT SELECT, INSERT, UPDATE, DELETE ON `aerosol`.* TO 'aerosol'@'localhost';

-- If your Next.js app connects from a different host (e.g. Docker),
-- also grant to that host, for example:
-- CREATE USER IF NOT EXISTS 'aerosol'@'%' IDENTIFIED BY 'aerosol1234';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON `aerosol`.* TO 'aerosol'@'%';

FLUSH PRIVILEGES;
