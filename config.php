<?php

$conn = mysqli_connect("localhost:3307", "root", "", "assignment_system");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
