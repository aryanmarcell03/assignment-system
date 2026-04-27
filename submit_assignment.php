<?php 
include 'config.php'; 
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $filename = time() . "_" . $_FILES['file']['name'];
    move_uploaded_file($_FILES['file']['tmp_name'], "uploads/" . $filename);
    
    $stmt = $conn->prepare("INSERT INTO submissions (user_id, assignment_id, file) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $_SESSION['user']['id'], $_POST['assignment_id'], $filename);
    
    if($stmt->execute()) {
        echo "<div class='alert alert-success'>Uploaded successfully!</div>";
    }
}
$assignments = mysqli_query($conn, "SELECT * FROM assignments");
?>

<div class="container d-flex justify-content-center mt-5">
    <div class="card shadow-sm p-4" style="width: 100%; max-width: 500px; border-radius: 10px;">
        <h2 class="text-center mb-4">Submit Assignment</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <select name="assignment_id" class="form-select" required>
                    <option value="" disabled selected>Select Assignment</option>
                    <?php while($a = mysqli_fetch_assoc($assignments)): ?>
                        <option value="<?= $a['id'] ?>"><?= $a['title'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <input type="file" name="file" class="form-control" required>
            </div>
            
            <div class="d-grid">
                <button class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php';?>
