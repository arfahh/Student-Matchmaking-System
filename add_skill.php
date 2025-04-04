<?php
// Start session
session_start();

// Include database connection
include('db.php');
include('headeradmin.php');

// Ensure admin access by checking 'adid' in the URL
if (!isset($_GET['adid']) || $_GET['adid'] != '0') {
    die("Access denied. You must be an admin (adid=0) to view this page.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $skill_name = $_POST['skill_name'];
    $category = $_POST['category'];

    // Prepare the SQL query to insert new skill (without skill_type)
    $insert_sql = "INSERT INTO skills_list (skill_name, category) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($insert_sql);
    $stmt_insert->bind_param("ss", $skill_name, $category);

    // Execute the query and check for errors
    if ($stmt_insert->execute()) {
        // Redirect to manage_skills.php after successful insertion
        header("Location: manage_skills.php?adid=0");
        exit();
    } else {
        echo "Error adding skill: " . $conn->error;
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
    <title>Add Skill</title>
    <link rel="stylesheet" href="styles.css">
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
    <h1>Add New Skill</h1>
    <a href="manage_faculty.php?adid=0" class="back-btn">Back to Manage Faculty</a>

    <form action="add_skill.php?adid=0" method="POST">
        <label for="skill_name">Skill Name:</label>
        <input type="text" id="skill_name" name="skill_name" required><br><br>

        <label for="category">Category:</label>
        <select id="category" name="category" required>
            <option value="">Select Category</option>
            <option value="languages">Language</option>
            <option value="tools">Tool</option>
            <option value="frameworks">Framework</option>
            <!-- Add more options as necessary -->
        </select><br><br>

        <input type="submit" value="Add Skill">
    </form>

    
</body>
</html>

