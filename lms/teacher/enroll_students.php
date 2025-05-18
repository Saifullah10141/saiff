<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll Students</title>
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

        .student-list {
            margin-top: 20px;
            border-collapse: collapse;
            width: 100%;
            display: none;
        }

        .student-list th, .student-list td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .student-list th {
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

        <div class="form-container">
            <h3>Enroll Student</h3> <br>
            <label for="course">Select Course:</label>
            <select id="course" style="margin:10px;" onchange="fetchStudents()">
                <option value="">Select Course</option>
            </select>

            <label for="registration">Registration No. (Format: XXXX-ag-XXXX):</label>
            <input type="text" id="registration" placeholder="Enter registration number" style="margin:10px;">

            <button onclick="enrollStudent()">Enroll Student</button>
        </div>

        <h3>Enrolled Students</h3>
        <table class="student-list" id="student-table">
            <thead>
                <tr>
                    <th>Registration No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Student data will be inserted dynamically -->
            </tbody>
        </table>
    </div>

    <script>
window.onload = function () {
    loadAssignments();
};

function loadAssignments() {
    fetch("enroll_students0.php?action=get_assignments")
        .then(res => res.json())
        .then(data => {
            const courseSelect = document.getElementById("course");
            courseSelect.innerHTML = '<option value="">Select Course</option>';
            data.forEach(course => {
                courseSelect.innerHTML += `<option value="${course.assignment_id}">${course.name}</option>`;
            });
        });
}

function enrollStudent() {
    let assignmentId = document.getElementById("course").value;
    let regNo = document.getElementById("registration").value.trim();

    if (!assignmentId || !regNo) {
        alert("Please fill in all fields.");
        return;
    }

    fetch("enroll_students0.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=enroll_student&assignment_id=${assignmentId}&registration_number=${regNo}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert("Student enrolled successfully!");
            fetchStudents();
        } else {
            alert(data.error || "Failed to enroll student.");
        }
    });
}

function fetchStudents() {
    let assignmentId = document.getElementById("course").value;
    let studentTable = document.getElementById("student-table");
    let tbody = studentTable.getElementsByTagName("tbody")[0];

    if (!assignmentId) {
        studentTable.style.display = "none";
        return;
    }

    studentTable.style.display = "table";
    tbody.innerHTML = "<tr><td colspan='4'>Loading...</td></tr>";

    fetch("enroll_students0.php?action=get_students&assignment_id=" + assignmentId)
        .then(res => res.json())
        .then(data => {
            if (data.length === 0) {
                tbody.innerHTML = "<tr><td colspan='4'>No students enrolled yet.</td></tr>";
            } else {
                tbody.innerHTML = "";
                data.forEach(student => {
                    tbody.innerHTML += `
                    <tr>
                        <td>${student.registration_number}</td>
                        <td>${student.full_name}</td>
                        <td>${student.email}</td>
                        <td><button class="delete-button" onclick="deleteStudent(this, '${student.registration_number}')">Remove</button></td>
                    </tr>
                `;
                });
            }
        });
}

function deleteStudent(button, studentId) {
    const course = document.getElementById("course").value;

    if (!confirm("Are you sure you want to remove this student?")) return;

    fetch('remove_student.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `student_id=${encodeURIComponent(studentId)}&assignment_id=${encodeURIComponent(course)}`
    })
    .then(res => res.text())
    .then(response => {
        alert(response);
        button.closest("tr").remove();
    })
    .catch(() => alert("Failed to remove student."));
}

</script>

</body>
</html>