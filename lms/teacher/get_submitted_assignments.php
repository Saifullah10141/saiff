<?php
include '../db_connect.php';
include '../auth.php';

$assigned_id = $_GET['assigned_id'] ?? '';
$assignment_id = $_GET['assignment_id'] ?? '';

$sql = "
SELECT 
    st.student_id,
    u.username AS student_name,
    sa.submitted_at,
    sa.status,
    sa.file_path
FROM enrollments e
JOIN students st ON e.student_id = st.student_id
JOIN users u ON st.user_id = u.user_id
LEFT JOIN assignment_submissions sa 
    ON sa.student_id = st.student_id AND sa.assignment_id = ?
WHERE e.assignment_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $assignment_id, $assigned_id);

$stmt->execute();
$result = $stmt->get_result();

$submissions = [];
while ($row = $result->fetch_assoc()) {
    $submissions[] = $row;
}

echo json_encode($submissions);
?>
