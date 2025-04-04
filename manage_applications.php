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

// Query to fetch all students
$sql_application = "SELECT aid, sid, pid, status, score FROM application";
$result_application = $conn->query($sql_application);

// Check if there are any students in the database
if ($result_application->num_rows > 0) {
    // Create an array to store student records
    $application = [];
    while ($row = $result_application->fetch_assoc()) {
        $application[] = $row;
    }
} else {
    $application = []; // No students found
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Manage Applications </title>
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
        .add-faculty-btn {
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




    </style>
</head>
<body>
    <h1>Manage Applications </h1>
    <a href="admin_panel.php?adid=0" class="back-btn"> <- Back to Admin Panel</a>
    
    
    
    <table border="1">
        <thead>
            <tr>
                
                <th>Application ID</th>
                <th>Student ID</th>
                <th>Project ID</th>
                <th>Status</th>
                <th>Score</th>
                <th>Actions</th>
                
            </tr>
        </thead>
        <tbody>
    <?php 
    $colors = ["#5E9FDB", "#BBD5ED"];
    $index = 0;
    foreach ($application as $application): 
        $color = $colors[$index % count($colors)];
        $index++;
    ?>
        <tr style="background-color: <?php echo $color; ?>;">
           
            <td><?php echo htmlspecialchars($application['aid']); ?></td>
            <td><?php echo htmlspecialchars($application['sid']); ?></td>
            <td><?php echo htmlspecialchars($application['pid']); ?></td>
            <td><?php echo htmlspecialchars($application['status']); ?></td>
            <td><?php echo htmlspecialchars($application['score']); ?></td>
             <td>
                <a href="edit_applications.php?adid=0&aid=<?php echo $application['aid']; ?>" class="edit-btn">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="delete_application.php?adid=0&aid=<?php echo $application['aid']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this faculty?')">
                    <i class="fas fa-trash-alt"></i> Delete
                </a>
            </td>
            

        </tr>
    <?php endforeach; ?>
</tbody>

    </table>
</body>
</html>