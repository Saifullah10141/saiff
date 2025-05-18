<?php
// Start session and include auth.php to protect this page
require_once '../auth.php'; // Protect the page with session authentication

// Example: Fetch statistics for the dashboard
require_once '../db.php'; // Assuming db.php contains the PDO connection
$stmt_students = $pdo->query("SELECT COUNT(*) FROM students");
$total_students = $stmt_students->fetchColumn();

$stmt_instructors = $pdo->query("SELECT COUNT(*) FROM instructors");
$total_instructors = $stmt_instructors->fetchColumn();

$stmt_coordinators = $pdo->query("SELECT COUNT(*) FROM course_coordinators");
$total_coordinators = $stmt_coordinators->fetchColumn();

$stmt_managers = $pdo->query("SELECT COUNT(*) FROM lms_managers");
$total_managers = $stmt_managers->fetchColumn();

// Fetch logged-in admin's information
$stmt_admin = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt_admin->execute([$_SESSION['user_id']]);
$admin_info = $stmt_admin->fetch(PDO::FETCH_ASSOC);
?>