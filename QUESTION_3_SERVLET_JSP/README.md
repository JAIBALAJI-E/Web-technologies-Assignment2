# Question 3: Servlet + JDBC + JSP Student Profile & Byte Streaming Portal

## Overview
This component implements an enterprise Java Web application using standard Servlets, JavaServer Pages (JSP), JDBC database connectivity, session authentication, and HTTP file streaming without external frameworks (No Spring, No Hibernate).

## Architecture & Components
1. **Database Schema (`DB_SCHEMA/question3_schema.sql`)**:
   - `users`: Demo login credentials (admin/admin123).
   - `students`: Student profile records.
   - `student_files`: Associated file metadata.
2. **JDBC Helper (`com.webtechnology.DBConnection`)**:
   - Manages connection lifecycle with MySQL using `com.mysql.cj.jdbc.Driver`.
3. **Login & Session Management (`com.webtechnology.LoginServlet` & `login.jsp`)**:
   - Validates user input against the `users` table via `PreparedStatement`.
   - Creates HTTP Session (`request.getSession(true)`) upon successful authentication.
   - Restricts unauthenticated access to `students.jsp` / `StudentListServlet`.
   - Provides logout session invalidation (`LoginServlet?action=logout`).
4. **Student List Fetcher (`com.webtechnology.StudentListServlet` & `students.jsp`)**:
   - Retrieves student details and file metadata via JDBC.
   - Forwards data object attributes to `students.jsp`.
   - `students.jsp` uses JSP scriptlets and conditional statements to display student cards, profile photos via `<img src="FileServlet?fileId=...">`, and document download links.
5. **Byte-Level File Streaming (`com.webtechnology.FileServlet`)**:
   - Receives `fileId` parameter.
   - Validates user session and path traversal safety (`targetFile.getCanonicalPath().startsWith(...)`).
   - Sets appropriate HTTP `Content-Type` header based on file format.
   - Streams byte chunks from `FileInputStream` to `ServletOutputStream`.

## How to Run (Apache Tomcat + MySQL)
1. Import `DB_SCHEMA/question3_schema.sql` in MySQL.
2. Add the MySQL JDBC Driver `.jar` (e.g. `mysql-connector-j-8.x.x.jar`) to Tomcat's `lib/` directory or project `WEB-INF/lib/`.
3. Compile Java source files into `WEB-INF/classes/` or deploy as a standard Web Application Archive / folder in Tomcat's `webapps/` directory.
4. Start Apache Tomcat.
5. Open browser at `http://localhost:8080/QUESTION_3_SERVLET_JSP/index.jsp`.
6. Login using credentials:
   - **Username**: `admin`
   - **Password**: `admin123`

> These credentials are for local demonstration only. Change the seeded passwords and use password hashing before any deployment.
