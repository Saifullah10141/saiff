<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Student ID</title>
    <style>
         * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            height: 100vh;
            background-color: #f4f4f4;
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
            max-width: 600px;
            margin: auto;
        }

        label, input, select {
            display: block;
            width: 100%;
            margin: 10px 0;
            padding: 8px;
        }

        button {
            background-color: #2980b9;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #0066cc;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php
        require_once "../db.php";
        require_once "../auth.php";
    ?>
     <div class="sidebar">
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_students.php">Manage Students</a>
        <a href="manage_instructors.php">Manage Instructors</a>
        <a href="manage_degrees.php">Manage Degrees</a>
        <a href="view_attendance_reports.php">Attendance Reports</a>
        <a href="view_assignment_reports.php">Assignment Reports</a>
        <a href="contact_us.php">Contact Us</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <div class="header">
            <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        </div>

        <div class="form-container">
            <h2>Create Student ID</h2>
            <form action="create_student0.php" method="post">
                <label>Reg. No.</label>
                <input type="text" name="reg_no" required>
                
                <label>Name</label>
                <input type="text" name="name" required>
                
                <label>Father's Name</label>
                <input type="text" name="father_name" required>
                
                <label>CNIC</label>
                <input type="text" name="cnic" required>
                
                <label>Email</label>
                <input type="email" name="email" required>
                
                <label>Password</label>
                <input type="password" name="password" required>
                
                <label>Date of Birth</label>
                <input type="date" name="dob" required>
                
                <label>Gender</label>
                <select name="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>

                <label for="degree">Degree</label>
                <select id="degree" name="degree" required>
                    <option value="">Select Degree</option>
                    <?php
                    require_once '../db_connect.php';
                    $degrees = $conn->query("SELECT degree_id, name FROM degrees");
                    if (!$degrees) {
                        die("Query failed: " . $conn->error);
                    }
                    while ($row = $degrees->fetch_assoc()) {
                        echo "<option value='{$row['degree_id']}'>{$row['name']}</option>";
                    }
                    ?>
                </select>

                <label for="semester">Semester</label>
                <select id="semester" name="semester" required>
                    <option value="">Select Semester</option>
                </select>

                <label for="section">Section</label>
                <select id="section" name="section" required>
                    <option value="">Select Section</option>
                </select>


                <button type="submit">Create Student ID</button>
            </form>
        </div>
    </div>
    <script>
         $(document).ready(function() {
            // Load semesters dynamically when degree is selected
            $('#degree').change(function() {
                var degree_id = $(this).val();
                $.post('fetch_semesters.php', { degree_id: degree_id }, function(data) {
                    $('#semester').html(data);
                    $('#section').html('<option value="">Select Section</option>'); // reset section
                });
            });

            // Load sections dynamically when semester is selected
            $('#semester').change(function() {
                var semester_id = $(this).val();
                $.post('fetch_sections.php', { semester_id: semester_id }, function(data) {
                    $('#section').html(data);
                });
            });
        });
    </script>
</body>
</html>