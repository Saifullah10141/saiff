<?php
    require_once "../db_connect.php";
    require_once "../auth.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results</title>
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
        .form-container {
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 5px;
        }
        label {
            font-weight: bold;
        }
        input, select, button {
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
            border-collapse: collapse;
            width: 100%;
            display: none;
        }
        .results-table th, .results-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .results-table th {
            background-color: #2980b9;
            color: white;
        }
        .edit-button, .delete-button {
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            color: white;
        }
        .edit-button {
            background-color: #ffc107;
        }
        .delete-button {
            background-color: #dc3545;
        }
        .edit-button:hover {
            background-color: #e0a800;
        }
        .delete-button:hover {
            background-color: #c82333;
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

    <div class="content">
        <div class="header">
            <h2>Welcome, <span id="username"><?= htmlspecialchars($username) ?></span>!</h2>
        </div>

        <div class="form-container">
            <h3>Upload Student Results</h3> <br>
            <label for="course">Select Course:</label>
            <select id="course" onchange="loadStudents()">
                <option value="">Select Course</option>
            </select>

            <label for="registration">Select Student:</label>
            <select id="registration">
                <option value="">Select Student</option>
            </select>

            <label for="mid">Mid Marks:</label>
            <input type="text" id="mid" placeholder="Enter Mid Marks">
            <label for="final">Final Marks:</label>
            <input type="text" id="final" placeholder="Enter Final Marks">
            <label for="sessional">Sessional Marks:</label>
            <input type="text" id="sessional" placeholder="Enter Sessional Marks">
            <label for="practical">Practical Marks:</label>
            <input type="text" id="practical" placeholder="Enter Practical Marks">

            <button onclick="uploadResult()">Upload Result</button>
        </div>

        <h3>Uploaded Results</h3>
        <table class="results-table" id="results-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Mid</th>
                    <th>Final</th>
                    <th>Sessional</th>
                    <th>Practical</th>
                    <th>Total Obtained</th>
                    <th>Grade</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Results will be inserted dynamically -->
            </tbody>
        </table>
    </div>

    <script>
        window.onload = function () {
            fetch('get_assigned_subjects.php')
                .then(res => res.json())
                .then(data => {
                    let courseSelect = document.getElementById("course");
                    data.forEach(course => {
                        let option = document.createElement("option");
                        option.value = course.assignment_id;
                        option.text = course.name;
                        courseSelect.appendChild(option);
                    });
                });
        };

        function fetchResults() {
            let assignment_id = document.getElementById("course").value;
            let resultsTable = document.getElementById("results-table");
            let tbody = resultsTable.getElementsByTagName("tbody")[0];

            resultsTable.style.display = "table";

            tbody.innerHTML = "<tr><td colspan='7'>No results found.</td></tr>";

            if (!assignment_id) {
                resultsTable.style.display = "none";
                return;
            }

            fetch('fetch_results.php?assignment_id=' + assignment_id)
            .then(res => res.json())
            .then(data => {
                tbody.innerHTML = "";

                if (data.length === 0) {
                    tbody.innerHTML = "<tr><td colspan='7'>No results found.</td></tr>";
                    return;
                }

                data.forEach(result => {
                    let row = `
                        <tr>
                            <td>${result.student_id + " - " + result.username}</td>
                            <td>${result.mid}</td>
                            <td>${result.final}</td>
                            <td>${result.sessional}</td>
                            <td>${result.practical}</td>
                            <td>${result.total}</td>
                            <td>${result.grade}</td>
                            <td><button class="delete-button" onclick="deleteResult(${result.result_id}, this)">Delete</button></td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            });
        }

        function loadStudents() {
            const assignmentId = document.getElementById("course").value;
            const studentSelect = document.getElementById("registration");

            if (!assignmentId) return;

            fetch('load_students.php?assignment_id=' + assignmentId)
                .then(res => res.json())
                .then(data => {
                    studentSelect.innerHTML = '<option value="">Select Student</option>';
                    data.forEach(student => {
                        let option = document.createElement("option");
                        option.value = student.student_id;
                        option.text = student.student_id + " - " + student.username;
                        studentSelect.appendChild(option);
                    });
                });

            fetchResults();
        }

        function uploadResult() {
            let assignment_id = document.getElementById("course").value;
            let student_id = document.getElementById("registration").value;
            let mid = document.getElementById("mid").value;
            let final = document.getElementById("final").value;
            let sessional = document.getElementById("sessional").value;
            let practical = document.getElementById("practical").value;

            if (!assignment_id || !student_id) {
                alert("Please select course and student.");
                return;
            }

            fetch('upload_result.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `assignment_id=${assignment_id}&student_id=${student_id}&mid=${mid}&final=${final}&sessional=${sessional}&practical=${practical}`
            })
            .then(res => res.text())
            .then(data => {
                if (data === "success") {
                    alert("Result uploaded successfully!");
                    fetchResults(); // Refresh table
                }
                else if (data === "exists") {
                    alert('Result of this student is already uploaded.');
                    window.location.href = 'results.php';
                }
                else {
                    alert("Failed to upload result.");
                }
            });
        }

        function deleteResult(resultId, button) {
            fetch('delete_result.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `result_id=${resultId}`
            })
            .then(res => res.text())
            .then(data => {
                if (data === "success") {
                    button.closest("tr").remove();
                    alert("Result deleted.");
                } else {
                    alert("Failed to delete result.");
                }
            });
        }
</script>
</body>
</html>