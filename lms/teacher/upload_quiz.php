<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quiz</title>
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
        .main-content {
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
        }
        .quiz-form {
            margin-top: 20px;
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 5px;
        }
        .input-group {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
        }
        input, select, button {
            display: block;
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            font-size: 16px;
            border-radius: 5px;
        }
        button {
            background-color: #28a745;
            color: white;
            cursor: pointer;
            border: none;
            margin-top: 10px;
        }
        button:hover {
            background-color: #218838;
        }
        .add-question {
            background-color: #007bff;
        }
        .add-question:hover {
            background-color: #0056b3;
        }
        .question-container {
            margin-bottom: 15px;
            background-color: #fff;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
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
        <a href="course_content.php">Course Contents</a>
        <a href="assignments.php">Assignments</a>
        <a href="quizzes.php">Quizzes</a>
        <a href="attendance.php">Attendance</a>
        <a href="enroll_students.php">Enroll Students</a>
        <a href="results.php">Results</a>
        <a href="contact_us.php">Contact Us</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Welcome, <span id="username"><?= htmlspecialchars($username) ?></span>!</h2>
        </div>

        <div class="quiz-form">
            <h3>Create a New Quiz</h3>   <br>
            <form action="upload_quiz0.php" method="post">
                <div class="input-group">
                    <label for="subject" style="font-size:20px;">Select Course:</label>
                    <select id="subject" name="subject" style="font-size:20px;"></select>
                    </select>
                </div>

                <div class="input-group">
                    <label for="title">Quiz Title:</label>
                    <input type="text" id="title" name="title" placeholder="Enter Quiz Title" required>
                </div>

                <div class="input-group">
                    <label for="time">Time:</label>
                    <input type="number" id="time" name="time" placeholder="Enter Maximum Time in minutes" required>
                </div>

                <div class="input-group">
                    <label for="due_date">Due Date:</label>
                    <input type="date" id="due_date" name="due_date" required>
                </div>

                <h3>Quiz Questions</h3>
                <div id="questions-container">
                    <!-- Default Question -->
                    <div class="question-container">
                        <input type="text" name="question[]" placeholder="Enter Question" required>
                        <input type="text" name="correct_answer[]" placeholder="Correct Answer" required>
                        <input type="text" name="option2[]" placeholder="Option 2" required>
                        <input type="text" name="option3[]" placeholder="Option 3" required>
                        <input type="text" name="option1[]" placeholder="Option 4" required>
                    </div>
                </div>

                <button type="button" class="add-question" onclick="addQuestion()">+ Add Question</button>
                <button type="submit">Submit Quiz</button>
            </form>
        </div>
    </div>

    <script>
               // Load subjects on page load
                window.onload = function () {
                    fetch('get_assigned_subjects.php')
                        .then(res => res.json())
                        .then(data => {
                            let subjectSelect = document.getElementById("subject");
                            subjectSelect.innerHTML = '<option value="">Select Course</option>';
                            data.forEach(subject => {
                                subjectSelect.innerHTML += `<option value="${subject.assignment_id}">${subject.name}</option>`;
                            });
                        });
                };

        function addQuestion() {
            let container = document.getElementById("questions-container");
            let newQuestion = document.createElement("div");
            newQuestion.classList.add("question-container");
            newQuestion.innerHTML = `
                <input type="text" name="question[]" placeholder="Enter Question" required>
                <input type="text" name="correct_answer[]" placeholder="Correct Answer" required>
                <input type="text" name="option2[]" placeholder="Option 2" required>
                <input type="text" name="option3[]" placeholder="Option 3" required>
                <input type="text" name="option1[]" placeholder="Option 4" required>
            `;
            container.appendChild(newQuestion);
        }
    </script>
</body>
</html>
