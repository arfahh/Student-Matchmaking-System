<?php 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('db.php');
include ('header.php');

// Get faculty ID from session or URL
$fid = isset($_SESSION['fid']) ? $_SESSION['fid'] : (isset($_GET['fid']) ? htmlspecialchars(trim($_GET['fid'])) : null);
if (!$fid) {
    die("No Faculty ID provided.");
}

// Fetch available skills
$skills_query = "SELECT skill_name, category FROM skills_list ORDER BY category, skill_name";
$skills_result = $conn->query($skills_query);
$skillsByCategory = [];

while ($row = $skills_result->fetch_assoc()) {
    $skillsByCategory[$row['category']][] = $row['skill_name'];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $projectName = trim($_POST["projectName"]);
    $projDesc = trim($_POST["projDesc"]);
    $startDate = isset($_POST["startDate"]) ? $_POST["startDate"] : null;
    $endDate = isset($_POST["endDate"]) ? $_POST["endDate"] : null;
    $applicationDeadline = isset($_POST["applicationDeadline"]) ? $_POST["applicationDeadline"] : null;
    $attachmentPath = "";
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";
        $attachmentName = basename($_FILES['attachment']['name']);
        $targetFilePath = $uploadDir . $attachmentName;

        // Move the uploaded file to the server
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFilePath)) {
            $attachmentPath = $targetFilePath;
        }
    }
    // Prepare skill proficiency as JSON
    $skills = [];
    foreach ($skillsByCategory as $category => $skillList) {
        foreach ($skillList as $skill) {
            if (isset($_POST[$category]) && in_array($skill, $_POST[$category])) {
                $skills[$category][$skill] = intval($_POST['proficiency'][$skill] ?? 3);
            }
        }
    }
    $skills_json = json_encode($skills);

    // Validate inputs
    if (empty($projectName) || empty($projDesc) || empty($skills_json) || !$applicationDeadline) {
        echo "<p style='color:red;'>All fields are required.</p>";
    } else {
        // Insert project data
        $sql = "INSERT INTO project (projectName, FID, ProjDesc, status, projstartdate, projenddate, application_deadline, attachment_path) 
                VALUES (?, ?, ?, TRUE, ?, ?, ?,?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssssss", $projectName, $fid, $projDesc, $startDate, $endDate, $applicationDeadline, $attachmentPath);

            if ($stmt->execute()) {
                $projectId = $stmt->insert_id;

                // Insert skills with proficiency
                $insert_skills_query = "INSERT INTO project_skills (PID, skills) VALUES (?, ?)";
                $stmt_skills = $conn->prepare($insert_skills_query);
                $stmt_skills->bind_param("is", $projectId, $skills_json);
                $stmt_skills->execute();
                $stmt_skills->close();

                // Redirect to faculty projects page
                header("Location: faculty_applications.php?fid=" . urlencode($fid));
                exit;
            } else {
                echo "<p style='color:red;'>Error adding project: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='color:red;'>Query preparation failed: " . $conn->error . "</p>";
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Project</title>
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
            max-height: 300px;
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
        .slider-container {
            margin-top: 5px;
            display: none;
            align-items: center;
            gap: 10px;
        }
        .slider-container input[type="range"] {
            width: 150px;
        }
        .slider-label {
            margin-top: 3px;
            font-size: 14px;
            color: #333;
            text-align: center;
        }
        .attachment-container {
            background-color: #f9f9f9;
            border: 2px solid #007bff;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .attachment-container label {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
            display: block;
        }
        .save-button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .save-button:hover {
            background-color: #0056b3;
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
    </style>
</head>
<body>


<main>
    <div class="container">
        <h2>Create a New Project</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="projectName">Project Name:</label>
            <input type="text" id="projectName" name="projectName" required class="profile-input">

            <label for="projDesc">Project Description:</label>
            <textarea id="projDesc" name="projDesc" required class="profile-input"></textarea>

            <label for="startDate">Start Date:</label>
            <input type="date" id="startDate" name="startDate" required class="profile-input">

            <label for="endDate">End Date:</label>
            <input type="date" id="endDate" name="endDate" required class="profile-input">

            <label for="applicationDeadline">Application Deadline:</label>
            <input type="date" id="applicationDeadline" name="applicationDeadline" required class="profile-input">

            <!-- Resume Upload Section -->
            <fieldset>
                <legend>Attachment Upload</legend>
                <label for="attachment">Upload Attachment (optional):</label>
                <input type="file" id="attachment" name="attachment" accept=".pdf,.doc,.docx">
                <?php if (!empty($project['attachment_path'])): ?>
                    <p>Current Attachment: 
                        <a href="<?php echo htmlspecialchars($project['attachment_path']); ?>" target="_blank">View Attachment</a>
                    </p>
                <?php endif; ?>
            </fieldset>

            <h3>Required Skills:</h3>
            <?php foreach ($skillsByCategory as $category => $skills): ?>
                <fieldset>
                    <legend><?php echo ucfirst($category); ?></legend>
                    <input type="text" class="search-bar" placeholder="Search for <?php echo $category; ?>..." onkeyup="filterSkills('<?php echo $category; ?>')">
                    <div id="skills-<?php echo $category; ?>" class="scrollable-container">
                        <?php foreach ($skills as $skill): ?>
                            <label>
                                <input type="checkbox" name="<?php echo $category; ?>[]" value="<?php echo $skill; ?>" onchange="toggleSlider(this, '<?php echo $category . '-' . $skill; ?>')">
                                <?php echo $skill; ?>
                            </label>
                            <div class="slider-container" id="slider-<?php echo $category . '-' . $skill; ?>">
                                <input type="range" name="proficiency[<?php echo $skill; ?>]" min="1" max="5" value="3" oninput="updateProficiencyLabel(this, '<?php echo $category . '-' . $skill; ?>')">
                                <span class="slider-label" id="label-<?php echo $category . '-' . $skill; ?>">Intermediate</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </fieldset>
            <?php endforeach; ?>

            <button type="submit" class="save-button">Submit Project</button>
        </form>
    </div>
</main>

<script>
    function filterSkills(category) {
        var input = document.querySelector("input[placeholder='Search for " + category + "...']");
        var filter = input.value.toLowerCase();
        var container = document.getElementById("skills-" + category);
        var labels = container.getElementsByTagName("label");
        for (let label of labels) {
            label.style.display = label.textContent.toLowerCase().includes(filter) ? "" : "none";
        }
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