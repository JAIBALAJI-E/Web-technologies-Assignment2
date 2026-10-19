<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%
    // KPR Institute of Engineering and Technology - Web Technology (U21CS501)
    // Entry Point: Check session and redirect to StudentListServlet or login.jsp
    HttpSession userSession = request.getSession(false);
    if (userSession != null && userSession.getAttribute("user") != null) {
        response.sendRedirect("StudentListServlet");
    } else {
        response.sendRedirect("login.jsp");
    }
%>
