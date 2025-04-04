<?php
// Start the session
session_start();

// Destroy all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to the home page or login page
header("Location: faculty_student.php"); // Or replace with "login.php" if you have a dedicated login page
exit;
?>
