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

if (!isset($_GET['pid'])) {
    die("Project ID not provided.");
}

$pid = trim($_GET['pid']); // Remove spaces



$sql = "SELECT pid, projectName, FID, ProjDesc, ProjSkills, ProjProgram, SID, projstartdate, projenddate, application_deadline, attachment_path, created_at, archived  FROM project WHERE pid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $project = $result->fetch_assoc();
} else {
    die("Project not found.");
}



// Handle form submission for updating student data
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
    $archived = isset($_POST['archived']) && $_POST['archived'] !== '' ? $_POST['archived'] : 0 ;

    // Prepare and execute the update query
  $update_sql = "UPDATE project 
               SET projectName = ?, FID = ?, ProjDesc = ?, ProjSkills = ?, ProjProgram = ?, SID = ?, projstartdate = ?, 
                   projenddate = ?, application_deadline = ?, attachment_path = ?, archived = ? 
               WHERE pid = ?";
$stmt_update = $conn->prepare($update_sql);
$stmt_update->bind_param("sisssissssii", 
    $projectName, $FID, $ProjDesc, $ProjSkills, $ProjProgram, $SID, $projstartdate, 
    $projenddate, $application_deadline, $attachment_path, $archived, $PID
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
    <title>Edit Project</title>
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

                // Hide the success message after 3 seconds (8000ms)
                setTimeout(function() {
                    successMessage.style.display = 'none';
                    // Redirect to manage_projects.php after the success message is hidden
                    window.location.href = 'manage_projects.php?adid=0';
                }, 800); 
            };
        <?php endif; ?>
    </script>
</head>
<body>
    <h1>Edit Student</h1>
     <a href="manage_projects.php?adid=0" class="back-btn">Back to Manage Projects</a>
         
    <!-- Success message pop-up -->
    <div id="successMessage" class="success-message">
        Project updated successfully!
    </div>
    <form action="edit_project_admin.php?adid=0&pid=<?php echo htmlspecialchars($project['pid']); ?>" method="POST">
   

    <input type="hidden" name="PID" value="<?php echo htmlspecialchars($project['pid']); ?>">
 
    <div class="form-group">
        <div>
            <label for="projectName">Project Name:</label>
            <input type="text" id="projectName" name="projectName" value="<?php echo htmlspecialchars($project['projectName']); ?>" required>
        </div>
        <div>
            <label for="FID">Faculty ID:</label>
            <input type="text" id="FID" name="FID" value="<?php echo htmlspecialchars($project['FID']); ?>" required>
        </div>
    </div>

    <label for="ProjDesc">Project Description:</label>
    <input type="text" id="ProjDesc" name="ProjDesc" value="<?php echo htmlspecialchars($project['ProjDesc']); ?>" required><br>

     <div class="form-group">
        <div>
            <label for="ProjSkills">Project Skills:</label>
            <input type="text" id="ProjSkills" name="ProjSkills" value="<?php echo htmlspecialchars($project['ProjSkills']); ?>" required>
        </div>
        <div>
            <label for="ProjProgram">Year:</label>
            <select id="ProjProgram" name="ProjProgram" required>
                <option value="Undergraduate" <?php if ($project['ProjProgram'] == 'Undergraduate') echo 'selected'; ?>>Undergraduate</option>
                <option value="Graduate" <?php if ($project['ProjProgram'] == 'Graduate') echo 'selected'; ?>>Graduate</option>
            </select>
            <br>
        </div>
    </div>

    <label for="SID">Student ID:</label>
    <input type="text" id="SID" name="SID" value="<?php echo htmlspecialchars($project['SID']); ?>"><br>

    <div class="form-group">
        <div>
            <label for="projstartdate">Project Start Date:</label>
            <input type="date" id="projstartdate" name="projstartdate" value="<?php echo htmlspecialchars($project['projstartdate']); ?>">
        </div>
        <div>
           <label for="projenddate">Project End Date::</label>
            <input type="date" id="projenddate" name="projenddate" value="<?php echo htmlspecialchars($project['projenddate']); ?>">
        </div>
    </div>


    <label for="application_deadline">Application Deadline:</label>
    <input type="date" id="application_deadline" name="application_deadline" value="<?php echo htmlspecialchars($project['application_deadline']); ?>"><br>

    <label for="archived">Archived:</label>
    <input type="text" id="archived" name="archived" value="<?php echo htmlspecialchars($project['archived'] ?? ''); ?>"><br>


    <input type="submit" value="Update Project">
</form>

    

    
    
    
</body>
</html>
