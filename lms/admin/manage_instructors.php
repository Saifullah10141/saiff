<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Instructors</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            height: 100vh;
            background-color: white;
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
            padding: 20px;
            margin-left: 250px;
        }

        .header {
            background-color: #2980b9;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .options-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 50px;
        }

        .option-card {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 250px;
            cursor: pointer;
            transition: 0.3s;
        }
        .option-card:hover {
            background-color: #e0e0e0;
        }

        .option-card a {
            text-decoration: none;
            color: #2980b9;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php
        require_once "../db.php";
        require_once "../auth.php";
    ?>
    <div class="sidebar">
        <a href="dashboard.php">Dashboard</a>
        <a href="reset_password.php">Reset User Password</a>
        <a href="manage_lms_managers.php">Manage LMS Managers</a>
        <a href="manage_course_coordinators.php">Manage Course Coordinators</a>
        <a href="manage_students.php">Manage Students</a>
        <a href="manage_instructors.php">Manage Instructors</a>
        <a href="view_user_activity.php">User Activity</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <div class="header">
            <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        </div>
        <div class="options-container">
            <div class="option-card">
                <a href="create_instructor.php">Create New Instructor ID</a>
            </div>
            <div class="option-card">
                <a href="view_instructors.php">View & Manage Instructors</a>
            </div>
        </div>
    </div>
</body>
</html>