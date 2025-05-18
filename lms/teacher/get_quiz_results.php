<?php
include '../db_connect.php';
include '../auth.php';

$assigned_id = $_GET['assigned_id'] ?? '';
$quiz_id = $_GET['quiz_id'] ?? '';

$sql = "
SELECT 
    st.student_id,
    u.username AS student_name,
    qs.score
FROM enrollments e
JOIN students st ON e.student_id = st.student_id
JOIN users u ON st.user_id = u.user_id
LEFT JOIN quiz_submissions qs 
    ON qs.student_id = st.student_id AND qs.quiz_id = ?
WHERE e.assignment_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $quiz_id, $assigned_id);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

echo json_encode($rows);
