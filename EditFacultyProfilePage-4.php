<?php
// Include the database connection
include('db.php');
include('header.php');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get Faculty ID (from URL)
$fid = isset($_GET['fid']) ? htmlspecialchars(trim($_GET['fid'])) : null;

if (!$fid) {
    die("No Faculty ID provided.");
}

// Prepare SQL statement to fetch faculty details
$sql = "SELECT fid, firstName, lastName, facultyName FROM faculty WHERE fid = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Statement preparation failed: " . $conn->error);
}

$stmt->bind_param("s", $fid);
$stmt->execute();
$result = $stmt->get_result();

// Check if faculty exists
if ($result->num_rows > 0) {
    $faculty = $result->fetch_assoc();
} else {
    die("Faculty not found.");
}

// Close statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Faculty Profile - Project Matchmaking System</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .profile-input {
            font-size: 16px;
            font-weight: bold;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            background-color: #f9f9f9;
        }
        header, footer {
            background-color: #00509e;
            color: white;
           
            text-align: center;
            
        }

       header, footer {
             background: linear-gradient(90deg, #0058ab 0%, #002279 50%, #0058ab 100%);
            color: white;

            text-align: center;

        }
    </style>
</head>
<body>

    <main>
        <div class="container">
            <h2>Edit Faculty Profile</h2>

            <form action="update_faculty_profile.php" method="POST">
                <label for="fid">Faculty ID:</label>
                <input type="text" id="fid" name="fid" value="<?php echo htmlspecialchars($faculty['fid']); ?>" readonly class="profile-input">

                <label for="firstName">First Name:</label>
                <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($faculty['firstName']); ?>" required class="profile-input">

                <label for="lastName">Last Name:</label>
                <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($faculty['lastName']); ?>" required class="profile-input">

                <label for="facultyName">Program:</label>
                <input type="text" id="facultyName" name="facultyName" value="<?php echo htmlspecialchars($faculty['facultyName']); ?>" required class="profile-input">

                <button type="submit" class="save-button">Save Changes</button>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Faculty Matchmaking System</p>
    </footer>
</body>
</html>
