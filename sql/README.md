# Database setup (MySQL)

These scripts create everything the Next.js app needs from scratch. Run them **in order**.

## 1. Prerequisites

- MySQL 8.x or MariaDB 10.4+ (matches the original XAMPP/MariaDB setup)
- A MySQL client: the `mysql` CLI, phpMyAdmin, MySQL Workbench, HeidiSQL, etc.

## 2. Run the scripts (CLI)

```bash
# 1) Creates the `aerosol` database + a dedicated `aerosol` DB user
mysql -u root -p < 01_create_database.sql

# 2) Creates the users / settings / sensor tables
mysql -u root -p aerosol < 02_schema.sql

# 3) Seeds default settings + one admin login + a few sample sensor rows
mysql -u root -p aerosol < 03_seed.sql
```

### Using phpMyAdmin instead

Open the SQL tab against the `aerosol` database and paste the contents of
each file in order: `01_create_database.sql` → `02_schema.sql` →
`03_seed.sql`.

## 3. Configure the app

Copy `.env.example` (at the project root) to `.env` and match the
credentials you used above:

```
DB_HOST=localhost
DB_PORT=3306
DB_USER=aerosol
DB_PASSWORD=aerosol1234
DB_NAME=aerosol
```

## 4. Default login

After seeding, you can log in with:

- **username:** `aerosol`
- **password:** `Aerosol@2026`

**Change this password immediately** (via `/dashboard/users` once logged
in as admin) if this database is reachable from anywhere other than your
own machine.

## 5. Table overview

| Table      | Purpose                                                              |
|------------|-----------------------------------------------------------------------|
| `users`    | Login accounts. `level` is `member` or `admin`. Passwords are bcrypt. |
| `settings` | Site-wide key/value config: `site_name`, `site_description`, `site_logo`, `maintenance_mode` (`'1'`/`'0'`). |
| `sensor`   | Time-series readings (`temperature`, `humidity`, `tds_value`, `ec_value`, `ph_value`, `create_at`) pushed by the ESP32 device or `/simulator` dev tool via `POST /api/sensor-data`. |

## 6. Resetting

To start over, just re-run `01_create_database.sql` (it's `CREATE
DATABASE IF NOT EXISTS`, so it won't wipe data) — if you want a hard
reset, drop the database first:

```sql
DROP DATABASE IF EXISTS aerosol;
```

then re-run all three scripts.
