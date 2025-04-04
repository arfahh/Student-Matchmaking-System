<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include('db.php');
include('header.php');

// Get the student ID from the URL
$sid = isset($_GET['sid']) ? htmlspecialchars(trim($_GET['sid'])) : null;

// Check if SID is provided
if (!$sid) {
    die("No Student ID provided.");
}

// Prepare SQL query to fetch student details
$sql_student = "SELECT sid, firstName, lastName, program, email, year, linkedin_link, github_link, website_link, other_links, resume_path FROM students WHERE sid = ?";
$stmt_student = $conn->prepare($sql_student);
if (!$stmt_student) {
    die("Query preparation failed: " . $conn->error);
}
$stmt_student->bind_param("s", $sid);
$stmt_student->execute();
$result_student = $stmt_student->get_result();

// Check if the student exists
if ($result_student->num_rows > 0) {
    $student = $result_student->fetch_assoc();
} else {
    die("Student not found.");
}
$stmt_student->close();

// Fetch student's skills
$sql_skills = "SELECT skills FROM student_skills WHERE SID = ?";
$stmt_skills = $conn->prepare($sql_skills);
$stmt_skills->bind_param("s", $sid);
$stmt_skills->execute();
$result_skills = $stmt_skills->get_result();
$student_skills = $result_skills->fetch_assoc();
$stmt_skills->close();

// Decode JSON skills data
$skills = isset($student_skills['skills']) ? json_decode($student_skills['skills'], true) : [];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        header, footer {
             background: linear-gradient(90deg, #0058ab 0%, #002279 50%, #0058ab 100%);
            color: white;

            text-align: center;

        }
        .container {
            width: 80%;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .profile-details, .student-skills {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .profile-details p, .student-skills p {
            font-size: 16px;
            margin: 8px 0;
        }
        .edit-button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: 0.3s;
        }
        .edit-button:hover {
            background-color: #0056b3;
        }
        nav ul {
            list-style: none;
            padding: 0;
            text-align: center;
        }
        nav ul li {
            display: inline;
            margin: 0 15px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
        }
        .collapsible-skill {
            background-color: #0056b3;
            color: white;
            padding: 8px;
            width: 100%;
            border: none;
            text-align: left;
            cursor: pointer;
            border-radius: 5px;
            margin: 5px 0;
            font-size: 15px;
        }
        .collapsible-skill:hover {
            background-color: #004494;
        }
        .collapsible-skill:after {
            content: '\002B';
            float: right;
        }
        .collapsible-skill.active:after {
            content: "\2212";
        }
        .skill-content {
            padding: 8px;
            background-color: #ddd;
            margin: 5px 0;
            border-radius: 5px;
            display: none;
        }
        .skill-item {
            margin: 4px 0;
            font-size: 14px;
        }
        .slider-container {
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .slider-container input[type="range"] {
            width: 150px;
        }

        .slider-label {
            font-size: 14px;
            color: #333;
            margin-left: 5px;
        }

        .collapsible-skill {
            background-color: #0056b3;
            color: white;
            padding: 8px;
            width: 100%;
            border: none;
            text-align: left;
            cursor: pointer;
            border-radius: 5px;
            margin: 5px 0;
            font-size: 15px;
        }

        .collapsible-skill:hover {
            background-color: #004494;
        }

        .collapsible-skill:after {
            content: '\002B'; /* Plus sign */
            float: right;
        }

        .collapsible-skill.active:after {
            content: "\2212"; /* Minus sign */
        }

        .skill-content {
            padding: 8px;
            background-color: #ddd;
            margin: 5px 0;
            border-radius: 5px;
            display: none;
        }

        .skill-item {
            margin: 4px 0;
            font-size: 14px;
        }

    </style>
</head>
<body>

    <main>
        <div class="container">
            <h2>Student Profile</h2>
            <div class="profile-details">
                <p><strong>SID:</strong> <?php echo htmlspecialchars($student['sid']); ?></p>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($student['firstName'] . " " . $student['lastName']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
                <p><strong>Program:</strong> <?php echo htmlspecialchars($student['program']); ?></p>
                <p><strong>Year:</strong> <?php echo htmlspecialchars($student['year']); ?></p>
                <?php if (!empty($student['linkedin_link'])): ?>
                <p><strong>LinkedIn:</strong> 
                    <a href="<?php echo (strpos($student['linkedin_link'], 'http') === 0 ? '' : 'https://') . htmlspecialchars($student['linkedin_link']); ?>" 
                    target="_blank" rel="noopener noreferrer">
                        <?php echo htmlspecialchars($student['linkedin_link']); ?>
                    </a>
                </p>
                <?php endif; ?>

                <?php if (!empty($student['github_link'])): ?>
                    <p><strong>GitHub:</strong> 
                        <a href="<?php echo (strpos($student['github_link'], 'http') === 0 ? '' : 'https://') . htmlspecialchars($student['github_link']); ?>" 
                        target="_blank" rel="noopener noreferrer">
                            <?php echo htmlspecialchars($student['github_link']); ?>
                        </a>
                    </p>
                <?php endif; ?>

                <?php if (!empty($student['website_link'])): ?>
                    <p><strong>Website:</strong> 
                        <a href="<?php echo (strpos($student['website_link'], 'http') === 0 ? '' : 'https://') . htmlspecialchars($student['website_link']); ?>" 
                        target="_blank" rel="noopener noreferrer">
                            <?php echo htmlspecialchars($student['website_link']); ?>
                        </a>
                    </p>
                <?php endif; ?>

                <?php if (!empty($student['other_links'])): ?>
                    <p><strong>Other:</strong> 
                        <a href="<?php echo (strpos($student['other_links'], 'http') === 0 ? '' : 'https://') . htmlspecialchars($student['other_links']); ?>" 
                        target="_blank" rel="noopener noreferrer">
                            <?php echo htmlspecialchars($student['other_links']); ?>
                        </a>
                    </p>
                <?php endif; ?>
                <?php if (!empty($student['resume_path'])): ?>
                    <p><strong>Resume:</strong> 
                        <a href="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . '/' . htmlspecialchars(str_replace(' ', '%20', $student['resume_path'])); ?>" 
                        target="_blank" rel="noopener noreferrer">
                            Click to Download Resume
                        </a>
                    </p>
                <?php else: ?>
                    <p><strong>Resume:</strong> Not provided.</p>
                <?php endif; ?>
            </div>

            <div class="student-skills">
            <h3>Skills</h3>
            <?php if (!empty($skills)): ?>
                <?php foreach ($skills as $category => $skillList): ?>
                    <button class="collapsible-skill"><?php echo ucfirst($category); ?></button>
                    <div class="skill-content">
                        <?php foreach ($skillList as $skill => $level): ?>
                            <div class="skill-item">
                                <?php echo htmlspecialchars($skill); ?>
                                <div class="slider-container">
                                    <input type="range" min="1" max="5" value="<?php echo htmlspecialchars($level); ?>" disabled>
                                    <span class="slider-label">
                                        <?php 
                                            $levels = ["Beginner", "Novice", "Intermediate", "Advanced", "Expert"];
                                            echo $levels[$level - 1];
                                        ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No skills available.</p>
            <?php endif; ?>
        </div>

        </div>
    </main>

    <footer>
        <p>&copy; 2024 Student Matchmaking System</p>
    </footer>

    <script>
        var coll = document.getElementsByClassName("collapsible-skill");
        for (var i = 0; i < coll.length; i++) {
            coll[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var content = this.nextElementSibling;
                content.style.display = content.style.display === "block" ? "none" : "block";
            });
        }
    </script>
</body>
</html>