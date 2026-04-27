<?php
$conn = new mysqli("localhost:3307", "root", "", "assignment_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
?>
