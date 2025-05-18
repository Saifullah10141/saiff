    <?php
        require_once "../db_connect.php";
        require_once "../auth.php";
        require_once "assignment0.php";
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments</title>
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
            color: black;
            font-family: Arial, sans-serif;
        }

        .header {
            background-color: #2980b9;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .sidebar {
            width: 250px;
            background-color: #003366; /* Dark blue sidebar */
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px;
            margin: 10px 0;
            background-color: #004080; /* Blue color for links */
            text-align: center;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background-color: #0066cc; /* Lighter blue on hover */
        }

        .content {
            flex: 1;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }
        #btn {
            padding: 6px 14px;
            background-color: #4CAF50;
            color: white;
            border: none;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        #btn:hover {
            background-color: #45a049;
        }

        .disabled-btn {
            padding: 6px 14px;
            background-color: #cccccc;
            color: #666666;
            border: none;
            border-radius: 4px;
            cursor: not-allowed;
            opacity: 0.6;
            pointer-events: none;
        }
        /* Modal overlay */
            .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s ease-in-out;
            }

            /* Modal box */
            .modal-content {
            background-color: #003366;
            color: #f1f1f1;
            padding: 30px 25px;
            border-radius: 15px;
            width: 450px;
            max-width: 90%;
            position: relative;
            box-shadow: 0 0 15px rgba(0,0,0,0.4);
            animation: slideIn 0.4s ease;
            font-family: 'Segoe UI', sans-serif;
            }

            /* Heading */
            .modal-heading {
            font-size: 24px;
            margin-bottom: 10px;
            }

            /* Description */
            .modal-description {
            font-size: 16px;
            margin-bottom: 20px;
            line-height: 1.5;
            }

            /* Close button */
            .close {
            position: absolute;
            top: 10px; right: 15px;
            font-size: 24px;
            color: #f1f1f1;
            cursor: pointer;
            transition: color 0.3s;
            }
            .close:hover {
            color: #ff5e5e;
            }

            /* File upload */
            .file-label {
            display: block;
            background: #2d2d44;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            cursor: pointer;
            text-align: center;
            font-size: 15px;
            transition: background 0.3s;
            }
            .file-label:hover {
            background: #4e8cff;
            }
            .file-label input[type="file"] {
            display: none;
            }

            /* Submit button */
            .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            }
            .submit-btn:hover {
            background-color: #336bdf;
            }

            /* Animations */
            @keyframes fadeIn {
            from { opacity: 0; } 
            to { opacity: 1; }
            }

            @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; } 
            to { transform: translateY(0); opacity: 1; }
            }



    </style>
