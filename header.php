<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>

<header>
    <h1>Student Matchmaking System</h1>

    <!-- User info in the top-right corner -->
    <div class="user-info">
        <?php  
        // Include the database connection
        include('db.php');

        // Get parameters from URL
        $sid = isset($_GET['sid']) ? htmlspecialchars(trim($_GET['sid'])) : null;
        $fid = isset($_GET['fid']) ? htmlspecialchars(trim($_GET['fid'])) : null;
        $adid = isset($_GET['adid']) ? htmlspecialchars(trim($_GET['adid'])) : null;

        // Check if adid is provided and show the admin's details
        if ($adid) {
            // Admin info (e.g., showing admin name or handling admin-related pages)
            echo "<strong>Admin</strong> 🧑‍💻";
            echo '<a href="logout.php">Logout</a>';
        }
        // If adid is not set, check for student or faculty profile
        else {
            // Check if SID is provided (Student Profile)
            if ($sid) {
                // Prepare SQL query to fetch student details
                $sql_student = "SELECT sid, firstName, lastName, program, email, year FROM students WHERE sid = ?";
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

                // Display student's name in top-right corner
                $displayName = htmlspecialchars($student['lastName'] . ", " . $student['firstName']);
                echo "<strong>$displayName</strong> 🎓";
                echo '<a href="logout.php">Logout</a>';
            }
            // Check if FID is provided (Faculty Profile)
            else if ($fid) {
                // Prepare SQL query to fetch faculty details
                $sql_faculty = "SELECT fid, firstName, lastName FROM faculty WHERE fid = ?";
                $stmt_faculty = $conn->prepare($sql_faculty);
                if (!$stmt_faculty) {
                    die("Query preparation failed: " . $conn->error);
                }
                $stmt_faculty->bind_param("s", $fid);
                $stmt_faculty->execute();
                $result_faculty = $stmt_faculty->get_result();

                // Check if the faculty exists
                if ($result_faculty->num_rows > 0) {
                    $faculty = $result_faculty->fetch_assoc();
                } else {
                    die("Faculty not found.");
                }
                $stmt_faculty->close();

                // Display faculty's name in top-right corner
                $displayName = htmlspecialchars($faculty['lastName'] . ", " . $faculty['firstName']);
                echo "<strong>$displayName</strong> 👨🏻‍🏫";
                echo '<a href="logout.php">Logout</a>';
            }
        }
        ?>
    </div>

    <nav>
        <ul class="nav-links">
            <?php
            // Show admin-related links if adid is set
            if($adid){
                echo '<li><a href="manage_students.php?adid=0">Manage Students</a></li>';
                echo '<li><a href="manage_faculty.php?adid=0">Manage Faculty</a></li>';
                echo '<li><a href="manage_applications.php?adid=0">Manage Applications</a></li>';
                echo '<li><a href="manage_skills.php?adid=0">Manage Skills</a></li>';
                echo '<li><a href="../logout.php">Logout</a></li>';
            }
            // Show student-related links if SID is set
            else if ($sid) {
                echo '<li><a href="home.php?sid=' . urlencode($sid) . '">Home</a></li>';
                echo '<li><a href="studentprojectpage.php?sid=' . urlencode($sid) . '">Projects</a></li>';
                echo '<li><a href="studentpage.php?sid=' . urlencode($sid) . '">My Profile</a></li>';
                echo '<li><a href="myapplications.php?sid=' . urlencode($sid) . '">My Applications</a></li>';
                echo '<li><a href="../logout.php">Logout</a></li>';
            }
            // Show faculty-related links if FID is set
            else if ($fid) {
                echo '<li><a href="home.php?fid=' . urlencode($fid) . '">Home</a></li>';
                echo '<li><a href="facultyprojectpage.php?fid=' . urlencode($fid) . '">Faculty Projects</a></li>';
                echo '<li><a href="facultypage.php?fid=' . urlencode($fid) . '">My Profile</a></li>';
                echo '<li><a href="faculty_applications.php?fid=' . urlencode($fid) . '">Faculty Applications</a></li>';
            }
            // If no SID or FID is set, show login link
            else{
                echo '<li><a href="faculty_student.php">Login</a></li>';
            }
            ?>
        </ul>
    </nav>
</header>


</body>
</html>
