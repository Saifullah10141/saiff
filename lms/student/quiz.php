<?php
    require_once "../db_connect.php";
    require_once "../auth.php";
    require_once "quiz0.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizes</title>
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
            color: black;
            font-family: Arial, sans-serif;
        }

        .header {
            background-color: #2980b9;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .sidebar {
            width: 250px;
            background-color: #003366; /* Dark blue sidebar */
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px;
            margin: 10px 0;
            background-color: #004080; /* Blue color for links */
            text-align: center;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background-color: #0066cc; /* Lighter blue on hover */
        }

        .content {
            flex: 1;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }
        .open-btn:hover {
            background-color: #45a049;
        }
        .open-btn {
            padding: 6px 14px;
            background-color: #4CAF50;
            color: white;
            border: none;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .open-btn.disabled {
            padding: 6px 14px;
            background-color: #cccccc;
            color: #666666;
            border: none;
            border-radius: 4px;
            cursor: not-allowed;
            opacity: 0.6;
            pointer-events: none;
        }


    </style>
</head>
<body>
    <div class="sidebar">
        <a href="dashboard.php">Dashboard</a>
        <a href="enroll_subjects.php">Enrolled Subjects</a>
        <a href="assignment.php">Assignment</a>
        <a class="active" href="quiz.php">Quiz</a>
        <a href="contact_us.php">Contact Us</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <div class="header">
            <h2>Welcome, <span id="username"><?= $_SESSION['username'] ?></span>!</h2>
        </div>

        <h3>Pending Quizzes</h3>
        <table>
            <tr>
                <th>Title</th>
                <th>Course Title</th>
                <th>Due Date</th>
                <th>Action</th>
            </tr>
            <?php if (count($pending_quizzes) > 0): ?>
                <?php foreach ($pending_quizzes as $quiz):
                    $due_date = $quiz['due_date'];
                    $today = time()-75600;
                    $isActive = $today <= strtotime($due_date); ?>
                    <tr>
                        <td><?= htmlspecialchars($quiz['title']) ?></td>
                        <td><?= htmlspecialchars($quiz['subject_name']) ?></td>
                        <td><?= date("F d, Y", strtotime($quiz['due_date'])) ?></td>
                        <td>
                            <?php if ($isActive): ?>
                                <a href="submit_quiz.php?quiz_id=<?= $quiz['quiz_id'] ?>" class="open-btn">Open</a>
                            <?php else: ?>
                                <a class="open-btn disabled">Due Date Passed</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">No pending quizzes.</td></tr>
            <?php endif; ?>
        </table>

        <br>

        <h3>Submitted Quizzes</h3>
        <table>
            <tr>
                <th>Title</th>
                <th>Course Title</th>
                <th>Submitted On</th>
                <th>Score</th>
            </tr>
            <?php if (count($submitted_quizzes) > 0): ?>
                <?php foreach ($submitted_quizzes as $quiz): ?>
                    <tr>
                        <td><?= htmlspecialchars($quiz['title']) ?></td>
                        <td><?= htmlspecialchars($quiz['subject_name']) ?></td>
                        <?php if($quiz['submitted_at']) {?>
                        <td><?= date("F d, Y", strtotime($quiz['submitted_at'])) ?></td>
                        <?php } else{ ?>
                        <td> Not Submitted </td>
                        <?php } ?>
                        <?php if($quiz['score']) {?>
                        <td><?= $quiz['score'] ?>%</td>
                        <?php } else{ ?>
                        <td> 0% </td>
                        <?php } ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">No quizzes submitted yet.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>