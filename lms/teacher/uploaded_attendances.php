<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Attendances</title>
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
        .filter-form {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .filter-form select, .filter-form input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 30%;
        }
        .filter-button {
            padding: 10px 15px;
            font-size: 16px;
            border: none;
            background-color: #28a745;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        .filter-button:hover {
            background-color: #218838;
        }
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
        }
        .attendance-table th, .attendance-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .attendance-table th {
            background-color: #2980b9;
            color: white;
        }
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .view-button, .delete-button {
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }
        .view-button {
            background-color: #004080;
            color: white;
        }
        .view-button:hover {
            background-color: #0066cc;
        }
        .delete-button {
            background-color: #d9534f;
            color: white;
            border: none;
        }
        .delete-button:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>
    <?php
        require_once "../db_connect.php";
        require_once "../auth.php";
        require_once "uploaded_attendances0.php";
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
            <h2>Welcome, <span id="username"><?php echo htmlspecialchars($username); ?></span>!</h2>
        </div>

        <h2>Uploaded Attendances</h2>

        <!-- Filter Section -->
        <form action="" method="GET" class="filter-form">
        <select name="subject">
            <option value="">Select Course</option>
            <?php
                // Fetch assigned subjects dynamically
                $subjectQuery = $conn->prepare("
                    SELECT DISTINCT s.name
                    FROM assigned_subjects s
                    JOIN attendances a ON s.assignment_id = a.assignment_id
                    WHERE a.instructor_id = ?
                ");
                $subjectQuery->bind_param("s", $instructor_id);
                $subjectQuery->execute();
                $subjectResult = $subjectQuery->get_result();

                while ($subjectRow = $subjectResult->fetch_assoc()) {
                    $selected = ($_GET['subject'] ?? '') === $subjectRow['name'] ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($subjectRow['name']) . "' $selected>" . htmlspecialchars($subjectRow['name']) . "</option>";
                }
                $subjectQuery->close();
            ?>
        </select>


            <input type="date" name="date">

            <button type="submit" class="filter-button">Filter</button>
        </form>

        <!-- Attendance Records Table -->
        <table class="attendance-table">
    <tr>
        <th>Course</th>
        <th>Date</th>
        <th>Time</th>
        <th>Actions</th>
    </tr>
    <?php
        if (count($attendances) > 0) {
            foreach ($attendances as $attendance) {
                echo "<tr>
                    <td>" . htmlspecialchars($attendance['subject_name']) . "</td>
                    <td>" . htmlspecialchars($attendance['session_date']) . "</td>
                    <td>" . htmlspecialchars($attendance['session_time']) . "</td>
                    <td class='action-buttons'>
                        <a href='view_attendance.php?id={$attendance['attendance_id']}' class='view-button'>View</a>
                        <button class='delete-button' onclick='deleteAttendance({$attendance['attendance_id']})'>Delete</button>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No attendance records found.</td></tr>";
        }
    ?>
</table>

    </div>

    <script>
        function deleteAttendance(id) {
            if (confirm("Are you sure you want to delete this attendance record?")) {
                window.location.href = "delete_attendance.php?id=" + id;
            }
        }
    </script>
</body>
</html>
