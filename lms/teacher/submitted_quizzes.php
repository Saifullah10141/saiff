<?php
    require_once "../db_connect.php";
    require_once "../auth.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submitted Quizzes</title>
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
        .filter-section {
            margin-top: 20px;
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 5px;
        }
        select, button {
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
        .results-table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }
        .results-table th, .results-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .results-table th {
            background-color: #2980b9;
            color: white;
        }
    </style>
</head>
<body>
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
            <h2>Welcome, <span id="username"><?= $_SESSION['username'] ?></span>!</h2>
        </div>

        <div class="filter-section">
            <label for="subject">Select Course:</label>
            <select id="subject" onchange="fetchQuizzes()"></select>

            <label for="quiz">Select Quiz:</label>
            <select id="quiz" onchange="fetchResults()"></select>

        </div>

        <table class="results-table" id="results-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Registration No.</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                <!-- Results will be inserted here dynamically -->
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
    fetchSubjects();
});

function fetchSubjects() {
    const subjectSelect = document.getElementById("subject");
    subjectSelect.innerHTML = `<option value="">Loading...</option>`;

    fetch("get_assigned_subjects.php")
        .then(res => res.json())
        .then(data => {
            subjectSelect.innerHTML = `<option value="">Select Subject</option>`;
            data.forEach(subject => {
                subjectSelect.innerHTML += `<option value="${subject.assignment_id}">${subject.name}</option>`;
            });
        });
}

function fetchQuizzes() {
    const assignmentId = document.getElementById("subject").value;
    const quizSelect = document.getElementById("quiz");

    if (!assignmentId) return;

    quizSelect.innerHTML = `<option value="">Loading...</option>`;

    fetch(`get_quizzess.php?assigned_id=${assignmentId}`)
        .then(res => res.json())
        .then(data => {
            quizSelect.innerHTML = `<option value="">Select Quiz</option>`;
            data.forEach(quiz => {
                quizSelect.innerHTML += `<option value="${quiz.quiz_id}">${quiz.title}</option>`;
            });
        });
}


        function fetchResults() {
            const assignedId = document.getElementById("subject").value;
            const quizId = document.getElementById("quiz").value;
            const tbody = document.querySelector("#results-table tbody");

            if (!assignedId || !quizId) return;

            tbody.innerHTML = '<tr><td colspan="3">Loading...</td></tr>';

            fetch(`get_quiz_results.php?assigned_id=${assignedId}&quiz_id=${quizId}`)
                .then(res => res.json())
                .then(data => {
                    tbody.innerHTML = "";

                    if (data.length === 0) {
                        tbody.innerHTML = "<tr><td colspan='3'>No data found</td></tr>";
                        return;
                    }

                    data.forEach(row => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${row.student_name}</td>
                                <td>${row.student_id}</td>
                                <td>${row.score !== null ? row.score + "%" : "Not submitted"}</td>
                            </tr>
                        `;
                    });
                });
        }

    </script>
</body>
</html>
