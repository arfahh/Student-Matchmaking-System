<?php 
session_start();
include('db.php');
include('header.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if faculty is logged in
if (isset($_SESSION['fid'])) {
    $fid = $_SESSION['fid'];
} elseif (isset($_GET['fid'])) {
    $fid = htmlspecialchars(trim($_GET['fid']));
} else {
    die("No Faculty ID provided.");
}
$sql_max_projects = "SELECT max_projects FROM faculty WHERE FID = ?";
 $stmt_max_projects = $conn->prepare($sql_max_projects);
 $stmt_max_projects->bind_param("i", $fid);
 $stmt_max_projects->execute();
 $stmt_max_projects->bind_result($max_projects);
 $stmt_max_projects->fetch();
 $stmt_max_projects->close();
 
 // Fetch the current number of projects the faculty has
 $sql_current_projects = "SELECT COUNT(*) FROM project WHERE FID = ?";
 $stmt_current_projects = $conn->prepare($sql_current_projects);
 $stmt_current_projects->bind_param("i", $fid);
 $stmt_current_projects->execute();
 $stmt_current_projects->bind_result($current_projects);
 $stmt_current_projects->fetch();
 $stmt_current_projects->close();
 
 // Check if the faculty has reached the maximum number of projects
 $errorMessage = "";
 $disableButton = false;
 if ($current_projects >= $max_projects) {
     $errorMessage = "You have reached your limit of $max_projects projects.";
     $disableButton = true;  // Disable the Create Project button
 }
// Fetch projects assigned to the faculty along with skills and attachments
// Fetch projects assigned to the faculty along with skills and attachments
$sql_projects = "
    SELECT p.PID, p.projectName, p.ProjDesc, p.projstartdate, p.projenddate, p.created_at, p.attachment_path, ps.skills, p.archived
    FROM project p
    LEFT JOIN project_skills ps ON p.PID = ps.PID
    WHERE p.fid = ?
    ORDER BY p.archived ASC, p.created_at DESC";
$stmt_projects = $conn->prepare($sql_projects);
$stmt_projects->bind_param("i", $fid);
$stmt_projects->execute();
$result_projects = $stmt_projects->get_result();


$projects = [];
while ($row = $result_projects->fetch_assoc()) {
    $projects[$row['PID']] = $row;
}
$stmt_projects->close();


// Fetch applications for each project
$applications = [];
$sql_applications = "SELECT a.AID, a.SID, a.PID, a.status, a.score, s.firstName, s.lastName
                     FROM application a
                     JOIN students s ON a.SID = s.SID
                     WHERE a.PID = ?
                     ORDER BY a.score DESC" ;
foreach ($projects as $pid => $project) {
    $stmt_applications = $conn->prepare($sql_applications);
    $stmt_applications->bind_param("i", $pid);
    $stmt_applications->execute();
    $result_applications = $stmt_applications->get_result();

    while ($row = $result_applications->fetch_assoc()) {
        $applications[$pid][] = $row;
    }
    $stmt_applications->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Applications</title>
    <link rel="stylesheet" href="styles.css">
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }


     header, footer {
             background: linear-gradient(90deg, #0058ab 0%, #002279 50%, #0058ab 100%);
            color: white;

            text-align: center;

        }

    .project-card {
        position: relative;
        margin: 5px 0;
        padding: 12px;
        background-color: #e9e9e9;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .project-card h3 {
        margin: 0;
        font-size: 18px;
        font-weight: bold;
    }

    .project-date {
        position: absolute;
        top: 12px;
        right: 12px;
        font-size: 12px;
        color: #777;
    }

    .collapsible {
        background-color: #0056b3;
        color: white;
        padding: 8px;
        width: 100%;
        border: none;
        text-align: left;
        cursor: pointer;
        border-radius: 5px;
        margin-top: 5px;
        font-size: 15px;
    }

    .collapsible:hover {
        background-color: #004494;
    }

    .collapsible:after {
        content: '\002B';
        float: right;
    }
     .error-box {
             background-color: #f8d7da;
             color: #721c24;
             border: 1px solid #f5c6cb;
             padding: 10px;
             margin: 15px 0;
             display: none;
             border-radius: 5px;
             width: 80%;
             max-width: 400px;
             margin: 15px auto;
             text-align: center;
             position: absolute; /* Position it at the top */
             top: 20px; /* Adjust this to space it from the top */
             left: 50%;
             transform: translateX(-50%); /* Center horizontally */
             z-index: 1000; /* Ensure it's above other content */
         }

    .collapsible.active:after {
        content: "\2212";
    }

    .content {
        display: none;
        padding: 10px;
        background-color: #f1f1f1;
        border-radius: 5px;
        margin-top: 5px;
    }

    .attachment-link {
        display: inline-block;
        background-color: #28a745;
        color: white;
        padding: 5px 10px;
        text-decoration: none;
        border-radius: 5px;
        margin-top: 5px;
    }

    .attachment-link:hover {
        background-color: #218838;
    }

    .btn {
        background-color: #007bff;
        color: white;
        padding: 8px 12px;
        text-decoration: none;
        border-radius: 5px;
        border: none;
        font-size: 14px;
        margin: 5px;
    }

    .collapsible-skill {
    background-color: #0056b3; /* Blue color for skill category */
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
        background-color: #555;
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
        margin-top: 3px;
        font-size: 14px;
        color: #333;
        text-align: center;
    }

    .proficiency-bar {
        width: 100%;
        height: 8px;
        background-color: #e0e0e0;
        border-radius: 5px;
        position: relative;
        margin-top: 3px;
    }

    .proficiency-fill {
        height: 100%;
        background-color: #28a745;
        border-radius: 5px;
        position: absolute;
        top: 0;
        left: 0;
    }

    .proficiency-level {
        font-size: 12px;
        font-weight: bold;
        color: #333;
        margin-left: 5px;
        display: inline-block;
        vertical-align: middle;
        margin-top: -2px;
    }

    .slider-label {
        margin-left: 5px;
        font-size: 12px;
        color: #333;
        display: inline-block;
        vertical-align: middle;
    }

    .proficiency-indicator {
        background-color: #28a745;
        height: 16px;
        width: 16px;
        border-radius: 50%;
        position: absolute;
        top: -4px;
        transform: translateX(-50%);
    }

    .skill-item:hover .proficiency-bar {
        background-color: #cccccc;
    }

    .proficiency-indicator:hover {
        background-color: #13e843;
    }
    .slider-container {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 5px 0;
}

    .slider-container input[type="range"] {
        width: 150px;
        margin-right: 5px;
    }

    .slider-label {
        font-size: 14px;
        color: #333;
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
    display: none;
    padding: 10px;
    background-color: #f1f1f1;
    border-radius: 5px;
    margin-top: 5px;
}

.applicant-list table {
    width: 100%;
    border-collapse: collapse;
}

.applicant-list th, .applicant-list td {
    padding: 8px;
    text-align: left;
    border: 1px solid #ddd;
}

.applicant-list th {
    background-color: #0056b3;
    color: white;
}

.applicant-list tr:nth-child(odd) {
    background-color: #e9e9e9; /* Light gray for odd rows */
}

.applicant-list tr:nth-child(even) {
    background-color: #f4f4f4; /* Slightly lighter for even rows */
}

.button-container {
    display: inline-flex;
    gap: 5px;
}


.delete_btn {
    background-color: #ff0000;
    color: white;
    padding: 8px 12px;
    text-decoration: none;
    border-radius: 5px;
    border: none;
    font-size: 14px;
    margin: 5px;
}
.edit_btn {
    background-color: #007bff;
    color: white;
    padding: 5px 12px;
    text-decoration: none;
    border-radius: 5px;
    border: none;
    font-size: 14px;
    margin: 5px;
}

</style>
<script>
         // Function to show error message and make it disappear after 3 seconds
         function showError(message) {
             var errorBox = document.getElementById('errorBox');
             errorBox.innerHTML = message;
             errorBox.style.display = 'block'; // Show the error box
             setTimeout(function() {
                 errorBox.style.display = 'none'; // Hide the error box after 3 seconds
             }, 3000);
         }
 
         // Check if an error message exists from PHP and show it using JavaScript
         window.onload = function() {
             <?php if (!empty($errorMessage)): ?>
                 showError("<?php echo addslashes($errorMessage); ?>");
             <?php endif; ?>
         }
     </script>

</head>
<body>

<main>
    <h2>My Projects</h2>

 <div id="errorBox" class="error-box"></div>
    
      <?php if ($disableButton): ?> 
         <a href="create_project.php?fid=<?php echo $fid; ?>" class="btn" style="pointer-events: none; background-color: grey;">Create New Project </a>
         <h3> limit reached</h3>
     <?php else: ?>
         <a href="create_project.php?fid=<?php echo $fid; ?>" class="btn">Create New Project</a>
     <?php endif; ?>
 
    <?php if (count($projects) > 0): ?>
        <?php foreach ($projects as $pid => $project): ?>
            <div class="project-card">
                <div class="project-date">Posted on: <?php echo date("F j, Y", strtotime($project['created_at'])); ?></div>
                <h3><?php echo htmlspecialchars($project['projectName']); ?></h3>
                <button class="collapsible">View Details</button>
                
                <div class="content">
                    <div class="button-container">
                        <form action="delete_project.php" method="POST" style="display: inline;">
                            <input type="hidden" name="PID" value="<?php echo $pid; ?>">
                            <input type="hidden" name="fid" value="<?php echo htmlspecialchars($fid); ?>">
                            <button type="submit" class="delete_btn" onclick="return confirm('Are you sure you want to delete this project? This will also delete all associated applications.')">Delete</button>
                        </form>
                        <!-- Edit Project Button -->
                        <a href="edit_project.php?pid=<?php echo $pid; ?>&fid=<?php echo htmlspecialchars($fid); ?>" class="edit_btn">Edit</a>
                        <!-- Archive/Unarchive Button -->
                        <form action="archive_project.php" method="POST" style="display: inline;">
                            <input type="hidden" name="pid" value="<?php echo $pid; ?>">
                            <input type="hidden" name="archived" value="<?php echo $project['archived'] ? 0 : 1; ?>">
                            <input type="hidden" name="fid" value="<?php echo htmlspecialchars($fid); ?>">
                            <button type="submit" class="btn" style="background-color: <?php echo $project['archived'] ? '#888' : '#007bff'; ?>;">
                                <?php echo $project['archived'] ? 'Unarchive' : 'Archive'; ?>
                            </button>
                        </form>


                    </div>


                    <p><?php echo htmlspecialchars($project['ProjDesc']); ?></p>
                    <p><strong>Start Date:</strong> <?php echo htmlspecialchars($project['projstartdate']); ?></p>
                    <p><strong>End Date:</strong> <?php echo htmlspecialchars($project['projenddate']); ?></p>

                    <?php if (!empty($project['attachment_path'])): ?>
                        <a href="<?php echo htmlspecialchars($project['attachment_path']); ?>" target="_blank" class="attachment-link">View Attachment</a>
                    <?php endif; ?>

                    <!-- Skills Display -->
                    <p><strong>Skills:</strong></p>
                    <?php 
                    $skills = json_decode($project['skills'], true);
                    if ($skills): ?>
                        <?php foreach ($skills as $category => $skillSet): ?>
                            <button class="collapsible-skill"><?php echo ucfirst($category); ?></button>
                            <div class="skill-content">
                                <?php foreach ($skillSet as $skill => $level): ?>
                                    <div class="skill-item">
                                        <?php echo htmlspecialchars($skill); ?>
                                        <div class="slider-container">
                                            <input type="range" min="1" max="5" value="<?php echo $level; ?>" disabled
                                                oninput="updateProficiencyLabel(this, '<?php echo $category . '-' . $skill; ?>')">
                                            <span class="slider-label" id="label-<?php echo $category . '-' . $skill; ?>">
                                                <?php echo ["Beginner", "Novice", "Intermediate", "Advanced", "Expert"][$level - 1]; ?>
                                            </span>
                                        </div>
                                        
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                                        <!-- Applicant List -->
                    <h4>Applicants:</h4>
                    <?php if (!empty($applications[$pid])): ?>
                        <button class="collapsible-skill">View Applicants</button>
                        <div class="skill-content">
                            <div class="applicant-list">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Score</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($applications[$pid] as $app): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($app['SID']); ?></td>
                                                <td><?php echo htmlspecialchars($app['firstName'] . " " . $app['lastName']); ?></td>
                                                <td><?php echo htmlspecialchars($app['status']); ?></td>
                                                <td><?php echo htmlspecialchars($app['score'] !== null ? $app['score'] : 'N/A'); ?></td>
                                                <td>
                                                    <a href="studentpageForfaculty.php?sid=<?php echo urlencode($app['SID']); ?>&fid=<?php echo urlencode($fid); ?>" class="btn">View Profile</a>
                                                    <!-- Contact Student Button -->
                                                    <form action="contact_student.php" method="POST" style="display: inline;">
                                                        <input type="hidden" name="sid" value="<?php echo htmlspecialchars($app['SID']); ?>">
                                                        <input type="hidden" name="pid" value="<?php echo htmlspecialchars($pid); ?>">
                                                        <input type="hidden" name="fid" value="<?php echo htmlspecialchars($fid); ?>">
                                                        <button type="submit" class="btn">Contact Student</button>
                                                    </form>

                                                    <!-- Select For Project Button -->
                                                    <form action="select_student.php" method="POST" style="display: inline;">
                                                        <input type="hidden" name="sid" value="<?php echo htmlspecialchars($app['SID']); ?>">
                                                        <input type="hidden" name="pid" value="<?php echo htmlspecialchars($pid); ?>">
                                                        <input type="hidden" name="fid" value="<?php echo htmlspecialchars($fid); ?>">
                                                        <button type="submit" class="btn">Select For Project</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php else: ?>
                        <p>No applicants yet.</p>
                    <?php endif; ?>



                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No projects assigned.</p>
    <?php endif; ?>
</main>

<footer>
    <p>&copy; 2024 Student Matchmaking System</p>
</footer>

<script>
    // Toggle the project details
    var coll = document.getElementsByClassName("collapsible");
    for (var i = 0; i < coll.length; i++) {
        coll[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var content = this.nextElementSibling;
            content.style.display = content.style.display === "block" ? "none" : "block";
        });
    }

    // Toggle the skills and applicants dropdown
    var skillCollapsibles = document.getElementsByClassName("collapsible-skill");
    for (var j = 0; j < skillCollapsibles.length; j++) {
        skillCollapsibles[j].addEventListener("click", function() {
            this.classList.toggle("active");
            var skillContent = this.nextElementSibling;
            skillContent.style.display = skillContent.style.display === "block" ? "none" : "block";
        });
    }

    // Update the proficiency label dynamically
    function updateProficiencyLabel(slider, skillId) {
        const levels = ["Beginner", "Novice", "Intermediate", "Advanced", "Expert"];
        document.getElementById("label-" + skillId).innerText = levels[slider.value - 1];
    }

    // Toggle the visibility of the proficiency slider
    function toggleSlider(checkbox, skillId) {
        const slider = document.getElementById("slider-" + skillId);
        slider.style.display = checkbox.checked ? "flex" : "none";
    }
</script>



</body>
</html> 
