<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View and Manage Students</title>
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
            height: 100%;
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
    <?php
        require_once "../db_connect.php";
        require_once "../auth.php";
        require_once "view_students0.php"
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
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        </div>

        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search Students...">
        </div>

        <div class="table-container">
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
                        <th>Degree</th>
                        <th>Semester</th>
                        <th>Section</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="student-list">
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr data-id="<?= $row['registration_number']; ?>"> <!-- Add data-id attribute -->
                            <td><?= $row['registration_number']; ?></td>
                            <td><?= $row['name']; ?></td>
                            <td><?= $row['father_name']; ?></td>
                            <td><?= $row['cnic']; ?></td>
                            <td><?= $row['email']; ?></td>
                            <td><?= $row['dob']; ?></td>
                            <td><?= $row['gender']; ?></td>
                            <td><?= $row['faculty_name']; ?></td>
                            <td><?= $row['department_name']; ?></td>
                            <td><?= $row['degree_name']; ?></td>
                            <td><?= $row['semester_name']; ?></td>
                            <td><?= $row['section_name']; ?></td>
                            <td class="action-buttons">
                                <button class="edit-btn" onclick="openEditModal(this)">Edit</button>
                                <button class="delete-btn" onclick="deleteStudent(this)">Delete</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
    document.getElementById('searchInput').addEventListener('input', function () {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#student-list tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    function openEditModal(button) {
        const row = button.closest('tr');
        const cells = row.querySelectorAll('td');
        const studentId = row.dataset.id; // Get the student ID from data-id
        const studentData = Array.from(cells).slice(0, 12).map(td => td.textContent);

        fetch('get_dropdown_data.php')
            .then(res => res.json())
            .then(data => {
                const modalHtml = `
                <div class="modal" id="editModal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal()">&times;</span>
                        <h3>Edit Student</h3>
                        <form>
                            <input type="hidden" name="id" value="${studentId}">
                            <label>Reg. No.</label>
                            <input type="text" value="${studentData[0]}" readonly>
                            <label>Name</label>
                            <input type="text" value="${studentData[1]}" name="name">
                            <label>Father's Name</label>
                            <input type="text" value="${studentData[2]}" name="father_name">
                            <label>CNIC</label>
                            <input type="text" value="${studentData[3]}" name="cnic">
                            <label>Email</label>
                            <input type="email" value="${studentData[4]}" readonly name="email">
                            <label>DOB</label>
                            <input type="date" value="${studentData[5]}" name="dob">
                            <label>Gender</label>
                            <select name="gender">
                                <option value="Male" ${studentData[6] === 'Male' ? 'selected' : ''}>Male</option>
                                <option value="Female" ${studentData[6] === 'Female' ? 'selected' : ''}>Female</option>
                                <option value="Other" ${studentData[6] === 'Other' ? 'selected' : ''}>Other</option>
                                <!-- Add more options if needed -->
                            </select>
                            <label>Degree</label>
                            <select name="degree" id="degree-select" onchange="loadSemesters()">
                                ${data.degrees.map(dg => `<option value="${dg.id}" ${studentData[7] === dg.name ? 'selected' : ''}>${dg.name}</option>`).join('')}
                            </select>
                            <label>Semester</label>
                            <select name="semester" id="semester-select" onchange="loadSections()">
                                <!-- Semester options will be loaded based on degree selection -->
                            </select>
                            <label>Section</label>
                            <select name="section" id="section-select">
                                <!-- Section options will be loaded based on semester selection -->
                            </select>
                            <button type="button" onclick="saveChanges()">Save Changes</button>
                        </form>
                    </div>
                </div>`;
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                document.getElementById('editModal').style.display = 'block';
                loadSemesters();
                loadSections();
            });
    }


    function closeModal() {
        const modal = document.getElementById('editModal');
        if (modal) modal.remove();
    }

    function loadSemesters() {
        const degreeId = document.querySelector('[name="degree"]').value;
        fetch(`get_semesters_by_degree.php?degree_id=${degreeId}`)
            .then(res => res.json())
            .then(data => {
                const semesterSelect = document.getElementById('semester-select');
                semesterSelect.innerHTML = data.semesters.map(s => `<option value="${s.semester_id}">${s.name}</option>`).join('');
            });
            setTimeout(() => {
                loadSections();
            }, 100);
    }

    function loadSections() {
        
        const semesterId = document.querySelector('[name="semester"]').value;
        fetch(`get_sections_by_semester.php?semester_id=${semesterId}`)
            .then(res => res.json())
            .then(data => {
                const sectionSelect = document.getElementById('section-select');
                sectionSelect.innerHTML = data.sections.map(sec => `<option value="${sec.section_id}">${sec.name}</option>`).join('');
            });
    }

    function saveChanges() {
        const formData = new FormData(document.querySelector('#editModal form'));
        fetch('save_student_changes.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text())
        .then(response => {
            if (response === 'success') {
                alert('Student information updated successfully.');
                closeModal();
            } else {
                alert('Error updating student.');
            }
        });
    }

    function deleteStudent(button) {
    if (!confirm("Are you sure you want to delete this student?")) return;

    const row = button.closest('tr');
    const registrationNumber = row.dataset.id;

    fetch('delete_student.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${encodeURIComponent(registrationNumber)}`
    })
    .then(res => res.text())
    .then(response => {
        if (response === 'success') {
            alert('Student deleted successfully.');
            row.remove();
        } else {
            alert('Error deleting student.');
        }
    });
}

</script>

</body>
</html>