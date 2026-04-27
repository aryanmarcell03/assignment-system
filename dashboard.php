<?php 
session_start();
include 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'header.php'; 

// Determine if user is admin or student
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .dashboard-container {
            max-width: 800px;
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
    <div class="dashboard-container">
        <h1 class="mb-4">Welcome <?= htmlspecialchars($_SESSION['username']) ?></h1>

        <?php if ($role === 'admin'): ?>
            <!-- Admin Dashboard -->
            <div class="mb-3">
                <input type="text" id="search" class="form-control" placeholder="Search assignments...">
            </div>
            <div id="results" class="list-group"></div>
        <?php else: ?>
            <!-- Student Dashboard -->
            <div class="mb-3">
                <input type="text" id="search" class="form-control" placeholder="Search assignments...">
            </div>
            <div id="results" class="list-group"></div>
        <?php endif; ?>
    </div>
</div>

<script>
const search = document.getElementById('search');
search.addEventListener('input', () => {
    fetch('fetch_assignments.php?q=' + encodeURIComponent(search.value))
        .then(res => res.text())
        .then(data => document.getElementById('results').innerHTML = data);
});

// Load all assignments on page load
search.dispatchEvent(new Event('input'));
</script>

<?php include 'footer.php'; ?>

</body>
</html>
