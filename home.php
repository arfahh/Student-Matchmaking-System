<?php
// Include database connection
include('db.php');
include('header.php');

// Grab any filter parameters (if provided)
$filterDate = isset($_GET['filter_date']) ? trim($_GET['filter_date']) : '';
$filterFaculty = isset($_GET['filter_faculty']) ? trim($_GET['filter_faculty']) : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Match Making System</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        header, footer {
            background: linear-gradient(90deg, #0058ab 0%, #002279 50%, #0058ab 100%);
            color: white;
            text-align: center;
        }

        /* Flexbox layout for the main content */
        .main-content {
            display: flex;
            height: 100vh;
            max-width: 1400px; /* Adjust this value to make it wider */
            margin: 0 auto; /* Centers the content */
        }

        /* Left half: Image container */
        .left-half {
            flex: 0 0 60%; /* Sets the left column width to 50% */
            background-image: url('home.phppic.jpg'); /* Replace with your image path */
            background-size: cover;
            background-position: center;
            background-color: #031328;
        }

        /* Right half: New Projects container */
        .right-half {
            flex: 0 0 40%; /* Sets the right column width to 50% */
            padding: 2rem;
            background-color: #031328;
        }

        /* Container for the new projects list with scrolling */
        .new-projects {
            width: 100%;
            max-height: 500px; /* You can adjust this height to fit your design */
            overflow-y: auto; /* Enable vertical scrolling */
            margin-top: 20px;
        }

        /* Ensure individual project items have a consistent look */
        .project-item {
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
            background-color: #f1f1f1;
        }

        .project-item h4 {
            margin-bottom: 10px;
        }

        .project-item p {
            margin-bottom: 5px;
        }

        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border: 2px solid #ccc;
            z-index: 1000;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
        }

        .popup-content {
            max-height: 300px;
            overflow-y: auto;
        }

        .popup .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 18px;
            cursor: pointer;
        }

        /* Additional styling for project description */
        .project-description {
            height: 60px;
            overflow: hidden;
        }

        .project-description.expand {
            height: auto;
        }
    </style>

    <script>
        function toggleDescription(element) {
            const description = element.querySelector('.project-description');
            description.classList.toggle('expand');
        }

        function showPopup(projectId) {
            const popup = document.getElementById('popup-' + projectId);
            popup.style.display = 'block';
        }

        function closePopup(projectId) {
            const popup = document.getElementById('popup-' + projectId);
            popup.style.display = 'none';
        }
    </script>
</head>

<body>

    <main class="main-content">
        <!-- Left half: Image Section -->
        <div class="left-half">
            <!-- This area is for your background image -->
        </div>

        <!-- Right half: New Projects Section -->
        <div class="right-half">
            <h2>New Projects</h2>

            <!-- Scrollable container for the new projects -->
            <div class="new-projects">
                <?php
                // Fetch new projects from the last week
                $sql = "SELECT * FROM project WHERE created_at >= CURDATE() - INTERVAL 7 DAY ORDER BY created_at DESC";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $projectId = $row["PID"];
                        $projectName = $row["projectName"];
                        $description = $row["ProjDesc"];
                        $facultyName = $row["FID"]; // Assuming you have faculty details available
                        $skills = $row["ProjSkills"];

                        echo "<div class='project-item'>";
                        echo "<h4>" . htmlspecialchars($projectName) . "</h4>";
                        echo "<p><strong>Faculty ID:</strong> " . $facultyName . "</p>";
                        echo "<div class='project-description' onclick='toggleDescription(this)'>" . htmlspecialchars($description) . "</div>";
                        echo "<button onclick='showPopup($projectId)'>View Details</button>";
                        echo "</div>";

                        // Popup for project details
                        echo "<div id='popup-$projectId' class='popup'>
                                <span class='close' onclick='closePopup($projectId)'>X</span>
                                <div class='popup-content'>
                                    <h3>" . htmlspecialchars($projectName) . "</h3>
                                    <p><strong>Faculty ID:</strong> " . htmlspecialchars($facultyName) . "</p>
                                    <p><strong>Description:</strong> " . nl2br(htmlspecialchars($description)) . "</p>
                                    <p><strong>Skills:</strong> " . htmlspecialchars($skills) . "</p>
                                    <p><strong>Start Date:</strong> " . htmlspecialchars($row['projstartdate']) . "</p>
                                    <p><strong>End Date:</strong> " . htmlspecialchars($row['projenddate']) . "</p>
                                    <p><strong>Application Deadline:</strong> " . htmlspecialchars($row['application_deadline']) . "</p>
                                </div>
                              </div>";
                    }
                } else {
                    echo "<p>No new projects available.</p>";
                }
                ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Student Matchmaking System</p>
    </footer>

</body>

</html>

<?php
$conn->close();
?>
