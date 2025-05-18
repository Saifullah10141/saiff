<?php
    $score = isset($_GET['score']) ? htmlspecialchars($_GET['score']) : 'NAN';
    require_once "../db_connect.php";
    require_once "../auth.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Submitted</title>
    <link rel="stylesheet" href="styles.css"> <!-- External CSS file for styles -->
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
            text-align: center;
        }

        .header {
            background-color: #2980b9;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .message-container {
            margin-top: 100px;
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            background-color: #2980b9;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
            transition: 0.3s;
        }

        .back-button:hover {
            background-color: #1f6692;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="dashboard.php">Dashboard</a>
        <a href="/saiff/LMS/student/enroll_subjects.php">Enrolled Subjects</a>
        <a href="/saiff/LMS/student/assignment.php">Assignment</a>
        <a href="/saiff/LMS/student/quiz.php">Quiz</a>
        <a href="/saiff/LMS/student/contact_us.php">Contact Us</a>
        <a href="/saiff/LMS/student/logout.php">Logout</a>
    </div>

    <div class="content">
        <div class="header">
            <h2>Welcome, <span id="username"><?= $_SESSION['username'] ?></span>!</h2>
        </div>
        <div class="message-container">
            Quiz has been submitted successfully!
            
    <p>Your score: <?= $score ?>%</p>
        </div>
        <a href="dashboard.php" class="back-button">Return to your Dashboard</a>
    </div>
</body>
</html>