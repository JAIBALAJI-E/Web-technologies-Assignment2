# Question 1: HTML + CSS + JavaScript Student Admission Portal Form

## Overview
This component implements a client-side student admission registration form adhering strictly to standard HTML5, CSS3, and Vanilla JavaScript (ES6) principles without external dependencies or frameworks.

## Features & Implementation
1. **HTML Form (`index.html`)**:
   - Captures Student Name, Roll Number, Email, Course, Password, and Document Upload.
   - Structured semantically with accessible labels, input hints, error containers, and action buttons.
2. **CSS Styling (`css/style.css`)**:
   - Clean, academic UI layout using card containers, custom focused input outlines, error highlighting, and responsive grid flexbox design.
3. **Vanilla JS Validation (`js/validation.js`)**:
   - Real-time (`input`/`change`) and on-submit validation handlers.
   - Rules:
     - **Name**: Required, minimum 3 letters, alphabetic & spaces only.
     - **Roll Number**: Required, 6–15 alphanumeric characters.
     - **Email**: Required, standard email format regex (`user@domain.com`).
     - **Course**: Required dropdown selection.
     - **Password**: Required, minimum 8 characters with at least 1 uppercase, 1 lowercase, 1 digit, and 1 special character (`@$!%*?&`).
     - **File Upload**: Required, allowed extensions (`.pdf`, `.jpg`, `.jpeg`, `.png`), size limit <= 2MB.
   - Displays clear error feedback below affected fields and renders a success banner when all validations pass.

## How to Run
1. Navigate to `QUESTION_1_HTML_CSS_JS/`.
2. Open `index.html` directly in any web browser (Google Chrome, Mozilla Firefox, Microsoft Edge, Safari).
3. Test validation by submitting an empty form, invalid fields, and a completely valid form.
