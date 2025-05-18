<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Content</title>
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

        .week {
            background-color: #f4f4f4;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .edit-button, .save-button {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
        }

        .save-button {
            display: none;
            background-color: #007bff;
        }
        label { font-weight: bold; }
        select{ width: 50%; padding: 8px; margin-top: 5px; font-size: 16px; border-radius: 5px; }
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

        <label for="subject" style="font-size:20px;">Select Course:</label>
        <select id="subject" onchange="showWeeks()" style="font-size:20px;"></select>
        </select>

        <div id="weeks-container" style="display: none; margin-top: 20px;">
            <h2>Course Content</h2>
            <div id="weeks"></div>
        </div>
    </div>

    <script>
       // Load subjects on page load
window.onload = function () {
    fetch('get_assigned_subjects.php')
        .then(res => res.json())
        .then(data => {
            let subjectSelect = document.getElementById("subject");
            subjectSelect.innerHTML = '<option value="">Select Course</option>';
            data.forEach(subject => {
                subjectSelect.innerHTML += `<option value="${subject.assignment_id}">${subject.name}</option>`;
            });
        });
};

function showWeeks() {
    let assignmentId = document.getElementById("subject").value;
    let weeksContainer = document.getElementById("weeks-container");
    let weeksDiv = document.getElementById("weeks");

    if (assignmentId) {
        weeksContainer.style.display = "block";
        weeksDiv.innerHTML = "";

        // Get existing course contents
        fetch(`get_course_content.php?assignment_id=${assignmentId}`)
            .then(res => res.json())
            .then(contentData => {
                for (let i = 1; i <= 8; i++) {
                    let content = contentData[i] || `Content for Week ${i}`;
                    weeksDiv.innerHTML += `
                        <div class='week'>
                            <h3>Week ${i}</h3> <br>
                            <p id='text${i}'>${content}</p>
                            <textarea id='edit${i}' style='display: none; width: 100%;'></textarea> <br>
                            <button class='edit-button' onclick='editWeek(${i})'>Edit</button>
                            <button class='save-button' id='save${i}' onclick='saveWeek(${i})'>Save Changes</button>
                        </div>
                    `;
                }
            });
    } else {
        weeksContainer.style.display = "none";
    }
}


        function editWeek(week) {
            document.getElementById(`text${week}`).style.display = "none";
            document.getElementById(`edit${week}`).style.display = "block";
            document.getElementById(`edit${week}`).value = document.getElementById(`text${week}`).innerText;
            document.getElementById(`save${week}`).style.display = "inline-block";
        }

        function saveWeek(week) {
    let newText = document.getElementById(`edit${week}`).value;
    let assignmentId = document.getElementById("subject").value;

    fetch("save_course_content.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `assignment_id=${assignmentId}&week=${week}&content=${encodeURIComponent(newText)}`
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById(`text${week}`).innerText = newText;
        document.getElementById(`text${week}`).style.display = "block";
        document.getElementById(`edit${week}`).style.display = "none";
        document.getElementById(`save${week}`).style.display = "none";
        alert(data);
    })
    .catch(error => {
        console.error("Error:", error);
        alert("An error occurred while saving.");
    });
}

    </script>
</body>
</html>