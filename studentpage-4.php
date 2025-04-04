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

// Fetch student's skills and proficiency (using the JSON structure)
$sql_proficiency = "SELECT skills FROM student_skills WHERE sid = ?";
$stmt_proficiency = $conn->prepare($sql_proficiency);
$stmt_proficiency->bind_param("s", $sid);
$stmt_proficiency->execute();
$result_proficiency = $stmt_proficiency->get_result();
$student_proficiencies = [];
if ($row = $result_proficiency->fetch_assoc()) {
    $student_proficiencies = json_decode($row['skills'], true);
}
$stmt_proficiency->close();

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
            padding: 8px 10px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: 0.3s;
        }
        .edit-button:hover {
            background-color: #0056b3;
        }
        .skill-category {
            font-size: 16px;
            font-weight: bold;
            margin-top: 12px;
            cursor: pointer;
            background-color: #ccc;
            color: #333;
            padding: 6px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .plus-sign {
            font-weight: bold;
            font-size: 18px;
            margin-left: 10px;
        }
        .skill-list {
            display: none;
            padding-left: 20px;
        }
        .skill-item {
            margin: 8px 0;
            cursor: pointer;
            padding: 8px;
            background-color: #f1f1f1;
            border-radius: 5px;
            transition: 0.3s;
        }
        .skill-item:hover {
            background-color: #e0e0e0;
        }
        .skill-details {
            display: none;
            padding-left: 20px;
        }
        .proficiency-bar {
            width: 100%;
            background-color: #e0e0e0;
            border-radius: 5px;
            overflow: hidden;
            height: 10px;
        }
        .proficiency-fill {
            height: 100%;
            border-radius: 5px;
        }
        .proficiency-level {
            text-align: center;
            color: #fff;
            line-height: 10px;
            font-size: 12px;
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
            <?php foreach (["languages", "tools", "frameworks"] as $category): ?>
                <div class="skill-category" onclick="toggleCategory('<?php echo $category; ?>')">
                    <?php echo ucfirst($category); ?>
                    <span id="plus-<?php echo $category; ?>" class="plus-sign">+</span>
                </div>
                <div id="<?php echo $category; ?>" class="skill-list">
                    <?php if (!empty($student_proficiencies[$category])): ?>
                        <?php foreach ($student_proficiencies[$category] as $skill => $level): ?>
                            <div class="skill-item"><?php echo htmlspecialchars($skill); ?>
                                <div class="proficiency-bar">
                                    <div class="proficiency-fill" style="width: <?php echo ($level * 20); ?>%; background-color: #28a745;">
                                        <div class="proficiency-level"><?php echo $level; ?> / 5</div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No skills added yet.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="edit-profile">
            <a href="EditStudentProfilePage.php?sid=<?php echo urlencode($sid); ?>" class="edit-button">Edit Profile</a>
        </div>
    </div>
</main>

<footer>
    <p>&copy; 2024 Student Matchmaking System</p>
</footer>

<script>
    function toggleCategory(category) {
        var element = document.getElementById(category);
        var plusSign = document.getElementById("plus-" + category);
        element.style.display = element.style.display === "block" ? "none" : "block";
        plusSign.textContent = element.style.display === "block" ? "−" : "+";
    }
</script>

</body>
</html>