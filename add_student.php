<?php
// Start session
session_start();

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include database connection and header
include('db.php');
include('headeradmin.php');

// Ensure the admin is accessing (adid=0)
if (!isset($_GET['adid']) || $_GET['adid'] !== '0') {
    die("Access denied. You must be an admin (adid=0) to view this page.");
}

// Handle form submission for adding a new student
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect required fields
    $SID = $_POST['SID'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $program = $_POST['program'];
    $username = $_POST['username'];
    $password = $_POST['password']; // Consider hashing this for security
    $email = $_POST['email'];
    $year = $_POST['year'];
    $password = $_POST['password'];

   

    // Use NULL for optional fields if left blank
    $resume_path = isset($_POST['resume_path']) && $_POST['resume_path'] !== '' ? $_POST['resume_path'] : NULL;
    $linkedin_link = isset($_POST['linkedin_link']) && $_POST['linkedin_link'] !== '' ? $_POST['linkedin_link'] : NULL;
    $github_link = isset($_POST['github_link']) && $_POST['github_link'] !== '' ? $_POST['github_link'] : NULL;
    $website_link = isset($_POST['website_link']) && $_POST['website_link'] !== '' ? $_POST['website_link'] : NULL;
    $other_links = isset($_POST['other_links']) && $_POST['other_links'] !== '' ? $_POST['other_links'] : NULL;

    // Proceed with inserting the new student directly without checking SID
    $insert_sql = "INSERT INTO students (SID, firstName, lastName, program, username, password, email, year, resume_path, linkedin_link, github_link, website_link, other_links)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($insert_sql);
    $stmt_insert->bind_param("sssssssssssss", 
        $SID, $firstName, $lastName, $program, $username, $password, $email, $year, 
        $resume_path, $linkedin_link, $github_link, $website_link, $other_links
    );

    // Check if the insert was successful
    if ($stmt_insert->execute()) {
        $insertSuccess = true;
    } else {
        echo "Error adding student: " . $conn->error;
    }

    // Close the insert statement to free the connection
    $stmt_insert->close();
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <style>
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

        label {
            color: white;
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .required::after {
            content: " *";
            color: red;
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
        <?php if (isset($insertSuccess) && $insertSuccess): ?>
            window.onload = function() {
                var successMessage = document.getElementById('successMessage');
                successMessage.style.display = 'block';

                setTimeout(function() {
                    successMessage.style.display = 'none';
                    window.location.href = 'manage_students.php?adid=0';
                }, 800);
            };
        <?php endif; ?>
    </script>
</head>
<body>
    <h1>Add Student</h1>

    <div id="successMessage" class="success-message">
        Student added successfully!
    </div>
     <a href="manage_faculty.php?adid=0" class="back-btn">Back to Manage Faculty</a>
    <form action="add_student.php?adid=0" method="POST">
        <div class="form-group">
            <div>
                <label for="SID" class="required">Student ID (SID):</label>
                <input type="text" id="SID" name="SID" required>
            </div>
            <div>
                <label for="firstName" class="required">First Name:</label>
                <input type="text" id="firstName" name="firstName" required>
            </div>
        </div>

        <div class="form-group">
            <div>
                <label for="lastName" class="required">Last Name:</label>
                <input type="text" id="lastName" name="lastName" required>
            </div>
            <div>
                <label for="program" class="required">Program:</label>
                <input type="text" id="program" name="program" required><br>
            </div>
        </div>

        <label for="username" class="required">Username:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password" class="required">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <label for="email" class="required">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="year" class="required">Year:</label>
        <select id="year" name="year" required>
            <option value="Undergraduate">Undergraduate</option>
            <option value="Graduate">Graduate</option>
        </select><br>

        <label for="resume_path">Resume Path:</label>
        <input type="text" id="resume_path" name="resume_path"><br>

        <label for="linkedin_link">LinkedIn Link:</label>
        <input type="text" id="linkedin_link" name="linkedin_link"><br>

        <label for="github_link">GitHub Link:</label>
        <input type="text" id="github_link" name="github_link"><br>

        <label for="website_link">Website Link:</label>
        <input type="text" id="website_link" name="website_link"><br>

        <label for="other_links">Other Links:</label>
        <input type="text" id="other_links" name="other_links"><br>

        <input type="submit" value="Add Student">
    </form>

    
</body>
</html>
