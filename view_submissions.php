<?php 
include 'config.php'; 
include 'header.php';
$user = $_SESSION['user'];
$sql = ($user['role'] == 'admin') 
    ? "SELECT users.name, assignments.title, submissions.file FROM submissions 
       JOIN users ON submissions.user_id = users.id 
       JOIN assignments ON submissions.assignment_id = assignments.id"
    : "SELECT users.name, assignments.title, submissions.file FROM submissions 
       JOIN users ON submissions.user_id = users.id 
       JOIN assignments ON submissions.assignment_id = assignments.id 
       WHERE submissions.user_id = " . $user['id'];

$res = mysqli_query($conn, $sql);
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<div class="container mt-5">
    <h2>Submissions</h2>
    <table class="table table-bordered">
        <tr><th>User</th><th>Assignment</th><th>File</th></tr>
        <?php while($row = mysqli_fetch_assoc($res)): ?>
            <tr>
                <td><?= $row['name'] ?></td>
                <td><?= $row['title'] ?></td>
                <td><a href="uploads/<?= $row['file'] ?>">Download</a></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include 'footer.php'; ?>
