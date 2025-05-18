<?php
require_once "../db_connect.php";
require_once "../auth.php";

$user_id = $_SESSION['user_id']; // Logged-in student's user_id

// Get student's student_id
$stmt = $conn->prepare("SELECT student_id FROM students WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($student_id);
$stmt->fetch();
$stmt->close();

// Fetch pending assignments (student not yet submitted)
$pending_assignments = [];
$sql = "SELECT a.assignment_id, a.title, a.description, a.due_date, s.name AS course_name, a.file_path as instructor_file
        FROM assignments a
        INNER JOIN enrollments e ON a.assignment_id1 = e.assignment_id
        INNER JOIN assigned_subjects s ON a.assignment_id1 = s.assignment_id
        WHERE e.student_id = ?
          AND NOT EXISTS (
              SELECT 1 FROM assignment_submissions sa
              WHERE sa.assignment_id = a.assignment_id
                AND sa.student_id = e.student_id
          )
        ORDER BY a.due_date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $pending_assignments[] = $row;
}
$stmt->close();

// Fetch submitted assignments
$submitted_assignments = [];
$sql = "SELECT a.assignment_id, a.title, a.description, sa.submitted_at AS submitted_on, sa.status, sa.file_path, a.due_date, s.name AS course_name, a.file_path as instructor_file
        FROM assignment_submissions sa
        INNER JOIN assignments a ON sa.assignment_id = a.assignment_id
        INNER JOIN assigned_subjects s ON a.assignment_id1 = s.assignment_id
        WHERE sa.student_id = ?
        ORDER BY sa.submitted_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $submitted_assignments[] = $row;
}
$stmt->close();
?>
