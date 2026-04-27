<?php

// Redirect to login if session is not valid
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-custom { background-color: #34495e; }
        .nav-link { color: #ffffff !important; }
        .nav-link:hover { color: #bdc3c7 !important; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-custom px-4 mb-4">
    <div class="container-fluid justify-content-center">
        <div class="navbar-nav">
            <a class="nav-link px-3" href="dashboard.php">Dashboard</a>

            <?php if ($role == 'admin'): ?>
                <a class="nav-link px-3" href="create_assignment.php">Create Assignment</a>
                <a class="nav-link px-3" href="view_submissions.php">View Submissions</a>
            <?php else: ?>
                <a class="nav-link px-3" href="submit_assignment.php">Submit Assignment</a>
                <a class="nav-link px-3" href="view_submissions.php">View Submissions</a>
            <?php endif; ?>

            <a class="nav-link px-3" href="logout.php">Logout</a>
        </div>
    </div>
</nav>
