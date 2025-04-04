<?php
session_start();
include('db.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sid = isset($_POST['sid']) ? trim($_POST['sid']) : null;
    if (!$sid) {
        die("Student ID is missing.");
    }

    $firstName = isset($_POST['firstName']) ? htmlspecialchars(trim($_POST['firstName'])) : "";
    $lastName = isset($_POST['lastName']) ? htmlspecialchars(trim($_POST['lastName'])) : "";
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : "";
    $program = isset($_POST['program']) ? htmlspecialchars(trim($_POST['program'])) : "";
    $year = isset($_POST['year']) ? htmlspecialchars(trim($_POST['year'])) : "";
    $linkedin_link = isset($_POST['linkedin_link']) ? htmlspecialchars(trim($_POST['linkedin_link'])) : "";
    $github_link = isset($_POST['github_link']) ? htmlspecialchars(trim($_POST['github_link'])) : "";
    $website_link = isset($_POST['website_link']) ? htmlspecialchars(trim($_POST['website_link'])) : "";
    $other_links = isset($_POST['other_links']) ? htmlspecialchars(trim($_POST['other_links'])) : "";
    $resumePath = "";

    // Handle resume upload
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";
        $resumeName = basename($_FILES['resume']['name']);
        $targetFilePath = $uploadDir . $resumeName;

        // Move the uploaded file to the server
        if (move_uploaded_file($_FILES['resume']['tmp_name'], $targetFilePath)) {
            $resumePath = $targetFilePath;
        }
    }

    // Update student details including resume and social links
    $update_student_query = "UPDATE students SET firstName=?, lastName=?, email=?, program=?, year=?, resume_path=?, linkedin_link=?, github_link=?, website_link=?, other_links=? WHERE sid=?";
    $stmt = $conn->prepare($update_student_query);
    if (!$stmt) {
        die("Statement preparation failed: " . $conn->error);
    }
    $stmt->bind_param("sssssssssss", $firstName, $lastName, $email, $program, $year, $resumePath, $linkedin_link, $github_link, $website_link, $other_links, $sid);
    if (!$stmt->execute()) {
        die("Execution failed: " . $stmt->error);
    }
    $stmt->close();

    // Collect proficiency data from the form
    $proficiency = isset($_POST['proficiency']) ? $_POST['proficiency'] : [];
    $skills_data = [
        "languages" => [],
        "tools" => [],
        "frameworks" => []
    ];

    foreach ($proficiency as $skill => $level) {
        foreach (['languages', 'tools', 'frameworks'] as $category) {
            if (isset($_POST[$category]) && in_array($skill, $_POST[$category])) {
                $skills_data[$category][$skill] = (int)$level;
            }
        }
    }

    // Convert skills data to JSON
    $skills_json = json_encode($skills_data);

    // Check if skills already exist for the student
    $check_query = "SELECT SID FROM student_skills WHERE SID = ?";
    $stmt = $conn->prepare($check_query);
    if (!$stmt) {
        die("Statement preparation failed: " . $conn->error);
    }
    $stmt->bind_param("s", $sid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing skills record
        $update_skills_query = "UPDATE student_skills SET skills=? WHERE SID=?";
        $stmt = $conn->prepare($update_skills_query);
        if (!$stmt) {
            die("Statement preparation failed: " . $conn->error);
        }
        $stmt->bind_param("ss", $skills_json, $sid);
    } else {
        // Insert new skills record
        $insert_skills_query = "INSERT INTO student_skills (SID, skills) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_skills_query);
        if (!$stmt) {
            die("Statement preparation failed: " . $conn->error);
        }
        $stmt->bind_param("ss", $sid, $skills_json);
    }

    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: studentpage.php?sid=" . urlencode($sid));
    exit();
}
?>
