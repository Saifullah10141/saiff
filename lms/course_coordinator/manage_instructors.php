<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Instructors</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { display: flex; height: 100vh; background-color: white; font-family: Arial, sans-serif; }
        .sidebar { width: 250px; background-color: #003366; padding: 20px; display: flex; flex-direction: column; height: 100vh; position: fixed; }
        .sidebar a { color: white; text-decoration: none; padding: 10px; margin: 10px 0; background-color: #004080; text-align: center; border-radius: 5px; }
        .sidebar a:hover { background-color: #0066cc; }
        .content { flex: 1; padding: 20px; margin-left: 250px; }
        .header { background-color: #2980b9; color: white; padding: 15px; text-align: center; border-radius: 5px; margin-bottom: 20px; }
        .form-container { background-color: #f4f4f4; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        label { font-weight: bold; }
        input, select, button { width: 100%; padding: 8px; margin-top: 5px; font-size: 16px; border-radius: 5px; }
        button { background-color: #28a745; color: white; cursor: pointer; border: none; margin-top: 10px; }
        button:hover { background-color: #218838; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #004080; color: white; }
        .delete-btn { background-color: red; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 5px; }
        .delete-btn:hover { background-color: darkred; }
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
            <h3>Assign Instructor</h3>
            <label for="course">Select Course:</label>
            <select id="course" onchange="loadInstructors()">
                <option value="">Select Course</option>
            </select>

            
            <label for="course_name">Course Name:</label>
            <input type="text" id="course_name" placeholder="Enter Course Name">
            
            <label for="instructor_id">Instructor ID: (Format: XXXX-ag-XXXX)</label>
            <input type="text" id="instructor_id" placeholder="Enter Instructor ID">

            <button onclick="assignInstructor()">Assign Instructor</button>
        </div>

        <div class="table-container">
            <h3>Assigned Instructors</h3>
            <div class="search-container">
                <input type="text" id="searchBox" onkeyup="searchCourses()" placeholder="Search for courses...">
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Instructor Name</th>
                        <th>Instructor ID</th>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Department</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="instructor-list">
                   
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadCourses();
            loadInstructors();
        });

    function loadCourses() {
    fetch('get_courses.php')
    .then(response => response.json())
    .then(data => {
        const courseSelect = document.getElementById('course');
        courseSelect.innerHTML = '<option value="">Select Course</option>';
        data.forEach(course => {
            const option = document.createElement('option');
            option.value = course.course_id;
            option.textContent = course.course_id + " - " + course.course_name;
            courseSelect.appendChild(option);
        });
    })
    .catch(error => console.error('Error loading courses:', error));
}


    function assignInstructor() {
        let course = document.getElementById("course").value;
        let courseName = document.getElementById("course_name").value;
        let instructorId = document.getElementById("instructor_id").value;

        if (!course || !courseName || !instructorId) {
            alert("Please fill in all fields.");
            return;
        }

        let formData = new FormData();
        formData.append('course', course);
        formData.append('course_name', courseName);
        formData.append('instructor_id', instructorId);

        fetch('assign_instructor.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status == 'success') {
                loadInstructors();
                document.getElementById("course").value = '';
                document.getElementById("course_name").value = '';
                document.getElementById("instructor_id").value = '';
            }
        });
    }

    function loadInstructors() {
    const selectedCourseId = document.getElementById('course').value;

    let url = 'get_assigned_instructors.php';
    if (selectedCourseId) {
        url += '?course_id=' + encodeURIComponent(selectedCourseId);
    }

    fetch(url)
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        const instructorList = document.getElementById('instructor-list');
        instructorList.innerHTML = '';

        if (data.length === 0) {
            instructorList.innerHTML = '<tr><td colspan="6">No instructors assigned.</td></tr>';
        } else {
            data.forEach(instructor => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${instructor.instructor_name}</td>
                    <td>${instructor.instructor_id}</td>
                    <td>${instructor.course_id}</td>
                    <td>${instructor.course_name}</td>
                    <td>${instructor.department_name}</td>
                    <td><button class="delete-btn" onclick="deleteInstructor(${instructor.assignment_id})">Remove</button></td>
                `;
                instructorList.appendChild(row);
            });
        }
    })
    .catch(error => console.error('Fetch error:', error));
}


    function deleteInstructor(assignmentId) {
        if (confirm("Are you sure you want to remove this instructor?")) {
            let formData = new FormData();
            formData.append('assignment_id', assignmentId);

            fetch('delete_assigned_instructor.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status == 'success') {
                    loadInstructors();
                }
            });
        }
    }

    function searchCourses() {
        let input = document.getElementById("searchBox").value.toLowerCase();
        let table = document.getElementById("instructor-list");
        let rows = table.getElementsByTagName("tr");
        let count = 0;

        for (let i = 0; i < rows.length; i++) {
            let cells = rows[i].getElementsByTagName("td");
            let found = false;
            for (let j = 0; j < cells.length - 1; j++) {
                if (cells[j].innerText.toLowerCase().includes(input)) {
                    found = true;
                    break;
                }
            }
            rows[i].style.display = found ? "" : "none";
            if (found) count++;
        }
        document.getElementById("course-count").innerText = count;
    }
</script>

</body>
</html>