<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <?php
        require_once "../db_connect.php";
        require_once "../auth.php";
        require_once "attendance0.php";
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

        <h2>Attendance for <span id="subject-title"><?= htmlspecialchars($subject_name) ?></span></h2>
        <h3>Instructor: <span id="teacher-name"><?= htmlspecialchars($instructor_name) ?></span></h3>
        <h3>Attendance Percentage: <span id="attendance-percentage"><?= htmlspecialchars($attendance_percentage) ?>%</span></h3>

        <table>
            <tr>
                <th>Lecture #</th>
                <th>Date</th>
                <th>Start Time</th>
                <th>Status</th>
            </tr>
            <?php if (!empty($attendance_records)): ?>
                <?php
                    $lecture_no = 0;
                    foreach ($attendance_records as $record): ?>
                    <tr>
                        <td><?= htmlspecialchars(++$lecture_no) ?></td>
                        <td><?= htmlspecialchars(date('d-m-Y', strtotime($record['session_date']))) ?></td>
                        <td><?= htmlspecialchars($record['session_time']) ?></td>
                        <td><?= htmlspecialchars($record['status'][0]) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
