-- =====================================================================
-- Aerosol Project - 03_seed.sql
-- Default settings + one admin account so you can log in immediately.
-- Run after 02_schema.sql.
--
--   mysql -u root -p aerosol < 03_seed.sql
--
-- !! SECURITY !!
-- Default admin login is:
--   username: aerosol
--   password: Aerosol@2026
-- Log in once, then change the password immediately from
-- /dashboard/users (or update the `users` table directly with a new
-- bcrypt hash). Never leave the seeded password on a public deployment.
-- =====================================================================

USE `aerosol`;

-- ---------------------------------------------------------------------
-- Default site settings
-- ---------------------------------------------------------------------
INSERT INTO `settings` (`setting_key`, `setting_value`)
VALUES
  ('site_name', 'Aerosol'),
  ('site_description', 'Aerosol นวัตกรรม AeroFogPonic Farming'),
  ('maintenance_mode', '0'),
  ('site_logo', '')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);

-- ---------------------------------------------------------------------
-- Default admin user
-- Password hash below is bcrypt (10 rounds) for: Aerosol@2026
-- ---------------------------------------------------------------------
INSERT INTO `users` (`username`, `email`, `level`, `password`)
VALUES
  ('aerosol', 'aerosol@example.com', 'admin',
   '$2b$10$N26ljVEs3aNaiTmyuBg2C.nBvKTZ0FaCIf3ewVAJm5rTOS9fUiQnK')
ON DUPLICATE KEY UPDATE `username` = `username`;

-- ---------------------------------------------------------------------
-- (Optional) a few sample sensor readings so the dashboard chart isn't
-- empty on first run. Safe to delete — real data will arrive from the
-- ESP32 device or /simulator page. Comment this block out if you don't
-- want sample data.
-- ---------------------------------------------------------------------
INSERT INTO `sensor` (`temperature`, `humidity`, `tds_value`, `ec_value`, `ph_value`, `create_at`)
VALUES
  (26.1, 63.2, 720, 1.4, 6.4, NOW() - INTERVAL 5 MINUTE),
  (26.0, 63.5, 715, 1.4, 6.4, NOW() - INTERVAL 4 MINUTE),
  (25.9, 63.8, 710, 1.3, 6.5, NOW() - INTERVAL 3 MINUTE),
  (25.9, 64.0, 708, 1.3, 6.5, NOW() - INTERVAL 2 MINUTE),
  (26.0, 63.9, 712, 1.4, 6.4, NOW() - INTERVAL 1 MINUTE);
