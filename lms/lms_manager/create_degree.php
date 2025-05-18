<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Degree</title>
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
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        label, select, input, button {
            display: block;
            margin: 12px 0;
            width: 100%;
        }

        input, select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #004080;
            color: white;
            padding: 12px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            transition: 0.3s;
        }
        button:hover {
            background-color: #0066cc;
        }
    </style>
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
        
        <h2>Manage Degrees</h2>
        
        <div class="form-container">
            <label for="manageOption">Select what to manage:</label>
            <select id="manageOption" onchange="showFields()">
                <option value="">Select</option>
                <option value="degree">Add Degree Program</option>
                <option value="semester">Add Semester</option>
                <option value="section">Add Section</option>
            </select>

            <div id="degreeFields" style="display: none;">
                <label for="degreeName">Degree Name:</label>
                <input type="text" id="degreeName">
                <button onclick="addDegree()">Add Degree</button>
            </div>

            <div id="semesterFields" style="display: none;">
                <label for="degreeSelect">Select Degree:</label>
                <select id="degreeSelect"></select>
                <label for="semesterName">Semester:</label>
                <input type="text" id="semesterName">
                <button onclick="addSemester()">Add Semester</button>
            </div>

            <div id="sectionFields" style="display: none;">
                <label for="degreeSelectSec">Select Degree:</label>
                <select id="degreeSelectSec"></select>
                <label for="semesterSelect">Select Semester:</label>
                <select id="semesterSelect"></select>
                <label for="sectionName">Section Name:</label>
                <input type="text" id="sectionName">
                <button onclick="addSection()">Add Section</button>
            </div>
        </div>
    </div>

    <script>
function showFields() {
    document.getElementById("degreeFields").style.display = "none";
    document.getElementById("semesterFields").style.display = "none";
    document.getElementById("sectionFields").style.display = "none";

    let selectedOption = document.getElementById("manageOption").value;
    if (selectedOption) {
        document.getElementById(selectedOption + "Fields").style.display = "block";
    }
}


function postData(action, data, callback) {
    data.action = action;

    fetch('create_degree0.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams(data)
    })
    .then(response => response.json())
    .then(callback)
    .catch(error => alert("Error: " + error));
}

function addDegree() {
    const degreeName = document.getElementById("degreeName").value;
    if (!degreeName) return alert("Please enter a degree name.");
    
    postData('addDegree', { degreeName }, (response) => {
        alert(response.message);
        loadDegrees(); // Reload the dropdowns
    });
}

function addSemester() {
    const degreeId = document.getElementById("degreeSelect").value;
    const semesterName = document.getElementById("semesterName").value;
    if (!degreeId || !semesterName) return alert("Please select a degree and enter semester name.");

    postData('addSemester', { degreeId, semesterName }, (response) => {
        alert(response.message);
        loadSemesters(); // Reload if needed
    });
}

function addSection() {
    const degreeId = document.getElementById("degreeSelectSec").value;
    const semesterId = document.getElementById("semesterSelect").value;
    const sectionName = document.getElementById("sectionName").value;
    if (!degreeId || !semesterId || !sectionName) return alert("Please fill in all section details.");

    postData('addSection', { degreeId, semesterId, sectionName }, (response) => {
        alert(response.message);
    });
}

function loadDegrees() {
    fetch('get_degrees.php')
        .then(res => res.json())
        .then(degrees => {
            const selects = [document.getElementById("degreeSelect"), document.getElementById("degreeSelectSec")];
            selects.forEach(select => {
                select.innerHTML = '<option value="">Select Degree</option>';
                degrees.forEach(degree => {
                    const option = document.createElement("option");
                    option.value = degree.degree_id;
                    option.textContent = degree.name;
                    select.appendChild(option);
                });
            });
        });
}

function loadSemesters() {
    const degreeId = document.getElementById("degreeSelectSec").value;
    if (!degreeId) return;

    fetch(`get_semesters.php?degree_id=${degreeId}`)
        .then(res => res.json())
        .then(semesters => {
            const semesterSelect = document.getElementById("semesterSelect");
            semesterSelect.innerHTML = '<option value="">Select Semester</option>';
            semesters.forEach(sem => {
                const option = document.createElement("option");
                option.value = sem.semester_id;
                option.textContent = sem.name;
                semesterSelect.appendChild(option);
            });
        });
}

// Load degrees when the page loads
window.onload = function () {
    loadDegrees();

    // Load semesters when a degree is selected in section form
    document.getElementById("degreeSelectSec").addEventListener("change", loadSemesters);
}
</script>

</body>
</html>