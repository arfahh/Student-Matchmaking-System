<?php
// Start session
session_start();

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include database connection
include('db.php');
include('headeradmin.php');

// Ensure the admin is accessing (adid=0)
if (!isset($_GET['adid']) || $_GET['adid'] !== '0') {
    die("Access denied. You must be an admin (adid=0) to view this page.");
}

// Ensure Student ID (sid) is provided
if (!isset($_GET['sid'])) {
    die("Student ID not provided.");
}

$sid = trim($_GET['sid']); // Remove spaces


// Fetch the student's current details
$sql = "SELECT sid, firstName, lastName, program, username, password, email, year, resume_path, linkedin_link, github_link, website_link, other_links FROM students WHERE sid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $sid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $students = $result->fetch_assoc();
} else {
    die("Student not found.");
}



// Handle form submission for updating student data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect required fields
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $program = $_POST['program'];
    $username = $_POST['username'];
    $password = $_POST['password']; // Consider hashing this for security
    $email = $_POST['email'];
    $year = $_POST['year'];

    // Use NULL for optional fields if left blank
    $resume_path = isset($_POST['resume_path']) && $_POST['resume_path'] !== '' ? $_POST['resume_path'] : NULL;
    $linkedin_link = isset($_POST['linkedin_link']) && $_POST['linkedin_link'] !== '' ? $_POST['linkedin_link'] : NULL;
    $github_link = isset($_POST['github_link']) && $_POST['github_link'] !== '' ? $_POST['github_link'] : NULL;
    $website_link = isset($_POST['website_link']) && $_POST['website_link'] !== '' ? $_POST['website_link'] : NULL;
    $other_links = isset($_POST['other_links']) && $_POST['other_links'] !== '' ? $_POST['other_links'] : NULL;

    // Prepare and execute the update query
    $update_sql = "UPDATE students 
                   SET firstName = ?, lastName = ?, program = ?, username = ?, password = ?, email = ?, year = ?, 
                       resume_path = ?, linkedin_link = ?, github_link = ?, website_link = ?, other_links = ? 
                   WHERE sid = ?";
    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bind_param("sssssssssssss", 
        $firstName, $lastName, $program, $username, $password, $email, $year, 
        $resume_path, $linkedin_link, $github_link, $website_link, $other_links, $sid
    );

    // Check if the update was successful
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
    <title>Edit Student</title>
    <style>

         header, footer {
             background: linear-gradient(90deg, #0058ab 0%, #002279 50%, #0058ab 100%);
            color: white;

            text-align: center;

        }
        /* Style for the success message */
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
        body {
            background-color: #2c3e50; /* Dark background */
            color: white;
            font-family: Arial, sans-serif;
        }

        form {
            max-width: 500px;
            margin: auto; /* Center the form */
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }

        /* Flexbox for grouping related inputs */
        .form-group {
            display: flex;
            gap: 15px; /* Spacing between fields */
            margin-bottom: 15px;
        }

        /* Ensures input boxes share equal width */
        .form-group input {
            flex: 1;
        }

        label {
            color: white;
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid white;
            border-radius: 5px;
            font-size: 16px;
        }

        /* Style for all input and select elements */
        input[type="text"],
        input[type="password"],
        input[type="email"],
        select {
            width: 100%; /* Full width to match other input boxes */
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid white;
            border-radius: 5px;
            font-size: 16px;
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

        input[type="submit"]:hover {
            background-color: #2980b9;
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
                    window.location.href = 'manage_students.php?adid=0';
                }, 800); // Fixed to 3 seconds
            };
        <?php endif; ?>
    </script>
</head>
<body>
    <h1>Edit Student</h1>
     <a href="manage_faculty.php?adid=0" class="back-btn">Back to Manage Faculty</a>
    <!-- Success message pop-up -->
    <div id="successMessage" class="success-message">
        Student updated successfully!
    </div>
    <form action="edit_student.php?adid=0&sid=<?php echo htmlspecialchars($students['sid']); ?>" method="POST">
    <!-- First Name & Last Name in One Row -->
    <div class="form-group">
        <div>
            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($students['firstName']); ?>" required>
        </div>
        <div>
            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($students['lastName']); ?>" required>
        </div>
    </div>

    <label for="program">Program:</label>
    <input type="text" id="program" name="program" value="<?php echo htmlspecialchars($students['program']); ?>" required><br>

    <label for="username">Username:</label>
    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($students['username']); ?>" required><br>

    <label for="password">Password:</label>
    <input type="text" id="password" name="password" value="<?php echo htmlspecialchars($students['password']); ?>" required><br>

    <label for="email">Email:</label>
    <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($students['email']); ?>" required><br>

    <label for="year">Year:</label>
        <select id="year" name="year" required>
            <option value="Undergraduate" <?php if ($students['year'] == 'Undergraduate') echo 'selected'; ?>>Undergraduate</option>
            <option value="Graduate" <?php if ($students['year'] == 'Graduate') echo 'selected'; ?>>Graduate</option>
        </select>
        <br>


    <!-- Optional Links -->
    <label for="resume_path">Resume Path:</label>
    <input type="text" id="resume_path" name="resume_path" value="<?php echo htmlspecialchars($students['resume_path']); ?>"><br>

    <label for="linkedin_link">LinkedIn Link:</label>
    <input type="text" id="linkedin_link" name="linkedin_link" value="<?php echo htmlspecialchars($students['linkedin_link'] ?? ''); ?>"><br>


    <label for="github_link">GitHub Link:</label>
    <input type="text" id="github_link" name="github_link" value="<?php echo htmlspecialchars($students['github_link'] ?? ''); ?>"><br>

    <label for="website_link">Website Link:</label>
    <input type="text" id="website_link" name="website_link" value="<?php echo htmlspecialchars($students['website_link'] ?? ''); ?>"><br>

    <label for="other_links">Other Links:</label>
    <input type="text" id="other_links" name="other_links" value="<?php echo htmlspecialchars($students['other_links'] ?? ''); ?>"><br>

    <input type="submit" value="Update Student">
</form>

    

    
    
    
</body>
</html>
