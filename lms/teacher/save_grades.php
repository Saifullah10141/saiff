<?php
include '../db_connect.php';
include '../auth.php';

$data = json_decode(file_get_contents("php://input"), true);

foreach ($data as $gradeEntry) {
    $assignment_id = $gradeEntry['assignment_id'];
    $student_id = $gradeEntry['student_id'];
    $grade = $gradeEntry['grade']; // 'P' or 'F'

    $sql = "UPDATE assignment_submissions SET status = ? WHERE assignment_id = ? AND student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $grade, $assignment_id, $student_id);
    $stmt->execute();
}

echo json_encode(['success' => true]);
?>
