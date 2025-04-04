<?php
session_start();
include('db.php');
include('header.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if SID or FID is passed
if (isset($_GET['sid'])) {
    $sid = $_GET['sid'];
    $isStudent = true;
} elseif (isset($_GET['fid'])) {
    $fid = $_GET['fid'];
    $isStudent = false;
} else {
    die("No SID or FID provided.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications - Project Matchmaking System</title>
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

        .application-item {
            position: relative;
            margin: 5px 0;
            padding: 12px;
            background-color: #e9e9e9;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .application-item h3 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .collapsible {
            background-color: #0056b3;
            color: white;
            padding: 10px;
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

        .content p, .content ul {
            margin: 5px 0;
        }

        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
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
        .project-date {
            position: absolute;
            top: 12px;
            right: 12px;
            font-size: 12px;
            color: #777;
        }


        .accepted {
            background-color: #c8e6c9; /* Light green */
            border: 1px solid #4caf50;
        }

        .under-review {
            background-color: #ffecb3; /* Light yellow */
            border: 1px solid #ffb300;
        }

        .pending {
            background-color: #fff9c4; /* Light yellow */
            border: 1px solid #fbc02d;
        }

        .not-selected {
            background-color: #d3d3d3; /* Gray */
            color: #888;
            filter: grayscale(100%);
            margin: 5px 0;
            padding: 12px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .application-item {
            position: relative;
            margin: 5px 0;
            padding: 12px;
            background-color: #e9e9e9;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<main>
    <?php if ($isStudent): ?>
        <h2>My Applications</h2>
     <?php
// Fetch student applications with faculty name instead of FID
        $sql = "SELECT a.AID, a.status, a.score, p.projectName, f.firstName, f.lastName, p.attachment_path, p.created_at, p.ProjDesc, ps.skills
                FROM application a 
                JOIN project p ON a.PID = p.PID
                LEFT JOIN faculty f ON p.FID = f.FID
                LEFT JOIN project_skills ps ON p.PID = ps.PID
                WHERE a.SID = ?
                ORDER BY CASE WHEN a.status = 'Not Selected for Project' THEN 1 ELSE 0 END, p.created_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $sid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Determine the class based on status
                $statusClass = 'application-item';
                if ($row['status'] == 'Not Selected for Project') {
                    $statusClass .= ' not-selected';
                } elseif ($row['status'] == 'Accepted') {
                    $statusClass .= ' accepted';
                } elseif ($row['status'] == 'Under Review') {
                    $statusClass .= ' under-review';
                } elseif ($row['status'] == 'Pending') {
                    $statusClass .= ' pending';
                }

                echo "<div class='$statusClass'>";
                echo "<h3>" . htmlspecialchars($row['projectName']) . "</h3>";
                $datePosted = date("F j, Y", strtotime($row["created_at"]));
                echo "<div class='project-date'>Posted on: " . $datePosted . "</div>";
                echo "<button class='collapsible'>View Details</button>";
                echo "<div class='content'>";
                echo "<p><strong>Application ID:</strong> " . htmlspecialchars($row['AID']) . "</p>";
                echo "<p><strong>Status:</strong> " . htmlspecialchars($row['status']) . "</p>";
                echo "<p><strong>Faculty Member:</strong> " . htmlspecialchars($row['firstName'] . " " . $row['lastName']) . "</p>";
                echo "<p><strong>Description:</strong> " . htmlspecialchars($row['ProjDesc']) . "</p>";

                // Display attachment if available under details
                if (!empty($row["attachment_path"])) {
                    echo "<p><strong>Attachment:</strong> <a href='" . htmlspecialchars($row["attachment_path"]) . "' target='_blank'>View Attachment</a></p>";
                }

                // Required Skills from project_skills table (JSON structure)
                $skills = json_decode($row['skills'], true);
                if ($skills) {
                    foreach ($skills as $category => $skillList) {
                        echo "<button class='collapsible-skill'>" . ucfirst($category) . "</button>";
                        echo "<div class='skill-content'>";
                        foreach ($skillList as $skill => $level) {
                            echo "<div class='skill-item'>" . htmlspecialchars($skill) . "</div>";
                        }
                        echo "</div>";
                    }
                } else {
                    echo "<p><strong>Required Skills:</strong> None</p>";
                }

                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>You have not applied to any projects yet.</p>";
        }
        ?>


    <?php endif; ?>
</main>

<footer>
    <p>&copy; 2024 Student Matchmaking System</p>
</footer>

<script>
    var coll = document.getElementsByClassName("collapsible");
    for (var i = 0; i < coll.length; i++) {
        coll[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var content = this.nextElementSibling;
            content.style.display = content.style.display === "block" ? "none" : "block";
        });
    }

    var skillCollapsibles = document.getElementsByClassName("collapsible-skill");
    for (var j = 0; j < skillCollapsibles.length; j++) {
        skillCollapsibles[j].addEventListener("click", function() {
            this.classList.toggle("active");
            var skillContent = this.nextElementSibling;
            skillContent.style.display = skillContent.style.display === "block" ? "none" : "block";
        });
    }
</script>

</body>
</html>
