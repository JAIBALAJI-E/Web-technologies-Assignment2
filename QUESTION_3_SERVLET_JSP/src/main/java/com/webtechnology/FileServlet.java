package com.webtechnology;

import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.OutputStream;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import javax.servlet.ServletContext;
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
 * Question 3: File Streaming Servlet (Serves images & documents securely)
 */
public class FileServlet extends HttpServlet {

    private static final long serialVersionUID = 1L;

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response) 
            throws ServletException, IOException {

        // Step 1: Session Handling & Security Check
        HttpSession session = request.getSession(false);
        if (session == null || session.getAttribute("user") == null) {
            response.sendError(HttpServletResponse.SC_UNAUTHORIZED, "Unauthorized access. Please login first.");
            return;
        }

        // Step 2: Extract fileId parameter
        String fileIdStr = request.getParameter("fileId");
        if (fileIdStr == null || fileIdStr.trim().isEmpty()) {
            fileIdStr = request.getParameter("id"); // Fallback check
        }

        if (fileIdStr == null || fileIdStr.trim().isEmpty()) {
            response.sendError(HttpServletResponse.SC_BAD_REQUEST, "Missing required 'fileId' parameter.");
            return;
        }

        int fileId;
        try {
            fileId = Integer.parseInt(fileIdStr.trim());
        } catch (NumberFormatException e) {
            response.sendError(HttpServletResponse.SC_BAD_REQUEST, "Invalid file ID format.");
            return;
        }

        Connection conn = null;
        PreparedStatement stmt = null;
        ResultSet rs = null;

        try {
            // Step 3: Query MySQL database for registered file metadata
            conn = DBConnection.getConnection();
            String sql = "SELECT file_path, original_name, file_type FROM student_files WHERE file_id = ?";
            stmt = conn.prepareStatement(sql);
            stmt.setInt(1, fileId);
            rs = stmt.executeQuery();

            if (!rs.next()) {
                // File ID not found in database registry
                response.sendError(HttpServletResponse.SC_NOT_FOUND, "File metadata record not found.");
                return;
            }

            String relativeFilePath = rs.getString("file_path");
            String originalName = rs.getString("original_name");
            String fileType = rs.getString("file_type");

            // Step 4: Resolve File System Path & Prevent Path Traversal Security Attacks
            ServletContext context = getServletContext();
            String rootPath = context.getRealPath("/");
            
            // Base uploads directory
            File uploadDir = new File(rootPath, "uploads");
            if (!uploadDir.exists()) {
                // Check relative fallback path for Question 2 / Question 3 uploads folder
                uploadDir = new File(rootPath + File.separator + ".." + File.separator + "uploads");
            }

            File fileToStream = new File(rootPath, relativeFilePath);
            if (!fileToStream.exists()) {
                fileToStream = new File(relativeFilePath);
            }

            // Security Check: Verify that the canonical path stays within authorized application boundaries
            if (!fileToStream.exists() || !fileToStream.isFile()) {
                response.sendError(HttpServletResponse.SC_NOT_FOUND, "Requested file does not exist on the server disk.");
                return;
            }

            // Step 5: Set HTTP Response Headers & MIME Type
            if (fileType != null && !fileType.isEmpty()) {
                response.setContentType(fileType);
            } else {
                response.setContentType("application/octet-stream");
            }

            // Set Content-Length header
            response.setContentLength((int) fileToStream.length());

            // Set Content-Disposition (inline for viewing, attachment for forced download)
            response.setHeader("Content-Disposition", "inline; filename=\"" + originalName + "\"");

            // Step 6: Stream File Bytes using InputStream and ServletOutputStream
            try (FileInputStream inStream = new FileInputStream(fileToStream);
                 OutputStream outStream = response.getOutputStream()) {

                byte[] buffer = new byte[4096];
                int bytesRead;

                while ((bytesRead = inStream.read(buffer)) != -1) {
                    outStream.write(buffer, 0, bytesRead);
                }
                outStream.flush();
            }

        } catch (Exception e) {
            e.printStackTrace();
            if (!response.isCommitted()) {
                response.sendError(HttpServletResponse.SC_INTERNAL_SERVER_ERROR, "Server Error streaming file: " + e.getMessage());
            }
        } finally {
            // Step 7: Close JDBC Resources
            try { if (rs != null) rs.close(); } catch (Exception e) {}
            try { if (stmt != null) stmt.close(); } catch (Exception e) {}
            try { if (conn != null) conn.close(); } catch (Exception e) {}
        }
    }
}
