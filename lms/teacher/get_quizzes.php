<?php
require_once "../db_connect.php";
require_once "../auth.php";

$instructor_email = $_SESSION['user_id'];
$mode = $_GET['mode'] ?? null;

$stmt = $conn->prepare("SELECT instructor_id FROM instructors WHERE user_id = ?");
$stmt->bind_param("s", $instructor_email);
$stmt->execute();
$result = $stmt->get_result();
$instructor = $result->fetch_assoc();
$instructor_id = $instructor['instructor_id'] ?? null;

if (!$instructor_id || !$mode) {
    echo json_encode([]);
    exit;
}

header('Content-Type: application/json');

if ($mode === 'subjects') {
    $stmt = $conn->prepare("SELECT DISTINCT name FROM assigned_subjects WHERE instructor_id = ?");
    $stmt->bind_param("s", $instructor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row['name'];
    }
    echo json_encode($data);
    exit;
}

if ($mode === 'quizzes_by_subject') {
    $subject = $_GET['subject'] ?? '';
    $stmt = $conn->prepare("
        SELECT q.quiz_id, q.title, q.due_date 
        FROM quizzes q
        JOIN assigned_subjects a ON q.assignment_id = a.assignment_id
        WHERE a.instructor_id = ? AND a.name = ?
    ");
    $stmt->bind_param("ss", $instructor_id, $subject);
    $stmt->execute();
    $result = $stmt->get_result();

    $quizzes = [];
    while ($row = $result->fetch_assoc()) {
        $quizzes[] = $row;
    }
    echo json_encode($quizzes);
    exit;
}

echo json_encode([]);
