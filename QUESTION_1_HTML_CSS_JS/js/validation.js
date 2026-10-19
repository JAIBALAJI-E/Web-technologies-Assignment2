/**
 * KPR Institute of Engineering and Technology
 * Department of Computer Science and Engineering
 * Course: U21CS501 Web Technology | Assignment II
 * 
 * Question 1: Client-side JavaScript Validation File
 */

// Execute script after the DOM tree is fully loaded
document.addEventListener('DOMContentLoaded', () => {
    
    // Fetch reference to form and error containers
    const form = document.getElementById('registrationForm');
    const successAlert = document.getElementById('successMessage');

    // Input fields
    const nameInput = document.getElementById('studentName');
    const rollInput = document.getElementById('rollNumber');
    const emailInput = document.getElementById('email');
    const courseSelect = document.getElementById('course');
    const passwordInput = document.getElementById('password');
    const fileInput = document.getElementById('studentFile');

    // Helper function to display error message for a specific field
    function showError(inputElement, errorElementId, message) {
        const parent = inputElement.parentElement;
        const errorElement = document.getElementById(errorElementId);
        
        parent.classList.add('error');
        parent.classList.remove('success');
        errorElement.textContent = message;
    }

    // Helper function to mark field as valid
    function showSuccess(inputElement, errorElementId) {
        const parent = inputElement.parentElement;
        const errorElement = document.getElementById(errorElementId);
        
        parent.classList.remove('error');
        parent.classList.add('success');
        errorElement.textContent = '';
    }

    // 1. Validate Student Name
    function validateName() {
        const nameValue = nameInput.value.trim();
        const nameRegex = /^[A-Za-z\s]+$/;

        if (nameValue === '') {
            showError(nameInput, 'nameError', 'Student name is required.');
            return false;
        } else if (nameValue.length < 3) {
            showError(nameInput, 'nameError', 'Name must be at least 3 characters long.');
            return false;
        } else if (!nameRegex.test(nameValue)) {
            showError(nameInput, 'nameError', 'Name can only contain alphabetic letters and spaces.');
            return false;
        } else {
            showSuccess(nameInput, 'nameError');
            return true;
        }
    }

    // 2. Validate Roll Number
    function validateRollNumber() {
        const rollValue = rollInput.value.trim();
        // Standard academic roll number format (alphanumeric, 6 to 15 characters)
        const rollRegex = /^[A-Za-z0-9]{6,15}$/;

        if (rollValue === '') {
            showError(rollInput, 'rollError', 'Roll number is required.');
            return false;
        } else if (!rollRegex.test(rollValue)) {
            showError(rollInput, 'rollError', 'Roll number must be 6-15 alphanumeric characters (e.g., STU000001).');
            return false;
        } else {
            showSuccess(rollInput, 'rollError');
            return true;
        }
    }

    // 3. Validate Email Format
    function validateEmail() {
        const emailValue = emailInput.value.trim();
        // Standard email regex pattern
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        if (emailValue === '') {
            showError(emailInput, 'emailError', 'Email address is required.');
            return false;
        } else if (!emailRegex.test(emailValue)) {
            showError(emailInput, 'emailError', 'Please enter a valid email address (e.g., user@domain.com).');
            return false;
        } else {
            showSuccess(emailInput, 'emailError');
            return true;
        }
    }

    // 4. Validate Course Selection
    function validateCourse() {
        const courseValue = courseSelect.value;

        if (courseValue === '') {
            showError(courseSelect, 'courseError', 'Please select an academic course from the list.');
            return false;
        } else {
            showSuccess(courseSelect, 'courseError');
            return true;
        }
    }

    // 5. Validate Password Strength
    function validatePassword() {
        const passValue = passwordInput.value;
        // Password regex: At least 8 chars, 1 uppercase, 1 lowercase, 1 number, 1 special character
        const passRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

        if (passValue === '') {
            showError(passwordInput, 'passwordError', 'Password is required.');
            return false;
        } else if (passValue.length < 8) {
            showError(passwordInput, 'passwordError', 'Password must be at least 8 characters long.');
            return false;
        } else if (!passRegex.test(passValue)) {
            showError(passwordInput, 'passwordError', 'Password must include uppercase, lowercase, digit, and special char (@$!%*?&).');
            return false;
        } else {
            showSuccess(passwordInput, 'passwordError');
            return true;
        }
    }

    // 6. Validate File Selection & Properties
    function validateFile() {
        const files = fileInput.files;

        if (files.length === 0) {
            showError(fileInput, 'fileError', 'Please select a profile document or photo to upload.');
            return false;
        }

        const file = files[0];
        const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
        const fileExtension = file.name.split('.').pop().toLowerCase();
        const maxSizeBytes = 2 * 1024 * 1024; // 2 MB limit

        if (!allowedExtensions.includes(fileExtension)) {
            showError(fileInput, 'fileError', 'Invalid file type. Only PDF, JPG, and PNG files are allowed.');
            return false;
        } else if (file.size > maxSizeBytes) {
            showError(fileInput, 'fileError', 'File size exceeds 2MB. Please upload a smaller file.');
            return false;
        } else {
            showSuccess(fileInput, 'fileError');
            return true;
        }
    }

    // Attach real-time validation event listeners on user input change
    nameInput.addEventListener('input', validateName);
    rollInput.addEventListener('input', validateRollNumber);
    emailInput.addEventListener('input', validateEmail);
    courseSelect.addEventListener('change', validateCourse);
    passwordInput.addEventListener('input', validatePassword);
    fileInput.addEventListener('change', validateFile);

    // Form Reset Handler
    form.addEventListener('reset', () => {
        // Clear error styles on reset
        const groups = document.querySelectorAll('.form-group');
        groups.forEach(group => {
            group.classList.remove('error', 'success');
        });
        successAlert.style.display = 'none';
    });

    // Form Submission Event Handler
    form.addEventListener('submit', (e) => {
        // Prevent traditional form submission page reload
        e.preventDefault();

        // Run all field validations
        const isNameValid = validateName();
        const isRollValid = validateRollNumber();
        const isEmailValid = validateEmail();
        const isCourseValid = validateCourse();
        const isPasswordValid = validatePassword();
        const isFileValid = validateFile();

        // Check if all fields pass validation
        if (isNameValid && isRollValid && isEmailValid && isCourseValid && isPasswordValid && isFileValid) {
            // Display global success message
            successAlert.style.display = 'block';
            
            // Scroll smoothly to top of form to view success banner
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            // Hide success banner if validation fails
            successAlert.style.display = 'none';
        }
    });
});
