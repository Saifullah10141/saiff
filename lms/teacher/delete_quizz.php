<?php
require_once "../db_connect.php";
require_once "../auth.php";

$quiz_id = $_POST['quiz_id'] ?? null;

if (!$quiz_id) {
    http_response_code(400);
    echo "Quiz ID is required.";
    exit;
}

// Optionally: delete related questions
$stmt = $conn->prepare("DELETE FROM quiz_questions WHERE quiz_id = ?");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();

// Delete the quiz
$stmt = $conn->prepare("DELETE FROM quizzes WHERE quiz_id = ?");
$stmt->bind_param("i", $quiz_id);

if ($stmt->execute()) {
    echo "Quiz deleted successfully.";
} else {
    http_response_code(500);
    echo "Failed to delete quiz.";
}
?>
