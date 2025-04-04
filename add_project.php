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
    $PID = $_POST['PID'];
    $projectName = $_POST['projectName'];
    $FID = $_POST['FID'];
    $ProjDesc = $_POST['ProjDesc'];
    $ProjSkills = $_POST['ProjSkills'];
   
    // Use NULL for optional fields if left blank
    $ProjProgram = isset($_POST['ProjProgram']) && $_POST['ProjProgram'] !== '' ? $_POST['ProjProgram'] : NULL;
    $SID = isset($_POST['SID']) && $_POST['SID'] !== '' ? $_POST['SID'] : NULL;
    $projstartdate = isset($_POST['projstartdate']) && $_POST['projstartdate'] !== '' ? $_POST['projstartdate'] : NULL;
    $projenddate = isset($_POST['projenddate']) && $_POST['projenddate'] !== '' ? $_POST['projenddate'] : NULL;
    $application_deadline = isset($_POST['application_deadline']) && $_POST['application_deadline'] !== '' ? $_POST['application_deadline'] : '2099-12-31';
    $attachment_path = isset($_POST['attachment_path']) && $_POST['attachment_path'] !== '' ? $_POST['attachment_path'] : NULL;
    


     
    
    
    $insert_sql = "INSERT INTO project (projectName, FID, ProjDesc, ProjSkills, ProjProgram, SID, projstartdate, projenddate, application_deadline, attachment_path, created_at, archived) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 0)";
    $stmt_insert = $conn->prepare($insert_sql);
    $stmt_insert->bind_param("sisssissss", $projectName, $FID, $ProjDesc, $ProjSkills, $ProjProgram, $SID, $projstartdate, $projenddate, $application_deadline, $attachment_path);



   // Execute the query and check for errors
    if ($stmt_insert->execute()) {
        echo "Project member added successfully!";
        // Redirect to manage project page
        header("Location: manage_projects.php?adid=0");
        exit();
    } else {
        echo "Error adding project member: " . $conn->error;
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
    <title>Add Project</title>
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
                    window.location.href = 'manage_projects.php?adid=0';
                }, 800);
            };
        <?php endif; ?>
    </script>
</head>
<body>
    <h1>Add Project</h1>

    <div id="successMessage" class="success-message">
        Student added successfully!
    </div>
     <a href="manage_projects.php?adid=0" class="back-btn">Back to Manage Project</a>
    <form action="add_project.php?adid=0" method="POST">
        <div class="form-group">
            <div>
                <label for="projectName" class="required">Project Name:</label>
                <input type="text" id="projectName" name="projectName" required>
            </div>
           <div>
                <label for="FID" class="required">Faculty ID:</label>
                <input type="text" id="FID" name="FID" required><br>
            </div>

        </div>

        <label for="ProjDesc" class="required">Project Description:</label>
                <input type="text" id="ProjDesc" name="ProjDesc" required><br>


        <div class="form-group">
            <div>
                <label for="ProjSkills" class="required">Project Skills:</label>
                <input type="text" id="ProjSkills" name="ProjSkills" required><br>
            </div>
            <div>
                <label for="ProjProgram" class ="required">Project Program:</label>
                <select id="ProjProgram" name="ProjProgram" required>
                    <option value="Undergraduate">Undergraduate</option>
                    <option value="Graduate">Graduate</option>
                </select><br>
            </div>
        </div>

        <label for="SID" >Student ID:</label>
        <input type="text" id="SID" name="SID" ><br>

        <div class="form-group">
            <div>
                <label for="projstartdate" >Project Start Date:</label>
                <input type="date" id="projstartdate" name="projstartdate">
            </div>
            <div>
                <label for="projenddate" >Project End Date:</label>
                <input type="date" id="projenddate" name="projenddate" >
            </div>
        </div>

        <label for="application_deadline">Application Deadline:</label>
        <input type="date" id="application_deadline" name="application_deadline"><br>

       


        <input type="submit" value="Add Project">
    </form>

    
</body>
</html>
