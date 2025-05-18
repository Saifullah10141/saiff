<?php
    require_once "../db_connect.php";
    require_once "../auth.php";
    require_once "view_delete_courses0.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View & Delete Courses</title>
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

        .table-container {
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 5px;
        }

        .search-container {
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
        }

        .search-container input {
            width: 80%;
            padding: 8px;
            border: 1px solid #ddd;
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

        .delete-btn {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        .delete-btn:hover {
            background-color: darkred;
        }

        #total-courses {
            font-weight: bold;
            margin-top: 10px;
        }
        /* Modal Styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
        }

        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        /* Close Button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Form Styling */
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: green;
        }

        /* Table and other UI */
        .table-container {
            margin-top: 20px;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th,
        table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .edit-btn {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .edit-btn:hover {
            background-color: green;
        }
    </style>
</head>
<body>
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
            <h2>Welcome, <span id="username"><?php echo $_SESSION['username']; ?></span>!</h2>
        </div>

        <div class="table-container">
            <h3>Enrolled Courses</h3>
            <div class="search-container">
                <input type="text" id="searchBox" onkeyup="searchCourses()" placeholder="Search for courses...">
            </div>
            <table>
                <p id="total-courses">Total Courses: <span id="course-count"><?php echo count($courses); ?></span></p>
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Course Code</th>
                        <th>Credit Hours (Theory)</th>
                        <th>Credit Hours (Practical)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="course-list">
                    <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?php echo $course['course_name']; ?></td>
                        <td><?php echo $course['course_id']; ?></td>
                        <td><?php echo $course['credit_hours_theory']; ?></td>
                        <td><?php echo $course['credit_hours_practical']; ?></td>
                        <td><button class="edit-btn" onclick="openEditModal('<?php echo $course['course_id']; ?>')">Edit</button></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Course Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h3>Edit Course</h3> <br>
            <form id="editCourseForm" method="POST" action="update_course.php">
                <input type="hidden" name="course_id" id="modalCourseId">
                <label for="course_name">Course Name:</label>
                <input type="text" name="course_name" id="modalCourseName" required>
                
                <label for="course_code">Course Code:</label>
                <input type="text" name="course_code" id="modalCourseCode" required>
                
                <label for="credit_hours_theory">Credit Hours (Theory):</label>
                <input type="number" name="credit_hours_theory" id="modalCreditHoursTheory" required>
                
                <label for="credit_hours_practical">Credit Hours (Practical):</label>
                <input type="number" name="credit_hours_practical" id="modalCreditHoursPractical" required>
                
                <button type="submit">Update Course</button>
            </form>
        </div>
    </div>

    <script>
    // Open Edit Modal and populate it with course details
    function openEditModal(courseId) {
        // Fetch the course data based on the courseId
        fetchCourseDetails(courseId);
        
        // Open the modal
        document.getElementById('editModal').style.display = 'block';
    }

    // Close Edit Modal
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Function to fetch course details via Ajax and populate the modal
    function fetchCourseDetails(courseId) {
        // Send a request to get the course details
        fetch('get_course_details.php?course_id=' + courseId)
            .then(response => response.json())
            .then(data => {
                // Populate the modal fields with the fetched data
                document.getElementById('modalCourseId').value = data.course_id;
                document.getElementById('modalCourseName').value = data.course_name;
                document.getElementById('modalCourseCode').value = data.course_code;
                document.getElementById('modalCreditHoursTheory').value = data.credit_hours_theory;
                document.getElementById('modalCreditHoursPractical').value = data.credit_hours_practical;
            })
            .catch(error => {
                console.error('Error fetching course details:', error);
            });
    }

    // Function to handle the course update submission
    document.getElementById('editCourseForm').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent default form submission

        // Prepare form data
        const formData = new FormData(this);

        // Send the data to the server using Fetch
        fetch('update_course.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Course updated successfully!');
                closeEditModal(); // Close the modal after successful update
                // Optionally, you can reload the courses or update the course list dynamically
            } else {
                alert('Error updating course. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error updating course:', error);
        });
    });

    // Search functionality for courses
    function searchCourses() {
        let input = document.getElementById("searchBox").value.toLowerCase();
        let table = document.getElementById("course-list");
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