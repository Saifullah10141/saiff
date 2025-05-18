<?php
require_once "../db_connect.php";
require_once "../auth.php";

if (!isset($_GET['id'])) {
    echo "<script>alert('No attendance selected.'); window.location.href='attendance.php';</script>";
    exit();
}

$attendance_id = intval($_GET['id']);
$user_id = $_SESSION['user_id']; // instructor's email

// First fetch instructor_id
$stmt = $conn->prepare("SELECT instructor_id FROM instructors WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($instructor_id);
$stmt->fetch();
$stmt->close();

if (!$instructor_id) {
    echo "<script>alert('Instructor not found.'); window.location.href='attendance.php';</script>";
    exit();
}

// Verify that the attendance belongs to this instructor
$stmt = $conn->prepare("
    SELECT a.session_date, a.session_time, ass.name AS subject_name
    FROM attendances a
    INNER JOIN assigned_subjects ass ON a.assignment_id = ass.assignment_id
    WHERE a.attendance_id = ? AND a.instructor_id = ?
");

$stmt->bind_param("is", $attendance_id, $instructor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Unauthorized or invalid attendance record.'); window.location.href='uploaded_attendances.php';</script>";
    exit();
}

$attendance_info = $result->fetch_assoc();
$stmt->close();

// Now fetch student attendance records
$stmt = $conn->prepare("
    SELECT u.username AS student_name, s.student_id AS registration_no, ar.status
    FROM attendance_records ar
    INNER JOIN students s ON ar.student_id = s.student_id
    INNER JOIN users u ON s.user_id = u.user_id
    WHERE ar.attendance_id = ?
    ORDER BY s.student_id ASC
");

$stmt->bind_param("i", $attendance_id);
$stmt->execute();
$students_result = $stmt->get_result();

$student_attendance = [];
while ($row = $students_result->fetch_assoc()) {
    $student_attendance[] = $row;
}
$stmt->close();
?>
