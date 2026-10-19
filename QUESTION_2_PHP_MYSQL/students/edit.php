<?php
/**
 * KPR Institute of Engineering and Technology
 * Department of Computer Science and Engineering
 * Course: U21CS501 Web Technology | Assignment II
 * 
 * Question 2: Edit Existing Student Profile & Update Files
 */

require_once '../config/database.php';

$student_id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['student_id']) ? intval($_POST['student_id']) : 0);

if ($student_id <= 0) {
    header("Location: list.php");
    exit();
}

$errors = [];

// Fetch Existing Student Data
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$studentResult = $stmt->get_result();

if ($studentResult->num_rows === 0) {
    die("Error: Student record not found.");
}

$student = $studentResult->fetch_assoc();
$stmt->close();

// Handle Form Submission (POST Request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $roll_number = trim($_POST['roll_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $course = trim($_POST['course'] ?? '');

    // Validate Input Fields
    if (empty($name)) { $errors[] = "Student Name is required."; }
    if (empty($roll_number)) { $errors[] = "Roll Number is required."; }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "A valid Email Address is required."; }
    if (empty($course)) { $errors[] = "Course selection is required."; }

    // Check Roll Number Uniqueness (Excluding Current Student ID)
    if (empty($errors)) {
        $checkStmt = $conn->prepare("SELECT student_id FROM students WHERE roll_number = ? AND student_id != ?");
        $checkStmt->bind_param("si", $roll_number, $student_id);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            $errors[] = "Roll Number '$roll_number' is already assigned to another student.";
        }
        $checkStmt->close();
    }

    // Process optional new file upload
    $newFileMeta = null;
    if (isset($_FILES['new_document']) && $_FILES['new_document']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['new_document'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

        if (!in_array($ext, $allowed)) {
            $errors[] = "Invalid new file format. Allowed: PDF, JPG, PNG.";
        } elseif ($file['size'] > 5242880) {
            $errors[] = "Uploaded file exceeds 5MB size limit.";
        } else {
            $originalName = basename($file['name']);
            $uniqueName = uniqid('doc_edit_', true) . '.' . $ext;
            $targetPath = '../uploads/' . $uniqueName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $newFileMeta = [
                    'original_name' => $originalName,
                    'stored_name'   => $uniqueName,
                    'file_path'     => 'uploads/' . $uniqueName,
                    'file_type'     => $file['type'],
                    'file_size'     => $file['size']
                ];
            } else {
                $errors[] = "Failed to upload new document to server.";
            }
        }
    }

    // Execute Update Query via Prepared Statements
    if (empty($errors)) {
        $conn->begin_transaction();

        try {
            $updateStmt = $conn->prepare("UPDATE students SET name = ?, roll_number = ?, email = ?, course = ? WHERE student_id = ?");
            $updateStmt->bind_param("ssssi", $name, $roll_number, $email, $course, $student_id);
            $updateStmt->execute();
            $updateStmt->close();

            // Insert new document metadata if uploaded
            if ($newFileMeta) {
                $fStmt = $conn->prepare("INSERT INTO student_files (student_id, original_name, stored_name, file_path, file_type, file_size) VALUES (?, ?, ?, ?, ?, ?)");
                $fStmt->bind_param("issssi", $student_id, $newFileMeta['original_name'], $newFileMeta['stored_name'], $newFileMeta['file_path'], $newFileMeta['file_type'], $newFileMeta['file_size']);
                $fStmt->execute();
                $fStmt->close();
            }

            $conn->commit();
            header("Location: list.php?msg=updated");
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = "Database Update Error: " . $e->getMessage();
        }
    }
}

include_once '../includes/header.php';
?>

<div class="page-header">
    <h2>Edit Student Profile</h2>
    <a href="list.php" class="btn btn-secondary">&larr; Back to Student List</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <strong>Update Errors:</strong>
        <ul style="margin-left: 20px; margin-top: 5px;">
            <?php foreach ($errors as $err): ?>
                <li><?php echo htmlspecialchars($err); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <form action="edit.php?id=<?php echo $student_id; ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">

        <div class="form-group">
            <label for="name">Full Name <span class="required">*</span></label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($student['name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="roll_number">Roll Number <span class="required">*</span></label>
            <input type="text" id="roll_number" name="roll_number" class="form-control" value="<?php echo htmlspecialchars($student['roll_number']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address <span class="required">*</span></label>
            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="course">Course <span class="required">*</span></label>
            <select id="course" name="course" class="form-control" required>
                <option value="Computer Science and Engineering" <?php if ($student['course'] == 'Computer Science and Engineering') echo 'selected'; ?>>B.E. Computer Science and Engineering</option>
                <option value="Electronics and Communication Engineering" <?php if ($student['course'] == 'Electronics and Communication Engineering') echo 'selected'; ?>>B.E. Electronics and Communication Engineering</option>
                <option value="Electrical and Electronics Engineering" <?php if ($student['course'] == 'Electrical and Electronics Engineering') echo 'selected'; ?>>B.E. Electrical and Electronics Engineering</option>
                <option value="Mechanical Engineering" <?php if ($student['course'] == 'Mechanical Engineering') echo 'selected'; ?>>B.E. Mechanical Engineering</option>
                <option value="Artificial Intelligence and Data Science" <?php if ($student['course'] == 'Artificial Intelligence and Data Science') echo 'selected'; ?>>B.Tech. Artificial Intelligence and Data Science</option>
            </select>
        </div>

        <hr style="margin: 20px 0; border: none; border-top: 1px solid #e2e8f0;">

        <h3>Upload Additional Document / Image</h3>
        <div class="form-group">
            <label for="new_document">Select New File (Optional)</label>
            <input type="file" id="new_document" name="new_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            <small style="color: #666;">Allowed: PDF, JPG, PNG (Max 5MB)</small>
        </div>

        <div style="margin-top: 25px;">
            <button type="submit" class="btn btn-warning">Update Student Profile</button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include_once '../includes/footer.php'; ?>
