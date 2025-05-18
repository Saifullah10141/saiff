<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { display: flex; height: 100vh; background-color: white; font-family: Arial, sans-serif; }
        .sidebar { width: 250px; background-color: #003366; padding: 20px; display: flex; flex-direction: column; height: 100vh; position: fixed; }
        .sidebar a { color: white; text-decoration: none; padding: 10px; margin: 10px 0; background-color: #004080; text-align: center; border-radius: 5px; }
        .sidebar a:hover { background-color: #0066cc; }
        .content { flex: 1; padding: 20px; margin-left: 250px; }
        .header { background-color: #2980b9; color: white; padding: 15px; text-align: center; border-radius: 5px; margin-bottom: 20px; }
        .form-container { background-color: #f4f4f4; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        select, input, button { width: 100%; padding: 8px; margin-top: 5px; border-radius: 5px; }
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
            <h3>Manage Students</h3>
            <label>Select Course:</label>
            <select id="course"><option value="">Select Course</option></select>
            <label>Enroll New Student :</label>
            <input type="text" id="registration" placeholder="Enter Registration No">
            <button onclick="enrollStudent()">Enroll Student</button>
        </div>
        
        <div class="table-container">
            <h3>Enrolled Students</h3>
            <table>
                <thead>
                    <tr><th>Registration No</th><th>Name</th><th>Email</th><th>Action</th></tr>
                </thead>
                <tbody id="student-list">
                </tbody>
            </table>
        </div>
    </div>

    <script>
       document.addEventListener('DOMContentLoaded', function() {
    loadCourses();
});

function loadCourses() {
    fetch('get_assigned_courses.php')
    .then(response => response.json())
    .then(data => {
        const courseSelect = document.getElementById('course');
        courseSelect.innerHTML = '<option value="">Select Course</option>';
        data.courses.forEach(course => {
            const option = document.createElement('option');
            option.value = course.course_id; // Keep course_id as value (optional)
            option.dataset.assignmentId = course.assignment_id; // <-- store assignment_id
            option.textContent = course.name;
            courseSelect.appendChild(option);
        });
    })
    .catch(error => console.error('Error loading courses:', error));
}


// Load enrolled students when a course is selected
document.getElementById('course').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex]; // Get selected <option>
    const assignmentId = selectedOption.dataset.assignmentId; // Get assignment_id from data attribute

    if (assignmentId) {
        loadEnrolledStudents(assignmentId); // <-- Pass assignment ID now
    } else {
        document.getElementById('student-list').innerHTML = ''; // Clear list if no course selected
    }
});


// Fetch enrolled students for the selected course
function loadEnrolledStudents(courseId) {
    fetch('manage_students0.php?course_id=' + courseId)
    .then(response => response.json())
    .then(data => {
        const studentList = document.getElementById('student-list');
        studentList.innerHTML = ''; // Clear existing list

            data.students.forEach(student => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${student.registration_number}</td>
                    <td>${student.name}</td>
                    <td>${student.email}</td>
                    <td><button class="delete-btn" onclick="deleteStudent(this)">Delete</button></td>
                `;
                studentList.appendChild(row);
            });
        
    })
    .catch(error => console.error('Error loading enrolled students:', error));
}


function enrollStudent() {
    // Get the selected option which holds the assignment_id in data-assignment-id
    let selectedOption = document.getElementById("course").options[document.getElementById("course").selectedIndex];
    let assignmentId = selectedOption.dataset.assignmentId;  // Now using assignment_id, not course_id
    let registration = document.getElementById("registration").value;

    if (!assignmentId || !registration) {
        alert("Please fill in all fields.");
        return;
    }

    let formData = new FormData();
    formData.append('assignment_id', assignmentId);  // Send assignment_id, not course_id
    formData.append('registration', registration);

    fetch('manage_students0.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.success || data.error);
        if (data.success) {
            loadEnrolledStudents(assignmentId); // Pass assignment_id to reload students
        }
    })
    .catch(error => console.error('Error enrolling student:', error));
}

function deleteStudent(button) {
    if (confirm("Are you sure you want to delete this student?")) {
        let row = button.parentNode.parentNode;
        let studentId = row.cells[0].textContent;  // Assuming student ID is in the first column
        let selectedOption = document.getElementById("course").options[document.getElementById("course").selectedIndex];
        let assignmentId = selectedOption.dataset.assignmentId;  // Now using assignment_id, not course_id

        let formData = new FormData();
        formData.append('student_id', studentId);  // Send the student ID
        formData.append('assignment_id', assignmentId);  // Send the assignment ID

        fetch('delete_student.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                row.parentNode.removeChild(row);  // Remove the row from the table
                alert("Student deleted successfully!");
            } else {
                alert(data.error || 'Failed to delete student');
            }
        })
        .catch(error => console.error('Error deleting student:', error));
    }
}


    </script>
</body>
</html>
