<?php
// Include the database connection
include('db.php');
include('header.php');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get Student ID (from URL or session)
$sid = isset($_GET['sid']) ? htmlspecialchars(trim($_GET['sid'])) : null;

if (!$sid) {
    die("No student ID provided.");
}

// Prepare SQL statement to fetch student details
$sql = "SELECT sid, firstName, lastName, program, year, email, linkedin_link, github_link, website_link, other_links, resume_path FROM students WHERE sid = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Statement preparation failed: " . $conn->error);
}

$stmt->bind_param("s", $sid);
$stmt->execute();
$result = $stmt->get_result();

// Check if student exists
if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    die("Student not found.");
}

// Fetch available skills from skills_list table
$skills_query = "SELECT skill_name, category FROM skills_list ORDER BY category, skill_name";
$skills_result = $conn->query($skills_query);
$skillsByCategory = [];

while ($row = $skills_result->fetch_assoc()) {
    $skillsByCategory[$row['category']][] = $row['skill_name'];
}

// Fetch the student's existing skills from the new JSON structure
$student_skills_query = "SELECT skills FROM student_skills WHERE SID = ?";
$stmt = $conn->prepare($student_skills_query);
$stmt->bind_param("s", $sid);
$stmt->execute();
$student_skills_result = $stmt->get_result();
$student_skills = $student_skills_result->fetch_assoc();

// Decode the JSON data into an associative array
$student_skills = $student_skills ? json_decode($student_skills['skills'], true) : [];

// Close connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student Profile - Project Matchmaking System</title>
    <link rel="stylesheet" href="styles.css">
    <style>

        header, footer {
             background: linear-gradient(90deg, #0058ab 0%, #002279 50%, #0058ab 100%);
            color: white;

            text-align: center;

        }
        .profile-input {
            font-size: 16px;
            font-weight: bold;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            background-color: #f9f9f9;
        }
        .scrollable-container {
            max-height: 200px;
            overflow-y: auto;
            border: 2px solid #007bff;
            padding: 5px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .scrollable-container label {
            display: block;
            margin: 2px 0;
            font-size: 14px;
        }
        legend {
            font-weight: bold;
            color: #007bff;
            font-size: 16px;
        }
        .search-bar {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
            background-color: #f9f9f9;
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
    </style>
</head>
<body>

<main>
    <div class="container">
        <h2>Edit Student Profile</h2>

        <form action="updateProfile.php" method="POST" enctype="multipart/form-data">
            <label for="sid">Student ID:</label>
            <input type="text" id="sid" name="sid" value="<?php echo htmlspecialchars($student['sid']); ?>" readonly class="profile-input">

            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($student['firstName']); ?>" required class="profile-input">

            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($student['lastName']); ?>" required class="profile-input">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required class="profile-input">

            <label for="program">Program:</label>
            <input type="text" id="program" name="program" value="<?php echo htmlspecialchars($student['program']); ?>" required class="profile-input">
            
            <fieldset class="year-options">
                <legend>Year of Study</legend>
                <label><input type="radio" name="year" value="Undergraduate" <?php echo ($student['year'] == 'Undergraduate') ? 'checked' : ''; ?>> Undergraduate</label>
                <label><input type="radio" name="year" value="Graduate" <?php echo ($student['year'] == 'Graduate') ? 'checked' : ''; ?>> Graduate</label>
            </fieldset>

            <!-- Social Links -->
            <label for="linkedin_link">LinkedIn Link:</label>
            <input type="text" id="linkedin_link" name="linkedin_link" value="<?php echo htmlspecialchars($student['linkedin_link'] ?? ''); ?>" class="profile-input">

            <label for="github_link">GitHub Link:</label>
            <input type="text" id="github_link" name="github_link" value="<?php echo htmlspecialchars($student['github_link'] ?? ''); ?>" class="profile-input">

            <label for="website_link">Website Link:</label>
            <input type="text" id="website_link" name="website_link" value="<?php echo htmlspecialchars($student['website_link'] ?? ''); ?>" class="profile-input">

            <label for="other_links">Other Links:</label>
            <textarea id="other_links" name="other_links" class="profile-input"><?php echo htmlspecialchars($student['other_links'] ?? ''); ?></textarea>

            <!-- SKILLS SELECTION WITH SEARCH FUNCTIONALITY -->
            <?php foreach ($skillsByCategory as $category => $skills): ?>
                <fieldset>
                    <legend><?php echo ucfirst($category); ?></legend>
                    
                    <input type="text" id="search-<?php echo $category; ?>" class="search-bar" placeholder="Search for <?php echo $category; ?>..." onkeyup="filterSkills('<?php echo $category; ?>')" onfocus="resetSkills('<?php echo $category; ?>')">

                    <div id="skills-<?php echo $category; ?>" class="scrollable-container">
                        <?php foreach ($skills as $skill): ?>
                            <?php
                                $isChecked = isset($student_skills[$category][$skill]);
                                $proficiency = $isChecked ? $student_skills[$category][$skill] : 3;
                            ?>
                            <label>
                                <input type="checkbox" name="<?php echo $category; ?>[]" value="<?php echo $skill; ?>" 
                                    <?php echo $isChecked ? 'checked' : ''; ?>
                                    onchange="toggleSlider(this, '<?php echo $category . '-' . $skill; ?>')">
                                <?php echo $skill; ?>
                            </label>
                            <div class="slider-container" id="slider-<?php echo $category . '-' . $skill; ?>" style="display: <?php echo $isChecked ? 'flex' : 'none'; ?>;">
                                <input type="range" name="proficiency[<?php echo $skill; ?>]" min="1" max="5" value="<?php echo $proficiency; ?>" 
                                    oninput="updateProficiencyLabel(this, '<?php echo $category . '-' . $skill; ?>')">
                                <span id="label-<?php echo $category . '-' . $skill; ?>"><?php echo ["Beginner", "Novice", "Intermediate", "Advanced", "Expert"][$proficiency - 1]; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </fieldset>
            <?php endforeach; ?>
            <!-- Resume Upload Section -->
            <fieldset>
                <legend>Resume Upload</legend>
                <label for="resume">Upload Resume (optional):</label>
                <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx">
                <?php if (!empty($student['resume_path'])): ?>
                    <p>Current Resume: 
                        <a href="<?php echo htmlspecialchars($student['resume_path']); ?>" target="_blank">View Resume</a>
                    </p>
                <?php endif; ?>
            </fieldset>

            <button type="submit" class="save-button">Save Changes</button>
        </form>
    </div>
</main>

<footer>
    <p>&copy; 2024 Student Matchmaking System</p>
</footer>

<script>
    function filterSkills(category) {
        var input = document.getElementById("search-" + category);
        var filter = input.value.toLowerCase();
        var container = document.getElementById("skills-" + category);
        var labels = container.getElementsByTagName("label");
        for (let label of labels) {
            label.style.display = label.textContent.toLowerCase().includes(filter) ? "" : "none";
        }
    }

    function resetSkills(category) {
        document.getElementById("search-" + category).value = "";
        filterSkills(category);
    }

    function toggleSlider(checkbox, skillId) {
        const slider = document.getElementById("slider-" + skillId);
        slider.style.display = checkbox.checked ? "flex" : "none";
    }

    function updateProficiencyLabel(slider, skillId) {
        const levels = ["Beginner", "Novice", "Intermediate", "Advanced", "Expert"];
        document.getElementById("label-" + skillId).innerText = levels[slider.value - 1];
    }
</script>

</body>
</html>