<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll Course</title>
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
            width: 50%;
            margin: auto;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        input, button {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
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
    </style>
</head>
<body>
    <?php
        require_once "../db_connect.php";
        require_once "../auth.php";
    ?>
    <div class="sidebar">
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_courses.php">Manage Courses</a>
        <a href="manage_students.php">Manage Students</a>
        <a href="manage_instructors.php">Manage Instructors</a>
        <a href="contact_us.php">Contact Us</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <div class="header">
            <h2>Welcome, <span id="username"><?= htmlspecialchars($username) ?></span>!</h2>
        </div>

        <div class="form-container">
            <h3>Enroll New Course</h3>
            <label for="course-name">Course Title:</label>
            <input type="text" id="course-name" placeholder="Enter course name" required>

            <label for="course-code">Course Code:</label>
            <input type="text" id="course-code" placeholder="Enter course code" required>

            <label for="credit-hours-theory">Credit Hours (Theory):</label>
            <input type="number" id="credit-hours-theory" placeholder="Enter theory credit hours" required>

            <label for="credit-hours-practical">Credit Hours (Practical):</label>
            <input type="number" id="credit-hours-practical" placeholder="Enter practical credit hours" required>


            <button onclick="enrollCourse()">Enroll Course</button>
        </div>
    </div>

    <script>
       function enrollCourse() {
            let courseName = document.getElementById("course-name").value.trim();
            let courseCode = document.getElementById("course-code").value.trim();
            let creditTheory = document.getElementById("credit-hours-theory").value;
            let creditPractical = document.getElementById("credit-hours-practical").value;

            console.log("courseName:", courseName);
            console.log("courseCode:", courseCode);
            console.log("creditTheory:", creditTheory);
            console.log("creditPractical:", creditPractical);

            // Use strict undefined/null check instead of falsy check
            if (
                courseName.length > 0 &&
                courseCode.length > 0 &&
                creditTheory !== null &&
                creditTheory !== "" &&
                creditPractical !== null &&
                creditPractical !== ""
            ) {
                let formData = new FormData();
                formData.append('course_name', courseName);
                formData.append('course_code', courseCode);
                formData.append('credit_hours_theory', creditTheory);
                formData.append('credit_hours_practical', creditPractical);

                fetch('enroll_course0.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') {
                        window.location.href = "course_enrolled.php";
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Something went wrong!');
                });
            } else {
                alert("Please fill in all fields.");
            }
        }

    </script>
</body>
</html>
