<?php
// Start session
session_start();

// Include database connection
include('db.php');
include('headeradmin.php');

// Ensure adid is passed in the URL and equals 0 (admin access)
if (!isset($_GET['adid']) || $_GET['adid'] != '0') {
    die("Access denied. You must be an admin (adid=0) to view this page.");
}

// Ensure Student ID (sid) is passed in the URL
if (!isset($_GET['sid'])) {
    die("Student ID not provided.");
}

$sid = $_GET['sid']; // Get the student ID from the URL

// Prepare and execute the delete query
$delete_sql = "DELETE FROM students WHERE sid = ?";
$stmt_delete = $conn->prepare($delete_sql);
$stmt_delete->bind_param("s", $sid);

// Execute the query and check for errors
if ($stmt_delete->execute()) {
    // Successfully deleted the student member
    header("Location: manage_students.php?adid=0");
    exit();
} else {
    // Error deleting the student member
    echo "Error deleting student member: " . $conn->error;
}

// Close the database connection
$conn->close();
?>
