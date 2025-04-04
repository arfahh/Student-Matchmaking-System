<?php
// Start session
session_start();

// Include database connection
include('db.php');
include('headeradmin.php');

// Ensure admin access by checking 'adid' in the URL
if (!isset($_GET['adid']) || $_GET['adid'] != '0') {
    die("Access denied. You must be an admin (adid=0) to view this page.");
}

// Check if skill_id is passed in the URL
if (!isset($_GET['skill_id'])) {
    die("Skill ID is required.");
}

$skill_id = $_GET['skill_id'];

// Prepare and execute delete query
$sql = "DELETE FROM skills_list WHERE skill_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $skill_id);



// Execute the query and check for errors
if ($stmt->execute()) {
   
    header("Location: manage_skills.php?adid=0");
    exit();
} else {
    
    echo "Error deleting skill member: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
