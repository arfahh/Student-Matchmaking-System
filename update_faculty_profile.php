<?php
// Include the database connection
include('db.php');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $fid = isset($_POST['fid']) ? $_POST['fid'] : null;
    $firstName = isset($_POST['firstName']) ? $_POST['firstName'] : '';
    $lastName = isset($_POST['lastName']) ? $_POST['lastName'] : '';
    $facultyName = isset($_POST['facultyName']) ? $_POST['facultyName'] : '';

    if ($fid && $firstName && $lastName && $facultyName) {
        // Prepare the query to update the student profile
        $sql = "UPDATE faculty SET firstName = ?, lastName = ?, facultyName= ? WHERE fid = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Statement preparation failed: " . $conn->error);
        }

        $stmt->bind_param("ssss", $firstName, $lastName, $facultyName, $fid);

        // Execute the update query
        if ($stmt->execute()) {
            // Redirect back to the profile edit page with a success message
            header("Location: EditFacultyProfilePage.php?fid=$fid&status=success");
        } else {
            // Redirect back to the profile edit page with an error message
            header("Location: EditFacultyProfilePage.php?fid=$fid&status=error");
        }

        $stmt->close();
    } else {
        // If the data is incomplete, redirect back with an error
        header("Location: EditFacultyProfilePage.php?fid=$sid&status=error");
    }

    $conn->close();
}
