<?php
session_start();
require "config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

include 'header.php';

$message = "";

// Fetch all assignments for dropdown
$assignments = [];
$stmt = $conn->prepare("SELECT id, title FROM assignments ORDER BY id DESC");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $assignments[] = $row;
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $assignment_id = $_POST['assignment_id'] ?? '';
    $filename = "";

    // Server-side validation
    if (empty($assignment_id)) {
        $message = "Please select an assignment!";
    } else if (empty($_FILES['file']['name'])) {
        $message = "Please select a file to upload!";
    } else {
        
        $target_dir = "uploads/";
        
        // Create uploads directory if it doesn't exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $fileType = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
        $fileSize = $_FILES["file"]["size"];
        
        // File type validation
        $allowed = ['pdf', 'docx', 'txt', 'doc'];
        if (!in_array($fileType, $allowed)) {
            $message = "Only PDF, DOCX, DOC, and TXT files are allowed!";
        } 
        // File size validation (max 5MB)
        else if ($fileSize > 5 * 1024 * 1024) {
            $message = "File size must be less than 5MB!";
        } 
        else {
            
            $filename = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES["file"]["name"]));
            $target_file = $target_dir . $filename;

            // Check if file with same name already exists
            $counter = 1;
            while (file_exists($target_file)) {
                $filename = time() . "_" . $counter . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES["file"]["name"]));
                $target_file = $target_dir . $filename;
                $counter++;
            }

            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                
                // Insert submission into database
                $stmt = $conn->prepare("INSERT INTO submissions (user_id, assignment_id, file) VALUES (?, ?, ?)");
                
                if ($stmt) {
                    $stmt->bind_param("iis", $_SESSION['user_id'], $assignment_id, $filename);
                    
                    if ($stmt->execute()) {
                        $message = "Assignment submitted successfully!";
                    } else {
                        $message = "Error saving submission to database!";
                        // Delete the uploaded file if DB insert fails
                        unlink($target_file);
                    }
                    $stmt->close();
                } else {
                    $message = "Database error!";
                    unlink($target_file);
                }
            } else {
                $message = "File upload failed!";
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
    <title>Submit Assignment</title>
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
    function validateSubmissionForm() {
        let assignment_id = document.getElementById('assignment_id').value;
        let file = document.getElementById('file').value;
        
        if (assignment_id === '') {
            alert('Please select an assignment!');
            return false;
        }
        
        if (file === '') {
            alert('Please select a file to upload!');
            return false;
        }
        
        // Validate file type
        let allowedExtensions = ['pdf', 'docx', 'txt', 'doc'];
        let fileExtension = file.split('.').pop().toLowerCase();
        
        if (!allowedExtensions.includes(fileExtension)) {
            alert('Only PDF, DOCX, DOC, and TXT files are allowed!');
            return false;
        }
        
        // Validate file size (5MB max)
        let fileInput = document.getElementById('file');
        if (fileInput.files[0].size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB!');
            return false;
        }
        
        return true;
    }
    </script>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2 class="text-center mb-4">Submit Assignment</h2>

        <?php if (!empty($message)): ?>
            <?php 
                $alertType = (strpos($message, 'successfully') !== false) ? 'alert-success' : 'alert-danger';
            ?>
            <div class="alert <?= $alertType ?>" role="alert">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" onsubmit="return validateSubmissionForm()">
            <div class="mb-3">
                <label for="assignment_id" class="form-label">Select Assignment</label>
                <select id="assignment_id" name="assignment_id" class="form-control" required>
                    <option value="">-- Choose Assignment --</option>
                    <?php foreach ($assignments as $assignment): ?>
                        <option value="<?= $assignment['id'] ?>"><?= htmlspecialchars($assignment['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="file" class="form-label">Upload File (Max 5MB)</label>
                <input type="file" id="file" name="file" class="form-control" accept=".pdf,.docx,.doc,.txt" required>
                <small class="form-text text-muted">Allowed formats: PDF, DOCX, DOC, TXT</small>
            </div>

            <button type="submit" class="btn btn-primary w-100">Submit</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
