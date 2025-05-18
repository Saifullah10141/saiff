<?php
    require_once "../db_connect.php";
    require_once "../auth.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submitted Assignments</title>
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

        .filter-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f4f4f4;
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
            padding: 10px;
        }
        button:hover {
            background-color: #218838;
        }

        .assignment-list {
            margin-top: 20px;
            border-collapse: collapse;
            width: 100%;
        }

        .assignment-list th, .assignment-list td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .assignment-list th {
            background-color: #2980b9;
            color: white;
        }

        .view-button {
            background-color: #004080;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }
        .view-button:hover {
            background-color: #0066cc;
        }

        .pass-fail {
            display: flex;
            gap: 10px;
        }
        .graded {
            font-weight: bold;
            color: green;
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

        <div class="filter-section">
            <h3>Filter Assignments</h3>
            <label for="subject">Select Course:</label>
            <select id="subject" onchange="fetchAssignments()">
                <option value="">Select Course</option>
            </select>

            <label for="assignment">Select Assignment:</label>
            <select id="assignment" onchange="fetchResults()" data-assigned-id="${a.assigned_id}">
                <option value="">Select Assignment</option>
            </select>
        </div>

        <h2>Submitted Assignments</h2>
        <table class="assignment-list" id="results-table">
            <thead>
                <tr>
                    <th>Registration No.</th>
                    <th>Student Name</th>
                    <th>Submission Date</th>
                    <th>Grade</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody><tr><td colspan="5">Please select an assignment.</td></tr></tbody>
        </table>

        <button onclick="saveGrades()">Save Grades</button>
    </div>

    <script>
        window.onload = function () {
            fetch('get_assigned_subjects.php')
                .then(res => res.json())
                .then(data => {
                    const subjectSelect = document.getElementById('subject');
                    subjectSelect.innerHTML = `<option value="">Select Subject</option>`;
                    data.forEach(subject => {
                        subjectSelect.innerHTML += `<option value="${subject.assignment_id}">${subject.name}</option>`;
                    });
                });
        };

        function fetchAssignments() {
            const assignmentId = document.getElementById("subject").value;
            const assignmentSelect = document.getElementById("assignment");

            assignmentSelect.innerHTML = '<option value="">Loading...</option>';

            fetch(`get_assignments.php?subject_id=${assignmentId}`)
                .then(res => res.json())
                .then(data => {
                    assignmentSelect.innerHTML = `<option value="">Select Assignment</option>`;
                    data.forEach(a => {
                        assignmentSelect.innerHTML += `<option value="${a.assignment_id}">${a.title}</option>`;
                    });
                });
        }

        function fetchResults() {
    const assignedId = document.getElementById("subject").value;
    const actualAssignmentId = document.getElementById("assignment").value;
    const tbody = document.getElementById("results-table").querySelector("tbody");

    tbody.innerHTML = `<tr><td colspan="5">Loading...</td></tr>`;

    fetch(`get_submitted_assignments.php?assigned_id=${assignedId}&assignment_id=${actualAssignmentId}`)
        .then(res => res.json())
        .then(data => {
            tbody.innerHTML = '';
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5">No submissions found.</td></tr>';
                return;
            }

            data.forEach(sub => {
                tbody.innerHTML += `
                    <tr>
                        <td>${sub.student_id}</td>
                        <td>${sub.student_name}</td>
                        <td>${sub.submitted_at ?? 'Not submitted'}</td>
                        <td id="grade-${sub.student_id}">
                            ${
                                sub.status === "Pass" ? "Pass" :
                                sub.status === "Fail" ? "Fail" :
                                `
                                <div class="pass-fail">
                                    <input type="radio" name="grade-${sub.student_id}" value="Pass"> P
                                    <input type="radio" name="grade-${sub.student_id}" value="Fail"> F
                                </div>
                                `
                            }
                        </td>
                        <td>
                            ${sub.file_path ? 
                                `<a href="../uploads/student_assignments/${sub.file_path}" class="view-button" target="_blank">View</a>` 
                                : '<span>No file</span>'}
                        </td>
                    </tr>`;
            });
        });
}


        function saveGrades() {
            const assignmentId = document.getElementById("assignment").value;
            const grades = [];
            
            document.querySelectorAll(".pass-fail").forEach(div => {
                const studentId = div.parentElement.id.replace("grade-", "");
                const selected = div.querySelector(`input[name="grade-${studentId}"]:checked`);
                console.log(assignmentId, studentId)
                if (selected) {
                    grades.push({
                        assignment_id: assignmentId,
                        student_id: studentId,
                        grade: selected.value
                    });
                }
            });

            fetch("save_grades.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(grades)
            }).then(res => res.json())
              .then(data => {
                  if (data.success) {
                      alert("Grades saved successfully!");
                      fetchResults();
                  }
              });
        }
    </script>
</body>
</html>