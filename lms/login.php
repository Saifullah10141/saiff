<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
body {
    height: 100vh;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Segoe UI', sans-serif;
    background: rgba(255, 255, 255, 0.1); /* almost transparent */
    background-image: radial-gradient(circle at top left, rgba(173, 216, 230, 0.2), transparent), 
                      radial-gradient(circle at bottom right, rgba(135, 206, 250, 0.3), transparent);
    backdrop-filter: blur(20px); /* strong blur for watery effect */
    -webkit-backdrop-filter: blur(20px);
    overflow: hidden;
}

.container {
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
    background-color: rgba(255, 255, 255, 0.15); /* glass effect */
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 20px;
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.2);
    width: 900px;
    display: flex;
    padding: 40px;
    transition: 0.3s ease;
}

.login-form, .check-result {
    flex: 1;
}

.login-form {
    padding-right: 30px;
    border-right: 1px solid rgba(255, 255, 255, 0.3);
}

.check-result {
    padding-left: 30px;
}

h3 {
    margin-bottom: 20px;
    color: #003b5c;
    font-weight: bold;
}

label {
    font-weight: 500;
    color: #003344;
}

input {
    background-color: rgba(255, 255, 255, 0.25);
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 12px;
    margin-top: 5px;
    margin-bottom: 15px;
    padding: 12px;
    width: 100%;
    color: #001f33;
    backdrop-filter: blur(6px);
    transition: all 0.3s ease;
}

input::placeholder {
    color: rgba(0, 0, 0, 0.4);
}

input:focus {
    outline: none;
    border-color: #4ec6f7;
    background-color: rgba(255, 255, 255, 0.35);
    box-shadow: 0 0 8px rgba(0, 123, 255, 0.2);
    transform: scale(1.02);
}

button {
    width: 100%;
    padding: 12px;
    border-radius: 25px;
    background: linear-gradient(to right, #66d3fa, #3399cc);
    color: white;
    font-weight: bold;
    border: none;
    transition: all 0.3s ease;
    letter-spacing: 0.5px;
    backdrop-filter: blur(2px);
}

button:hover {
    background: linear-gradient(to right, #3399cc, #66d3fa);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.form-text {
    font-size: 0.85rem;
    color: #444;
}




    </style>
</head>
<body>

<?php
session_start();

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
            <form method="POST" action="login0.php">
                <h3>LMS Login</h3>

                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your Email" required>
                <div class="form-text">Your email will be kept confidential.</div>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your Password" required>

                <button type="submit">Login</button>
            </form>
        </div>

        <div class="check-result">
            <h3>Check Result</h3>
            <form method="POST" action="check_result.php">
                <label for="searchRegNo">Enter Registration Number</label>
                <input type="text" id="searchRegNo" name="student_id" placeholder="XXXX-ag-XXXX" required>
                <button type="submit">Check Result</button>
            </form>
        </div>
    </div>
</body>
</html>
