<?php
include '../db_connect.php';
include '../auth.php';

$instructor_email = $_SESSION['user_id'];

// Get form inputs
$subjectName = $_POST['subject'];
$title = $_POST['title'];
$time = $_POST['time'];
$due_date = $_POST['due_date'];

$questions = $_POST['question'];
$correct_answers = $_POST['correct_answer'];
$options1 = $_POST['option1'];
$options2 = $_POST['option2'];
$options3 = $_POST['option3'];

// Get instructor_id
$stmt = $conn->prepare("SELECT instructor_id FROM instructors WHERE user_id = ?");
$stmt->bind_param("s", $instructor_email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$instructor_id = $row['instructor_id'] ?? null;

if (!$instructor_id) {
    die("Instructor not found.");
}

// Get assignment_id from assigned_subjects using subject name
$stmt = $conn->prepare("
    SELECT assignment_id 
    FROM assigned_subjects
    WHERE assignment_id = ? AND instructor_id = ?
");
$stmt->bind_param("ss", $subjectName, $instructor_id);
$stmt->execute();
$result = $stmt->get_result();
$assignment = $result->fetch_assoc();

if (!$assignment) {
    die("Course not assigned to this instructor.");
}

$assignment_id = $assignment['assignment_id'];

// Insert quiz into quizzes table
$stmt = $conn->prepare("
    INSERT INTO quizzes (title, due_date, time_limit, assignment_id, instructor_id)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("ssiis", $title, $due_date, $time, $assignment_id, $instructor_id);
$stmt->execute();
$quiz_id = $stmt->insert_id;

// Insert each question
$stmt = $conn->prepare("
    INSERT INTO quiz_questions (quiz_id, question_text, correct_answer, option1, option2, option3)
    VALUES (?, ?, ?, ?, ?, ?)
");

for ($i = 0; $i < count($questions); $i++) {
    $question = $questions[$i];
    $correct = $correct_answers[$i];
    $opt1 = $options1[$i];
    $opt2 = $options2[$i];
    $opt3 = $options3[$i];

    $stmt->bind_param("isssss", $quiz_id, $question, $correct, $opt1, $opt2, $opt3);
    $stmt->execute();
}

echo "Quiz created successfully!";
header("Location: quiz_uploaded.php");
?>
