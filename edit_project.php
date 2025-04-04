<?php 
session_start();
include('db.php');
include('headeradmin.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get PID and FID from URL
$pid = isset($_GET['pid']) ? htmlspecialchars(trim($_GET['pid'])) : null;
$fid = isset($_GET['fid']) ? htmlspecialchars(trim($_GET['fid'])) : null;

if (!$pid || !$fid) {
    die("Invalid project or faculty ID.");
}

// Fetch available skills
$skills_query = "SELECT skill_name, category FROM skills_list ORDER BY category, skill_name";
$skills_result = $conn->query($skills_query);
$skillsByCategory = [];

while ($row = $skills_result->fetch_assoc()) {
    $skillsByCategory[$row['category']][] = $row['skill_name'];
}

// Fetch existing project details
$sql_project = "SELECT projectName, ProjDesc, projstartdate, projenddate, application_deadline, attachment_path FROM project WHERE PID = ?";
$stmt = $conn->prepare($sql_project);
$stmt->bind_param("i", $pid);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch existing project skills
$sql_skills = "SELECT skills FROM project_skills WHERE PID = ?";
$stmt = $conn->prepare($sql_skills);
$stmt->bind_param("i", $pid);
$stmt->execute();
$skills_result = $stmt->get_result()->fetch_assoc();
$existingSkills = $skills_result ? json_decode($skills_result['skills'], true) : [];
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $projectName = trim($_POST["projectName"]);
    $projDesc = trim($_POST["projDesc"]);
    $startDate = $_POST["startDate"];
    $endDate = $_POST["endDate"];
    $applicationDeadline = $_POST["applicationDeadline"];

    // Handle attachment upload
    $attachmentPath = $project['attachment_path']; // Keep existing path if no new file is uploaded
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $attachmentName = basename($_FILES['attachment']['name']);
        $targetFilePath = $uploadDir . $attachmentName;

        // Move the uploaded file to the server
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFilePath)) {
            $attachmentPath = "uploads/" . $attachmentName; // Update the path
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

    // Update project details
    $sql_update = "UPDATE project SET projectName=?, ProjDesc=?, projstartdate=?, projenddate=?, application_deadline=?, attachment_path=? WHERE PID=?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("ssssssi", $projectName, $projDesc, $startDate, $endDate, $applicationDeadline, $attachmentPath, $pid);
    $stmt->execute();
    $stmt->close();

    // Update project skills
    $update_skills_query = "UPDATE project_skills SET skills=? WHERE PID=?";
    $stmt = $conn->prepare($update_skills_query);
    $stmt->bind_param("si", $skills_json, $pid);
    $stmt->execute();
    $stmt->close();

    // Redirect to faculty applications page
    header("Location: faculty_applications.php?fid=" . urlencode($fid));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Project</title>
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
        <h2>Edit Project</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="projectName">Project Name:</label>
            <input type="text" name="projectName" value="<?php echo htmlspecialchars($project['projectName']); ?>" required class="profile-input">

            <label for="projDesc">Project Description:</label>
            <textarea name="projDesc" required class="profile-input"><?php echo htmlspecialchars($project['ProjDesc']); ?></textarea>

            <label for="startDate">Start Date:</label>
            <input type="date" name="startDate" value="<?php echo htmlspecialchars($project['projstartdate']); ?>" required class="profile-input">

            <label for="endDate">End Date:</label>
            <input type="date" name="endDate" value="<?php echo htmlspecialchars($project['projenddate']); ?>" required class="profile-input">

            <label for="applicationDeadline">Application Deadline:</label>
            <input type="date" name="applicationDeadline" value="<?php echo htmlspecialchars($project['application_deadline']); ?>" required class="profile-input">
            <!-- Attachment Upload Section -->
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
                            <?php $isChecked = isset($existingSkills[$category][$skill]); ?>
                            <label>
                                <input type="checkbox" name="<?php echo $category; ?>[]" value="<?php echo $skill; ?>" 
                                    <?php echo $isChecked ? 'checked' : ''; ?>
                                    onchange="toggleSlider(this, '<?php echo $category . '-' . $skill; ?>')">
                                <?php echo $skill; ?>
                            </label>
                           <div class="slider-container" id="slider-<?php echo $category . '-' . $skill; ?>" style="display: <?php echo $isChecked ? 'flex' : 'none'; ?>;">
                                <input type="range" name="proficiency[<?php echo $skill; ?>]" min="1" max="5" value="<?php echo $isChecked ? $existingSkills[$category][$skill] : 3; ?>" oninput="updateProficiencyLabel(this, '<?php echo $category . '-' . $skill; ?>')">
                                <span class="slider-label" id="label-<?php echo $category . '-' . $skill; ?>">
                                    <?php echo ["Beginner", "Novice", "Intermediate", "Advanced", "Expert"][$existingSkills[$category][$skill] - 1]; ?>
                                </span>
                            </div>

                        <?php endforeach; ?>
                    </div>
                </fieldset>
            <?php endforeach; ?>

            <button type="submit" class="save-button">Save Changes</button>
        </form>
    </div>
</main>
<script> 
    function updateProficiencyLabel(slider, skillId) {
        const levels = ["Beginner", "Novice", "Intermediate", "Advanced", "Expert"];
        const levelText = levels[slider.value - 1];
        const labelElement = document.getElementById("label-" + skillId);
        labelElement.innerText = levelText;
    }
</script>
</body>
</html>