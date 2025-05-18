<?php
require_once "../db_connect.php";
require_once "../auth.php";

$student_id = $_POST['student_id'] ?? null;
$assignment_id = $_POST['assignment_id'] ?? null;

if (!$student_id || !$assignment_id) {
    echo "Invalid request!";
    exit;
}

$stmt = $conn->prepare("DELETE FROM enrollments WHERE student_id = ? AND assignment_id = ?");
$stmt->bind_param("si", $student_id, $assignment_id);

if ($stmt->execute()) {
    echo "Student removed successfully!";
} else {
    echo "Failed to remove student.";
}
?>
