<?php include 'config.php';
$q = "%" . ($_GET['q'] ?? '') . "%";
$stmt = $conn->prepare("SELECT * FROM assignments WHERE title LIKE ?");
$stmt->bind_param("s", $q);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    echo "<div class='list-group-item'><strong>{$row['title']}</strong><br>{$row['description']}</div>";
}
?>
