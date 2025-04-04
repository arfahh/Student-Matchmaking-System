<?php
// Start session
session_start();

// Include database connection
include('db.php');
include('headeradmin.php');

// Ensure fid is passed in the URL and equals 0
if (!isset($_GET['adid']) || $_GET['adid'] != '0') {
    die("Access denied. You must be an admin (adid=0) to view this page.");
}

// Query to fetch all projects
$sql_project = "SELECT * FROM project";
$result_project = $conn->query($sql_project);

// Check if there are any projects in the database
if ($result_project->num_rows > 0) {
    // Create an array to store student records
    $project = [];
    while ($row = $result_project->fetch_assoc()) {
        $project[] = $row;
    }
} else {
    $project = []; // No projects found
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Manage Projects </title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
            background-color: #75b2eb;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        header, footer {
             background: linear-gradient(90deg, #0058ab 0%, #002279 50%, #0058ab 100%);
            color: white;
           
            text-align: center;
            
        }

        table {
            width: 80%; /* Adjust width */
            margin: 20px auto; /* Center table */
            border-collapse: separate; /* Required for border-radius to work */
            border-spacing: 0; /* Removes gaps between cells */
            border-radius: 15px; /* Round edges */
            overflow: hidden; /* Ensures rounded corners apply properly */
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2); /* Optional shadow */
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd; /* Light border for better structure */
        }

        th {
            background-color: #0058ab;
            color: white;
        }

        th:first-child {
            border-top-left-radius: 15px;
        }

        th:last-child {
            border-top-right-radius: 15px;
        }

        tr:last-child td:first-child {
            border-bottom-left-radius: 15px;
        }

        tr:last-child td:last-child {
            border-bottom-right-radius: 15px;
        }

        h1 {
            color: #ffffff; /* Change this to any color you want */
            text-align: center; /* Centers the text */
            font-size: 24px; /* Adjust the size */
            font-weight: bold; /* Makes it bold */
        }
        .add-project-btn {
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

        .add-faculty-btn:hover {
            background-color: #003f7f; /* Darker shade on hover */
        }
        .edit-btn, .delete-btn {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            transition: 0.3s;
        }

        .edit-btn {
            background-color: #28a745; /* Green for Edit */
            color: white;
        }

        .edit-btn:hover {
            background-color: #218838;
        }

        .delete-btn {
            background-color: #dc3545; /* Red for Delete */
            color: white;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .edit-btn i, .delete-btn i {
            margin-right: 5px;
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

        .back-btn:hover {
            background-color: #333; /* Darker gray on hover */
        }

        .table-container {
            width: 95%; /* Ensures the table takes up 95% of the container width */
            max-width: 1500px; /* Maximum width for the table */
            margin: 0 auto; /* Centers the table horizontally */
            overflow-x: auto; /* Enables horizontal scrolling if the table overflows */
            overflow-y: auto;
            border-radius: 15px; /* Rounded corners for the container */
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2); /* Adds a shadow effect */
            background-color: white; /* White background for the container */
        }

        table {
            width: 100%; /* Ensures the table takes up the full width of its container */
            border-collapse: collapse; /* Ensures the table borders collapse */
            table-layout: auto; /* Automatically adjusts column widths based on content */
        }

        th, td {
            padding: 12px; /* Adds padding inside the cells */
            text-align: left; /* Aligns text to the left */
            border: 1px solid #ddd; /* Adds a border between cells */
        }

        th {
            background-color: #f4f4f4; /* Light gray background for headers */
            font-weight: bold; /* Makes header text bold */
        }

        @media (max-width: 1200px) {
            .table-container {
                width: 90%; /* Adjust width for smaller screens */
                max-width: 100%; /* Allows table to stretch full width on smaller screens */
            }
            table {
                width: 100%; /* Ensures the table stretches to fill the container */
            }
        }

        @media (max-width: 768px) {
            .table-container {
                width: 100%; /* On even smaller screens, use the full width */
            }
            table {
                width: 100%; /* Ensures the table is responsive */
            }
            th, td {
                padding: 8px; /* Reduces padding on smaller screens */
            }
        }

        

    </style>
</head>
<body>
    <h1>Manage Projects</h1>
    <a href="admin_panel.php?adid=0" class="back-btn"> <- Back to Admin Panel</a>
    <a href="add_project.php?adid=0" class="add-project-btn">Add New Project</a>
    
    
    
   <div class="table-container">
    <table border="1">
        <thead>
            <tr>
                <th>Actions</th>
                <th>Project ID</th>
                <th>Project Name</th>
                <th>Faculty ID</th>
                <th>Project Description</th>
                <th>Project Skills</th>
                <th>Project Program</th>
                <th>Student ID</th>
                <th>Status</th>
                <th>Project Start Date</th>
                <th>Project End Date</th>
                <th>Application Deadline</th>
                <th>Attachment Path</th>
                <th>Created on</th>
                <th>Archived</th>
                
            </tr>
        </thead>
        <tbody>
            <?php 
            $colors = ["#5E9FDB", "#BBD5ED"];
            $index = 0;
            foreach ($project as $student): 
                $color = $colors[$index % count($colors)];
                $index++;
            ?>
            <tr style="background-color: <?php echo $color; ?>;">
            <td>
                    <a href="edit_project_admin.php?adid=0&pid=<?php echo $student['PID']; ?>" class="edit-btn">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="delete_project_admin.php?adid=0&pid=<?php echo $student['PID']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this student?')">
                        <i class="fas fa-trash-alt"></i> Delete
                    </a>
                </td>
                <td><?php echo htmlspecialchars($student['PID']); ?></td>
                <td><?php echo htmlspecialchars($student['projectName']); ?></td>
                <td><?php echo htmlspecialchars($student['FID']); ?></td>
                <td><?php echo htmlspecialchars($student['ProjDesc']); ?></td>
                <td><?php echo htmlspecialchars($student['ProjSkills']); ?></td>
                <td><?php echo htmlspecialchars($student['ProjProgram']); ?></td>
                <td><?php echo htmlspecialchars($student['SID']); ?></td>
                <td><?php echo htmlspecialchars($student['status']); ?></td>
                <td><?php echo htmlspecialchars($student['projstartdate']); ?></td>
                <td><?php echo htmlspecialchars($student['projenddate']); ?></td>
                <td><?php echo htmlspecialchars($student['application_deadline']); ?></td>
                <td><?php echo htmlspecialchars($student['attachment_path']); ?></td>
                <td><?php echo htmlspecialchars($student['created_at']); ?></td>
                <td><?php echo htmlspecialchars($student['archived']); ?></td>
                
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
