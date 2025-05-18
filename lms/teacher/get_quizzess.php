<?php
include '../db_connect.php';
include '../auth.php';

$assignment_id = $_GET['assigned_id'] ?? '';

$sql = "SELECT quiz_id, title FROM quizzes WHERE assignment_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $assignment_id);
$stmt->execute();
$result = $stmt->get_result();

$quizzes = [];
while ($row = $result->fetch_assoc()) {
    $quizzes[] = $row;
}

echo json_encode($quizzes);
