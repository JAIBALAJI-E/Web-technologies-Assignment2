<?php
/**
 * KPR Institute of Engineering and Technology
 * Department of Computer Science and Engineering
 * Course: U21CS501 Web Technology | Assignment II
 * 
 * Question 2: Delete Student Record & Remove Physical Files
 */

require_once '../config/database.php';

$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($student_id <= 0) {
    header("Location: list.php?msg=error");
    exit();
}

try {
    // 1. Fetch physical file paths associated with student via Prepared Statement
    $fileStmt = $conn->prepare("SELECT file_path FROM student_files WHERE student_id = ?");
    $fileStmt->bind_param("i", $student_id);
    $fileStmt->execute();
    $filesResult = $fileStmt->get_result();

    // 2. Unlink (delete) stored physical files from the uploads directory
    while ($fileRow = $filesResult->fetch_assoc()) {
        $physicalPath = '../' . $fileRow['file_path'];
        if (file_exists($physicalPath) && is_file($physicalPath)) {
            @unlink($physicalPath);
        }
    }
    $fileStmt->close();

    // 3. Delete student record from MySQL database using Prepared Statement
    // Note: Foreign Key CASCADE will automatically remove rows from student_files
    $delStmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
    $delStmt->bind_param("i", $student_id);
    $delStmt->execute();
    $delStmt->close();

    // Redirect with deletion success message
    header("Location: list.php?msg=deleted");
    exit();

} catch (Exception $e) {
    error_log("Delete Error: " . $e->getMessage());
    header("Location: list.php?msg=error");
    exit();
}
?>
