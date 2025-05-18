<?php
    require_once "../db.php";
    require_once "../auth.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View and Manage LMS Manager</title>
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
            margin-left: 250px; /* Moves content to the right */
        }


        .header {
            background-color: #2980b9;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
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

        .table-container {
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
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

        .action-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-btn {
            background-color: #ffc107;
            color: black;
        }

        .delete-btn {
            background-color: red;
            color: white;
        }

        .modal {
            display: none;
            position: absolute;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            width: 50%;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }

        .close {
            float: right;
            cursor: pointer;
            font-size: 20px;
        }

        .modal-content input {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .modal-content button {
            background-color: #2980b9;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        .modal-content button:hover {
            background-color: #0066cc;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input, select, button {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
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

        <input type="text" id="searchInput" placeholder="Search LMS Managers...">

        <table>
            <thead>
                <tr>
                    <th>Reg. No.</th>
                    <th>Name</th>
                    <th>Father's Name</th>
                    <th>CNIC</th>
                    <th>Email</th>
                    <th>DOB</th>
                    <th>Gender</th>
                    <th>Faculty</th>
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="coordinator-list"></tbody>
        </table>
    </div>

    <div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h3>Edit LMS Manager</h3>
        <form id="editForm">
            <input type="hidden" name="coordinator_id" id="editCoordinatorId">
            <label>Name</label>
            <input type="text" name="name" id="editName" required>
            <label>Father's Name</label>
            <input type="text" name="father_name" id="editFatherName" required>
            <label>CNIC</label>
            <input type="text" name="cnic" id="editCnic" required>
            <label>Email</label>
            <input type="email" name="email" id="editEmail" required readonly>
            <label>DOB</label>
            <input type="date" name="dob" id="editDob" required>
            <label>Gender</label>
            <select name="gender" id="editGender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            <!-- Department Dropdown -->
             <label>Department</label>
            <select id="editDepartment" name="editDepartment" required>
                <option value="">Select Department</option>
                <!-- Department options will be loaded dynamically -->
            </select>

            <button type="submit">Update</button>
        </form>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        loadDepartments(); // Load departments on page load
        loadCoordinators(); // Load coordinators
    });

    // Function to load all departments
    function loadDepartments() {
        fetch("get_all_departments.php")
            .then(res => res.json())
            .then(data => {
                const departmentSelect = document.getElementById("editDepartment");
                departmentSelect.innerHTML = `<option value="">-- Select Department --</option>`;
                data.forEach(department => {
                    departmentSelect.innerHTML += `<option value="${department.name}">${department.name}</option>`;
                });
            })
            .catch(err => {
                console.error("Error fetching departments:", err);
                alert("Error loading departments.");
            });
    }

    // Function to load coordinators
    function loadCoordinators() {
        fetch('get_lms_managers.php')
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('coordinator-list');
                tbody.innerHTML = '';
                data.forEach(row => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${row.manager_id}</td>
                            <td>${row.username}</td>
                            <td>${row.father_name}</td>
                            <td>${row.cnic}</td>
                            <td>${row.user_id}</td>
                            <td>${row.dob}</td>
                            <td>${row.gender}</td>
                            <td>${row.faculty_name}</td>
                            <td>${row.department_name}</td>
                            <td>
                                <button onclick="openEditModal(this)">Edit</button>
                                <button onclick="deleteCoordinator('${row.manager_id}')">Delete</button>
                            </td>
                        </tr>`;
                });
            })
            .catch(err => {
                console.error("Error fetching managers:", err);
                alert("Error loading managers.");
            });
    }

    // Search functionality for coordinators
    document.getElementById('searchInput').addEventListener('input', function () {
        const filter = this.value.toLowerCase();
        document.querySelectorAll('#coordinator-list tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
        });
    });

    // Function to open the edit modal
    function openEditModal(button) {
    const row = button.closest("tr").children;
    document.getElementById("editCoordinatorId").value = row[0].textContent;
    document.getElementById("editName").value = row[1].textContent;
    document.getElementById("editFatherName").value = row[2].textContent;
    document.getElementById("editCnic").value = row[3].textContent;
    document.getElementById("editEmail").value = row[4].textContent;
    document.getElementById("editDob").value = row[5].textContent;
    document.getElementById("editGender").value = row[6].textContent;

    const department = row[8].textContent;
    // Set department value
    document.getElementById("editDepartment").value = department;
    // Set up the modal
    document.getElementById("editModal").style.display = "block";
    }


    // Function to close the edit modal
    function closeEditModal() {
        document.getElementById("editModal").style.display = "none";
    }

    // Handle the form submission for editing coordinators
    document.getElementById("editForm").addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch("update_lms_manager.php", {
            method: "POST",
            body: formData
        })
            .then(res => res.text())
            .then(response => {
                if (response === "updated") {
                    alert("Manager updated successfully!");
                    closeEditModal();
                    loadCoordinators();
                } else {
                    alert("Update failed.");
                    console.error("Update failed:", response);
                }
            });
    });

    // Function to delete a coordinator
    function deleteCoordinator(id) {
        if (confirm("Are you sure you want to delete this LMS Manager?")) {
            fetch('delete_lms_manager.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `reg_no=${encodeURIComponent(id)}`
            })
                .then(res => res.text())
                .then(response => {
                    if (response === 'deleted') {
                        alert("Manager deleted successfully!");
                        loadCoordinators();
                    } else {
                        alert("Failed to delete.");
                    }
                });
        }
    }
</script>
</body>
</html>
