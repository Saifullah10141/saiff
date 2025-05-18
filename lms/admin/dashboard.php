<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .header {
            background-color: #2980b9;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        body {
            display: flex;
            height: 100vh;
            background-color: white;
            color: #000;
            font-family: Arial, sans-serif;
        }
        .sidebar {
            width: 250px;
            background-color: #003366;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 7.35px;
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
        }
        .header {
            background-color: #2980b9;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .info-container {
            width: 60%;
            margin-left:20%;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .info-container .info-item {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .info-container .info-item:last-child {
            border-bottom: none;
        }
        .info-title {
            font-weight: bold;
        }
        .change-password-btn {
            margin-top: 20px;
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            width: 100%;
            text-align: center;
        }
        .change-password-btn:hover {
            background-color: #218838;
        }
        .modal-content {
            padding: 20px;
        }
        .admin-info {
            width: 250px;
            background-color: #003366;
            color: white;
            padding: 20px;
            height: 25.8vh;
            position: fixed;
            left: 0;
            bottom: 0;
        }

        .admin-info h4 {
            text-align: center;
        }

        .admin-info p {
            font-size: 14px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <?php
        require_once "dashboard0.php";
    ?>
    <div class="sidebar">
        <a href="dashboard.php">Dashboard</a>
        <a href="reset_password.php">Reset User Password</a>
        <a href="manage_lms_managers.php">Manage LMS Managers</a>
        <a href="manage_course_coordinators.php" style = "height: 57.33px;">Manage Course Coordinators</a>
        <a href="manage_students.php">Manage Students</a>
        <a href="manage_instructors.php">Manage Instructors</a>
        <a href="view_user_activity.php">User Activity</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <div class="header">
            <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        </div>
        <div class="info-container">
            <div class="info-item"><span class="info-title">Name:</span> <span><?php echo htmlspecialchars($username); ?></span></div>
            <div class="info-item"><span class="info-title">Email:</span> <span><?php echo htmlspecialchars($_SESSION['user_id']); ?></span></div>
            <button class="change-password-btn" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</button>
        </div> <hr>
        <div class="info-container" style=" color: white;
            background-color: #004080;
            text-align: center;
            border-radius: 5px;">
            <h4>LMS Statistics</h4>
                <p><strong>Total Students:</strong> <?php echo $total_students; ?></p>
                <p><strong>Total Instructors:</strong> <?php echo $total_instructors; ?></p>
                <p><strong>Total Course Coordinators:</strong> <?php echo $total_coordinators; ?></p>
                <p><strong>Total LMS Managers:</strong> <?php echo $total_managers; ?></p>
        </div>
        
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="oldPassword">Old Password</label>
                    <input type="password" id="oldPassword" class="form-control" required>
                    <label for="newPassword" class="mt-2">New Password</label>
                    <input type="password" id="newPassword" class="form-control" required>
                    <label for="confirmNewPassword" class="mt-2">Confirm New Password</label>
                    <input type="password" id="confirmNewPassword" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="savePassword">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById("savePassword").addEventListener("click", function () {
        let oldPassword = document.getElementById("oldPassword").value;
        let newPassword = document.getElementById("newPassword").value;
        let confirmNewPassword = document.getElementById("confirmNewPassword").value;

        if (newPassword !== confirmNewPassword) {
            alert("New passwords do not match!");
            return;
        }

        fetch("change_password.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `old_password=${encodeURIComponent(oldPassword)}&new_password=${encodeURIComponent(newPassword)}`
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') {
                const modal = bootstrap.Modal.getInstance(document.getElementById("changePasswordModal"));
                modal.hide();
            }
        })
        .catch(() => alert("Something went wrong"));
    });

    </script>
</body>
</html>
