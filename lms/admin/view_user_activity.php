<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User Activity</title>
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
    </style>
</head>
<body>
    <?php  
        require_once '../db_connect.php';
        require_once '../auth.php';
    ?>
    <div class="sidebar">
        <a href="dashboard.php">Dashboard</a>
        <a href="reset_password.php">Reset User Password</a>
        <a href="manage_lms_managers.php">Manage LMS Managers</a>
        <a href="manage_course_coordinators.php">Manage Course Coordinators</a>
        <a href="manage_students.php">Manage Students</a>
        <a href="manage_instructors.php">Manage Instructors</a>
        <a href="view_user_activity.php">User Activity</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <div class="header">
            <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        </div>

        <h2>View User Activity</h2> <br>

        <div class="filter-container">
            <label for="roleSelect">Select Role:</label>
            <select id="roleSelect">
                <option value="all">All</option>
                <option value="Admin">Admin</option>
                <option value="lms_manager">LMS Manager</option>
                <option value="course_coordinator">Course Coordinator</option>
                <option value="Instructor">Instructor</option>
                <option value="Student">Student</option>
            </select>
            <label for="userName">Email:</label>
            <input type="text" id="userName">
            <label for="activityDate">Select Date:</label>
            <input type="date" id="activityDate">
            <button onclick="filterActivity()">Filter</button>
        </div>

        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search user activity...">
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Sr. No.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Activity</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>IP Address</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody id="activityTable">
                    <!-- Filtered data will be inserted here dynamically -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
       function filterActivity() {
    const role = document.getElementById('roleSelect').value;
    const regNo = document.getElementById('userName').value;
    const date = document.getElementById('activityDate').value;

    const params = new URLSearchParams({ role, regNo, date });

    fetch('view_user_activity0.php?' + params.toString())
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById("activityTable");
            tableBody.innerHTML = "";

            if (data.length === 0) {
                tableBody.innerHTML = "<tr><td colspan='6'>No activity found.</td></tr>";
                return;
            }
            let srno = 0;
            data.forEach(item => {
                let row = `<tr>
                    <td>${++srno}</td>
                    <td>${item.username}</td>
                    <td>${item.user_id}</td>
                    <td>${item.role}</td>
                    <td>${item.action}</td>
                    <td>${item.activity_date}</td>
                    <td>${item.activity_time}</td>
                    <td>${item.ip_address}</td>
                    <td><button onclick="deleteActivity(${item.activity_id})">Delete</button></td>
                </tr>`;
                tableBody.innerHTML += row;
            });
        })
        .catch(error => {
        console.error("Fetch error:", error);
        document.getElementById("activityTable").innerHTML =
            "<tr><td colspan='6'>Error loading activity data.</td></tr>";
    });
}

        document.getElementById('searchInput').addEventListener('input', function () {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#activityTable tr');

            rows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
        window.addEventListener('DOMContentLoaded', () => {
            filterActivity(); // Load all activities initially
        });

        function deleteActivity(activityId) {
    if (!confirm("Are you sure you want to delete this activity?")) return;

    fetch('delete_user_activity.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: activityId })
    })
    .then(response => response.text())
    .then(result => {
        alert(result);
        filterActivity(); // Refresh the table
    })
    .catch(error => {
        console.error("Delete error:", error);
        alert("An error occurred while deleting the activity.");
    });
}
    </script>
</body>
</html>
