# Flictix Web (PHP + MySQL)
Simple Netflix-like PHP project (demo) named **flictix_web**.

## What is included
- PHP files (index, movie detail, auth, admin)
- `config.php` to configure DB connection
- `sql/setup.sql` with table creation and sample data
- Basic styling in `assets/style.css`
- Instructions to run locally

## Requirements
- PHP 7.4+ (with mysqli)
- MySQL or MariaDB
- A web server (XAMPP, WAMP, Laragon, etc.)

## Setup
1. Import `sql/setup.sql` into your MySQL server (e.g., using phpMyAdmin or `mysql` CLI):
   ```sql
   source sql/setup.sql;
   ```
2. Update `config.php` with your DB credentials.
3. Put the `flictix_web` folder into your web server's www/htdocs folder OR run built-in PHP server:
   ```bash
   php -S localhost:8000 -t .
   ```
4. Open http://localhost:8000 in your browser.

## Default admin user
- email: admin@flictix.test
- password: admin123

## Notes
- Thumbnails use external URLs; replace with local images in `assets/` if you prefer.
- This is a demo starter — extend features, improve security, add routing, etc.
