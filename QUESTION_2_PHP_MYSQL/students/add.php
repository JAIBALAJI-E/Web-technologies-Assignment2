<?php
/**
 * KPR Institute of Engineering and Technology
 * Department of Computer Science and Engineering
 * Course: U21CS501 Web Technology | Assignment II
 * 
 * Question 2: Add New Student Record & Handle File Uploads
 */

require_once '../config/database.php';

$errors = [];

// Handle Form Submission (POST Request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize and collect text inputs
    $name = trim($_POST['name'] ?? '');
    $roll_number = trim($_POST['roll_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $course = trim($_POST['course'] ?? '');

    // 1. Validate Required Text Fields
    if (empty($name)) { $errors[] = "Student Name is required."; }
    if (empty($roll_number)) { $errors[] = "Roll Number is required."; }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "A valid Email Address is required."; }
    if (empty($course)) { $errors[] = "Course selection is required."; }

    // 2. Check Roll Number Uniqueness via Prepared Statement
    if (empty($errors)) {
        $checkStmt = $conn->prepare("SELECT student_id FROM students WHERE roll_number = ?");
        $checkStmt->bind_param("s", $roll_number);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        if ($checkResult->num_rows > 0) {
            $errors[] = "Roll Number '$roll_number' is already registered in the system.";
        }
        $checkStmt->close();
    }

    // 3. Helper Function for Validating & Processing File Upload
    function processFileUpload($fileKey, &$errors, $allowedTypes, $maxSizeBytes = 5242880) {
        if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] === UPLOAD_ERR_NO_FILE) {
            return null; // File optional or handled separately
        }

        $file = $_FILES[$fileKey];

        // Check upload error code
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading file for '$fileKey'. Code: " . $file['error'];
            return null;
        }

        // Validate File Size (Max 5MB)
        if ($file['size'] > $maxSizeBytes) {
            $errors[] = "File '$fileKey' exceeds the 5MB size limit.";
            return null;
        }

        // Validate File Extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedTypes)) {
            $errors[] = "Invalid file type for '$fileKey'. Allowed formats: " . implode(', ', $allowedTypes);
            return null;
        }

        // Generate Unique Stored Filename to Prevent Overwriting Existing Files
        $originalName = basename($file['name']);
        $uniqueName = uniqid($fileKey . '_', true) . '.' . $ext;
        $targetDir = '../uploads/';
        $targetPath = $targetDir . $uniqueName;

        // Move uploaded file from PHP temp folder to target directory
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $errors[] = "Failed to save uploaded file '$originalName' to server destination.";
            return null;
        }

        return [
            'original_name' => $originalName,
            'stored_name'   => $uniqueName,
            'file_path'     => 'uploads/' . $uniqueName,
            'file_type'     => $file['type'],
            'file_size'     => $file['size']
        ];
    }

    // Process File Uploads
    $allowedFormats = ['pdf', 'jpg', 'jpeg', 'png'];
    $profileImageMeta = processFileUpload('profile_image', $errors, $allowedFormats);
    $idProofMeta = processFileUpload('id_proof', $errors, $allowedFormats);
    $certificateMeta = processFileUpload('certificate', $errors, $allowedFormats);

    // 4. Insert Student Record & Metadata into Database if No Validation Errors
    if (empty($errors)) {

        // Begin transaction for database integrity
        $conn->begin_transaction();

        try {
            // Prepared statement for inserting primary student record
            $stmt = $conn->prepare("INSERT INTO students (name, roll_number, email, course) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $roll_number, $email, $course);
            $stmt->execute();
            
            // Fetch generated auto-increment primary key ID
            $student_id = $conn->insert_id;
            $stmt->close();

            // Prepared statement for inserting file metadata into student_files table
            $fileStmt = $conn->prepare("INSERT INTO student_files (student_id, original_name, stored_name, file_path, file_type, file_size) VALUES (?, ?, ?, ?, ?, ?)");

            $filesToInsert = array_filter([$profileImageMeta, $idProofMeta, $certificateMeta]);

            foreach ($filesToInsert as $fMeta) {
                $fileStmt->bind_param("issssi", 
                    $student_id, 
                    $fMeta['original_name'], 
                    $fMeta['stored_name'], 
                    $fMeta['file_path'], 
                    $fMeta['file_type'], 
                    $fMeta['file_size']
                );
                $fileStmt->execute();
            }
            $fileStmt->close();

            // Commit transaction
            $conn->commit();

            // Redirect to list with success message
            header("Location: list.php?msg=added");
            exit();

        } catch (Exception $e) {
            // Rollback transaction on database error
            $conn->rollback();
            $errors[] = "Database Error: " . $e->getMessage();
        }
    }
}

include_once '../includes/header.php';
?>

<div class="page-header">
    <h2>Add New Student Profile</h2>
    <a href="list.php" class="btn btn-secondary">&larr; Back to Student List</a>
</div>

<!-- Display Validation Errors -->
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <strong>Form Validation Failed:</strong>
        <ul style="margin-left: 20px; margin-top: 5px;">
            <?php foreach ($errors as $err): ?>
                <li><?php echo htmlspecialchars($err); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Add Student Form -->
<div class="card">
    <form action="add.php" method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
            <label for="name">Full Name <span class="required">*</span></label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="roll_number">Roll Number <span class="required">*</span></label>
            <input type="text" id="roll_number" name="roll_number" class="form-control" value="<?php echo htmlspecialchars($_POST['roll_number'] ?? ''); ?>" placeholder="e.g. STU000001" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address <span class="required">*</span></label>
            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" placeholder="e.g. student001@example.com" required>
        </div>

        <div class="form-group">
            <label for="course">Course <span class="required">*</span></label>
            <select id="course" name="course" class="form-control" required>
                <option value="">-- Select Course --</option>
                <option value="Computer Science and Engineering">B.E. Computer Science and Engineering</option>
                <option value="Electronics and Communication Engineering">B.E. Electronics and Communication Engineering</option>
                <option value="Electrical and Electronics Engineering">B.E. Electrical and Electronics Engineering</option>
                <option value="Mechanical Engineering">B.E. Mechanical Engineering</option>
                <option value="Artificial Intelligence and Data Science">B.Tech. Artificial Intelligence and Data Science</option>
            </select>
        </div>

        <hr style="margin: 20px 0; border: none; border-top: 1px solid #e2e8f0;">

        <h3>Upload Student Documents & Images</h3>
        <p style="font-size: 0.85rem; color: #666; margin-bottom: 15px;">Allowed file types: PDF, JPG, PNG (Max size per file: 5MB)</p>

        <div class="form-group">
            <label for="profile_image">Profile Image</label>
            <input type="file" id="profile_image" name="profile_image" class="form-control" accept=".jpg,.jpeg,.png">
        </div>

        <div class="form-group">
            <label for="id_proof">ID Proof Document (College ID / Aadhaar)</label>
            <input type="file" id="id_proof" name="id_proof" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
        </div>

        <div class="form-group">
            <label for="certificate">Academic Certificate / Marksheet</label>
            <input type="file" id="certificate" name="certificate" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
        </div>

        <div style="margin-top: 25px;">
            <button type="submit" class="btn btn-success">Save Student Record</button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include_once '../includes/footer.php'; ?>
