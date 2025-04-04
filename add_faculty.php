<?php
// Start session
session_start();

// Include database connection
include('db.php');
include('headeradmin.php');

// Ensure admin access
if (!isset($_GET['adid']) || $_GET['adid'] != '0') {
    die("Access denied. You must be an admin (adid=0) to view this page.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $facultyName = $_POST['facultyName'];
    $max_projects = $_POST['max_projects'];

    // Prepare the SQL query to insert new faculty
    $insert_sql = "INSERT INTO faculty (firstName, lastName, facultyName, max_projects) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($insert_sql);
    $stmt_insert->bind_param("sssi", $firstName, $lastName, $facultyName, $max_projects);

    // Execute the query and check for errors
    if ($stmt_insert->execute()) {
        echo "Faculty member added successfully!";
        // Redirect to manage faculty page
        header("Location: manage_faculty.php?adid=0");
        exit();
    } else {
        echo "Error adding faculty member: " . $conn->error;
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
    <title>Add Faculty Member</title>
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
</head>
<body>
    <h1>Add New Faculty Member</h1>
    <a href="manage_faculty.php?adid=0" class="back-btn">Back to Manage Faculty</a>

    <form action="add_faculty.php?adid=0" method="POST">
        <label for="firstName">First Name:</label>
        <input type="text" id="firstName" name="firstName" required><br><br>

        <label for="lastName">Last Name:</label>
        <input type="text" id="lastName" name="lastName" required><br><br>

        <label for="facultyName">Faculty Name:</label>
        <input type="text" id="facultyName" name="facultyName" required><br><br>

        <label for="max_projects">Max Number of Projects:</label>
        <input type="text" id="max_projects" name="max_projects" required><br><br>

        <input type="submit" value="Add Faculty">
    </form>

    
</body>
</html>
