<?php
session_start();
require "config.php";

$message = "";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = htmlspecialchars(trim($_POST['title']));
    $desc = htmlspecialchars(trim($_POST['description']));
    $category = htmlspecialchars(trim($_POST['category']));

    if (empty($title) || empty($desc) || empty($category)) {
        $message = "All fields are required!";
    } else {

        $filename = "";

        if (!empty($_FILES['file']['name'])) {

            $target_dir = "uploads/";

            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $filename = time() . "_" . basename($_FILES["file"]["name"]);
            $target_file = $target_dir . $filename;

            $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $allowed = ['jpg','jpeg','png','pdf'];

            if (!in_array($fileType, $allowed)) {
                $message = "Only JPG, PNG & PDF allowed!";
            } else {

                if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {

                    $stmt = $conn->prepare("INSERT INTO requests (user_id, title, description, category, file) VALUES (?, ?, ?, ?, ?)");
                    
                    if ($stmt) {
                        $stmt->bind_param("issss", $_SESSION['user_id'], $title, $desc, $category, $filename);
                        $stmt->execute();
                        $message = "Request submitted successfully!";
                    } else {
                        $message = "Database error!";
                    }

                } else {
                    $message = "File upload failed!";
                }
            }

        } else {
        
            $stmt = $conn->prepare("INSERT INTO requests (user_id, title, description, category, file) VALUES (?, ?, ?, ?, ?)");
            
            if ($stmt) {
                $stmt->bind_param("issss", $_SESSION['user_id'], $title, $desc, $category, $filename);
                $stmt->execute();
                $message = "Request submitted (no file)";
            } else {
                $message = "Database error!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Submit Request</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<script>
function validateForm() {
    let title = document.forms["form"]["title"].value;
    let desc = document.forms["form"]["description"].value;
    let cat = document.forms["form"]["category"].value;

    if (title == "" ||  desc == "" ||  cat == "") {
        alert("All fields must be filled!");
        return false;
    }
}
</script>

</head>
<body>

<nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand">Portal</span>
    <a href="dashboard.php" class="btn btn-light btn-sm">Back</a>
</nav>

<div class="container mt-4">

    <div class="card p-4">
        <h3 class="mb-3">Submit Request</h3>

        <?php if(!empty($message)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form name="form" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">

            <div class="mb-3">
                <input type="text" name="title" class="form-control" placeholder="Title">
            </div>

            <div class="mb-3">
                <textarea name="description" class="form-control" placeholder="Description"></textarea>
            </div>

            <div class="mb-3">
                <select name="category" class="form-control">
                    <option value="">Select Category</option>
                    <option>Academic</option>
                    <option>Facility</option>
                    <option>Technical</option>
                    <option>Administration</option>
                </select>
            </div>

            <div class="mb-3">
                <input type="file" name="file" class="form-control">
            </div>

            <button class="btn btn-primary w-100">Submit</button>

        </form>
    </div>

</div>

</body>
</html>
