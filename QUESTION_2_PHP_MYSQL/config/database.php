<?php
/**
 * KPR Institute of Engineering and Technology
 * Department of Computer Science and Engineering
 * Course: U21CS501 Web Technology | Assignment II
 * 
 * Question 2: MySQL Database Connection Configuration File
 */

// Database configuration settings
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'wt_assignment_q2');

// Establish connection to MySQL database using PHP mysqli extension
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check if database connection failed
if ($conn->connect_error) {
    // Log internal error message safely and output user-friendly message
    error_log("Database Connection Failed: " . $conn->connect_error);
    die("Database Connection Error: Unable to connect to MySQL database. Please verify XAMPP MySQL service is running and database schema is imported.");
}

// Set charset to utf8mb4 for unicode character support
$conn->set_charset("utf8mb4");
?>
