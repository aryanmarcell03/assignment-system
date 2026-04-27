<?php
session_start();
require "config.php";

// Redirect if not admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'header.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    // Server-side validation
    if (empty($title) || empty($description)) {
        $message = "All fields are required!";
    } else if (strlen($title) < 3) {
        $message = "Title must be at least 3 characters!";
    } else if (strlen($description) < 10) {
        $message = "Description must be at least 10 characters!";
    } else {
        // Insert assignment into database
        $stmt = $conn->prepare("INSERT INTO assignments (title, description) VALUES (?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("ss", $title, $description);
            
            if ($stmt->execute()) {
                $message = "Assignment created successfully!";
                // Clear form fields
                $title = "";
                $description = "";
            } else {
                $message = "Error creating assignment. Please try again!";
            }
            $stmt->close();
        } else {
            $message = "Database error!";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Assignment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 600px;
            margin: 30px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            background: #fff;
        }
    </style>
    <script>
    function validateAssignmentForm() {
        let title = document.getElementById('title').value.trim();
        let description = document.getElementById('description').value.trim();
        
        if (title === '' || description === '') {
            alert('All fields are required!');
            return false;
        }
        
        if (title.length < 3) {
            alert('Title must be at least 3 characters!');
            return false;
        }
        
        if (description.length < 10) {
            alert('Description must be at least 10 characters!');
            return false;
        }
        
        return true;
    }
    </script>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2 class="text-center mb-4">Create Assignment</h2>

        <?php if (!empty($message)): ?>
            <?php 
                $alertType = (strpos($message, 'successfully') !== false) ? 'alert-success' : 'alert-danger';
            ?>
            <div class="alert <?= $alertType ?>" role="alert">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="POST" onsubmit="return validateAssignmentForm()">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" id="title" name="title" class="form-control" placeholder="Enter assignment title" value="<?= htmlspecialchars($title ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="6" placeholder="Enter assignment description" required><?= htmlspecialchars($description ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100">Create</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
