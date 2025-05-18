<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            height: 100vh;
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            width: 250px;
            background-color: #003366;
            padding: 20px;
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: fixed;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px;
            margin: 10px 0;
            background-color: #004080;
            text-align: center;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background-color: #0066cc;
        }

        .content {
            flex: 1;
            padding: 40px;
            margin-left: 270px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .contact-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .contact-container h2 {
            color: #003366;
            margin-bottom: 15px;
        }

        .contact-container p {
            font-size: 16px;
            margin: 8px 0;
            color: #333;
        }

        .logo {
            width: 120px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php
        require_once "../db_connect.php";
        require_once "../auth.php";
    ?>
    <div class="sidebar">
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_courses.php">Manage Courses</a>
        <a href="manage_students.php">Manage Students</a>
        <a href="manage_instructors.php">Manage Instructors</a>
        <a href="contact_us.php">Contact Us</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <div class="contact-container">
            <img src="../saif_logo.png" alt="Saif's Group Logo" class="logo">
            <h2>Contact Us</h2>
            <p><strong>Email:</strong> 2023ag10141@uaf.edu.pk</p>
            <p><strong>Phone:</strong> +92 309 7171127</p>
            <p><strong>Working Hours:</strong> 09:00 AM - 05:00 PM</p>
            <p><strong>Developed by:</strong> Saif Group</p>
        </div>
    </div>
</body>
</html>