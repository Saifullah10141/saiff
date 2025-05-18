<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Content</title>
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
            color: #000;
            font-family: Arial, sans-serif;
        }
        .sidebar {
            width: 250px;
            background-color: #003366;
            padding: 20px;
            display: flex;
            flex-direction: column;
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
        .main-content {
            flex: 1;
            padding: 20px;
        }
        .header {
            background-color: #2980b9;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .attendance-btn {
            display: block;
            background-color: #2980b9;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 20px;
        }
        .week-list {
            list-style: none;
        }
        .week-list li {
            padding: 10px;
            margin: 5px 0;
            background-color: #f0f0f0;
            border-radius: 5px;
        }
        #teacher-name{
        }
    </style>
</head>
<body>
    <?php
        require_once "../db_connect.php";
        require_once "../auth.php";
        require_once "course_content0.php";
    ?>
    <div class="sidebar">
        <a href="dashboard.php">Dashboard</a>
        <a href="/saiff/LMS/student/enroll_subjects.php">Enrolled Subjects</a>
        <a href="/saiff/LMS/student/assignment.php">Assignment</a>
        <a href="/saiff/LMS/student/quiz.php">Quiz</a>
        <a href="/saiff/LMS/student/contact_us.php">Contact Us</a>
        <a href="/saiff/LMS/student/logout.php">Logout</a>
    </div>
    <div class="main-content">
        <div class="header">
            <h2>Welcome, <span id="username"><?= htmlspecialchars($username) ?></span>!</h2>
        </div>
        
        <!-- Dynamically display the course content -->
        <h1 id="subject-title"><?= htmlspecialchars($subject_name) ?></h1>   <br>
        <h2>Instructor: <span id="teacher-name"><?= htmlspecialchars($instructor_name) ?></span></h2>
        
        <!-- Link for viewing attendance -->
        <a id="attendance-link" href="attendance.php?assignment_id=<?= $assignment_id ?>" class="attendance-btn">View Attendance</a>
        
        <!-- Week-wise content list -->
        <ul class="week-list" id="week-list">
            <?php
            // First, organize available weeks into an array where week_no is the key
            $availableWeeks = [];
            if (!empty($weeks)) {
                foreach ($weeks as $week) {
                    $availableWeeks[(int)$week['week_no']] = $week['description'];
                }
            }

            // Now loop from 1 to 8 and display week headers whether content exists or not
            for ($i = 1; $i <= 8; $i++):
            ?>
                <li>
                    <strong>Week <?= $i ?>:</strong> 
                    <?= isset($availableWeeks[$i]) ? htmlspecialchars($availableWeeks[$i]) : '' ?>
                </li>
            <?php endfor; ?>
        </ul>

    </div>
</body>
</html>
