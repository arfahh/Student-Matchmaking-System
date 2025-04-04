<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>

<header>
    <h1>Admin Dashboard</h1>

    <!-- User info in the top-right corner -->
    <div class="user-info">
        <?php  
        // Include the database connection
        include('db.php');

        // Admin info (e.g., showing admin name or handling admin-related pages)
        echo "<strong>Admin</strong> 🧑‍💻";
        echo '<a href="logout.php">Logout</a>';
        ?>
    </div>

    <nav>
        <ul class="nav-links">
            <!-- Admin-related links -->
            <li><a href="manage_students.php?adid=0">Manage Students</a></li>
            <li><a href="manage_faculty.php?adid=0">Manage Faculty</a></li>
            <li><a href="manage_projects.php?adid=0">Manage Projects</a></li>
            <li><a href="manage_applications.php?adid=0">Manage Applications</a></li>
            <li><a href="manage_skills.php?adid=0">Manage Skills</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

</body>
</html>
