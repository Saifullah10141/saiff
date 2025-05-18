<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View and delete Degrees</title>
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
        .content { flex: 1; padding: 20px; margin-left: 250px; }
        .header {
            background-color: #2980b9;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .form-container, .table-container { background-color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        select, button { display: block; margin: 12px 0; width: 100%; padding: 10px; border-radius: 5px; }
        button { background-color: #004080; color: white; cursor: pointer; transition: 0.3s; }
        button:hover { background-color: #0066cc; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #004080; color: white; }
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
        <div class="form-container">
            <label>Select Degree:</label>
            <select id="degreeSelect" onchange="updateSemesters()"></select>

            <label>Select Semester:</label>
            <select id="semesterSelect" onchange="updateSections()"></select>

            <label>Select Section:</label>
            <select id="sectionSelect"></select>

            <button onclick="showTable()">Show Table</button>
        </div>

        <div class="table-container">
            <table>
                <tbody id="selectedData"></tbody>
            </table>
        </div>
    </div>

    <script>
        function loadDegrees() {
            fetch('view_degrees0.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=getDegrees'
            })
            .then(res => res.json())
            .then(data => {
                let options = data.degrees.map(d => `<option value="${d.degree_id}">${d.name}</option>`);
                document.getElementById("degreeSelect").innerHTML = options.join('');
                updateSemesters();
            });
        }

        function updateSemesters() {
            const degreeId = document.getElementById("degreeSelect").value;
            fetch('view_degrees0.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=getSemesters&degreeId=${degreeId}`
            })
            .then(res => res.json())
            .then(data => {
                let options = data.semesters.map(s => `<option value="${s.semester_id}">${s.name}</option>`);
                document.getElementById("semesterSelect").innerHTML = options.join('');
                updateSections();
            });
        }

        function updateSections() {
            const degreeId = document.getElementById("degreeSelect").value;
            const semesterId = document.getElementById("semesterSelect").value;
            fetch('view_degrees0.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=getSections&degreeId=${degreeId}&semesterId=${semesterId}`
            })
            .then(res => res.json())
            .then(data => {
                let options = data.sections.map(s => `<option value="${s.section_id}">${s.name}</option>`);
                document.getElementById("sectionSelect").innerHTML = options.join('');
            });
        }

        function showTable() {
            let degreeSelect = document.getElementById("degreeSelect");
            let semesterSelect = document.getElementById("semesterSelect");
            let sectionSelect = document.getElementById("sectionSelect");

            let tableHTML = "";

            if (degreeSelect.value) {
                let degreeText = degreeSelect.selectedOptions[0].text;
                let degreeId = degreeSelect.value;
                tableHTML += `<tr><td>Degree: ${degreeText}</td><td><button onclick="deleteEntry('Degree', ${degreeId})">Delete</button></td></tr>`;
            }

            if (semesterSelect.value) {
                let semesterText = semesterSelect.selectedOptions[0].text;
                let semesterId = semesterSelect.value;
                tableHTML += `<tr><td>Semester: ${semesterText}</td><td><button onclick="deleteEntry('Semester', ${semesterId})">Delete</button></td></tr>`;
            }

            if (sectionSelect.value) {
                let sectionText = sectionSelect.selectedOptions[0].text;
                let sectionId = sectionSelect.value;
                tableHTML += `<tr><td>Section: ${sectionText}</td><td><button onclick="deleteEntry('Section', ${sectionId})">Delete</button></td></tr>`;
            }

            if (tableHTML === "") {
                tableHTML = "<tr><td colspan='2'>No data selected.</td></tr>";
            }

            document.getElementById("selectedData").innerHTML = tableHTML;
        }


        function deleteEntry(type, id) {
            if (!confirm(`Are you sure you want to delete this ${type}?`)) return;
            fetch('view_degrees0.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=delete&type=${type}&id=${id}`
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                loadDegrees();
                document.getElementById("selectedData").innerHTML = "";
            });
        }

        window.onload = () => {
            loadDegrees();
        };
    </script>
</body>
</html>
