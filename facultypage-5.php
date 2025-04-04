<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include('db.php');
include('header.php');

// Get the faculty ID from the URL
$fid = isset($_GET['fid']) ? htmlspecialchars(trim($_GET['fid'])) : null;

// Check if FID is provided
if (!$fid) {
    die("No Faculty ID provided.");
}

// Prepare SQL query to fetch faculty details
$sql_faculty = "SELECT fid, firstName, lastName, facultyName FROM faculty WHERE fid = ?";
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Profile</title>
    <link rel="stylesheet" href="styles.css">
    <style>

        header, footer {
             background: linear-gradient(90deg, #0058ab 0%, #002279 50%, #0058ab 100%);
            color: white;

            text-align: center;

        }

        .edit-profile {
                background-color: #007bff;
                color: white;
                padding: 8px 10px; /* Reduced horizontal padding from 12px to 10px */
                text-decoration: none;
                border-radius: 5px;
                border: none;
                font-size: 14px;
                margin: 5px;
            }


    </style>
</head>
<body>




    <main>
        <section id="profile-section">
            <h2>Faculty Profile</h2>
            <div class="profile-details">
                <p><strong>FID:</strong> <?php echo htmlspecialchars($faculty['fid']); ?></p>
                <p><strong>First Name:</strong> <?php echo htmlspecialchars($faculty['firstName']); ?></p>
                <p><strong>Last Name:</strong> <?php echo htmlspecialchars($faculty['lastName']); ?></p>
                <p><strong>Faculty:</strong> <?php echo htmlspecialchars($faculty['facultyName']); ?></p>
            </div>
            <div class="edit-profile">
                <a href="EditFacultyProfilePage.php?fid=<?php echo $faculty['fid']; ?>" class="edit-button">Edit Profile</a>
            </div>

        </section>
    </main>

 
</body>
  <footer>
        <p>&copy; 2024 Student Matchmaking System</p>
    </footer>
</html>

<?php
$conn->close();
?>
