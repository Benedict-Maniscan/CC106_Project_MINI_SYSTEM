CREATE DATABASE IF NOT EXISTS ems_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ems_db;

CREATE TABLE IF NOT EXISTS departments (
 department_id INT AUTO_INCREMENT PRIMARY KEY,
 department_name VARCHAR(80) NOT NULL UNIQUE,
 location VARCHAR(80) NOT NULL
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS positions (
 position_id INT AUTO_INCREMENT PRIMARY KEY,
 position_title VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS employees (
 employee_id INT AUTO_INCREMENT PRIMARY KEY,
 emp_no VARCHAR(20) NOT NULL UNIQUE,
 first_name VARCHAR(60) NOT NULL,
 last_name VARCHAR(60) NOT NULL,
 email VARCHAR(120) NOT NULL UNIQUE,
 phone VARCHAR(20) NOT NULL,
 hire_date DATE NOT NULL,
 salary DECIMAL(10,2) NOT NULL DEFAULT 0 CHECK (salary >= 0),
 status ENUM('Active','On Leave','Resigned') NOT NULL DEFAULT 'Active',
 department_id INT NOT NULL,
 position_id INT NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_emp_dept FOREIGN KEY (department_id) REFERENCES departments(department_id) ON UPDATE CASCADE,
 CONSTRAINT fk_emp_position FOREIGN KEY (position_id) REFERENCES positions(position_id) ON UPDATE CASCADE
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS users (
 user_id INT AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(50) NOT NULL UNIQUE,
 full_name VARCHAR(100) NOT NULL,
 email VARCHAR(120) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 role ENUM('Admin','User') NOT NULL DEFAULT 'User',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS projects (
 project_id INT AUTO_INCREMENT PRIMARY KEY,
 project_name VARCHAR(100) NOT NULL UNIQUE,
 start_date DATE NOT NULL
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS employee_projects (
 employee_id INT NOT NULL,
 project_id INT NOT NULL,
 role_in_project VARCHAR(60) NOT NULL,
 PRIMARY KEY (employee_id, project_id),
 FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE,
 FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT IGNORE INTO departments (department_id,department_name,location) VALUES
(1,'Human Resources','Floor 2'),(2,'Finance','Floor 3'),(3,'Information Technology','Floor 4'),(4,'Marketing','Floor 1');
INSERT IGNORE INTO positions (position_id,position_title) VALUES
(1,'Manager'),(2,'Senior Analyst'),(3,'Developer'),(4,'Coordinator'),(5,'Associate');
INSERT IGNORE INTO projects (project_id,project_name,start_date) VALUES
(1,'Payroll Revamp','2024-01-10'),(2,'Website Relaunch','2024-05-02');
INSERT IGNORE INTO employees (emp_no,first_name,last_name,email,phone,hire_date,salary,status,department_id,position_id) VALUES
('EMP-0001','Isabella','Reyes','isabella.reyes@example.com','09171234567','2021-03-15',65000,'Active',1,1),
('EMP-0002','Marcus','Santos','marcus.santos@example.com','09181234567','2022-07-01',48000,'Active',3,3),
('EMP-0003','Elena','Cruz','elena.cruz@example.com','09191234567','2020-11-20',52000,'On Leave',2,2),
('EMP-0004','Julian','Garcia','julian.garcia@example.com','09201234567','2023-01-09',38000,'Active',4,4);
INSERT IGNORE INTO employee_projects (employee_id,project_id,role_in_project) VALUES
(2,1,'Lead Developer'),(2,2,'Developer'),(3,1,'Analyst'),(4,2,'Content Lead');
