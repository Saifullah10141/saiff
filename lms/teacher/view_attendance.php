<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            display: flex;
            height: 100vh;
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
        .container {
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
            background: white;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #2980b9;
            color: white;
        }
        .back-button {
            background-color: #004080;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .back-button:hover {
            background-color: #0066cc;
        }
        h3{
            margin: 10px;
        }
        .edit-button {
            padding: 5px 10px;
            background-color: #004080;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .edit-button:hover {
            background-color: #0066cc;
        }
        .save-button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .save-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<?php
    require_once "../db_connect.php";
    require_once "../auth.php";
    require_once "view_attendance0.php";
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

<div class="container">
    <div class="header">
        <h2>Welcome, <span id="username"><?php echo htmlspecialchars($username); ?></span>!</h2>
    </div>

    <h2>View Attendance:</h2> <br>
    <h3>Course: <?php echo htmlspecialchars($attendance_info['subject_name']); ?></h3>
    <h3>Date: <?php echo htmlspecialchars($attendance_info['session_date']); ?></h3>
    <h3>Time: <?php echo date("h:i:s A", strtotime($attendance_info['session_time'])); ?></h3>

    <table>
    <tr>
        <th>Student Name</th>
        <th>Registration Number</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>

    <?php
    if (count($student_attendance) > 0) {
        foreach ($student_attendance as $student) {
            echo "<tr>
                <td>" . htmlspecialchars($student['student_name']) . "</td>
                <td>" . htmlspecialchars($student['registration_no']) . "</td>
                <td>" . htmlspecialchars(substr($student['status'], 0, 1)) . "</td>
                <td>
                    <button class='edit-button' onclick='openModal(\"" . htmlspecialchars($student['registration_no']) . "\", \"" . htmlspecialchars($student['student_name']) . "\", \"" . htmlspecialchars($student['status']) . "\")'>
                        Edit
                    </button>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No students found.</td></tr>";
    }
?>

</table>

</div>

<!-- Edit Attendance Modal -->
<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
    <div style="background:#fff; margin:10% auto; padding:20px; width:400px; position:relative; border-radius:8px;">
        <span style="position:absolute; top:10px; right:15px; cursor:pointer;" onclick="closeModal()">✖</span>

        <h2>Edit Attendance</h2>
        <form id="editForm" method="POST" action="update_attendance.php">
            <input type="hidden" name="attendance_id" value="<?php echo $attendance_id; ?>">
            <input type="hidden" name="registration_no" id="modal_registration_no">
<br>
            <p>Student Name: <b id="modal_student_name"></b></p>
<br>
            <label for="status">Status:</label>
            <div id="modal_status">
<br>
                <input type="radio" id="present" name="status" value="Present" required>
                <label for="present">Present</label>

                <input type="radio" id="absent" name="status" value="Absent">
                <label for="absent">Absent</label>

                <input type="radio" id="leave" name="status" value="Leave">
                <label for="leave">Leave</label>
            </div>


            <br><br>
            <button class="save-button" type="submit">Save Changes</button>
        </form>
    </div>
</div>

<script>
function openModal(registration_no, student_name, status) {
    document.getElementById('modal_registration_no').value = registration_no;
    document.getElementById('modal_student_name').innerText = student_name;
    document.getElementById('modal_status').value = status;
    document.getElementById('editModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>


</body>
</html>