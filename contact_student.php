<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('db.php');

// Get parameters from the form submission
$sid = $_POST['sid'];
$pid = $_POST['pid'];
$fid = $_POST['fid'];

// Fetch student email and name
$sql = "SELECT email, firstName, lastName FROM students WHERE SID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $sid);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$studentEmail = $row['email'];
$studentName = $row['firstName'] . " " . $row['lastName'];
$stmt->close();

// Fetch project name
$sql_project = "SELECT projectName FROM project WHERE PID = ?";
$stmt = $conn->prepare($sql_project);
$stmt->bind_param("i", $pid);
$stmt->execute();
$result = $stmt->get_result();
$projectRow = $result->fetch_assoc();
$projectName = $projectRow['projectName'];
$stmt->close();

// Fetch faculty member's name
$sql_faculty = "SELECT firstName, lastName FROM faculty WHERE FID = ?";
$stmt = $conn->prepare($sql_faculty);
$stmt->bind_param("i", $fid);
$stmt->execute();
$result = $stmt->get_result();
$facultyRow = $result->fetch_assoc();
$facultyName = $facultyRow['firstName'] . " " . $facultyRow['lastName'];
$stmt->close();

// Update selected applicant's status to "Under Review"
$sql_update = "UPDATE application SET status = 'Under Review' WHERE SID = ? AND PID = ?";
$stmt = $conn->prepare($sql_update);
$stmt->bind_param("si", $sid, $pid);
$stmt->execute();
$stmt->close();

$conn->close();

// Open the email client with a pre-defined message
$subject = "Discussion Regarding Your Application for " . $projectName;
$body = "Dear $studentName,\n\nWe would like to discuss your application for the project '$projectName' in more detail. Please let us know your availability for a meeting or further discussion.\n\nBest Regards,\n$facultyName";
$mailto = "mailto:$studentEmail?subject=" . rawurlencode($subject) . "&body=" . rawurlencode($body);

header("Location: $mailto");
exit();
?>
