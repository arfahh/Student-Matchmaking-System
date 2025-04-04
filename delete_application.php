<?php
// Start session
session_start();

// Include database connection
include('db.php');
include('headeradmin.php');

// Ensure fid is passed in the URL and equals 0 (admin access)
if (!isset($_GET['adid']) || $_GET['adid'] != '0') {
    die("Access denied. You must be an admin (adid=0) to view this page.");
}


if (!isset($_GET['aid'])) {
    die("Application ID not provided.");
}

$aid = $_GET['aid']; 

// Prepare and execute the delete query
$delete_sql = "DELETE FROM application WHERE aid = ?";
$stmt_delete = $conn->prepare($delete_sql);
$stmt_delete->bind_param("i", $aid);

// Execute the query and check for errors
if ($stmt_delete->execute()) {
    // Successfully deleted the faculty member
    header("Location: manage_applications.php?adid=0");
    exit();
} else {
    // Error deleting the faculty member
    echo "Error deleting faculty member: " . $conn->error;
}

// Close the database connection
$conn->close();
?>
