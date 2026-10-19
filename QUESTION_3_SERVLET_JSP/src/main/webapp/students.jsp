<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ page import="java.util.List" %>
<%@ page import="java.util.Map" %>

<%-- 
  KPR Institute of Engineering and Technology
  Department of Computer Science and Engineering
  Course: U21CS501 Web Technology | Assignment II
  
  Question 3: Protected Student Portal JSP Page
--%>

<%
    // Step 1: Session Verification Guard
    HttpSession userSession = request.getSession(false);
    if (userSession == null || userSession.getAttribute("user") == null) {
        // Redirect unauthorized user to login page
        response.sendRedirect("login.jsp?error=unauthorized");
        return;
    }

    String loggedUser = (String) userSession.getAttribute("user");
    String fullName = (String) userSession.getAttribute("fullName");

    // Step 2: Retrieve Student List forwarded by StudentListServlet
    @SuppressWarnings("unchecked")
    List<Map<String, Object>> studentList = (List<Map<String, Object>>) request.getAttribute("studentList");
%>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile Portal - KPRIET</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Header Navigation Bar -->
    <header class="navbar">
        <a href="StudentListServlet" class="navbar-brand">KPRIET <span>Student Directory</span></a>
        <div>
            <span class="user-badge">Logged in as: <strong><%= fullName != null ? fullName : loggedUser %></strong></span>
            <a href="LoginServlet?action=logout" class="btn-logout">Logout</a>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="container">
        
        <div style="margin-bottom: 25px;">
            <h2 style="color: #1a365d; font-size: 1.6rem;">Registered Student Profiles</h2>
            <p style="color: #666; font-size: 0.95rem;">
                Images and document files are dynamically streamed directly from the database using <code>FileServlet</code>.
            </p>
        </div>

        <!-- Student Profiles Listing Loop -->
        <div class="student-grid">
            <%
                // Step 3: Loop through student records if list is present and non-empty
                if (studentList != null && !studentList.isEmpty()) {
                    for (Map<String, Object> student : studentList) {
                        Integer profileFileId = (Integer) student.get("profileFileId");
                        @SuppressWarnings("unchecked")
                        List<Map<String, Object>> files = (List<Map<String, Object>>) student.get("files");
            %>
                        <!-- Individual Student Card -->
                        <div class="student-card">
                            
                            <!-- 1. Conditional Logic for Profile Image Display -->
                            <div class="student-avatar">
                                <% if (profileFileId != null) { %>
                                    <!-- Dynamic <img> tag pointing directly to FileServlet with fileId parameter -->
                                    <img src="FileServlet?fileId=<%= profileFileId %>" alt="Profile Image">
                                <% } else { %>
                                    <!-- Fallback conditional message when no profile image exists -->
                                    <div style="text-align: center; color: #a0aec0;">
                                        <div style="font-size: 2.5rem; line-height: 1;">&#128100;</div>
                                        <small>No Image Available</small>
                                    </div>
                                <% } %>
                            </div>

                            <!-- 2. Student Information Display -->
                            <div class="student-details">
                                <h3><%= student.get("name") %></h3>
                                <span class="roll-tag"><%= student.get("roll_number") %></span>

                                <div class="detail-row">
                                    <strong>Email:</strong> <%= student.get("email") %>
                                </div>
                                <div class="detail-row">
                                    <strong>Course:</strong> <%= student.get("course") %>
                                </div>
                                <div class="detail-row">
                                    <strong>Student ID:</strong> #<%= student.get("student_id") %>
                                </div>

                                <!-- 3. Conditional Logic for Document List Display -->
                                <div class="doc-section">
                                    <h4>Attached Documents & Proofs:</h4>
                                    <div class="doc-link-list">
                                        <%
                                            if (files != null && !files.isEmpty()) {
                                                for (Map<String, Object> docFile : files) {
                                                    int docId = (Integer) docFile.get("file_id");
                                                    String docName = (String) docFile.get("original_name");
                                        %>
                                                    <!-- Secure Link to FileServlet for Document Streaming -->
                                                    <a href="FileServlet?fileId=<%= docId %>" target="_blank" class="doc-link">
                                                        &#128196; View Document (<%= docName %>)
                                                    </a>
                                        <%
                                                }
                                            } else {
                                        %>
                                                <!-- Fallback conditional message when no documents are uploaded -->
                                                <span style="color: #718096; font-size: 0.85rem; font-style: italic;">
                                                    No documents uploaded.
                                                </span>
                                        <%
                                            }
                                        %>
                                    </div>
                                </div>
                            </div>
                        </div>
            <%
                    }
                } else {
            %>
                    <div class="card" style="text-align: center; color: #718096; padding: 40px;">
                        <h3>No Student Profiles Available</h3>
                        <p>Please check your MySQL database configuration and seed data script.</p>
                    </div>
            <%
                }
            %>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 KPR Institute of Engineering and Technology | Web Technology (U21CS501)</p>
    </footer>

</body>
</html>
