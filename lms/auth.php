<?php
require_once 'ini.php';
require_once 'ip.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['dark_mode'])) {
    $_SESSION['dark_mode'] = false; // Default: Light Mode
}

// Not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /saiff/lms/login.php");
    exit();
}

// Session timeout check
$timeout = 3600;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout) {
    session_unset();
    session_destroy();
    header("Location: ../login.php?timeout=true");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

// ---- Role-based path check ----
$role = $_SESSION['role'] ?? '';
$username = $_SESSION['username'];
$path = $_SERVER['PHP_SELF'];

// Define valid folders for each role
$roleFolders = [
    'student' => '/student/',
    'instructor' => '/teacher/',
    'course_coordinator' => '/course_coordinator/',
    'lms_manager' => '/lms_manager/',
    'admin' => '/admin/'
];

$expectedPath = $roleFolders[$role] ?? '';

if ($expectedPath && strpos($path, $expectedPath) === false) {
    // Optional: Log the unauthorized attempt
    require_once 'db.php';
    $action = "Unauthorized access - forced logout";
    $ip_address = getUserIP();
    $log = $pdo->prepare("INSERT INTO user_activities (user_id, action, role, ip_address) VALUES (?, ?, ?, ?)");
    $log->execute([$_SESSION['user_id'], $action, $role, $ip_address]);

    // Kill session
    session_unset();
    session_destroy();
    header("Location: /saiff/lms/login.php?unauthorized=true");
    exit();
}
