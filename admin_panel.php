<?php
// Start session
session_start();

// Check if fid is passed in the URL and is equal to 0
if (!isset($_GET['adid']) || $_GET['adid'] != '0') {
    die("Access denied. You must be an admin (adid=0) to view this page.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
</head>
<body>
    <h1>Welcome to the Admin Panel</h1>
    
    <nav>
        <ul>
            <li><a href="manage_students.php?adid=0">Manage Students</a></li>
            <li><a href="manage_faculty.php?adid=0">Manage Faculty</a></li>
            <li><a href="manage_applications.php?adid=0">Manage Applications</a></li>
            <li><a href="manage_projects.php?adid=0">Manage Projects</a></li>
            <li><a href="manage_skills.php?adid=0">Manage Skills</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>
</body>
</html>
