<?php
include('db.php'); // Database connection

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sid']) && isset($_POST['pid'])) {
    $studentID = $_POST['sid'];
    $projectID = $_POST['pid'];

    // Step 1: Insert the application into the 'application' table if it does not exist
    $insertQuery = "INSERT IGNORE INTO application (SID, PID, status) VALUES (?, ?, 'pending')";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("si", $studentID, $projectID);
    $stmt->execute();
    $stmt->close();

    // Step 2: Run the matchmaking ranking algorithm
    function calculateScore($studentID, $projectID, $conn) {
        // Fetch student skills from student_skills table (JSON format)
        $studentQuery = "SELECT skills, program, year FROM students 
                         LEFT JOIN student_skills ON students.SID = student_skills.SID WHERE students.SID = ?";
        $stmt = $conn->prepare($studentQuery);
        $stmt->bind_param("s", $studentID);
        $stmt->execute();
        $studentResult = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$studentResult) return false;

        $studentSkills = json_decode($studentResult['skills'], true);

        // Fetch project skills from project_skills table (JSON format)
        $projectQuery = "SELECT skills, ProjProgram, ProjYear FROM project
                         LEFT JOIN project_skills ON project.PID = project_skills.PID
                         WHERE project.PID = ?";
        $stmt = $conn->prepare($projectQuery);
        $stmt->bind_param("i", $projectID);
        $stmt->execute();
        $projectResult = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$projectResult) return false;

        $projectSkills = json_decode($projectResult['skills'], true);

        // Initialize Scores
        $score = 0;
        $maxScore = 0;

        // Skill Weights and Proficiency Values
        $skillWeights = ["languages" => 3, "tools" => 2, "frameworks" => 1];

        // Skill Matching Calculation
        foreach ($projectSkills as $category => $skills) {
            $weight = $skillWeights[$category] ?? 1;
            foreach ($skills as $skill => $requiredLevel) {
                $requiredPoints = $requiredLevel * $weight;
                $maxScore += $requiredPoints;

                // Check if student has the skill and calculate points
                if (isset($studentSkills[$category][$skill])) {
                    $studentLevel = $studentSkills[$category][$skill];
                    $studentPoints = $studentLevel * $weight;
                    $score += min($studentPoints, $requiredPoints);
                }
            }
        }

        // Calculate Percentage Score
        $percentageScore = $maxScore > 0 ? ($score / $maxScore) * 100 : 0;

        // Update application score in 'application' table
        $updateQuery = "UPDATE application SET score = ? WHERE SID = ? AND PID = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("dsi", $percentageScore, $studentID, $projectID);
        $stmt->execute();
        $stmt->close();

        return $percentageScore;
    }

    // Run the algorithm and update score
    $calculatedScore = calculateScore($studentID, $projectID, $conn);

    // Redirect back to the projects page with status
    if ($calculatedScore !== false) {
        header("Location: studentprojectpage.php?sid=$studentID&status=success");
    } else {
        header("Location: studentprojectpage.php?sid=$studentID&status=error");
    }

    exit();
} else {
    // If accessed without form submission
    header("Location: studentprojectpage.php?sid=$studentID&status=error");
    exit();
}
?>