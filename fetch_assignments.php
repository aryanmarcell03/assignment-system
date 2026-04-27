<?php 
include 'config.php';

$q = "%" . ($_GET['q'] ?? '') . "%";

$stmt = $conn->prepare("SELECT id, title, description FROM assignments WHERE title LIKE ? ORDER BY id DESC");
$stmt->bind_param("s", $q);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "<div class='alert alert-info'>No assignments found</div>";
} else {
    while ($row = $res->fetch_assoc()) {
        echo "<div class='list-group-item d-flex justify-content-between align-items-center'>";
        echo "  <div class='w-100'>";
        echo "    <strong>" . htmlspecialchars($row['title']) . "</strong><br>";
        echo "    <small class='text-muted'>" . htmlspecialchars(substr($row['description'], 0, 100)) . "...</small>";
        echo "  </div>";
        echo "  <a href='submit_assignment.php?id=" . $row['id'] . "' class='btn btn-success btn-sm ms-2'>Submit</a>";
        echo "</div>";
    }
}

$stmt->close();
?>
