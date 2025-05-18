<?php
session_start();
require_once 'db.php'; // This file should define $pdo = new PDO(...);
require_once 'ip.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $_SESSION['LAST_ACTIVITY'] = time();

    // Validate input
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Email and password are required.";
        header("Location: login.php");
        exit;
    }

    try {
        // Fetch user by email/user_id only
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true); // for security

            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];

            // Log login activity
            $action = 'Logged in'; // The action description
            $ip_address = getUserIP();
            $stmt = $pdo->prepare("INSERT INTO user_activities (user_id, action, role, ip_address) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user['user_id'], $action, $user['role'], $ip_address]);

            // Redirect based on role from DB
            switch ($user['role']) {
                case 'student':
                    header("Location: student/dashboard.php");
                    break;
                case 'instructor':
                    header("Location: teacher/dashboard.php");
                    break;
                case 'course_coordinator':
                    header("Location: course_coordinator/dashboard.php");
                    break;
                case 'lms_manager':
                    header("Location: lms_manager/dashboard.php");
                    break;
                case 'admin':
                    header("Location: admin/dashboard.php");
                    break;
                default:
                    $_SESSION['error'] = "Unknown role. Please contact support.";
                    header("Location: login.php");
                    break;
            }
            exit;
        } else {
            $_SESSION['error'] = "Incorrect email or password.";
            header("Location: login.php");
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Something went wrong. Please try again.";
        error_log("Login error: " . $e->getMessage());
        header("Location: login.php");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
