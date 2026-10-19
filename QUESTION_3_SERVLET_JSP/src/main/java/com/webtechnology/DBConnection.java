package com.webtechnology;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

/**
 * KPR Institute of Engineering and Technology
 * Department of Computer Science and Engineering
 * Course: U21CS501 Web Technology | Assignment II
 * 
 * Question 3: Utility class to manage MySQL JDBC Database Connection
 */
public class DBConnection {

    // Database Connection Configuration Constants
    private static final String JDBC_DRIVER = "com.mysql.cj.jdbc.Driver";
    private static final String DB_URL = "jdbc:mysql://localhost:3306/wt_assignment_q3?useSSL=false&allowPublicKeyRetrieval=true&serverTimezone=UTC";
    private static final String USER = "root";
    private static final String PASS = "";

    /**
     * Establishes and returns a new active Connection to the MySQL database.
     * 
     * @return Connection object
     * @throws SQLException if a database access error occurs
     * @throws ClassNotFoundException if the MySQL JDBC Driver is not found
     */
    public static Connection getConnection() throws SQLException, ClassNotFoundException {
        // Step 1: Explicitly load and register the MySQL JDBC Driver class
        Class.forName(JDBC_DRIVER);

        // Step 2: Establish connection using DriverManager
        return DriverManager.getConnection(DB_URL, USER, PASS);
    }
}
