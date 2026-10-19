<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KPRIET Portal Login - Servlet & JDBC Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <header class="navbar">
        <a href="login.jsp" class="navbar-brand">KPRIET <span>Student Portal</span></a>
    </header>

    <main class="container">
        <div class="card login-card">
            <h2 class="card-title">User Authentication Login</h2>
            <p style="font-size: 0.85rem; color: #666; margin-bottom: 20px;">
                Please authenticate using your credentials to access student profiles and streaming files.
            </p>

            <!-- Error Message Display -->
            <%
                String errorMsg = (String) request.getAttribute("errorMessage");
                String errorParam = request.getParameter("error");
                String msgParam = request.getParameter("msg");

                if (errorMsg != null) {
            %>
                    <div class="alert alert-danger"><%= errorMsg %></div>
            <%
                } else if ("unauthorized".equals(errorParam)) {
            %>
                    <div class="alert alert-danger">Access Denied: Session expired or invalid. Please login.</div>
            <%
                } else if ("logged_out".equals(msgParam)) {
            %>
                    <div class="alert alert-success">You have been logged out successfully.</div>
            <%
                }
            %>

            <!-- Login Form -->
            <form action="LoginServlet" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Default: admin" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Default: admin123" required>
                </div>

                <div class="form-group" style="margin-top: 25px;">
                    <button type="submit" class="btn btn-primary">Login to Portal</button>
                </div>
            </form>

            <div style="font-size: 0.8rem; color: #718096; margin-top: 20px; text-align: center; border-top: 1px solid #edf2f7; padding-top: 15px;">
                <strong>Demo Credentials:</strong> Username: <code>admin</code> | Password: <code>admin123</code>
            </div>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; 2026 KPR Institute of Engineering and Technology | Web Technology (U21CS501)</p>
    </footer>

</body>
</html>
