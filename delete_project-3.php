<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $PID = isset($_POST['PID']) ? intval($_POST['PID']) : null;
    $fid = isset($_POST['fid']) ? htmlspecialchars(trim($_POST['fid'])) : null;

    if (!$PID || !$fid) {
        die("Invalid project ID or Faculty ID.");
    }

    $sql_delete = "DELETE FROM project WHERE PID = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    if (!$stmt_delete) {
        die("Query preparation failed: " . $conn->error);
    }

    $stmt_delete->bind_param("i", $PID);
    if ($stmt_delete->execute()) {
        echo "Project deleted successfully.";
    } else {
        echo "Error deleting project: " . $conn->error;
    }

    $stmt_delete->close();
    $conn->close();

    // Redirect back to faculty page with the correct fid
    header("Location: faculty_applications.php?fid=" . urlencode($fid));
    exit();
} else {
    die("Invalid request.");
}
?>
