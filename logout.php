<?php
session_start();

// Clear all session variables
session_unset();

// Destroy the session entirely
session_destroy();

// Redirect back to the login page
header("Location: login.php");
exit();
?>
