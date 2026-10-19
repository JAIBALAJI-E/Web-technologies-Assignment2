# Question 2: PHP + MySQL CRUD Administration & File Upload Portal

## Overview
This module provides a complete student administration portal supporting Full CRUD (Create, Read, Update, Delete) operations and file upload management linked to MySQL relational database tables using `mysqli` prepared statements.

## Features & Implementation
1. **Database Schema (`DB_SCHEMA/question2_schema.sql`)**:
   - `students`: Primary student details (student_id, name, roll_number, email, course, created_at).
   - `student_files`: Document metadata linked via foreign key with CASCADE deletion.
2. **Database Config (`config/database.php`)**:
   - Manages connection parameters and connection error reporting.
3. **CRUD Operations (`students/`)**:
   - `list.php`: Renders directory table of registered students with attached file counts.
   - `add.php`: Form for student registration with profile image and document file uploads. Validates input fields, roll number uniqueness, file format (`.pdf`, `.jpg`, `.png`), size limit (5MB), generates unique filenames (`uniqid()`), and executes prepared statement queries.
   - `view.php`: Displays individual student details, profile photo, and attached document links.
   - `edit.php`: Pre-fills student record for updates and allows uploading additional files.
   - `delete.php`: Unlinks physical files from `uploads/` directory and removes database entries safely.

## Setup & How to Run (XAMPP / Apache / MySQL)
1. Copy the `QUESTION_2_PHP_MYSQL` folder to your XAMPP `htdocs` directory (e.g. `C:\xampp\htdocs\QUESTION_2_PHP_MYSQL`).
2. Start **Apache** and **MySQL** services in XAMPP Control Panel.
3. Open phpMyAdmin (`http://localhost/phpmyadmin`) and import `DB_SCHEMA/question2_schema.sql`.
4. Ensure the `uploads/` directory has write permissions.
5. Open your browser and navigate to `http://localhost/QUESTION_2_PHP_MYSQL/index.php`.
