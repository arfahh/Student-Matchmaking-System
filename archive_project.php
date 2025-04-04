<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get parameters from POST
    $pid = intval($_POST['pid']);
    $archived = intval($_POST['archived']);
    $fid = intval($_POST['fid']);  // Get fid from POST data

    // Update the project status (archive/unarchive)
    $sql = "UPDATE project SET archived = ? WHERE PID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $archived, $pid);

    if ($stmt->execute()) {
        header("Location: faculty_applications.php?fid=" . $fid);
        exit();
    } else {
        echo "Error updating project: " . $conn->error;
    }
}
?>