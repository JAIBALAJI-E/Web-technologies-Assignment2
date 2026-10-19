package com.webtechnology;

import java.io.IOException;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;

/**
 * KPR Institute of Engineering and Technology
 * Department of Computer Science and Engineering
 * Course: U21CS501 Web Technology | Assignment II
 * 
 * Question 3: Login & Session Management Servlet
 */
public class LoginServlet extends HttpServlet {
    
    private static final long serialVersionUID = 1L;

    /**
     * Handles GET requests for Logout action.
     */
    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response) 
            throws ServletException, IOException {
        
        String action = request.getParameter("action");
        if ("logout".equalsIgnoreCase(action)) {
            // Step 1: Retrieve active session if exists
            HttpSession session = request.getSession(false);
            if (session != null) {
                // Step 2: Invalidate existing HTTP session to log out user safely
                session.invalidate();
            }
            // Step 3: Redirect user back to login page
            response.sendRedirect("login.jsp?msg=logged_out");
            return;
        }

        // Default GET redirect to login page
        response.sendRedirect("login.jsp");
    }

    /**
     * Handles POST requests for User Authentication.
     */
    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response) 
            throws ServletException, IOException {
        
        // Retrieve submitted login credentials from HTML form
        String usernameInput = request.getParameter("username");
        String passwordInput = request.getParameter("password");

        // Simple validation for empty fields
        if (usernameInput == null || usernameInput.trim().isEmpty() ||
            passwordInput == null || passwordInput.trim().isEmpty()) {
            
            request.setAttribute("errorMessage", "Username and Password are required fields.");
            request.getRequestDispatcher("login.jsp").forward(request, response);
            return;
        }

        Connection conn = null;
        PreparedStatement stmt = null;
        ResultSet rs = null;

        try {
            // Step 1: Obtain JDBC database connection
            conn = DBConnection.getConnection();

            // Step 2: Prepare SQL query using PreparedStatement to prevent SQL Injection
            String sql = "SELECT user_id, username, full_name, role FROM users WHERE username = ? AND password = ?";
            stmt = conn.prepareStatement(sql);
            stmt.setString(1, usernameInput.trim());
            stmt.setString(2, passwordInput.trim());

            // Step 3: Execute query and evaluate ResultSet
            rs = stmt.executeQuery();

            if (rs.next()) {
                // Authentication Successful!
                String username = rs.getString("username");
                String fullName = rs.getString("full_name");
                String role = rs.getString("role");

                // Step 4: Create a new HttpSession object
                HttpSession session = request.getSession(true);
                session.setAttribute("user", username);
                session.setAttribute("fullName", fullName);
                session.setAttribute("role", role);
                
                // Set session max inactive interval (e.g. 30 minutes = 1800 seconds)
                session.setMaxInactiveInterval(1800);

                // Step 5: Redirect authenticated user to protected Student List Servlet
                response.sendRedirect("StudentListServlet");

            } else {
                // Authentication Failed!
                request.setAttribute("errorMessage", "Invalid Username or Password. Please check your credentials.");
                request.getRequestDispatcher("login.jsp").forward(request, response);
            }

        } catch (Exception e) {
            e.printStackTrace();
            request.setAttribute("errorMessage", "Database Error during authentication: " + e.getMessage());
            request.getRequestDispatcher("login.jsp").forward(request, response);

        } finally {
            // Step 6: Close JDBC resources in finally block to prevent resource leaks
            try { if (rs != null) rs.close(); } catch (Exception e) {}
            try { if (stmt != null) stmt.close(); } catch (Exception e) {}
            try { if (conn != null) conn.close(); } catch (Exception e) {}
        }
    }
}
