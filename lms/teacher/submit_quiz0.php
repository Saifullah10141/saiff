<?php
include '../db_connect.php';
include '../auth.php';

if (!isset($_GET['quiz_id'])) {
    die("Quiz ID missing.");
}

$quiz_id = $_GET['quiz_id'];

// Get logged-in student name
$user_id = $_SESSION['user_id'];
$user_stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
$user_stmt->bind_param("s", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result()->fetch_assoc();
$student_name = $user_result['username'];
$user_stmt->close();

// Get student_id from user_id
$sql = "SELECT student_id FROM students WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$student_id = $student['student_id'];
$stmt->close();

// Assuming $student_id and $quiz_id are available
$sql = "
    SELECT 1
    FROM enrollments e
    JOIN assigned_subjects a ON e.assignment_id = a.assignment_id
    JOIN quizzes q ON q.assignment_id = a.assignment_id
    WHERE q.quiz_id = ? AND e.student_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $quiz_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "<script>alert('You are not enrolled in this subject.'); window.location.href='quiz.php';</script>";
    exit;
}

// Get quiz info
$quiz_stmt = $conn->prepare("SELECT q.title, q.time_limit, q.due_date, a.name AS course_title 
                             FROM quizzes q
                             JOIN assigned_subjects a ON q.assignment_id = a.assignment_id
                             WHERE q.quiz_id = ?");
$quiz_stmt->bind_param("i", $quiz_id);
$quiz_stmt->execute();
$quiz_info = $quiz_stmt->get_result()->fetch_assoc();
$quiz_stmt->close();

$current_time = time();
    if ($current_time >= (strtotime($quiz_info['due_date'])+75600)) {
        echo "<script>alert('This quiz is not available right now as due date is passed.'); window.location.href='quiz.php';</script>";
        exit;
    }

// Get questions
$question_stmt = $conn->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
$question_stmt->bind_param("i", $quiz_id);
$question_stmt->execute();
$questions = $question_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$question_stmt->close();

// Check if submission record exists
$sql = "SELECT started_at FROM quiz_submissions WHERE quiz_id = ? AND student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $quiz_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();
$submission = $result->fetch_assoc();

if (!$submission) {

    // First time accessing — insert submission with current timestamp
    $insert = $conn->prepare("INSERT INTO quiz_submissions (quiz_id, student_id, started_at) VALUES (?, ?, NOW())");
    $insert->bind_param("is", $quiz_id, $student_id);
    $insert->execute();

    // Re-query to get the correct started_at
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $quiz_id, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $submission = $result->fetch_assoc();
}



$start_time = strtotime($submission['started_at'])-10800;
$end_time = $start_time + ($quiz_info['time_limit'] * 60);

?>