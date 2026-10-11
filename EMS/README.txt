MAISON EMPLOYEE MANAGEMENT SYSTEM — LARAGON SETUP

1. Copy the ems_ready folder into C:\laragon\www\ and rename it to ems.
2. Start Apache and MySQL in Laragon.
3. Open http://localhost/phpmyadmin and import ems/database.sql.
4. Open http://localhost/ems/install.php once to create the initial accounts.
5. Delete install.php immediately after it runs.
6. Open http://localhost/ems/login.php.

Initial accounts (change these passwords after first login):
Admin: admin / admin123
User: user / user123

Features included:
- Login, signup, hashed passwords, session regeneration, CSRF checks
- Admin/user access roles; users have read-only employee access
- Employee create, read, update, delete with server-side validation
- Search, department/status filters, pagination
- Department and position JOINs, project count, department headcount and average salary
- CSV export and printable report (Save as PDF)
- Responsive black-and-gold theme
- Normalized database with departments, positions, employees, users, projects, employee_projects

Duplicate prevention:
- Unique constraints protect employee number, email, department name, position title, username, project name, and user email.
- The supplied sample inserts use INSERT IGNORE so re-importing sample seed statements does not duplicate the sample rows with the same unique keys.
- Employee-project pairs use a composite primary key to prevent duplicate assignments.

Notes:
- The database uses MySQL-compatible InnoDB tables and foreign keys.
- For production use, configure a non-root MySQL account, HTTPS, stronger login rate limiting, and backups.