</head>
<body>
    <div class="sidebar">
        <a href="dashboard.php">Dashboard</a>
        <a href="/saiff/LMS/student/enroll_subjects.php">Enrolled Subjects</a>
        <a href="/saiff/LMS/student/assignment.php">Assignment</a>
        <a href="/saiff/LMS/student/quiz.php">Quiz</a>
        <a href="/saiff/LMS/student/contact_us.php">Contact Us</a>
        <a href="/saiff/LMS/student/logout.php">Logout</a>
    </div>

    <div class="content">
        <div class="header">
            <h2>Welcome, <span id="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>!</h2>
        </div>

        <h3>Pending Assignments</h3>
        <table>
            <tr>
                <th>Title</th>
                <th>Course Title</th>
                <th>Due Date</th>
                <th>Action</th>
            </tr>
            <?php
            if (count($pending_assignments) > 0) {
                foreach ($pending_assignments as $assignment) {
                    $due_date = strtotime($assignment['due_date'])+75600;
                    $now = time();
                    $disabled = ($now > $due_date) ? "disabled" : "";
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['course_name']); ?></td>
                        <td><?php echo date("F d, Y", strtotime($assignment['due_date'])); ?></td>
                        <td>
                            <?php if ($now <= $due_date) { ?>
                                <button 
                                    class="open-modal-btn" id = "btn"
                                    data-id="<?php echo $assignment['assignment_id']; ?>" 
                                    data-title="<?php echo htmlspecialchars($assignment['title']); ?>" 
                                    data-description="<?php echo htmlspecialchars($assignment['description']); ?>"
                                    data-instructor-file="<?php echo htmlspecialchars($assignment['instructor_file']); ?>"
                                    >Open</button>

                            <?php } else { ?>
                                <button disabled class="disabled-btn" title="Due Date Passed">Due Date Passed</button>
                            <?php } ?>
                        </td>

                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='3'>No pending assignments!</td></tr>";
            }
            ?>
        </table>

        <br>

        <h3>Submitted Assignments</h3>
        <table>
            <tr>
                <th>Title</th>
                <th>Course Title</th>
                <th>Submitted On</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php
            if (count($submitted_assignments) > 0) {
                foreach ($submitted_assignments as $assignment) {
                   // Check conditions
                    $status = strtolower($assignment['status']);
                    $now = time();
                    $due_date = strtotime($assignment['due_date'])+75600;
                    $can_edit = ($status == 'not graded') && ($now <= $due_date);
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['course_name']); ?></td>
                        <td><?php echo date("F d, Y", strtotime($assignment['submitted_on'])); ?></td>
                        <td><?php echo $assignment['status'] ? htmlspecialchars($assignment['status']) : "Not Graded"; ?></td>
                        <td>
                        <?php
                        

                        if ($can_edit) {
                            // If not graded and due date not passed
                            ?>
                            <button 
                                class="open-modal-btn" id = "btn"
                                data-id="<?php echo $assignment['assignment_id']; ?>" 
                                data-title="<?php echo htmlspecialchars($assignment['title']); ?>" 
                                data-description="<?php echo htmlspecialchars($assignment['description']); ?>"
                                data-action="Edit"
                                data-file="<?php echo htmlspecialchars($assignment['file_path']); ?>"
                                data-instructor-file="<?php echo htmlspecialchars($assignment['instructor_file']); ?>"
                                >Edit</button>

                            <?php
                        } else {
                            // Disabled button with reason
                            $tooltip = ($now > $due_date) ? "Due Date Passed" : "Already Graded";
                            ?>
                            <button disabled class="disabled-btn" title="<?php echo $tooltip; ?>"><?php echo $tooltip; ?></button>
                            <?php
                        }
                        ?>
                        </td>

                    </tr>
                    <?php
                }
            }
            ?>
        </table>
    </div>
    <!-- Modal -->
    <div id="assignmentModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modalTitle" class="modal-heading"></h2>
            <p id="modalDescription" class="modal-description"></p>
            <form id="assignmentForm" action="submit_assignment_handler.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="assignment_id" id="modalAssignmentId">
            <div id="filePreview"></div> <br>
            <label class="file-label">
                Upload File
                <input type="file" name="assignment_file" required>
            </label>
            <button type="submit" id="modalSubmitBtn" class="submit-btn">Submit</button>
            </form>
        </div>
    </div>


    <script>
        document.querySelectorAll('.open-modal-btn').forEach(button => {
    button.addEventListener('click', function () {
        const modal = document.getElementById('assignmentModal');

        // Populate modal fields
        document.getElementById('modalTitle').innerText = this.dataset.title;
        document.getElementById('modalDescription').innerText = this.dataset.description;
        document.getElementById('modalAssignmentId').value = this.dataset.id;

        // Set button label and form action
        const action = this.dataset.action || 'Submit';
        const file = this.dataset.file || '';
        const instructorFile = this.dataset.instructorFile || '';
        const filePreview = document.getElementById('filePreview');

// Clear preview
filePreview.innerHTML = '';

// Show instructor file if available
if (instructorFile) {
    filePreview.innerHTML += `
        <p style="color: cyan;">Assignment File from Instructor: 
        <a style="color: cyan;" href="../uploads/instructor_assignments/${instructorFile}" target="_blank">${instructorFile}</a></p>`;
}

        const submitBtn = document.getElementById('modalSubmitBtn');
        submitBtn.innerText = 'Submit';
        document.getElementById('assignmentForm').action = 'submit_assignment_handler.php';

        // Show previously uploaded file if editing
        if (action === 'Edit' && file) {
            filePreview.innerHTML += `
                <p style= "color: yellow;">Your Previously uploaded file: 
                <a style= "color: yellow;"  href="../uploads/student_assignments/${file}" target="_blank">${file}</a></p>`;
        }

        // Show the modal
        modal.style.display = 'flex';
    });
});

// Close button
document.querySelector('.close').onclick = function () {
    document.getElementById('assignmentModal').style.display = 'none';
};

// Close modal when clicking outside of modal content
window.onclick = function (event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
};

    </script>


</body>
</html>
