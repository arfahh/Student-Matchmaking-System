<?php
// Check if the SID is provided via URL
if (!isset($_GET['sid'])) {
    exit("Error: SID is missing.");
}

$studentID = $_GET['sid'];

// Include database connection
include('db.php');
include('header.php');

// Grab any filter parameters (if provided)
$filterDate = isset($_GET['filter_date']) ? trim($_GET['filter_date']) : '';
$filterFaculty = isset($_GET['filter_faculty']) ? trim($_GET['filter_faculty']) : '';

// Fetch available faculties (those with projects) for the drop-down
$sql_faculty = "SELECT DISTINCT f.FID, CONCAT(f.firstName, ' ', f.lastName) AS facultyName 
                FROM faculty f 
                JOIN project p ON f.FID = p.FID 
                ORDER BY facultyName ASC";
$result_faculty = $conn->query($sql_faculty);
$facultyOptions = [];
if ($result_faculty && $result_faculty->num_rows > 0) {
    while ($row_faculty = $result_faculty->fetch_assoc()) {
        $facultyOptions[] = $row_faculty;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Listings - Project Matchmaking System</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .details-container {
            margin-top: 10px;
            padding: 10px;
            background-color: #f1f1f1;
            border-radius: 5px;
            display: none;
        }
        header, footer {
             background: linear-gradient(90deg, #0058ab 0%, #002279 50%, #0058ab 100%);
            color: white;

            text-align: center;

        }

        .project-item {
            position: relative;
            margin: 1px 0;
            padding: 12px;
            background-color: #e9e9e9;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .project-item h4 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .project-description-preview {
            margin-top: 5px;
            color: #555;
            font-size: 14px;
        }

        .project-date {
            position: absolute;
            top: 12px;
            right: 12px;
            font-size: 12px;
            color: #777;
        }

        .collapsible-skill {
            background-color: #888;
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

        .collapsible:hover {
            background-color: #0056b3;
        }

        .details-container p {
            margin: 5px 0;
        }

        .details-container h4 {
            margin: 10px 0 5px;
            color: #333;
        }

        .apply-button {
            margin: 5px 0;
            background-color: #28a745;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .apply-button:hover {
            background-color: #218838;
        }

        .applied-status {
            color: green;
            font-weight: bold;
            margin: 8px 0;
        }
    </style>
    <script>
        

    function searchProjects() {
        var input = document.getElementById("search-bar").value.toLowerCase();
        var projectItems = document.querySelectorAll(".project-item");

        projectItems.forEach(function(item) {
            var title = item.getAttribute("data-title").toLowerCase();
            var description = item.getAttribute("data-description").toLowerCase();
            var skills = item.getAttribute("data-skills").toLowerCase();

            if (title.includes(input) || description.includes(input) || skills.includes(input)) {
                item.style.display = "block";
            } else {
                item.style.display = "none";
            }
        });
    }

    function filterProjects() {
        var filterDate = document.getElementById("filter_date").value;
        var filterFaculty = document.getElementById("filter_faculty").value;
        var projectItems = document.querySelectorAll(".project-item");

        projectItems.forEach(function(item) {
            var datePosted = item.getAttribute("data-date");
            var faculty = item.getAttribute("data-faculty");

            var dateMatch = !filterDate || new Date(datePosted) >= new Date(filterDate);
            var facultyMatch = !filterFaculty || faculty === filterFaculty;

            if (dateMatch && facultyMatch) {
                item.style.display = "block";
            } else {
                item.style.display = "none";
            }
        });
    }

    // Automatically trigger the filtering whenever the user changes the date or faculty
    document.getElementById("filter_date").addEventListener("change", filterProjects);
    document.getElementById("filter_faculty").addEventListener("change", filterProjects);



        // Event listeners for filters
        document.getElementById("filter_date").addEventListener("change", filterProjects);
        document.getElementById("filter_faculty").addEventListener("change", filterProjects);

        function toggleDetails(projectId) {
            var details = document.getElementById('details-' + projectId);
            var toggleButton = document.getElementById('toggle-' + projectId);
            if (details.style.display === "none") {
                details.style.display = "block";
                toggleButton.textContent = "Hide Details";
            } else {
                details.style.display = "none";
                toggleButton.textContent = "Show Details";
            }
        }

        function toggleSkillContent(skillId) {
            var content = document.getElementById(skillId);
            var button = document.getElementById("btn-" + skillId);
            if (content.style.display === "block") {
                content.style.display = "none";
                button.classList.remove("active");
            } else {
                content.style.display = "block";
                button.classList.add("active");
            }
        }
    </script>
</head>

<body>

<main>
    <section id="search-section">
        <h2>Find a Project</h2>
        <label for="filter_date">Filter by Date (on or after):</label>
        <input type="date" name="filter_date" id="filter_date">

        <label for="filter_faculty">Filter by Faculty Name:</label>
        <select name="filter_faculty" id="filter_faculty">
            <option value="">All Faculties</option>
            <?php foreach ($facultyOptions as $option): ?>
                <option value="<?php echo htmlspecialchars($option['facultyName']); ?>">
                    <?php echo htmlspecialchars($option['facultyName']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="button" onclick="filterProjects()">Apply Filters</button>

        <input type="text" id="search-bar" placeholder="Search by project title or skill..." onkeyup="searchProjects()">
    </section>


    <section id="project-list-section">
        <h3>Available Projects</h3>
        <div id="project-list">
            <?php
            $sql = "SELECT p.PID, p.projectName, p.ProjDesc, p.attachment_path, p.created_at, p.projstartdate, p.projenddate,
                f.firstName, f.lastName, ps.skills
                FROM project p
                LEFT JOIN faculty f ON p.FID = f.FID
                LEFT JOIN project_skills ps ON p.PID = ps.PID
                WHERE p.archived = 0";  // Exclude archived projects

            if (!empty($filterDate)) {
                $sql .= " AND p.created_at >= ?";
                $params[] = $filterDate;
                $types .= 's';
            }

            if (!empty($filterFaculty)) {
                $sql .= " AND f.FID = ?";
                $params[] = $filterFaculty;
                $types .= 's';
            }

            $sql .= " ORDER BY p.created_at DESC";


            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $pid = $row["PID"];
                    $facultyName = htmlspecialchars($row["firstName"] . " " . $row["lastName"]);
                    $datePosted = date("F j, Y", strtotime($row["created_at"]));
                    $descriptionPreview = strtok($row["ProjDesc"], "\n");

                    $skills = json_decode($row['skills'], true);
                    $allSkills = [];
                    if ($skills) {
                        foreach ($skills as $category => $skillList) {
                            foreach ($skillList as $skill => $level) {
                                $allSkills[] = $skill;
                            }
                        }
                    }
                    $skillsString = implode(", ", $allSkills);

                   echo "<div class='project-item' 
                    data-title='" . htmlspecialchars($row["projectName"]) . "' 
                    data-description='" . htmlspecialchars($row["ProjDesc"]) . "' 
                    data-skills='" . htmlspecialchars($skillsString) . "' 
                    data-date='" . htmlspecialchars($row["created_at"]) . "' 
                    data-faculty='" . htmlspecialchars($row["firstName"] . " " . $row["lastName"]) . "'>";

                    echo "<div class='project-date'>" . $datePosted . "</div>";
                    echo "<h4>" . htmlspecialchars($row["projectName"]) . "</h4>";
                    echo "<p class='project-description-preview'>" . htmlspecialchars($descriptionPreview) . "</p>";

                    // Check if the student has applied
                    $check_sql = "SELECT * FROM application WHERE SID = ? AND PID = ?";
                    $stmt = $conn->prepare($check_sql);
                    $stmt->bind_param("si", $studentID, $pid);
                    $stmt->execute();
                    $application_result = $stmt->get_result();
                    $applied = ($application_result->num_rows > 0);
                    $stmt->close();

                    if ($applied) {
                        echo "<p class='applied-status'>Applied</p>";
                    } else {
                        echo "<form action='apply.php' method='POST'>";
                        echo "<input type='hidden' name='pid' value='" . htmlspecialchars($pid) . "'>";
                        echo "<input type='hidden' name='sid' value='" . htmlspecialchars($studentID) . "'>";
                        echo "<button type='submit' class='apply-button'>Apply Now</button>";
                        echo "</form>";
                    }

                    echo "<button type='button' id='toggle-" . $pid . "' onclick='toggleDetails(" . $pid . ")'>Show Details</button>";
                    echo "<div id='details-" . $pid . "' class='details-container'>";
                    echo "<p><strong>Faculty Name:</strong> " . $facultyName . "</p>";
                    echo "<p><strong>Description:</strong> " . htmlspecialchars($row["ProjDesc"]) . "</p>";
                    echo "<p><strong>Start Date:</strong> " . htmlspecialchars($row["projstartdate"]) . "</p>";
                    echo "<p><strong>End Date:</strong> " . htmlspecialchars($row["projenddate"]) . "</p>";
                    
                    // Display attachment if available under details
                    if (!empty($row["attachment_path"])) {
                        echo "<p><strong>Attachment:</strong> <a href='" . htmlspecialchars($row["attachment_path"]) . "' target='_blank'>View Attachment</a></p>";
                    }
                    $skills = json_decode($row['skills'], true);
                    if ($skills) {
                        foreach ($skills as $category => $skillList) {
                            echo "<button class='collapsible-skill' id='btn-$pid-$category' onclick=\"toggleSkillContent('$pid-$category')\">" . ucfirst($category) . "</button>";
                            echo "<div id='$pid-$category' class='skill-content'>";
                            foreach ($skillList as $skill => $level) {
                                echo "<div class='skill-item'>" . htmlspecialchars($skill) . "</div>";
                            }
                            echo "</div>";
                        }
                    }

                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>No projects available.</p>";
            }

            $conn->close();
            ?>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2024 Student Matchmaking System</p>
</footer>

</body>
</html>
