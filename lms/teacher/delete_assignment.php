<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Assignment</title>
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
            background-color: #dc3545;
            color: white;
            cursor: pointer;
            border: none;
            margin-top: 10px;
        }
        button:hover {
            background-color: #c82333;
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

        .delete-button {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }
        .delete-button:hover {
            background-color: #c82333;
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

    <div class="content">
        <div class="header">
            <h2>Welcome, <span id="username"><?= htmlspecialchars($username) ?></span>!</h2>
        </div>

        <div class="filter-section">
            <h3>Filter Assignments</h3> <br>
            <label for="subject">Select Course:</label>
            <select id="subject" onchange="fetchAssignments()">
                <option value="">Select Course</option>
            </select>

            <label for="assignment">Select Assignment:</label>
            <select id="assignment" onchange="fetchResults()">
                <option value="">Select Assignment</option>
            </select>
        </div>

        <h2>Delete Assignments</h2>
        <table class="assignment-list" id="results-table">
            <thead>
                <tr>
                    <th>Assignment Title</th>
                    <th>Due Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be inserted here dynamically -->
            </tbody>
        </table>
    </div>

    <script>
    async function fetchAssignments() {
        const subjectSelect = document.getElementById("subject");
        const assignmentSelect = document.getElementById("assignment");
        const subjectId = subjectSelect.value;

        if (!subjectId) return;

        assignmentSelect.innerHTML = '<option>Loading...</option>';
        const res = await fetch(`get_assignments.php?subject_id=${subjectId}`);
        const assignments = await res.json();

        assignmentSelect.innerHTML = '<option value="">Select Assignment</option>';
        assignments.forEach(a => {
            assignmentSelect.innerHTML += `<option value="${a.assignment_id}">${a.title}</option>`;
        });

        // Show in table too
        const tbody = document.getElementById("results-table").querySelector("tbody");
        tbody.innerHTML = '';
        assignments.forEach(a => {
            tbody.innerHTML += `
                <tr>
                    <td>${a.title}</td>
                    <td>${a.due_date}</td>
                    <td><button onclick="deleteAssignment(${a.assignment_id}, this)">Delete</button></td>
                </tr>`;
        });
    }

    async function deleteAssignment(id, button) {
        const confirmed = confirm("Are you sure you want to delete this assignment?");
        if (!confirmed) return;

        const res = await fetch('delete_assignment0.php', {
            method: 'POST',
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ assignment_id: id })
        });

        const result = await res.json();
        if (result.success) {
            button.closest("tr").remove();
            alert("Assignment deleted!");
        } else {
            alert("Failed to delete assignment.");
        }
    }

    async function loadSubjects() {
        const res = await fetch('get_assigned_subjects.php');
        const subjects = await res.json();
        const subjectSelect = document.getElementById("subject");

        subjectSelect.innerHTML = '<option value="">Select Course</option>';
        subjects.forEach(s => {
            subjectSelect.innerHTML += `<option value="${s.assignment_id}">${s.name}</option>`;
        });
    }

    // Load subjects on page load
    window.onload = loadSubjects;
</script>

</body>
</html>
