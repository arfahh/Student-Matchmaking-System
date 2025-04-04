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

// Query to fetch all skills
$sql_skills = "SELECT skill_id, skill_name, category FROM skills_list";
$result_skills = $conn->query($sql_skills);

// Check if there are any skills in the database
if ($result_skills->num_rows > 0) {
    // Create an array to store skill records
    $skills = [];
    while ($row = $result_skills->fetch_assoc()) {
        $skills[] = $row;
    }
} else {
    $skills = []; // No skills found
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Manage Skills </title>
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
            width: 80%; 
            margin: 20px auto; 
            border-collapse: separate; 
            border-spacing: 0; 
            border-radius: 15px; 
            overflow: hidden; 
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2); 
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd; 
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
            color: #ffffff;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }

        .add-skill-btn {
            display: inline-block;
            background-color: #0058ab; 
            color: white; 
            padding: 10px 20px; 
            border-radius: 25px; 
            text-decoration: none; 
            font-size: 16px;
            font-weight: bold;
            transition: 0.3s;
        }

        .add-skill-btn:hover {
            background-color: #003f7f; 
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
            background-color: #28a745; 
            color: white;
        }

        .edit-btn:hover {
            background-color: #218838;
        }

        .delete-btn {
            background-color: #dc3545; 
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
            background-color: #0058ab; 
            color: white; 
            padding: 10px 20px; 
            border-radius: 25px; 
            text-decoration: none; 
            font-size: 16px;
            font-weight: bold;
            transition: 0.3s;
        }

        .back-btn:hover {
            background-color: #333; 
        }
    </style>
</head>
<body>
    <h1>Manage Skills</h1>
    <a href="admin_panel.php?adid=0" class="back-btn"> <- Back to Admin Panel</a>
    <a href="add_skill.php?adid=0" class="add-skill-btn">Add New Skill</a>
    
    <table border="1">
        <thead>
            <tr>
                <th>Skill ID</th>
                <th>Skill Name</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
    <?php 
    $colors = ["#5E9FDB", "#BBD5ED"];
    $index = 0;
    foreach ($skills as $skill): 
        $color = $colors[$index % count($colors)];
        $index++;
    ?>
        <tr style="background-color: <?php echo $color; ?>;">
            <td><?php echo htmlspecialchars($skill['skill_id']); ?></td>
            <td><?php echo htmlspecialchars($skill['skill_name']); ?></td>
            <td><?php echo htmlspecialchars($skill['category']); ?></td>
            <td>
                <a href="delete_skill.php?adid=0&skill_id=<?php echo $skill['skill_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this skill?')">
                    <i class="fas fa-trash-alt"></i> Delete
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    </table>

</body>
</html>
