<?php
require_once "../db_connect.php";
require_once "../auth.php";

$assignment_id = $_GET['assignment_id'];

$sql = "SELECT s.student_id, u.username
        FROM enrollments e
        JOIN students s ON e.student_id = s.student_id
        JOIN users u ON s.user_id = u.user_id
        WHERE e.assignment_id = ?
        ";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $assignment_id);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode($students);
?>
