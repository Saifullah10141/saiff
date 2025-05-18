<?php
    require_once "../db_connect.php";
    require_once "../auth.php";
    require_once "submit_quiz0.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Quiz</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        #btn {
            color: white;
            background-color: #2980b9; /* Changed to blue */
            margin: 10px;
            padding: 10px;
            border-radius: 10px;
            float: right;
            font-size: 20px;
            cursor: pointer;
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
            background-color: white; /* Changed to white */
            color: #000; /* Changed text color to black */
            font-family: Arial, sans-serif;
        }
        .sidebar {
            width: 250px;
            background-color: #003366; /* Changed to dark blue */
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px;
            margin: 10px 0;
            background-color: #004080; /* Changed to blue */
            text-align: center;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background-color: #0066cc; /* Lighter blue on hover */
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        h1 {
            margin-bottom: 20px;
            color: #000; /* Ensured main heading text is black */
        }
        .quiz-question {
            margin-bottom: 20px;
        }
        .quiz-options {
            list-style: none;
        }
        .quiz-options li {
            padding: 10px;
            margin: 10px 0;
            background-color: #f0f0f0; /* Light gray background */
            border-radius: 5px;
            cursor: pointer;
        }
        .quiz-options li:hover {
            background-color: #ddd; /* Darker gray on hover */
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="dashboard.php">Dashboard</a>
        <a href="enroll_subjects.php">Enrolled Subjects</a>
        <a href="assignment.php">Assignment</a>
        <a href="quiz.php">Quiz</a>
        <a href="contact_us.php">Contact Us</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Welcome, <?= htmlspecialchars($student_name) ?>!</h2>
        </div>
        <h1>Quiz: <?= htmlspecialchars($quiz_info['title']) ?> (<?= htmlspecialchars($quiz_info['course_title']) ?>)</h1>

        <form action="submit_quiz1.php" method="POST">
            <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">

            <?php foreach ($questions as $index => $q): ?>
                <div class="quiz-question">
                    <h3>Q<?= $index + 1 ?>. <?= htmlspecialchars($q['question_text']) ?></h3>
                    <?php
                    $options = [
                        'A' => $q['correct_answer'],
                        'B' => $q['option1'],
                        'C' => $q['option2'],
                        'D' => $q['option3']
                    ];

                    // Shuffle while preserving answer values but randomizing their keys
                    $shuffled_options = [];
                    foreach ($options as $key => $val) {
                        $shuffled_options[] = ['value' => $key, 'text' => $val];
                    }
                    shuffle($shuffled_options);
                    ?>

                    <ul class="quiz-options">
                        <?php foreach ($shuffled_options as $opt): ?>
                            <li>
                                <input type="radio" name="question_<?= $q['question_id'] ?>" value="<?= $opt['value'] ?>" required>
                                <?= htmlspecialchars($opt['text']) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                </div>
            <?php endforeach; ?>

            <button type="submit" id="btn">Submit</button>
        </form>
    </div>
    <script>
    const quizEndTimestamp = <?= $end_time * 1000 ?>; // in ms
    const form = document.querySelector('form');
    const display = document.createElement('div');
    display.style.fontWeight = 'bold';
    display.style.margin = '10px 0';
    form.prepend(display);

    function updateTimer() {
        const now = Date.now();
        const remaining = quizEndTimestamp - now;

        if (remaining <= 0) {
            clearInterval(timer);
            form.submit();
            return;
        }

        const minutes = Math.floor(remaining / 60000);
        const seconds = Math.floor((remaining % 60000) / 1000);
        display.textContent = `Time Remaining: ${minutes}m ${seconds < 10 ? '0' : ''}${seconds}s`;
        display.style.position = 'fixed';
        display.style.top = '10px';
        display.style.right = '20px';
        display.style.zIndex = '1000';
        display.style.backgroundColor = '#222';
        display.style.color = '#fff';
        display.style.padding = '10px 15px';
        display.style.borderRadius = '8px';
        display.style.boxShadow = '0 0 10px rgba(0, 0, 0, 0.3)';

    }

    const timer = setInterval(updateTimer, 1000);
    updateTimer();
    form.addEventListener("submit", () => {
    document.getElementById("btn").disabled = true;
});

    </script>

</body>
</html>