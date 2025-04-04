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


if (!isset($_GET['pid'])) {
    die("Project ID not provided.");
}

$pid = $_GET['pid']; 

// Prepare and execute the delete query
$delete_sql = "DELETE FROM project WHERE pid = ?";
$stmt_delete = $conn->prepare($delete_sql);
$stmt_delete->bind_param("i", $pid);

// Execute the query and check for errors
if ($stmt_delete->execute()) {
    // Successfully deleted the project member
    header("Location: manage_projects.php?adid=0");
    exit();
} else {
    // Error deleting the projectmember
    echo "Error deleting project member: " . $conn->error;
}

// Close the database connection
$conn->close();
?>
