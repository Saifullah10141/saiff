<?php
require_once "../db_connect.php";
require_once "../auth.php";
require_once "upload_attendance0.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Attendance</title>
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
        .content {
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
        .attendance-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 70%;
            margin: auto;
        }
        .form-group {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        label {
            font-weight: bold;
        }
        select, input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 70%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #2980b9;
            color: white;
        }
        .attendance-options {
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        .submit-btn {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 20px auto 0;
            font-size: 18px;
        }
        .submit-btn:hover {
            background-color: #218838;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

    <div class="attendance-form">
        <h2>Upload Attendance</h2>
        <form action="upload_attendance1.php" method="POST">
            <div class="form-group">
                <label for="subject">Select Course:</label>
                <select id="subject" name="subject" required>
                    <option value="">Select Course</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?= htmlspecialchars($subject['assignment_id']) ?>">
                            <?= htmlspecialchars($subject['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="date">Select Date:</label>
                <input type="date" id="date" name="date" required>
            </div>

            <div class="form-group">
                <label for="time">Select Time:</label>
                <input type="time" step="1" id="time" name="time" required>
            </div>

            <table id="studentsTable">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Registration No.</th>
                        <th>Attendance</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Students will be loaded here dynamically -->
                </tbody>
            </table>

            <button type="submit" class="submit-btn">Submit Attendance</button>
        </form>
    </div>
</div>

<script>
// When subject is selected, fetch enrolled students
$('#subject').change(function() {
    let assignmentId = $(this).val();
    if (assignmentId) {
        $.ajax({
            url: 'fetch_students.php',
            method: 'POST',
            data: {assignment_id: assignmentId},
            success: function(response) {
                $('#studentsTable tbody').html(response);
            }
        });
    } else {
        $('#studentsTable tbody').html('');
    }
});
</script>

</body>
</html>