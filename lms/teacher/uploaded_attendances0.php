<?php
require_once "../db_connect.php";
require_once "../auth.php";

// Get logged-in instructor_id
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT instructor_id FROM instructors WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($instructor_id);
$stmt->fetch();
$stmt->close();

if (!$instructor_id) {
    die("Instructor not found.");
}

// Handle filter
$subject = $_GET['subject'] ?? '';
$date = $_GET['date'] ?? '';

// Base query
$query = "
    SELECT a.attendance_id, a.session_date, a.session_time, s.name AS subject_name
    FROM attendances a
    JOIN assigned_subjects s ON a.assignment_id = s.assignment_id
    WHERE a.instructor_id = ?
";

// Add filters if provided
$params = [$instructor_id];
$types = "s";

if (!empty($subject)) {
    $query .= " AND s.name = ?";
    $params[] = $subject;
    $types .= "s";
}

if (!empty($date)) {
    $query .= " AND a.session_date = ?";
    $params[] = $date;
    $types .= "s";
}

$query .= " ORDER BY a.session_date DESC, a.session_time DESC"; // newest first

// Prepare statement dynamically
$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$attendances = [];
while ($row = $result->fetch_assoc()) {
    $attendances[] = $row;
}
$stmt->close();
?>
