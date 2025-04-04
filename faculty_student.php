<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Selection</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .container {
            text-align: center;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 20px;
        }
        .login-btn {
            display: block;
            width: 200px;
            padding: 10px;
            margin: 10px auto;
            text-align: center;
            text-decoration: none;
            font-size: 18px;
            color: white;
            border-radius: 5px;
            transition: 0.3s;
        }
        .student-btn { background-color: #007bff; }
        .faculty-btn { background-color: #28a745; }
        .admin-btn { background-color: #dc3545; } /* Red for admin */
        .login-btn:hover { opacity: 0.8; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Select Login Type</h2>
        <a href="studentpage.php" class="login-btn student-btn">Login as Student</a>
        <a href="facultypage.php" class="login-btn faculty-btn">Login as Faculty</a>
        <a href="admin_panel.php" class="login-btn admin-btn">Login as Admin</a> <!-- Admin Login -->
    </div>

</body>
</html>
