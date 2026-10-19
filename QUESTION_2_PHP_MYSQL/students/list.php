<?php
/**
 * KPR Institute of Engineering and Technology
 * Department of Computer Science and Engineering
 * Course: U21CS501 Web Technology | Assignment II
 * 
 * Question 2: Student Records Listing Page (Read / View All)
 */

// Include database connection and header template
require_once '../config/database.php';
include_once '../includes/header.php';

// Prepare query to fetch student list along with count of attached documents
$sql = "SELECT s.student_id, s.name, s.roll_number, s.email, s.course, s.created_at, 
               COUNT(f.file_id) AS total_files 
        FROM students s 
        LEFT JOIN student_files f ON s.student_id = f.student_id 
        GROUP BY s.student_id 
        ORDER BY s.student_id DESC";

$result = $conn->query($sql);
?>

<div class="page-header">
    <h2>Student Records Directory</h2>
    <a href="add.php" class="btn btn-primary">+ Add New Student</a>
</div>

<!-- Status Message Feedback Banners -->
<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] == 'added'): ?>
        <div class="alert alert-success">Student profile and uploaded files added successfully!</div>
    <?php elseif ($_GET['msg'] == 'updated'): ?>
        <div class="alert alert-success">Student details updated successfully!</div>
    <?php elseif ($_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-success">Student record and associated files deleted successfully!</div>
    <?php elseif ($_GET['msg'] == 'error'): ?>
        <div class="alert alert-danger">An error occurred while processing your request. Please try again.</div>
    <?php endif; ?>
<?php endif; ?>

<!-- Student Directory Table -->
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Roll Number</th>
                <th>Email Address</th>
                <th>Course</th>
                <th>Documents</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><strong>#<?php echo htmlspecialchars($row['student_id']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><span class="badge badge-info"><?php echo htmlspecialchars($row['roll_number']); ?></span></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['course']); ?></td>
                        <td><?php echo $row['total_files']; ?> file(s)</td>
                        <td>
                            <a href="view.php?id=<?php echo $row['student_id']; ?>" class="btn btn-sm btn-primary">View</a>
                            <a href="edit.php?id=<?php echo $row['student_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete.php?id=<?php echo $row['student_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this student and all associated uploaded files?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #777; padding: 25px;">
                        No student records found. Click <strong>"+ Add New Student"</strong> to create a record.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Include footer template
include_once '../includes/footer.php';
?>
