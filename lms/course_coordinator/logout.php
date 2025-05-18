<?php
session_start();
require_once '../db.php'; // This should initialize $pdo = new PDO(...)
require_once '../ip.php';

// Log logout activity before destroying session
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    $action = 'Logged out';
    $ip_address = getUserIP();

    try {
        $stmt = $pdo->prepare("INSERT INTO user_activities (user_id, action, role, ip_address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $action, $role, $ip_address]);
    } catch (Exception $e) {
        error_log("Logout activity log failed: " . $e->getMessage());
    }
}

// Destroy session
session_unset();
session_destroy();

// Redirect to login page
header("Location: /saiff/lms/login.php?logged_out=true");
exit();
