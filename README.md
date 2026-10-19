# KPR Institute of Engineering and Technology
## Department of Computer Science and Engineering
### Course: U21CS501 Web Technology | Assignment II

- **Academic Year**: 2026–2027
- **Year / Semester / Section**: III / V / A
- **Issue Date**: 07.10.2026
- **Submission Date**: 20.10.2026
- **Maximum Marks**: 40

---

## Repository Overview & Project Structure

This repository contains the source code, database schemas, screenshots, and documentation for all three assignment questions: **Question 1** (HTML, CSS & JavaScript), **Question 2** (PHP + MySQL CRUD & File Upload), and **Question 3** (Java Servlet + JSP + JDBC + Byte Streaming).

```
WebTechnology_Assignment_II/
│
├── QUESTION_1_HTML_CSS_JS/
│   ├── css/
│   │   └── style.css          # Registration portal styling
│   ├── js/
│   │   └── validation.js      # Client-side form validation
│   ├── sample-upload.png      # PNG fixture for upload validation
│   ├── index.html             # Student admission form
│   └── README.md              # Question 1 instructions
│
├── QUESTION_2_PHP_MYSQL/
│   ├── config/
│   │   └── database.php       # MySQL database connection (mysqli)
│   ├── css/
│   │   └── style.css          # Admin portal styling
│   ├── includes/
│   │   ├── header.php         # Navigation header template
│   │   └── footer.php         # Footer template
│   ├── students/
│   │   ├── list.php           # Student directory list view
│   │   ├── add.php            # Add student & file upload form
│   │   ├── view.php           # Student details & document viewer
│   │   ├── edit.php           # Edit student profile form
│   │   └── delete.php         # Delete student & unlink files
│   ├── uploads/
│   │   └── .gitkeep           # Physical uploads storage directory
│   ├── index.php              # Entry point redirecting to student list
│   ├── README.md              # Question 2 component instructions
│   └── .gitignore             # Question 2 ignore rules
│
├── QUESTION_3_SERVLET_JSP/
│   ├── src/
│   │   └── main/
│   │       ├── java/
│   │       │   └── com/
│   │       │       └── webtechnology/
│   │       │           ├── DBConnection.java       # JDBC database helper
│   │       │           ├── LoginServlet.java       # Authentication & session handler
│   │       │           ├── StudentListServlet.java # JDBC data retriever
│   │       │           └── FileServlet.java        # Byte file streaming servlet
│   │       │
│   │       └── webapp/
│   │           ├── index.jsp                       # Session check entry point
│   │           ├── login.jsp                       # User authentication login
│   │           ├── students.jsp                    # Dynamic JSP student portal
│   │           ├── css/
│   │           │   └── style.css                   # Web portal styling
│   │           └── WEB-INF/
│   │               ├── web.xml                     # Servlet mappings
│   │               └── lib/
│   │                   └── mysql-connector-j-26.7.0.jar # MySQL JDBC driver
│   ├── uploads/
│   │   └── .gitkeep           # Upload storage directory
│   ├── README.md              # Question 3 component instructions
│   └── .gitignore             # Question 3 ignore rules
│
├── DB_SCHEMA/
│   ├── question2_schema.sql   # SQL schema for Question 2 (wt_assignment_q2)
│   └── question3_schema.sql   # SQL schema for Question 3 (wt_assignment_q3)
│
├── DOCUMENTATION/
│   └── WebTechnology_Assignment_II_Documentation.docx
│
├── SCREENSHOTS/
│   ├── QUESTION_1/
│   ├── QUESTION_2/
│   └── QUESTION_3/
│
├── .gitignore                 # Root repository ignore rules
└── README.md                  # Main repository README
```

---

## Technologies Used

| Module | Technology Stack |
| :--- | :--- |
| **Question 1** | HTML5 + CSS3 + Vanilla JavaScript (ES6) |
| **Question 2** | PHP 8 + MySQL (`mysqli`) + HTML5 + CSS3 + XAMPP |
| **Question 3** | Java JDK 17 + Java Servlets 3.1 + JSP 2.3 + JDBC + MySQL + Apache Tomcat 9.0.122 |

---

## Question 1: Student Admission Registration Form

Question 1 is a responsive, client-side student admission form built with **HTML5, CSS3, and Vanilla JavaScript (ES6)**. It runs directly in a browser and does not require a server, database, framework, or third-party library.

### Features

- Collects the student's name, roll number, email, academic course, password, and a document or profile image.
- Validates fields as the user types and again when the form is submitted.
- Checks that the name contains at least three letters, the roll number is 6–15 alphanumeric characters, the email is valid, and a course is selected.
- Requires a password of at least eight characters with uppercase and lowercase letters, a number, and a supported special character (`@$!%*?&`).
- Accepts PDF, JPG, JPEG, and PNG uploads up to 2 MB. The form demonstrates client-side validation only; it does not send or store submissions.
- Includes [`sample-upload.png`](QUESTION_1_HTML_CSS_JS/sample-upload.png), a small PNG fixture for testing the upload field.

### Run and test

1. Open [`QUESTION_1_HTML_CSS_JS/index.html`](QUESTION_1_HTML_CSS_JS/index.html) in a modern web browser.
2. Submit the empty form to see required-field errors, then try invalid values to test individual validation rules.
3. Enter valid values and select `sample-upload.png` to see the success message.

### Screenshots

