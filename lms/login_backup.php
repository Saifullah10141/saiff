<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }
        .container {
            display: flex;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 800px;
        }
        .login-form {
            width: 60%;
            padding-right: 20px;
            border-right: 2px solid #ddd;
        }
        .check-result {
            width: 40%;
            padding-left: 20px;
        }
        h3 {
            margin-bottom: 15px;
            color: #333;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #28a745;
            color: white;
            cursor: pointer;
            border: none;
            margin-top: 15px;
            font-weight: bold;
            transition: 0.3s;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<?php
session_start();

// If user is already logged in, redirect to their role's dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'student':
            header("Location: student/dashboard.php");
            exit;
        case 'instructor':
            header("Location: teacher/dashboard.php");
            exit;
        case 'course_coordinator':
            header("Location: course_coordinator/dashboard.php");
            exit;
        case 'lms_manager':
            header("Location: lms_manager/dashboard.php");
            exit;
        case 'admin':
            header("Location: admin/dashboard.php");
            exit;
    }
}
?>

    <div class="container">
        <div class="login-form">
            <form id="Form" method="POST" action="login0.php">
                <h3>LMS Login</h3>

                <label for="exampleInputEmail1">Email address</label>
                <input type="email" class="form-control" id="exampleInputEmail1" name="email" required>
                <div class="form-text">Your email will be kept confidential.</div>
                
                <label for="exampleInputPassword1">Password</label>
                <input type="password" class="form-control" id="exampleInputPassword1" name="password" required>
                
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
        
        <div class="check-result">
            <h3>Check Result</h3>
            <label for="searchRegNo">Enter Registration Number</label>
            <input type="text" id="searchRegNo" placeholder="Reg. No.">
            <button onclick = "window.location.href = 'check_result.php';">Check Result</button>
        </div>
    </div>
</body>
</html>
