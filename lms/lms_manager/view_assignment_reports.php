<?php
    require_once "../db_connect.php";
    require_once "../auth.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Reports</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .filter-container, .table-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        label, select, input, button {
            display: block;
            margin: 10px 0;
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

        .search-bar {
            margin-bottom: 10px;
            text-align: center;
        }

        .search-bar input {
            padding: 10px;
            width: 50%;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #004080;
            color: white;
        }
        .chart-container {
            display: flex;
            justify-content: center;
            align-items: center; 
            height: 100vh; 
        }
        @media (max-width: 768px) {
            .search-bar input {
                width: 90%;
            }

            .sidebar {
                width: 100px;
                padding: 10px;
            }

            .content {
                margin-left: 100px;
                padding: 10px;
            }

            canvas {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
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
        
        <h2>Assignment Reports</h2>

        <div class="filter-container">
            <label for="degreeSelect">Select Degree:</label>
            <select id="degreeSelect"></select>
            <label for="semesterSelect">Select Semester:</label>
            <select id="semesterSelect"></select>
            <label for="sectionSelect">Select Section:</label>
            <select id="sectionSelect"></select>
            <button onclick="filterAssignments()">Filter</button>
        </div>

        <div class="chart-container">
            <canvas id="assignmentPieChart" width="400" height="200"></canvas>
        </div>
        
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search assignments...">
        </div>


        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Reg. No.</th>
                        <th>Degree</th>
                        <th>Semester</th>
                        <th>Section</th>
                        <th>Assignment Title</th>
                        <th>Submission Status</th>
                    </tr>
                </thead>
                <tbody id="assignmentTable">
                    <!-- Filtered data will be inserted here dynamically -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            loadDegrees();

            document.getElementById("degreeSelect").addEventListener("change", function () {
                const degreeId = this.value;
                loadSemesters(degreeId);
            });

            document.getElementById("semesterSelect").addEventListener("change", function () {
                const semesterId = this.value;
                loadSections(semesterId);
            });

            document.getElementById("searchInput").addEventListener("input", function () {
                let filter = this.value.toLowerCase();
                document.querySelectorAll("#assignmentTable tr").forEach(row => {
                    let text = row.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? "" : "none";
                });
            });
        });

        function loadDegrees() {
            fetch("get_degrees.php")
                .then(res => res.json())
                .then(data => {
                    const degreeSelect = document.getElementById("degreeSelect");
                    degreeSelect.innerHTML = '<option value="">-- Select Degree --</option>';
                    data.forEach(degree => {
                        degreeSelect.innerHTML += `<option value="${degree.degree_id}">${degree.name}</option>`;
                    });
                });
        }

        function loadSemesters(degreeId) {
            fetch(`get_semesters_by_degree.php?degree_id=${degreeId}`)
                .then(res => res.json())
                .then(data => {
                    const semesterSelect = document.getElementById("semesterSelect");
                    semesterSelect.innerHTML = '<option value="">-- Select Semester --</option>';
                    data.semesters.forEach(sem => {
                        semesterSelect.innerHTML += `<option value="${sem.semester_id}">${sem.name}</option>`;
                    });
                });
        }

        function loadSections(semesterId) {
            fetch(`get_sections_by_semester.php?semester_id=${semesterId}`)
                .then(res => res.json())
                .then(data => {
                    const sectionSelect = document.getElementById("sectionSelect");
                    sectionSelect.innerHTML = '<option value="">-- Select Section --</option>';
                    data.sections.forEach(sec => {
                        sectionSelect.innerHTML += `<option value="${sec.section_id}">${sec.name}</option>`;
                    });
                });
        }

        
        let assignmentChart = null; // global chart reference
        function filterAssignments() {
            const degree = document.getElementById("degreeSelect").value;
            const semester = document.getElementById("semesterSelect").value;
            const section = document.getElementById("sectionSelect").value;

            if (!degree && !semester && !section) {
                alert("Please select at least one Field.");
                return;
            }

            fetch("fetch_assignment_reports.php", {
                method: "POST",
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `degree_id=${degree || ''}&semester_id=${semester || ''}&section_id=${section || ''}`
            })
            .then(res => res.json())
            .then(data => {
                // Fill table
                const table = document.getElementById('assignmentTable');
                table.innerHTML = '';
                data.forEach(row => {
                    table.innerHTML += `<tr>
                        <td>${row.name}</td>
                        <td>${row.reg}</td>
                        <td>${row.degree}</td>
                        <td>${row.semester}</td>
                        <td>${row.section}</td>
                        <td>${row.title}</td>
                        <td>${row.status}</td>
                    </tr>`;
                });

        // Count statuses
        let counts = { Pass: 0, Fail: 0, 'Not Graded': 0 };
        data.forEach(row => {
            const status = row.status;
            console.log('Assignment status:', status); // Check actual status values
            if (status === 'Pass') {
                counts.Pass++;
            } else if (status === 'Fail') {
                counts.Fail++;
            } else if (status === 'Not Graded') {
                counts['Not Graded']++;
            } else {
                console.log('Unknown status:', status); // Log any unknown status
            }
        });

        // Update chart
        const ctx = document.getElementById('assignmentPieChart').getContext('2d');
        if (assignmentChart) assignmentChart.destroy(); // destroy previous chart

        assignmentChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Pass', 'Fail', 'Not Graded'],
                datasets: [{
                    data: [counts.Pass, counts.Fail, counts['Not Graded']],
                    backgroundColor: ['#28a745', '#dc3545', '#ffc107']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Assignment Submission Status'
                    }
                }
            }
        });
    })
    .catch(err => {
        console.error('Error fetching assignment data:', err);
    });
}


        document.getElementById('searchInput').addEventListener('input', function () {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#assignmentTable tr');

            rows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    </script>

</body>
</html>