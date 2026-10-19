package com.webtechnology;

import java.io.IOException;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
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
 * Question 3: Servlet to fetch Student Profiles & Files from MySQL
 */
public class StudentListServlet extends HttpServlet {

    private static final long serialVersionUID = 1L;

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response) 
            throws ServletException, IOException {

        // Step 1: Session Verification Guard
        HttpSession session = request.getSession(false);
        if (session == null || session.getAttribute("user") == null) {
            // Unauthorized access attempt - redirect to login page
            response.sendRedirect("login.jsp?error=unauthorized");
            return;
        }

        Connection conn = null;
        PreparedStatement studentStmt = null;
        PreparedStatement fileStmt = null;
        ResultSet studentRs = null;
        ResultSet fileRs = null;

        List<Map<String, Object>> studentList = new ArrayList<>();

        try {
            // Step 2: Establish JDBC Connection
            conn = DBConnection.getConnection();

            // Step 3: Query all student records
            String studentSql = "SELECT student_id, name, roll_number, email, course, created_at FROM students ORDER BY student_id ASC";
            studentStmt = conn.prepareStatement(studentSql);
            studentRs = studentStmt.executeQuery();

            // Step 4: Loop through student ResultSet
            while (studentRs.next()) {
                Map<String, Object> studentMap = new HashMap<>();
                int studentId = studentRs.getInt("student_id");
                
                studentMap.put("student_id", studentId);
                studentMap.put("name", studentRs.getString("name"));
                studentMap.put("roll_number", studentRs.getString("roll_number"));
                studentMap.put("email", studentRs.getString("email"));
                studentMap.put("course", studentRs.getString("course"));
                studentMap.put("created_at", studentRs.getTimestamp("created_at"));

                // Step 5: Query associated uploaded file metadata for current student
                String fileSql = "SELECT file_id, original_name, stored_name, file_path, file_type, file_size FROM student_files WHERE student_id = ? ORDER BY file_id ASC";
                fileStmt = conn.prepareStatement(fileSql);
                fileStmt.setInt(1, studentId);
                fileRs = fileStmt.executeQuery();

                List<Map<String, Object>> fileList = new ArrayList<>();
                Integer profileFileId = null;

                while (fileRs.next()) {
                    Map<String, Object> fileMap = new HashMap<>();
                    int fileId = fileRs.getInt("file_id");
                    String fileType = fileRs.getString("file_type");
                    String storedName = fileRs.getString("stored_name");

                    fileMap.put("file_id", fileId);
                    fileMap.put("original_name", fileRs.getString("original_name"));
                    fileMap.put("stored_name", storedName);
                    fileMap.put("file_path", fileRs.getString("file_path"));
                    fileMap.put("file_type", fileType);
                    fileMap.put("file_size", fileRs.getInt("file_size"));

                    fileList.add(fileMap);

                    // Check if file is a profile image
                    if (fileType != null && fileType.startsWith("image/") && profileFileId == null) {
                        profileFileId = fileId;
                    }
                }
                fileRs.close();
                fileStmt.close();

                studentMap.put("files", fileList);
                studentMap.put("profileFileId", profileFileId);

                studentList.add(studentMap);
            }

            // Step 6: Store student list in request attribute
            request.setAttribute("studentList", studentList);

            // Step 7: Forward request and response to JSP for dynamic HTML rendering
            request.getRequestDispatcher("students.jsp").forward(request, response);

        } catch (Exception e) {
            e.printStackTrace();
            request.setAttribute("errorMessage", "Error retrieving student records: " + e.getMessage());
            request.getRequestDispatcher("students.jsp").forward(request, response);

        } finally {
            // Step 8: Safely close all database resources
            try { if (studentRs != null) studentRs.close(); } catch (Exception e) {}
            try { if (studentStmt != null) studentStmt.close(); } catch (Exception e) {}
            try { if (conn != null) conn.close(); } catch (Exception e) {}
        }
    }
}
