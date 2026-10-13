CODER EMPLOYEE MANAGEMENT SYSTEM
================================

IMPORTANT DATABASE NOTE
- The existing database.sql has been preserved and is not modified by this package.
- This application expects the existing ems_db database and its tables: departments, positions, employees, users, projects, employee_projects.
- The application does not automatically alter or migrate your database.

INSTALL ON LARAGON
1. Extract the Coder_EMS folder into C:\laragon\www\
2. Start Apache and MySQL in Laragon.
3. Open phpMyAdmin and import your existing database.sql only if ems_db has not already been created.
4. Check config.php. Default Laragon credentials are host=localhost, database=ems_db, username=root, blank password. Change these only if your local MySQL uses different credentials.
5. Visit http://localhost/Coder_EMS/login.php

LOGIN ACCOUNTS
- Sign in using an existing approved account. New users can sign up from the login page and must wait for administrator approval.
- The uploaded install.php from the starter project was intentionally not included because it creates predictable default passwords.
- The administrator no longer creates user accounts manually. New registrations are stored as hashed-password requests in data/pending_users.json until approved. The existing SQL file and database schema are not changed.

FEATURES
- Responsive sidebar navigation and mobile slide-out menu
- Login/logout, session regeneration, role-based pages and CSRF checks
- Dashboard summary cards, recent employees, department headcount chart
- Employee add/edit/delete, validation, search, filters, pagination and CSV export
- Project management using existing projects and employee_projects tables
- Department and position directories
- Workforce and salary analytics (salary figures shown to administrators only)
- Public sign-up with administrator approval; administrators can approve or reject registration requests and manage existing account roles
- Print-friendly pages

PERMISSIONS
- Admin: add/edit/delete employees, manage projects and user accounts, view salary analytics.
- User: view/search employee directory, projects, departments, positions and non-sensitive workforce reports; export does not include salary.
- Salary visibility is hidden in the interface for non-admins. For production, also review any custom endpoints you add.

NOTES
- Use only on a trusted local environment until HTTPS, production-grade deployment configuration, backups, and a security review are completed.
- Back up your database before any major change.
