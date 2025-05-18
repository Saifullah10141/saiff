<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrolled Subjects</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .header {
            background-color: #2980b9;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
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
        h1 {
            margin-bottom: 20px;
            color: #000;
        }
        .subject-list {
            list-style: none;
        }
        .subject-list li {
            padding: 10px;
            margin: 10px 0;
            background-color: #f0f0f0;
            border-radius: 5px;
            cursor: pointer;
        }
        .subject-list li:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <?php
        require_once "../db_connect.php";
        require_once "../auth.php";
        require_once "enroll_subjects0.php";
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
        <h1>Enrolled Subjects</h1>
        <ul class="subject-list">
            <?php if (count($subjects) > 0): ?>
                <?php foreach ($subjects as $subject): ?>
                    <li onclick="openCourseContent('<?= htmlspecialchars($subject['course_name']) ?>', '<?= htmlspecialchars($subject['assignment_id']) ?>')">
                        <?= htmlspecialchars($subject['course_name']) ?>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No subjects enrolled yet.</li>
            <?php endif; ?>
        </ul>

    </div>

    <script>
        // Function to open course content
        function openCourseContent(courseName, assignmentId) {
            // Redirect to the course content page with the assignment_id in the URL
            window.location.href = 'course_content.php?assignment_id=' + assignmentId;
        }

    </script>
</body>
</html>