-- ============================================================
-- KPR Institute of Engineering and Technology
-- Department of Computer Science and Engineering
-- Course: U21CS501 Web Technology | Assignment II
-- Database Schema for Question 3 (Servlet + JDBC + JSP + File Streaming)
-- ============================================================

-- Create database if it does not already exist
CREATE DATABASE IF NOT EXISTS wt_assignment_q3;
USE wt_assignment_q3;

-- Drop existing tables if re-importing to ensure clean slate
DROP TABLE IF EXISTS student_files;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS users;

-- Create 'users' table for authentication and session management
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role VARCHAR(20) DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create 'students' table to store student details
CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    roll_number VARCHAR(30) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL,
    course VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create 'student_files' table to store document metadata linked to student
CREATE TABLE student_files (
    file_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL UNIQUE,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(100) NOT NULL,
    file_size INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_q3_student_file FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin and student login credentials
INSERT INTO users (user_id, username, password, full_name, role) VALUES
(1, 'admin', 'admin123', 'System Administrator', 'admin'),
(2, 'faculty', 'faculty123', 'Sample Faculty', 'faculty');

-- Insert sample student profiles
INSERT INTO students (student_id, name, roll_number, email, course, created_at) VALUES
(1, 'Alex Morgan', 'STU000001', 'student001@example.com', 'Computer Science and Engineering', '2026-10-07 10:00:00'),
(2, 'Taylor Jordan', 'STU000002', 'student002@example.com', 'Computer Science and Engineering', '2026-10-07 10:15:00'),
(3, 'Casey Rivera', 'STU000003', 'student003@example.com', 'Electronics and Communication Engineering', '2026-10-07 10:30:00');

-- Insert sample file metadata for file streaming via Servlet
INSERT INTO student_files (file_id, student_id, original_name, stored_name, file_path, file_type, file_size, uploaded_at) VALUES
(1, 1, 'profile_student001.jpg', 'img_67041a_profile.jpg', 'uploads/img_67041a_profile.jpg', 'image/jpeg', 145200, '2026-10-07 10:05:00'),
(2, 1, 'admission_certificate.pdf', 'doc_67041b_cert.pdf', 'uploads/doc_67041b_cert.pdf', 'application/pdf', 524000, '2026-10-07 10:06:00'),
(3, 2, 'profile_student002.png', 'img_67042a_profile.png', 'uploads/img_67042a_profile.png', 'image/png', 189000, '2026-10-07 10:20:00');
