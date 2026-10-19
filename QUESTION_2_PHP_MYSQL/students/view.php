<?php
/**
 * KPR Institute of Engineering and Technology
 * Department of Computer Science and Engineering
 * Course: U21CS501 Web Technology | Assignment II
 * 
 * Question 2: View Detailed Student Profile & Documents
 */

require_once '../config/database.php';

// Validate student ID GET parameter
$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($student_id <= 0) {
    header("Location: list.php");
    exit();
}

// 1. Fetch Student Details via Prepared Statement
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$studentResult = $stmt->get_result();

if ($studentResult->num_rows === 0) {
    die("Error: Student record not found.");
}

$student = $studentResult->fetch_assoc();
$stmt->close();

// 2. Fetch Associated Uploaded Files via Prepared Statement
$fileStmt = $conn->prepare("SELECT * FROM student_files WHERE student_id = ? ORDER BY file_id ASC");
$fileStmt->bind_param("i", $student_id);
$fileStmt->execute();
$filesResult = $fileStmt->get_result();

$uploadedFiles = [];
$profileImage = null;

while ($fileRow = $filesResult->fetch_assoc()) {
    $uploadedFiles[] = $fileRow;
    // Identify profile image based on file type / name
    if (strpos($fileRow['file_type'], 'image') !== false && strpos($fileRow['stored_name'], 'profile') !== false && !$profileImage) {
        $profileImage = $fileRow;
    }
}
$fileStmt->close();

// Fallback to first image if no specific profile image matched
if (!$profileImage) {
    foreach ($uploadedFiles as $f) {
        if (strpos($f['file_type'], 'image') !== false) {
            $profileImage = $f;
            break;
        }
    }
}

include_once '../includes/header.php';
?>

<div class="page-header">
    <h2>Student Profile Details</h2>
    <div>
        <a href="edit.php?id=<?php echo $student['student_id']; ?>" class="btn btn-warning">Edit Profile</a>
        <a href="list.php" class="btn btn-secondary">&larr; Back to Student List</a>
    </div>
</div>

<!-- Student Profile Info Card -->
<div class="card">
    <div class="profile-card">
        <div class="profile-img-container">
            <?php if ($profileImage && file_exists('../' . $profileImage['file_path'])): ?>
                <img src="../<?php echo htmlspecialchars($profileImage['file_path']); ?>" alt="Profile Photo">
            <?php else: ?>
                <!-- Academic Placeholder Profile Image -->
                <div style="text-align: center; color: #888;">
                    <div style="font-size: 3rem; line-height: 1;">&#128100;</div>
                    <small>No Image</small>
                </div>
            <?php endif; ?>
        </div>
        <div class="profile-info">
            <h3><?php echo htmlspecialchars($student['name']); ?></h3>
            <span class="badge badge-info"><?php echo htmlspecialchars($student['roll_number']); ?></span>
            
            <div class="info-grid">
                <div class="info-label">Email:</div>
                <div><?php echo htmlspecialchars($student['email']); ?></div>

                <div class="info-label">Course:</div>
                <div><?php echo htmlspecialchars($student['course']); ?></div>

                <div class="info-label">Student ID:</div>
                <div>#<?php echo htmlspecialchars($student['student_id']); ?></div>

                <div class="info-label">Registered On:</div>
                <div><?php echo date('d M Y, h:i A', strtotime($student['created_at'])); ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Attached Documents & Images Section -->
<div class="card">
    <h3 style="color: #0d3b66; margin-bottom: 15px;">Uploaded Documents & Files</h3>

    <?php if (!empty($uploadedFiles)): ?>
        <div class="document-list">
            <?php foreach ($uploadedFiles as $doc): ?>
                <div class="document-card">
                    <div class="document-info">
                        <div class="document-name" title="<?php echo htmlspecialchars($doc['original_name']); ?>">
                            &#128196; <?php echo htmlspecialchars($doc['original_name']); ?>
                        </div>
                        <div class="document-meta">
                            Type: <?php echo htmlspecialchars($doc['file_type']); ?> | 
                            Size: <?php echo round($doc['file_size'] / 1024, 1); ?> KB
                        </div>
                    </div>
                    <div>
                        <?php if (file_exists('../' . $doc['file_path'])): ?>
                            <a href="../<?php echo htmlspecialchars($doc['file_path']); ?>" target="_blank" class="btn btn-sm btn-primary">View File</a>
                        <?php else: ?>
                            <span style="color: #d90429; font-size: 0.8rem;">File Missing</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="color: #777;">No documents or profile files have been uploaded for this student.</p>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>
