<?php
// Start session
session_start();

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include database connection
include('db.php');
include('headeradmin.php');

// Ensure fid is passed in the URL and equals 0
if (!isset($_GET['adid']) || $_GET['adid'] != '0') {
    die("Access denied. You must be an admin (adid=0) to view this page.");
}

// Ensure application ID is passed in the URL
if (!isset($_GET['aid'])) {
    die("Application ID not provided.");
}

$aid= $_GET['aid'];

// Fetch the applications's current details from the database
$sql = "SELECT aid, sid, pid, status, score FROM application WHERE aid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $aid);  // Use "i" here for integer parameter
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $application = $result->fetch_assoc();
} else {
    die("Application not found.");
}

// Update student data if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sid = $_POST['sid'];
    $pid = $_POST['pid'];
    $status = $_POST['status'];
    $score = $_POST['score'];

    // Prepare and execute the update query
    $update_sql = "UPDATE application SET sid = ?, pid = ?, status = ?, score = ? WHERE aid = ?";
    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bind_param("ssssi", $sid, $pid, $status, $score, $aid);  // Changed to "ssssi" to match string types

    // Check if the query executes successfully
    if ($stmt_update->execute()) {
        $updateSuccess = true;
    } else {
        echo "Error updating student: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Application</title>
    <style>
        /* Style for the success message */
         <style>
        /* Style for the success message */
        header, footer {
            background: linear-gradient(90deg, #0058ab 0%, #002279 50%, #0058ab 100%);
            color: white;
            text-align: center;
        }
        .success-message {
            display: none;
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 18px;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            border-radius: 5px;
            width: 300px;
        }
        h1 {
            color: #ffffff; /* Change this to any color you want */
            text-align: center; /* Centers the text */
            font-size: 24px; /* Adjust the size */
            font-weight: bold; /* Makes it bold */
        }
        body {
            background-color: #2c3e50;
            color: white;
            font-family: Arial, sans-serif;
        }
         form {
            max-width: 500px;
            margin: auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }

        .form-group {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group input {
            flex: 1;
        }
         input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid white;
            border-radius: 5px;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #2980b9;
        }
        label {
            color: white;
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .success-message {
            display: none;
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 18px;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            border-radius: 5px;
            width: 300px;
        }
          .back-btn {
            display: inline-block;
            background-color: #0058ab; /* Button color */
            color: white; /* Text color */
            padding: 10px 20px; /* Size */
            border-radius: 25px; /* Makes it rounded */
            text-decoration: none; /* Removes underline */
            font-size: 16px;
            font-weight: bold;
            transition: 0.3s;
        }
    </style>
    </style>
    <script type="text/javascript">
        <?php if (isset($updateSuccess) && $updateSuccess): ?>
            // Show the success message
            window.onload = function() {
                var successMessage = document.getElementById('successMessage');
                successMessage.style.display = 'block';

                // Hide the success message after 3 seconds (3000ms)
                setTimeout(function() {
                    successMessage.style.display = 'none';
                    // Redirect to manage_students.php after the success message is hidden
                    window.location.href = 'manage_applications.php?adid=0';
                }, 300); // Change the delay to 3 seconds
            };
        <?php endif; ?>
    </script>
</head>
<body>
    <h1>Edit Application</h1>

    <!-- Success message pop-up -->
    <div id="successMessage" class="success-message">
        Student updated successfully!
    </div>
    <a href="manage_faculty.php?adid=0" class="back-btn">Back to Manage Faculty</a>

    <form action="edit_applications.php?adid=0&aid=<?php echo $application['aid']; ?>" method="POST">
        <label for="sid">SID:</label>
        <input type="text" id="sid" name="sid" value="<?php echo htmlspecialchars($application['sid']); ?>" required><br><br>

        <label for="pid">PID:</label>
        <input type="text" id="pid" name="pid" value="<?php echo htmlspecialchars($application['pid']); ?>" required><br><br>

        <label for="status">Status:</label>
        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="Not selected for project" <?php if ($application['status'] == "Not selected for project") echo "selected"; ?>>Not selected for project</option>
            <option value="Pending" <?php if ($application['status'] == "Pending") echo "selected"; ?>>Pending</option>
            <option value="Selected for project" <?php if ($application['status'] == "Selected for project") echo "selected"; ?>>Selected for project</option>
        </select>
        <br><br>

        <label for="score">Score:</label>
        <input type="text" id="score" name="score" value="<?php echo htmlspecialchars($application['score']); ?>" required><br><br>

        <input type="submit" value="Update Application">
    </form>
    <a href="manage_applications.php?adid=0">Back to Manage Applications</a>
</body>
</html>
