<?php
include '../db_connect.php';
include '../auth.php'; // ensure student is logged in

$user_id = $_SESSION['user_id'];  // Get the logged-in user's ID

$sql = "SELECT student_id FROM students WHERE user_id = '$user_id'";
$result = $conn->query($sql);

$student = $result->fetch_assoc();
$student_id = $student['student_id'];

// Fetch enrolled assignment_ids
$assignment_ids = [];
$enrolled_sql = "SELECT assignment_id FROM enrollments WHERE student_id = ?";
$stmt = $conn->prepare($enrolled_sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $assignment_ids[] = $row['assignment_id'];
}
$stmt->close();

$assignment_placeholder = implode(',', array_fill(0, count($assignment_ids), '?'));

// Fetch quizzes that are enrolled and not submitted (Pending)
$pending_quizzes = [];
if (!empty($assignment_ids)) {
    $sql = "
    SELECT q.quiz_id, q.title, q.due_date, a.name AS subject_name
    FROM quizzes q
    INNER JOIN assigned_subjects a ON q.assignment_id = a.assignment_id
    WHERE q.assignment_id IN ($assignment_placeholder)
    AND q.quiz_id NOT IN (
        SELECT quiz_id FROM quiz_submissions 
        WHERE student_id = ? AND submitted_at IS NOT NULL
    )
";

    $stmt = $conn->prepare($sql);
    $params = array_merge($assignment_ids, [$student_id]);
    $stmt->bind_param(str_repeat('s', count($assignment_ids)) . 's', ...$params);
    $stmt->execute();
    $pending_quizzes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Fetch quizzes that are submitted
$submitted_quizzes = [];
$sql = "
    SELECT q.title, s.submitted_at, s.score, a.name AS subject_name
    FROM quizzes q
    INNER JOIN quiz_submissions s ON q.quiz_id = s.quiz_id
    INNER JOIN assigned_subjects a ON q.assignment_id = a.assignment_id
    WHERE s.student_id = ? AND submitted_at IS NOT NULL
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$submitted_quizzes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>