- [Registration form](SCREENSHOTS/QUESTION_1/Q1_registration_form.png)
- [Successful validation](SCREENSHOTS/QUESTION_1/Q1_success.png)
- [Validation errors](SCREENSHOTS/QUESTION_1/Q1_validation_error.png)

See the [Question 1 README](QUESTION_1_HTML_CSS_JS/README.md) for component-level details.

---

## Question 2: PHP + MySQL Setup & Execution Instructions

### Setup Steps:
1. Install **XAMPP** (or Apache + MySQL server environment).
2. Start **Apache** and **MySQL** services in XAMPP Control Panel.
3. Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
4. Import `DB_SCHEMA/question2_schema.sql` (Creates database `wt_assignment_q2` with `students` and `student_files` tables).
5. Copy the `QUESTION_2_PHP_MYSQL/` folder into your XAMPP `htdocs` directory:
   `C:\xampp\htdocs\QUESTION_2_PHP_MYSQL\`
6. Ensure write permissions exist for `QUESTION_2_PHP_MYSQL/uploads/`.
7. Open browser and navigate to:
   `http://localhost/QUESTION_2_PHP_MYSQL/index.php`

### Main Functionality Implemented:
- **Add Student (`students/add.php`)**: Validates text inputs, roll number uniqueness, file type (`.pdf`, `.jpg`, `.png`), size limit (5MB), generates unique filenames (`uniqid()`), and executes `mysqli` prepared statements.
- **View Directory (`students/list.php`)**: Queries student records with file counts using a `LEFT JOIN`.
- **View Profile (`students/view.php`)**: Displays student info card, profile photo, and uploaded document links.
- **Edit Profile (`students/edit.php`)**: Pre-fills fields for updating student data and uploading additional documents.
- **Delete Student (`students/delete.php`)**: Unlinks physical files from `uploads/` using `unlink()` and deletes student DB records (cascading file metadata removal).

---

## Question 3: Java Servlet + JSP + JDBC Setup & Deployment Instructions

### Prerequisites & Environment:
- **Java JDK**: JDK 17 (e.g. Eclipse Adoptium Temurin JDK 17)
- **Web Container**: Apache Tomcat 9.0.122
- **Database**: MySQL Server
- **JDBC Driver**: `mysql-connector-j-26.7.0.jar` (located in `src/main/webapp/WEB-INF/lib/`)

### Database Setup:
1. Open phpMyAdmin or MySQL CLI.
2. Import `DB_SCHEMA/question3_schema.sql` (Creates database `wt_assignment_q3` with `users`, `students`, and `student_files` tables).
3. Pre-seeded admin user: Username: `admin`, Password: `admin123`.

### Manual Compilation (`javac`):
Compile Java source files using `javac` with `servlet-api.jar` and `mysql-connector-j-26.7.0.jar` in the classpath, outputting compiled bytecode to `WEB-INF/classes/`:

```bash
javac -cp "C:\path\to\apache-tomcat-9.0.122\lib\servlet-api.jar;QUESTION_3_SERVLET_JSP\src\main\webapp\WEB-INF\lib\mysql-connector-j-26.7.0.jar" -d QUESTION_3_SERVLET_JSP\src\main\webapp\WEB-INF\classes QUESTION_3_SERVLET_JSP\src\main\java\com\webtechnology\*.java
```

*(Note: Generated `.class` files are excluded from Git repository commits via `.gitignore` to keep the repository reproducible from source code).*

### Apache Tomcat Deployment:
1. Copy the contents of `QUESTION_3_SERVLET_JSP/src/main/webapp/` into Tomcat's `webapps` directory under a folder named `QUESTION_3_SERVLET_JSP`:
   `C:\path\to\apache-tomcat-9.0.122\webapps\QUESTION_3_SERVLET_JSP\`
2. Ensure the compiled `.class` files exist inside `WEB-INF/classes/com/webtechnology/` in Tomcat.
3. Ensure `mysql-connector-j-26.7.0.jar` exists inside `WEB-INF/lib/` in Tomcat.
4. Start Tomcat using `bin/startup.bat` (Windows) or `bin/startup.sh` (Linux/Mac).
5. Open browser and access:
   `http://localhost:8080/QUESTION_3_SERVLET_JSP/`

### Application URL & Login Credentials:
- **Application URL**: `http://localhost:8080/QUESTION_3_SERVLET_JSP/`
- **Username**: `admin`
- **Password**: `admin123`

---

## Database Schemas Reference

- **Question 2 Schema**: `DB_SCHEMA/question2_schema.sql` (Database: `wt_assignment_q2`)
- **Question 3 Schema**: `DB_SCHEMA/question3_schema.sql` (Database: `wt_assignment_q3`)

---

## Important Notes & Guidelines

- **No Framework Dependencies**: Built strictly using native PHP 8 and standard Java Servlets 3.1 / JSP 2.3 without Spring Boot, Hibernate, Laravel, or external UI frameworks.
- **Excluded Binary Installations**: Apache Tomcat server installation, JDK, XAMPP, and compiled `.class` files are excluded from Git commits via `.gitignore`.
- **Security & Credentials**: Database connections use standard local development configurations (`root` user with empty password for XAMPP MySQL). The seeded `admin/admin123` login is for local demonstration only; change it and use password hashing before deployment. No real passwords, API keys, or private tokens are included.
- **Repository Readiness**: All source files, SQL scripts, `.gitignore` rules, documentation, and screenshots are organized and ready for manual Git repository upload.
