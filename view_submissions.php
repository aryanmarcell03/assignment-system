<?php 
session_start(); 

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php'; 
include 'header.php';

// Get session data
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// SQL query based on role
if ($role == 'admin') {
    // Admins can see all submissions
    $sql = "SELECT 
                s.id, 
                u.name as student_name, 
                a.title as assignment_title, 
                s.file
            FROM submissions s 
            JOIN users u ON s.user_id = u.id 
            JOIN assignments a ON s.assignment_id = a.id 
            ORDER BY s.id DESC";
} else {
    // Students can only see their own submissions
    $sql = "SELECT 
                s.id, 
                u.name as student_name, 
                a.title as assignment_title, 
                s.file
            FROM submissions s 
            JOIN users u ON s.user_id = u.id 
            JOIN assignments a ON s.assignment_id = a.id 
            WHERE s.user_id = $user_id 
            ORDER BY s.id DESC";
}

$res = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submissions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .submissions-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="submissions-container">
        <h2 class="mb-4">
            <?php echo ($role === 'admin') ? 'All Submissions' : 'My Submissions'; ?>
        </h2>

        <?php if (mysqli_num_rows($res) === 0): ?>
            <div class="alert alert-info">No submissions found</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <?php if ($role === 'admin'): ?>
                                <th>Student Name</th>
                            <?php endif; ?>
                            <th>Assignment Title</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($res)): ?>
                            <tr>
                                <?php if ($role === 'admin'): ?>
                                    <td><?= htmlspecialchars($row['student_name']) ?></td>
                                <?php endif; ?>
                                <td><?= htmlspecialchars($row['assignment_title']) ?></td>
                                <td>
                                    <?php if(!empty($row['file'])): ?>
                                        <a href="uploads/<?= htmlspecialchars($row['file']) ?>" class="btn btn-sm btn-primary" download>Download</a>
                                    <?php else: ?>
                                        <span class="text-muted">No file</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
